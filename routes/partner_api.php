<?php

// API 登录
Route::group(['namespace' => "PartnerApi"], function () {
    // 登录
    Route::post('login',                                                        'ApiAuthController@login')->name('login');
    Route::options('login',                                                     'ApiAuthController@login')->name('login');

    // 登出
    Route::any('logout',                                                        'ApiAuthController@logout')->name('logout');
    Route::options('logout',                                                    'ApiAuthController@logout')->name('logout');

    // 发送验证码
    Route::any('sendCode',                                                      'ApiAuthController@sendCode')->name('sendCode');

    //上传
    Route::any('uploadImg',                                                    'ApiUploadController@uploadImg')->name('uploadImg');

});

// Partner Api 商户管理
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\Admin"], function () {

    // 清空缓存
    Route::any('admin/partnerAdminCacheClean',                                  'ApiPartnerAdminController@partnerAdminCacheClean')->name('Admin/partnerAdminCacheClean');

    // 首页模块开关
    Route::any('admin/partnerModuleSet/{id}',                                   'ApiPartnerAdminController@partnerModuleSet')->name('Admin/partnerModuleSet');
    Route::any('admin/partnerModuleDel/{id}',                                   'ApiPartnerAdminController@partnerModuleDel')->name('Admin/partnerModuleDel');
    Route::any('admin/partnerModelList',                                        'ApiPartnerAdminController@partnerModelList')->name('Admin/partnerModelList');

    // 首页导航
    Route::any('admin/partnerAdminNavigationSet/{id}',                          'ApiPartnerAdminController@partnerAdminNavigationSet')->name('Admin/partnerAdminNavigationSet');
    Route::any('admin/partnerAdminNavigationDel/{id}',                          'ApiPartnerAdminController@partnerAdminNavigationDel')->name('Admin/partnerAdminNavigationDel');
    Route::any('admin/partnerAdminNavigationList',                              'ApiPartnerAdminController@partnerAdminNavigationList')->name('Admin/partnerAdminNavigationList');


    // 首页模块化
    Route::any('admin/partnerAdminHomeModuleSet/{id}',                          'ApiPartnerAdminController@partnerAdminHomeModuleSet')->name('Admin/partnerAdminHomeModuleSet');
    Route::any('admin/partnerAdminHomeSet/{id}',                                'ApiPartnerAdminController@partnerAdminHomeSet')->name('Admin/partnerAdminHomeSet');
    Route::any('admin/partnerAdminHomeDel/{id}',                                'ApiPartnerAdminController@partnerAdminHomeDel')->name('Admin/partnerAdminHomeDel');
    Route::any('admin/partnerAdminHomeContentList/{id}',                        'ApiPartnerAdminController@partnerAdminHomeContentList')->name('Admin/partnerAdminHomeContentList');
    Route::any('admin/partnerAdminHomeList',                                    'ApiPartnerAdminController@partnerAdminHomeList')->name('Admin/partnerAdminHomeList');
    Route::any('admin/partnerTelegramChannelList',                              'ApiPartnerAdminController@partnerTelegramChannelList')->name('Admin/partnerTelegramChannelList');
    Route::any('admin/partnerTelegramChannelEdit/{id?}',                        'ApiPartnerAdminController@partnerTelegramChannelEdit')->name('Admin/partnerTelegramChannelEdit');
    Route::any('admin/partnerTelegramChannelGenId/{id?}',                       'ApiPartnerAdminController@partnerTelegramChannelGenId')->name('Admin/partnerTelegramChannelGenId');


    // 商户　管理员　管理
    Route::any('admin/adminUserList',                                           'ApiPartnerAdminController@partnerAdminUserList')->name('Admin/adminUserList');
    Route::any('admin/adminUserAdd',                                            'ApiPartnerAdminController@partnerAdminUserAdd')->name('Admin/adminUserAdd');
    Route::any('admin/adminUserStatus/{id}',                                    'ApiPartnerAdminController@partnerAdminUserStatus')->name('Admin/adminUserStatus');
    Route::any('admin/delAdminUser/{id}',                                       'ApiPartnerAdminController@delAdminUser')->name('Admin/delAdminUser');
    Route::any('admin/editPassword',                                            'ApiPartnerAdminController@editPassword')->name('Admin/editPassword');
    Route::any('admin/setFundPassword',                                         'ApiPartnerAdminController@setFundPassword')->name('Admin/setFundPassword');
    Route::any('admin/setAdminPassword/{adminId}',                              'ApiPartnerAdminController@setAdminPassword')->name('Admin/setAdminPassword');
    Route::any('admin/setAdminFundPassword/{adminId}',                          'ApiPartnerAdminController@setAdminFundPassword')->name('Admin/setAdminFundPassword');

    // 商户详情
    Route::any('admin/adminUserDetail/{id}',                                    'ApiPartnerAdminController@adminUserDetail')->name('Admin/adminUserDetail');

    // 商户　管理组　管理
    Route::any('admin/adminGroupList',                                          'ApiPartnerAdminController@partnerAdminGroupList')->name('Admin/adminGroupList');
    Route::any('admin/adminGroupAdd',                                           'ApiPartnerAdminController@partnerAdminGroupAdd')->name('Admin/adminGroupAdd');
    Route::any('admin/adminGroupStatus/{id}',                                   'ApiPartnerAdminController@partnerAdminGroupStatus')->name('Admin/adminGroupStatus');
    Route::any('admin/adminGroupAcl/{id}',                                      'ApiPartnerAdminController@partnerAdminGroupAcl')->name('Admin/adminGroupAcl');
    Route::any('admin/adminGroupDel/{id}',                                      'ApiPartnerAdminController@partnerAdminGroupDel')->name('Admin/adminGroupDel');
    Route::any('admin/adminGroupSetAcl/{id}',                                   'ApiPartnerAdminController@partnerAdminGroupSetAcl')->name('Admin/adminGroupSetAcl');

    //查看管理组权限
    Route::any('partner/adminGroupAcl/{id}',                                    'ApiPartnerAdminController@adminUserBehaviorList')->name('Partner/adminGroupAcl');


    // 商户域名分配
    Route::any('admin/domainList',                                              'ApiPartnerDomainController@partnerDomainList')->name('Admin/domainList');
    
    // 商户 菜单管理
    Route::any('admin/menuList',                                                'ApiPartnerMenuController@partnerMenuList')->name('Admin/menuList');
    Route::any('admin/menuStatus/{menuId}',                                     'ApiPartnerMenuController@partnerMenuStatus')->name('Admin/menuStatus');
    Route::any('partner/partnerMenuConfigList',                                 'ApiPartnerMenuController@partnerMenuConfigList')->name('partner/partnerMenuConfigList');

    // 商户 访问日志管理
    Route::any('admin/accessLogList',                                           'ApiPartnerController@partnerAccessLogList')->name('Admin/accessLogList');

    // 商户 管理员日志
    Route::any('admin/adminUserBehaviorList',                                   'ApiPartnerController@adminUserBehaviorList')->name('Admin/adminUserBehaviorList');

    //商户二维码

    Route::any('admin/editUpLoadImg',                                           'ApiPartnerController@editUpLoadImg')->name('admin/editUpLoadImg');

    Route::any('admin/qrImage',                                                 'ApiPartnerController@qrImage')->name('admin/qrImage');
    Route::any('admin/qrCodeDel',                                               'ApiPartnerController@qrCodeDel')->name('admin/qrCodeDel');

    // 客服
    Route::any('admin/csSet/{id}',                                              'ApiPartnerController@csSet')->name('admin/csSet');
    Route::any('admin/csList',                                                  'ApiPartnerController@csList')->name('admin/csList');


    // 审核
    Route::any('admin/saveCheckUser',                                              'ApiPartnerController@saveCheckUser')->name('admin/saveCheckUser');
    Route::any('admin/getCheckUser',                                               'ApiPartnerController@getCheckUser')->name('admin/getCheckUser');
    Route::any('admin/delCheckUser',                                               'ApiPartnerController@delCheckUser')->name('admin/delCheckUser');
    Route::any('admin/getCheckType',                                               'ApiPartnerController@getCheckType')->name('admin/getCheckType');
    Route::any('admin/getCheckUserOne',                                            'ApiPartnerController@getCheckUserOne')->name('admin/getCheckUserOne');

    // 广告位置
    Route::any('admin/getAdvertising',                    'ApiAdvertisingController@getAdvertising')->name('admin/getAdvertising');
    Route::any('admin/delAdvertising',                    'ApiAdvertisingController@delAdvertising')->name('admin/delAdvertising');
    Route::any('admin/saveAdvertising',                   'ApiAdvertisingController@saveAdvertising')->name('admin/saveAdvertising');
    Route::any('admin/getType',                           'ApiAdvertisingController@getType')->name('admin/getType');



});

