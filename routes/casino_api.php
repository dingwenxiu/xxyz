<?php

// 娱乐城API
Route::group(['namespace' => "CasinoApi"], function () {
    Route::any('casino/gameList',       'ApiCasinoHomeController@gameList')->name('casino/gameList');
});
Route::group(['middleware' => ['jwt.auth', 'api'], 'namespace' => "CasinoApi"], function () {
    Route::any('casino/joinGame',       'ApiCasinoController@joinGame')->name('casino/joinGame');
    Route::any('casino/getBalance',     'ApiCasinoController@getBalance')->name('casino/getBalance');
    Route::any('casino/getAllBalance',     'ApiCasinoController@getAllBalance')->name('casino/getAllBalance');
    Route::any('casino/transferIn',     'ApiCasinoController@transferIn')->name('casino/transferIn');
    Route::any('casino/transferTo',     'ApiCasinoController@transferTo')->name('casino/transferTo');

    Route::any('casino/fishingList',    'ApiCasinoHomeController@fishingList')->name('casino/fishingList');
    Route::any('casino/liveList',    'ApiCasinoHomeController@liveList')->name('casino/liveList');
    Route::any('casino/gamePlat',       'ApiCasinoHomeController@gamePlat')->name('casino/gamePlat');
});
