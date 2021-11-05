<?php

namespace App\Http\Controllers\CasinoApi;

use App\Http\Controllers\Api\ApiBaseController;
use App\Lib\Casino\CasinoApi;
use App\Lib\Help;
use App\Models\Partner\PartnerCasinoPlatform;
use Illuminate\Support\Facades\Hash;

/**
 * ApiFinanceController.php
 * @version 2.0.0 2019.09
 */
class ApiCasinoController extends ApiBaseController
{
    /**
     * 进入游戏
     * @return \Illuminate\Http\JsonResponse
     */
    function joinGame() {
            $player  = auth()->guard('api')->user();
            $partner = $this->partner;
            if (!$player) {
                return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
            }

            $c          = request()->all();
            $CasinoApi  = new CasinoApi($player, $partner);
            $res        = $CasinoApi->joinGame($c);

            return $res;
    }

    /**
     * 获取余额
     */
    public function getBalance() {

        $player  = auth()->guard('api')->user();
        $partner = $this->partner;
        if (!$player) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $c          = request()->all();
        $CasinoApi  = new CasinoApi($player, $partner);
        $res        = $CasinoApi->getBalance($c);
        if (is_null($res)) {
            return Help::returnApiJson('平台正在维护', 0);
        } elseif ($res == 'err') {
            return Help::returnApiJson('遇到错误', 0);
        }
        return Help::returnApiJson('获取成功', 1, $res);
    }

    /**
     * 获取余额
     */
    public function getAllBalance() {

        $player  = auth()->guard('api')->user();
        $partner = $this->partner;
        if (!$player) {
            return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
        }

        $PartnerCasinoPlatform = PartnerCasinoPlatform::where('partner_sign', $partner->sign)->get(['main_game_plat_code']);
        $CasinoApi  = new CasinoApi($player, $partner);
        $data = [];
        foreach ($PartnerCasinoPlatform as $key => $item) {
            $data[$item->main_game_plat_code] = $CasinoApi->getBalance(['mainGamePlat' => $item->main_game_plat_code]);
        }
        return Help::returnApiJson('获取成功', 1, $data);
    }

    /**
     * 平台转入娱乐城
     */
    public function transferIn() {
        $player  = auth()->guard('api')->user();
        $partner = $this->partner;

		if ($player->is_tester == 1) {
			return Help ::returnApiJson('对不起, 测试会员不能转账,请注册正式会员！', 0, ['reason_code' => 999],401);
		}

        // 资金密码
		// 资金密码
		$codeOne = base64_decode(request("fund_password", ''));
		$codeTwo = substr($codeOne, 0, -4);
		$final = base64_decode($codeTwo);
		$password = substr($final, 5, 37);
        if (!$password || !Hash::check($password, $player->fund_password)) {
            return Help::returnApiJson("对不起, 资金密码不正确!", 0);
        }

        if (!$player) {
            return Help::returnApiJson(
                '对不起, 用户未登录!', 0, ['reason_code' => 999],401
            );
        }

        $c = request()->all();
        $CasinoApi = new CasinoApi($player, $partner);
        $res = $CasinoApi->transferIn($c);

        return $res;
    }

    /**
     * 娱乐城转入平台
     */
    public function transferTo() {
        $player  = auth()->guard('api')->user();
        $partner = $this->partner;
        
		if ($player->is_tester == 1) {
			return Help ::returnApiJson('对不起, 测试会员不能转账,请注册正式会员！', 0, ['reason_code' => 999],401);
		}
        // 资金密码
        $codeOne = base64_decode(request("fund_password", ''));
        $codeTwo = substr($codeOne, 0, -4);
        $final = base64_decode($codeTwo);
        $password = substr($final, 5, 37);
        if (!$password || !Hash::check($password, $player->fund_password)) {
            return Help::returnApiJson("对不起, 资金密码不正确!", 0);
        }

        if (!$player) {
            return Help::returnApiJson(
                '对不起, 用户未登录!', 0, ['reason_code' => 999],401
            );
        }

        $c = request()->all();
        $CasinoApi = new CasinoApi($player, $partner);
        $res = $CasinoApi->transferTo($c);

        return $res;
    }
}
