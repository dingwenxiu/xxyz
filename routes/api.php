<?php

// API 登录
Route::group(['namespace' => "Api"], function () {
    // 登录
    Route::any('login',                                                 'AuthController@login')->name('login');
    Route::options('login',                                             'AuthController@login')->name('login');

    // 注册
    Route::post('register',                                             'AuthController@register')->name('register');
    Route::options('register',                                          'AuthController@register')->name('register');

    // 验证码
    Route::any('captcha',                                               'AuthController@captcha')->name('captcha');

    // 下级注册
    Route::any('proxy/register',                                        'ApiProxyController@registerByLink')->name('proxy/register');

    // 基础配置
    Route::any('site/baseConfig',                                       'ApiPlatformController@baseConfig')->name('site/baseConfig');
    Route::any('site/popularMethods',                                   'ApiPlatformController@popularMethods')->name('site/popularMethods');
    Route::any('site/hotLotteryList',                                   'ApiPlatformController@hotLotteryList')->name('site/hotLotteryList');

    Route::any('site/openList',                                         'ApiPlatformController@openList')->name('site/openList');

    // 是否加密参数
    Route::any('site/openEncryption',                                   'ApiPlatformController@openEncryption')->name('site/openEncryption');

    // 登出
    Route::any('logout',                                                'AuthController@logout')->name('logout');
    Route::options('logout',                                            'AuthController@logout')->name('logout');

    // 帮助中心
    Route::any('helpMenuList',                                           'ApiSiteController@helpMenuList')->name('helpMenuList');

    //彩票游戏列表
    Route::any('lottery/lotteryList',                                   'ApiGameController@lotteryList')->name('lottery/lotteryList');

    //活动列表
    Route::any('activity/getLists',                                    'ApiActivityController@getLists')->name('activity/getLists');

    Route::any('lottery/lotteryInfo',                                   'ApiGameController@lotteryInfo')->name('lottery/lotteryInfo');

    // 活动
    Route::any('activity/getOne',                                       'ApiActivityController@getOne')->name('activity/getOne');

    //发消息客服（未登录）
    Route::any('talk/sendServiceUnLogin',                                     'ApiTalkController@sendServiceUnLogin')->name('talk/sendServiceUnLogin');
    //查询客服列表（未登录）
    Route::any('talk/friendListUnLogin',                                     'ApiTalkController@friendListUnLogin')->name('talk/sendServiceUnLogin');
    //绑定（未登录）
    Route::any('talk/bindUnLogin',                                     'ApiTalkController@bindUnLogin')->name('talk/bindUnLogin');
    //获取历史记录（未登录）
    Route::any('talk/getTalkHistoryUnLogin',                                     'ApiTalkController@getTalkHistoryUnLogin')->name('talk/getTalkHistoryUnLogin');

});

