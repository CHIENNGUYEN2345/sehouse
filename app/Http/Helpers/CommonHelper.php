<?php

namespace App\Http\Helpers;

use App\Models\RoleAdmin;
use App\Models\Setting;
use Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Session;
use View;


class CommonHelper
{

    function __construct()
    {
        setlocale(LC_ALL, 'vi_VN.UTF8');
    }
    public static function getShippingMethod($transport_type, $package_no, $so_km, $type_quantity,$quantity ){
        $shippingMethodObj = \Modules\ThemeLogistics\Models\ShippingMethod::where('transport_type_id', $transport_type)
            ->where('transport_product_type_id', $package_no)
            ->where('range_from', '<=', $so_km)
            ->where('range_to', '>=', $so_km)
            ->where('type_calculation', $type_quantity)
            ->where('amount_from','<=', $quantity)
            ->where('amount_to','>=', $quantity)->get();
        return $shippingMethodObj;
    }
    public static function renderShippingMethod($data){

    }
    public static function randomCode($length)
    {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public static function one_time_message($class, $message)
    {

        Session::flash('alert-class', $class);
        Session::flash('message', $message);
    }

    public static function has_permission($admin_id, $per_check = '')
    {
        try {
            if (is_object(\Auth::guard('api')->user())) {
                $company_id = @\Auth::guard('api')->user()->last_company_id;
            } else {
                $company_id = @\Auth::guard('admin')->user()->last_company_id;
            }

            $key_cache = is_array($per_check) ? implode('|', $per_check) . '|' : '';
            $key_cache = 'permission_role_has_per' . $admin_id . $key_cache;
            $key_cache = md5($key_cache);

            $permissions = \DB::table('permissions')
                ->join('permission_role', 'permission_role.permission_id', '=', 'permissions.id')
                ->join('role_admin', 'permission_role.role_id', '=', 'role_admin.role_id')
                ->whereIn('permissions.name', is_array($per_check) ? array_merge($per_check, ['super_admin']) : [$per_check, 'super_admin'])
                ->where(function ($query) use ($company_id) {
                    $query->orWhere('role_admin.company_id', $company_id);
                    $query->orWhereNull('role_admin.company_id');
                })
                ->where('role_admin.admin_id', $admin_id)
                ->pluck('permissions.name')->toArray();
            CommonHelper::putToCache($key_cache, $permissions, ['roles']);

            if (is_string($per_check)) {
                if (empty($permissions)) {
                    return false;
                } else {
                    return true;
                }
            }

            return array_unique($permissions);
        } catch (\Exception $ex) {
            dd($admin_id, $per_check, $ex->getMessage(), $ex->getLine(), $key_cache);
            return false;
        }
    }

    public static function saveFile($file, $path)
    {
        $base_path = str_replace('/public', '/public_html', public_path()) . '/filemanager/userfiles/';
        $dir_name = $base_path . $path;

        if (!is_dir($dir_name)) {
            // Tạo thư mục của chúng tôi nếu nó không tồn tại
            mkdir($dir_name, 0755, true);
        }
        if (is_string($file)) {
            $name = explode('.', $file);
            $file_name = explode('/', $file)[count(explode('/', $file)) - 1];
            $file_name_insert = str_slug(str_replace(end($name), '', $file_name), '-') . '.' . end($name);
            try {
                $v = file_get_contents($file);
            } catch (\Exception $ex) {
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );
                $v = file_get_contents($file, false, stream_context_create($arrContextOptions));
            }
            file_put_contents(base_path() . '/public_html/filemanager/userfiles/' . $path . '/' . $file_name_insert, $v);
            return $path . '/' . $file_name_insert;
        } else {
            # dd(base_path() . '/public_html/filemanager/userfiles/' . $path);
            $file_name = $file->getClientOriginalName();
            try {
                if ($file->getSize() > 10920510) {
                    dd('Ảnh yêu cầu nhỏ hơn 10Mb');
                }
            } catch(Exception $ex) {
                echo $ex->getMessage();
            }

            $name = explode('.', $file_name);
            $file_name_insert = str_slug(str_replace(end($name), '', $file_name), '-') . '.' . end($name);

            $file->move(base_path() . '/public_html/filemanager/userfiles/' . $path, $file_name_insert);
            // dd($path . '/' . $file_name_insert);
            return $path . '/' . $file_name_insert;
        }
    }

