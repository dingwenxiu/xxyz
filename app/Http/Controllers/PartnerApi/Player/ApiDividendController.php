<?php namespace App\Http\Controllers\PartnerApi\Player;

use App\Lib\Help;
use Illuminate\Support\Facades\Hash;
use App\Lib\Logic\Player\DividendLogic;
use App\Models\Report\ReportUserDividend;
use App\Http\Controllers\PartnerApi\ApiBaseController;


class ApiDividendController extends ApiBaseController
{
    // 分红报表
    public function dividendReportList()
    {
        $c      = request()->all();
        $c['partner_sign'] = $this->partnerSign;

        $data   = ReportUserDividend::getList($c);

        foreach ($data['data'] as $item) {
            $item->total_bets                       = number4($item->total_bets);
            $item->total_cancel                     = number4($item->total_cancel);
            $item->total_bonus                      = number4($item->total_bonus);

            $item->total_he_return                  = number4($item->total_he_return);
            $item->total_commission_from_bet        = number4($item->total_commission_from_bet);
            $item->total_commission_from_child      = number4($item->total_commission_from_child);
            $item->total_gift                       = number4($item->total_gift);
            $item->total_salary                     = number4($item->total_salary);
            $t                                      = $item->profit - $item->total_dividend;
            $item->profit                           = number4($t);
            $item->total_dividend                   = number4(0);            
            $item->amount                           = number4($item->amount);
            $item->real_amount                      = number4($item->real_amount);

            $item->send_time                        = $item->send_time ? date("Y-m-d H:i:s", $item->send_time) : '';
        }

        return Help::returnApiJson('获取数据成功!', 1, $data);
    }

    /**
     * 商户端 发送分红
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function dividendReportSend() {

        $partnerAdminUser = auth() -> guard('partner_api') -> user();
        if (!$partnerAdminUser) {
            return Help ::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        // 资金密码
        // $fundPassword = trim(request('fund_password', ''));
        $codeOne      = base64_decode(trim(request('fund_password', '')));
        $codeTwo      = substr($codeOne, 0, -4);
        $final        = base64_decode($codeTwo);
        $fundPassword = substr($final, 5, 37);
        if (!Hash ::check($fundPassword, $partnerAdminUser -> fund_password)) {
            return Help ::returnApiJson('对不起, 无效的资金密码!', 0);
        }

        // 校验ID
        $ids = request('ids', []);
        $items = ReportUserDividend::whereIn('id', $ids)->get();

        foreach ($items as $item) {
            if (!$item) {
                return Help::returnApiJson('对不起, 无效的数据!', 0);
            }

            if ($item->partner_sign != $partnerAdminUser->partner_sign) {
                return Help::returnApiJson("对不起, 存在一些用户您没有权限!", 0);
            }

            if ($item->status == ReportUserDividend::STATUS_SEND) {
                return Help::returnApiJson("对不起, 用户{$item->username}已经发放!", 0);
            }
        }

        // 发放
        $res = DividendLogic::sendBonus($items);

        if (!$res['status'] && $res['total_player'] != $res['fail_count']) {
            return Help::returnApiJson('对不起, 部分完成!', 0, $res);
        } else if (!$res['status'] ) {
            return Help::returnApiJson('对不起, 发放分行失败!', 0, $res);
        } else {
            return Help::returnApiJson('恭喜! 发放分红成功', 1, $res);
        }
    }
}
