<?php

namespace App\Custom\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;

class ServiceProvider
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


            $module['tabs']['cham_cong_tab'] = [
                'label' => 'Cấu hình chấm công',
                'icon' => '<i class="flaticon2-time"></i>',
                'td' => [
                    ['name' => 'cham_cong_xa_toi_da', 'type' => 'number', 'label' => 'Khoảng cách chấm công tối đa (m)'],
                    ['name' => 'vp_lat', 'type' => 'text', 'label' => 'Toạ độ: Lat', 'des' => '<a href="https://prnt.sc/gS6VYa4_6-L1" target="_blank">Hướng dẫn lấy lat/long </a>'],
                    ['name' => 'vp_long', 'type' => 'text', 'label' => 'Toạ độ: Long'],
                ]
            ];
            $module['tabs']['gio_lam_tab'] = [
                'label' => 'Cấu hình thời gian chấm công',
                'icon' => '<i class="flaticon-time"></i>',
                'td' => [
                    ['name' => 'gio_lam_sang', 'type' => 'time', 'label' => 'Giờ vào làm sáng'],
                    ['name' => 'gio_lam_chieu', 'type' => 'time', 'label' => 'Giờ vào làm chiều'],

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
                'bao_cao_dan_khach_view', 'bao_cao_dan_khach_add', 'bao_cao_dan_khach_edit', 'bao_cao_dan_khach_delete',

            ]);
            return $per_check;
        }, 1, 1);
    }


    public function rendAsideMenu()
    {
        \Eventy::addFilter('aside_menu.dashboard_after', function () {
            print view('Custom.partials.aside_menu.menu_left');
        }, 1, 1);
    }
}
