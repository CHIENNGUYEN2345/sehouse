<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use App\Models\Author;
use App\Models\Company;
use App\Models\DoomLog;
use App\Models\Publishing;
use Mail;
use Session;
use  App\Models\Product;
use  App\Models\Category;
use  App\Models\User;

class VnDoc extends Base
{

    function __construct()
    {
        /*if (Session::get('login_Ebook') == null) {
            $this->loginSystem();
            Session::put('login_Ebook', true);
        }*/
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
     *
     * */

    public function getDataItem($website, $doom_setting, $product_link, $product = false)
    {
        try {
          //  $product_link = 'https://vndoc.com/giai-bai-tap-trang-12-13-sgk-toan-1-cac-so-1-2-3-luyen-tap/download';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie_ebook.txt');
            curl_setopt($ch, CURLOPT_URL, $product_link);
            $html = str_get_html(curl_exec($ch));
            //$data['id'] = $product->id;

            $data['name'] = trim(@$html->find('.maincontent h1', 0)->innertext);
            if ($data['name'] == '') {
                print "Product does not exist";
                $data['name'] == 'khong co ten';
            }
            $data['slug'] = $this->renderSlug(false, $data['name']);

           $data['content'] = trim(@$html->find('.maincontent ', 0)->innertext);

          //  dd($data);

// add name table author
            $author_name = trim(@$html->find('.item-info span', 1)->innertext);
            if ($author_name != '') {
                $author_db = Author::where('name', $author_name)->first();
                if (!is_object($author_db)) {
                    $author_db = new Author();
                    $author_db->name = $author_name;
                    $author_db->save();
                }
                $data['author_id'] = $author_db->id;
            }
// add name table users
            $dataUser_name = trim(@$html->find('.member-info .user-name', 0)->innertext);;
            if(isset($dataUser_name)){
                $dataUser_db = User::where('name',$dataUser_name)->first();
                if(!is_object($dataUser_db)){
                    $dataUser_db = new User();
                    $dataUser_db->name = $dataUser_name;
                    $dataUser_db->email = str_slug($dataUser_name, '_') . '@gmail.com';

                    $dataUser_db->save();
                }
                $data['user_id'] = $dataUser_db ->id;
            }
            $data['kind']=trim(@$html->find('.breadcrumb a', 1)->innertext);
            $data['ngay_phat_hanh'] = trim(@$html->find('.date-info .item-info time', 0)->innertext);
            //lấy file pdf
            $file = '';
            if ($html->find('.downbox a', 0) != null) {
                $file = $dataPdf['file_pdf'] = trim(@$html->find('.downbox a', 0)->getAttribute('href'));
                if ($file != null) {
                    $data['file'] = 'https://vndoc.com' . $file;
                }
                $button_html_download = $data['file'];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie_ebook.txt');
                curl_setopt($ch, CURLOPT_URL, $button_html_download);
                $html_download = str_get_html(curl_exec($ch));

                $file = substr( @$html_download->find('.message a', 0)->getAttribute('data-downurl'),-3);
                if($file != null){
                    $type = array('pdf');
                    $type_exe = array('xlsx');
                    $type_zip = array('zip');
                    $type_chm =array('chm');
                    $type_mp4 = array('mp4');
                    $type_rar = array('rar');
                    $type_prc = array('prc');

                    if(in_array($file,$type)){
                        if (@$html_download->find('.message a', 0)->getAttribute('data-downurl') != null) {
                            $data['file'] = @$html_download->find('.message a', 0)->getAttribute('data-downurl');
                        }
                        try {
                            $data_file = file_get_contents($data['file']);
                            file_put_contents(base_path() . '/public/filemanager/userfiles/ebooks/vd/'. $data['slug'] . '.pdf', $data_file);
                            $data['file'] = 'https://files.khosach.net/ebooks/vd/'. $data['slug'] . '.pdf';
                        } catch (\Exception $ex) {
                        }
                    }elseif (in_array($file,$type_prc)){
                        if (@$html_download->find('.message a', 0)->getAttribute('data-downurl') != null) {
                            $data['file'] = @$html_download->find('.message a', 0)->getAttribute('data-downurl');
                        }
                        try {
                            $data_file = file_get_contents($data['file']);
                            file_put_contents(base_path() . '/public/filemanager/userfiles/ebooks/vd_prc/'. $data['slug'] . '.prc', $data_file);
                            $data['file'] = 'https://files.khosach.net/ebooks/vd_prc/'. $data['slug'] . '.prc';
                        } catch (\Exception $ex) {
                        }
                    }
                    elseif (in_array($file,$type_exe)){
                        if (@$html_download->find('.message a', 0)->getAttribute('data-downurl') != null) {
                            $data['file'] = @$html_download->find('.message a', 0)->getAttribute('data-downurl');
                        }
                        try {
                            $data_file = file_get_contents($data['file']);
                            file_put_contents(base_path() . '/public/filemanager/userfiles/ebooks/vd_excel/'. $data['slug'] . '.xlsx', $data_file);
                            $data['file'] = 'https://files.khosach.net/ebooks/vd_excel/'.$data['slug'] . '.xlsx';
                        } catch (\Exception $ex) {
                        }
                    }elseif (in_array($file,$type_rar)){
                        if (@$html_download->find('.message a', 0)->getAttribute('data-downurl') != null) {
                            $data['file'] = @$html_download->find('.message a', 0)->getAttribute('data-downurl');
                        }
                        try {
                            $data_file = file_get_contents($data['file']);
                            file_put_contents(base_path() . '/public/filemanager/userfiles/ebooks/vd_rar/'. $data['slug'] . '.rar', $data_file);
                            $data['file'] = 'https://files.khosach.net/ebooks/vd_rar/'. $data['slug'] . '.rar';
                        } catch (\Exception $ex) {
                        }
                    }
                    elseif (in_array($file,$type_zip)){
                        if (@$html_download->find('.message a', 0)->getAttribute('data-downurl') != null) {
                            $data['file'] = @$html_download->find('.message a', 0)->getAttribute('data-downurl');
                        }
                        try {
                            $data_file = file_get_contents($data['file']);
                            file_put_contents(base_path() . '/public/filemanager/userfiles/ebooks/vd_zip/'. $data['slug'] . '.zip', $data_file);
                            $data['file'] = 'https://files.khosach.net/ebooks/vd_zip/'. $data['slug'] . '.zip';
                        } catch (\Exception $ex) {
                        }
                    }
                    elseif (in_array($file,$type_chm)){
                        if (@$html_download->find('.message a', 0)->getAttribute('data-downurl') != null) {
                            $data['file'] = @$html_download->find('.message a', 0)->getAttribute('data-downurl');
                        }
                        try {
                            $data_file = file_get_contents($data['file']);
                            file_put_contents(base_path() . '/public/filemanager/userfiles/ebooks/vd_chm/'. $data['slug'] . '.chm', $data_file);
                            $data['file'] = 'https://files.khosach.net/ebooks/vd_chm/'.$data['slug'] . '.chm';
                        } catch (\Exception $ex) {
                        }
                    }
                    elseif (in_array($file,$type_mp4)){
                        if (@$html_download->find('.message a', 0)->getAttribute('data-downurl') != null) {
                            $data['file'] = @$html_download->find('.message a', 0)->getAttribute('data-downurl');
                        }
                        try {
                            $data_file = file_get_contents($data['file']);
                            file_put_contents(base_path() . '/public/filemanager/userfiles/ebooks/vd_mp4/'. $data['slug'] . '.mp4', $data_file);
                            $data['file'] = 'https://files.khosach.net/ebooks/vd_mp4/'.$data['slug'] . '.mp4';
                        } catch (\Exception $ex) {
                        }
                    }
                    else{
                        if (@$html_download->find('.message a', 0)->getAttribute('data-downurl') != null) {
                            $data['file'] = @$html_download->find('.message a', 0)->getAttribute('data-downurl');
                        }
                        try {
                            $data_file = file_get_contents($data['file']);
                            file_put_contents(base_path() . '/public/filemanager/userfiles/ebooks/vd_word/'. $data['slug'] . '.doc', $data_file);
                            $data['file'] = 'https://files.khosach.net/ebooks/vd_word/'.$data['slug'] . '.doc';
                        } catch (\Exception $ex) {
                        }
                    }
                }else{
                    $data['file'] = 'ko co file';
                }

                $data['file_word'] = $data['file'];
                $data['read_online_iframe'] = @$html_download->find('.message a', 0)->getAttribute('data-downurl');
                $data['type'] = '|3|';
            }else{
                $data['file'] = 'ko co file';
            }


            $data['website_id'] = $website->id;
            $data['link'] = $product_link;


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
