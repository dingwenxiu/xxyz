<?php

namespace App\Http\Controllers\Api;

use App\Lib\Help;
use App\Models\Partner\PartnerNotice;

// 普通
class ApiCommonController extends ApiBaseController
{

    /**
     * 公告相关列表
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function noticeList()
    {
    	$c = request()->all();
		$user = auth()->guard('api')->user();
		if (!$user) {
			return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
		}

        $data = PartnerNotice::getDataFromCache($this->partner->sign,$c);
		$noPop = [];

        foreach ($data['data'] as $item) {
        	if ($item['no_popup'] != null){
				$ids = explode('|', $item['no_popup']);
				if (in_array($user->id,$ids)) {
					$item['no_popup'] = true;
				}
			}
			$noPop[] = [
				'id'		   => $item['id'],
				'no_popup'     => $item['no_popup'],
				'type'         => $item['type'],
				'device_type'  => $item['device_type'],
				'title'        => $item['title'],
				'type_desc'    => $item['type_desc'],
				'status'       => $item['status'],
				'content'      => $item['content'],
				'start_time'   => date('Y-m-d H:i:s', $item['start_time']),
				'end_time'     => date('Y-m-d H:i:s', $item['end_time'])];
		}
        $data['data'] = $noPop;
        return Help::returnApiJson('恭喜, 获取数据成功!', 1, $data);
    }


	/**
	 * 不再弹窗
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function noPopup () {
		$user = auth()->guard('api')->user();
		if (!$user) {
			return Help::returnApiJson('对不起, 用户未登录!', 0, ['reason_code' => 999],401);
		}

		$c = request()->all();

		$data = PartnerNotice::getDataFromCache($this->partner->sign, $c);
		foreach ($data['data'] as $item) {
			$notice = PartnerNotice::find($item['id']);
			if ($notice->no_popup != null){
				$ids = explode('|', $notice->no_popup);
				if (in_array($user->id,$ids)) {
					$popup = $notice->no_popup;
				} else {
					$popup = $notice->no_popup.'|'.$user->id;
				}
			} else {
				$popup = $user->id;
			}
			PartnerNotice::where('id',$item['id'])->update(['no_popup' => $popup]);
		}
		// 清除缓存
		PartnerNotice::_flushCache("notice_" . $this->partner->sign);
		return Help::returnApiJson('您好,不再提示弹窗',1);
	}
}
