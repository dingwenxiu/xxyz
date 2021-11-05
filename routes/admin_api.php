<?php

// API 登录
Route::group(['namespace' => "AdminApi"], function () {
    // 登录
    Route::post('login',                                                                    'ApiAuthController@login')->name('login');
    Route::options('login',                                                                 'ApiAuthController@login')->name('login');

    // 登出
    Route::any('logout',                                                                    'ApiAuthController@logout')->name('logout');
    Route::options('logout',                                                                'ApiAuthController@logout')->name('logout');


    // 发送验证码
    Route::any('sendCode',                                                                  'ApiAuthController@sendCode')->name('sendCode');

    // 充值回调
    Route::any('callback/{sign}',                                                            'CallbackController@rechargeCallback')->name('rechargeCallback');

    Route::any('encode',                                                                    'ApiSystemController@encode')->name('encode');
});

// API 活动列表
Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Activity"], function () {
    // 活动列表
    Route::any('activityList',                                                              'ApiActivityController@activityList')->name('activityList');
    Route::any('activityAdd',                                                         'ApiActivityController@activityAdd')->name('activityAdd');
});

// API 后台用户
Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Admin"], function () {

    Route::any('menus',                                                                     'ApiAdminController@menu')->name('menu');

    // 管理员
    Route::any('admin/adminUserList',                                                       'ApiAdminController@adminUserList')->name('admin/adminUserList');
    Route::any('admin/adminUserDetail/{id}',                                                'ApiAdminController@adminUserDetail')->name('admin/adminUserDetail');
    Route::any('admin/adminUserAdd',                                                        'ApiAdminController@adminUserAdd')->name('admin/adminUserAdd');
    Route::any('admin/adminUserStatus/{id}',                                                'ApiAdminController@adminUserStatus')->name('admin/adminUserStatus');
    Route::any('admin/adminUserPassword/{id}',                                              'ApiAdminController@adminUserPassword')->name('admin/adminUserPassword');

    // 管理员删除
	Route::any('admin/adminUserDel/{id}',                                                        'ApiAdminController@adminUserDel')->name('admin/adminUserDel');


	// 修改管理自身密码
	Route::any('admin/editPassword',                                                        'ApiAdminController@editPassword')->name('admin/editPassword');

	// 管理组
    Route::any('admin/adminGroupList',                                                      'ApiAdminGroupController@adminGroupList')->name('admin/adminGroupList');
    Route::any('admin/adminGroupDetail/{id}',                                               'ApiAdminGroupController@adminGroupDetail')->name('admin/adminGroupDetail');
    Route::any('admin/adminGroupAdd/{id}',                                                  'ApiAdminGroupController@adminGroupAdd')->name('admin/adminGroupAdd');
    Route::any('admin/adminGroupStatus/{id}',                                               'ApiAdminGroupController@adminGroupStatus')->name('admin/adminGroupStatus');
    Route::any('admin/adminGroupDel/{id}',                                                  'ApiAdminGroupController@adminGroupDel')->name('admin/adminGroupDel');
    Route::any('admin/adminGroupsAcl/{id}',                                                 'ApiAdminGroupController@adminGroupsAcl')->name('admin/adminGroupsAcl');
    Route::any('admin/adminGroupsSetAcl/{id}',                                              'ApiAdminGroupController@adminGroupsSetAcl')->name('admin/adminGroupsSetAcl');

    // 权限
    Route::any('admin/adminGroupAclEdit/{id}',                                              'ApiAdminGroupController@groupAclEdit')->name('admin/groupAclEdit');
    Route::any('admin/adminGroupAclDetail/{id}',                                            'ApiAdminGroupController@groupAclDetail')->name('admin/groupAclDetail');

    // 管理菜单
    Route::any('admin/adminMenuList',                                                       'ApiAdminMenuController@adminMenuList')->name('admin/adminMenuList');
    Route::any('admin/adminMenuDetail/{id}',                                                'ApiAdminMenuController@adminMenuDetail')->name('admin/adminMenuDetail');
    Route::any('admin/adminMenuAdd/{id?}',                                                  'ApiAdminMenuController@adminMenuAdd')->name('admin/adminMenuAdd');

    // 管理员日志
    Route::any('admin/adminLogList',                                                        'ApiAdminController@adminLogList')->name('admin/adminLogList');

    // 管理员日志
    Route::any('admin/adminUserBehaviorList',                                               'ApiAdminController@adminBehaviorList')->name('admin/adminUserBehaviorList');

	// 审核管理
	Route::any('admin/reviewList',                                                          'ApiAdminController@reviewList')->name('player/reviewList');
	Route::any('admin/reviewProcess/{id}',                                                  'ApiAdminController@reviewProcess')->name('player/reviewProcess');
	Route::any('admin/reviewDetail/{id}',                                                   'ApiAdminController@reviewDetail')->name('player/reviewDetail');
});

