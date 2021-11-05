<?php

use Illuminate\Database\Seeder;

class PartnerKlcTableSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run()
    {
        // 1. 添加默认商户游侠的二维码
        DB::table('partner_setting')->insert([
            'partner_sign'        => "KLC",
            'qr_code_1'           => "klc/activity/20191126143039.png",
            'qr_code_2'           => "klc/activity/20191126145951.png",
            'qr_code_3'           => "",
        ]);

        $data = [
                'sign'                  => "KLC",
                'name'                  => "快乐彩",
                'theme'                 => "default",
                'remark'                => "系统默认商户",
                'add_template_sign'     => "klc",

                'admin_email'           => "klc888@gmail.com",
                'admin_password'        => "1234qwer",
                'admin_fund_password'   => "qwer1234",

                'logo_image_pc_1'       => 'klc/logo/logo_image_pc_1.png',
                'logo_image_pc_2'       => 'klc/logo/logo_image_pc_2.png',
                'logo_image_h5_1'       => 'klc/logo/logo_image_h5_1.png',
                'logo_image_h5_2'       => 'klc/logo/logo_image_h5_2.png',
        ];


        $adminUser = \App\Models\Admin\AdminUser::find(1);
        $partner = new \App\Models\Partner\Partner();
        $partner->saveItem($data, $adminUser);

        // 添加域名
        $domainList = [
            [
                'partner_sign'  => $data['sign'],
                'name'          => "投注API　线上",
                'domain'        => "api.luck03.com",
                'type'          => 1,
                'env_type'      => 3,
                'remark'        => "暂无备注",
                'status'        => 1,
            ],
            [
                'partner_sign'  => $data['sign'],
                'name'          => "投注API　测试",
                'domain'        => "api.8521pk.com",
                'type'          => 1,
                'env_type'      => 2,
                'remark'        => "暂无备注",
                'status'        => 1,
            ],
            [
                'partner_sign'  => $data['sign'],
                'name'          => "商户API　线上",
                'domain'        => "partner-api.luck03.com",
                'type'          => 2,
                'env_type'      => 3,
                'remark'        => "暂无备注",
                'status'        => 1,
            ],
            [
                'partner_sign'  => $data['sign'],
                'name'          => "商户API　测试",
                'domain'        => "partner-api.8521pk.com",
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

        // 6. 添加默认VIP 等级
        $vipLevel = [
            [
                'partner_sign'      => "KLC",
                'vip_level'         => 1,
                'name'              => "vip1",
                'show_name'         => "vip1",
                'recharge_total'    => 0,
                'icon'              =>'system/vip/vip1.png'
            ],
            [
                'partner_sign'      => "KLC",
                'vip_level'         => 2,
                'name'              => "vip2",
                'show_name'         => "vip2",
                'recharge_total'    => 100,
                'icon'              =>'system/vip/vip2.png'
            ],
            [
                'partner_sign'      => "KLC",
                'vip_level'         => 3,
                'name'              => "vip3",
                'show_name'         => "vip3",
                'recharge_total'    => 500,
                'icon'              => 'system/vip/vip3.png'
            ],
            [
                'partner_sign'      => "KLC",
                'vip_level'         => 4,
                'name'              => "vip4",
                'show_name'         => "vip4",
                'recharge_total'    => 2000,
                'icon'              => 'system/vip/vip4.png'
            ],
            [
                'partner_sign'      => "KLC",
                'vip_level'         => 5,
                'name'              => "vip5",
                'show_name'         => "vip5",
                'recharge_total'    => 10000,
                'icon'              => 'system/vip/vip5.png'
            ],
            [
                'partner_sign'      => "KLC",
                'vip_level'         => 6,
                'name'              => "vip6",
                'show_name'         => "vip6",
                'recharge_total'    => 100000,
                'icon'              => 'system/vip/vip6.png'
            ],
            [
                'partner_sign'      => "KLC",
                'vip_level'         => 7,
                'name'              => "vip7",
                'show_name'         => "vip7",
                'recharge_total'    => 500000,
                'icon'              => 'system/vip/vip7.png'
            ],
            [
                'partner_sign'      => "KLC",
                'vip_level'         => 8,
                'name'              => "vip8",
                'show_name'         => "vip8",
                'recharge_total'    => 2000000,
                'icon'              => 'system/vip/vip8.png'
            ],
            [
                'partner_sign'      => "KLC",
                'vip_level'         => 9,
                'name'              => "vip9",
                'show_name'         => "vip9",
                'recharge_total'    => 10000000,
                'icon'              => 'system/vip/vip9.png'
            ],
        ];

        \App\Models\Player\PlayerVipConfig::insert($vipLevel);
    }


}
