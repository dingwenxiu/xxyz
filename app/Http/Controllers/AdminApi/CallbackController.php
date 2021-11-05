<?php

namespace App\Http\Controllers\AdminApi;

use App\Lib\Clog;
use App\Lib\Help;
use App\Lib\Pay\Pay;
use Illuminate\Http\Request;
use App\Models\Player\Player;
use App\Models\Finance\Recharge;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\AccountChange;
use Illuminate\Routing\Controller;
use App\Models\Report\ReportStatStack;
use App\Models\Player\PlayerVipConfig;
use App\Models\Finance\FinancePlatform;
use function GuzzleHttp\default_ca_bundle;
use App\Models\Finance\FinancePlatformAccount;
use App\Models\Finance\FinancePlatformChannel;

class CallbackController extends Controller
{
    protected $data;

    /**
     * 构造各种接收前端参数方式
     * CallbackController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->data = $request->all() or $this->data = $request->input() or $this->data = $_REQUEST or $this->data = file_get_contents('php://input','r');
    }

    /**
     * 回调处理
     * @param $sign
     * @return string
     * @throws \Exception
     */
    public function rechargeCallback($sign)
    {
        try{
            // 1.接收参数,记录回调返回参数
            $params = request()->all();
            Clog ::rechargeLog("充值回调参数", [$params,$sign]);

            $pay = new Pay();
            $pay = $pay->getHandle($sign);

            // 2.接收参数是否是数组
            $ifIsArray = $pay -> checkParamsIfIsArray($params);
          
            if ($ifIsArray !== true) {
                Clog ::rechargeLog("充值回调参数不是数组", [$ifIsArray]);
                return Help ::returnApiJson("", 0, $ifIsArray);
            }

            // 3.接收的回调地址参数是否合法 checkRechargeCallbackParams
            $pay -> setRechargeCallbackParams($params);

            $checkCallParams = $pay -> checkRechargeCallbackParams();
            
            if ($checkCallParams === false) {
                Clog ::rechargeLog("充值回调地址参数不合法", [$checkCallParams]);
                return Help ::returnApiJson("对不起,充值回调地址参数不合法", 0, $checkCallParams);
            }

            // 4.查询日志获取参数
            $pay -> setRechargeOrderId($params['game_order_id']);
            $rLog = $pay -> getRechargeLog();

            if (!isset($rLog) || empty($rLog)) {
                Clog ::rechargeLog("充值回调订单不存在！", [$rLog]);
                return Help ::returnApiJson("对不起,充值回调订单不存在！", 0);
            }

            // 5.判断接收回调订单号是否正确
            $request_back = json_decode($rLog->request_back);
            $pay_order_id = $request_back->data->pay_order_id;
            $order = ['pay_order_id' => $pay_order_id];
            $pay -> setRechargeOrder($order);
            $cOrder = $pay -> checkRechargeCallbackOrder();
            if ($cOrder === false) {
                Clog ::rechargeLog("充值回调接收的订单号不合法！", [$cOrder]);
                return Help ::returnApiJson("对不起,充值回调接收的订单号不合法！", 0);
            }

            // 6.判断是否在白名单中
            $fPlatform = FinancePlatform::where('platform_sign',$sign) -> first();
            if ($fPlatform){
                $allowIps  = explode('|',$fPlatform->whitelist_ips);
                if (!in_array(real_ip(),$allowIps)) {
                    Clog ::rechargeLog("充值回调IP不在白名单中！", [$allowIps]);
                    return Help ::returnApiJson("对不起,充值回调IP不在白名单中！", 0);
                }
            }else{
                Clog ::rechargeLog("充值回调厂商不存在！", [$sign]);
                return Help ::returnApiJson("对不起,充值回调厂商不存在！", 0);
            }
            // 7.验证签名是否合法
            $request_params = json_decode($rLog->request_params, true);
            if (!$request_params){
                Clog ::rechargeLog("充值回调验证签名错误", [$request_params]);
                return Help ::returnApiJson("对不起,key不存在！", 0);
            }

            // 8.设置key
            $pAccount = FinancePlatformAccount::where('merchant_code',$request_params['merchant_id'])->first();
            $setKey = $pAccount -> merchant_secret;
            $pay->setKey($setKey);
            $verifyRes = $pay -> verify($params);
            if (!is_array($verifyRes) || !isset($verifyRes) || empty($verifyRes)) {
                Clog ::rechargeLog("充值回调验证签名不合法", [$verifyRes]);
                return Help ::returnApiJson("对不起,充值回调验证签名不合法", 0);
            }

            //9.查询渠道是否存在。
            $pChannel = FinancePlatformChannel::where('platform_sign',$sign)
                ->where('type_sign',$request_params['channel'])
                ->first();
            if (!$pChannel){
                Clog ::rechargeLog("充值回调渠道不存在！", [$verifyRes]);
                return Help ::returnApiJson("对不起,充值回调渠道不存在！", 0);
            }

            // 9.判断是入款还是出款处理逻辑
            if($pChannel->direction == FinancePlatformChannel::DIR_IN){
                // 9.1 充值上分
               $this->ProcessingPayment((array)$verifyRes);
                
            }
            echo 'success';
        } catch (\Exception $e) {
            Clog ::rechargeLog("充值回调错误catch", [$e->getMessage()]);
            return  $e->getMessage();
        }
    }