// API 活动列表
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\Activity"], function () {

    // 活动图片上传
    Route::any('activity/activityUploadImg',                      'ApiActivityRuleController@activityUploadImg')->name('activity/activityUploadImg');

    //活动规则
    Route::any('activity/ruleSet/{id}',                      'ApiActivityRuleController@ruleSet')->name('activity/ruleSet');
    Route::any('activity/ruleDel/{id}',                      'ApiActivityRuleController@ruleDel')->name('activity/ruleDel');
    Route::any('activity-rule/getList',                      'ApiActivityRuleController@getList')->name('activity-rule/getList');
    Route::any('activity-rule/getRule',                      'ApiActivityRuleController@getRule')->name('activity-rule/getRule');

    //活动奖品管理
    Route::any('activity/prize/getLists',                                       'ApiActivityPrizeController@getLists')->name('activity/prize/getLists');
    Route::any('activity/prize/set/{id}',                                       'ApiActivityPrizeController@set')->name('activity/prize/set');
    Route::any('activity/prize/del/{id}',                                            'ApiActivityPrizeController@del')->name('activity/prize/del');

    //活动记录管理
    Route::any('activity/check/getLists',                                   'ApiActivityCheckController@getLists')->name('activity/check/getLists');
    Route::any('activity/check/getParams',                                   'ApiActivityCheckController@getParams')->name('activity/check/getParams');
    Route::any('activity/check/{id}',                                       'ApiActivityCheckController@check')->name('activity/check');
});

