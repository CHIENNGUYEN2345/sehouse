<?php
/**
 * Created by PhpStorm.
 * BillPayment: hoanghung
 * Date: 08/09/2016
 * Time: 19:52
 */

namespace App\Console\Commands\Website;

use App\CRMDV\Models\CompanyCategory;
use App\CRMDV\Models\CompanyProfile;
use App\CRMDV\Models\DoomLog;
use App\Models\Category;
use Mail;
use Mailin\Mailin;

class CrawlHSCtyBase extends Base
{

    public function crawlCtys($website)
    {
        foreach ($website->categories as $category) {
            print "  - Crawl products in " . $category->link . "\n";
            $this->crawlCtyInTinh($category, json_decode($website->doom), $website);
        }
    }

    function crawlCtyInTinh($province_id, $link)
    {
        /*$data = CompanyProfile::whereNull('career_id')->limit(10000)->get();
        foreach ($data as $k => $cty_exist) {
            $v = trim($cty_exist->nganh_nghe);
            $career = CompanyCategory::where('name', $v)->first();
            if (!is_object($career)) {
                $career = new CompanyCategory();
                $career->name = $v;
                $career->save();
            }
            $cty_exist->career_id = $career->id;
            $cty_exist->save();
            print "  - update xong " .$k . ": ". $cty_exist->name . "\n";
        }
        die('xong');*/



        $flag = true;
        $page_id = 1;
        while ($flag) {
            $url_crawl = str_replace('{i}', $page_id, $link);

            print "    + Crawl cty trong link " . $url_crawl . "\n";

            if (!$this->checkUrl404($url_crawl)) {

//                $html = new \Htmldom($url_crawl);
                $html = file_get_html($url_crawl);

                if (empty($html->find('ul.hsdn li'))) {
                    $flag = false;
                    print "Hết trang " . "\n";
                } else {
                    foreach ($html->find('ul.hsdn li') as $cty) {
                        if ($cty->find('a', 0) != null) {
                            $cty_link = $cty->find('a', 0)->getAttribute('href');
                            if (strpos($cty_link, 'http') === false) {
                                $cty_link = 'https://hosocongty.vn/' . $cty_link;
//                                print "đổi link thành: " . $cty_link . "\n";
                            }

                            if (strpos($cty_link, 'http') === false) {
                                if ($cty_link[0] == '/') {
                                    $cty_link = substr($cty_link, 1);
                                }
                                $cty_link = 'https://hosocongty.vn/' . $cty_link;
                            }

//                        dd($cty_link);
                            $cty_exist = CompanyProfile::where('crawl_link', $cty_link)->first();
                            if (is_object($cty_exist)) {
                                print "        => Đã có\n";
//                                if (!$this->crawlCty('update', $cty_link, $province_id, $cty_exist)) {
//                                }
//                                $flag = false;


                                break;  //  nếu đã tồn tài rồi thì thoát luôn sang trang sau
                            } else {
                                if (!$this->crawlCty('insert', $cty_link, $province_id, $cty_exist)) {
//                                $flag = false;
                                }
                            }
                        } else {
                            print "null " . $cty->plaintext . "\n";
                        }

                    }
                }
                $page_id++;
            } else {
                $flag = false;
                print "Không thấy trang " .$url_crawl. "\n";
            }
        }
    }

