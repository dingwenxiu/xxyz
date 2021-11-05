<?php

namespace App\Models\Admin;

class SysCity extends Base
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'sys_city';

    /**
     * @param int $provinceId
     * @return array
     */
    static function getCityList($provinceId) {
        $data = self::select('id', 'region_id', 'region_name')->where('region_parent_id', $provinceId)->get()->toArray();
        return $data;
    }

    /**
     * @return array
     */
    static function getProvinceList() {
        $res    = self::where("region_parent_id", 0)->get();
        $data   = [];
        foreach ($res as $item) {
            $data[] = [
                'id'            => $item->id,
                'region_id'     => $item->region_id,
                'region_name'   => $item->region_name
            ];
        }
        return $data;
    }

    /**
     * 获取银行下拉列表
     * @return array
     */
    static function getOption() {
        $res    = self::orderBy('id', 'asc')->whereIn('region_level',[1,2])->get();
        $data   = [];

        foreach ($res as $item) {
            $data[$item->region_id]        = $item->region_name;
        }

        return $data;
    }

    /**
     * getOptionCard
     */
    static function getOptionCard() {
        $res    = self::orderBy('id', 'asc')->whereIn('region_level',[1,2])->get();
        $data   = [];

        foreach ($res as $item) {
            $data[$item->id]        = $item->region_name;
        }

        return $data;
    }
}
