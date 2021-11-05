<?php

use Illuminate\Database\Seeder;

class FinancePlatformChannelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('finance_platform_channel')->delete();

        \DB::table('finance_platform_channel')->insert(array (
            //mifu
            0 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'mifu_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            1 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'mifu_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            2 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'mifu_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            3 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'mifu_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            4 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'mifu_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            5 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'mifu_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            6 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'mifu_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            7 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'mifu_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            8 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'mifu_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            9 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'mifu_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            10 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'mifu_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            11 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'mifu_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            12 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'mifu_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            13 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'mifu_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            14 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'mifu_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            15 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'mifu_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            16 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'mifu_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            17 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'mifu_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            18 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'mifu_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            19 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'mifu_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            20 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'mifu_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            21 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'mifu_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            22 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'mifu_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            23 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'mifu_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            24 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'mifu_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            25 =>
                array (
                    'platform_child_sign'       => "mifu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'mifu_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //panda
            26 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'panda_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            27 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'panda_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            28 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'panda_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            29 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'panda_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            30 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'panda_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            31 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'panda_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            32 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'panda_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            33 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'panda_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            34 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'panda_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            35 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'panda_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            36 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'panda_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            37 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'panda_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            38 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'panda_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            39 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'panda_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            40 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'panda_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            41 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'panda_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            42 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'panda_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            43 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'panda_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            44 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'panda_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            45 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'panda_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            46 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'panda_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            47 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'panda_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            48 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'panda_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            49 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'panda_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            50 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'panda_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            51 =>
                array (
                    'platform_child_sign'       => "panda",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'panda_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //cpay
            52 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'cpay_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            53 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'cpay_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            54 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'cpay_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            55 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'cpay_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            56 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'cpay_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            57 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'cpay_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            58 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'cpay_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            59 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'cpay_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            60 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'cpay_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            61 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'cpay_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            62 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'cpay_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            63 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'cpay_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            64 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'cpay_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            65 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'cpay_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            66 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'cpay_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            67 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'cpay_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            68 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'cpay_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            69 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'cpay_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            70 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'cpay_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            71 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'cpay_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            72 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'cpay_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            73 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'cpay_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            74 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'cpay_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            75 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'cpay_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            76 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'cpay_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            77 =>
                array (
                    'platform_child_sign'       => "cpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'cpay_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //dingpay
            78 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'dingpay_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            79 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'dingpay_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            80 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'dingpay_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            81 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'dingpay_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            82 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'dingpay_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            83 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'dingpay_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            84 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'dingpay_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            85 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'dingpay_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            86 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'dingpay_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            87 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'dingpay_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            88 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'dingpay_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            89 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'dingpay_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            90 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'dingpay_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            91 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'dingpay_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            92 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'dingpay_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            93 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'dingpay_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            94 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'dingpay_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            95 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'dingpay_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            96 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'dingpay_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            97 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'dingpay_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            98 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'dingpay_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            99 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'dingpay_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            100 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'dingpay_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            101 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'dingpay_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            102 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'dingpay_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            103 =>
                array (
                    'platform_child_sign'       => "dingpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'dingpay_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //heshun
            104 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'heshun_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            105 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'heshun_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            106 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'heshun_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            107 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'heshun_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            108 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'heshun_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            109 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'heshun_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            110 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'heshun_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            111 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'heshun_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            112 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'heshun_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            113 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'heshun_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            114 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'heshun_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            115 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'heshun_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            116 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'heshun_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            117 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'heshun_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            118 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'heshun_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            119 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'heshun_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            120 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'heshun_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            121 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'heshun_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            122 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'heshun_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            123 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'heshun_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            124 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'heshun_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            125 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'heshun_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            126 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'heshun_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            127 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'heshun_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            128 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'heshun_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            129 =>
                array (
                    'platform_child_sign'       => "heshun",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'heshun_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //jiufu
            130 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'jiufu_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            131 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'jiufu_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            132 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'jiufu_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            133 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'jiufu_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            134 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'jiufu_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            135 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'jiufu_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            136 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'jiufu_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            137 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'jiufu_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            138 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'jiufu_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            139 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'jiufu_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            140 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'jiufu_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            141 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'jiufu_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            142 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'jiufu_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            143 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'jiufu_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            144 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'jiufu_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            145 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'jiufu_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            146 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'jiufu_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            147 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'jiufu_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            148 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'jiufu_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            149 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'jiufu_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            150 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'jiufu_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            151 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'jiufu_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            152 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'jiufu_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            153 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'jiufu_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            154 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'jiufu_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            155 =>
                array (
                    'platform_child_sign'       => "jiufu",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'jiufu_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //kaer
            156 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'kaer_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            157 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'kaer_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            158 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'kaer_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            159 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'kaer_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            160 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'kaer_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            161 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'kaer_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            162 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'kaer_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            163 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'kaer_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            164 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'kaer_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            165 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'kaer_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            166 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'kaer_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            167 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'kaer_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            168 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'kaer_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            169 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'kaer_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            170 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'kaer_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            171 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'kaer_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            172 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'kaer_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            173 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'kaer_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            174 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'kaer_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            175 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'kaer_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            176 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'kaer_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            177 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'kaer_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            178 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'kaer_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            179 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'kaer_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            180 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'kaer_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            181 =>
                array (
                    'platform_child_sign'       => "kaer",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'kaer_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //ruiyin
            182 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'ruiyin_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            183 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'ruiyin_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            184 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'ruiyin_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            185 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'ruiyin_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            186 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'ruiyin_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            187 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'ruiyin_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            188 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'ruiyin_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            189 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'ruiyin_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            190 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'ruiyin_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            191 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'ruiyin_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            192 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'ruiyin_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            193 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'ruiyin_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            194 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'ruiyin_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            195 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'ruiyin_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            196 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'ruiyin_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            197 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'ruiyin_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            198 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'ruiyin_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            199 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'ruiyin_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            200 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'ruiyin_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            201 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'ruiyin_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            202 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'ruiyin_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            203 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'ruiyin_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            204 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'ruiyin_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            205 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'ruiyin_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            206 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'ruiyin_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            207 =>
                array (
                    'platform_child_sign'       => "ruiyin",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'ruiyin_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //shuangzi
            208 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'shuangzi_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            209 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'shuangzi_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            210 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'shuangzi_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            211 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'shuangzi_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            212 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'shuangzi_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            213 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'shuangzi_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            214 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'shuangzi_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            215 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'shuangzi_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            216 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'shuangzi_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            217 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'shuangzi_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            218 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'shuangzi_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            219 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'shuangzi_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            220 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'shuangzi_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            221 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'shuangzi_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            222 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'shuangzi_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            223 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'shuangzi_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            224 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'shuangzi_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            225 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'shuangzi_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            226 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'shuangzi_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            227 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'shuangzi_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            228 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'shuangzi_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            229 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'shuangzi_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            230 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'shuangzi_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            231 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'shuangzi_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            232 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'shuangzi_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            233 =>
                array (
                    'platform_child_sign'       => "shuangzi",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'shuangzi_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //yongheng
            234 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'yongheng_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            235 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'yongheng_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            236 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'yongheng_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            237 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'yongheng_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            238 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'yongheng_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            239 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'yongheng_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            240 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'yongheng_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            241 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'yongheng_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            242 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'yongheng_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            243 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'yongheng_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            244 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'yongheng_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            245 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'yongheng_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            246 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'yongheng_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            247 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'yongheng_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            248 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'yongheng_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            249 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'yongheng_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            250 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'yongheng_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            251 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'yongheng_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            252 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'yongheng_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            253 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'yongheng_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            254 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'yongheng_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            255 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'yongheng_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            256 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'yongheng_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            257 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'yongheng_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            258 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'yongheng_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            259 =>
                array (
                    'platform_child_sign'       => "yongheng",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'yongheng_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //alapay
            260 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'alapay_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            261 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'alapay_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            262 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'alapay_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            263 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'alapay_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            264 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'alapay_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            265 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'alapay_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            266 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'alapay_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            267 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'alapay_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            268 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'alapay_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            269 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'alapay_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            270 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'alapay_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            271 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'alapay_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            272 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'alapay_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            273 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'alapay_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            274 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'alapay_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            275 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'alapay_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            276 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'alapay_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            277 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'alapay_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            278 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'alapay_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            279 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'alapay_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            280 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'alapay_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            281 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'alapay_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            282 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'alapay_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            283 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'alapay_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            284 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'alapay_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            285 =>
                array (
                    'platform_child_sign'       => "alapay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'alapay_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //xinft
            286 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'xinft_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            287 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'xinft_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            288 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'xinft_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            289 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'xinft_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            290 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'xinft_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            291 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'xinft_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            292 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'xinft_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            293 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'xinft_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            294 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'xinft_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            295 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'xinft_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            296 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'xinft_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            297 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'xinft_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            298 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'xinft_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            299 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'xinft_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            300 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'xinft_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            301 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'xinft_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            302 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'xinft_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            303 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'xinft_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            304 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'xinft_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            305 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'xinft_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            306 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'xinft_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            307 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'xinft_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            308 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'xinft_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            309 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'xinft_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            310 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'xinft_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            311 =>
                array (
                    'platform_child_sign'       => "xinft",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'xinft_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),

            //mpay
            312 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账',
                    'channel_sign'              => 'mpay_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            313 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝',
                    'channel_sign'              => 'mpay_zfb',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            314 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码',
                    'channel_sign'              => 'mpay_unionpay_qr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            315 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "business_card",
                    'banks_code'                => '',
                    'channel_name'              => '公司入款',
                    'channel_sign'              => 'mpay_business_card',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            316 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat",
                    'banks_code'                => '',
                    'channel_name'              => '微信',
                    'channel_sign'              => 'mpay_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            317 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "kuaijie",
                    'banks_code'                => '',
                    'channel_name'              => '快捷支付',
                    'channel_sign'              => 'mpay_kuaijie',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            318 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq",
                    'banks_code'                => '',
                    'channel_name'              => 'QQ支付',
                    'channel_sign'              => 'mpay_qq',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            319 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "withdraw",
                    'banks_code'                => '',
                    'channel_name'              => '公司出款',
                    'channel_sign'              => 'mpay_withdraw',
                    
                    'request_mode'              => 0,
                    'direction'                 => 0,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            320 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft",
                    'banks_code'                => '',
                    'channel_name'              => '财付通',
                    'channel_sign'              => 'mpay_cft',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            321 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付',
                    'channel_sign'              => 'mpay_jd',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            322 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "human",
                    'banks_code'                => '',
                    'channel_name'              => '人工充值',
                    'channel_sign'              => 'mpay_human',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            323 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay",
                    'banks_code'                => '',
                    'channel_name'              => '网银',
                    'channel_sign'              => 'mpay_unionpay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            324 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷',
                    'channel_sign'              => 'mpay_unionpay_kj',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            325 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_transfer",
                    'banks_code'                => '',
                    'channel_name'              => '线下银行转账',
                    'channel_sign'              => 'mpay_offline_transfer',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            326 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_wechat",
                    'banks_code'                => '',
                    'channel_name'              => '线下微信',
                    'channel_sign'              => 'mpay_offline_wechat',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            327 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_alipay",
                    'banks_code'                => '',
                    'channel_name'              => '线下支付宝',
                    'channel_sign'              => 'mpay_offline_alipay',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            328 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "offline_unionqr",
                    'banks_code'                => '',
                    'channel_name'              => '线下银联扫码',
                    'channel_sign'              => 'mpay_offline_unionqr',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            329 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "qq_wap",
                    'banks_code'                => '',
                    'channel_name'              => 'QQH5',
                    'channel_sign'              => 'mpay_qq_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            330 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "jd_wap",
                    'banks_code'                => '',
                    'channel_name'              => '京东支付H5',
                    'channel_sign'              => 'mpay_jd_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            331 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "cft_wap",
                    'banks_code'                => '',
                    'channel_name'              => '财付通H5',
                    'channel_sign'              => 'mpay_cft_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            332 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "zfb_wap",
                    'banks_code'                => '',
                    'channel_name'              => '支付宝H5',
                    'channel_sign'              => 'mpay_zfb_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            333 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "wechat_wap",
                    'banks_code'                => '',
                    'channel_name'              => '微信H5',
                    'channel_sign'              => 'mpay_wechat_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            334 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_wap",
                    'banks_code'                => '',
                    'channel_name'              => '网银H5',
                    'channel_sign'              => 'mpay_unionpay_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            335 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "transfer_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银行转账H5',
                    'channel_sign'              => 'mpay_transfer_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            336 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_qr_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联扫码H5',
                    'channel_sign'              => 'mpay_unionpay_qr_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
            337 =>
                array (
                    'platform_child_sign'       => "mpay",
                    'platform_sign'             => "fmis",
                    'type_sign'                 => "unionpay_kj_wap",
                    'banks_code'                => '',
                    'channel_name'              => '银联快捷H5',
                    'channel_sign'              => 'mpay_unionpay_kj_wap',
                    
                    'request_mode'              => 0,
                    'direction'                 => 1,
                    'status'                    => 1,
                    'created_at'                => '2020-01-01 08:08:08',
                    'updated_at'                => '2020-01-01 08:08:08',
                ),
        ));
    }
}