    /**
     * 充值处理逻辑
     * @param array $verifyRes
     * @return bool|string
     * @throws \Exception
     */
    private function ProcessingPayment($verifyRes=[])
    {
        // 1.判断是否接收到回调验证后的参数
        if ($verifyRes) {
            db()->beginTransaction();

            $recharge =  Recharge::where('order_id',$verifyRes['merchant_order_no'])->first();
            if($recharge->status!=0) {
                Clog ::rechargeLog("对不起, 请勿重复充值回调!!:",[$verifyRes]);
                    return false;
            }

            Clog ::rechargeLog("充值回调订单".$recharge->order_id."的状态是!!:".$recharge->status);

            // 2.更改充值状态
           $res = Recharge::where('order_id',$verifyRes['merchant_order_no'])->update(['status'=>Recharge::STATUS_SEND_SUCCESS]);

            if($res){
                // 3.判断金额
                $orderInfo = Recharge::where('order_id',$verifyRes['merchant_order_no'])->first();
                
                // 判断是否已经充值回调
                if ($verifyRes['real_money'] > $orderInfo->amount) {
                    Clog ::rechargeLog("对不起, 充值回调无效的金额!!:");
                    return false;
                }
                $locker = new AccountLocker($orderInfo->user_id);
                if(!$locker->getLock()){
                    Clog ::rechargeLog("对不起, 充值回调获取用户锁失败!!:");
                    return false;
                }

                // 获取充值数据更新用户等级
                $vip_level = PlayerVipConfig::getUserLevel($orderInfo->partner_sign,$orderInfo->user_id,$verifyRes['real_money']);
                // 充值会员等级变化 获取总充值金额
                Player::where('id',$orderInfo->user_id)->update(['vip_level'=>$vip_level]);

                try {
                    $user       = Player::find($orderInfo->user_id);
                    $account    = $user->account();

                    // 4.充值上分
                    $params = [
                        'user_id'       => $user->id,
                        'amount'        => moneyUnitTransferIn($verifyRes['real_money']),
                        'desc'          => '验签成功,自动上分',
                        'project_id'    => $orderInfo->parent_id,
                        'admin_id'      => $orderInfo->admin_id,
                    ];
                    $accountChange = new AccountChange();
                    $res = $accountChange->change($account, 'recharge', $params, $user->is_tester);
                    if ($res !== true) {
                        Clog ::rechargeLog("充值回调上分失败", [$res]);

                        $locker->release();
                        db()->rollback();
                        return false;
                    }

                    // 统计
                    ReportStatStack::doRecharge($user, moneyUnitTransferIn($verifyRes['real_money']));

                    $playerRecharge                = Recharge::where('order_id',$verifyRes['merchant_order_no'])->first();
                    $playerRecharge->real_amount   = moneyUnitTransferIn($verifyRes['real_money']);  // 确认金额
                    $playerRecharge->pay_order_id  = $verifyRes['pay_order_id'];                     // 外部订单号
                    $playerRecharge->desc          = '验签成功,自动上分';                               // 描述
                    $playerRecharge->callback_time = time();                                         // 回调时间
                    $playerRecharge->day_m         = date("YmdHi");                           // 处理时间
                    $playerRecharge->status        = 1;                                              // 充值成功
                    $playerRecharge->save();
                    db()->commit();

                    $locker->release();
                } catch (\Exception $e) {
                    Clog ::rechargeLog("充值回调处理逻辑异常", [$e->getMessage()]);
                    $locker->release();
                    db()->rollback();
                    return  false;
                   
                }
            }
        }
    }
}
