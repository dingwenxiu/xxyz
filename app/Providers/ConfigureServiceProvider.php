<?php
namespace App\Providers;

use App\Lib\Configure;
use Illuminate\Support\ServiceProvider;

class ConfigureServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->singleton('Configure', function()
        {
            return new Configure();
        });
    }
}