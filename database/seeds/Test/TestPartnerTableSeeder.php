<?php

use Illuminate\Database\Seeder;

class TestPartnerTableSeeder extends Seeder
{
    /**
     * 尝试　创建商户
     * @return bool
     */
    public function run()
    {
        if (isProductEnv()) {
            return true;
        }

        $this->call(PartnerKlcTableSeeder::class);

    }
}
