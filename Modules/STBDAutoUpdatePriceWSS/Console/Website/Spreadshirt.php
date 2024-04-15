<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use Mail;
use Modules\CheckErrorLink\Http\Helpers\CommonHelper;
use Modules\STBDAutoUpdatePriceWSS\Entities\DoomProduct;
use Modules\STBDAutoUpdatePriceWSS\Entities\Product;
use Session;

class Spreadshirt extends Base
{
    /*function __construct()
    {
        if (Session::get('login_KhosachnoiCom') == null) {
            $this->loginSystem();
            Session::put('login_KhosachnoiCom', true);
        }
    }*/

    private function loginSystem()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://khosachnoi.com");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $rs = str_get_html(curl_exec($ch));
        $reqFields = array(
            "__VIEWSTATE" => $rs->find("input[id=__VIEWSTATE]", 0)->value,
            "__VIEWSTATEGENERATOR" => $rs->find("input[id=__VIEWSTATEGENERATOR]", 0)->value,
            "__EVENTVALIDATION" => $rs->find("input[id=__EVENTVALIDATION]", 0)->value,
            'ctl00$ucLogin1$txtUserName' => 'kisyrua',
            'ctl00$ucLogin1$txtPassword' => 'ruatien',
            'ctl00$ucLogin1$btLogin' => 'Đăng nhập',
            'ctl00$txtKey' => ''
        );

        curl_setopt($ch, CURLOPT_URL, "http://khosachnoi.com");
        curl_setopt($ch, CURLOPT_POST, count($reqFields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($reqFields));
        curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie_khosachnoicom.txt');
        $rs = str_get_html(curl_exec($ch));
    }

    public function crawlPageList($website) {
        $doom_setting = json_decode($website->doom);

        foreach ($website->categories as $category) {
            $max_page = $category->page;

            $i = 1;
            while ($i <= $max_page) {
                $html = file_get_html('https://www.teezily.com/en-gb/marketplace?query=cat&page=2');
                dd($html->find('.ais-hits .ais-hits--item'));
                foreach ($html->find('.ais-hits .ais-hits--item .store-ais__label-campaign-marketplace2') as $product) {
                    $product_link = 'https:'.$product->getAttribute('href');
dd($product_link);
                    $product_exist = DoomProduct::where('product_link', $product_link)->first();

                    if (is_object($product_exist)) {
                        $this->updateProduct($product_exist);
                    } else {
                        $this->crawlProduct($website, $category, $product_link);
                    }
                }
                $i++;

            }
        }
        return true;
    }

    public function updateProduct($product) {
        return true;
    }

    /*
     * Lay thong tin cua 1 film
     *
     * $data['group_link_video'] : so tap film
     * */
    public function crawlProduct($website, $category, $product_link)
    {
        $doom_setting = json_decode($website->doom);
        try {

            $html = file_get_html($product_link);
            //  Lấy ảnh sp
            $img_extra = [];
            $data['name'] = trim(@$html->find($doom_setting->product->name_product_doom, 0)->innertext);

            foreach ($html->find('.product-view img') as $k => $image) {
                //  userfiles/{ten folder theo quy dinh}/{ten anh}
//                Sửa kích thước ảnh
                $size = str_replace('width=100,height=100',',width=800,height=800,',$image->getAttribute('src'));
                if ($k == 0) {
                    \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD('https:' . $size, 'design', $k,$data['name']);
                } elseif ($k == 1) {
                    \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD('https:' . $size, 'imagine1', $k,$data['name']);
                } elseif ($k == 2) {
                    \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD('https:' . $size, 'imagine2', $k,$data['name']);
                } elseif ($k == 3) {
                    \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD('https:' . $size, 'imagine3', $k,$data['name']);
                } elseif ($k == 4) {
                    \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD('https:' . $size, 'imagine4', $k,$data['name']);
                } else {
                    \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD('https:' . $size, 'imagine5', $k,$data['name']);
                }
                $img_extra[] = 'https:' . $image->getAttribute('src');
            }
            $data['image_extra'] = implode('|', $img_extra);
            $data['website_id'] = $website->id;
            $data['product_link'] = $product_link;
            $data['multi_cat'] = $category->id;

            $product = new DoomProduct();

            foreach ($data as $k => $v) {
                $product->{$k} = $v;
            }
            $product->save();
            return $product;
        } catch (\Exception $ex) {
            return [
                'status' => false,
                'msg' => $ex->getMessage()
            ];
        }
    }
}