// API 彩票游戏
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\Lottery"], function () {
    // 彩种相关
    Route::any('lottery/lotteryList',                                           'ApiLotteryController@lotteryList')->name('lottery/lotteryList');

    //彩票广告图上传 和删除
    Route::any('lottery/lotteryAdImgUpload',                                    'ApiLotteryController@lotteryAdImgUpload')->name('lottery/lotteryAdImgUpload');
    Route::any('lottery/lotteryAdImgDelete',                                    'ApiLotteryController@lotteryAdImgDelete')->name('lottery/lotteryAdImgDelete');

    //彩种设置
    Route::any('lottery/lotterySet/{sign}',                                     'ApiLotteryController@lotterySet')->name('lottery/lotterySet');
    Route::any('lottery/lotteryDetail/{sign}',                                  'ApiLotteryController@lotteryDetail')->name('lottery/lotteryDetail');
    Route::any('lottery/lotteryStatus/{sign}',                                  'ApiLotteryController@lotteryStatus')->name('lottery/lotteryStatus');
    Route::any('lottery/lotteryPopular/{sign}',                                 'ApiLotteryController@lotteryPopular')->name('lottery/lotteryPopular');
    Route::any('lottery/lotterySetRate/{sign}',                                 'ApiLotteryController@lotterySetRate')->name('lottery/lotterySetRate');

    // 控水是否开启
    Route::any('lottery/rateOpen',                                              'ApiLotteryController@rateOpen')->name('lottery/rateOpen');


    Route::any('lottery/lotteryInfoSet/{id}',                                   'ApiLotteryController@lotteryInfoSet')->name('lottery/lotteryInfoSet');
    Route::any('lottery/lotteryUploadImg',                                      'ApiLotteryController@lotteryUploadImg')->name('lottery/lotteryUploadImg');


    // 玩法相关
    Route::any('lottery/methodList',                                            'ApiLotteryController@methodList')->name('lottery/methodList');
    Route::any('lottery/methodDetail/{id}',                                     'ApiLotteryController@methodDetail')->name('lottery/methodDetail');
    Route::any('lottery/methodStatus/{id}',                                     'ApiLotteryController@methodStatus')->name('lottery/methodStatus');
    Route::any('lottery/methodSet/{id}',                                        'ApiLotteryController@methodSet')->name('lottery/methodSet');

    // 奖期相关
    Route::any('lottery/issueList',                                             'ApiLotteryController@issueList')->name('lottery/issueList');
    Route::any('lottery/issueDetail/{id}',                                      'ApiLotteryController@issueDetail')->name('lottery/issueDetail');

    // 奖期规则
    Route::any('lottery/issueRuleList',                                         'ApiLotteryController@issueRuleList')->name('lottery/issueRuleList');

    // 投注相关
    Route::any('lottery/projectList',                                           'ApiLotteryProjectController@projectList')->name('lottery/projectList');

        // 投注总计
    Route::any('lottery/projectTotal',                                           'ApiLotteryProjectController@projectTotal')->name('lottery/projectTotal');
    Route::any('lottery/projectDetail/{id}',                                    'ApiLotteryProjectController@projectDetail')->name('lottery/projectDetail');
    Route::any('lottery/projectCommission/{id}',                                'ApiLotteryProjectController@projectCommission')->name('lottery/projectCommission');
    Route::any('lottery/projectAccountChange/{id}',                             'ApiLotteryProjectController@projectAccountChange')->name('lottery/projectAccountChange');
    Route::any('lottery/cancelProject/{id}',                                    'ApiLotteryProjectController@cancelProject')->name('lottery/cancelProject');

    // 追号相关
    Route::any('lottery/traceList',                                             'ApiLotteryTraceController@traceList')->name('lottery/traceList');
    Route::any('lottery/traceDetail/{id}',                                      'ApiLotteryTraceController@traceDetail')->name('lottery/traceDetail');
    Route::any('lottery/cancelTrace/{id}',                                      'ApiLotteryTraceController@cancelTrace')->name('lottery/cancelTrace');
    Route::any('lottery/cancelTraceDetail/{id}',                                'ApiLotteryTraceController@cancelTraceDetail')->name('lottery/cancelTraceDetail');

    // 返点相关
    Route::any('lottery/commissionList',                                        'ApiLotteryCommissionController@commissionList')->name('lottery/commissionList');
});