    public static function getUrlImageThumb($file, $width = null, $height = null)
    {
        /*if (@env(OFF_URL_IMAGE_THUMB) != null) {
            return $file;
        }*/
        try {
            $file_mime = explode('.', $file)[count(explode('.', $file)) - 1];
            $file_thumb = '/public_html/filemanager/userfiles/_thumbs/' . str_replace('.' . $file_mime, '', $file) . '-' . $width . 'x' . $height . '.' . $file_mime;

            //  Nếu đã tồn tại file thumb thì trả về luôn
            if (file_exists(base_path() . $file_thumb)) {
                return \URL::asset(str_replace(' ', '%20', str_replace('public_html/', '', $file_thumb)));
            }

            //  Nếu ko xác định width - height thì trả về ảnh gốc
            if ($width == null && $height == null) {
                return \URL::asset('filemanager/userfiles/' . $file);
            }

            //  Nếu không tồn tại file thumb thì cắt ảnh

            $path_file = base_path() . '/public_html/filemanager/userfiles/' . $file;
            if ($file != '' && file_exists($path_file)) {
                $file_name = explode('/', $file)[count(explode('/', $file)) - 1];
                $folder_path = base_path() . '/public_html/filemanager/userfiles/_thumbs/' . str_replace('/' . $file_name, '', $file);
                if (!is_dir($folder_path)) {    //  Tạo folder nếu không tồn tại
                    mkdir($folder_path, 0755, true);
                }
                try {
                    \Image::make($path_file)->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save(base_path() . '/' . $file_thumb);
                } catch (\Exception $ex) {
                    return self::getLogoUrl();
                }
                return \URL::asset(str_replace(' ', '%20', str_replace('public_html/', '', $file_thumb)));
            }
            return self::getLogoUrl();
        } catch (\Exception $ex) {
            return self::getLogoUrl();
        }


        //  USE timthumb.php
        /*$height = ($height != false && $height != 'auto') ? "&h=" . $height : "";
        $width = ($width != false && $width != 'auto') ? "&w=" . $width : "";
        try {
            if ($file != '' && file_exists(base_path() . '/public_html/filemanager/userfiles/' . $file)) {
                $file_exist = true;
            } else {
                $file_exist = false;
            }
            if ($file_exist) {
                if ($width == false && $height == false) {
                    return \URL::asset('filemanager/userfiles/' . $file);
                }
                return url("/timthumb.php?src=" . \URL::asset('filemanager/userfiles/' . $file) . $width . $height);
            }
            return url("/timthumb.php?src=" . \URL::asset('filemanager/userfiles/logo.png') . $width . $height);
        } catch (Exception $ex) {
            return url("/timthumb.php?src=" . \URL::asset('filemanager/userfiles/logo.png') . $width . $height);
        }*/
    }

    public static function getLogoUrl() {
        if (file_exists(base_path() . '/public_html/filemanager/userfiles/image_default/no-image.png')) {
            return \URL::asset('/filemanager/userfiles/image_default/no-image.png');
        }

        $settings = CommonHelper::getFromCache('settings');
        if (!$settings) {
            $settings = Setting::whereIn('type', ['general_tab'])->pluck('value', 'name')->toArray();
            CommonHelper::putToCache('settings', $settings);
        }
        $path_file = base_path() . '/public_html/filemanager/userfiles/' . $settings['logo'];
        $file_mime = explode('.', $settings['logo'])[count(explode('.', $settings['logo'])) - 1];
        $file_thumb = '/filemanager/userfiles/_thumbs/' . str_replace('.' . $file_mime, '', $settings['logo']) . '-' . 300 . 'x' . '' . '.' . $file_mime;
        try {
            \Image::make($path_file)->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save(base_path() . '/' . $file_thumb);
            return \URL::asset(str_replace(' ', '%20', $file_thumb));
        } catch (\Exception $ex) {
            return asset('/filemanager/userfiles/' . $settings['logo']);
        }
    }

