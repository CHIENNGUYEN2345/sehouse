<?php

namespace Modules\LandingPage\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\LandingPage\Console\Tool;

class LandingPageServiceProvider extends ServiceProvider
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

//        $this->registerFactories();
//        $this->loadMigrationsFrom(module_path('LandingPage', 'Database/Migrations'));

        //  Nếu là trang admin thì gọi các cấu hình
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
            //  Custom setting
            $this->registerPermission();
            $this->registerViews();
            $this->rendAsideMenu();
        }

        $this->commands([
            Tool::class
        ]);
    }

    public function registerPermission()
    {
        \Eventy::addFilter('permission.check', function ($per_check) {
            $per_check = array_merge($per_check, ['landingpage_view', 'landingpage_add', 'landingpage_edit', 'landingpage_delete', 'landingpage_publish',]);
            return $per_check;
        }, 1, 1);
    }

    public function rendAsideMenu() {
        \Eventy::addFilter('aside_menu.dashboard_after', function() {
            print view('landingpage::partials.aside_menu.dashboard_after');
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
            module_path('LandingPage', 'Config/config.php') => config_path('landingpage.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('LandingPage', 'Config/config.php'), 'landingpage'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/landingpage');

        $sourcePath = module_path('LandingPage', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/landingpage';
        }, \Config::get('view.paths')), [$sourcePath]), 'landingpage');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/landingpage');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'landingpage');
        } else {
            $this->loadTranslationsFrom(module_path('LandingPage', 'Resources/lang'), 'landingpage');
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
            app(Factory::class)->load(module_path('LandingPage', 'Database/factories'));
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
