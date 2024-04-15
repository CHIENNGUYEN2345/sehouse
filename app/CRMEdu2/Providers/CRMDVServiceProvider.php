<?php

namespace App\CRMEdu\Providers;

use App\Models\Setting;
use App\CRMEdu\Console\ScanErrorLink;
use Illuminate\Support\Facades\View;


class CRMEduServiceProvider
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

            //  Xoa menu khách hàng
            \Eventy::addFilter('aside_menu.user', function () {
                return '';
            }, 2, 1);
        }


        //  Setting Custom
//        $this->schedule();
//        $this->commands($this->moreCommands);

    }

    protected $moreCommands = [
//        ServiceRenewal::class,
        ScanErrorLink::class,
//        CrawlWeb::class,
    ];

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
                'receipt_payment_view',
                'dhbill_view', 'dhbill_edit', 
                // 'service_view', 'service_add', 'service_edit', 'service_delete',
                'codes_view', 'codes_add', 'codes_edit', 'codes_delete',
                'dashboard','remind',
                'bill_histories_delete',
                'lead_view', 'lead_add', 'lead_edit', 'lead_delete', 'lead_assign', 'lead_float_view', 'lead_import',
                'mktlead_view',
                'timekeeping_view', 'timekeeping_add', 'timekeeping_edit', 'timekeeping_delete',
                'course', 'course_view',
                'document', 'document_view',
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
            print view('CRMEdu.partials.aside_menu.dashboard_after_bill');
        }, 1, 1);
    }
}