// API 娱乐城
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\Casino"], function () {


    // 上传删除 平台logo
    Route::any('casino/uploadImage',     'ApiCasinoController@uploadImage')->name('casino/uploadImage');
    Route::any('casino/deleteImage',     'ApiCasinoController@deleteImage')->name('casino/deleteImage');

    // 娱乐城玩法 广告图片删除和上传
    Route::any('casino/adImgUpload',     'ApiCasinoController@adImgUpload')->name('casino/adImgUpload');
    Route::any('casino/adImgDelete',     'ApiCasinoController@adImgDelete')->name('casino/adImgDelete');

    // 娱乐城游戏图片删除和上传
    Route::any('casino/casinoGameImgUpload',     'ApiCasinoController@casinoGameImgUpload')->name('casino/casinoGameImgUpload');
    Route::any('casino/casinoGameImgDelete',     'ApiCasinoController@casinoGameImgDelete')->name('casino/casinoGameImgDelete');


    // 同步游戏列表
    Route::any('casino/callGameList',                                           'ApiCasinoController@callGameList')->name('casino/callGameList');
    // 同步游戏类型
    Route::any('casino/seriesLists',                                            'ApiCasinoController@seriesLists')->name('casino/seriesLists');
    // 获取游戏列表
    Route::any('casino/getGameList',                                            'ApiCasinoController@getGameList')->name('casino/getGameList');
    // 禁用启用游戏状态
    Route::any('casino/gameControl',                                            'ApiCasinoController@gameControl')->name('casino/gameControl');
    // 获取游戏平台
    Route::any('casino/getPlatType',                                            'ApiCasinoController@getPlatType')->name('casino/getPlatType');
    // 设置首页显示游戏
    Route::any('casino/setHomeGame',                                            'ApiCasinoController@setHomeGame')->name('casino/setHomeGame');
    Route::any('casino/setHomePlat',                                            'ApiCasinoController@setHomePlat')->name('casino/setHomePlat');
    // 获取游戏投注
    Route::any('casino/getBetLog',                                              'ApiCasinoController@getBetLog')->name('casino/getBetLog');
    // 接口日志
    Route::any('casino/getApiLog',                                              'ApiCasinoController@getApiLog')->name('casino/getApiLog');
    Route::any('casino/getStatistics',                                              'ApiCasinoController@getStatistics')->name('casino/getStatistics');
    // 转账记录
    Route::any('casino/getTransfer',                                            'ApiCasinoController@getTransfer')->name('casino/getTransfer');
});

