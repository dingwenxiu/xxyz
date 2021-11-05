<?php
namespace App\Providers;

use App\Lib\Dcode;
use Illuminate\Support\ServiceProvider;

class DcodeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Dcode', function()
        {
            return new Dcode();
        });

    }
}