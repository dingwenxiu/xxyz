<?php

namespace App\Http\Controllers\AdminApi;

use App\Lib\Clog;
use App\Lib\Help;
use App\Lib\Pay\Pay;
use Illuminate\Http\Request;
use App\Models\Player\Player;
use App\Models\Finance\Withdraw;
use App\Lib\Logic\AccountLocker;
use App\Lib\Logic\AccountChange;
use Illuminate\Routing\Controller;
use App\Models\Finance\WithdrawLog;
use App\Models\Finance\FinancePlatform;
use App\Models\Finance\FinancePlatformAccount;

class WithdrawCallbackController extends Controller
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
     * 提现回调处理
     * @param $sign
     * @return string
     * @throws \Exception
     */
    public function withdrawCallback($sign)
    {
        // 1.接收参数,记录回调返回参数
        $params = request()->all();
        Clog ::withdrawLog("提现回调信息", $params);
        $pay    = new Pay();
        $pay    = $pay->getHandle($sign);

        // 2.接收参数是否是数组
        $ifIsArray = $pay -> checkParamsIfIsArray($params);
        if ($ifIsArray !== true) {
            return Help ::returnApiJson($ifIsArray, 0);
        }

        // 3.查询日志获取参数
        $pay           -> setWithdrawOrder($params['game_order_id']);
        $wLog   = $pay -> getWithdrawLog();
        if (!isset($wLog) || empty($wLog)) {
            return Help ::returnApiJson("对不起,充值订单不存在！", 0);
        }

        // 4. 判断订单号是否一致
        $withdrawQueryParams = json_decode($wLog['request_params'],true);

        $pay      -> setWithdrawLog($wLog);
        $pay      -> setWithdrawQueryParams($withdrawQueryParams);
        $r = $pay -> checkWithdrawCallbackOrder();

        if ($r !== true) {
            return Help ::returnApiJson("对不起,充值订单号不正确！", 0);
        }

        // 5.判断是否在白名单中
        $fPlatform     = FinancePlatform::where('platform_sign','panda') -> first();
        if ($fPlatform){
            $allowIps  = explode('|',$fPlatform->whitelist_ips);
            if (!in_array(real_ip(),$allowIps)) {
                return Help ::returnApiJson("对不起,IP不在白名单中！", 0);
            }
        }else{
            return Help ::returnApiJson("对不起,IP不在白名单中！", 0);
        }

        // 6.验证签名是否合法
        // 设置key
        $pAccount = FinancePlatformAccount::where('merchant_code',$withdrawQueryParams['merchant_id'])->first();
        $setKey   = $pAccount -> merchant_secret;
        $pay -> setKey($setKey);
        $verifyRes = $pay -> Verify($params);
        if (!is_array($verifyRes) || !isset($verifyRes) || empty($verifyRes)) {
            return Help ::returnApiJson("对不起,验证签名不合法！", 0);
        }

        // 7.提现操作逻辑
        $this->ProcessingWithdraw($verifyRes);
        return 'success';
    }

    /**
     * 提现处理逻辑
     * @param array $verifyRes
     * @return string
     */
    private function ProcessingWithdraw($verifyRes)
    {
        // 1.判断是否接收到回调验证后的参数
        if ($verifyRes) {
            db()->beginTransaction();
            // 2.更改提现状态
            $res = Withdraw::where('order_id',$verifyRes['merchant_order_no'])->update(['status'=>Withdraw::STATUS_SEND_SUCCESS]);
            if($res){
                $orderInfo = Withdraw::where('order_id',$verifyRes['merchant_order_no'])->first();
                if ($verifyRes['real_money'] > $orderInfo->amount) {
                    return "Sorry , Invalid amount!";
                }
                // 用户锁
                $locker = new AccountLocker($orderInfo->user_id);
                if(!$locker->getLock()){
                    db()->rollback();
                    return "对不起, 获取用户锁失败!!";
                }
                try {
                    $user          = Player::find($orderInfo->user_id);
                    $account       = $user->account();
                    // 4.提现
                    $params = [
                        'user_id'       => $user->id,
                        'amount'        => $verifyRes['real_money'],
                        'desc'          => '恭喜,回调成功,出款成功',
                        'project_id'    => $orderInfo->parent_id,
                        'admin_id'      => $orderInfo->admin_id,
                    ];
                    $accountChange = new AccountChange();
                    $res           = $accountChange->change($account, 'withdraw_finish',  $params, $user->is_tester);
                    if ($res !== true) {
                        $locker->release();
                        db()->rollback();
                        return $res;
                    }

                    $userWithdraw = Withdraw::where('order_id',$verifyRes['merchant_order_no'])->first();
                    $userWithdraw->real_amount   = $verifyRes['real_money'];    // 确认金额
                    $userWithdraw->pay_order_id  = $verifyRes['pay_order_id'];  // 外部订单号
                    $userWithdraw->description   = '恭喜,回调成功,出款成功';        // 描述
                    $userWithdraw->day_m         = time();                      // 处理时间
                    $userWithdraw->status        = 4;                           // 回调成功
                    $userWithdraw->save();
                    db()->commit();
                } catch (\Exception $e) {
                    db()->rollback();
                    return  $e->getMessage();
                }
                $locker->release();
            }
        }else{
            return false;
        }
    }
}