// API 游戏相关
Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Lottery"], function () {

    // 彩种相关
    Route::any('lottery/lotteryList',                                                      'ApiLotteryController@lotteryList')->name('lottery/lotteryList');
    // 添加遊戲 
    Route::any('lottery/lotteryAdd',                                                       'ApiLotteryController@lotteryAdd')->name('lottery/lotteryAdd');
    //修改遊戲
    Route::any('lottery/lotteryEdit/{id?}',                                                'ApiLotteryController@lotteryEdit')->name('lottery/lotteryAdd');

    Route::any('lottery/lotteryDetail/{sign}',                                              'ApiLotteryController@lotteryDetail')->name('lottery/lotteryDetail');
    Route::any('lottery/lotteryStatus/{sign}',                                              'ApiLotteryController@lotteryStatus')->name('lottery/lotteryStatus');
    Route::any('lottery/lotteryAssign/{sign}',                                              'ApiLotteryController@lotteryAssign')->name('lottery/lotteryAssign');
    Route::any('lottery/lotteryFlush',                                                      'ApiLotteryController@lotteryFlush')->name('lottery/lotteryFlush');

    Route::any('lottery/lotteryInfoSet/{id}',                                               'ApiLotteryController@lotteryEdit')->name('lottery/lotteryInfoSet');
    Route::any('lottery/lotteryUploadImg',                                                  'ApiLotteryController@lotteryUploadImg')->name('lottery/lotteryUploadImg');

    // 玩法相关
    Route::any('lottery/methodList',                                                        'ApiLotteryMethodController@methodList')->name('lottery/methodList');
    Route::any('lottery/methodDetail/{id}',                                                 'ApiLotteryMethodController@methodDetail')->name('lottery/methodDetail');
    Route::any('lottery/methodStatus/{id}',                                                 'ApiLotteryMethodController@methodStatus')->name('lottery/methodStatus');
    Route::any('lottery/methodSet/{id}',                                                    'ApiLotteryMethodController@methodSet')->name('lottery/methodSet');

    // 奖期相关
    Route::any('lottery/issueList',                                                         'ApiLotteryIssueController@issueList')->name('lottery/issueList');
    Route::any('lottery/issueDetail/{id}',                                                  'ApiLotteryIssueController@issueDetail')->name('lottery/issueDetail');
    Route::any('lottery/issueGen',                                                          'ApiLotteryIssueController@issueGen')->name('lottery/issueGen');
    Route::any('lottery/issueDel',                                                          'ApiLotteryIssueController@issueDel')->name('lottery/issueDel');
    Route::any('lottery/issueEncode/{id}',                                                  'ApiLotteryIssueController@issueEncode')->name('lottery/issueEncode');
    Route::any('lottery/issueCancel/{id}',                                                  'ApiLotteryIssueController@issueCancel')->name('lottery/issueCancel');
    Route::any('lottery/issueOpen/{id}',                                                    'ApiLotteryIssueController@issueOpen')->name('lottery/issueOpen');
    Route::any('lottery/issueSend/{id}',                                                    'ApiLotteryIssueController@issueSend')->name('lottery/issueSend');
    Route::any('lottery/issueTrace/{id}',                                                   'ApiLotteryIssueController@issueTrace')->name('lottery/issueTrace');

    // 奖期规则
    Route::any('lottery/issueRuleList',                                                     'ApiLotteryIssueRuleController@issueRuleList')->name('lottery/issueRuleList');
    Route::any('lottery/issueRuleAdd/{id?}',                                                'ApiLotteryIssueRuleController@issueRuleAdd')->name('lottery/issueRuleAdd');
    Route::any('lottery/issueRuleDetail/{id}',                                              'ApiLotteryIssueRuleController@issueRuleDetail')->name('lottery/issueRuleDetail');

    // 投注相关
    Route::any('lottery/projectList',                                                       'ApiLotteryProjectController@projectList')->name('lottery/projectList');

    Route::any('lottery/projectHistoryList',                                                'ApiLotteryProjectController@projectHistoryList')->name('lottery/projectHistoryList');
    Route::any('lottery/traceHistoryList',                                                  'ApiLotteryTraceController@traceList')->name('lottery/traceHistoryList');
    Route::any('lottery/projectCommission/{id}',                                            'ApiLotteryProjectController@projectCommission')->name('lottery/projectCommission');
    Route::any('lottery/projectAccountChange/{id}',                                         'ApiLotteryProjectController@projectAccountChange')->name('lottery/projectAccountChange');
    Route::any('lottery/cancelProject/{id}',                                                'ApiLotteryProjectController@cancelProject')->name('lottery/cancelProject');


    // 追号相关
    Route::any('lottery/traceList',                                                         'ApiLotteryTraceController@traceList')->name('lottery/traceList');
    Route::any('lottery/traceDetail/{id}',                                                  'ApiLotteryTraceController@traceDetail')->name('lottery/traceDetail');
    Route::any('lottery/cancelTrace/{id}',                                                  'ApiLotteryTraceController@cancelTrace')->name('lottery/cancelTrace');

    // 控水
    Route::any('lottery/jackpotIssueList',                                                  'ApiLotteryJackpotController@jackpotIssueList')->name('jackpotIssueList');
    Route::any('lottery/jackpotIssueDetail',                                                'ApiLotteryJackpotController@jackpotIssueDetail')->name('jackpotIssueDetail');
    Route::any('lottery/issueDetail',                                                       'ApiLotteryJackpotController@issueDetail')->name('issueDetail');

});

