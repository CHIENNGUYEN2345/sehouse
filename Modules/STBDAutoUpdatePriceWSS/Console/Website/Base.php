<?php
/**
 * Created by PhpStorm.
 * User: hoanghung
 * Date: 08/09/2016
 * Time: 19:52
 */

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use Modules\STBDAutoUpdatePriceWSS\Entities\Product;

class Base
{

    protected $model;

    public function __construct()
    {
        require_once base_path('app/Console/Commands/simple_html_dom.php');
        ini_set("user_agent", "Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0");
        $this->model = new $this->module['modal'];
    }

    public function checkUrl404($url) {
        $handle = curl_init($url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($handle);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if($httpCode == 404) {
            return  true;
        }
        return false;
    }

    public function writeLog($data)
    {
        /*if ($data['type'] == 0) {
            print "        => ERROR " . $data['action'] . " : " . $data['name'] . " => msg: " . $data['msg'] . " => link:" . $data['link'] . "\n";
        } else {
            print "        => SUCCESS " . $data['action'] . " " . $data['product_name'] . " => id: " . $data['product_id'] . "\n";
        }*/
//        DoomLog::create($data);
        return true;
    }

    function renderSlug($id, $name, $field = 'slug')
    {
        $slug = str_slug($name, '-');
        $item = $this->model->where($field, '=', $slug);
        if ($id) $item = $item->where('id', '!=', $id);

        if ($item->count() > 0) {
            return $slug . '-' . time();
        }
        return $slug;
    }

    public function convert_vi_to_en($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        //$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
        return $str;
    }
}