// API 主逻辑
Route::group(['middleware' => ['jwt.auth', 'api'], 'namespace' => "Api"], function () {

    // 游戏投注相关
    Route::any('lottery/bet',                                           'ApiGameController@bet')->name('lottery/bet');//

    Route::any('lottery/availableIssues',                               'ApiGameController@issueInfo')->name('lottery/issueInfo');
    Route::any('lottery/lastIssue/{lotterySign}',                       'ApiGameController@lastIssue')->name('lottery/lastIssue');
    Route::any('lottery/issueHistory',                                  'ApiGameController@issueHistory')->name('lottery/issueHistory');

    Route::any('lottery/cancelProject',                                 'ApiGameController@cancelProject')->name('lottery/cancelProject');
    Route::any('lottery/cancelTrace',                                   'ApiGameController@cancelTrace')->name('lottery/cancelTrace');
    Route::any('lottery/cancelTraceDetail',                             'ApiGameController@cancelTraceDetail')->name('lottery/cancelTraceDetail');

    Route::any('lottery/projectHistory',                                'ApiGameController@projectHistory')->name('lottery/projectHistory');
    Route::any('lottery/casinoProjectHistory',                          'ApiGameController@casinoProjectHistory')->name('lottery/casinoProjectHistory');
    Route::any('lottery/traceHistory',                                  'ApiGameController@traceHistory')->name('lottery/traceHistory');
    Route::any('lottery/traceDetail/{id}',                              'ApiGameController@traceDetail')->name('lottery/traceDetail');

    Route::any('lottery/trend',                                         'ApiGameController@trend')->name('lottery/trend');

    // 用户相关
    Route::any('player/info',                                           'ApiPlayerController@info')->name('player/info');
    Route::any('player/setInfo',                                        'ApiPlayerController@setInfo')->name('player/setInfo');
    Route::any('player/setAvatar',                                      'ApiPlayerController@setAvatar')->name('player/setAvatar');
    Route::any('player/balance',                                        'ApiPlayerController@balance')->name('player/balance');

    Route::any('player/optionData',                                     'ApiPlayerController@optionData')->name('player/optionData');
    Route::any('player/cityList',                                       'ApiPlayerController@cityList')->name('player/cityList');
    Route::any('player/cardList',                                       'ApiPlayerController@cardList')->name('player/cardList');
    Route::any('player/bindCard',                                       'ApiPlayerController@bindCard')->name('player/bindCard');
    Route::any('player/bindCardCheck',                                  'ApiPlayerController@bindCardCheck')->name('player/bindCardCheck');

    Route::any('player/setFundPassword',                                'ApiPlayerController@setFundPassword')->name('player/setFundPassword');
    Route::any('player/changeFundPassword',                             'ApiPlayerController@changeFundPassword')->name('player/changeFundPassword');
    Route::any('player/changeLoginPassword',                            'ApiPlayerController@changeLoginPassword')->name('player/changeLoginPassword');
    Route::any('player/accountChangeList',                              'ApiPlayerController@accountChangeList')->name('player/accountChangeList');
    Route::any('player/changeTypeList',                                 'ApiPlayerController@accountChangeTypeList')->name('player/changeTypeList');

    Route::any('player/accountChangeDetail',                            'ApiPlayerController@accountChangeDetail')->name('player/accountChangeDetail');

    // 支付
    Route::any('player/rechargeList',                                   'ApiFinanceController@rechargeList')->name('player/rechargeList');
    Route::any('player/getRechargeChannel',                             'ApiFinanceController@getRechargeChannel')->name('player/getRechargeChannel');
    Route::any('player/recharge',                                       'ApiFinanceController@recharge')->name('player/recharge');
    Route::any('player/withdrawList',                                   'ApiFinanceController@withdrawList')->name('player/withdrawList');
    Route::any('player/withdraw',                                       'ApiFinanceController@withdraw')->name('player/withdraw');
    Route::any('player/configureList',                                  'ApiFinanceController@configureList')->name('player/configureList');
    Route::any('player/detail',                                         'ApiPlayerController@detail')->name('player/detail');

    // 代理管理
    Route::any('proxy/main',                                            'ApiProxyController@proxyMain')->name('proxy/proxyMain');
    Route::any('proxy/childList',                                       'ApiProxyController@childList')->name('proxy/childList');
    Route::any('proxy/childTeamBalance',                                'ApiProxyController@childTeamBalance')->name('proxy/childTeamBalance');
    Route::any('proxy/addChild',                                        'ApiProxyController@addChild')->name('proxy/addChild');
    Route::any('proxy/inviteLinkList',                                  'ApiProxyController@inviteLinkList')->name('proxy/inviteLinkList');
    Route::any('proxy/addInviteLink',                                   'ApiProxyController@addInviteLink')->name('proxy/addInviteLink');
    Route::any('proxy/delInviteLink',                                   'ApiProxyController@delInviteLink')->name('proxy/delInviteLink');

    Route::any('proxy/salarySet',                                       'ApiProxyController@salarySet')->name('proxy/salarySet');
    Route::any('proxy/bonusSet',                                        'ApiProxyController@bonusSet')->name('proxy/bonusSet');
    Route::any('proxy/prizeGroupSet',                                   'ApiProxyController@prizeGroupSet')->name('proxy/prizeGroupSet');
    Route::any('proxy/transferToChild',                                 'ApiProxyController@transferToChild')->name('proxy/transferToChild');

    // 上下级分红比例
    Route::any('proxy/childsDividend',                                  'ApiPlayerController@childsDividend')->name('proxy/childsDividend');


    // 公告相关
    Route::any('noticeList',                                            'ApiCommonController@noticeList')->name('noticeList');
    //站内信
    Route::any('getMessageList',                                        'ApiPlayerController@getMessageList')->name('getMessageList');
    //站内信提示 已读
	Route::any('readMessage',                                           'ApiPlayerController@readMessage')->name('readMessage');
	// 用户删除站内信
	Route::any('deleteMessage',                                         'ApiPlayerController@deleteMessage')->name('deleteMessage');
	// 公告弹窗不在弹
	Route::any('noPopup',                                               'ApiCommonController@noPopup')->name('noPopup');

	// 个人报表相关
    Route::any('report/player/salaryList',                              'ApiReportController@playerSalaryList')->name('report/player/salaryList');
    Route::any('report/player/dividendList',                            'ApiReportController@playerDividendList')->name('report/player/dividendList');
    Route::any('report/player/profitList',                              'ApiReportController@playerProfitList')->name('report/player/profitList');

    // 团队 报表
    Route::any('report/team/salaryList',                                'ApiReportController@teamSalaryList')->name('report/team/salaryList');
    Route::any('report/team/dividendList',                              'ApiReportController@teamDividendList')->name('report/team/dividendList');
    Route::any('report/team/profitList',                                'ApiReportController@teamProfitList')->name('report/team/profitList');
    Route::any('report/team/casinoProfitList',                          'ApiReportController@teamCasinoProfitList')->name('report/team/casinoProfitList');
    Route::any('report/team/statList',                                  'ApiReportController@teamStatList')->name('report/team/statList');

    // 发放下级分红
    Route::any('report/playerDividendSend',                             'ApiReportController@playerDividendSend')->name('report/playerDividendSend');

    //活动管理
    Route::any('activity/get-prizes',                                   'ApiActivityController@getPrizes')->name('activity/get-prizes');
    Route::post('activity/joinAct',                                     'ApiActivityController@joinAct')->name('activity/join');
    Route::get('activity/get-records',                                  'ApiActivityController@getRecords')->name('activity/get-records');

    //聊天相关
    Route::any('talk/getTalkConfig',                                   'ApiTalkController@getTalkConfig')->name('talk/getTalkConfig');
    Route::any('talk/bind',                                            'ApiTalkController@bind')->name('talk/bind');
    Route::any('talk/isCidOnLine',                                     'ApiTalkController@isCidOnLine')->name('talk/isCidOnLine');
    Route::any('talk/sendMsg',                                         'ApiTalkController@sendMsg')->name('talk/sendMsg');
    Route::any('talk/friendList',                                      'ApiTalkController@friendList')->name('talk/friendList');
    Route::any('talk/getTalkHistory',                                  'ApiTalkController@getTalkHistory')->name('talk/getTalkHistory');
    Route::any('talk/sendService',                                     'ApiTalkController@sendService')->name('talk/sendService');
    Route::any('talk/serviceHistory',                                     'ApiTalkController@serviceHistory')->name('talk/serviceHistory');

});
