<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    protected $namespace = 'App\Http\Controllers';

    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        // API
        $this->mapApiRoutes();

        // 商户端
        $this->mapPartnerRoutes();

        //　后台管理
        $this->mapAdminRoutes();

        //　手机端 API 管理
        $this->mapMobileApiRoutes();

        //　手机端 API 管理
        $this->mapCasinoApiRoutes();
    }

    /**
     * Api路由
     */
    protected function mapApiRoutes()
    {
        Route::prefix('web-api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * Mobile Api路由
     */
    protected function mapMobileApiRoutes()
    {
        Route::prefix('mobile-api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/mobile_api.php'));
    }

    /**
     * Admin 路由
     */
    protected function mapAdminRoutes()
    {
        Route::prefix('admin-api')
            ->middleware('admin.api')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin_api.php'));
    }

    /**
     * Partner 路由
     */
    protected function mapPartnerRoutes()
    {
        Route::prefix('partner-api')
            ->middleware('partner.api')
            ->namespace($this->namespace)
            ->group(base_path('routes/partner_api.php'));
    }

    /**
     * Partner 路由
     */
    protected function mapCasinoApiRoutes()
    {
        Route::prefix('casino-api')
            ->middleware('casino.api')
            ->namespace($this->namespace)
            ->group(base_path('routes/casino_api.php'));
    }
}
