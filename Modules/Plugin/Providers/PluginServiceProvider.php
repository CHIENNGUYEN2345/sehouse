<?php

namespace Modules\Plugin\Providers;

use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        //  Nếu là trang admin thì gọi các cấu hình
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
            //  Cấu hình core
            $this->registerTranslations();
            $this->registerConfig();
            $this->registerViews();
//        $this->registerFactories();
//        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        }
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
            __DIR__.'/../Config/config.php' => config_path('plugin.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'plugin'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/plugin');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/plugin';
        }, \Config::get('view.paths')), [$sourcePath]), 'plugin');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/plugin');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'plugin');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'plugin');
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
