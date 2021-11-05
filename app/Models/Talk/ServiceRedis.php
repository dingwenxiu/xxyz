<?php

namespace App\Models\Talk;

class ServiceRedis
{
    public static function reServiceWorkStatusHash()
    {
        /*
            filed 为service表中的ID
            value 如下 data
        */
        $res =  array(
            'partner_sign'=>null,
            'service_id'=>null,//客服表中的ID
            'work_status'=>1,//1频道开启服务，2频道休息中（给用户看默认开启）
            'staff_status'=>0,//频道内客服人数，如有两位员工上位该客服，则显示2
            'parent_info'=>null//json 数组,里面保存坚守人信息
        );
        return $res;
    }


}