// API 娱乐游戏相关
Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Casino"], function () {

    // 平台相关
    Route::any('casino/platformList',                                                       'ApiCasinoPlatformController@platformList')->name('casino/platformList');
    Route::any('casino/platformFetch/{sign?}',                                              'ApiCasinoPlatformController@platformFetch')->name('casino/platformFetch');

});

// API 玩家相关
Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Player"], function () {
    // 玩家信息
    Route::any('player/playerList',                                                         'ApiPlayerController@playerList')->name('player/playerList');
    Route::any('player/playerDetail/{id}',                                                  'ApiPlayerController@playerDetail')->name('player/detail');
    Route::any('player/status/{id}',                                                        'ApiPlayerController@playerStatus')->name('player/status');
    // 银行卡
    Route::any('player/cardList',                                                           'ApiPlayerCardController@cardList')->name('player/cardList');
    Route::any('player/cardDetail/{id}',                                                    'ApiPlayerCardController@cardDetail')->name('player/cardDetail');

    // 薪资契约
    Route::any('player/salaryReportList',                                                   'ApiSalaryController@salaryReportList')->name('player/salaryReportList');

    // 分红契约
    Route::any('player/dividendReportList',                                                 'ApiPlayerDividendController@dividendList')->name('player/dividendReportList');
//    Route::any('player/dividendDetail/{id}',                                                'ApiPlayerDividendController@dividendDetail')->name('player/dividendDetail');

});

// API 系统相关
Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi"], function () {
    // 公告管理
    Route::any('system/noticeList',                                                         'ApiSystemController@noticeList')->name('system/noticeList');
    Route::any('system/noticeFlush',                                                        'ApiSystemController@noticeFlush')->name('system/noticeFlush');
    Route::any('system/noticeDetail/{id}',                                                  'ApiSystemController@noticeDetail')->name('system/noticeDetail');
    Route::any('system/online',                                                             'ApiSystemController@online')->name('system/online');

    // 配置
    Route::any('system/configureList',                                                      'ApiSystemController@getSystemConfigureList')->name('system/configureList');
