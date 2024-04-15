<?php

namespace Modules\CheckErrorLink\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\CheckErrorLink\Console\ScanErrorLink;
use Modules\EworkingCompany\Http\Controllers\Admin\MailController;
use Modules\EworkingService\Models\Service;
use Nwidart\Modules\Module;

class CheckErrorLinkServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->addSetting();

        $this->commands($this->moreCommands);
        $this->rendAsideMenu();
        $this->registerPermission();
        $this->schedule();
    }

    public function schedule() {
        \Eventy::addAction('schedule.run', function ($schedule) {
            $settings = Setting::where('type', 'scan_error')->pluck('value', 'name')->toArray();
            if ($settings['scan_status'] == 1) {
                $cron = @$settings['minute_scan'] . ' ' . @$settings['hour_scan'] . ' ' . @$settings['day_in_month_scan'] . ' ' . @$settings['month_scan'] . ' ' . @$settings['day_in_week_scan'];
                $schedule->command('link:check')->cron($cron);
            }
            return true;
        }, 1, 1);
    }

    protected $moreCommands = [
        ScanErrorLink::class
    ];

    public function rendAsideMenu()
    {
        \Eventy::addFilter('aside_menu.dashboard_after', function () {
            print view('checkerrorlink::partials.aside_menu.aside_menu_dashboard_after');
        }, 2, 1);
    }
    public function registerPermission()
    {
        \Eventy::addFilter('permission.check', function ($per_check) {
            $per_check = array_merge($per_check, ['super_admin']);
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
            __DIR__ . '/../Config/config.php' => config_path('checkerrorlink.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'checkerrorlink'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/checkerrorlink');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/checkerrorlink';
        }, \Config::get('view.paths')), [$sourcePath]), 'checkerrorlink');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/checkerrorlink');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'checkerrorlink');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'checkerrorlink');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    public function addSetting()
    {
        \Eventy::addFilter('setting.custom_module', function ($module) {
            $module['tabs']['scan_error'] = [
                'label' => 'Quét lỗi link tự động',
                'icon' => '<i class="flaticon2-time"></i>',
                'td' => [
                    ['name' => 'inner', 'type' => 'inner', 'label' => '', 'html' => '<p>Cấu hình lịch quét tự động</p>'],
                    ['name' => 'scan_status', 'type' => 'checkbox', 'label' => 'Kích hoạt'],
                    ['name' => 'minute_scan', 'type' => 'text', 'label' => 'Phút (0-59) tương ứng với số từ (0-59)', 'des' => 'Nhập vào số phút, có thể nhập vào 2 giá trị các nhau bởi dấu phảy :<br> Ví dụ ( phút 20 và tháng 50 ) : 20, 50'],
                    ['name' => 'hour_scan', 'type' => 'text', 'label' => 'Giờ (0-23) tương ứng với số từ (0-23)', 'des' => 'Nhập vào số tháng, có thể nhập vào 2 giá trị các nhau bởi dấu phảy'],
                    ['name' => 'day_in_month_scan', 'type' => 'text', 'label' => 'Ngày trong tháng (1-31) tương ứng với số từ (1-31)', 'des' => 'Nhập vào số ngày trong tháng, có thể nhập vào 2 giá trị các nhau bởi dấu phảy'],
                    ['name' => 'month_scan', 'type' => 'text', 'label' => 'Tháng (1-12) tương ứng với số từ (1-12)', 'des' => 'Nhập vào số tháng, có thể nhập vào 2 giá trị các nhau bởi dấu phảy'],
                    ['name' => 'day_in_week_scan', 'type' => 'text', 'label' => 'Thứ trong tuần ( thứ 2 -> Chủ nhật tương ứng với số từ 0 -> 7)', 'des' => 'Nhập vào số giờ, có thể nhập vào 2 giá trị các nhau bởi dấu phảy (chủ nhật = 0 or 7)'],
                ]
            ];
            return $module;
        }, 1, 1);
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
