<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use App\Models\District;
use App\Models\Ward;
use Auth;
use DB;
use Illuminate\Http\Request;
use Mail;

class DashboardController extends Controller
{
    public function getIndex()
    {
       dd(1);
    }

    public function changeTheme(Request $request)
    {
        \Cookie::queue('admin_theme_style', $request->style, 129600);
        CommonHelper::one_time_message('success', 'Đã đổi giao diện!');
        return back();
    }

    public function tooltipInfo(Request $request)
    {
        $modal = new $request->modal;
        $data['item'] = $modal->find($request->id);
        $data['tooltip_info'] = $request->tooltip_info;

        return view(config('core.admin_theme') . '.partials.modal.tooltip_info', $data);
    }

    public function ajax_up_file(Request $request)
    {
        if ($request->has('file')) {
            $fileRequest = $request->file;
            if (in_array($fileRequest->getClientOriginalExtension(), ['jpg', 'png', 'gif', 'jpeg'])) {
                $path = 'upload/' . date('Y/m/d_His');

                $base_path = public_path() . '/filemanager/userfiles/';
                $dir_name = $base_path . $path;
                if (!is_dir($dir_name)) {
                    // Tạo thư mục của chúng tôi nếu nó không tồn tại
                    mkdir($dir_name, 0777, true);
                }
                if (is_dir($dir_name)) {
                    $file = CommonHelper::saveFile($request->file('file'), $path);
                    return response()->json([
                        'status' => true,
                        'file' => '/filemanager/userfiles/' . $file,
                        'value' => $file,
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Sai định dạng ảnh, vui lòng chọn lại',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'msg' => 'Không tồn tại file. xin vui  lòng thử lại',
            ]);
        }
    }

    public function ajax_up_file2(Request $request)
    {
        if ($request->has('file')) {
            if (is_array($request->file)) {
                foreach ($request->file as $key => $fileRequest) {
                    if (in_array($fileRequest->getClientOriginalExtension(), ['jpg', 'png', 'gif', 'jpeg'])) {
                        $path = 'upload/' . date('Y/m/d');
                        $base_path = public_path() . '/filemanager/userfiles/';
                        $dir_name = $base_path . $path;
                        if (!is_dir($dir_name)) {
                            // Tạo thư mục của chúng tôi nếu nó không tồn tại
                            mkdir($dir_name, 0777, true);
                        }
                        if (is_dir($dir_name)) {
                            $file = CommonHelper::saveFile($fileRequest, $path);
                            $img[] = [
                                'status' => true,
                                'file' => '/public/filemanager/userfiles/' . $file,
                                'value' => $file,
                                'msg' => 'Thành công !',
                            ];
                        }
                    } else {
                        $img[] = [
                            'status' => false,
                            'msg' => 'Không đúng định dạng !',
                        ];
                    }
                }
            } else {
                $fileRequest = $request->file;
                if (in_array($fileRequest->getClientOriginalExtension(), ['jpg', 'png', 'gif', 'jpeg'])) {
                    $path = 'upload/' . date('Y/m/d');
                    $base_path = public_path() . '/filemanager/userfiles/';
                    $dir_name = $base_path . $path;
                    if (!is_dir($dir_name)) {
                        // Tạo thư mục của chúng tôi nếu nó không tồn tại
                        mkdir($dir_name, 0777, true);
                    }
                    if (is_dir($dir_name)) {
                        $file = CommonHelper::saveFile($fileRequest, $path);
                        $img[] = [
                            'status' => true,
                            'file' => '/public/filemanager/userfiles/' . $file,
                            'value' => $file,
                            'msg' => 'Thành công !',
                        ];
                    }
                } else {
                    $img[] = [
                        'status' => false,
                        'msg' => 'Không đúng định dạng !',
                    ];
                }
            }

            return response()->json([
                'data' => $img
            ]);
        }
    }

    public function getDataLocation(Request $r, $table) {
        if ($table == 'districts') {
            $items = District::where('province_id', $r->province_id)->pluck('name', 'id');
        } elseif ($table == 'wards') {
            $items = Ward::where('district_id', $r->district_id)->pluck('name', 'id');
        }
        return response()->json([
            'status' => true,
            'msg' => '',
            'data' => $items
        ]);
    }


}
