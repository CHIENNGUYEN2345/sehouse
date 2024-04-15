<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class CacheController extends Controller
{

    protected $module = [
        'code' => 'cache',
        'label' => 'Cache',
    ];

    public function getIndex(Request $request)
    {
        $data['module'] = $this->module;
        $data['page_type'] = 'list';
        $data['page_title'] = 'Cache';
        return view(config('core.admin_theme') . '.cache.view')->with($data);
    }
}
