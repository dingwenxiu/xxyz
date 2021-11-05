<?php

use Illuminate\Database\Seeder;

// 商户
class PartnerTableSeeder extends Seeder
{

    public function run()
    {
        // 1. 添加默认商户游侠的二维码
        DB::table('partner_setting')->insert([
            'partner_sign'        => "YX",
            'qr_code_1'           => "yx/activity/20191126143039.png",
            'qr_code_2'           => "yx/activity/20191126145951.png",
            'qr_code_3'           => "",
        ]);

        // 2. 添加默认商户游侠的域名
        $domainList = [
            [
                'partner_sign'  => "YX",
                'name'          => "游侠投注 API(线上)",
                'domain'        => "api.youxiabw.com ",
                'type'          => 1,
                'env_type'      => 3,
                'remark'        => "暂无备注",
                'status'        => 1,
            ],

            [
                'partner_sign'  => "YX",
                'name'          => "游侠投注 API(测试)",
                'domain'        => "api.play322.com ",
                'type'          => 1,
                'env_type'      => 2,
                'remark'        => "暂无备注",
                'status'        => 1,
            ],

            [
                'partner_sign'  => "YX",
                'name'          => "游侠商户 API(线上)",
                'domain'        => "partner-api.youxiabw.com ",
                'type'          => 2,
                'env_type'      => 3,
                'remark'        => "暂无备注",
                'status'        => 1,
            ],

            [
                'partner_sign'  => "YX",
                'name'          => "游侠商户 API(测试)",
                'domain'        => "partner-api.play322.com ",
                'type'          => 2,
                'env_type'      => 2,
                'remark'        => "暂无备注",
                'status'        => 1,
            ],
        ];

        foreach ($domainList as $item) {
            $domain = new App\Models\Partner\PartnerDomain();
            $res = $domain->saveItem($item, null);
            info($res);
        }

        // 3. 添加默认商户 游侠
        $partnerData = [
            'id'                    => 1,
            'name'                  => "游侠娱乐",
            'sign'                  => "YX",
            'theme'                 => "default",
            'remark'                => "系统默认商户",
            'admin_email'           => "youxia888@gmail.com",
            'logo_image_pc_1'       => 'yx/logo/20191126085037.png',
            'logo_image_pc_2'       => 'yx/logo/20191126085044.png',
            'logo_image_h5_1'       => 'yx/logo/20191126085037.png',
            'logo_image_h5_2'       => 'yx/logo/20191126085044.png',
            'admin_password'        => "1234qwer",
            'admin_fund_password'   => "qwer1234",
        ];

        $partner  = new App\Models\Partner\Partner();
        $res = $partner->saveItem($partnerData);
        if (!is_object($res)) {
            info($res);
        }

        // 4. 添加默认公告
        $notice = [
            [
                'partner_sign'  => "YX",
                'device_type'   => "1",
                'type'          => "1",
                'title'         => "测试001",
                'content'       => "测试内容",
                'start_time'    => time(),
                'end_time'      => time() + 86400,
            ],

            [
                'partner_sign'  => "YX",
                'device_type'   => "2",
                'type'          => "1",
                'title'         => "测试002",
                'content'       => "测试内容2",
                'start_time'    => time(),
                'end_time'      => time() + 86400,
            ],
        ];

        \App\Models\Partner\PartnerNotice::insert($notice);

        // 5. 添加默认帮助分类
        $helpMenu = [
            [
                'id' => 1,
                'partner_sign'  => "YX",
                'name'         => "投注指南",
            ],

            [
                'id' => 2,
                'partner_sign'  => "YX",
                'name'         => "充值指南",
            ],
            [
                'id' => 3,
                'partner_sign'  => "YX",
                'name'         => "新手指引",
            ],
        ];

        \App\Models\Partner\HelpMenu::insert($helpMenu);

        // 添加测试 帮助文章
        $helpContent = [
            [
                'pid'  => 1,
                'title'  => "投注细节",
                'content'   => "追号投注，普通投注，撤销投注，异常撤销投注",
                'status'    => 1,
            ],

            [
                'pid'  => 2,
                'title'  => "手机充值",
                'content'  => "充值渠道，银行卡绑定，支付宝支付，微信支付",
                'status'    => 1,
            ],
            [
                'pid'  => 3,
                'title'  => "新手指导",
                'content'  => "如何投注，如何提现，如何转账，如何充值",
                'status'    => 1,
            ],
        ];

        \App\Models\Partner\HelpCenter::insert($helpContent);


        // 6. 添加默认VIP 等级
        $vipLevel = [
            [
                'partner_sign' => "YX",
                'vip_level' => 1,
                'name' => "vip1",
                'show_name' => "vip1",
                'recharge_total' => 0,
                'icon'=>'system/vip/vip1.png'
            ],
            [
                'partner_sign' => "YX",
                'vip_level' => 2,
                'name' => "vip2",
                'show_name' => "vip2",
                'recharge_total' => 100,
                'icon'=>'system/vip/vip2.png'
            ],
            [
                'partner_sign' => "YX",
                'vip_level' => 3,
                'name' => "vip3",
                'show_name' => "vip3",
                'recharge_total' => 500,
                'icon'=>'system/vip/vip3.png'
            ],
            [
                'partner_sign' => "YX",
                'vip_level' => 4,
                'name' => "vip4",
                'show_name' => "vip4",
                'recharge_total' => 2000,
                'icon'=>'system/vip/vip4.png'
            ],
            [
                'partner_sign' => "YX",
                'vip_level' => 5,
                'name' => "vip5",
                'show_name' => "vip5",
                'recharge_total' => 10000,
                'icon'=>'system/vip/vip5.png'
            ],
            [
                'partner_sign' => "YX",
                'vip_level' => 6,
                'name' => "vip6",
                'show_name' => "vip6",
                'recharge_total' => 100000,
                'icon'=>'system/vip/vip6.png'
            ],
            [
                'partner_sign' => "YX",
                'vip_level' => 7,
                'name' => "vip7",
                'show_name' => "vip7",
                'recharge_total' => 500000,
                'icon'=>'system/vip/vip7.png'
            ],
            [
                'partner_sign' => "YX",
                'vip_level' => 8,
                'name' => "vip8",
                'show_name' => "vip8",
                'recharge_total' => 2000000,
                'icon'=>'system/vip/vip8.png'
            ],
            [
                'partner_sign' => "YX",
                'vip_level' => 9,
                'name' => "vip9",
                'show_name' => "vip9",
                'recharge_total' => 10000000,
                'icon'=>'system/vip/vip9.png'
            ],
        ];

        \App\Models\Player\PlayerVipConfig::insert($vipLevel);
    }
}
