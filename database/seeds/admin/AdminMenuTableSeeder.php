<?php

use Illuminate\Database\Seeder;

class AdminMenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ 系统管理 @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
        // 系统管理
        DB::table('admin_menus')->insert([
            'id'                    => 10000,
            'title'                 => "系统管理",
            'pid'                   => 0,
            'rid'                   => "10000",
            'type'                  => 0,
            'route'                 => 0,
            'status'                => 1,
            'sort'                  => 1,
            'css_class'             => 'fa fa-lg fa-fw fa-cog',
        ]);

        /** =========== 配置管理 =========== */
        DB::table('admin_menus')->insert([
            'id'                    => 10100,
            'rid'                   => "10000|10100",
            'title'                 => "配置列表",
            'pid'                   => 10000,
            'type'                  => 0,
            'route'                 => "system/configList",
            'api_path'              => "system/configure-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10101,
            'rid'                   => "10000|10100|10101",
            'title'                 => "添加配置",
            'pid'                   => 10100,
            'type'                  => 1,
            'route'                 => "system/configAdd",
            'api_path'              => "system/configure-add",
            'status'                => 1,
            'css_class'             => 'fa fa-lg fa-fw fa-plus',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10102,
            'rid'                   => "10000|10100|10102",
            'title'                 => "配置详情",
            'pid'                   => 10100,
            'type'                  => 1,
            'route'                 => "system/configDetail",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10103,
            'rid'                   => "10000|10100|10103",
            'title'                 => "配置状态",
            'pid'                   => 10100,
            'type'                  => 1,
            'route'                 => "system/configStatus",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10104,
            'rid'                   => "10000|10100|10104",
            'title'                 => "刷新配置",
            'pid'                   => 10100,
            'type'                  => 1,
            'route'                 => "system/configFlush",
            'status'                => 1,
            'api_path'              => "",
            'css_class'             => 'fa fa-lg fa-fw fa-plus',
        ]);

        /**  ============= 公告管理 ============= */
        DB::table('admin_menus')->insert([
            'id'                    => 10600,
            'rid'                   => "10000|10600",
            'title'                 => "公告管理",
            'pid'                   => 10000,
            'type'                  => 0,
            'route'                 => "noticeList",
            'api_path'              => "system/notice-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10601,
            'rid'                   => "10000|10600|10601",
            'title'                 => "刷新缓存",
            'pid'                   => 10600,
            'type'                  => 1,
            'route'                 => "noticeFlush",
            'api_path'              => "system/notice-flush",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10602,
            'rid'                   => "10000|10600|10602",
            'title'                 => "添加公告",
            'pid'                   => 10600,
            'type'                  => 1,
            'route'                 => "noticeAdd",
            'api_path'              => "system/notice-add",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10603,
            'rid'                   => "10000|10600|10603",
            'title'                 => "修改公告状态",
            'pid'                   => 10600,
            'type'                  => 1,
            'route'                 => "noticeStatus",
            'api_path'              => "system/notice-status",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10604,
            'rid'                   => "10000|10600|10604",
            'title'                 => "置顶公告",
            'pid'                   => 10600,
            'type'                  => 1,
            'route'                 => "noticeTop",
            'api_path'              => "system/notice-top",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10605,
            'rid'                   => "10000|10600|10605",
            'title'                 => "公告详情",
            'pid'                   => 10600,
            'type'                  => 1,
            'route'                 => "noticeDetail",
            'api_path'              => "system/notice-detail",
            'status'                => 1,
        ]);

        /**  ============= 小飞机管理 ============= */
        DB::table('admin_menus')->insert([
            'id'                    => 10700,
            'rid'                   => "10000|10700",
            'title'                 => "小飞机管理",
            'pid'                   => 10000,
            'sort'                  => 7,
            'type'                  => 0,
            'route'                 => "telegramChannelList",
            'api_path'              => "system/telegram-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10701,
            'rid'                   => "10000|10700|10701",
            'title'                 => "添加频道",
            'pid'                   => 10700,
            'type'                  => 1,
            'route'                 => "telegramChannelAdd",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10702,
            'rid'                   => "10000|10700|10702",
            'title'                 => "修改状态",
            'pid'                   => 10700,
            'type'                  => 1,
            'route'                 => "telegramChannelDel",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10703,
            'rid'                   => "10000|10700|10703",
            'title'                 => "生成ID",
            'pid'                   => 10700,
            'type'                  => 1,
            'route'                 => "telegramChannelGenId",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 10800,
            'rid'                   => "10000|10800",
            'title'                 => "模板列表",
            'pid'                   => 10000,
            'sort'                  => 8,
            'type'                  => 0,
            'route'                 => "TemplateGitList",
            'api_path'              => "system/merchants-config",
            'status'                => 1,
        ]);


		DB::table('admin_menus')->insert([
			'id'                    => 10400,
			'rid'                   => "10000|10400",
			'title'                 => "审核管理",
			'pid'                   => 10000,
			'sort'                  => 9,
			'type'                  => 0,
			'route'                 => "system/reviewList",
			'api_path'              => "system/review-list",
			'status'                => 1,
		]);


        /**  =============　系统管理　－ 推送管理 End ============= */

        /** @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ 游戏管理 @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
        DB::table('admin_menus')->insert([
            'id'                    => 20000,
            'rid'                   => "20000",
            'title'                 => "游戏管理",
            'pid'                   => 0,
            'type'                  => 0,
            'route'                 => '',
            'status'                => 1,
            'sort'                  => 3,
            'css_class'             => 'fa fa-lg fa-fw fa-gamepad',
        ]);

        /** =========== 游戏管理 - 彩种管理 Start =========== */
        DB::table('admin_menus')->insert([
            'id'                    => 20100,
            'rid'                   => "20000|20100",
            'title'                 => "游戏列表",
            'pid'                   => 20000,
            'type'                  => 0,
            'sort'                  => 1,
            'route'                 => "lottery/lotteryList",
            'api_path'              => "lottery/lottery-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20101,
            'rid'                   => "20000|20100|20101",
            'title'                 => "添加游戏",
            'pid'                   => 20100,
            'type'                  => 1,
            'route'                 => "lottery/lotteryAdd",
            'api_path'              => "lottery/lottery-add",
            'status'                => 1,
            'css_class'             => 'fa fa-lg fa-fw fa-plus',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20102,
            'rid'                   => "20000|20100|20102",
            'title'                 => "游戏状态",
            'pid'                   => 20100,
            'type'                  => 1,
            'route'                 => "lottery/lotteryStatus",
            'api_path'              => "lottery/lottery-status",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20103,
            'rid'                   => "20000|20100|20103",
            'title'                 => "删除游戏",
            'pid'                   => 20100,
            'type'                  => 1,
            'route'                 => "lottery/lotteryDel",
            'api_path'              => "lottery/lottery-del",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20104,
            'rid'                   => "20000|20100|20104",
            'title'                 => "刷新缓存",
            'pid'                   => 20100,
            'type'                  => 1,
            'route'                 => "lottery/lotteryFlush",
            'api_path'              => "lottery/lottery-flush",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20105,
            'rid'                   => "20000|20100|20105",
            'title'                 => "編輯游戏",
            'pid'                   => 20100,
            'type'                  => 1,
            'route'                 => "lottery/lotteryEdit",
            'api_path'              => "lottery/lottery-edit",
            'status'                => 1,
            'css_class'             => 'fa fa-lg fa-fw fa-plus',
        ]);

        /** =========== 游戏管理 - 玩法管理 Start =========== */
        DB::table('admin_menus')->insert([
            'id'                    => 20200,
            'rid'                   => "20000|20200",
            'title'                 => "玩法列表",
            'pid'                   => 20000,
            'type'                  => 0,
            'sort'                  => 2,
            'route'                 => "lottery/methodList",
            'api_path'              => "lottery/method-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20201,
            'rid'                   => "20000|20200|20201",
            'title'                 => "玩法状态",
            'pid'                   => 20200,
            'type'                  => 1,
            'route'                 => "lottery/methodStatus",
            'api_path'              => "lottery/method-status",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20202,
            'rid'                   => "20000|20200|20202",
            'title'                 => "玩法排序",
            'pid'                   => 20200,
            'type'                  => 1,
            'route'                 => "lottery/methodSort",
            'api_path'              => "lottery/method-sort",
            'status'                => 1,
        ]);

        /** =========== 游戏管理 - 奖期管理 Start =========== */
        DB::table('admin_menus')->insert([
            'id'                    => 20300,
            'rid'                   => "20000|20300",
            'title'                 => "奖期列表",
            'pid'                   => 20000,
            'type'                  => 0,
            'sort'                  => 3,
            'route'                 => "lottery/issueList",
            'api_path'              => "lottery/issue-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20301,
            'rid'                   => "20000|20300|20301",
            'title'                 => "生成奖期",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/issueGen",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20302,
            'rid'                   => "20000|20300|20302",
            'title'                 => "录号",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/issueEncode",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20303,
            'rid'                   => "20000|20300|20303",
            'title'                 => "计奖",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/issueOpen",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20304,
            'rid'                   => "20000|20300|20304",
            'title'                 => "派奖",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/issueSend",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);


        DB::table('admin_menus')->insert([
            'id'                    => 20305,
            'rid'                   => "20000|20300|20305",
            'title'                 => "追号",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/issueTrace",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20306,
            'rid'                   => "20000|20300|20306",
            'title'                 => "追号",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/issueCommission",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20307,
            'rid'                   => "20000|20300|20307",
            'title'                 => "删除奖期",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/issueDel",
            'api_path'              => "lottery/issue-del",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20308,
            'rid'                   => "20000|20300|20308",
            'title'                 => "详情",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/issueDetail",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20309,
            'rid'                   => "20000|20300|20309",
            'title'                 => "撤单",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/issueCancel",
            'api_path'              => "",
            'status'                => 1,
        ]);

        /** =========== 游戏管理 - 奖期规则管理 Start =========== */
        DB::table('admin_menus')->insert([
            'id'                    => 20400,
            'rid'                   => "20000|20400",
            'title'                 => "奖期规则",
            'pid'                   => 20000,
            'type'                  => 0,
            'sort'                  => 4,
            'route'                 => "lottery/issueRuleList",
            'api_path'              => "lottery/issue-rule-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20401,
            'rid'                   => "20000|20400|20401",
            'title'                 => "添加规则",
            'pid'                   => 20400,
            'type'                  => 1,
            'route'                 => "lottery/issueRuleAdd",
            'api_path'              => "lottery/issue-rule-add",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20402,
            'rid'                   => "20000|20400|20402",
            'title'                 => "删除规则",
            'pid'                   => 20400,
            'type'                  => 1,
            'route'                 => "lottery/issueRuleDel",
            'api_path'              => "lottery/issue-rule-del",
            'status'                => 1,
        ]);

        /** =========== 游戏管理 - 追号管理 Start =========== */
        DB::table('admin_menus')->insert([
            'id'                    => 20500,
            'rid'                   => "20000|20500",
            'title'                 => "追号列表",
            'pid'                   => 20000,
            'type'                  => 0,
            'sort'                  => 6,
            'route'                 => "lottery/traceList",
            'api_path'              => "lottery/trace-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20501,
            'rid'                   => "20000|20500|20501",
            'title'                 => "追号详情",
            'pid'                   => 20500,
            'type'                  => 1,
            'route'                 => "lottery/traceDetail",
            'api_path'              => "lottery/trace-detail",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20502,
            'rid'                   => "20000|20500|20502",
            'title'                 => "结束追号",
            'pid'                   => 20500,
            'type'                  => 1,
            'route'                 => "lottery/traceEnd",
            'api_path'              => "lottery/trace-end",
            'status'                => 1,
        ]);

        /** =========== 游戏管理 - 投注管理 Start =========== */
        DB::table('admin_menus')->insert([
            'id'                    => 20600,
            'rid'                   => "20000|20600",
            'title'                 => "投注列表",
            'pid'                   => 20000,
            'type'                  => 0,
            'sort'                  => 5,
            'route'                 => "lottery/projectList",
            'api_path'              => "lottery/project-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20601,
            'rid'                   => "20000|20600|20601",
            'title'                 => "投注详情",
            'pid'                   => 20600,
            'type'                  => 1,
            'route'                 => "lottery/projectDetail",
            'api_path'              => "lottery/project-detail",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 20602,
            'rid'                   => "20000|20600|20602",
            'title'                 => "撤单",
            'pid'                   => 20600,
            'type'                  => 1,
            'route'                 => "lottery/projectCancel",
            'api_path'              => "lottery/project-cancel",
            'status'                => 1,
        ]);

        /** =========== 游戏管理 - 投注管理 Start =========== */
        DB::table('admin_menus')->insert([
            'id'                    => 20700,
            'rid'                   => "20000|20700",
            'title'                 => "控水列表",
            'pid'                   => 20000,
            'type'                  => 0,
            'sort'                  => 7,
            'route'                 => "lottery/jackpotIssueList",
            'api_path'              => "lottery/jackpot-issue-list",
            'status'                => 1,
        ]);

        /** @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ 玩家管理 @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
        DB::table('admin_menus')->insert([
            'id'                    => 30000,
            'title'                 => "玩家管理",
            'pid'                   => 0,
            'type'                  => 0,
            'route'                 => '',
            'status'                => 1,
            'sort'                  => 4,
            'css_class'             => 'fa fa-lg fa-fw fa-user-md',
        ]);

        /** =========== 玩家管理 - 玩家列表 Start =========== */
        DB::table('admin_menus')->insert([
            'id'                    => 30100,
            'rid'                   => "30000|30100",
            'title'                 => "玩家列表",
            'pid'                   => 30000,
            'type'                  => 0,
            'sort'                  => 1,
            'route'                 => "player/playerList",
            'api_path'              => "player/player-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 30101,
            'rid'                   => "30000|30100|30101",
            'title'                 => "玩家详情",
            'pid'                   => 30100,
            'type'                  => 1,
            'route'                 => "player/detail",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 30102,
            'rid'                   => "30000|30100|30102",
            'title'                 => "玩家状态",
            'pid'                   => 30100,
            'type'                  => 1,
            'route'                 => "player/status",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 30200,
            'rid'                   => "30000|30200",
            'title'                 => "__银行卡",
            'pid'                   => 30000,
            'type'                  => 0,
            'sort'                  => 2,
            'route'                 => "player/cardList",
            'api_path'              => "player/player-card-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 30201,
            'rid'                   => "30000|30200|30201",
            'title'                 => "银行卡状态",
            'pid'                   => 30200,
            'type'                  => 1,
            'route'                 => "player/cardStatus",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 30300,
            'rid'                   => "30000|30300",
            'title'                 => "工资记录",
            'pid'                   => 30000,
            'type'                  => 0,
            'sort'                  => 3,
            'route'                 => "player/salaryReportList",
            'api_path'              => "player/salary-report-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 30301,
            'rid'                   => "30000|30300|30301",
            'title'                 => "工资详情",
            'pid'                   => 30300,
            'type'                  => 1,
            'route'                 => "player/salaryDetail",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 30400,
            'rid'                   => "30000|30400",
            'title'                 => "分红记录",
            'pid'                   => 30000,
            'type'                  => 0,
            'sort'                  => 4,
            'route'                 => "player/dividendReportList",
            'api_path'              => "player/dividend-report-list",
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 30401,
            'rid'                   => "30000|30400|30401",
            'title'                 => "分红详情",
            'pid'                   => 30400,
            'type'                  => 1,
            'route'                 => "player/dividendDetail",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 30500,
            'rid'                   => "30000|30500",
            'title'                 => "账变列表",
            'pid'                   => 30000,
            'type'                  => 0,
            'route'                 => "account/accountChangeReportList",
            'api_path'              => "account/account-report-list",
            'sort'                  => 6,
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 30501,
            'rid'                   => "30000|30500|30501",
            'title'                 => "账变详情",
            'pid'                   => 30500,
            'type'                  => 1,
            'route'                 => "account/accountChangeReportDetail",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);

        /** =============================== 开发管理 ================================ */
        DB::table('admin_menus')->insert([
            'id'                    => 40000,
            'title'                 => "开发管理",
            'pid'                   => 0,
            'rid'                   => "40000",
            'type'                  => 0,
            'route'                 => 0,
            'status'                => 1,
            'sort'                  => 5,
            'css_class'             => 'fa fa-lg fa-fw fa-cny',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 40100,
            'rid'                   => "40000|40100",
            'title'                 => "账变类型",
            'pid'                   => 40000,
            'type'                  => 0,
            'route'                 => "account/accountChangeTypeList",
            'api_path'              => "account/type-list",
            'sort'                  => 1,
            'status'                => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 40101,
            'rid'                   => "40000|40100|40101",
            'title'                 => "添加类型",
            'pid'                   => 40100,
            'type'                  => 1,
            'route'                 => "account/accountChangeTypeAdd",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                    => 40103,
            'rid'                   => "40000|40100|40103",
            'title'                 => "刷新缓存",
            'pid'                   => 40100,
            'type'                  => 1,
            'route'                 => "account/accountChangeTypeFlush",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);

        /** =============================== 财务管理 ================================ */
        // 报表管理
        DB::table('admin_menus')->insert([
            'id'                        => 50000,
            'title'                     => "充提管理",
            'rid'                       => "50000",
            'pid'                       => 0,
            'type'                      => 0,
            'route'                     => 0,
            'status'                    => 1,
            'sort'                      => 6,
            'css_class'                 => 'fa fa-lg fa-fw fa-credit-card',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50100,
            'rid'                       => "50000|50100",
            'title'                     => "充值列表",
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => "finance/rechargeList",
            'api_path'                  => "finance/recharge-list",
            'sort'                      => 1,
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50101,
            'rid'                       => "50000|50100|50101",
            'title'                     => "人工处理",
            'pid'                       => 50100,
            'type'                      => 1,
            'route'                     => "finance/rechargeHand",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50102,
            'rid'                       => "50000|50100|50102",
            'title'                     => "日志详情",
            'pid'                       => 50100,
            'type'                      => 1,
            'route'                     => "finance/rechargeLogDetail",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50200,
            'rid'                       => "50000|50200",
            'title'                     => "充值日志",
            'pid'                       => 50000,
            'type'                      => 0,
            'sort'                      => 2,
            'route'                     => "finance/rechargeLogList",
            'api_path'                  => "finance/recharge-log-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50300,
            'rid'                       => "50000|50300",
            'title'                     => "提现列表",
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => "finance/withdrawList",
            'api_path'                  => "finance/withdraw-list",
            'sort'                      => 3,
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50301,
            'rid'                       => "50000|50300|50301",
            'title'                     => "人工处理",
            'pid'                       => 50300,
            'type'                      => 1,
            'route'                     => "finance/withdrawHand",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50302,
            'rid'                       => "50000|50300|50302",
            'title'                     => "日志详情",
            'pid'                       => 50300,
            'type'                      => 1,
            'route'                     => "finance/withdrawLogDetail",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50303,
            'rid'                       => "50000|50300|50303",
            'title'                     => "生成订单",
            'pid'                       => 50300,
            'type'                      => 1,
            'route'                     => "finance/withdrawGenOrder",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50304,
            'rid'                       => "50000|50300|50304",
            'title'                     => "审核订单",
            'pid'                       => 50300,
            'type'                      => 1,
            'route'                     => "finance/withdrawCheckProcess",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50400,
            'rid'                       => "50000|50400",
            'title'                     => "提现日志",
            'pid'                       => 50000,
            'type'                      => 0,
            'sort'                      => 4,
            'route'                     => "finance/withdrawLogList",
            'api_path'                  => "finance/withdraw-log-list",
            'status'                    => 1,
        ]);

        //支付厂商列表
        DB::table('admin_menus')->insert([
            'id'                        => 50500,
            'rid'                       => "50000|50500",
            'title'                     => "支付厂商",
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => "finance/platform/list",
            'api_path'                  => "finance/platform/list",
            'sort'                      => 5,
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50501,
            'rid'                       => "50000|50500|50501",
            'title'                     => "添加/编辑",
            'pid'                       => 50500,
            'type'                      => 1,
            'route'                     => "finance/platform/create/{id?}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50502,
            'rid'                       => "50000|50500|50502",
            'title'                     => "删除",
            'pid'                       => 50500,
            'type'                      => 1,
            'route'                     => "finance/platform/del/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        //支付账户
        DB::table('admin_menus')->insert([
            'id'                        => 50600,
            'rid'                       => "50000|50600",
            'title'                     => "支付账户",
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => "finance/platformAccount/list",
            'api_path'                  => "finance/platformAccount/list",
            'sort'                      => 6,
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50601,
            'rid'                       => "50000|50600|50601",
            'title'                     => "状态",
            'pid'                       => 50600,
            'type'                      => 1,
            'route'                     => "finance/platformAccount/status/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50602,
            'rid'                       => "50000|50600|50602",
            'title'                     => "添加-修改",
            'pid'                       => 50600,
            'type'                      => 1,
            'route'                     => "finance/platformAccount/create/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50603,
            'rid'                       => "50000|50600|50603",
            'title'                     => "删除",
            'pid'                       => 50600,
            'type'                      => 1,
            'route'                     => "finance/platformAccount/del/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50604,
            'rid'                       => "50000|50600|50604",
            'title'                     => "获取充值渠道",
            'pid'                       => 50600,
            'type'                      => 1,
            'route'                     => "finance/platformAccount/foreign_channel/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        //支付类型
        DB::table('admin_menus')->insert([
            'id'                        => 50700,
            'rid'                       => "50000|50700",
            'title'                     => "支付类型",
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => "finance/channelType/list",
            'api_path'                  => "finance/channelType/list",
            'sort'                      => 7,
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50701,
            'rid'                       => "50000|50700|50701",
            'title'                     => "添加-修改",
            'pid'                       => 50700,
            'type'                      => 1,
            'route'                     => "finance/channelType/create/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50702,
            'rid'                       => "50000|50700|50702",
            'title'                     => "删除",
            'pid'                       => 50700,
            'type'                      => 1,
            'route'                     => "finance/channelType/del/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50703,
            'rid'                       => "50000|50700|50703",
            'title'                     => "图片上传",
            'pid'                       => 50700,
            'type'                      => 1,
            'route'                     => "finance/channelType/channelTypeUploadImg",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        //支付厂商-开放渠道
        DB::table('admin_menus')->insert([
            'id'                        => 50800,
            'rid'                       => "50000|50800",
            'title'                     => "支付厂商-开放渠道",
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => "finance/platformChannel/list",
            'api_path'                  => "finance/platformChannel/list",
            'sort'                      => 8,
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50801,
            'rid'                       => "50000|50800|50801",
            'title'                     => "添加-修改",
            'pid'                       => 50800,
            'type'                      => 1,
            'route'                     => "finance/platformChannel/create/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50802,
            'rid'                       => "50000|50800|50802",
            'title'                     => "删除",
            'pid'                       => 50800,
            'type'                      => 1,
            'route'                     => "finance/platformChannel/del/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        //支付账户-开放渠道
        DB::table('admin_menus')->insert([
            'id'                        => 50900,
            'rid'                       => "50000|50900",
            'title'                     => "支付账户-开放渠道",
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => "finance/platformAccountChannel/list",
            'api_path'                  => "finance/platformAccountChannel/list",
            'sort'                      => 9,
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50901,
            'rid'                       => "50000|50900|50901",
            'title'                     => "添加-修改",
            'pid'                       => 50900,
            'type'                      => 1,
            'route'                     => "finance/platformAccountChannel/create/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50902,
            'rid'                       => "50000|50900|50902",
            'title'                     => "删除",
            'pid'                       => 50900,
            'type'                      => 1,
            'route'                     => "finance/platformAccountChannel/del/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 50903,
            'rid'                       => "50000|50900|50903",
            'title'                     => "状态",
            'pid'                       => 50900,
            'type'                      => 1,
            'route'                     => "finance/platformAccountChannel/status/{id}",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        // 提现记录(风控)
        DB::table('admin_menus')->insert([
            'id'                        => 51000,
            'rid'                       => "50000|51000",
            'title'                     => "提现记录(风控)",
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => "finance/viewWithdrawList",
            'api_path'                  => "finance/viewWithdrawList",
            'sort'                      => 10,
            'status'                    => 1,
        ]);

        // 提现记录(财务)
        DB::table('admin_menus')->insert([
            'id'                        => 51100,
            'rid'                       => "50000|51100",
            'title'                     => "提现记录(财务)",
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => "finance/withdrawPassedList",
            'api_path'                  => "finance/withdrawPassedList",
            'sort'                      => 11,
            'status'                    => 1,
        ]);

        // 人工提现表
        DB::table('admin_menus')->insert([
            'id'                        => 51200,
            'rid'                       => "50000|51200",
            'title'                     => "人工提现表",
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => "finance/viewWithdrawHandList",
            'api_path'                  => "finance/viewWithdrawHandList",
            'sort'                      => 12,
            'status'                    => 1,
        ]);

        /** ===== 报表管理 ===== */
        // 报表管理
        DB::table('admin_menus')->insert([
            'id'                        => 60000,
            'title'                     => "报表管理",
            'pid'                       => 0,
            'rid'                       => "60000",
            'type'                      => 0,
            'route'                     => 0,
            'status'                    => 1,
            'sort'                      => 7,
            'css_class'                 => 'fa fa-lg fa-fw fa-bar-chart',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 60100,
            'rid'                       => "60000|60100",
            'title'                     => "用户日结算",
            'pid'                       => 60000,
            'type'                      => 0,
            "sort"                      => 2,
            'route'                     => "report/statUserDayList",
            'api_path'                  => "report/stat-user-day-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 60101,
            'rid'                       => "60000|60100|60101",
            'title'                     => "日结对账",
            'pid'                       => 60100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "report/statUserDayCheck",
            'api_path'                  => "report/stat-user-day-check",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 60200,
            'rid'                       => "60000|60200",
            'title'                     => "用户总结算",
            'pid'                       => 60000,
            'type'                      => 0,
            "sort"                      => 3,
            'route'                     => "report/statUserList",
            'api_path'                  => "report/stat-user-list",
            'status'                    => 1,
        ]);

        // 日结专场
        DB::table('admin_menus')->insert([
            'id'                        => 60300,
            'rid'                       => "60000|60300",
            'title'                     => "彩种日结算",
            'pid'                       => 60000,
            'type'                      => 0,
            "sort"                      => 4,
            'route'                     => "report/lotteryDayList",
            'api_path'                  => "report/lottery-day-list",
            'status'                    => 1,
        ]);

        // 日工资
        DB::table('admin_menus')->insert([
            'id'                        => 60400,
            'rid'                       => "60000|60400",
            'title'                     => "代理日工资",
            'pid'                       => 60000,
            'type'                      => 0,
            "sort"                      => 5,
            'route'                     => "report/salaryList",
            'api_path'                  => "report/salary-list",
            'status'                    => 1,
        ]);

        // 日工资
        DB::table('admin_menus')->insert([
            'id'                        => 60401,
            'rid'                       => "60000|60400|60401",
            'title'                     => "人工发放",
            'pid'                       => 60400,
            'type'                      => 1,
            "sort"                      => 2,
            'route'                     => "report/salaryHand",
            'api_path'                  => "report/salary-hand",
            'status'                    => 1,
        ]);

        // 分红
        DB::table('admin_menus')->insert([
            'id'                        => 60500,
            'rid'                       => "60000|60500",
            'title'                     => "代理分红",
            'pid'                       => 60000,
            'type'                      => 0,
            "sort"                      => 6,
            'route'                     => "report/dividendList",
            'api_path'                  => "report/dividend-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 60501,
            'rid'                       => "60000|60500|60501",
            'title'                     => "人工发放",
            'pid'                       => 60500,
            'type'                      => 1,
            "sort"                      => 2,
            'route'                     => "report/dividendHand",
            'api_path'                  => "report/dividend-hand",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 60700,
            'rid'                       => "60000|60700",
            'title'                     => "商户日结算",
            'pid'                       => 60000,
            'type'                      => 0,
            "sort"                      => 1,
            'route'                     => "report/statPartnerDayList",
            'api_path'                  => "report/stat-partner-day-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 60701,
            'rid'                       => "60000|60700|60701",
            'title'                     => "日结对账",
            'pid'                       => 60700,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "report/statPartnerCheck",
            'api_path'                  => "",
            'status'                    => 1,
        ]);



        DB::table('admin_menus')->insert([
            'id'                        => 60800,
            'rid'                       => "60000|60800",
            'title'                     => "商户报表",
            'pid'                       => 60000,
            'type'                      => 0,
            "sort"                      => 1,
            'route'                     => "report/statPartnerList",
            'api_path'                  => "report/stat-partner-list",
            'status'                    => 1,
        ]);

        /** ================= 后台商户管理 ================== */

        DB::table('admin_menus')->insert([
            'id'                        => 70000,
            'title'                     => "商户管理",
            'pid'                       => 0,
            'type'                      => 0,
            'route'                     => '',
            'status'                    => 1,
            'sort'                      => 1,
            'css_class'                 => 'fa fa-lg fa-fw fa-github-alt',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70100,
            'rid'                       => "70000|70100",
            'title'                     => "商户列表",
            'pid'                       => 70000,
            'type'                      => 0,
            "sort"                      => 1,
            'route'                     => "partner/partnerList",
            'api_path'                  => "partner/partner-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70101,
            'rid'                       => "70000|70100|70101",
            'title'                     => "添加商户",
            'pid'                       => 70100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/partnerAdd",
            'api_path'                  => "partner/partner-add",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70102,
            'rid'                       => "70000|70100|70102",
            'title'                     => "添加商户",
            'pid'                       => 70100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/partnerAdd",
            'api_path'                  => "partner/partner-add",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70103,
            'rid'                       => "70000|70100|70103",
            'title'                     => "商户状态",
            'pid'                       => 70100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/partnerStatus",
            'api_path'                  => "partner/partner-status",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70104,
            'rid'                       => "70000|70100|70104",
            'title'                     => "商户详情",
            'pid'                       => 70100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/partnerDetail",
            'api_path'                  => "partner/partner-detail",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70105,
            'rid'                       => "70000|70100|70105",
            'title'                     => "设置娱乐城",
            'pid'                       => 70100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/partnerSetCasino",
            'api_path'                  => "partner/partner-set-casino",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70106,
            'rid'                       => "70000|70100|70106",
            'title'                     => "设置菜单",
            'pid'                       => 70100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/partnerSetMenu",
            'api_path'                  => "partner/partner-set-menu",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70200,
            'rid'                       => "70000|70200",
            'title'                     => "商户管理员",
            'pid'                       => 70000,
            'type'                      => 0,
            "sort"                      => 2,
            'route'                     => "partner/adminUserList",
            'api_path'                  => "partner/admin-user-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70201,
            'rid'                       => "70000|70200|70201",
            'title'                     => "添加商户管理员",
            'pid'                       => 70200,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/adminUserAdd",
            'api_path'                  => "partner/admin-user-add",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70202,
            'rid'                       => "70000|70200|70202",
            'title'                     => "商户管理员状态",
            'pid'                       => 70200,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/adminUserStatus",
            'api_path'                  => "partner/admin-user-status",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70203,
            'rid'                       => "70000|70200|70203",
            'title'                     => "商户管理员详情",
            'pid'                       => 70200,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/adminUserDetail",
            'api_path'                  => "partner/admin-user-detail",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70204,
            'rid'                       => "70000|70200|70204",
            'title'                     => "商戶修改密碼",
            'pid'                       => 70200,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/adminUserPassword",
            'api_path'                  => "partner/admin-user-password",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70300,
            'rid'                       => "70000|70300",
            'title'                     => "商户管理组",
            'pid'                       => 70000,
            'type'                      => 0,
            "sort"                      => 3,
            'route'                     => "partner/adminGroupList",
            'api_path'                  => "partner/admin-group-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70301,
            'rid'                       => "70000|70300|70301",
            'title'                     => "添加管理组",
            'pid'                       => 70300,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/adminGroupAdd",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70302,
            'rid'                       => "70000|70300|70302",
            'title'                     => "管理组状态",
            'pid'                       => 70300,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/adminGroupStatus",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70303,
            'rid'                       => "70000|70300|70303",
            'title'                     => "管理组权限",
            'pid'                       => 70300,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/adminGroupAcl",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70304,
            'rid'                       => "70000|73000|70304",
            'title'                     => "设置组权限",
            'pid'                       => 70300,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/adminGroupAclSet",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70400,
            'rid'                       => "70000|70400",
            'title'                     => "商户域名列表",
            'pid'                       => 70000,
            'type'                      => 0,
            "sort"                      => 4,
            'route'                     => "partner/domainList",
            'api_path'                  => "partner/domain-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70401,
            'rid'                       => "70000|70400|70401",
            'title'                     => "添加域名",
            'pid'                       => 70400,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/domainAdd",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70402,
            'rid'                       => "70000|70400|70402",
            'title'                     => "域名状态",
            'pid'                       => 70400,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/domainStatus",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70403,
            'rid'                       => "70000|70400|70403",
            'title'                     => "一鍵修改測試域名",
            'pid'                       => 70400,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/domainTestSet",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70600,
            'rid'                       => "70000|70600",
            'title'                     => "商户菜单列表",
            'pid'                       => 70000,
            'type'                      => 0,
            "sort"                      => 4,
            'route'                     => "partner/partnerMenuList",
            'api_path'                  => "partner/partner-menu-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70601,
            'rid'                       => "70000|70600|70601",
            'title'                     => "菜单状态",
            'pid'                       => 70600,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/partnerMenuStatus",
            'api_path'                  => "",
            'status'                    => 1,
        ]);


        DB::table('admin_menus')->insert([
            'id'                        => 70602,
            'rid'                       => "70000|70600|70602",
            'title'                     => "商户绑定预设菜单",
            'pid'                       => 70600,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/partnerBindMenuConfig",
            'api_path'                  => "partner/partner-Bind-MennConfig",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70700,
            'rid'                       => "70000|70700",
            'title'                     => "商户预设菜单",
            'pid'                       => 70000,
            'type'                      => 0,
            "sort"                      => 4,
            'route'                     => "partner/partnerMenuConfigList",
            'api_path'                  => "partner/partner-menu-config-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70701,
            'rid'                       => "70000|70700|70701",
            'title'                     => "添加预设菜单",
            'pid'                       => 70700,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/partnerMenuConfigAdd",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70702,
            'rid'                       => "70000|70700|70702",
            'title'                     => "预设菜单状态",
            'pid'                       => 70700,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "partner/partnerMenuConfigStatus",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70800,
            'rid'                       => "70000|70800",
            'title'                     => "访问日志",
            'pid'                       => 70000,
            'type'                      => 0,
            "sort"                      => 8,
            'route'                     => "partner/adminAccessLogList",
            'api_path'                  => "partner/partner-access-log",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 70900,
            'rid'                       => "70000|70900",
            'title'                     => "行为日志",
            'pid'                       => 70000,
            'type'                      => 0,
            "sort"                      => 9,
            'route'                     => "partner/adminBehavior",
            'api_path'                  => "partner/partner-admin-behavior",
            'status'                    => 1,
        ]);


		DB::table('admin_menus')->insert([
			'id'                        => 72000,
			'rid'                       => "70000|72000",
			'title'                     => "商户配置列表",
			'pid'                       => 70000,
			'type'                      => 0,
			"sort"                      => 1,
			'route'                     => "partner/partnerConfigureList",
			'api_path'                  => "partner/partner-config-list",
			'status'                    => 1,
		]);
		DB::table('admin_menus')->insert([
			'id'                    => 72001,
			'rid'                   => "70000|72000|72001",
			'title'                 => "添加商户配置",
			'pid'                   => 72000,
			'type'                  => 1,
			'route'                 => "partner/partnerConfigureAdd",
			'api_path'              => "partner/partner-configure-add",
			'status'                => 1,
			'css_class'             => 'fa fa-lg fa-fw fa-plus',
		]);

		DB::table('admin_menus')->insert([
			'id'                    => 72003,
			'rid'                   => "70000|72000|72003",
			'title'                 => "商户配置详情",
			'pid'                   => 72000,
			'type'                  => 1,
			'route'                 => "partner/partnerConfigureDetail",
			'api_path'              => "partner/partner-configure-detail",
			'status'                => 1,
		]);

		DB::table('admin_menus')->insert([
			'id'                    => 72004,
			'rid'                   => "70000|72000|72004",
			'title'                 => "商户配置状态",
			'pid'                   => 72000,
			'type'                  => 1,
			'route'                 => "partner/partnerConfigureStatus",
			'api_path'              => "partner/partner-configure-status",
			'status'                => 1,
		]);

		DB::table('admin_menus')->insert([
			'id'                    => 72005,
			'rid'                   => "70000|72000|72005",
			'title'                 => "商户刷新配置",
			'pid'                   => 72000,
			'type'                  => 1,
			'route'                 => "partner/partnerConfigureFlush",
			'status'                => 1,
			'api_path'              => "partner/partner-configure-flush",
			'css_class'             => '',
		]);



        /** ================= 后台管理账户 ================== */

        DB::table('admin_menus')->insert([
            'id'                        => 80000,
            'title'                     => "__管理员",
            'pid'                       => 0,
            'type'                      => 0,
            'route'                     => '',
            'status'                    => 1,
            'sort'                      => 1,
            'css_class'                 => 'fa fa-lg fa-fw fa-user',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80100,
            'rid'                       => "80000|80100",
            'title'                     => "__管理员",
            'pid'                       => 80000,
            'type'                      => 0,
            "sort"                      => 1,
            'route'                     => "admin/adminUserList",
            'api_path'                  => "admin/admin-user-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80101,
            'rid'                       => "80000|80100|80101",
            'title'                     => "添加管理员",
            'pid'                       => 80100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "admin/adminUserAdd",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80102,
            'rid'                       => "80000|80100|80102",
            'title'                     => "管理员详情",
            'pid'                       => 80100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "admin/adminUserDetail",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80103,
            'rid'                       => "80000|80100|80103",
            'title'                     => "管理员状态",
            'pid'                       => 80100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "admin/adminUserStatus",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80104,
            'rid'                       => "80000|80100|80104",
            'title'                     => "管理员密码",
            'pid'                       => 80100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "admin/adminUserPassword",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80200,
            'rid'                       => "80000|80200",
            'title'                     => "__管理组",
            'pid'                       => 80000,
            'type'                      => 0,
            "sort"                      => 2,
            'route'                     => "admin/adminGroupList",
            'api_path'                  => "admin/admin-group-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80201,
            'rid'                       => "80000|80200|80201",
            'title'                     => "添加管理组",
            'pid'                       => 80200,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "admin/adminGroupAdd",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80202,
            'rid'                       => "80000|80200|80202",
            'title'                     => "管理组详情",
            'pid'                       => 80200,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "admin/adminGroupDetail",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80203,
            'rid'                       => "80000|80200|80203",
            'title'                     => "删除管理组",
            'pid'                       => 80200,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "admin/adminGroupDel",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80204,
            'rid'                       => "80000|80200|80204",
            'title'                     => "管理组权限",
            'pid'                       => 80200,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "admin/adminGroupAclDetail",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80205,
            'rid'                       => "80000|80200|80205",
            'title'                     => "编辑管理组权限",
            'pid'                       => 80200,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "admin/adminGroupAclEdit",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80206,
            'rid'                       => "80000|80200|80206",
            'title'                     => "添加管理组",
            'pid'                       => 80200,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "admin/adminGroupAddChildGroup",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        /**  ============= 日志管理 ============= */
        DB::table('admin_menus')->insert([
            'id'                        => 80300,
            'rid'                       => "80000|80300",
            'title'                     => "访问日志",
            'pid'                       => 80000,
            'type'                      => 0,
            "sort"                      => 3,
            'route'                     => "admin/adminAccessLog",
            'api_path'                  => "admin/admin-log-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80400,
            'rid'                       => "80000|80400",
            'title'                     => "关键行为",
            'pid'                       => 80000,
            'type'                      => 0,
            "sort"                      => 4,
            'route'                     => "admin/adminBehavior",
            'api_path'                  => "admin/behavior-list",
            'status'                    => 1,
        ]);


        /** =========== 菜单管理 =========== */
        DB::table('admin_menus')->insert([
            'id'                        => 80500,
            'rid'                       => "80000|80500",
            'title'                     => "菜单管理",
            'pid'                       => 80000,
            'type'                      => 0,
            'route'                     => "admin/menuList",
            'api_path'                  => "admin/menu-list",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80501,
            'rid'                       => "80000|80500|80501",
            'title'                     => "添加菜单",
            'pid'                       => 80500,
            'type'                      => 1,
            'route'                     => "admin/menuAdd",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => 'fa fa-lg fa-fw fa-plus',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 80502,
            'rid'                       => "80000|80500|80502",
            'title'                     => "修改状态",
            'pid'                       => 80500,
            'type'                      => 1,
            'route'                     => "admin/menuStatus",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        /** ========================= 后台 历史数据  =============================== */

        DB::table('admin_menus')->insert([
            'id'                        => 90000,
            'title'                     => "历史数据",
            'pid'                       => 0,
            'rid'                       => "90000",
            'type'                      => 0,
            'route'                     => 0,
            'status'                    => 1,
            'sort'                      => 9,
            'css_class'                 => 'fa fa-lg fa-fw fa-folder',
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 90100,
            'rid'                       => "90000|90100",
            'title'                     => "投注历史数据",
            'pid'                       => 90000,
            'type'                      => 0,
            "sort"                      => 1,
            'route'                     => "backup/playerProject",
            'api_path'                  => "backup/player-project",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 90101,
            'rid'                       => "90000|90100|90101",
            'title'                     => "投注历史详情",
            'pid'                       => 90100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "projectHistoryDetail",
            'api_path'                  => "history/project-history-detail",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 90200,
            'rid'                       => "90000|90200",
            'title'                     => "追号历史数据",
            'pid'                       => 90000,
            'type'                      => 0,
            "sort"                      => 2,
            'route'                     => "backup/playerTrace",
            'api_path'                  => "backup/player-trace",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 90201,
            'rid'                       => "90000|90200|90201",
            'title'                     => "追号历史详情",
            'pid'                       => 90200,
            'type'                      => 1,
            "sort"                      => 2,
            'route'                     => "backup/playerTraceDes",
            'api_path'                  => "backup/player-trace-des",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 90300,
            'rid'                       => "90000|90300",
            'title'                     => "账变历史数据",
            'pid'                       => 90000,
            'type'                      => 0,
            "sort"                      => 3,
            'route'                     => "backup/funcChange",
            'api_path'                  => "backup/func-change",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 90301,
            'rid'                       => "90000|90300|90301",
            'title'                     => "账变历史详情",
            'pid'                       => 90300,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "accountChangeReportHistoryDetail",
            'api_path'                  => "history/report-history-detail",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 90400,
            'rid'                       => "90000|90400",
            'title'                     => "商户历史访问记录",
            'pid'                       => 90000,
            'type'                      => 0,
            "sort"                      => 4,
            'route'                     => "backup/partnerVisit",
            'api_path'                  => "backup/partner-visit",
            'status'                    => 1,
        ]);

         DB::table('admin_menus')->insert([
            'id'                        => 90500,
            'rid'                       => "90000|90500",
            'title'                     => "商户历史行为记录",
            'pid'                       => 90000,
            'type'                      => 0,
            "sort"                      => 5,
            'route'                     => "backup/partnerBehavior",
            'api_path'                  => "backup/partner-behavior",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 90600,
            'rid'                       => "90000|90600",
            'title'                     => "玩家历史行为记录",
            'pid'                       => 90000,
            'type'                      => 0,
            "sort"                      => 6,
            'route'                     => "backup/playerVisit",
            'api_path'                  => "backup/player-visit",
            'status'                    => 1,
        ]);

         DB::table('admin_menus')->insert([
            'id'                        => 90700,
            'rid'                       => "90000|90700",
            'title'                     => "玩家历史IP记录",
            'pid'                       => 90000,
            'type'                      => 0,
            "sort"                      => 7,
            'route'                     => "backup/playerIp",
            'api_path'                  => "backup/player-ip",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 90800,
            'rid'                       => "90000|90800",
            'title'                     => "玩家历史返点记录",
            'pid'                       => 90000,
            'type'                      => 0,
            "sort"                      => 8,
            'route'                     => "backup/playerCommission",
            'api_path'                  => "backup/player-commission",
            'status'                    => 1,
        ]);

        DB::table('admin_menus')->insert([
            'id'                        => 90900,
            'rid'                       => "90000|90900",
            'title'                     => "历史奖期记录",
            'pid'                       => 90000,
            'type'                      => 0,
            "sort"                      => 9,
            'route'                     => "backup/issuesList",
            'api_path'                  => "backup/issues-list",
            'status'                    => 1,
        ]);

    }

}
