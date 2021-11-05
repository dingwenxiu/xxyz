<?php

namespace App\Models\Partner;


use Illuminate\Database\Eloquent\Model;

class PartnerAdvertising extends Model
{
    public $errMsg;

    public function getList($c, $partnerSign)
    {
        $query = self::where('partner_sign', $partnerSign);
        if (isset($c['type']) && $c['type']) {
            $query->where('type', $c['type']);
        }

        if (isset($c['module_sign']) && $c['module_sign']) {
            $query->where('module_sign', $c['module_sign']);
        }

        $pageSize = 10;
        $currentPage    = isset($c['pageIndex']) ? intval($c['pageIndex']) : 1;
        $pageSize       = isset($c['pageSize']) ? intval($c['pageSize']) : $pageSize;
        $offset         = ($currentPage - 1) * $pageSize;

        $total  = $query->count();
        $menus  = $query->skip($offset)->take($pageSize)->get();

        return ['data' => $menus, 'total' => $total, 'currentPage' => $currentPage, 'totalPage' => intval(ceil($total / $pageSize))];

    }

    public function saveItem($c, $partnerSign, $id)
    {
        $advertM = self::where([
            'partner_sign' => $partnerSign,
            'module_sign' => $c['module_sign'],
            'sign' => $c['sign'],
            'type' => $c['type'],
            'pid' => $c['pid'],
            'game_id' => $c['game_id'],
        ])->first();

        if ($id == 0 && !is_null($advertM)) {
            $this->errMsg = '广告位已经存在';
            return false;
        }

        $query = self::where('id', $id)->first();

        if (is_null($query)) {
            $query = $this;
        }

        $query->title = $c['title'] ?? '';
        $query->type = $c['type'] ?? '';
        $query->type_name = $c['type_name'] ?? '';
        $query->module_sign = $c['module_sign'] ?? '';
        $query->module_name = $c['module_name'] ?? '';
        $query->pid = $c['pid'] ?? '';
        $query->game_id = $c['game_id'] ?? '';
        $query->img = $c['img'] ?? '';
        $query->url = $c['url'] ?? '';
        $query->sign = $c['sign'] ?? '';
        $query->sign_name = $c['sign_name'] ?? '';
        $query->partner_sign = $partnerSign;
        $query->save();

        return true;
    }
}
