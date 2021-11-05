<?php

use Illuminate\Database\Seeder;

// 商户菜单
class PartnerMenuTableSeeder extends Seeder
{

    public function run()
    {
        /** ======================= 菜单管理 ＠seed ========================== */

        // 系统管理
        DB::table('partner_menu_config')->insert([
            'id'                    => 10000,
            'cn_name'               => "运营管理",
            'en_name'               => "System Manage",
            'pid'                   => 0,
            'rid'                   => "10000",
            'type'                  => 0,
            'route'                 => 0,
            'status'                => 1,
            'sort'                  => 1,
            'css_class'             => 'fa fa-lg fa-fw fa-cog',
        ]);

        /** =========== 配置管理 =========== */
        DB::table('partner_menu_config')->insert([
            'id'                    => 10100,
            'rid'                   => "10000|10100",
            'cn_name'               => "配置列表",
            'en_name'               => "Configure List",
            'pid'                   => 10000,
            'type'                  => 0,
            'route'                 => "configList",
            'api_path'              => "system/configure-list",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10101,
            'rid'                   => "10000|10100|10101",
            'cn_name'               => "添加配置",
            'en_name'               => "Add Configure",
            'pid'                   => 10100,
            'type'                  => 1,
            'route'                 => "configAdd",
            'api_path'              => "system/configure-add",
            'status'                => 1,
            'css_class'             => 'fa fa-lg fa-fw fa-plus',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10102,
            'rid'                   => "10000|10100|10102",
            'cn_name'               => "配置详情",
            'en_name'               => "Configure Detail",
            'pid'                   => 10100,
            'type'                  => 1,
            'route'                 => "configDetail",
            'api_path'              => "system/configure-detail",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10103,
            'rid'                   => "10000|10100|10103",
            'cn_name'               => "配置状态",
            'en_name'               => "Configure Status",
            'pid'                   => 10100,
            'type'                  => 1,
            'route'                 => "configStatus",
            'api_path'              => "system/configure-status",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10104,
            'rid'                   => "10000|10100|10104",
            'cn_name'               => "刷新配置",
            'en_name'               => "Flush Configure",
            'pid'                   => 10100,
            'type'                  => 1,
            'route'                 => "configFlush",
            'status'                => 1,
            'api_path'              => "",
            'css_class'             => '',
        ]);

        /**  ============= 公告管理 ============= */
        DB::table('partner_menu_config')->insert([
            'id'                    => 10200,
            'rid'                   => "10000|10200",
            'cn_name'               => "公告管理",
            'en_name'               => "Notice Manage",
            'pid'                   => 10000,
            'type'                  => 0,
            'sort'                  => 2,
            'route'                 => "system/noticeList",
            'api_path'              => "system/notice-list",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10201,
            'rid'                   => "10000|10200|10201",
            'cn_name'               => "刷新缓存",
            'en_name'               => "Flush Notice",
            'pid'                   => 10200,
            'type'                  => 1,
            'route'                 => "system/noticeFlush",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10202,
            'rid'                   => "10000|10200|10202",
            'cn_name'               => "添加公告",
            'en_name'               => "Add Notice",
            'pid'                   => 10200,
            'type'                  => 1,
            'route'                 => "system/noticeAdd",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10203,
            'rid'                   => "10000|10200|10203",
            'cn_name'               => "修改公告状态",
            'en_name'               => "Change Notice Status",
            'pid'                   => 10200,
            'type'                  => 1,
            'route'                 => "system/noticeStatus",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10204,
            'rid'                   => "10000|10200|10204",
            'cn_name'               => "置顶公告",
            'en_name'               => "Top Notice",
            'pid'                   => 10200,
            'type'                  => 1,
            'route'                 => "system/noticeTop",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10205,
            'rid'                   => "10000|10200|10205",
            'cn_name'               => "上传图片",
            'en_name'               => "Upload Image",
            'pid'                   => 10200,
            'type'                  => 1,
            'route'                 => "system/noticeUploadImg",
            'api_path'              => "",
            'status'                => 1,
        ]);

        /**  ============= 首页配置 ============= */
        DB::table('partner_menu_config')->insert([
            'id'                    => 10300,
            'rid'                   => "10000|10300",
            'cn_name'               => "平台配置",
            'en_name'               => "Homepage Manage",
            'pid'                   => 10000,
            'sort'                  => 4,
            'type'                  => 0,
            'route'                 => "system/moduleList",
            'api_path'              => "system/module-list",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10301,
            'rid'                   => "10000|10300|10301",
            'cn_name'               => "模块添加",
            'en_name'               => "Module Add",
            'pid'                   => 10300,
            'type'                  => 1,
            'route'                 => "system/moduleAdd",
            'api_path'              => "system/module-add",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10302,
            'rid'                   => "10000|10300|10301",
            'cn_name'               => "模块设置",
            'en_name'               => "Module Set",
            'pid'                   => 10300,
            'type'                  => 1,
            'route'                 => "system/moduleSet",
            'api_path'              => "system/module-set",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10303,
            'rid'                   => "10000|10300|10303",
            'cn_name'               => "Telegram列表",
            'en_name'               => "Telegram List",
            'pid'                   => 10300,
            'type'                  => 1,
            'route'                 => "system/partnerTelegramChannelList",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10304,
            'rid'                   => "10000|10300|10304",
            'cn_name'               => "Telegram编辑",
            'en_name'               => "Telegram Edit",
            'pid'                   => 10300,
            'type'                  => 1,
            'route'                 => "system/partnerTelegramChannelEdit",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10305,
            'rid'                   => "10000|10300|10305",
            'cn_name'               => "Telegram生成ID",
            'en_name'               => "Telegram Gen Id",
            'pid'                   => 10300,
            'type'                  => 1,
            'route'                 => "system/partnerTelegramChannelGenId",
            'api_path'              => "",
            'status'                => 1,
        ]);

        /**  ============= 审核管理 ============= */
        DB::table('partner_menu_config')->insert([
            'id'                    => 10400,
            'rid'                   => "10000|10400",
            'cn_name'               => "审核管理",
            'en_name'               => "Review Manage",
            'pid'                   => 10000,
            'sort'                  => 5,
            'type'                  => 0,
            'route'                 => "system/reviewList",
            'api_path'              => "system/review-list",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10401,
            'rid'                   => "10000|10400|10401",
            'cn_name'               => "审核领单",
            'en_name'               => "Review Fetch",
            'pid'                   => 10400,
            'type'                  => 1,
            'route'                 => "system/reviewFetch",
            'api_path'              => "system/review-fetch",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 10402,
            'rid'                   => "10000|10400|10402",
            'cn_name'               => "处理审核",
            'en_name'               => "Review Process",
            'pid'                   => 10400,
            'type'                  => 1,
            'route'                 => "system/reviewProcess",
            'api_path'              => "system/review-process",
            'status'                => 1,
        ]);

        /**  ============= 管理管理 ============= */
        DB::table('partner_menu_config')->insert([
            'id'                        => 10500,
            'rid'                       => "10000|10500",
            'cn_name'                   => "管理员",
            'en_name'                   => "Admin List",
            'pid'                       => 10000,
            'type'                      => 0,
            'sort'                      => 6,
            'route'                     => "admin/adminUserList",
            'api_path'                  => "admin/admin-user-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10501,
            'rid'                       => "10000|10500|10501",
            'cn_name'                   => "添加管理员",
            'en_name'                   => "Admin Add",
            'pid'                       => 10500,
            'type'                      => 1,
            'route'                     => "admin/adminUserAdd",
            'api_path'                  => "admin/admin-user-add",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10502,
            'rid'                       => "10000|10500|10502",
            'cn_name'                   => "管理员状态",
            'en_name'                   => "Admin Status",
            'pid'                       => 10500,
            'type'                      => 1,
            'route'                     => "admin/adminUserStatus",
            'api_path'                  => "admin/admin-user-status",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10503,
            'rid'                       => "10000|10500|10503",
            'cn_name'                   => "管理员详情",
            'en_name'                   => "Admin Detail",
            'pid'                       => 10500,
            'type'                      => 1,
            'route'                     => "admin/adminUserDetail",
            'api_path'                  => "admin/admin-user-detail",
            'status'                    => 1,
        ]);

        /**  ============= 管理组管理 ============= */
        DB::table('partner_menu_config')->insert([
            'id'                        => 10600,
            'rid'                       => "10000|10600",
            'cn_name'                   => "管理组",
            'en_name'                   => "Admin Group List",
            'pid'                       => 10000,
            'type'                      => 0,
            'sort'                      => 7,
            'route'                     => "admin/adminGroupList",
            'api_path'                  => "admin/admin-group-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10601,
            'rid'                       => "10000|10600|10601",
            'cn_name'                   => "添加管理组",
            'en_name'                   => "Admin Group Add",
            'pid'                       => 10600,
            'type'                      => 1,
            'route'                     => "admin/adminGroupAdd",
            'api_path'                  => "admin/admin-group-add",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10602,
            'rid'                       => "10000|10600|10602",
            'cn_name'                   => "管理组状态",
            'en_name'                   => "Admin Group Status",
            'pid'                       => 10600,
            'type'                      => 1,
            'route'                     => "admin/adminGroupStatus",
            'api_path'                  => "admin/admin-group-status",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10603,
            'rid'                       => "10000|10600|10603",
            'cn_name'                   => "管理员详情",
            'en_name'                   => "Admin Group Detail",
            'pid'                       => 10600,
            'type'                      => 1,
            'route'                     => "admin/adminGroupDetail",
            'api_path'                  => "admin/admin-group-detail",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10604,
            'rid'                       => "10000|10600|10604",
            'cn_name'                   => "设置管理组权限",
            'en_name'                   => "Admin Group Set Acl",
            'pid'                       => 10600,
            'type'                      => 1,
            'route'                     => "admin/adminGroupSetAcl",
            'api_path'                  => "admin/admin-group-set-acl",
            'status'                    => 1,
        ]);

        /**  ============= 菜单管理 ============= */
        DB::table('partner_menu_config')->insert([
            'id'                        => 10700,
            'rid'                       => "10000|10700",
            'cn_name'                   => "菜单列表",
            'en_name'                   => "Menu List",
            'pid'                       => 10000,
            'type'                      => 0,
            'sort'                      => 8,
            'route'                     => "admin/menuList",
            'api_path'                  => "admin/menu-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10701,
            'rid'                       => "10000|10700|10701",
            'cn_name'                   => "添加菜单",
            'en_name'                   => "Menu Add",
            'pid'                       => 10700,
            'type'                      => 1,
            'route'                     => "admin/menuAdd",
            'api_path'                  => "admin/menu-add",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10702,
            'rid'                       => "10000|10700|10702",
            'cn_name'                   => "菜单状态",
            'en_name'                   => "Menu Status",
            'pid'                       => 10700,
            'type'                      => 1,
            'route'                     => "admin/menuStatus",
            'api_path'                  => "admin/menu-status",
            'status'                    => 1,
        ]);

        /**  ============= 站内信 ============= */
        DB::table('partner_menu_config')->insert([
            'id'                        => 10800,
            'rid'                       => "10000|10800",
            'cn_name'                   => "站内信列表",
            'en_name'                   => "Message List",
            'pid'                       => 10000,
            'type'                      => 0,
            'sort'                      => 3,
            'route'                     => "system/getList",
            'api_path'                  => "system/get-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10801,
            'rid'                       => "10000|10800|10801",
            'cn_name'                   => "站内信添加",
            'en_name'                   => "Message Add",
            'pid'                       => 10800,
            'type'                      => 1,
            'route'                     => "system/contentDel",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10802,
            'rid'                       => "10000|10800|10802",
            'cn_name'                   => "站内信",
            'en_name'                   => "Message Status",
            'pid'                       => 10800,
            'type'                      => 1,
            'route'                     => "system/addMessageContent",
            'api_path'                  => "",
            'status'                    => 1,
        ]);


        DB::table('partner_menu_config')->insert([
            'id'                        => 10900,
            'rid'                       => "10000|10900",
            'cn_name'                   => "审核权限列表",
            'en_name'                   => "Check User List",
            'pid'                       => 10000,
            'type'                      => 0,
            'sort'                      => 3,
            'route'                     => "admin/checkUserList",
            'api_path'                  => "admin/check-user-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10901,
            'rid'                       => "10000|10900|10901",
            'cn_name'                   => "删除审核权限",
            'en_name'                   => "Check Del List",
            'pid'                       => 10900,
            'type'                      => 1,
            'route'                     => "admin/checkDelUserList",
            'api_path'                  => "admin/check-del-user-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10902,
            'rid'                       => "10000|10900|10902",
            'cn_name'                   => "添加审核权限",
            'en_name'                   => "Check Save List",
            'pid'                       => 10900,
            'type'                      => 1,
            'route'                     => "admin/checkSaveUserList",
            'api_path'                  => "admin/check-save-user-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 10903,
            'rid'                       => "10000|10900|10903",
            'cn_name'                   => "设置审核",
            'en_name'                   => "Template selected",
            'pid'                       => 10900,
            'type'                      => 1,
            'route'                     => "setTemplate",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        /** ======================== 游戏 ========================= */
        DB::table('partner_menu_config')->insert([
            'id'                    => 20000,
            'cn_name'               => "彩票游戏",
            'en_name'               => "Lottery Game",
            'rid'                   => "20000",
            'pid'                   => 0,
            'type'                  => 0,
            'route'                 => 0,
            'status'                => 1,
            'sort'                  => 2,
            'css_class'             => 'fa fa-lg fa-fw fa-gamepad',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20100,
            'rid'                   => "20000|20100",
            'cn_name'               => "彩票列表",
            'en_name'               => "Lottery List",
            'pid'                   => 20000,
            'type'                  => 0,
            'sort'                  => 1,
            'route'                 => "lottery/lotteryList",
            'api_path'              => "lottery/lottery-list",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20101,
            'rid'                   => "20000|20100|20101",
            'cn_name'               => "彩票状态",
            'en_name'               => "Lottery Status",
            'pid'                   => 20100,
            'type'                  => 1,
            'sort'                  => 1,
            'route'                 => "lottery/lotteryStatus",
            'api_path'              => "lottery/lottery-status",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20103,
            'rid'                   => "20000|20100|20103",
            'cn_name'               => "刷新缓存",
            'en_name'               => "Lottery Flush",
            'pid'                   => 20100,
            'type'                  => 1,
            'route'                 => "lottery/lotteryCacheFlush",
            'api_path'              => "lottery/lottery-flush",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20104,
            'rid'                   => "20000|20100|20104",
            'cn_name'               => "设置水率",
            'en_name'               => "Lottery Rate Set",
            'pid'                   => 20100,
            'type'                  => 1,
            'route'                 => "lottery/lotterySetRate",
            'api_path'              => "",
            'status'                => 1,
            'css_class'             => '',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20200,
            'rid'                   => "20000|20200",
            'cn_name'               => "玩法列表",
            'en_name'               => "Method List",
            'pid'                   => 20000,
            'type'                  => 0,
            "sort"                  => 2,
            'route'                 => "lottery/methodList",
            'api_path'              => "lottery/method-list",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20201,
            'rid'                   => "20000|20200|20201",
            'cn_name'               => "玩法状态",
            'en_name'               => "Method Status",
            'pid'                   => 20200,
            'type'                  => 1,
            'sort'                  => 1,
            'route'                 => "lottery/methodStatus",
            'api_path'              => "lottery/method-status",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20203,
            'rid'                   => "20000|20200|20203",
            'cn_name'               => "玩法排序",
            'en_name'               => "method Sort",
            'pid'                   => 20200,
            'type'                  => 1,
            'route'                 => "lottery/methodSort",
            'api_path'              => "lottery/method-sort",
            'status'                => 1,
            'css_class'             => '',
        ]);

        // 订单
        DB::table('partner_menu_config')->insert([
            'id'                    => 20300,
            'rid'                   => "20000|20300",
            'cn_name'               => "注单列表",
            'en_name'               => "Order List",
            'pid'                   => 20000,
            'type'                  => 0,
            'sort'                  => 3,
            'route'                 => "lottery/projectList",
            'api_path'              => "lottery/project-list",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20301,
            'rid'                   => "20000|20300|20301",
            'cn_name'               => "注单详情",
            'en_name'               => "Order Detail",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/projectDetail",
            'api_path'              => "lottery/project-detail",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20303,
            'rid'                   => "20000|20300|20303",
            'cn_name'               => "撤单",
            'en_name'               => "Order Cancel",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/projectCancel",
            'api_path'              => "lottery/project-cancel",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20304,
            'rid'                   => "20000|20300|20304",
            'cn_name'               => "注单返点",
            'en_name'               => "Project Commission",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/projectCommission",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20305,
            'rid'                   => "20000|20300|20305",
            'cn_name'               => "注单返点",
            'en_name'               => "Project Account Change",
            'pid'                   => 20300,
            'type'                  => 1,
            'route'                 => "lottery/projectAccountChange",
            'api_path'              => "",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20400,
            'rid'                   => "20000|20400",
            'cn_name'               => "追号列表",
            'en_name'               => "Trace List",
            'pid'                   => 20000,
            'type'                  => 0,
            'sort'                  => 1,
            'route'                 => "lottery/traceList",
            'api_path'              => "lottery/trace-list",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20401,
            'rid'                   => "20000|20400|20401",
            'cn_name'               => "追号详情",
            'en_name'               => "Trace Detail",
            'pid'                   => 20400,
            'type'                  => 1,
            'route'                 => "lottery/traceDetail",
            'api_path'              => "lottery/trace-detail",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20402,
            'rid'                   => "20000|20400|20402",
            'cn_name'               => "追号撤单",
            'en_name'               => "Trace Cancel",
            'pid'                   => 20400,
            'type'                  => 1,
            'route'                 => "lottery/traceCancel",
            'api_path'              => "lottery/trace-cancel",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20500,
            'rid'                   => "20000|20500",
            'cn_name'               => "奖期列表",
            'en_name'               => "Issue List",
            'pid'                   => 20000,
            'type'                  => 0,
            "sort"                  => 5,
            'route'                 => "lottery/issueList",
            'api_path'              => "lottery/issue-list",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                    => 20501,
            'rid'                   => "20000|20500|20501",
            'cn_name'               => "奖期详情",
            'en_name'               => "Issue Detail",
            'pid'                   => 20500,
            'type'                  => 1,
            "sort"                  => 5,
            'route'                 => "lottery/issueDetail",
            'api_path'              => "lottery/issue-detail",
            'status'                => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 20502,
            'rid'                       => "20000|20500|20502",
            'cn_name'                   => "录号",
            'en_name'                   => "Issue Encode",
            'pid'                       => 20500,
            'type'                      => 1,
            "sort"                      => 5,
            'route'                     => "lottery/issueEncode",
            'api_path'                  => "lottery/issue-encode",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 20503,
            'rid'                       => "20000|20500|20503",
            'cn_name'                   => "撤单",
            'en_name'                   => "Issue Cancel",
            'pid'                       => 20500,
            'type'                      => 1,
            "sort"                      => 5,
            'route'                     => "lottery/issueCancel",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 20700,
            'rid'                       => "20000|20700",
            'cn_name'                   => "投注返点",
            'en_name'                   => "Project Commission",
            'pid'                       => 20000,
            'type'                      => 0,
            "sort"                      => 7,
            'route'                     => "lottery/commission",
            'api_path'                  => "lottery/commission-list",
            'status'                    => 1,
        ]);

        /** ======================== 后台管理 ========================= */
        DB::table('partner_menu_config')->insert([
            'id'                        => 30000,
            'cn_name'                   => "日志管理",
            'en_name'                   => "Log Manage",
            'rid'                       => "30000",
            'pid'                       => 0,
            'type'                      => 0,
            'route'                     => 0,
            'status'                    => 1,
            'sort'                      => 3,
            'css_class'                 => 'fa fa-lg fa-fw fa-folder-open',
        ]);

        // 访问日志
        DB::table('partner_menu_config')->insert([
            'id'                        => 30100,
            'rid'                       => "30000|30100",
            'cn_name'                   => "管理-访问日志",
            'en_name'                   => "Admin Log List",
            'pid'                       => 30000,
            'type'                      => 0,
            'route'                     => "admin/accessLog",
            'api_path'                  => "admin/admin-access-log",
            'status'                    => 1,
        ]);

        // 管理员行为
        DB::table('partner_menu_config')->insert([
            'id'                        => 30200,
            'rid'                       => "30000|30200",
            'cn_name'                   => "管理-行为日志",
            'en_name'                   => "Admin Behavior",
            'pid'                       => 30000,
            'type'                      => 0,
            'route'                     => "admin/behaviorLog",
            'api_path'                  => "admin/admin-behavior-log",
            'status'                    => 1,
        ]);

        // 玩家日志
        DB::table('partner_menu_config')->insert([
            'id'                        => 30300,
            'rid'                       => "30000|30300",
            'cn_name'                   => "玩家-访问日志",
            'en_name'                   => "Player Access Log",
            'pid'                       => 30000,
            'type'                      => 0,
            'route'                     => "admin/playerAccessLog",
            'api_path'                  => "admin/player-access-log",
            'status'                    => 1,
        ]);

        // IP日志
        DB::table('partner_menu_config')->insert([
            'id'                        => 30400,
            'rid'                       => "30000|30400",
            'cn_name'                   => "玩家-IP日志",
            'en_name'                   => "Player IP Log",
            'pid'                       => 30000,
            'type'                      => 0,
            'route'                     => "admin/playerIpLog",
            'api_path'                  => "admin/player-ip-log",
            'status'                    => 1,
        ]);


        /** ======================== 真人管理 ========================= */
        DB::table('partner_menu_config')->insert([
            'id'                        => 40000,
            'cn_name'                   => "娱乐城",
            'en_name'                   => "Casino Manage",
            'rid'                       => "40000",
            'pid'                       => 0,
            'type'                      => 0,
            'route'                     => 0,
            'status'                    => 1,
            'sort'                      => 4,
            'css_class'                 => 'fa fa-lg fa-fw fa-optin-monster',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 40100,
            'rid'                       => "40000|40100",
            'cn_name'                   => "平台列表",
            'en_name'                   => "Platform List",
            'pid'                       => 40000,
            'type'                      => 0,
            'route'                     => "casino/platformList",
            'api_path'                  => "casino/casino-platform-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 40101,
            'rid'                       => "40000|40100|40101",
            'cn_name'                   => "修改平台状态",
            'en_name'                   => "Platform Status",
            'pid'                       => 40100,
            'type'                      => 1,
            'route'                     => "casino/platformStatus",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 40200,
            'rid'                       => "40000|40200",
            'cn_name'                   => "分类列表",
            'en_name'                   => "Category List",
            'pid'                       => 40000,
            'type'                      => 0,
            'route'                     => "casino/categoryList",
            'api_path'                  => "casino/casino-category-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 40201,
            'rid'                       => "40000|40200|40201",
            'cn_name'                   => "修改分类状态",
            'en_name'                   => "Category Status",
            'pid'                       => 40200,
            'type'                      => 1,
            'route'                     => "casino/categoryStatus",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 40300,
            'rid'                       => "40000|40300",
            'cn_name'                   => "玩法列表",
            'en_name'                   => "Method List",
            'pid'                       => 40000,
            'type'                      => 0,
            'route'                     => "casino/methodList",
            'api_path'                  => "casino/casino-method-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 40301,
            'rid'                       => "40000|40300|40301",
            'cn_name'                   => "修改玩法状态",
            'en_name'                   => "Method Status",
            'pid'                       => 40300,
            'type'                      => 1,
            'route'                     => "casino/methodStatus",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 40400,
            'rid'                       => "40000|40400",
            'cn_name'                   => "转账列表",
            'en_name'                   => "Transfer List",
            'pid'                       => 40000,
            'type'                      => 0,
            'sort'                      => 4,
            'route'                     => "casino/transferLogList",
            'api_path'                  => "casino/casino-transfer-log-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 40500,
            'rid'                       => "40000|40500",
            'cn_name'                   => "接口日志",
            'en_name'                   => "Api Log List",
            'pid'                       => 40000,
            'type'                      => 0,
            'sort'                      => 5,
            'route'                     => "casino/apiLogList",
            'api_path'                  => "casino/casino-api-log-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 40600,
            'rid'                       => "40000|40600",
            'cn_name'                   => "注单列表",
            'en_name'                   => "Bet List",
            'pid'                       => 40000,
            'type'                      => 0,
            'sort'                      => 5,
            'route'                     => "casino/getBetLog",
            'api_path'                  => "casino/casino-bet-list",
            'status'                    => 1,
        ]);

        /** ======================== 玩家管理 ========================= */
        DB::table('partner_menu_config')->insert([
            'id'                        => 60000,
            'cn_name'                   => "玩家管理",
            'en_name'                   => "Player Manage",
            'rid'                       => "60000",
            'pid'                       => 0,
            'type'                      => 0,
            'route'                     => 0,
            'status'                    => 1,
            'sort'                      => 6,
            'css_class'                 => 'fa fa-lg fa-fw fa-users',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60100,
            'rid'                       => "60000|60100",
            'cn_name'                   => "玩家列表",
            'en_name'                   => "Player List",
            'pid'                       => 60000,
            'type'                      => 0,
            'sort'                      => 1,
            'route'                     => "player/playerList",
            'api_path'                  => "player/player-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60101,
            'rid'                       => "60000|60100|60101",
            'cn_name'                   => "添加玩家",
            'en_name'                   => "Player List",
            'pid'                       => 60100,
            'type'                      => 1,
            'route'                     => "player/playerAddTop",
            'api_path'                  => "player/player-add",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60102,
            'rid'                       => "60000|60100|60102",
            'cn_name'                   => "玩家转帐",
            'en_name'                   => "Player Transfer",
            'pid'                       => 3100,
            'type'                      => 1,
            'route'                     => "player/playerTransfer",
            'api_path'                  => "player/player-transfer",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60103,
            'rid'                       => "60000|60100|60103",
            'cn_name'                   => "玩家冻结",
            'en_name'                   => "Player Frozen",
            'pid'                       => 60100,
            'type'                      => 1,
            'route'                     => "player/playerFrozen",
            'api_path'                  => "player/player-frozen",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60104,
            'rid'                       => "60000|60100|60104",
            'cn_name'                   => "玩家设置",
            'en_name'                   => "Player Setting",
            'pid'                       => 60100,
            'type'                      => 1,
            'route'                     => "playerSetting",
            'api_path'                  => "player/player-setting",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60105,
            'rid'                       => "60000|60100|60105",
            'cn_name'                   => "玩家密码",
            'en_name'                   => "Player Password",
            'pid'                       => 60100,
            'type'                      => 1,
            'route'                     => "player/playerPassword",
            'api_path'                  => "player/player-password",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60106,
            'rid'                       => "60000|60100|60106",
            'cn_name'                   => "玩家详情",
            'en_name'                   => "Player Detail",
            'pid'                       => 60100,
            'type'                      => 1,
            'route'                     => "player/playerDetail",
            'api_path'                  => "player/player-detail",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60200,
            'rid'                       => "60000|60200",
            'cn_name'                   => "玩家等级",
            'en_name'                   => "Player Level",
            'pid'                       => 60000,
            'type'                      => 0,
            'sort'                      => 2,
            'route'                     => "player/playerVipConfig",
            'api_path'                  => "player/playerVipConfig",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60201,
            'rid'                       => "60000|60200|60201",
            'cn_name'                   => "添加会员等级",
            'en_name'                   => "Add Player Level",
            'pid'                       => 60200,
            'type'                      => 1,
            'sort'                      => 1,
            'route'                     => "player/addPlayerVipConfig",
            'api_path'                  => "player/addPlayerVipConfig",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60202,
            'rid'                       => "60000|60200|60202",
            'cn_name'                   => "查看会员等级",
            'en_name'                   => "Player Level Detail",
            'pid'                       => 60200,
            'type'                      => 1,
            'sort'                      => 2,
            'route'                     => "player/playerVipConfigDetail",
            'api_path'                  => "player/playerVipConfigDetail",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60300,
            'rid'                       => "60000|60300",
            'cn_name'                   => "__银行卡",
            'en_name'                   => "Bank Card",
            'pid'                       => 60000,
            'type'                      => 0,
            'sort'                      => 3,
            'route'                     => "player/playerCardList",
            'api_path'                  => "player/player-card-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60301,
            'rid'                       => "60000|60300|60301",
            'cn_name'                   => "添加银行卡",
            'en_name'                   => "Add Card",
            'pid'                       => 60300,
            'type'                      => 1,
            'route'                     => "player/playerCardAdd",
            'api_path'                  => "player/player-card-add",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60302,
            'rid'                       => "60000|60300|60302",
            'cn_name'                   => "银行卡状态",
            'en_name'                   => "Card Status",
            'pid'                       => 60300,
            'type'                      => 1,
            'route'                     => "player/playerCardStatus",
            'api_path'                  => "player/player-card-status",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60303,
            'rid'                       => "60000|60300|60303",
            'cn_name'                   => "删除银行卡",
            'en_name'                   => "Card Del",
            'pid'                       => 60300,
            'type'                      => 1,
            'route'                     => "player/playerCardDel",
            'api_path'                  => "player/player-card-del",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60400,
            'rid'                       => "60000|60400",
            'cn_name'                   => "工资记录",
            'en_name'                   => "Salary Records",
            'pid'                       => 60000,
            'type'                      => 0,
            'sort'                      => 4,
            'route'                     => "player/reportSalaryList",
            'api_path'                  => "player/salary-report-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60401,
            'rid'                       => "60000|60400|60401",
            'cn_name'                   => "人工发放",
            'en_name'                   => "Salary Send",
            'pid'                       => 60400,
            'type'                      => 1,
            'route'                     => "player/reportSalarySend",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);


        DB::table('partner_menu_config')->insert([
            'id'                        => 60500,
            'rid'                       => "60000|60500",
            'cn_name'                   => "分红记录",
            'en_name'                   => "Dividend Records",
            'pid'                       => 60000,
            'type'                      => 0,
            'sort'                      => 5,
            'route'                     => "player/dividendSalaryList",
            'api_path'                  => "player/dividend-report-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60501,
            'rid'                       => "60000|60500|60501",
            'cn_name'                   => "人工分红",
            'en_name'                   => "Dividend Send",
            'pid'                       => 60500,
            'type'                      => 1,
            'route'                     => "player/dividendSalaryAdd",
            'api_path'                  => "",
            'status'                    => 1,
            'css_class'                 => '',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 60600,
            'rid'                       => "60000|60600",
            'cn_name'                   => "玩家头像",
            'en_name'                   => "Player Avatar",
            'pid'                       => 60000,
            'type'                      => 0,
            'sort'                      => 6,
            'route'                     => "system/playerAvatarList",
            'api_path'                  => "system/player-avatar-list",
            'status'                    => 1,
        ]);

        /** ======================== 财务管理 ========================= */
        DB::table('partner_menu_config')->insert([
            'id'                        => 70000,
            'cn_name'                   => "财务管理",
            'en_name'                   => "Finance Manage",
            'rid'                       => "70000",
            'pid'                       => 0,
            'type'                      => 0,
            'route'                     => 0,
            'status'                    => 1,
            'sort'                      => 7,
            'css_class'                 => 'fa fa-lg fa-fw fa-credit-card',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 70100,
            'cn_name'                   => "充值列表",
            'en_name'                   => "Recharge List",
            'rid'                       => "70000|70100",
            'pid'                       => 70000,
            'type'                      => 0,
            'route'                     => "finance/rechargeList",
            'api_path'                  => "finance/recharge-list",
            'status'                    => 1,
            'sort'                      => 1,
            'css_class'                 => 'fa fa-lg fa-fw fa-gamepad',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 70101,
            'cn_name'                   => "充值人工处理",
            'en_name'                   => "Recharge Hand",
            'rid'                       => "70000|70100|70101",
            'pid'                       => 70000,
            'type'                      => 1,
            'route'                     => "finance/rechargeHand",
            'api_path'                  => "",
            'status'                    => 1,
            'sort'                      => 2,
            'css_class'                 => '',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 70102,
            'cn_name'                   => "充值记录日志",
            'en_name'                   => "Recharge Log",
            'rid'                       => "70000|70100|70102",
            'pid'                       => 70000,
            'type'                      => 1,
            'route'                     => "finance/rechargeLog",
            'api_path'                  => "",
            'status'                    => 1,
            'sort'                      => 2,
            'css_class'                 => '',
        ]);

        // 支付账户
        DB::table('partner_menu_config')->insert([
            'id'                        => 70200,
            'cn_name'                   => '支付账户',
            'en_name'                   => 'platformAccount',
            'rid'                       => '70000|70200',
            'pid'                       => 70000,
            'type'                      => 0,
            'route'                     => 'finance/platformAccount',
            'api_path'                  => 'finance/platformAccount',
            'status'                    => 1,
            'sort'                      => 2,
            'css_class'                 => '',
        ]);

        // 支付账户-开放渠道
        DB::table('partner_menu_config')->insert([
            'id'                        => 70300,
            'cn_name'                   => '支付账户-开放渠道',
            'en_name'                   => 'platformAccountChannel',
            'rid'                       => "70000|70300",
            'pid'                       => 70000,
            'type'                      => 0,
            'route'                     => 'finance/platformAccountChannel',
            'api_path'                  => 'finance/platformAccountChannel',
            'status'                    => 1,
            'sort'                      => 2,
            'css_class'                 => '',
        ]);

        // 提现记录(风控)
        DB::table('partner_menu_config')->insert([
            'id'                        => 70400,
            'cn_name'                   => '提现记录(风控)',
            'en_name'                   => 'viewWithdrawList',
            'rid'                       => "70000|70400",
            'pid'                       => 70000,
            'type'                      => 0,
            'route'                     => 'finance/viewWithdrawList',
            'api_path'                  => 'finance/viewWithdrawList',
            'status'                    => 1,
            'sort'                      => 2,
            'css_class'                 => '',
        ]);

        // 提现记录(财务)
        DB::table('partner_menu_config')->insert([
            'id'                        => 70500,
            'cn_name'                   => '提现记录(财务)',
            'en_name'                   => 'withdrawPassedList',
            'rid'                       => "70000|70500",
            'pid'                       => 70000,
            'type'                      => 0,
            'route'                     => 'finance/withdrawPassedList',
            'api_path'                  => 'finance/withdrawPassedList',
            'status'                    => 1,
            'sort'                      => 2,
            'css_class'                 => '',
        ]);

        // 人工提现表
        DB::table('partner_menu_config')->insert([
            'id'                        => 70600,
            'cn_name'                   => '人工提现表',
            'en_name'                   => 'viewWithdrawHandList',
            'rid'                       => "70000|70600",
            'pid'                       => 70000,
            'type'                      => 0,
            'route'                     => 'finance/viewWithdrawHandList',
            'api_path'                  => 'finance/viewWithdrawHandList',
            'status'                    => 1,
            'sort'                      => 2,
            'css_class'                 => '',
        ]);

        /** ========================== 报表管理 ======================= */
        // 报表管理
        DB::table('partner_menu_config')->insert([
            'id'                        => 80000,
            'cn_name'                   => "报表管理",
            'en_name'                   => "Report",
            'pid'                       => 0,
            'rid'                       => "80000",
            'type'                      => 0,
            'route'                     => 0,
            'status'                    => 1,
            'sort'                      => 8,
            'css_class'                 => 'fa fa-lg fa-fw fa-bar-chart',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 80100,
            'rid'                       => "80000|80100",
            'cn_name'                   => "用户日结算",
            'en_name'                   => "Report Day",
            'pid'                       => 80000,
            'type'                      => 0,
            "sort"                      => 2,
            'route'                     => "report/statUserDayList",
            'api_path'                  => "report/stat-user-day-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 80101,
            'rid'                       => "80000|80100|80101",
            'cn_name'                   => "日结对账",
            'en_name'                   => "Report Day Check",
            'pid'                       => 80100,
            'type'                      => 1,
            "sort"                      => 1,
            'route'                     => "report/statUserDayCheck",
            'api_path'                  => "report/stat-user-day-check",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 80200,
            'rid'                       => "80000|80200",
            'cn_name'                   => "用户总结算",
            'en_name'                   => "Report User",
            'pid'                       => 80000,
            'type'                      => 0,
            "sort"                      => 3,
            'route'                     => "report/statUserList",
            'api_path'                  => "report/stat-user-list",
            'status'                    => 1,
        ]);

        // 日结专场
        DB::table('partner_menu_config')->insert([
            'id'                        => 80300,
            'rid'                       => "80000|80300",
            'cn_name'                   => "彩种日结算",
            'en_name'                   => "Lottery Day",
            'pid'                       => 80000,
            'type'                      => 0,
            "sort"                      => 4,
            'route'                     => "report/lotteryDayList",
            'api_path'                  => "report/lottery-day-list",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 80600,
            'rid'                       => "80000|80600",
            'cn_name'                   => "账变列表",
            'en_name'                   => "Account Change Report",
            'pid'                       => 80000,
            'type'                      => 0,
            "sort"                      => 0,
            'route'                     => "report/accountChangeReportList",
            'api_path'                  => "report/account-change-report",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 80601,
            'rid'                       => "80000|80600|80601",
            'cn_name'                   => "账变详情",
            'en_name'                   => "Account Change Detail",
            'pid'                       => 80600,
            'type'                      => 1,
            "sort"                      => 0,
            'route'                     => "report/accountChangeReportDetail",
            'api_path'                  => "",
            'status'                    => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 80700,
            'rid'                       => "80000|80700",
            'cn_name'                   => "盈亏报表",
            'en_name'                   => "Profit and Loss Report",
            'pid'                       => 80000,
            'type'                      => 0,
            "sort"                      => 0,
            'route'                     => "report/profitAndLossReportList",
            'api_path'                  => "report/profit-and-loss-report",
            'status'                    => 1,
        ]);

        /** ======================== 活动管理 ========================= */
        DB::table('partner_menu_config')->insert([
            'id'                        => 90000,
            'cn_name'                   => "活动管理",
            'en_name'                   => "Activity Manage",
            'rid'                       => "90000",
            'pid'                       => 0,
            'type'                      => 0,
            'route'                     => 0,
            'status'                    => 1,
            'sort'                      => 9,
            'css_class'                 => 'fa fa-lg fa-fw fa-gift',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 90100,
            'cn_name'                   => "活动信息",
            'en_name'                   => "Activity Info",
            'rid'                       => "90000|90100",
            'pid'                       => 90000,
            'type'                      => 0,
            'route'                     => "activity/activityInfo",
            'api_path'                  => "activity/activityInfo",
            'status'                    => 1,
            'sort'                      => 3,
            'css_class'                 => 'fa fa-lg fa-fw fa-gamepad',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 90101,
            'cn_name'                   => "添加活动",
            'en_name'                   => "Activity Add",
            'rid'                       => "90000|90100|90101",
            'pid'                       => 90100,
            'type'                      => 1,
            'route'                     => "activity/activityAdd",
            'api_path'                  => "",
            'status'                    => 1,
            'sort'                      => 1,
            'css_class'                 => 'fa fa-lg fa-fw fa-gamepad',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 90102,
            'cn_name'                   => "更改活动状态",
            'en_name'                   => "Activity Status",
            'rid'                       => "90000|90100|90102",
            'pid'                       => 90100,
            'type'                      => 1,
            'route'                     => "activity/activityStatus",
            'api_path'                  => "",
            'status'                    => 1,
            'sort'                      => 1,
            'css_class'                 => 'fa fa-lg fa-fw fa-gamepad',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 90600,
            'cn_name'                   => '活动奖品',
            'en_name'                   => 'Activity Prize',
            'rid'                       => '90000|90600',
            'pid'                       => 90000,
            'type'                      => 0,
            'route'                     => 'activity/activityPrize',
            'api_path'                  => 'activity/activityPrize',
            'status'                    => 1,
            'sort'                      => 3,
            'css_class'                 => 'fa fa-lg fa-fw fa-gamepad',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 90700,
            'cn_name'                   => '活动规则',
            'en_name'                   => 'Activity Rule',
            'rid'                       => '90000|90700',
            'pid'                       => 90000,
            'type'                      => 0,
            'route'                     => 'activity/activityRule',
            'api_path'                  => 'activity/activityRule',
            'status'                    => 1,
            'sort'                      => 3,
            'css_class'                 => 'fa fa-lg fa-fw fa-gamepad',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 90800,
            'cn_name'                   => '活动记录',
            'en_name'                   => 'Activity Log',
            'rid'                       => '90000|90800',
            'pid'                       => 90000,
            'type'                      => 0,
            'route'                     => 'activity/activityLog',
            'api_path'                  => 'activity/receive-lists',
            'status'                    => 1,
            'sort'                      => 3,
            'css_class'                 => 'fa fa-lg fa-fw fa-gamepad',
        ]);


        DB::table('partner_menu_config')->insert([
            'id'                        => 90801,
            'cn_name'                   => "审核活动",
            'en_name'                   => "Activity Check",
            'rid'                       => "90000|90800|90801",
            'pid'                       => 90800,
            'type'                      => 1,
            'route'                     => "activity/activityCheck",
            'api_path'                  => "",
            'status'                    => 1,
            'sort'                      => 1,
            'css_class'                 => 'fa fa-lg fa-fw fa-gamepad',
        ]);
        /** ======================== 帮助中心 ========================= */
        DB::table('partner_menu_config')->insert([
            'id'                        => 50000,
            'cn_name'                   => "帮助中心",
            'en_name'                   => "Help Center",
            'rid'                       => "50000",
            'pid'                       => 0,
            'type'                      => 0,
            'route'                     => 0,
            'status'                    => 1,
            'sort'                      => 5,
            'css_class'                 => 'fa fa-lg fa-fw fa-leanpub',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 50100,
            'cn_name'                   => "分类菜单",
            'en_name'                   => "Help Menu",
            'rid'                       => "50000|50100",
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => "system/helpMenu",
            'api_path'                  => "system/helpMenu",
            'status'                    => 1,
            'sort'                      => 3,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 50101,
            'cn_name'                   => "添加分类",
            'en_name'                   => "HelpMenu Add",
            'rid'                       => "50000|50100|50101",
            'pid'                       => 50100,
            'type'                      => 1,
            'route'                     => "system/helpMenuAdd",
            'api_path'                  => "",
            'status'                    => 1,
            'sort'                      => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 50102,
            'cn_name'                   => "删除分类",
            'en_name'                   => "HelpMenu Del",
            'rid'                       => "50000|50100|50102",
            'pid'                       => 50100,
            'type'                      => 1,
            'route'                     => "system/helpMenuDel",
            'api_path'                  => "",
            'status'                    => 1,
            'sort'                      => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 50200,
            'cn_name'                   => '内容列表',
            'en_name'                   => 'Help Content',
            'rid'                       => '50000|50200',
            'pid'                       => 50000,
            'type'                      => 0,
            'route'                     => 'system/helpMenuList',
            'api_path'                  => 'system/helpMenuList',
            'status'                    => 1,
            'sort'                      => 3,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 50201,
            'cn_name'                   => '删除内容',
            'en_name'                   => 'Help ContentDel',
            'rid'                       => '50000|50200|50201',
            'pid'                       => 50200,
            'type'                      => 1,
            'route'                     => 'system/addHelpContent',
            'api_path'                  => 'system/addHelpContent',
            'status'                    => 1,
            'sort'                      => 3,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 50202,
            'cn_name'                   => '修改内容',
            'en_name'                   => 'Help ContentEdit',
            'rid'                       => '50000|50200|50202',
            'pid'                       => 50200,
            'type'                      => 1,
            'route'                     => 'system/editHelp',
            'api_path'                  => 'system/editHelp',
            'status'                    => 1,
            'sort'                      => 3,
        ]);

        /** ======================== 历史数据 ========================= */

        DB::table('partner_menu_config')->insert([
            'id'                        => 100000,
            'cn_name'                   => "历史数据",
            'en_name'                   => "Back Up",
            'rid'                       => "100000",
            'pid'                       => 0,
            'type'                      => 0,
            'route'                     => 0,
            'status'                    => 1,
            'sort'                      => 10,
            'css_class'                 => 'fa fa-lg fa-fw fa-leanpub',
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 100100,
            'cn_name'                   => '帐变历史记录',
            'en_name'                   => 'Fund Change',
            'rid'                       => '100000|100100',
            'pid'                       => 100000,
            'type'                      => 0,
            'route'                     => 'backup/funcChange',
            'api_path'                  => 'backup/funcChange',
            'status'                    => 0,
            'sort'                      => 1,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 100200,
            'cn_name'                   => '商户访问历史记录',
            'en_name'                   => 'Partner Visit',
            'rid'                       => '100000|100200',
            'pid'                       => 100000,
            'type'                      => 0,
            'route'                     => 'backup/partnerVisit',
            'api_path'                  => 'backup/partnerVisit',
            'status'                    => 1,
            'sort'                      => 2,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 100300,
            'cn_name'                   => '商户行为历史记录',
            'en_name'                   => 'Partner Behavior',
            'rid'                       => '100000|100300',
            'pid'                       => 100000,
            'type'                      => 0,
            'route'                     => 'backup/partnerBehavior',
            'api_path'                  => 'backup/partnerBehavior',
            'status'                    => 1,
            'sort'                      => 3,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 100400,
            'cn_name'                   => '玩家访问历史记录',
            'en_name'                   => 'Player Visit',
            'rid'                       => '100000|100400',
            'pid'                       => 100000,
            'type'                      => 0,
            'route'                     => 'backup/playerVisit',
            'api_path'                  => 'backup/playerVisit',
            'status'                    => 1,
            'sort'                      => 4,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 100500,
            'cn_name'                   => '玩家IP历史记录',
            'en_name'                   => 'Player Ip',
            'rid'                       => '100000|100500',
            'pid'                       => 100000,
            'type'                      => 0,
            'route'                     => 'backup/playerIp',
            'api_path'                  => 'backup/playerIp',
            'status'                    => 1,
            'sort'                      => 5,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 100600,
            'cn_name'                   => '玩家返点历史记录',
            'en_name'                   => 'Player Commission',
            'rid'                       => '100000|100600',
            'pid'                       => 100000,
            'type'                      => 0,
            'route'                     => 'backup/playerCommission',
            'api_path'                  => 'backup/playerCommission',
            'status'                    => 1,
            'sort'                      => 6,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 100700,
            'cn_name'                   => '玩家投注历史记录',
            'en_name'                   => 'Player Project',
            'rid'                       => '100000|100700',
            'pid'                       => 100000,
            'type'                      => 0,
            'route'                     => 'backup/playerProject',
            'api_path'                  => 'backup/playerProject',
            'status'                    => 1,
            'sort'                      => 7,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 100800,
            'cn_name'                   => '玩家追号历史记录',
            'en_name'                   => 'Player Trace',
            'rid'                       => '100000|100800',
            'pid'                       => 100000,
            'type'                      => 0,
            'route'                     => 'backup/playerTrace',
            'api_path'                  => 'backup/playerTrace',
            'status'                    => 1,
            'sort'                      => 8,
        ]);

        DB::table('partner_menu_config')->insert([
            'id'                        => 100900,
            'cn_name'                   => '奖期列表历史记录',
            'en_name'                   => 'Issues List',
            'rid'                       => '100000|100900',
            'pid'                       => 100000,
            'type'                      => 0,
            'route'                     => 'backup/issuesList',
            'api_path'                  => 'backup/issuesList',
            'status'                    => 1,
            'sort'                      => 8,
        ]);
    }
}