// API 玩家相关
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\Player"], function () {
    // 玩家信息
    Route::any('player/playerList',                                             'ApiPlayerController@playerList')->name('player/playerList');
    Route::any('player/playerAddTop',                                           'ApiPlayerController@playerAddTop')->name('player/playerAddTop');
    Route::any('player/playerDetail/{id}',                                      'ApiPlayerController@playerDetail')->name('player/playerDetail');
    Route::any('player/playerStatus/{id}',                                      'ApiPlayerController@playerStatus')->name('player/playerStatus');
    Route::any('player/allowedTransfer/{id}',                                   'ApiPlayerController@allowedTransfer')->name('player/allowedTransfer');
    Route::any('player/playerPassword/{id}',                                    'ApiPlayerController@playerPassword')->name('player/playerPassword');
    // 直接冻结玩家
    Route::any('player/playerFrozen/{id}',                                      'ApiPlayerController@playerFrozen')->name('player/playerFrozen');
    //申请解冻 提交审核
    Route::any('player/playerUnfrozen/{id}',                                    'ApiPlayerController@playerUnfrozen')->name('player/playerUnfrozen');
    Route::any('player/playerTransfer/{id}',                                    'ApiPlayerController@playerTransfer')->name('player/playerTransfer');
    Route::any('player/frozenAll/{id}',                                         'ApiPlayerController@frozenAll')->name('player/frozenAll');
    Route::any('player/playerMark/{id}',                                        'ApiPlayerController@playerMark')->name('player/playerMark');

     //用户等级设置
    Route::any('player/playerVipConfig',                                        'ApiPlayerController@playerVipConfig')->name('player/playerVipConfig');
    Route::any('player/addPlayerVipConfig/{id?}',                               'ApiPlayerController@addPlayerVipConfig')->name('player/addPlayerVipConfig');
    Route::any('player/playerVipConfigDetail/{id}',                             'ApiPlayerController@playerVipConfigDetail')->name('player/playerVipConfigDetail');
    //玩家等级设置
    Route::any('player/setPlayerVipLevel',                                     'ApiPlayerController@setPlayerVipLevel')->name('player/setPlayerVipLevel');


    //日工资和分红
    Route::any('player/salarySet/{id}',                                         'ApiPlayerController@playerSetSalary')->name('player/salarySet');
    Route::any('player/bonusSet/{id}',                                          'ApiPlayerController@playerSetBonus')->name('player/bonusSet');
    Route::any('player/prizeGroupSet',                                          'ApiPlayerController@prizeGroupSet')->name('player/prizeGroupSet');

    // 上级转下级(暂时没有地方用到)
    Route::any('player/transferFrom/{id}',                                       'ApiPlayerController@transferFrom')->name('player/transferFrom');

    // 银行卡
    Route::any('player/playerCardList',                                         'ApiPlayerController@playerCardList')->name('player/playerCardList');
    Route::any('player/playerCardAdd/{id?}',                                    'ApiPlayerController@playerCardAdd')->name('player/playerCardAdd');
    Route::any('player/playerCardDetail/{id}',                                  'ApiPlayerController@playerCardDetail')->name('player/playerCardDetail');
    Route::any('player/editPlayerCard/{id?}',                                   'ApiPlayerController@editPlayerCard')->name('player/editPlayerCard');
    // 删除 银行卡
    Route::any('player/cardStatus/{id}',                                        'ApiPlayerController@cardStatus')->name('player/cardStatus');

    
    // 薪资
    Route::any('player/salaryReportList',                                       'ApiSalaryController@reportSalaryList')->name('player/salaryReportList');
    Route::any('player/salaryReportSend',                                       'ApiSalaryController@reportSalarySend')->name('player/salaryReportSend');

    // 分红
    Route::any('player/dividendReportList',                                     'ApiDividendController@dividendReportList')->name('player/dividendReportList');
    Route::any('player/dividendReportSend',                                     'ApiDividendController@dividendReportSend')->name('player/dividendReportSend');

    // 审核数据
    Route::any('player/reviewList',                                             'ApiPlayerController@reviewList')->name('player/reviewList');
    Route::any('player/reviewProcess/{id}',                                     'ApiPlayerController@reviewProcess')->name('player/reviewProcess');
    Route::any('player/reviewDetail/{id}',                                      'ApiPlayerController@reviewDetail')->name('player/reviewDetail');

    // 玩家日志 相关
    Route::any('player/userIpLogList',                                          'ApiPlayerController@userIpLogList')->name('player/userIpLogList');
    Route::any('player/userIp/{userId}',                                        'ApiPlayerController@userIp')->name('player/userIp');
    Route::any('player/userPlayerLogList',                                      'ApiPlayerController@userPlayerLogList')->name('player/userPlayerLogList');
    Route::any('player/userPlayerDetail/{userId}',                              'ApiPlayerController@userPlayerDetail')->name('player/userPlayerDetail');
});

