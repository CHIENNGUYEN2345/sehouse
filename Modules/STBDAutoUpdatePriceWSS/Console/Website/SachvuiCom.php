<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use App\Models\Author;
use App\Models\DoomLog;
use App\Models\Product;
use Mail;
use Session;

class SachvuiCom extends Base
{

    function __construct()
    {
        if (Session::get('login_SachvuiCom') == null) {
            $this->loginSystem();
            Session::put('login_SachvuiCom', true);
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
        curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie_sachvuiCom.txt');
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
            $product_link = 'https://sachvui.com/ebook/10-bi-quyet-thanh-cong-cua-nhung-dien-gia-mc-tai-nang-nhat-the-gioi-carmine-gallo.2159.html';
            $html = new \Htmldom($product_link);
            $data['name'] = trim(@$html->find($doom_setting->product->name, 0)->innertext);
            $data['slug'] = $this->renderSlug(false, $data['name']);

            if (!$product) {
                $name_en = strtolower($this->convert_vi_to_en($data['name']));
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
                    file_put_contents(base_path() . '/public/filemanager/userfiles/img/sv/' . $data['slug'] . '.jpg', $data_file);
                    $data['image'] = 'https://files.khosach.net/img/sv/' . $data['slug'] . '.jpg';
                } catch (\Exception $ex) {
                }

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
                $author_name = str_replace('Tác giả : ', '', $author_name);
                if ($author_name != '') {
                    $author_db = Author::where('name', $author_name)->first();
                    if (!is_object($author_db)) {
                        $author_db = new Author();
                        $author_db->name = $author_name;
                        $author_db->save();
                    }
                    $data['author_id'] = $author_db->id;
                }

                $kind_name = @$html->find('.col-md-8 h5 a', 0)->innertext;
                $data['kind'] = $kind_name;

                //  Lay file pdf
                if ($html->find('.col-md-8 .btn-danger', 0) != null) {
                    $data['file'] = @$html->find('.col-md-8 .btn-danger', 0)->getAttribute('href');
                    try {
                        $data_file = file_get_contents($data['file']);
                        file_put_contents(base_path() . '/public/filemanager/userfiles/ebooks/sv/' . $data['slug'] . '.pdf', $data_file);
                        $data['file'] = 'https://files.khosach.net/ebooks/sv/' . $data['slug'] . '.pdf';
                    } catch (\Exception $ex) {
                    }
                }

                //  Lay file epub
                if ($html->find('.col-md-8 .btn-primary', 0) != null) {
                    $data['epub'] = @$html->find('.col-md-8 .btn-primary', 0)->getAttribute('href');
                    try {
                        $data_file = file_get_contents($data['epub']);
                        file_put_contents(base_path() . '/public/filemanager/userfiles/epub/sv/' . $data['slug'] . '.epub', $data_file);
                        $data['epub'] = 'https://files.khosach.net/epub/sv/' . $data['slug'] . '.epub';
                    } catch (\Exception $ex) {
                    }
                }
                if ($html->find('.col-md-8 .btn-warning', 0) != null) {
                    try {
                        $read_online = [];
                        $read_more_btn = @$html->find('.col-md-8 .btn-warning', 0)->getAttribute('href');
                        if (strpos($read_more_btn, '/bib/i/?book=') !== false) {
                            $data['read_online_iframe'] = $read_more_btn;
                        } else {
                            $html = new \Htmldom($read_more_btn);
                            $ul = $html->find('.scrollable-menu', 0);
                            if ($ul != null) {
                                $taps = [];
                                foreach ($ul->find('li a') as $a) {
                                    $taps[] = $a->getAttribute('href');
                                }
                                foreach ($taps as $tap) {
                                    $html = new \Htmldom($tap);
                                    if ($html->find('.inner', 0) != null && $html->find('.inner', 0)->innertext != ' ') $content = $html->find('.inner', 0)->innertext;
                                    elseif ($html->find('.chapter-content', 0) != null && $html->find('.chapter-content', 0)->innertext != ' ') $content = $html->find('.chapter-content', 0)->innertext;
                                    elseif ($html->find('.entry-content', 0) != null && $html->find('.entry-content', 0)->innertext != ' ') $content = $html->find('.entry-content', 0)->innertext;
                                    elseif ($html->find('.noi_dung_online', 0) != null && $html->find('.noi_dung_online', 0)->innertext != ' ') $content = $html->find('.noi_dung_online', 0)->innertext;
                                    elseif ($html->find('.doc-online', 0) != null && $html->find('.doc-online', 0)->innertext != ' ') {
                                        $content = '';
                                        foreach ($html->find('.doc-online p') as $content_html) {
                                            $content .= $content_html->innertext;
                                        }
                                    }


                                    $read_online[] = [
                                        'name' => @$html->find('.link-tap', 0)->innertext,
                                        'content' => $content
                                    ];
                                }
                            }
                            $data['read_online'] = $read_online;
                        }
                    } catch (\Exception $ex) {
                    }
                }

                $data['website_id'] = $website->id;
                $data['link'] = $product_link;
                $data['type'] = '|';
                if (isset($data['file'])) {
                    $data['type'] .= '3|';
                }
                if (isset($data['read_online']) || isset($data['read_online_iframe'])) {
                    $data['type'] .= '5|';
                }
            }



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
