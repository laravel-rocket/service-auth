<?php
namespace LaravelRocket\ServiceAuthentication\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LaravelRocket\ServiceAuthentication\Console\Commands\AuthGeneratorCommand;
use LaravelRocket\ServiceAuthentication\Console\Commands\ServiceGeneratorCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->app->singleton('command.rocket.make.service-auth-base', function ($app) {
            return new AuthGeneratorCommand($app['config'], $app['files'], $app['view']);
        });

        $this->app->singleton('command.rocket.make.service-auth-service', function ($app) {
            return new ServiceGeneratorCommand($app['config'], $app['files'], $app['view']);
        });

        $this->commands('command.rocket.make.service-auth-base', 'command.rocket.make.service-auth-service');
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }
}
