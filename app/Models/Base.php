<?php

namespace App\Models;

use App\Lib\BaseCache;
use App\Lib\T;
use Illuminate\Database\Eloquent\Model;


class Base extends Model {

    use BaseCache;

    /** ========== 通知处理 ========== */

    /**
     * 发送异常数据
     * @param $msg
     */
    static function errorNotice($msg) {
        return T::exceptionNotice($msg);
    }

    /**
     * @param array   $data 更新数据.
     * @param integer $id   更新ID.
     * @return boolean
     */
    public function saveBase(array $data, int $id)
    {
        $selfObj = $this;
        if ($id) {
            $selfObj = self::where('id', $id)->first();
        }

        $field = ['id', '_token'];
        foreach ($data as $key => $v) {
            if (!in_array($key, $field)) {
                $selfObj->$key   = $v;
            }
        }

        $selfObj->save();
        return true;
    }

}
