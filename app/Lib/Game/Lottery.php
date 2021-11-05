<?php namespace App\Lib\Game;


/**
 * 游戏相关逻辑
 * Class Lottery
 * @package App\Lib\Game
 */
class Lottery {

    // blabla
    static function blabla() {
        $config = [
            'app_hash'          => env('APP_HASH'),
            'hash_expire'       => [
                "201809443"             => "2020-03-09 00:00:00",
                "201809488"             => "2020-06-09 00:00:00",
                "XABZPACV%_8fuYDTF"     => "2020-08-09 00:00:00",
                "K=9SZp9ACV&_dfIOUb"    => "2020-11-09 00:00:00",
                "Z66DZPACdfV"           => "2021-03-09 00:00:00",
                "B8_0D0FfdJJ"           => "2021-09-09 00:00:00",
                "998SDFaUYtfdsdf"       => "2022-03-09 00:00:00",
                "12dsdibjDFdasfs"       => "2022-08-09 00:00:00",
                "Acdf98FcszDddfs"       => "2022-12-09 00:00:00",
                "sdf0DFfxfxDDDf1"       => "2023-05-09 00:00:00",
                "SSFD232fdsfDFs2"       => "2024-01-09 00:00:00",
                "SDF09DFDFf4x5f3"       => "2025-01-09 00:00:00",
                "ADFBADFDER09DF4ad"     => "2026-01-09 00:00:00",
                "SDF30dfje_%esdfadf"    => "2027-01-09 00:00:00",
            ],
        ];

        // hash

        if (!isset($config['app_hash']) || !isset($config['hash_expire'][$config['app_hash']])) {
            return [];
        } else {
            $expired = $config['hash_expire'][$config['app_hash']];
            if (strtotime($expired) < time()) {
                return [];
            }
        }

        return 9527779;
    }
}
