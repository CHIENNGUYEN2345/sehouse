<?php

namespace Modules\Ticket\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class TicketServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('Ticket', 'Database/Migrations'));
        //  Nếu là trang admin thì gọi các cấu hình
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
            //  Custom setting

            //  Cấu hình menu trái
            $this->rendAsideMenu();

            //  Đăng ký quyền
            $this->registerPermission();
        }
    }

    //  Đăng ký quyền cho các module trong theme
    public function registerPermission()
    {
        \Eventy::addFilter('permission.check', function ($per_check) {
            $per_check = array_merge($per_check, ['ticket_view']);
            return $per_check;
        }, 1, 1);

    }

    //  Rend html ra menu  trái
    public function rendAsideMenu() {
        \Eventy::addFilter('aside_menu.dashboard_after', function() {
            print view('ticket::partials.aside_menu.dashboard_after_ticket');
        }, 100, 1);

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
            module_path('Ticket', 'Config/config.php') => config_path('ticket.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Ticket', 'Config/config.php'), 'ticket'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/ticket');

        $sourcePath = module_path('Ticket', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/ticket';
        }, \Config::get('view.paths')), [$sourcePath]), 'ticket');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/ticket');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'ticket');
        } else {
            $this->loadTranslationsFrom(module_path('Ticket', 'Resources/lang'), 'ticket');
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
            app(Factory::class)->load(module_path('Ticket', 'Database/factories'));
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
