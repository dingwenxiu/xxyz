<?php

use Illuminate\Database\Seeder;

class FinancePlatformTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        db()->table('finance_platform')->delete();
        if (isProductEnv()) {
            $platform_url = 'https://api.fmisco.com';
        } else {
            $platform_url = 'https://api.cqvip9.com';
        }

        db()->table('finance_platform')->insert(array (
            0 =>
                array (
                    'id'                    => 1,
                    'platform_name'         => 'FMIS',
                    'platform_url'          => $platform_url,
                    'platform_sign'         => 'fmis',
                    'is_pull'               => 1,
                    'whitelist_ips'         => '103.42.94.10|192.168.10.10|127.0.0.1|103.104.16.186|3.113.223.134|10.244.2.1|10.244.1.1|10.244.1.0|130.105.186.86',
                    'created_at'            => '2019-10-03 07:32:52',
                    'updated_at'            => '2019-10-03 07:32:52',
                )
        ));
    }
}
