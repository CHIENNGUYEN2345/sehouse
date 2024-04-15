<?php

namespace App\CoreERP\Providers;

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

            //  Cấu hình menu trái
            $this->rendAsideMenu();
        }

    }

    public function rendAsideMenu()
    {
        \Eventy::addFilter('aside_menu.dashboard_after', function () {
            print view('CoreERP.partials.aside_menu.menu_left');
        }, 2, 1);
    }
}
