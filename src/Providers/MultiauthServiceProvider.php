<?php

namespace SMartins\PassportMultiauth\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Events\AccessTokenCreated;
use SMartins\PassportMultiauth\ProviderRepository;

class MultiauthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(ProviderRepository $providers)
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
        }

        $this->createAccessTokenProvider($providers);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/.database/migrations');

        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Create access token provider when access token is created.
     *
     * @return void
     */
    protected function createAccessTokenProvider(ProviderRepository $providers)
    {
        Event::listen(AccessTokenCreated::class, function ($event) use ($providers) {
            $provider = config('auth.guards.api.provider');

            $providers->create($event->tokenId, $provider);
        });
    }
}