//    Route::any('system/configureList',                                                      'ApiSystemController@configureList')->name('system/configureList');
    Route::any('system/configureAdd/{id?}',                                                 'ApiSystemController@configureAdd')->name('system/configureAdd');
    Route::any('system/configureDetail/{id}',                                               'ApiSystemController@configureDetail')->name('system/configureDetail');
    Route::any('system/configureStatus/{id}',                                               'ApiSystemController@configureStatus')->name('system/configureStatus');
    Route::any('system/configureFlush',                                                     'ApiSystemController@configureFlush')->name('system/configureFlush');


    // 商户配置
	Route::any('partner/partnerConfigureList',                                                      'ApiSystemController@partnerConfigureList')->name('partner/partnerConfigureList');
	Route::any('partner/partnerConfigureAdd/{id?}',                                                 'ApiSystemController@partnerConfigureAdd')->name('partner/partnerConfigureAdd');
	Route::any('partner/partnerConfigureDetail/{id}',                                               'ApiSystemController@partnerConfigureDetail')->name('partner/partnerConfigureDetail');
	Route::any('partner/partnerConfigureStatus/{id}',                                               'ApiSystemController@partnerConfigureStatus')->name('partner/partnerConfigureStatus');
	Route::any('partner/partnerConfigureFlush',                                                     'ApiSystemController@partnerConfigureFlush')->name('partner/partnerConfigureFlush');

		// 缓存
    Route::any('system/cacheList',                                                          'ApiSystemController@cacheList')->name('cacheList');
    Route::any('system/cacheFlush/{key}',                                                   'ApiSystemController@cacheFlush')->name('cacheFlush');

    // Telegram
    Route::any('system/telegramChannelList',                                                'ApiSystemController@telegramChannelList')->name('telegramChannelList');
    Route::any('system/telegramChannelAdd/{id?}',                                           'ApiSystemController@telegramChannelAdd')->name('telegramChannelAdd');
    Route::any('system/telegramChannelEdit/{id?}',                                          'ApiSystemController@telegramChannelEdit')->name('telegramChannelEdit');
    Route::any('system/telegramChannelDel/{id}',                                            'ApiSystemController@telegramChannelDel')->name('telegramChannelDel');
    Route::any('system/telegramChannelGenId/{id}',                                          'ApiSystemController@telegramChannelGenId')->name('telegramChannelGenId');
    Route::any('system/telegramChannelStatus/{id}',                                         'ApiSystemController@telegramChannelStatus')->name('telegramChannelStatus');

    // 管理员日志
    Route::any('adminLogList',                                                              'ApiSystemController@adminLogList')->name('adminLogList');
});

// API 帐变相关
Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi"], function () {

    // 帐变类型
    Route::any('account/accountChangeTypeList',                                             'ApiAccountController@accountChangeTypeList')->name('account/accountChangeTypeList');
    Route::any('account/accountChangeTypeDetail/{id}',                                      'ApiAccountController@accountChangeTypeDetail')->name('account/accountChangeTypeDetail');
    Route::any('account/accountChangeTypeFlush',                                            'ApiAccountController@accountChangeTypeFlush')->name('account/accountChangeTypeFlush');

    // 帐变记录
    Route::any('account/accountChangeReportList',                                           'ApiAccountController@accountChangeReportList')->name('account/accountChangeReportList');
    Route::any('account/accountChangeReportDetail/{id}',                                    'ApiAccountController@accountChangeReportDetail')->name('account/accountChangeReportDetail');

    // 帐变记录 = 历史
    Route::any('account/accountChangeReportHistoryList',                                    'ApiAccountController@accountChangeReportList')->name('account/accountChangeReportHistoryList');
    Route::any('account/accountChangeReportHistoryDetail/{id}',                             'ApiAccountController@accountChangeReportDetail')->name('account/accountChangeReportHistoryDetail');
});