    public static function getUrlImageThumbHasDomain($file, $width = null, $height = null, $domain = false) {
        if (!$domain) {
            $domain = @$_SERVER['HTTP_HOST'];
        }
        $file_mime = explode('.', $file)[count(explode('.', $file)) - 1];
        $file_thumb = '/filemanager/userfiles/_thumbs/' . str_replace('.' . $file_mime, '', $file) . '-' . $width . 'x' . $height . '.' . $file_mime;
        if (file_exists(base_path() . $file_thumb)) {   //  Nếu đã tồn tại file thumb thì trả về luôn
            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $domain . "/" . str_replace(' ', '%20', $file_thumb);
        }

        //  Nếu không tồn tại file thumb thì cắt ảnh
        $path_file = base_path() . '/public_html/filemanager/userfiles/' . $file;
        if ($file != '' && file_exists($path_file)) {
            $file_name = explode('/', $file)[count(explode('/', $file)) - 1];
            $folder_path = base_path() . '/public_html/filemanager/userfiles/_thumbs/' . str_replace('/' . $file_name, '', $file);
            if (!is_dir($folder_path)) {    //  Tạo folder nếu không tồn tại
                mkdir($folder_path, 0755, true);
            }
            try {
                \Image::make($path_file)->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                })->save(base_path() . '/' . $file_thumb);
            } catch (\Exception $ex) {
                return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $domain . "/" . 'filemanager/userfiles/logo.png';
            }
            return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $domain . "/" . str_replace(' ', '%20', $file_thumb);
        }
        return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $domain . "/" . 'filemanager/userfiles/logo.png';
    }

    public static function formatTimePast($date)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $day = date('Y-m-d H:i:s');
        $result = ceil((strtotime($day) - strtotime($date)) / 60);
        if ($result > 1400 * 30 * 12) {
            return 'Khoảng ' . floor($result / (1400 * 30 * 12)) . ' năm trước';
        } else if ($result > 1400 * 30) {
            return 'Khoảng ' . floor($result / (1400 * 30)) . ' tháng trước ';
        } else if ($result > 1400) {
            return 'Khoảng ' . floor($result / 1400) . ' ngày trước';
        } else if ($result > 60) {
            return 'Khoảng ' . floor($result / 60) . ' giờ trước';
        } else if ($result < 60) {
            $result = $result < 1 ? 0 : $result;
            return 'Khoảng ' . $result . ' phút trước';
        }
    }

    public static function formatTimeDay($date)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $date1 = date('Y-m-d', strtotime($date));
        $day = date('Y-m-d');
        $result = floor(abs(strtotime($date1) - strtotime($day)) / (60 * 60 * 24));
        if ($result == 2) {
            return date('H:i', strtotime($date)) . ' - Ngày kia';
        } else if ($result == 1) {
            return date('H:i', strtotime($date)) . ' - Ngày mai';
        } else if ($result == 0) {
            return date('H:i', strtotime($date)) . ' - Hôm nay';
        } else if ($result == -1) {
            return date('H:i', strtotime($date)) . ' - Hôm qua';
        } else if ($result == -2) {
            return date('H:i', strtotime($date)) . ' - Hôm kia';
        } else {
            $result = empty($date) ? '' : date('H:i - d/m/Y', strtotime($date));
            return $result;
        }
    }

    public static function getRoleName($admin_id, $field = 'display_name')
    {
        $role_name = CommonHelper::getFromCache('roles_role_name' . $admin_id . $field, ['roles', 'admin']);
        if (!$role_name) {
            $role = RoleAdmin::join('roles', 'roles.id', '=', 'role_admin.role_id')
                ->select(['roles.name', 'roles.' . $field])->where('role_admin.admin_id', $admin_id);
            $role = $role->first();
            $role_name = @$role->{$field};
            CommonHelper::putToCache('roles_role_name' . $admin_id . $field, $role_name, ['roles', 'admin']);
        }

        return $role_name;
    }

    public static function getFromCache($key, $tags = [])
    {
        $key = env('DOMAIN') . $key;
        if (env('CACHE_ACTIVE') == false) {
            return false;
        }
        /*if ($key == 'menus_by_location_main_menu') {
            dd(Cache::tags(['menus'])->has('menus_by_location_main_menu'));
        }*/
        if (!Cache::tags($tags)->has($key)) {
            return false;
        }
        $value = Cache::tags($tags)->get($key);
        return $value === false ? null : $value;
    }

    public static function putToCache($key, $value, $tags = [], $time = 432000)
    {
        $key = env('DOMAIN') . $key;
        if (env('CACHE_ACTIVE') == false) {
            return false;
        }
        Cache::tags($tags)->remember($key, $time, function() use ($value) {
            return $value == null ? false : $value;
        });
        /*if (env('CACHE_DRIVER') == 'file') {
            Cache::tags($tags)->put($key, $value, $time);
        }*/
        return true;
    }

    public static function removedFromCache($key, $tags = [])
    {
        $key = env('DOMAIN') . $key;
        Cache::tags($tags)->forget($key);
        return true;
    }

    public static function removedTagsFromCache($tags = [])
    {
        Cache::tags($tags)->flush();
        return true;
    }

    public static function flushCache($tags = [])
    {
        if (env('CACHE_ACTIVE') == false) {
            return false;
        }
        Cache::tags($tags)->flush();
        Artisan::call('view:clear');
        return true;
    }

    public static function formatDateTimepickerToDateTimeLocal($time, $type = 'datetime-local')
    {
        $date = str_replace(strstr($time, ' '), '', $time);
        $day = date('Y-m-d', strtotime($date));
        if ($type != 'datetime-local') {
            return $day;
        } else {
            $hour = str_replace($date . ' ', '', $time);
            if (strpos($hour, ' PM')) {
                $split = explode(':', str_replace(' PM', '', $hour));
                $hour = ($split[0] + 12 == 24) ? '00' : $split[0] + 12;
                $hourMunites = $hour . ':' . $split[1];
            } else {
                $hourMunites = str_replace(' AM', '', $hour);
            }
            return $day . 'T' . $hourMunites;
        }
    }

}