// API 系统相关
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\System"], function () {

    Route::any('menus',                                                         'ApiSystemController@menu')->name('menu');
    Route::any('online',                                                        'ApiSystemController@online')->name('online');

    //获取当前服务器时间
	Route::any('getTimeNow',                                                    'ApiSystemController@getTimeNow')->name('getTimeNow');

	// 配置
    Route::any('configureList',                                                 'ApiSystemController@configureList')->name('configureList');
    Route::any('configureAdd/{id?}',                                            'ApiSystemController@configureAdd')->name('configureAdd');
    Route::any('configureDetail/{id}',                                          'ApiSystemController@configureDetail')->name('configureDetail');
    Route::any('configureStatus/{id}',                                          'ApiSystemController@configureStatus')->name('configureStatus');
    Route::any('configureFlush',                                                'ApiSystemController@configureFlush')->name('configureFlush');

    // 颜色配置
	Route::any('colorConfigureList',                                            'ApiSystemController@colorConfigureList')->name('colorConfigureList');
	Route::any('colorConfigureEdit',                                            'ApiSystemController@colorConfigureEdit')->name('colorConfigureEdit');
	Route::any('colorConfigureDelete',                                          'ApiSystemController@colorConfigureDelete')->name('colorConfigureDelete');
	

	// 缓存
    Route::any('cacheList',                                                     'ApiSystemController@cacheList')->name('cacheList');
    Route::any('cacheFlush/{key}',                                              'ApiSystemController@cacheFlush')->name('cacheFlush');

    //公告管理
    Route::any('system/noticeList',                                             'ApiNoticeController@noticeList')->name('system/noticeList');
    Route::any('system/noticeFlush',                                            'ApiNoticeController@noticeFlush')->name('system/noticeFlush');
    Route::any('system/noticeAdd/{id?}',                                        'ApiNoticeController@noticeAdd')->name('system/noticeAdd');
    Route::any('system/noticeStatus/{id}',                                      'ApiNoticeController@noticeStatus')->name('system/noticeStatus');
    Route::any('system/noticeTop/{id}',                                         'ApiNoticeController@noticeTop')->name('system/noticeTop');
    Route::any('system/noticeDel',                                              'ApiNoticeController@noticeDel')->name('system/noticeDel');

    // 玩家头像
    Route::any('system/setAvatar',                                               'ApiAvatarController@setAvatar')->name('system/setAvatar');
    Route::any('system/playerAvatarList',                                        'ApiAvatarController@playerAvatarList')->name('system/playerAvatarList');
    Route::any('system/avatarImgDel',                                            'ApiAvatarController@avatarImgDel')->name('system/avatarImgDel');

    // 管理员头像上传
	Route::any('system/adminAvatarImgUpload/{id}',                               'ApiAvatarController@adminAvatarImgUpload')->name('system/adminAvatarImgUpload');
	Route::any('system/adminAvatarImgDel/{id}',                                  'ApiAvatarController@adminAvatarImgDel')->name('system/adminAvatarImgDel');
	// 获取商户logo
	Route::any('system/getPartnerLogo',                                          'ApiAvatarController@getPartnerLogo')->name('system/getPartnerLogo');

	//商户logo
    Route::any('system/logoUpLoadImg',                                           'ApiNoticeController@logoUpLoadImg')->name('system/logoUpLoadImg');
    Route::any('system/logoImage',                                               'ApiNoticeController@logoImage')->name('system/logoImage');
    Route::any('system/logoDel',                                                 'ApiNoticeController@logoDel')->name('system/logoDel');

    // 帮助中心
    Route::any('system/helpMenuList',                                           'ApiHelpMenuController@helpMenuList')->name('system/helpMenuList');
    Route::any('system/helpMenu',                                               'ApiHelpMenuController@helpMenu')->name('system/helpMenu');
    Route::any('system/helpMenuAdd',                                            'ApiHelpMenuController@helpMenuAdd')->name('system/helpMenuAdd');
    Route::any('system/helpMenuDel/{id}',                                       'ApiHelpMenuController@helpMenuDel')->name('system/helpMenuDel');
    Route::any('system/addHelpContent/{pid}',                                   'ApiHelpMenuController@addHelpContent')->name('system/addHelpContent');
    Route::any('system/editHelp/{pid}',                                         'ApiHelpMenuController@editHelp')->name('system/editHelp');
    Route::any('system/contentDel/{id}',                                        'ApiHelpMenuController@contentDel')->name('system/contentDel');

    // 站内信
    Route::any('system/getList',                                                'ApiPartnerMessageController@getList')->name('system/getList');
    Route::any('system/contentDel',                                             'ApiPartnerMessageController@contentDel')->name('system/contentDel');
    Route::any('system/addMessageContent',                                      'ApiPartnerMessageController@addMessageContent')->name('system/addMessageContent');
});

