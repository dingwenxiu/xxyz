<?php

// API 登录
Route::group(['namespace' => "MobileApi"], function () {

    // 登录
    Route::any('login',                                                 'AuthController@login')->name('login');
    Route::options('login',                                             'AuthController@login')->name('login');

    // 注册
    Route::post('register',                                             'AuthController@register')->name('register');
    Route::options('register',                                          'AuthController@register')->name('register');

    // 翻译文件
    Route::any('captcha',                                               'AuthController@captcha')->name('captcha');

    // 下级注册
    Route::any('proxy/register',                                        'ApiProxyController@registerByLink')->name('proxy/register');

    // 网站配置信息
    Route::any('site/info',                                             'ApiSiteController@lotteryInfo')->name('site/info');
    Route::any('site/lotteryList',                                      'ApiSiteController@lotteryList')->name('site/lotteryList');
    Route::any('helpMenuList',                                          'ApiSiteController@helpMenuList')->name('site/helpMenuList');

    // 基础配置
    Route::any('site/baseConfig',                                       'ApiPlatformController@baseConfig')->name('site/baseConfig');
    Route::any('site/notice',                                           'ApiPlatformController@notice')->name('site/notice');
    Route::any('site/city',                                             'ApiPlatformController@city')->name('site/city');

    Route::any('site/openList',                                         'ApiGameController@openList')->name('lottery/openList');


    // 是否加密参数
    Route::any('site/openEncryption',                                   'ApiPlatformController@openEncryption')->name('site/openEncryption');

    // 登出
    Route::any('logout',                                                'AuthController@logout')->name('logout');
    Route::options('logout',                                            'AuthController@logout')->name('logout');

    // 活动列表
    Route::any('activity/getOne',                                    'ApiActivityController@getOne')->name('activity/getOne');

    // 回调
    //Route::any('callback/{sign}',                                      'CallbackController@callback')->name('callback');
});

// API 主逻辑
Route::group(['middleware' => ['jwt.auth', 'api'], 'namespace' => "MobileApi"], function () {

    // 游戏投注相关
    Route::any('lottery/bet',                                           'ApiGameController@bet')->name('lottery/bet');
    Route::any('lottery/lotteryList',                                   'ApiGameController@lotteryList')->name('lottery/lotteryList');
    Route::any('lottery/lotteryInfo',                                   'ApiGameController@lotteryInfo')->name('lottery/lotteryInfo');

    Route::any('lottery/availableIssues',                               'ApiGameController@issueInfo')->name('lottery/issueInfo');
    Route::any('lottery/lastIssue',                                     'ApiGameController@lastIssue')->name('lottery/lastIssue');
    Route::any('lottery/traceIssueList',                                'ApiGameController@traceIssueList')->name('lottery/traceIssueList');
    Route::any('lottery/issueHistory',                                  'ApiGameController@issueHistory')->name('lottery/issueHistory');

    Route::any('lottery/projectCancel',                                 'ApiGameController@projectCancel')->name('lottery/projectCancel');
    Route::any('lottery/traceCancel',                                   'ApiGameController@traceCancel')->name('lottery/traceCancel');

    Route::any('lottery/projectHistory',                                'ApiGameController@projectHistory')->name('lottery/projectHistory');
    Route::any('lottery/traceHistory',                                  'ApiGameController@traceHistory')->name('lottery/traceHistory');
    Route::any('lottery/casinoProjectHistory',                          'ApiGameController@casinoProjectHistory')->name('lottery/casinoProjectHistory');

    // 开奖中心
    Route::any('lottery/openList',                                      'ApiGameController@openList')->name('lottery/openList');
    Route::any('lottery/trend',                                         'ApiGameController@trend')->name('lottery/trend');

    // 用户相关
    Route::any('player/detail',                                         'ApiPlayerController@detail')->name('player/detail');
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

    // 支付
    Route::any('player/getRechargeChannel',                             'ApiFinanceController@getRechargeChannel')->name('player/getRechargeChannel');
    Route::any('player/rechargeChannel',                                'ApiFinanceController@rechargeChannel')->name('player/rechargeChannel');
    Route::any('player/recharge',                                       'ApiFinanceController@recharge')->name('player/recharge');
    Route::any('player/rechargeList',                                   'ApiFinanceController@rechargeList')->name('player/rechargeList');
    Route::any('player/configureList',                                  'ApiFinanceController@configureList')->name('player/configureList');
    Route::any('player/withdrawList',                                   'ApiFinanceController@withdrawList')->name('player/withdrawList');
    Route::any('player/withdraw',                                       'ApiFinanceController@withdraw')->name('player/withdraw');

    //支付 新
    Route::get('payment/get-channels','ApiPaymentController@getChannels')->name('payment/get-channels');
    Route::post('payment/recharge','ApiPaymentController@recharge')->name('payment/recharge');

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

    // 个人报表相关
    Route::any('report/player/salaryList',                              'ApiReportController@playerSalaryList')->name('report/player/salaryList');
    Route::any('report/player/dividendList',                            'ApiReportController@playerDividendList')->name('report/player/dividendList');
    Route::any('report/player/profitList',                              'ApiReportController@playerProfitList')->name('report/player/profitList');
    Route::any('report/player/statList',                                'ApiReportController@playerStatList')->name('report/player/statList');

    // 团队 报表
    Route::any('report/team/salaryList',                                'ApiReportController@teamSalaryList')->name('report/team/salaryList');
    Route::any('report/team/dividendList',                              'ApiReportController@teamDividendList')->name('report/team/dividendList');
    Route::any('report/team/profitList',                                'ApiReportController@teamProfitList')->name('report/team/profitList');
    Route::any('report/team/casinoProfitList',                          'ApiReportController@teamCasinoProfitList')->name('report/team/casinoProfitList');
    Route::any('report/team/statList',                                  'ApiReportController@teamStatList')->name('report/team/statList');
    Route::any('report/team/teamMemberInfoSet',                         'ApiReportController@teamMemberInfoSet')->name('report/team/teamMemberInfoSet');

    //活动管理
    Route::any('activity/get-lists',                                     'ApiActivityController@getLists')->name('activity/get-lists');
    Route::any('activity/get-detail',                                    'ApiActivityController@getDetail')->name('activity/get-detail');
    Route::any('activity/get-prizes',                                    'ApiActivityController@getPrizes')->name('activity/get-prizes');
    Route::any('activity/get-rules',                                     'ApiActivityController@getRules')->name('activity/get-rules');
    Route::any('activity/joinAct',                                         'ApiActivityController@joinAct')->name('activity/join');
    Route::any('activity/get-records',                                   'ApiActivityController@getRecords')->name('activity/get-records');
});