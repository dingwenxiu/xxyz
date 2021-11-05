<?php
namespace App\Http\Controllers\AdminApi\Talk;

use App\Http\Controllers\AdminApi\ApiBaseController;
use App\Models\Talk\MsgInits;

class ApiTalkController extends ApiBaseController
{
    public function systemTalkClearCache()
    {
        $params['type']=2;//全平台关系缓存删除
        return MsgInits::ClearCache($params);
    }

    public function systemTalkClearHistory()
    {
        return MsgInits::ClearTalkHistoryAll();
    }

}
