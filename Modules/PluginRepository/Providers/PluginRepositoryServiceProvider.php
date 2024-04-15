<?php

namespace Modules\PluginRepository\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class PluginRepositoryServiceProvider extends ServiceProvider
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
//        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        //  Nếu là trang admin thì gọi các cấu hình
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
            //  Custom setting
            $this->registerPermission();

            //  Cấu hình menu trái
            $this->rendAsideMenu();
        }
    }
    public function registerPermission()
    {
        \Eventy::addFilter('permission.check', function ($per_check) {
            $per_check = array_merge($per_check, ['plugin_repository_view', 'plugin_repository_add', 'plugin_repository_edit', 'plugin_repository_delete', 'plugin_repository_publish',]);
            return $per_check;
        }, 1, 1);
    }


    public function rendAsideMenu()
    {
        \Eventy::addFilter('aside_menu.dashboard_after', function () {
            print view('pluginrepository::partials.aside_menu.dashboard_after_plugin_repository');
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
            __DIR__.'/../Config/config.php' => config_path('pluginrepository.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'pluginrepository'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/pluginrepository');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/pluginrepository';
        }, \Config::get('view.paths')), [$sourcePath]), 'pluginrepository');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/pluginrepository');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'pluginrepository');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'pluginrepository');
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
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
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
