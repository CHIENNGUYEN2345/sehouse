<?php

namespace App\Custom\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;

class CustomServiceProvider
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
            //  Custom setting
            $this->registerPermission();

            //  Cấu hình menu trái
            $this->rendAsideMenu();
        }

        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/setting') !== false) {

            //  Cấu hình chạy tự động gửi mail dịch vụ
            $this->addSetting();
        }

        //  Setting Custom
//        $this->schedule();
//        $this->commands($this->moreCommands);

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

    public function registerPermission()
    {
        \Eventy::addFilter('permission.check', function ($per_check) {
            $per_check = array_merge($per_check, [
                'landingpage_view', 'landingpage_add', 'landingpage_edit', 'landingpage_delete', 'landingpage_publish',
                ]);
            return $per_check;
        }, 1, 1);
    }


    public function rendAsideMenu()
    {
        \Eventy::addFilter('aside_menu.dashboard_after', function () {
            print view('custom.partials.aside_menu.dashboard_after_bill');
        }, 1, 1);
    }
}