// API 财务相关
Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Finance"], function () {
    // 充值记录
    Route::any('finance/rechargeList',                                                      'ApiRechargeController@rechargeList')->name('finance/rechargeList');
    Route::any('finance/rechargeHand/{id}',                                                 'ApiRechargeController@rechargeHand')->name('finance/rechargeHand');
    Route::any('finance/rechargeLog/{id}',                                                  'ApiRechargeController@rechargeLog')->name('finance/rechargeLog');
    Route::any('finance/rechargeLogList',                                                   'ApiRechargeController@rechargeLogList')->name('finance/rechargeLogList');

    //　提现
    Route::any('finance/withdrawList',                                                      'ApiWithdrawController@withdrawList')->name('finance/withdrawList');
    Route::any('finance/viewWithdrawList/{id}',                                             'ApiWithdrawController@viewWithdrawList')->name('finance/viewWithdrawList');
    Route::any('finance/viewWithdrawHandList/{id}',                                         'ApiWithdrawController@viewWithdrawList')->name('finance/viewWithdrawList');
    Route::any('finance/withdrawPassedList/{id}',                                           'ApiWithdrawController@viewWithdrawList')->name('finance/viewWithdrawList');

    Route::any('finance/withdrawHand/{id}',                                                 'ApiWithdrawController@withdrawHand')->name('finance/withdrawHand');
    Route::any('finance/withdrawLogList',                                                   'ApiWithdrawController@withdrawLogList')->name('finance/withdrawLogList');
    Route::any('finance/withdrawLog/{id}',                                                  'ApiWithdrawController@withdrawLog')->name('finance/withdrawLog');

    //　提现审核
    Route::any('finance/withdrawCheckProcess/{id}',                                         'ApiWithdrawController@withdrawCheckProcess')->name('finance/withdrawCheckProcess');

    //获取充值网关
    Route::any('finance/getRechargeChannel',                                                'ApiRechargeController@getRechargeChannel')->name('finance/getRechargeChannel');

});

// 代理分红和代理日工资
Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Player"], function () {
    Route::any('report/salaryLists',                                                       'ApiSalaryController@salaryReportList')->name('report/salaryList');
});

// API 报表相关
Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Report"], function () {
    // 商户每日报表
    Route::any('report/partnerDayList'  ,                                                   'ApiReportController@statPartnerDayList')->name('report/partnerDayList');

    // 每日统计
    Route::any('report/statUserDayList'  ,                                                  'ApiReportController@statUserDayList')->name('report/statUserDayList');
    Route::any('report/statUserDayCheck/{id}'  ,                                            'ApiReportController@statUserDayCheck')->name('report/statUserDayCheck');

    // 用户总统计
    Route::any('report/statUserList'  ,                                                     'ApiReportController@statUserList')->name('report/statUserList');

    // 彩种每日销量
    Route::any('report/lotteryDayList'  ,                                                   'ApiReportController@lotteryDayList')->name('report/lotteryDayList');

    // 用户工资列表
    Route::any('report/salaryList'  ,                                                       'ApiReportController@salaryList')->name('report/salaryList');

    // 商户分红
    Route::any('report/dividendList'  ,                                                     'ApiReportController@dividendList')->name('report/dividendList');
    //商户报表
    Route::any('report/statPartnerList'  ,                                                   'ApiReportController@statPartnerList')->name('report/statPartnerList');

});

