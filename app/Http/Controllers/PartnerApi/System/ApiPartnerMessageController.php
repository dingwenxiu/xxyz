<?php

namespace App\Http\Controllers\PartnerApi\System;

use App\Http\Controllers\PartnerApi\ApiBaseController;
use App\Lib\Help;
use App\Models\Partner\PartnerMessage;
use App\Models\Player\Player;

class ApiPartnerMessageController extends ApiBaseController
{

    // 获取站内信列表
    public function getList()
    {
        $c                 = request()->all();
        $c['partner_sign'] = $this->partnerSign;
        $data       = PartnerMessage::getList($c);

        $types = [
			1 => '直属',
			2 => '代理',
			3 => '指定用户群',
		];

        $_data = [];
		foreach ($data['data'] as $item) {
			$res = unserialize($item->user_config);
			$names = [];
			foreach ($res as $key => $val) {
				$names[] = $key;
			}
			$_data[] = [
				'id'          => $item->id,
				'title'       => $item->title,
				'user_type'   => $item->user_type,
				'content'     => $item->content,
				'user_config' => $names,
				'created_at'  => date("Y-m-d H:i:s", strtotime($item->created_at))
			];
		}

		$data['data'] = $_data;

        return Help::returnApiJson('获取数据成功!', 1,  $data);
    }


    // 删除信息
    public function contentDel()
    {
        $id = request('id', []);
        if (!is_array($id)) {
            return Help::returnApiJson("对不起, 您需要传递一个数组！", 0);
        }

        $_childIdArr = [];
        foreach ($id as $_id) {
            if ($_id) {
                $_childIdArr[] = $_id;
            }
        }

        // 删除站内信
        PartnerMessage::whereIn('id', $_childIdArr)->delete();

        return Help::returnApiJson("恭喜, 删除数据成功！", 1);
    }


    // 添加站内信
    public function addMessageContent()
    {
        $params = request()->all();
        $params['partner_sign'] = $this->partnerSign;
        $params['user_type']    = request('user_type');

		// 判断用户类型
		if ($params['user_type'] == 3){
			$params['username'] = request('user_config', []);
			if (!$params['username']) {
				return Help::returnApiJson('请填写用户名', 0);
			}
			foreach ($params['username'] as $item) {
				$res = Player::where('partner_sign', $this->partnerSign)
					->where('username', $item)
					->where('status', 1)
					->first();
				if (!$res) {
					return Help::returnApiJson('用户' . $item . '不存在,请填写正确用户名', 0);
				}
			}
		} else if ($params['user_type'] == 1) {
			$params['username'] = Player::where('type', 1)->pluck('username')->toArray();
		} else if ($params['user_type'] == 2) {
			$nameOne = Player::where('type', 1)->pluck('username')->toArray();
			$nameTwo = Player::where('type', 2)->pluck('username')->toArray();
			$params['username'] = array_merge($nameOne,$nameTwo);
		}

		$message = new PartnerMessage();
		$message->saveItem($params);

        return Help::returnApiJson("恭喜, 添加成功!", 1);
    }

}
