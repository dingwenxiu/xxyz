<?php

use Illuminate\Database\Seeder;

class FinanceChannelTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        db()->table('finance_channel_type')->delete();

        db()->table('finance_channel_type')->insert(array (
            0 =>
                array (
                    'id'                    => 1,
                    'type_name'             => '银行转账',
                    'type_sign'             => 'transfer',
                    'is_bank'               => 1,
                    'icon'                  => '/system/finance/20191207142936.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            1 =>
                array (
                    'id'                    => 2,
                    'type_name'             => '支付宝',
                    'type_sign'             => 'zfb',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142920.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),

            2 =>
                array (
                    'id'                    => 3,
                    'type_name'             => '银联扫码',
                    'type_sign'             => 'unionpay_qr',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142936.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            3 =>
                array (
                    'id'                    => 4,
                    'type_name'             => '公司入款',
                    'type_sign'             => 'business_card',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142936.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            4 =>
                array (
                    'id'                    => 5,
                    'type_name'             => '微信',
                    'type_sign'             => 'wechat',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142900.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            5 =>
                array (
                    'id'                    => 6,
                    'type_name'             => '快捷支付',
                    'type_sign'             => 'kuaijie',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142802.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            6 =>
                array (
                    'id'                    => 7,
                    'type_name'             => 'QQ支付',
                    'type_sign'             => 'qq',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142838.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            7 =>
                array (
                    'id'                    => 8,
                    'type_name'             => '公司出款',
                    'type_sign'             => 'withdraw',
                    'is_bank'               => 1,
                    'icon'                  => '/system/finance/20191207142936.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),

            8 =>
                array (
                    'id'                    => 9,
                    'type_name'             => '财付通',
                    'type_sign'             => 'cft',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/cft.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            9 =>
                array (
                    'id'                    => 10,
                    'type_name'             => '京东支付',
                    'type_sign'             => 'jd',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/jd.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            10 =>
                array (
                    'id'                    => 11,
                    'type_name'             => '人工充值',
                    'type_sign'             => 'human',
                    'is_bank'               => 1,
                    'icon'                  => '/system/finance/human.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            11 =>
                array (
                    'id'                    => 12,
                    'type_name'             => '网银',
                    'type_sign'             => 'unionpay',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142936.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            12 =>
                array (
                    'id'                    => 13,
                    'type_name'             => '银联快捷',
                    'type_sign'             => 'unionpay_kj',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142802.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            13 =>
                array (
                    'id'                    => 14,
                    'type_name'             => '线下银行转账',
                    'type_sign'             => 'offline_transfer',
                    'is_bank'               => 1,
                    'icon'                  => '/system/finance/20191207142936.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            14 =>
                array (
                    'id'                    => 15,
                    'type_name'             => '线下微信',
                    'type_sign'             => 'offline_wechat',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142900.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            15 =>
                array (
                    'id'                    => 16,
                    'type_name'             => '线下支付宝',
                    'type_sign'             => 'offline_alipay',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142920.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            16 =>
                array (
                    'id'                    => 17,
                    'type_name'             => '线下银联扫码',
                    'type_sign'             => 'offline_unionqr',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142936.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            17 =>
                array (
                    'id'                    => 18,
                    'type_name'             => 'QQH5',
                    'type_sign'             => 'qq_wap',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142838.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            18 =>
                array (
                    'id'                    => 19,
                    'type_name'             => '京东支付H5',
                    'type_sign'             => 'jd_wap',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/jd_wap.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            19 =>
                array (
                    'id'                    => 20,
                    'type_name'             => '财付通H5',
                    'type_sign'             => 'cft_wap',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/cft_wap.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            20 =>
                array (
                    'id'                    => 21,
                    'type_name'             => '支付宝H5',
                    'type_sign'             => 'zfb_wap',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142920.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),

            21 =>
                array (
                    'id'                    => 22,
                    'type_name'             => '微信H5',
                    'type_sign'             => 'wechat_wap',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142900.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            22 =>
                array (
                    'id'                    => 23,
                    'type_name'             => '网银H5',
                    'type_sign'             => 'unionpay_wap',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142936.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            23 =>
                array (
                    'id'                    => 24,
                    'type_name'             => '银行转账H5',
                    'type_sign'             => 'transfer_wap',
                    'is_bank'               => 1,
                    'icon'                  => '/system/finance/20191207142936.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            24 =>
                array (
                    'id'                    => 25,
                    'type_name'             => '银联扫码H5',
                    'type_sign'             => 'unionpay_qr_wap',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142936.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
            25 =>
                array (
                    'id'                    => 26,
                    'type_name'             => '银联快捷H5',
                    'type_sign'             => 'unionpay_kj_wap',
                    'is_bank'               => 0,
                    'icon'                  => '/system/finance/20191207142802.png',
                    'created_at'            => '2019-10-21 09:06:08',
                    'updated_at'            => '2019-10-21 09:06:08',
                ),
        ));
    }

}