// API 帐变相关
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\Player"], function () {
    // 帐变记录
    Route::any('report/accountChangeList',                                      'ApiAccountController@accountChangeReportList')->name('report/accountChangeList');

    // 帐变记录统计
    Route::any('report/accountChangeTotal',                                      'ApiAccountController@accountChangeReportTotal')->name('report/accountChangeTotal');

    Route::any('report/accountChangeProjectDetail',                             'ApiAccountController@accountChangeProjectDetail')->name('report/accountChangeProjectDetail');

    // 充值记录
    Route::any('rechargeList',                                                  'ApiFinanceController@rechargeList')->name('rechargeList');
    Route::any('rechargeHand/{id}',                                             'ApiFinanceController@rechargeHand')->name('rechargeHand');

    //　提现
    Route::any('withdrawList',                                                  'ApiFinanceController@withdrawList')->name('withdrawList');
    Route::any('withdrawPassedList/{id}',                                       'ApiFinanceController@viewWithdrawList')->name('viewWithdrawList');
    Route::any('viewWithdrawList/{id}',                                         'ApiFinanceController@viewWithdrawList')->name('viewWithdrawList');
    Route::any('viewWithdrawHandList/{id}',                                     'ApiFinanceController@viewWithdrawList')->name('viewWithdrawList');
    Route::any('withdrawHand/{id}',                                             'ApiFinanceController@withdrawHand')->name('withdrawHand');
    Route::any('withdrawLog/{id}',                                              'ApiFinanceController@withdrawLog')->name('withdrawLog');
    Route::any('withdrawGenOrder',                                              'ApiFinanceController@withdrawGenOrder')->name('withdrawGenOrder');

    // 提现日志
    Route::any('withdrawLogList',                                               'ApiFinanceController@withdrawLogList')->name('withdrawLogList');
    Route::any('withdrawLog/{order_id}',                                        'ApiFinanceController@withdrawLog')->name('withdrawLog');

    // 充值日志
    Route::any('rechargeLogList',                                               'ApiFinanceController@rechargeLogList')->name('rechargeLogList');
    Route::any('rechargeLog/{order_id}',                                        'ApiFinanceController@rechargeLog')->name('rechargeLog');

    //　提现审核
    Route::any('finance/withdrawCheckProcess/{id}',                             'ApiFinanceController@withdrawCheckProcess')->name('finance/withdrawCheckProcess');
    Route::any('withdrawCheckProcess/{id}',                                     'ApiFinanceController@withdrawCheckProcess')->name('withdrawCheckProcess');
    Route::any('withDrawBetTimes',                                              'ApiFinanceController@withDrawBetTimes')->name('withDrawBetTimes');
    
});

// 支付
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\Finance"], function () {
    //支付厂商列表
    //列表
    Route::any('finance/platform/list',                                         'ApiPlatformController@list')->name('list');
    //添加
    Route::any('finance/platform/create/{id?}',                                 'ApiPlatformController@create')->name('create');
    //删除
    Route::any('finance/platform/del/{id}',                                     'ApiPlatformController@del')->name('del');
    Route::any('finance/platform/listChild/{sign}',                               'ApiPlatformController@listChild')->name('listChild');

    //支付账户
    //列表
    Route::any('finance/platformAccount/list',                                  'ApiPlatformAccountController@list')->name('list');
    //状态
    Route::any('finance/platformAccount/status/{id}',                           'ApiPlatformAccountController@status')->name('status');
    //添加-修改
    Route::any('finance/platformAccount/create/{id?}',                          'ApiPlatformAccountController@create')->name('create');
    //删除
    Route::any('finance/platformAccount/del/{id}',                              'ApiPlatformAccountController@del')->name('del');
    //更新充值渠道
    Route::any('finance/platformAccount/updateForeignChannel/{id}',             'ApiPlatformAccountController@updateForeignChannel')->name('updateForeignChannel');
    Route::any('finance/platformAccount/updatePaymentChannel/{id}',             'ApiPlatformAccountController@updatePaymentChannel')->name('updatePaymentChannel');
    Route::any('finance/platformAccount/updateRechargeChannel/{id}',            'ApiPlatformAccountController@updateRechargeChannel')->name('updateRechargeChannel');

    //支付类型 (可能没有用到) -----------------
    //列表
    Route::any('finance/channelType/list',                                      'ApiChannelTypeController@list')->name('list');
    //添加-修改
    Route::any('finance/channelType/create/{id?}',                              'ApiChannelTypeController@create')->name('create');
    //删除
    Route::any('finance/channelType/del/{id}',                                  'ApiChannelTypeController@del')->name('del');
	//----------------------

	//支付厂商-开放渠道
	//列表
    Route::any('finance/platformChannel/list',                                  'ApiPlatformChannelController@list')->name('list');
    //状态
    Route::any('finance/platformChannel/status/{id}',                           'ApiPlatformChannelController@status')->name('status');
    //添加-修改
    Route::any('finance/platformChannel/create/{id?}',                          'ApiPlatformChannelController@create')->name('create');
    //删除
    Route::any('finance/platformChannel/del/{id}',                              'ApiPlatformChannelController@del')->name('del');


	//支付账户-开放渠道
    //列表
    Route::any('finance/platformAccountChannel/list',                           'ApiPlatformAccountChannelController@list')->name('list');
    //编辑状态
    Route::any('finance/platformAccountChannel/status/{id}',                    'ApiPlatformAccountChannelController@status')->name('status');
    //添加-修改
    Route::any('finance/platformAccountChannel/create/{id?}',                   'ApiPlatformAccountChannelController@create')->name('create');
    //删除
    Route::any('finance/platformAccountChannel/del/{id}',                       'ApiPlatformAccountChannelController@del')->name('del');
});