    function getDataCty($crawl_link)
    {

        if (!$this->checkUrl404($crawl_link)) {

            try {
                $html = file_get_html($crawl_link);

                $fields = @$html->find('.module_data.detail ul.hsct li');
                $arr = [];

                foreach ($fields as $field) {
                    if ($field->find('label', 0) != null && $field->find('span', 0) != null ) {
                        if (strpos($field->find('label', 0)->plaintext, 'Email') !== false) {
                            //  nếu là email thì phải giải mã
                            $email_data = $field->find('a', 0)->getAttribute('data-cfemail');
                            $arr['Email'] = $email_data;
                        } else {
                            $arr[$field->find('label', 0)->plaintext] = $field->find('span', 0)->plaintext;
                        }
                    }
                }
                foreach ($arr as $k => $v) {
                    if (strpos($k, 'Tên quốc tế') !== false) {
                        $data['name_en'] = $v;
                    } elseif (strpos($k, 'Tên viết tắt') !== false) {
                        $data['name_short'] = $v;
                    } elseif (strpos($k, 'Mã số thuế') !== false) {
                        $data['mst'] = $v;
                    } elseif (strpos($k, 'Địa chỉ thuế') !== false) {
                        $data['address'] = $v;
                    } elseif (strpos($k, 'Đại diện pháp luật') !== false) {
                        $data['dai_dien'] = $v;
                    } elseif (strpos($k, 'Điện thoại') !== false) {
                        $data['tel'] = $v;
                    } elseif (strpos($k, 'Ngày cấp') !== false) {
                        $data['ngay_cap'] = date('Y-m-d', strtotime(trim(str_replace('/', '-', $v))));

                    } elseif (strpos($k, 'Ngành nghề chính') !== false) {
                        $v = trim($v);
                        $career = CompanyCategory::where('name', $v)->first();
                        if (!is_object($career)) {
                            $career = new CompanyCategory();
                            $career->name = $v;
                            $career->save();
                        }
                        $data['career_id'] = $career->id;
                    } elseif (strpos($k, 'Email') !== false) {
                        $data['email'] = $v;
                    } elseif (strpos($k, 'Cập nhật lần cuối') !== false) {
                        $data['last_update'] = date('Y-m-d', strtotime(trim(str_replace('/', '-', $v))));
                    }


                }

                $data['name'] = trim(@$html->find('.module_data.detail ul.hsct h1', 0)->innertext);

                return $data;
            } catch (\Exception $ex) {
                print $ex->getMessage() . "\n";
                return  [];
            }

        } else {
            $this->writeLog([
                'type' => 0,
                'action' => 'CURL link sản phẩm',
                'name' => 'Link bị 404 ' . $crawl_link,
                'msg' => 'Link bị 404 ' . $crawl_link,
                'link' => $crawl_link
            ]);
            return false;
        }
    }

    public function crawlCty($action, $cty_link, $province_id, $cty_exist)
    {
        print "      > Crawl cty " . $cty_link . "\n";

//        dd($ctys->price);
        if ($action == 'insert') {
            $cty_data = $this->getDataCty($cty_link);
            $cty_data['province_id'] = $province_id;
            $cty_data['crawl_link'] = $cty_link;

            if ($cty_data) {
                //  api sang web khác để lưu
                $x = file_get_contents('https://khoweb.top/data-cty?data=' . urlencode(json_encode($cty_data)));
                print $x . "\n";



//                $cty = new CompanyProfile();
//                foreach ($cty_data as $k => $v) {
//                    $cty->{$k} = $v;
//                }
//                $cty->save();
//                if ($cty) {
//                    print "        + đã tạo cty " . $cty_link . "\n";
//                    return true;
//                }
            }
        } elseif ($action == 'update') {
            if ($cty_exist->career_id != null) {
                dd('lặp lại');
            }
//            $cty_data = $this->getDataCty($cty_link);

            $v = trim($cty_exist->nganh_nghe);
            $career = CompanyCategory::where('name', $v)->first();
            if (!is_object($career)) {
                $career = new CompanyCategory();
                $career->name = $v;
                $career->save();
            }
            $cty_data['career_id'] = $career->id;

//            $cty_data = [
////                'ngay_cap' => @$cty_data['ngay_cap'],
////                'province_id' => $cty_data['province_id'],
//                'career_id' => $cty_data['career_id'],
//            ];
            foreach ($cty_data as $k => $v) {
                $cty_exist->$k = $v;
            }
            if ($cty_exist->save()) {
                print "          => Cập nhật thành công\n";
                return true;
            }
        }
        return false;
    }

