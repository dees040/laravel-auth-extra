<?php

namespace dees040\AuthExtra;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $events = [
        'Illuminate\Auth\Events\Registered' => [
            Listeners\LogRegisteredUser::class,
        ],
        'Illuminate\Auth\Events\Login' => [
            Listeners\LogSuccessfulLogin::class,
        ],
        'Illuminate\Auth\Events\Failed' => [
            Listeners\LogFailedLogin::class,
        ],

    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/auth_extra.php' => config_path('auth_extra.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__.'/../migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes.php');

        $this->listenForAuthEvents();
    }

    /**
     * Register the events and their listeners.
     *
     * @return void
     */
    private function listenForAuthEvents()
    {
        $event = $this->app['events'];

        foreach ($this->events as $for => $listeners) {
            foreach ($listeners as $with) {
                $event->listen($for, $with);
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAuthManager();
    }

    /**
     * Register the Auth Manager.
     *
     * @return void
     */
    private function registerAuthManager()
    {
        $manager = $this->app->make(\dees040\AuthExtra\AuthManager::class);

        $this->app->singleton('dees040\AuthExtra\AuthManager', function ($app) use ($manager) {
            return $manager;
        });
    }
}
