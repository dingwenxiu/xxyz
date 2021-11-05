<?php

namespace App\Models\Admin;


class SysBank extends Base
{
    // 如果未设置 默认是蛇形复数形式的表明
    protected $table = 'sys_bank';

    /**
     * @return array
     */
    static function getList() {
        $res    = self::orderBy('id', 'desc')->get();
        $data   = [];

        foreach ($res as $item) {
            $data[] = $item->toArray();
        }

        return $data;
    }

    /**
     * 获取银行下拉列表
     * @return array
     */
    static function getOption() {
        $res    = self::orderBy('id', 'desc')->get();
        $data   = [];

        foreach ($res as $item) {
            $data[$item->code] = $item->title;
        }

        return $data;
    }
}
