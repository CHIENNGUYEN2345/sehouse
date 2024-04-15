<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use Mail;
use Session;

class KhosachnoiCom extends Base
{
    function __construct()
    {
        if (Session::get('login_KhosachnoiCom') == null) {
            $this->loginSystem();
            Session::put('login_KhosachnoiCom', true);
        }
    }

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
            curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie_khosachnoicom.txt');
            curl_setopt($ch, CURLOPT_URL, $product_link);
            $html = str_get_html(curl_exec($ch));

            if (!$product) {
                $data['name'] = trim(@$html->find($doom_setting->product->name, 0)->innertext);
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

                if (isset($data['image']) || !$this->checkUrl404($data['image'])) {
                    try {
                        $data_file = file_get_contents($data['image']);
                        file_put_contents(base_path() . '/public/filemanager/userfiles/img/ksn/' . $data['slug'] . '.jpg', $data_file);
                        $data['image'] = 'https://files.khosach.net/img/ksn/' . $data['slug'] . '.jpg';
                    } catch (\Exception $ex) {
                    }
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

                $kind_name = @$html->find('#right .ct_nd .fl .pl10 a', 0)->innertext;
                $data['kind'] = $kind_name;

                $reader_name = @$html->find('#right .ct_nd .fl .pl10 a', 2)->innertext;
                $data['reader'] = $reader_name;

                $author_name = @$html->find('#right .ct_nd .fl .pl10 a', 1)->innertext;
                if ($author_name != '') {
                    $author_db = Author::where('name', $author_name)->first();
                    if (!is_object($author_db)) {
                        $author_db = new Author();
                        $author_db->name = $author_name;
                        $author_db->save();
                    }
                    $data['author_id'] = $author_db->id;
                }

                $publishing_name = @$html->find('.detail__intro-support td a', 0)->innertext;
                if ($publishing_name != '') {
                    $publishing_db = Publishing::where('name', $publishing_name)->first();
                    if (!is_object($publishing_db)) {
                        $publishing_db = new Publishing();
                        $publishing_db->name = $publishing_name;
                        $publishing_db->save();
                    }
                    $data['publishing_id'] = $publishing_db->id;
                }

                $company_name = @$html->find('.detail__intro-support td a', 1)->innertext;
                if ($company_name != '') {
                    $company_db = Company::where('name', $company_name)->first();
                    if (!is_object($company_db)) {
                        $company_db = new Company();
                        $company_db->name = $company_name;
                        $company_db->save();
                    }
                    $data['company_id'] = $company_db->id;
                }

                try {
                    $playList = $html->find("div[class=fl ml15 mt05] script", 2)->innertext;
                    $playList = strstr($playList, '[{title:');
                    $playList = str_replace(strstr($playList, ', {'), '', $playList);
                    $playList = rtrim(ltrim($playList, "\""), "\"");
                    $playList = str_replace('title:', '"title":', $playList);
                    $playList = str_replace('mp3:', '"mp3":', $playList);
                    $playList = str_replace('free:', '"free":', $playList);
                    $listFile = [];
                    foreach (json_decode($playList) as $play) {
                        if (strpos(@$play->title, '>')) {
                            $title = @explode('>', @$play->title)[1];
                            $title = @explode('<', $title)[0];
                        } else {
                            $title = @$play->title;
                        }
                        if (strpos(@$play->mp3, 'youtube.com')) {
                            $mp3 = @explode("src='", @$play->mp3)[1];
                            $mp3 = @explode("'", $mp3)[0];
                            if (strpos(@$play->mp3, 'youtube.com/embed/')) {
                                $mp3 = @explode("/embed/", $mp3)[1];
                                $mp3 = 'https://www.youtube.com/watch?v=' . $mp3;
                            }
                        } else {
                            $mp3 = @$play->mp3;
                        }
                        if ($mp3 == '' || $mp3 == null) {
                            print $product_link ."\n";
                            die('fff');
                        }
                        $listFile[] = [
                            'title' => $title,
                            'default_url' => $mp3,
                            'local_url' => @$play->local_url,
                        ];
                    }

                    if (!empty($listFile)) {
                        $data['audio'] = json_encode($listFile);
                    }
                } catch (\Exception $ex) {

                }
            }

            $data['website_id'] = $website->id;
            $data['link'] = $product_link;
            $data['type'] = '|2|';
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
