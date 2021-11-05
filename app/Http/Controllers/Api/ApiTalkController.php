<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\Talk\Bind;
use App\Http\Requests\Talk\IsCidOnLine;
use App\Http\Requests\Talk\SendMsg;
use App\Http\Requests\Talk\GetTalkHistory;
use App\Http\Requests\Talk\SendService;
use App\Http\Requests\Talk\ServiceHistory;
use App\Http\Requests\Talk\BindUnLogin;
use App\Http\Requests\Talk\SendServiceUnLogin;
use App\Http\Requests\Talk\GetTalkHistoryUnLogin;
use App\Lib\Help;
use App\Models\Talk\MsgInits;

/**
 * 玩家聊天接口
 * Class ApiPlayerController
 *
 * @package App\Http\Controllers\Api
 */
class ApiTalkController extends ApiBaseController
{

    //关系列表 *
    public function friendList()
    {
        return MsgInits::Friend();
    }

    //绑定用户 (只允许单个绑定) *
    public function bind(Bind $request)
    {
        $params = request()->all();
        return MsgInits::bind($params);
    }

    //发送消息个人 *
    public function sendMsg(SendMsg $request)
    {
        $params = request()->all();
        return Msginits::SendMsg($params);
    }

    //查看聊天通道信息
    public function getTalkConfig()
    {
        $res =array();
        $res['wk'] = configure('system_wk');
        $res['tws'] = configure('system_tws');
        return Help::returnApiJson('获取成功!', 1,$res);
    }

    //查询历史聊天记录
    public function getTalkHistory(GetTalkHistory $request)
    {
        $params = request()->all();
        return MsgInits::getTalkHistory($params);
    }

    //查询句柄是否在线
    public function isCidOnLine(IsCidOnLine $request)
    {
        $params = request()->all();
        return MsgInits::isCidOnLine($params);
    }

    //发送消息(客服)
    public function sendService(SendService $request)
    {
        $params = request()->all();
        return MsgInits::sendService($params);
    }

    public function serviceHistory(ServiceHistory $request)
    {
        $params = request()->all();
        return MsgInits::serviceHistory($params);
    }

    public function sendServiceUnLogin(SendServiceUnLogin $request)
    {
        $params = request()->all();
        $params['partner_sign'] = $this->partner->sign;
        return MsgInits::sendServiceUnLogin($params);
    }
    public function friendListUnLogin()
    {
        $params['partner_sign'] = $this->partner->sign;
        return MsgInits::searchServicePublic($params);
    }
    public function bindUnLogin(BindUnLogin $request)
    {
        $params = request()->all();
        $params['partner_sign'] = $this->partner->sign;
        return MsgInits::bindUnLogin($params);
    }

    public function getTalkHistoryUnLogin(GetTalkHistoryUnLogin $request)
    {
        $params = request()->all();
        $params['partner_sign'] = $this->partner->sign;
        return MsgInits::getTalkHistoryUnLogin($params);
    }
}