    function sendMailByMailgun($mail_from, $mail_to, $subject, $html, $text, $replyTo)
    {
//        $mail_from = 'info@bydoublea.com';
//        $mail_to = 'hoanghung.developer@gmail.com';
//        $subject = 'Test Email';
//        $html = 'demo';
//        $text = 'demo';
//        $replyTo = 'dev';
        print "        > Send mail to : " . $mail_to . "\n";
        $array_data = array(
            'from' => $mail_from,
            'to' => $mail_to,
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
            'h:Reply-To' => $replyTo
        );
        $session = curl_init('https://api.mailgun.net/v3/qt.bydoublea.com/messages');
        curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($session, CURLOPT_USERPWD, 'api:dab70a473416df89a330e1d1a97e7b9d-8889127d-49dc6e42');
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($session);
        curl_close($session);
        $results = json_decode($response, true);
        /*var_dump($results);
        die;


        print "        > Send mail to : " . $mail_to . "\n";
        dd($mail_from . '|' . $mail_to . '|' . $subject . '|' . $text . '|' . $replyTo);
        $array_data = array(
            'from' => $mail_from,
            'to' => $mail_to,
            'subject' => $subject,
            'html' => $html,
            'text' => $text,
            'h:Reply-To' => $replyTo
        );
        $session = curl_init(env('MAILGUN_URL') . '/messages');
        curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($session, CURLOPT_USERPWD, 'api:' . env('MAILGUN_KEY'));
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($session);
        curl_close($session);
        $results = json_decode($response, true);
        dd($results);*/

        if ($results == null) {
            print "          => Loi Send mail to : " . $mail_to . "\n";
            $this->writeLog([
                'type' => 0,
                'action' => 'Gửi mail',
                'name' => 'Lỗi gửi mail tới ' . $mail_to,
                'msg' => 'Lỗi gửi mail tới ' . $mail_to,
                'link' => ''
            ]);
        }
        return $results;
    }

    public function writeLog($data)
    {
        if ($data['type'] == 0) {
            print "        => ERROR " . $data['action'] . " : " . $data['name'] . " => msg: " . $data['msg'] . " => link:" . $data['link'] . "\n";
        } else {
            print "        => SUCCESS " . $data['action'] . " " . $data['product_name'] . " => id: " . $data['product_id'] . "\n";
        }
        DoomLog::create($data);
        return true;
    }

    public function updateProducts($website, $cty)
    {
        $this->crawlCty($cty->link, json_decode($website->doom), $website, false, 'update', $cty);
    }

    public static function discount($base_price, $final_price, $type = '%')
    {
        $sale = $base_price - $final_price;
        if ($sale <= 0)
            return '';

        switch ($type) {
            case '-' :
                return number_format($sale, 0, '.', '.') . 'đ';
                break;
            default :
                return number_format(($sale / $base_price) * 100, 0, '.', '.') . '%';
        }
    }

    public function removeDuplicated()
    {
        $skip = 0;
        $take = 100;
        $stop = false;
        while (!$stop) {
            $ctys = CompanyProfile::skip($skip)->take($take)->get();
            if (count($ctys) == 0) {
                $stop = true;
            } else {
                foreach ($ctys as $cty) {
                    print "products: " . $cty->_id . "\n";
                    $cty_duplicated = CompanyProfile::where('link', $cty->link)->where('_id', '!=', $cty->_id)->first();
                    if (is_object($cty_duplicated)) {
                        print "  product_duplicated: " . $cty_duplicated->_id . "\n";
                        if (count(explode('|', $cty->category_ids)) > 3) {
                            print "    delete product_duplicated: " . $cty_duplicated->_id . "\n";
                            $cty_duplicated->delete();
                        } else {
                            print "    delete product_duplicated: " . $cty->_id . "\n";
                            $cty->delete();
                        }
                    }
                }
                $skip += 100;
                $take += 100;
            }
        }
        die('xong!');
    }
}
