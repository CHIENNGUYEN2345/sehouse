<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\{Category, Banner, Post, Product, Widget, Setting};
use Illuminate\Http\Request;
use Modules\EduBill\Models\Bill;
use Modules\ThemeEdu\Models\Contact;
use Modules\ThemeEdu\Models\Order;

class HomeController extends Controller
{
    function GetHome()
    {

        return view(config('core.frontend_theme') . '.pages.home.index-magazine');
    }

    function GetFaqs()
    {
        return view(config('core.frontend_theme').'.pages.faqs');
    }

    function GetLanding()
    {
        return view(config('core.frontend_theme').'.pages.landing-4');
    }

    function GetMaintenance()
    {
        return view(config('core.frontend_theme').'.pages.maintenance');
    }

    function GetSearch(request $r)
    {
        $data['categorys']=Category::all();
        $data['products']=Product::where('name','like','%'.$r->q.'%')->paginate(12);
        $data['q']=$r->q;

        return view(config('core.frontend_theme').'.pages.product.search',$data);
    }

    public function cache_flush()
    {
        \Cache::flush();
        return redirect('/');
    }
}
