<?php

namespace DD4You\Dpanel;

use DD4You\Dpanel\Console\InstallDpanel;
use DD4You\Dpanel\Http\Middleware\AccessDpanel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Routing\Router;

class DpanelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallDpanel::class,
            ]);
        }

        $this->app->make(Router::class)->aliasMiddleware('accessdpanel', AccessDpanel::class);

        # Register Routes Begin
        if (File::exists(base_path('routes/dpanel.php'))) {
            Route::prefix(config('dpanel.prefix'))
                ->name(config('dpanel.prefix') . '.')
                ->middleware(['web', 'auth', 'accessdpanel'])
                ->group(function () {
                    $this->loadRoutesFrom(base_path('routes/dpanel.php'));
                });
        }

        Route::prefix(config('dpanel.prefix'))
            ->name(config('dpanel.prefix') . '.')
            ->middleware('web', 'guest')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
            });

        # Register Routes End

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'dpanel');
        Blade::componentNamespace('DD4You\\Views\\Components', 'dpanel');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/assets/' => public_path('dd4you/dpanel/'),
            ], 'dpanel-asset');

            $this->publishes([
                __DIR__ . '/database/migrations/update_to_users_table.php.stub' => database_path('migrations/2014_10_12_200000_update_to_users_table.php'),
            ], 'migrations');

            $this->publishes([
                __DIR__ . '/database/seeders/PermissionSeeder.php.stub' => database_path('seeders/PermissionSeeder.php'),
            ], 'seeders');
        }
    }
    public function register()
    {
    }
}
