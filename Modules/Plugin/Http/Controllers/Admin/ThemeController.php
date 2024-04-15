<?php
namespace Modules\Plugin\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function getIndex(Request $request)
    {
        $modules_active = [];
        if ($request->has('status')) {
            $modules =\Module::getByStatus($request->status);
        } else {
            $modules = \Module::all();
            $modules_active = \Module::getByStatus(1);
        }

        //  Unset các module là theme
        foreach ($modules as $module_name => $module) {
            if (strpos($module_name, 'Theme') === false || $module_name == 'Theme') {
                unset($modules[$module_name]);
            }
        }

        $page_title = 'Gói mở rộng';
        return view('plugin::show', compact('modules', 'modules_active', 'page_title'));
    }

    public function active(Request $request) {
        $module = \Module::find(strtolower($request->name));
        if ($request->status == 1) {
            $module->enable();
        } else {
            $module->disable();
        }
        return response()->json([
            'status' => true,
            'msg' => 'Cập nhật thành công!',
            'data' =>  $request->status
        ]);
    }
}
