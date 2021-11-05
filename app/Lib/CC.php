<?php namespace App\Lib;

/**
 * Class CC
 * @package App\Lib
 */
class CC {

    // 用户图标
    static function getUserIcon($partnerSign) {
        return config("user.main.user_icon");
    }

}
