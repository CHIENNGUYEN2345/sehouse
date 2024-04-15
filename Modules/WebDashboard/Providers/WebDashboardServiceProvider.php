<?php

namespace Modules\WebDashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class WebDashboardServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
//        $this->registerTranslations();
//        $this->registerConfig();
        $this->registerViews();
//        $this->registerFactories();
//        $this->loadMigrationsFrom(module_path('WebDashboard', 'Database/Migrations'));

        //  Nếu là trang admin thì gọi các cấu hình
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
            //  Custom setting
            $this->registerPermission();

        }
    }

    public function registerPermission()
    {
        \Eventy::addFilter('permission.check', function ($per_check) {
            $per_check = array_merge($per_check, ['dashboard']);
            return $per_check;
        }, 1, 1);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('WebDashboard', 'Config/config.php') => config_path('webdashboard.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('WebDashboard', 'Config/config.php'), 'webdashboard'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/webdashboard');

        $sourcePath = module_path('WebDashboard', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/webdashboard';
        }, \Config::get('view.paths')), [$sourcePath]), 'webdashboard');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/webdashboard');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'webdashboard');
        } else {
            $this->loadTranslationsFrom(module_path('WebDashboard', 'Resources/lang'), 'webdashboard');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('WebDashboard', 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
