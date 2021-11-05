<?php

namespace App\Http\Controllers\PartnerApi\Talk;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Http\Requests\Talk\Bind;
use App\Http\Requests\Talk\OpenService;
use App\Http\Requests\Talk\UpService;
use App\Http\Requests\Talk\ChangeService;
use App\Models\Talk\MsgInits;
use App\Http\Requests\Talk\TalkDelete;
use App\Http\Requests\Talk\IsCidOnLine;
use App\Http\Requests\Talk\EnterService;
use App\Http\Requests\Talk\ServiceSendClient;
use App\Http\Requests\Talk\EndService;
use App\Http\Requests\Talk\ServiceList;
use App\Http\Requests\Talk\ServiceHistory;
use App\Http\Requests\Talk\CloseService;
use App\Http\Requests\Talk\EditService;


/**
 * 玩家头上传
 * Class ApiAvatarController
 * @package App\Http\Controllers\PartnerApi\System
 */
class ApiTalkController extends ApiBaseController
{
    //删除聊天记录
    public function delete(TalkDelete $request)
    {
        $params = request()->all();
        return MsgInits::TalkDelete($params);
    }
    //删除商户下面的用户缓存
    public function clearCache()
    {
        $params['type']=1;//商户删除
        return MsgInits::ClearCache($params);
    }
    //绑定消息通道
    public function bind(Bind $request)
    {
        $params = request()->all();
        return MsgInits::ServiceBind($params);
    }
    
    //查询CID是否在线
    public function isCidOnLine(IsCidOnLine $request)
    {
        $params = request()->all();
        return MsgInits::isCidOnLine($params);
    }

    //查询客服通道
    public function searchService()
    {
        return MsgInits::searchService();
    }

    //开设客服通道
    public function openService(OpenService $request)
    {
        $params = request()->all();
        return MsgInits::openService($params);
    }

    //客服上位
    public function upService(UpService $request)
    {
        $params = request()->all();
        return MsgInits::upService($params);
    }

    //客服下位
    public function downService(UpService $request)
    {
        $params = request()->all();
        return MsgInits::downService($params);
    }

    //改变通道状态
    public function changeService(ChangeService $request)
    {
        $params = request()->all();
        return MsgInits::changeService($params);
    }

    //进入客服台
    public function enterService(EnterService $request)
    {
        $params = request()->all();
        return MsgInits::enterService($params);
    }

    //客服回复
    public function serviceSendClient(ServiceSendClient $request)
    {
        $params = request()->all();
        return MsgInits::serviceSendClient($params);
    }

    public function endService(EndService $request)
    {
        $params = request()->all();
        return MsgInits::endService($params);
    }

    public function serviceList(ServiceList $request)
    {
        $params = request()->all();
        return MsgInits::serviceList($params);
    }

    public function serviceHistory(ServiceHistory $request)
    {
        $params = request()->all();
        return MsgInits::serviceHistory($params);
    }

    public function closeService(CloseService $request)
    {
        $params = request()->all();
        return MsgInits::closeService($params);
    }

    public function editService(EditService $request)
    {
        $params = request()->all();
        return MsgInits::editService($params);
    }

    public function deleteServiceHistory(CloseService $request)
    {
        $params = request()->all();
        return MsgInits::deleteServiceHistory($params);
    }
}
