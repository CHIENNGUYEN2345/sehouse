<?php

namespace Modules\EduMarketing\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\EduMarketing\Console\CampaignEmail;

class EduMarketingServiceProvider extends ServiceProvider
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
            //  Core
//        $this->registerTranslations();
//        $this->registerConfig();
//            $this->registerViews();
//        $this->registerFactories();
//        $this->loadMigrationsFrom(module_path('EduMarketing', 'Database/Migrations'));


            //  Custom setting
            $this->registerPermission();

            //  Cấu hình menu trái
            $this->rendAsideMenu();
        }
        $this->registerViews();

        $this->schedule();

        $this->commands([
            CampaignEmail::class
        ]);
    }

    public function schedule()
    {
        \Eventy::addAction('schedule.run', function ($schedule) {
            $cron = '* * * * *';
            $schedule->command('campaign:email')->cron($cron);
            return true;
        }, 1, 1);
    }

    public function registerPermission()
    {
        \Eventy::addFilter('permission.check', function ($per_check) {
            $per_check = array_merge($per_check, ['marketing-mail_view', 'customer', 'tag', 'email_template_view', 'email_account_view']);
            return $per_check;
        }, 1, 1);
    }

    public function rendAsideMenu() {
        \Eventy::addFilter('aside_menu.dashboard_after', function() {
            print view('edumarketing::partials.aside_menu.dashboard_after');
        }, 2, 1);

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
            module_path('EduMarketing', 'Config/config.php') => config_path('edumarketing.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('EduMarketing', 'Config/config.php'), 'edumarketing'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/edumarketing');

        $sourcePath = module_path('EduMarketing', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/edumarketing';
        }, \Config::get('view.paths')), [$sourcePath]), 'edumarketing');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/edumarketing');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'edumarketing');
        } else {
            $this->loadTranslationsFrom(module_path('EduMarketing', 'Resources/lang'), 'edumarketing');
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
            app(Factory::class)->load(module_path('EduMarketing', 'Database/factories'));
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
