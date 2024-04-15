<?php

namespace Modules\STBDAutoUpdatePriceWSS\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\STBDAutoUpdatePriceWSS\Console\UpdatePriceWss;

class STBDAutoUpdatePriceWSSServiceProvider extends ServiceProvider
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
            //  Cấu hình Core
//            $this->registerTranslations();
//            $this->registerConfig();
            $this->registerViews();
//            $this->registerFactories();
//            $this->loadMigrationsFrom(module_path('stbdautoupdatepricewss', 'Database/Migrations'));

            //  Cấu hình menu trái
            $this->rendAsideMenu();
        }


        //  Nếu là trang admin/setting thì gọi các cấu hình
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/setting') !== false) {
            $this->addSetting();
        }

        $this->commands($this->moreCommands);
    }

    public function addSetting()
    {
        \Eventy::addFilter('setting.custom_module', function ($module) {
            $module['tabs']['update_price_product_tab'] = [
                'label' => 'Tự động cập nhật giá SP',
                'icon' => '<i class="flaticon-mail"></i>',
                'intro' => '',
                'td' => [
                    ['name' => 'merchant_id', 'type' => 'text', 'label' => 'merchantId', 'des' => 'ID tài khoản WSS'],
                    ['name' => 'username', 'type' => 'text', 'class' => '', 'label' => 'Username WSS',],
                    ['name' => 'password', 'type' => 'text', 'class' => '', 'label' => 'Password WSS',],
                    ['name' => 'top', 'type' => 'number', 'label' => 'Lấy sản phẩm top mấy?',],
                    ['name' => 'discount', 'type' => 'text', 'class' => 'number-price', 'label' => 'Tiền giảm giá',],
                    ['name' => 'ignore', 'type' => 'textarea', 'class' => '', 'label' => 'Loại trừ các từ khóa', 'inner' => 'rows=15', 'des' => 'Mỗi từ viết trên 1 dòng'],
                    ['name' => 'link_error_plus_mn', 'type' => 'text', 'class' => 'number-price', 'label' => 'Cộng tiền cho link SP lỗi',],
                ]
            ];
            return $module;
        }, 1, 1);
    }

    protected $moreCommands = [
        UpdatePriceWss::class
    ];

    public function rendAsideMenu()
    {

        \Eventy::addFilter('aside_menu.dashboard_after', function () {
            print view('stbdautoupdatepricewss::partials.aside_menu.dashboard_after');
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
            __DIR__.'/../Config/config.php' => config_path('stbdautoupdatepricewss.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'stbdautoupdatepricewss'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/stbdautoupdatepricewss');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/stbdautoupdatepricewss';
        }, \Config::get('view.paths')), [$sourcePath]), 'stbdautoupdatepricewss');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/stbdautoupdatepricewss');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'stbdautoupdatepricewss');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'stbdautoupdatepricewss');
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
