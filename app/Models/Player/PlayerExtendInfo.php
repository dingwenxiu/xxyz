<?php

namespace App\Models\Player;

use App\Lib\Check;
use Illuminate\Database\Eloquent\Model;

class PlayerExtendInfo extends Model
{
    protected $table = 'user_extend_info';

    /**
     * 初始化
     * @param $player
     */
    static function initUserInfo($player) {
        $query = new PlayerExtendInfo();
        $query->user_id         = $player->id;
        $query->partner_sign    = $player->partner_sign;
        $query->save();
    }

    /**
     * 设置信息
     * @param $data
     * @return array|bool|string|null
     */
    public function setInfo($data)
    {
        // address
        if (isset($data['address']) && $data['address']) {
            $res = Check::checkAddress($data['address']);
            if ($res !== true) {
                return $res;
            }
            $this->address      = $data['address'];
        }

        // email
        if (isset($data['email']) && $data['email']) {
            $res = Check::checkMail($data['email']);
            if ($res !== true) {
                return $res;
            }
            $this->email      = $data['email'];
        }

        // address
        if (isset($data['mobile']) && $data['mobile']) {
            $res = Check::checkMobile($data['mobile']);
            if ($res !== true) {
                return $res;
            }
            $this->mobile      = $data['mobile'];
        }

        // mobile
        if (isset($data['real_name']) && $data['real_name']) {
            $res = Check::checkRealName($data['real_name']);
            if ($res !== true) {
                return $res;
            }
            $this->real_name      = $data['real_name'];
        }

        // zip_code
        if (isset($data['zip_code']) && $data['zip_code']) {
            $res = Check::checkZipCode($data['zip_code']);
            if ($res !== true) {
                return $res;
            }
            $this->zip_code      = $data['zip_code'];
        }

        $this->save();
        return true;
    }
}
