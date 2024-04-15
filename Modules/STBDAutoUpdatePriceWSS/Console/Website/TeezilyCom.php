<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use App\Models\Error;
use Mail;
use Modules\CheckErrorLink\Http\Helpers\CommonHelper;
use Modules\STBDAutoUpdatePriceWSS\Entities\DoomProduct;
use Modules\STBDAutoUpdatePriceWSS\Entities\Product;
use Session;

class TeezilyCom extends Base
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

    public function crawlPageList($website)
    {
        $doom_setting = json_decode($website->doom);

        foreach ($website->categories as $category) {
            $max_page = $category->page;
            for ($i = 1; $i <= $max_page; $i++) {
                $html = file_get_html($category->link . '?page=' . $i);
                foreach ($html->find('#articleTileList div.col-6.col-md-4 a') as $k => $product) {
                    $product_link = 'https:' . $product->getAttribute('href');
                    $product_exist = DoomProduct::where('product_link', $product_link)->first();
                    if (is_object($product_exist)) {
                        $this->updateProduct($product_exist);
                    } else {
                        $this->crawlProduct($website, $category, $product_link, $i);
                    }
                }
            }
        }
    }

    public function updateProduct($product)
    {
        return true;
    }

    /*
     * Lay thong tin cua 1 film
     *
     * $data['group_link_video'] : so tap film
     * */
    public function crawlProduct($website, $category, $product_link, $i)
    {
        $doom_setting = json_decode($website->doom);
        try {

            $html = file_get_html($product_link);
            //  Lấy ảnh sp
            $data['name'] = trim(@$html->find($doom_setting->product->name_product_doom, 0)->innertext);
            $design = $html->find('#d-dE>img', 0);
            $img_magine1 = $html->find('#d-dG>img', 0);
            $img_magine2 = $html->find('#d-dI>img', 0);

//                Sửa kích thước ảnh
            $re_size1 = str_replace('width=100,height=100', ',width=800,height=800,', $design->getAttribute('src'));
            $re_size2 = str_replace('width=100,height=100', ',width=800,height=800,', $img_magine1->getAttribute('src'));
            $re_size3 = str_replace('width=100,height=100', ',width=800,height=800,', $img_magine2->getAttribute('src'));

            $product = new DoomProduct();
            $product->save();

//            Đổ dữ liệu vào bảng doom_product
            $data['img_design'] = \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD('https:' . $re_size1, 'design', $product->id, $data['name']);
            $data['img_magine1'] = \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD('https:' . $re_size2, 'imagine1', $product->id, $data['name']);
            $data['img_magine2'] = \Modules\STBDAutoUpdatePriceWSS\Helpers\STBDCrawllerHelper::saveFileSTBD('https:' . $re_size3, 'imagine2', $product->id, $data['name']);
            $data['image_extra'] = 'https:' . $re_size1 . '|https:' . $re_size2 . '|https:' . $re_size3;
            $data['website_id'] = $website->id;
            $data['product_link'] = $product_link;
            $data['multi_cat'] = $category->id;

            foreach ($data as $k => $v) {
                $product->{$k} = $v;
            }
            $product->save();

            return $product;
        } catch (\Exception $ex) {
            Error::create([
                'module' => 'STBDAutoUpdatePriceWSS',
                'massage' => $ex->getMessage(),
                'file' => $product_link
            ]);
            return [
                'status' => false,
                'msg' => $ex->getMessage()
            ];
        }
    }
}
