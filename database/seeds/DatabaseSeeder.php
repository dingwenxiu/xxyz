<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run()
    {
        // 清理缓存
        \App\Lib\Logic\Cache\IssueCache::clearAll();
        \App\Lib\Logic\Cache\LotteryCache::flushPartnerAll("YX");
        \App\Lib\Logic\Cache\LotteryCache::flushPartnerAll("system");

        // 清理缓存
        Artisan::call(
            'cache:clear', []
        );


        $this->call(AccountTableSeeder::class);
        $this->call(SystemBankTableSeeder::class);
        $this->call(SystemCityTableSeeder::class);
        $this->call(AdminUserTableSeeder::class);
        $this->call(AdminMenuTableSeeder::class);
        $this->call(AdminModulesTableSeeder::class);
        $this->call(ConfigureTableSeeder::class);
        $this->call(LotteryTableSeeder::class);
        $this->call(IssueRuleTableSeeder::class);

        // 包网财务配置
        $this->call(FinanceChannelTypeTableSeeder::class);
        $this->call(FinancePlatformTableSeeder::class);
        $this->call(FinancePlatformChannelTableSeeder::class);

        // 活动
        $this->call(ActivityRulesTableSeeder::class);
        $this->call(ActivityPrizesTableSeeder::class);

        $this->call(TemplateTableSeeder::class);
        // 娱乐城
        $this->call(PartnerCasinoCategories::class);


        // 生成玩法
        Artisan::call(
            'lottery:genMethods', []
        );

        // 商户
        $this->call(PartnerMenuTableSeeder::class);
        $this->call(PartnerTableSeeder::class);
        

        // 生成测试商户
        $this->call(TestPartnerTableSeeder::class);

        // 生成测试用户
        $this->call(PlayerGenTableSeeder::class);

        // 生成异常发送群组
        $this->call(SysTelegramChannelSeeder::class);

        // 生成奖期
        Artisan::call(
            'lottery:initIssue', []
        );
    }
}
