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
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'rocket-service-authentication-migrations');
        }

        $this->app->singleton('command.rocket.make.auth', function ($app) {
            return new AuthGeneratorCommand($app['config'], $app['files'], $app['view']);
        });

        $this->app->singleton('command.rocket.make.service', function ($app) {
            return new ServiceGeneratorCommand($app['config'], $app['files'], $app['view']);
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }
}