// API 商户
Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Partner"], function () {

    // 商户管理
    Route::any('partner/partnerList',                                                       'ApiPartnerController@partnerList')->name('partner/partnerList');
    Route::any('partner/partnerAdd/{id?}',                                                  'ApiPartnerController@partnerAdd')->name('partner/partnerAdd');
    Route::any('partner/partnerStatus/{id}',                                                'ApiPartnerController@partnerStatus')->name('partner/partnerStatus');
    Route::any('partner/partnerDetail/{id}',                                                'ApiPartnerController@partnerDetail')->name('partner/partnerDetail');
    Route::any('partner/partnerSetCasino',                                                  'ApiPartnerController@partnerSetCasino')->name('partner/partnerSetCasino');
    Route::any('partner/partnerSetAdminMenu/{id}',                                          'ApiPartnerController@partnerSetAdminMenu')->name('partner/partnerSetAdminMenu');
    Route::any('partner/partnerSetUploadImage/{id}',                                        'ApiPartnerController@partnerSetUploadImage')->name('partner/partnerSetUploadImage');
    Route::any('partner/getPartnerCasino',                                                  'ApiPartnerController@getPartnerCasino')->name('partner/getPartnerCasino');

    // 是否开启控水
    Route::any('partner/rateOpen',                                                          'ApiPartnerController@rateOpen')->name('partner/rateOpen');

    // 测试域名添加
	Route::any('partner/testWebAdd',                                                        'ApiPartnerController@testWebAdd')->name('partner/testWebAdd');

	// 商户　管理员　管理
    Route::any('partner/adminUserList',                                                     'ApiPartnerAdminController@adminUserList')->name('partner/adminUserList');
	Route::any('partner/partnerAdminUserAcl/{id}',                                          'ApiPartnerAdminController@partnerAdminUserAcl')->name('partner/partnerAdminUserAcl');

	//總後台 商戶修改密碼
    Route::any('partner/adminUserPassword/{id}',                                            'ApiPartnerAdminController@adminUserPassword')->name('partner/adminUserPassword');

    Route::any('partner/adminUserAdd/{id?}',                                                'ApiPartnerAdminController@adminUserAdd')->name('partner/adminUserAdd');
    Route::any('partner/adminUserStatus/{id}',                                              'ApiPartnerAdminController@adminUserStatus')->name('partner/adminUserStatus');

    // 商户　管理组　管理
    Route::any('partner/adminGroupList',                                                    'ApiPartnerAdminController@adminGroupList')->name('partner/adminGroupList');
    Route::any('partner/adminGroupAdd/{id?}',                                               'ApiPartnerAdminController@adminGroupAdd')->name('partner/partnerAdminGroupAdd');
    Route::any('partner/adminGroupStatus/{id}',                                             'ApiPartnerAdminController@adminGroupStatus')->name('partner/adminGroupStatus');
    Route::any('partner/adminGroupAcl/{id}',                                                'ApiPartnerAdminController@adminGroupAcl')->name('partner/adminGroupAcl');
    Route::any('partner/adminGroupSetAcl/{id}',                                             'ApiPartnerAdminController@adminGroupSetAcl')->name('partner/adminGroupSetAcl');

    // 商户域名分配
    Route::any('partner/domainList',                                                        'ApiPartnerDomainController@domainList')->name('partner/domainList');
    Route::any('partner/domainAdd/{id?}',                                                   'ApiPartnerDomainController@domainAdd')->name('partner/domainAdd');
    Route::any('partner/domainStatus/{id}',                                                 'ApiPartnerDomainController@domainStatus')->name('partner/domainStatus');
    Route::any('partner/domainTestSet/{partner_id}',                                        'ApiPartnerDomainController@domainTestSet')->name('partner/domainTestSet');
    Route::any('partner/domainDel',                                                         'ApiPartnerDomainController@domainDel')->name('partner/domainDel');

    // 商户 菜单管理(暂时弃用)
    Route::any('partner/partnerMenuList',                                                   'ApiPartnerMenuController@partnerMenuList')->name('partner/partnerMenuList');
    Route::any('partner/partnerMenuDel/{id?}',                                              'ApiPartnerMenuController@partnerMenuDel')->name('partner/partnerMenuDel');
    Route::any('partner/partnerMenuAdd/{id?}',                                              'ApiPartnerMenuController@partnerMenuAdd')->name('partner/partnerMenuAdd');
    Route::any('partner/partnerMenuStatus/{id}',                                            'ApiPartnerMenuController@partnerMenuStatus')->name('partner/partnerMenuStatus');


    //商戶 榜定菜單
    Route::any('partner/partnerBindMenuConfig',                                             'ApiPartnerMenuController@partnerBindMenuConfig')->name('partner/partnerBindMenuConfig');

    // 商户 预设菜单 管理
    Route::any('partner/partnerMenuConfigList',                                             'ApiPartnerMenuController@partnerMenuConfigList')->name('partner/partnerMenuConfigList');
    Route::any('partner/partnerMenuConfigAdd/{id?}',                                        'ApiPartnerMenuController@partnerMenuConfigAdd')->name('partner/partnerMenuConfigAdd');
    Route::any('partner/partnerMenuConfigDel/{id?}',                                        'ApiPartnerMenuController@partnerMenuConfigDel')->name('partner/partnerMenuConfigDel');
    Route::any('partner/partnerMenuConfigStatus/{id}',                                      'ApiPartnerMenuController@partnerMenuConfigStatus')->name('partner/partnerMenuConfigStatus');

    // 商户 访问日志管理
    Route::any('partner/adminAccessLogList',                                                   'ApiPartnerLogController@adminAccessLogList')->name('partner/adminAccessLogList');

    // 商户 管理员行为
    Route::any('partner/partnerAdminBehavior',                                              'ApiPartnerLogController@partnerAdminBehavior')->name('partner/partnerAdminBehavior');

    //總後台商戶審核權限
    //商户审核权限列表
    Route::any('partner/partnerReviewPermissionsList',                                       'ApiPartnerAdminPermissionController@partnerReviewPermissionsList')->name('partner/partnerReviewPermissionsList');
    //添加權限
    Route::any('partner/bindPermissions',                                                     'ApiPartnerAdminPermissionController@bindPermissions')->name('partner/bindPermissions');
    //修改权限
    Route::any('partner/editPermissions',                                                     'ApiPartnerAdminPermissionController@editPermissions')->name('partner/editPermissions');
    //删除权限
    Route::any('partner/deletePermissions',                                                     'ApiPartnerAdminPermissionController@deletePermissions')->name('partner/deletePermissions');

});

Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Template"], function () {

    // 商户模板配置
    Route::any('template/addTemplate',              'ApiTemplateController@addTemplate')->name('template/addTemplate');
    Route::any('template/getTemplateList',          'ApiTemplateController@getTemplateList')->name('template/getTemplateList');
    Route::any('template/getTemplateModule',        'ApiTemplateController@getTemplateModule')->name('template/getTemplateModule');
    Route::any('template/setTemplateModule',        'ApiTemplateController@setTemplateModule')->name('template/setTemplateModule');
    Route::any('template/getTemplateOfModule',      'ApiTemplateController@getTemplateOfModule')->name('template/getTemplateOfModule');
    Route::any('template/setTemplateOfModule',      'ApiTemplateController@setTemplateOfModule')->name('template/setTemplateOfModule');

});


Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Finance"], function () {
    //支付厂商列表
    //列表
    Route::any('finance/platform/list',                                                     'ApiPlatformController@list')->name('finance/platform/list');
    //添加
    Route::any('finance/platform/create/{id?}',                                             'ApiPlatformController@create')->name('finance/platform/create');
    //删除
    Route::any('finance/platform/del/{id}',                                                 'ApiPlatformController@del')->name('finance/platform/del');

    //支付账户
    //列表
    Route::any('finance/platformAccount/list',                                              'ApiPlatformAccountController@list')->name('finance/platformAccount/list');
    //状态
    Route::any('finance/platformAccount/status/{id}',                                       'ApiPlatformAccountController@status')->name('finance/platformAccount/status');
    //添加-修改
    Route::any('finance/platformAccount/create/{id?}',                                      'ApiPlatformAccountController@create')->name('finance/platformAccount/create');
    //删除
    Route::any('finance/platformAccount/del/{id}',                                          'ApiPlatformAccountController@del')->name('finance/platformAccount/del');
    //更新充值渠道
    Route::any('finance/platformAccount/updateForeignChannel/{id}',                         'ApiPlatformAccountController@updateForeignChannel')->name('finance/platformAccount/updateForeignChannel');

    //支付类型
    //列表
    Route::any('finance/channelType/list',                                                  'ApiChannelTypeController@list')->name('finance/channelType/list');
    //添加-修改
    Route::any('finance/channelType/create/{id?}',                                          'ApiChannelTypeController@create')->name('finance/channelType/create');
    //删除
    Route::any('finance/channelType/del/{id}',                                              'ApiChannelTypeController@del')->name('finance/channelType/del');
    //图片上传
    Route::any('finance/channelType/channelTypeUploadImg',                                  'ApiChannelTypeController@channelTypeUploadImg')->name('finance/channelType/channelTypeUploadImg');

    //支付账户-开放渠道
    //列表
    Route::any('finance/platformChannel/list',                                              'ApiPlatformChannelController@list')->name('finance/platformChannel/list');
    //状态
    Route::any('finance/platformChannel/status/{id}',                                       'ApiPlatformChannelController@status')->name('finance/platformChannel/status');
    //添加-修改
    Route::any('finance/platformChannel/create/{id?}',                                      'ApiPlatformChannelController@create')->name('finance/platformChannel/create');
    //删除
    Route::any('finance/platformChannel/del/{id}',                                          'ApiPlatformChannelController@del')->name('finance/platformChannel/del');

    //支付厂商-开放渠道
    //列表
    Route::any('finance/platformAccountChannel/list',                                       'ApiPlatformAccountChannelController@list')->name('finance/platformAccountChannel/list');
    //编辑状态
    Route::any('finance/platformAccountChannel/status/{id}',                                'ApiPlatformAccountChannelController@status')->name('finance/platformAccountChannel/status');
    //添加-修改
    Route::any('finance/platformAccountChannel/create/{id?}',                               'ApiPlatformAccountChannelController@create')->name('finance/platformAccountChannel/create');
    //删除
    Route::any('finance/platformAccountChannel/del/{id}',                                   'ApiPlatformAccountChannelController@del')->name('finance/platformAccountChannel/del');
});


Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Backup"], function () {

    // 帐变记录备份
    Route::any('backup/funcChange'  ,                                           'ApiBackupController@funcChange')->name('backup/funcChange');
    // 帐变记录详情备份
    Route::any('backup/accountChangeProjectDetail/{id}'  ,                      'ApiBackupController@accountChangeProjectDetail')->name('backup/accountChangeProjectDetail');

    // 商户访问记录备份
    Route::any('backup/partnerVisit'  ,                                         'ApiBackupController@partnerVisit')->name('backup/partnerVisit');

    // 商户行为记录备份
    Route::any('backup/partnerBehavior'  ,                                      'ApiBackupController@partnerBehavior')->name('backup/partnerBehavior');

    // 玩家访问记录备份
    Route::any('backup/playerVisit'  ,                                          'ApiBackupController@playerVisit')->name('backup/playerVisit');

    // 玩家IP记录备份
    Route::any('backup/playerIp'  ,                                             'ApiBackupController@playerIp')->name('backup/playerIp');

    // 玩家返点记录
    Route::any('backup/playerCommission'  ,                                      'ApiBackupController@playerCommission')->name('backup/playerCommission');

     // 玩家投注记录
    Route::any('backup/playerProject'  ,                                         'ApiBackupController@playerProject')->name('backup/playerProject');

    // 玩家追号记录
    Route::any('backup/playerTrace'  ,                                            'ApiBackupController@playerTrace')->name('backup/playerTrace');

    // 玩家追号记录详情
    Route::any('backup/playerTraceDes'  ,                                         'ApiBackupController@playerTraceDes')->name('backup/playerTraceDes');

    // 奖期列表记录
    Route::any('backup/issuesList'  ,                                            'ApiBackupController@issuesList')->name('backup/issuesList');
});

Route::group(['middleware' => ['set.guard:admin_api', 'jwt.auth', 'admin.api'], 'namespace' => "AdminApi\Talk"], function () {
    // 删除全系统的用户聊天关系缓存数据
    Route::any('talk/systemTalkClearCache'  ,                                            'ApiTalkController@systemTalkClearCache')->name('talk/systemTalkClearCache');
    //删除系统用户的聊天缓存数据
    Route::any('talk/systemTalkClearHistory'  ,                                            'ApiTalkController@systemTalkClearHistory')->name('talk/systemTalkClearHistory');
});


