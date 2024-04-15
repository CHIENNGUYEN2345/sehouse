<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use Mail;
use Modules\STBDAutoUpdatePriceWSS\Entities\Manufacturer;
use Session;

class STBDCrawlProduct extends CrawlProductBase
{

    public function appendData($data) {
        //  Thêm string vào tên
        if (isset($data['name'])) {
            $data['name'] = @$this->_doom_setting->name_prev . ' ' . $data['name'] . ' ' . @$this->_doom_setting->name_last;
        }

        //  Thêm string vào content
        if (isset($data['content'])) {
            $data['content'] = @$this->_doom_setting->content_prev . ' ' . $data['content'] . ' ' . @$this->_doom_setting->content_last;
        }

        //  Check ảnh vuông
        if ($this->_doom_setting->check_image_square == 1) {    //  Check kick co anh ko vuong thi bao loi
            $domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $file_headers = @get_headers($domain . '/public/filemanager/userfiles/' . $data['image']);
            if ($file_headers[0] == 'HTTP/1.0 500 Internal Server Error') {
                return [
                    'error' => true,
                    'msg' => 'Ảnh sản phẩm không hợp lệ !'
                ];
            } else {
                list($width, $height) = @getimagesize($domain . '/public/filemanager/userfiles/' . $data['image']);
            }
            if ($width != $height) {
                unset($data['image']);
                unlink(base_path() . '/public/filemanager/userfiles/' . $data['image']);
            }
        }

        //  Thương hiệu
        if (isset($data['manufacturer_name'])) {
            $manufacturer_slug = str_slug($data['manufacturer_name']);
            $manufacturer = Manufacturer::where('slug', $manufacturer_slug)->first();
            if (!is_object($manufacturer)) {
                $manufacturer = Manufacturer::create([
                    'name' => $data['manufacturer_name'],
                    'slug' => $manufacturer_slug,
                    'status' => 0,
                    'crawl_from' => $this->_domain
                ]);
            }

            $data['manufacturer_id'] = $manufacturer->id;
            unset($data['manufacturer_name']);
        }

        //  Xuất sứ
        if (isset($data['manufacturer_name'])) {
            $manufacturer_slug = str_slug($data['manufacturer_name']);
            $manufacturer = Manufacturer::where('slug', $manufacturer_slug)->first();
            if (!is_object($manufacturer)) {
                $manufacturer = Manufacturer::create([
                    'name' => $data['manufacturer_name'],
                    'slug' => $manufacturer_slug,
                    'status' => 0,
                    'crawl_from' => $this->_domain
                ]);
            }

            $data['manufacturer_id'] = $manufacturer->id;
            unset($data['manufacturer_name']);
        }

        $data = $this->appendDataAfter($data);

        return $data;
    }

    public function appendDataAfter($data) {
        return $data;
    }
}
