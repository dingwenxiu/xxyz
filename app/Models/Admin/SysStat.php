<?php

namespace App\Models\Admin;


class SysStat extends Base
{
    protected $table = 'sys_stat';

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

    static function getLast() {
        return self::orderBy('day_end_m', 'desc')->first();
    }

}
