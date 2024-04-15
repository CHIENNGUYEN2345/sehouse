<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use App\Models\Author;
use App\Models\Company;
use App\Models\DoomLog;
use App\Models\Publishing;
use Mail;
use Session;

class Ebook extends Base
{

    function __construct()
    {
        if (Session::get('login_Ebook') == null) {
            $this->loginSystem();
            Session::put('login_Ebook', true);
        }
    }

    private function loginSystem()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, "https://ebook.vn/sign-in");
        $postFields = array(
            "email" => "kisyrua@gmail.com",
            "password" => "ruatien"
        );
        curl_setopt($ch, CURLOPT_POST, count($postFields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie_ebook.txt');
        $rs = curl_exec($ch);
    }

    /*
     * Lay thong tin cua 1 film
     *
     * $data['group_link_video'] : so tap film
     * */
    public function getDataItem($website, $doom_setting, $product_link, $product = false)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie_ebook.txt');
            curl_setopt($ch, CURLOPT_URL, $product_link);
            $html = str_get_html(curl_exec($ch));
            $data['name'] = trim(@$html->find($doom_setting->product->name, 0)->innertext);

            if ($html->find('.book__price span', 0) != null) {
                $price_text = $html->find('.book__price span', 0)->innertext;
                if (strpos($price_text, 'Miễn phí') === false) {
                    $price_text = str_replace('đ','', $price_text);
                    $price_text = (int) str_replace('.','', $price_text);
                    $div_price_html = $html->find('.book__price', 0)->innertext;
                    if (strpos($div_price_html, 'Sách giấy') === false) {
                        $data['ebook_price'] = $price_text;
                    } else {
                        $data['final_price'] = $price_text;
                    }
                }
            }
            if (!$product) {
                $data['slug'] = $this->renderSlug(false, $data['name']);
                #
                if (isset($doom_setting->product->image_attribute) && $doom_setting->product->image_attribute != '' && $doom_setting->product->image_attribute != 'src') {  //  Neu lay link anh != img src
                    if ($html->find($doom_setting->product->image, 0) != null)
                        $data['image'] = @$html->find($doom_setting->product->image, 0)->getAttribute($doom_setting->product->image_attribute);
                } else {        //  Neu lay link anh o img src
                    if ($html->find($doom_setting->product->image, 0) != null)
                        $data['image'] = @$html->find($doom_setting->product->image, 0)->getAttribute('src');
                }
                if (isset($doom_setting->product->image_domain)) {
                    isset($data['image']) ? $data['image'] = $website->name . $data['image'] : '';
                }
                if (!isset($data['image']) || $this->checkUrl404($data['image'])) {
                    $this->writeLog([
                        'type' => 0,
                        'action' => 'Sản phẩm mất ảnh',
                        'website_id' => $website->id,
                        'name' => 'Sản phẩm mất ảnh ' . $product_link,
                        'msg' => 'Sản phẩm mất ảnh ' . $product_link,
                        'link' => $product_link
                    ]);
                    return false;
                }

                try {
                    $data_file = file_get_contents($data['image']);
                    file_put_contents(base_path() . '/public/filemanager/userfiles/img/eb/' . $data['slug'] . '.jpg', $data_file);
                    $data['image'] = 'https://files.khosach.net/img/eb/' . $data['slug'] . '.jpg';
                } catch (\Exception $ex) {}

                $data['content'] = '';
                foreach (explode('|', $doom_setting->product->content) as $content_target) {
                    foreach (@$html->find($content_target) as $content_html) {
                        $data['content'] .= $content_html->innertext;
                    }
                }
                $data['content'] = trim($data['content']);
                $data['intro'] = @$html->find($doom_setting->product->intro, 0)->innertext;
                $data['intro'] = trim($data['intro']);

                $author_name = @$html->find($doom_setting->product->author, 0)->innertext;
                if ($author_name != '') {
                    $author_db = Author::where('name', $author_name)->first();
                    if (!is_object($author_db)) {
                        $author_db = new Author();
                        $author_db->name = $author_name;
                        $author_db->save();
                    }
                    $data['author_id'] = $author_db->id;
                }

                for ($i = 0; $i <= 4; $i ++) {
                    if ($html->find('.detail__intro-support th', $i) != null) {
                        $label = $html->find('.detail__intro-support th', $i)->innertext;
                        if (strpos($label, 'Kích thước') !== false) {
                            $data['size'] = @$html->find('.detail__intro-support td', $i)->innertext;
                        } elseif(strpos($label, 'Nhà xuất bản') !== false) {
                            $publishing_name = @$html->find('.detail__intro-support td', $i)->find('a', 0)->innertext;
                            if ($publishing_name != '') {
                                $publishing_db = Publishing::where('name', $publishing_name)->first();
                                if (!is_object($publishing_db)) {
                                    $publishing_db = new Publishing();
                                    $publishing_db->name = $publishing_name;
                                    $publishing_db->save();
                                }
                                $data['publishing_id'] = $publishing_db->id;
                            }
                        } elseif(strpos($label, 'Công ty phát hành') !== false) {
                            $company_name = @$html->find('.detail__intro-support td', $i)->find('a', 0)->innertext;
                            if ($company_name != '') {
                                $company_db = Company::where('name', $company_name)->first();
                                if (!is_object($company_db)) {
                                    $company_db = new Company();
                                    $company_db->name = $company_name;
                                    $company_db->save();
                                }
                                $data['company_id'] = $company_db->id;
                            }
                        } elseif(strpos($label, 'Số Trang') !== false) {
                            $data['page_number'] = @$html->find('.detail__intro-support td', $i)->innertext;
                        }
                    }
                }

                $data['website_id'] = $website->id;
                $data['link'] = $product_link;
                $data['type'] = '|3|';

                if ($html->find('.detail__intro-form form .form-action .btn-greenlight', 0) != null) {
                    $data['file'] = 'https://ebook.vn' . @$html->find('.detail__intro-form form .form-action .btn-greenlight', 0)->getAttribute('href');
                    try {
                        $data_file = file_get_contents($data['file']);
                        file_put_contents(base_path() . '/public/filemanager/userfiles/ebooks/eb/' . $data['slug'] . '.pdf', $data_file);
                        $data['file'] = 'https://files.khosach.net/ebooks/eb/' . $data['slug'] . '.pdf';
                    } catch (\Exception $ex) {
                    }
                }
            }
//            dd($data);
            return $data;
        } catch (\Exception $ex) {
            $this->writeLog([
                'type' => 1,
                'action' => $ex->getMessage(),
                'website_id' => 0,
                'product_name' => '',
//                        'product_code' => @$product_data['code'],
                'link' => '',
                'product_id' => 0
            ]);
            return [
                'status' => false,
                'msg' => $ex->getMessage()
            ];
        }
    }
}
