<?php

namespace Modules\WebBill\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\WebBill\Console\CrawlWeb;
use Modules\WebBill\Console\ScanErrorLink;
use Modules\WebBill\Console\ServiceRenewal;

class WebBillServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
//        $this->registerTranslations();
        $this->registerViews();
//        $this->registerFactories();
//        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerConfig();

        //  Nếu là trang admin thì gọi các cấu hình
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
            //  Custom setting
            $this->registerPermission();

            //  Cấu hình menu trái
            $this->rendAsideMenu();

            //  Xoa menu khách hàng
            \Eventy::addFilter('aside_menu.user', function () {
                return '';
            }, 2, 1);
        }

        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/setting') !== false) {

            //  Cấu hình chạy tự động gửi mail dịch vụ
            $this->addSetting();
        }

        //  Setting Custom
//        $this->schedule();
        $this->commands($this->moreCommands);
    }

    protected $moreCommands = [
        ServiceRenewal::class,
        ScanErrorLink::class,
        CrawlWeb::class,
    ];

    public function addSetting()
    {
        \Eventy::addFilter('setting.custom_module', function ($module) {
            $module['tabs']['web_service'] = [
                'label' => 'Dịch vụ web',
                'icon' => '<i class="flaticon2-time"></i>',
                'td' => [
                    ['name' => 'inner', 'type' => 'inner', 'label' => '', 'html' => '====|Quá|======|Bây giờ|=====|Sát|======|Sắp đến|======>'],
                    ['name' => 'min_day', 'type' => 'number', 'label' => 'Số ngày thông báo quá kì hạn'],
                    ['name' => 'close_day', 'type' => 'number', 'label' => 'Số ngày thông báo sát kì hạn'],
                    ['name' => 'max_day', 'type' => 'number', 'label' => 'Số ngày thông báo sắp đến kì hạn'],
                    ['name' => 'inner', 'type' => 'inner', 'label' => '', 'html' => '<b>Cấu hình gia hạn tự động</b>'],
                    ['name' => 'status', 'type' => 'checkbox', 'label' => 'Kích hoạt gửi thông báo tự động'],
                    ['name' => 'minute_scan', 'type' => 'text', 'label' => 'Phút (0-59) tương ứng với số từ (0-59)', 'des' => 'Nhập vào số phút, có thể nhập vào 2 giá trị các nhau bởi dấu phảy :<br> Ví dụ ( phút 20 và tháng 50 ) : 20, 50'],
                    ['name' => 'hour_scan', 'type' => 'text', 'label' => 'Giờ (0-23) tương ứng với số từ (0-23)', 'des' => 'Nhập vào số tháng, có thể nhập vào 2 giá trị các nhau bởi dấu phảy'],
                    ['name' => 'day_in_month_scan', 'type' => 'text', 'label' => 'Ngày trong tháng (1-31) tương ứng với số từ (1-31)', 'des' => 'Nhập vào số ngày trong tháng, có thể nhập vào 2 giá trị các nhau bởi dấu phảy'],
                    ['name' => 'month_scan', 'type' => 'text', 'label' => 'Tháng (1-12) tương ứng với số từ (1-12)', 'des' => 'Nhập vào số tháng, có thể nhập vào 2 giá trị các nhau bởi dấu phảy'],
                    ['name' => 'day_in_week_scan', 'type' => 'text', 'label' => 'Thứ trong tuần ( thứ 2 -> Chủ nhật tương ứng với số từ 0 -> 7)', 'des' => 'Nhập vào số giờ, có thể nhập vào 2 giá trị các nhau bởi dấu phảy (chủ nhật = 0 or 7)'],
                ]
            ];

            $module['tabs']['check_link_error'] = [
                'label' => 'Kiểm tra lỗi web tự động',
                'icon' => '<i class="flaticon2-time"></i>',
                'td' => [
                    ['name' => 'links_direct', 'type' => 'textarea', 'class' => 'form-action', 'label' => 'Danh sách tên miền chuyển hướng', 'inner' => 'rows=15'],
                    ['name' => 'links', 'type' => 'textarea', 'class' => 'form-action', 'label' => 'DS Link theo dõi thêm', 'inner' => 'rows=15'],
                ]
            ];
            return $module;
        }, 1, 1);
    }

    public function schedule()
    {
        \Eventy::addAction('schedule.run', function ($schedule) {
            $settings = Setting::where('type', 'web_service')->pluck('value', 'name')->toArray();
            if ($settings['status'] == 1) {
                $cron = @$settings['minute_scan'] . ' ' . @$settings['hour_scan'] . ' ' . @$settings['day_in_month_scan'] . ' ' . @$settings['month_scan'] . ' ' . @$settings['day_in_week_scan'];
                $schedule->command('services:run')->cron($cron);
            }
            return true;
        }, 1, 1);
    }

    public function registerPermission()
    {
        \Eventy::addFilter('permission.check', function ($per_check) {
            $per_check = array_merge($per_check, [
                'truong_phong',
                'timekeeper_view', 'timekeeper_edit',
                'bill_view', 'bill_add', 'bill_edit', 'bill_delete', 'bill_publish',
                'dhbill_view', 'dhbill_edit', 
                // 'service_view', 'service_add', 'service_edit', 'service_delete',
                'codes_view', 'codes_add', 'codes_edit', 'codes_delete',
                'rooms_view', 'rooms_add', 'rooms_edit', 'rooms_delete',
                'dashboard','remind',
                'bill_histories_delete',
                'lead_view', 'lead_add', 'lead_edit', 'lead_delete', 'lead_assign', 'lead_float_view', 'lead_import',
                'mktlead_view',
                'timekeeping_view', 'timekeeping_add', 'timekeeping_edit', 'timekeeping_delete',
                'course_view',
                'cskh-bill_view', 'cskh-bill_edit',
                'plan_view', 'plan_add', 'plan_edit', 'plan_delete',
                'hradmin_view', 'hradmin_add', 'hradmin_edit', 'hradmin_publish',
                'penalty_ticket',
                'check_error_link_logs',
                'receipts_publish',
                ]);
            return $per_check;
        }, 1, 1);
    }


    public function rendAsideMenu()
    {
        \Eventy::addFilter('aside_menu.dashboard_after', function () {
            print view('webbill::partials.aside_menu.dashboard_after_bill');
        }, 1, 1);

        \Eventy::addFilter('aside_menu.dashboard_after', function () {
            print view('webbill::partials.aside_menu.dashboard_after_user');
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
            __DIR__.'/../Config/config.php' => config_path('webbill.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'webbill'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/webbill');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/webbill';
        }, \Config::get('view.paths')), [$sourcePath]), 'webbill');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/webbill');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'webbill');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'webbill');
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