// API 报表相关
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\Report"], function () {

    // 每日统计
    Route::any('report/statUserDayList'  ,                                      'ApiReportController@statUserDayList')->name('report/statUserDayList');
    Route::any('report/statUserDayCheck/{id}'  ,                                'ApiReportController@statUserDayCheck')->name('report/statUserDayCheck');

    // 用户总统计
    Route::any('report/statUserList'  ,                                         'ApiReportController@statUserList')->name('report/statUserList');

    // 彩种每日销量
    Route::any('report/lotteryDayList'  ,                                       'ApiReportController@lotteryDayList')->name('report/lotteryDayList');

    // 代理工资列表
    Route::any('report/salaryList'  ,                                           'ApiReportController@salaryList')->name('report/salaryList');

    // 代理分红列表
    Route::any('report/dividendList'  ,                                         'ApiReportController@dividendList')->name('report/dividendList');

    // 当日记录
    Route::any('report/getDailyStatistical'  ,                                  'ApiReportController@getDailyStatistical')->name('report/getDailyStatistical');

});

// 历史记录备份
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\Backup"], function () {

    // 帐变记录备份
    Route::any('backup/funcChange'  ,                                         'ApiBackupController@funcChange')->name('backup/funcChange');

    // 商户访问记录备份
    Route::any('backup/partnerVisit'  ,                                        'ApiBackupController@partnerVisit')->name('backup/partnerVisit');

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
    Route::any('backup/playerTrace'  ,                                           'ApiBackupController@playerTrace')->name('backup/playerTrace');

    // 玩家追号记录详情
    Route::any('backup/playerTraceDes'  ,                                         'ApiBackupController@playerTraceDes')->name('backup/playerTraceDes');

    // 奖期列表记录
    Route::any('backup/issuesList'  ,                                            'ApiBackupController@issuesList')->name('backup/issuesList');

});

// 模板
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\Template"], function () {
    Route::any('getTemplate',             'ApiTemplateController@getTemplate')->name('getTemplate');
    Route::any('setTemplate',             'ApiTemplateController@setTemplate')->name('setTemplate');

    Route::any('getTemplateColor',        'ApiTemplateColorController@getTemplateColor')->name('getTemplateColor');
});



//聊天 客服
Route::group(['middleware' => ['set.guard:partner_api', 'jwt.auth', 'partner.api'], 'namespace' => "PartnerApi\Talk"], function () {
    Route::any('talk/delete',             'ApiTalkController@delete')->name('talk/delete');
    Route::any('talk/clearCache',             'ApiTalkController@clearCache')->name('talk/clearCache');
    Route::any('talk/bind',             'ApiTalkController@bind')->name('talk/bind');
    Route::any('talk/IsCidOnLine',             'ApiTalkController@isCidOnLine')->name('talk/IsCidOnLine');
    Route::any('talk/searchService',             'ApiTalkController@searchService')->name('talk/searchService');
    Route::any('talk/openService',             'ApiTalkController@openService')->name('talk/openService');
    Route::any('talk/closeService',             'ApiTalkController@closeService')->name('talk/closeService');
    Route::any('talk/editService',             'ApiTalkController@editService')->name('talk/editService');
    Route::any('talk/deleteServiceHistory',             'ApiTalkController@deleteServiceHistory')->name('talk/deleteServiceHistory');
    Route::any('talk/upService',             'ApiTalkController@upService')->name('talk/upService');
    Route::any('talk/downService',             'ApiTalkController@downService')->name('talk/downService');
    Route::any('talk/changeService',             'ApiTalkController@changeService')->name('talk/changeService');
    Route::any('talk/enterService',             'ApiTalkController@enterService')->name('talk/enterService');
    Route::any('talk/serviceSendClient',             'ApiTalkController@serviceSendClient')->name('talk/serviceSendClient');
    Route::any('talk/endService',             'ApiTalkController@endService')->name('talk/endService');
    Route::any('talk/serviceList',             'ApiTalkController@serviceList')->name('talk/serviceList');
    Route::any('talk/serviceHistory',             'ApiTalkController@serviceHistory')->name('talk/serviceHistory');
});