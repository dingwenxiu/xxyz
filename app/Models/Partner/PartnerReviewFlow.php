<?php

namespace App\Models\Partner;

use App\Models\Base;

class PartnerReviewFlow extends Base {

    public $errorMsg = '';
    public $models = '';


    public function saveItem($c, $partnerSign)
    {

        if (empty($c['users'])) {
            $this->errorMsg = 'users必须有值';
            return false;
        }

        $query = self::where([
            'id' => $c['id'],
        ])->first();

        $userSqlStr = '';

        // 判断用户是否存在
        $c['users'] = implode('|', array_filter(explode('|', $c['users'])));

        $userArr = array_filter(explode(',', implode(',', explode('|', $c['users']))));
        if (count($userArr) != count(array_unique($userArr))) {
            $this->errorMsg = 'users 重复';
            return false;
        }

        if (is_null($query)) {

            // 1. 添加的时候 类型存在
            $query = self::where([
                'partner_sign' => $partnerSign,
                'type'         => $c['type'],
                'type_detail'  => $c['type_detail'],
            ])->first();

            if (!is_null($query)) {
                $this->errorMsg = '类型存在';
                return false;
            }


            $query1 = self::where([
                'partner_sign' => $partnerSign,
            ])->get();

            foreach ($query1 as $key => $item) {
                if (!empty($item->users)) {
                    $userSqlStr .= implode(',', explode('|', $item->users)) . ',';
                }
            }

            $userSqlArr = explode(',', $userSqlStr);

            foreach ($userArr as $va) {
                if (!empty($va) && in_array($va, $userSqlArr)) {
                    $this->errorMsg = '此用户已存在';
                    return false;
                }
            }

            $query = $this;
        } else {

            // 0 . 判断是否还存在 审核中的数据
            $PartnerAdminActionReview = PartnerAdminActionReview::where([
                'partner_sign' => $partnerSign,
                'type'         => $c['type'],
                'type_detail'  => $c['type_detail'],
                'handle_admin_three'  => '',
            ])->first();

            if (!is_null($PartnerAdminActionReview)) {
                $this->errorMsg = '请走完流程，在编辑';
                return false;
            }

            // 1. 添加的时候 类型存在
            $query1 = self::where([
                'partner_sign' => $partnerSign,
                'type'         => $c['type'],
                'type_detail'  => $c['type_detail'],
            ])->where('id', '!=', $c['id'])->first();

            if (!is_null($query1)) {
                $this->errorMsg = '类型存在';
                return false;
            }

            $query1 = self::where([
                'partner_sign' => $partnerSign,
            ])->where('id', '!=', $c['id'])->get();

            foreach ($query1 as $key => $item) {
                if (!empty($item->users)) {
                    $userSqlStr .= implode(',', explode('|', $item->users)) . ',';
                }
            }

            $userSqlArr = explode(',', $userSqlStr);

            foreach ($userArr as $va) {
                if (!empty($va) && in_array($va, $userSqlArr)) {
                    $this->errorMsg = '此用户已存在';
                    return false;
                }
            }
        }

        $query->type         = $c['type'];
        $query->type_detail  = $c['type_detail'];
        $query->users        = $c['users'];
        $query->partner_sign = $partnerSign;
        $query->save();

        return true;
    }

    public function getItem($c, $partnerSign)
    {
        $query = self::where('partner_sign', $partnerSign);
        if (isset($c['type'])) {
            $query->where('type', $c['type']);
        }
        $data = $query->get();

        return $data;
    }

    public function delItem($c)
    {
        $query = self::where('id', $c['id'])->delete();
        return true;
    }
}
