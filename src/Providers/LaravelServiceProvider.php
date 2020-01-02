<?php


namespace GzhPackages\JsonApi\Providers;


use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $path = realpath(__DIR__.'/../../config/config.php');
        $this->publishes([$path => config_path('errors.php')], 'config');
        $this->mergeConfigFrom($path, 'errors');
    }

    public function register()
    {
        $this->commands('GzhPackages\JsonApi\Console\MakeApiException');
    }
}