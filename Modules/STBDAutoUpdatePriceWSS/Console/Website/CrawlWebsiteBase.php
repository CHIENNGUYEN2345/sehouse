<?php
/**
 * Created by PhpStorm.
 * User: hoanghung
 * Date: 08/09/2016
 * Time: 19:52
 */

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use App\Models\Author;
use App\Models\Category;
use App\Models\CrawlCategory;
use App\Models\CrawlPost;
use App\Models\CrawlProduct;
use App\Models\DoomLog;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\TermRelationships;
use App\Models\TermTaxonomy;
use App\Models\WpPost;
use App\Models\WpTerm;
use App\Models\HtmlDom;
use Mail;
use Mailin\Mailin;

class CrawlWebsiteBase extends Base
{

    public function crawlProducts($website)
    {
        foreach ($website->categories as $category) {
            print "  - Crawl products in " . $category->link . "\n";
            $this->crawlProductsInCategory($category, json_decode($website->doom), $website);
        }
    }

    function crawlProductsInCategory($category, $doom_setting, $website)
    {
        $flag = true;
        $i = 1;
        while ($flag) {
            if (strpos($doom_setting->category->paginate, 'http') === false) {
                $url_crawl = $category->link . '/' . str_replace('{number}', $i, $doom_setting->category->paginate);
            } else {
                $url_crawl = str_replace('{number}', $i, $doom_setting->category->paginate);
            }
            print "    + Crawl product in " . $url_crawl . "\n";
            if (!$this->checkUrl404($url_crawl)) {
                $html = new HtmlDom($url_crawl);
//                $html = $html->html;
//                dd($html->doc);
                if (empty($html->find($doom_setting->product->target))) {
                    $flag = false;
                } else {
                    foreach ($html->find($doom_setting->product->target) as $product) {
                        $product_link = $product->find($doom_setting->product->link, 0)->getAttribute('href');
                        if (strpos($product_link, '&') !== false) {
                            $product_link = explode('&', $product_link)[0];
                        }
                        if (strpos($product_link, 'http') === false) {
                            if ($product_link[0] == '/') {
                                $product_link = substr($product_link, 1);
                            }
                            $product_link = $website->name . $product_link;
                        }

                        //  ->where('categoryids', 'like', "%|" . $category->id . "|%")
                        $product_exist = Product::where('link', $product_link)->first();
                        if (!is_object($product_exist)) {
                            if (!$this->crawlProduct($product_link, $doom_setting, $website, $category->id, 'insert')) {
//                                $flag = false;
                            }
                        } elseif (strpos($product_exist->categoryids, '|' . $category->id . '|') === false) {
//                            dd($product_link);
//                            $product_exist->categoryids = $product_exist->categoryids . $category->id . '|';
                            $product_exist->update([
                                'categoryids' => $product_exist->categoryids . $category->id . '|'
                            ]);
                        } else {
                            print "        => Đã có\n";
                            $flag = false;
                        }
                    }
                }
                $i++;
            } else {
                $this->writeLog([
                    'type' => 0,
                    'action' => 'CURL link danh mục',
                    'website_id' => $website->id,
                    'name' => 'Link bị 404 ' . $url_crawl,
                    'msg' => 'Link bị 404 ' . $url_crawl,
                    'link' => $url_crawl
                ]);
                $flag = false;
            }
        }
    }

    function getDataProduct($product_link, $doom_setting, $website_id, $website_name)
    {
        if (!$this->checkUrl404($product_link)) {
            $html = new HtmlDom($product_link);
            $data['name'] = trim(@$html->find($doom_setting->product->name, 0)->innertext);
            $data['slug'] = str_slug($data['name'], '-') . rand(1, 1000);

            $remove_char = ['.', ','];
//            dd($doom_setting->product->price);
            if (isset($doom_setting->product->price_child) && $doom_setting->product->price_child != null) {
                $data['price'] = str_replace($remove_char, '', @$html->find($doom_setting->product->price, (int)$doom_setting->product->price_child)->innertext);
            } else {
                $data['price'] = str_replace($remove_char, '', @$html->find($doom_setting->product->price, 0)->innertext);
            }

            if (isset($doom_setting->product->price_remove) && $doom_setting->product->price_remove !== null) $data['price'] = preg_replace($doom_setting->product->price_remove, '', $data['price']);
            $data['price'] = (int)$data['price'];
//            dd($data);
            if ($data['price'] == 0) unset($data['price']);
            if ($doom_setting->product->price_old !== null) {
                if (isset($doom_setting->product->price_old_child) && $doom_setting->product->price_old_child != null) {
                    $data['price_old'] = @$html->find($doom_setting->product->price_old, (int)$doom_setting->product->price_old_child)->innertext;
                } else {
                    $data['price_old'] = @$html->find($doom_setting->product->price_old, 0)->innertext;
                }
                $data['price_old'] = (int)str_replace($remove_char, '', $data['price_old']);
                $data['price_old'] = (int)$data['price_old'];
                if ($data['price_old'] == 0) unset($data['price_old']);
            }

            $data['code'] = trim(@$html->find($doom_setting->product->code, 0)->innertext);
            if ($doom_setting->product->code_remove !== null) $data['code'] = preg_replace($doom_setting->product->code_remove, '', $data['code']);
            #
            if (isset($doom_setting->product->image_attribute) && $doom_setting->product->image_attribute != '' && $doom_setting->product->image_attribute != 'src') {  //  Neu lay link anh != img src
                if ($html->find($doom_setting->product->image, 0) != null)
                    $data['image'] = @$html->find($doom_setting->product->image, 0)->getAttribute($doom_setting->product->image_attribute);
            } else {        //  Neu lay link anh o img src
                if ($html->find($doom_setting->product->image, 0) != null)
                    $data['image'] = @$html->find($doom_setting->product->image, 0)->getAttribute('src');
            }
            if (isset($doom_setting->product->image_domain)) {
                isset($data['image']) ? $data['image'] = $website_name . $data['image'] : '';
            }

            if (!isset($data['image']) || $this->checkUrl404($data['image'])) {
                $this->writeLog([
                    'type' => 0,
                    'action' => 'Sản phẩm mất ảnh',
                    'website_id' => $website_id,
                    'name' => 'Sản phẩm mất ảnh ' . $product_link,
                    'msg' => 'Sản phẩm mất ảnh ' . $product_link,
                    'link' => $product_link
                ]);
                return false;
            }

            $data['image_extra'] = '';
            foreach ($html->find($doom_setting->product->image_extra) as $image) {
                if (isset($doom_setting->product->image_attribute) && $doom_setting->product->image_attribute != '' && $doom_setting->product->image_attribute != 'src') {
                    $img = @$image->getAttribute($doom_setting->product->image_attribute);
                } else {
                    $img = @$image->getAttribute('src');
                }
                if (isset($doom_setting->product->image_domain)) {
                    $data['image_extra'] .= $website_name . $img . '|';
                } else {
                    $data['image_extra'] .= $img . '|';
                }
            }
            $data['image_extra'] = substr($data['image_extra'], 0, -1);

            $data['content'] = '';
            foreach (explode('|', $doom_setting->product->content) as $content_target) {
                foreach (@$html->find($content_target) as $content_html) {
                    $data['content'] .= $content_html->innertext;
                }
            }
            $data['content'] = trim($data['content']);
            $data['intro'] = @$html->find($doom_setting->product->intro, 0)->innertext;
            $data['intro'] = trim($data['intro']);

            if (isset($doom_setting->product->reader) && $doom_setting->product->kind != '') {  //  Lấy thể loại
                $kind_name = @$html->find($doom_setting->product->kind, 2)->innertext;
                $data['kind'] = $kind_name;
            }

            if (isset($doom_setting->product->author) && $doom_setting->product->author != '') {  //  Lấy tác giả
                $author_name = @$html->find($doom_setting->product->author, 4)->innertext;
                $author_db = Author::where('name', $author_name)->first();
                if (!is_object($author_db)) {
                    $author_db = new Author();
                    $author_db->name = $author_name;
                    $author_db->save();
                }
                $data['author_id'] = $author_db->id;
            }

            if (isset($doom_setting->product->reader) && $doom_setting->product->reader != '') {  //  Lấy thể loại
                $reader_name = @$html->find($doom_setting->product->reader, 6)->innertext;
                $data['reader'] = $reader_name;
            }

            if ($doom_setting->product->manufacturer !== null) $data['manufacturer'] = @$html->find($doom_setting->product->manufacturer, 0)->innertext;
            if ($doom_setting->product->tags !== null) {
                $data['tags'] = '';
                foreach ($html->find($doom_setting->product->tags) as $tag) {
                    $data['tags'] .= $tag->getAttribute('src');
                }
            }

            $data['website_id'] = $website_id;
            $data['link'] = $product_link;

            $urlItemReq = $product_link;
            $Detail = $html->find("div[class=detailInfo] tbody tr");
            $idOfItem = explode("/",$urlItemReq); $idOfItem = $idOfItem[4];
            try {
                $files = file_get_contents($website_name . "book/data?id=" . $idOfItem);
                preg_match_all('/(title: ")(.+)("),/', $files, $listOfTitle);
                preg_match_all('/(mp3: ")(.+)(")/', $files, $listOfMp3);
                $listFile = array();
                for ($k = 0; $k <= count($listOfTitle[2]) - 1; $k++) {
                    $local_url = "";
                    if (isset($doom_setting->product->save_audio) && $doom_setting->product->save_audio == 1) {
                        if (!file_exists("file/" . $this->stripUnicode($Detail[0]->plaintext))) {
                            mkdir('files/' . $this->stripUnicode($Detail[0]->plaintext), 0777, TRUE);
                        }
                        $localDir = "files/" . $this->stripUnicode($Detail[0]->plaintext) . "/" . $this->stripUnicode($listOfTitle[2][$k]) . ".mp3";

                        $downloadState = copy($listOfMp3[2][$k], $localDir);
                        if ($downloadState == TRUE) $local_url = $localDir;
                    }
                    $tmpFile = array(
                        "title" => $listOfTitle[2][$k],
                        "default_url" => $listOfMp3[2][$k],
                        "local_url" => $local_url
                    );
                    if (strpos($tmpFile['default_url'], 'sachnoionline.net') === false) {
                        array_push($listFile, $tmpFile);
                    }
                }
                if (!empty($listFile)) {
                    $data['audio'] = json_encode($listFile);
                }
            } catch (\Exception $ex) {

            }

            return $data;
        } else {
            $this->writeLog([
                'type' => 0,
                'action' => 'CURL link sản phẩm',
                'website_id' => $website_id,
                'name' => 'Link bị 404 ' . $product_link,
                'msg' => 'Link bị 404 ' . $product_link,
                'link' => $product_link
            ]);
            return false;
        }
    }

    public function stripUnicode($str)
    {
        if (!$str) return false;
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        $str = str_replace(" ", "", $str);
        $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str);
        return $str;
    }

    public function crawlProduct($product_link, $doom_setting, $website, $categoryid, $action, $product = false)
    {
        print "      > Crawl product " . $product_link . "\n";
        $product_data = $this->getDataProduct($product_link, $doom_setting, $website->id, $website->name);

//        dd($product->price);
//        dd($product_data);
        if ($action == 'insert') {
            if ($product_data) {
                $product_data['multi_cat'] = '|' . $categoryid . '|';
                $product_data['status'] = 1;
                $product_data['user_id'] = 8;
                $product_data['view_total'] = 0;
                $product_data['type'] = '|2|';
                $product_data['crawl'] = 1;
                $product = Product::create($product_data);
                if ($product) {
                    $this->writeLog([
                        'type' => 1,
                        'action' => 'Thêm sản phẩm',
                        'website_id' => $website->id,
                        'product_name' => $product_data['name'],
                        'product_code' => $product_data['code'],
                        'link' => $product_data['link'],
                        'productid' => $product->id
                    ]);
                    return true;
                }
            }
        } elseif ($action == 'update') {
            if ($product_data) {
                $product_price = $product->price;
                foreach ($product_data as $k => $v) {
                    $product->$k = $v;
                }
                if ($product->save()) {
//                    if (isset($product_data['price']) && $product_data['price'] < $product_price) {
//                        $this->sendEmailChangeProductPrice($product_price, $product, $website);
//                    }
                    $this->writeLog([
                        'type' => 1,
                        'action' => 'Cập nhật sản phẩm',
                        'website_id' => $website->id,
                        'product_name' => $product_data['name'],
                        'product_code' => $product_data['code'],
                        'link' => $product_data['link'],
                        'productid' => $product->id
                    ]);
                    return true;
                }
            } else {        //  Xoa san pham neu khong con tren website cu
                print "        =>> Delete product " . $product_link . "\n";
                $product->delete();
                return response()->json([
                    'status' => true,
                    'code' => 'deleted'
                ]);
            }
        }
        return false;
    }

    public function sendEmailChangeProductPrice($old_price, $product, $website)
    {
        $img = $product->image;
        if (strpos($img, 'http') === false) {
            $img = 'http:' . $img;
        }

        $user_emails = Favorite::where('productid', $product->id)->where('email', '!=', '')->pluck('email')->toArray();

        $categoryid = explode('|', $product->categoryids)[1];
        $category = Category::find($categoryid);
        $mail_title = @$category->name . ' ' . str_replace(['.vn', '.com', '.com.vn'], '', explode('/', $website->name)[2]) . ' bạn thích đang giảm giá ' . $this->discount($old_price, $product->price);

        foreach ($user_emails as $email) {
            $html = '<h3 style="text-align: center;text-transform: uppercase">MÌNH ĐÃ TÌM KIẾM GIẢM GIÁ CHO BẠN! SẢN PHẨM BẠN THÍCH ĐANG GIẢM GIÁ</h3>
<div class="product type-product status-publish has-post-thumbnail product_cat-chic product_cat-classic product_cat-tops  column-1_3 first instock shipping-taxable product-type-simple"
     style="    box-sizing: border-box;
    margin: 0;
    clear: none;
    padding: 0 40px 2em 0;  width: 343px;
    position: relative;
    text-align: center;
    margin: 0 auto;
">
    <div class="post_item post_layout_thumbs" style="    color: #2c292c;    font-family: inherit;
    font-size: 100%;
    font-style: inherit;
    font-weight: inherit;
    line-height: inherit;
    border: 0;
    outline: 0;
    -webkit-font-smoothing: antialiased;
    -ms-word-wrap: break-word;
    word-wrap: break-word;">
        <div class="post_featured hover_none" style="    overflow: hidden;
    margin-bottom: 0;"><a href="https://www.bydoublea.com/affproduct/' . $product->id . '"> <img width="430" style="width: 100%;
    height: auto;
    display: block;"
                                                                            height="520"
                                                                            src="' . $img . '"
                                                                            class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail wp-post-image"
                                                                            alt=""> </a></div>
        <div class="post_data">
            <div class="post_header entry-header" style="    font-size: 100%;margin-top: 11px;
    font-style: inherit;
    font-weight: inherit;
    line-height: inherit;
    border: 0;
    outline: 0;"><h2 class="woocommerce-loop-product__title" style="    font-weight: 600;
    letter-spacing: .1px;
    margin-bottom: 0;
    margin-top: 0;
    padding: 0;font-size: 1em;"><a style="font-family: \'Open Sans\',sans-serif;    color: #1d1d1d;    text-decoration: none;"
                    href="https://www.bydoublea.com/affproduct/' . $product->id . '">' . $product->name . '</a></h2>
                <p style="    margin: 5px;
    text-align: center;
    text-decoration: line-through;display: inline-block;
    float: left;
    margin-left: 35px;">' . number_format($old_price, 0, '.', '.') . 'đ</p>
                <p class="price-subcrible-list" style="    color: #c33442;
    font-family: inherit;
    font-size: 22px;
    font-style: inherit;
    font-weight: bolder;
    line-height: inherit;
    border: 0;
    outline: 0; margin: 0;
    text-align: center;    text-align: center;
    display: inline-block;
    float: left;">' . number_format($product->price, 0, '.', '.') . 'đ</p><p style="display: inline-block;
    float: left;
    margin: 0;
    line-height: 25px;
    margin-left: 5px;">(' . $this->discount($old_price, $product->price) . ' off)</p></div>
        </div>
        <span style="width: 100%;    display: inline-block; margin: 7px 0px;"><a href="https://www.bydoublea.com/affproduct/' . $product->id . '" style="    display: inline-block;
    width: 100px;
    text-align: center;
    border: 1px solid;
    padding: 8px 13px;
    border-radius: 6px;
    text-transform: uppercase;
    text-decoration: none;
    background: #c33442;
    color: #fff;;
    margin: auto;">Mua ngay</a></span>
        <a href="https://www.bydoublea.com/favorites-delete?product_id=' . $product->id . '&email=' . $email . '" style="width: 100%;
    display: inline-block;    display: inline-block;color: #c5c5c5">Tắt thông báo giảm giá của sản phẩm này</a>
    </div>
</div>';
            $this->sendMailByMailgun('info@bydoublea.com', $email, $mail_title, $html, '', 'info@bydoublea.com');
        }
        return true;
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
            print "        => SUCCESS " . $data['action'] . " " . $data['product_name'] . " => id: " . $data['productid'] . "\n";
        }
        DoomLog::create($data);
        return true;
    }

    public function updateProducts($website, $product)
    {
        $this->crawlProduct($product->link, json_decode($website->doom), $website, false, 'update', $product);
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

    public function updateReadOnline($product) {
        $product_link = $product->link;
        $html = new \Htmldom($product_link);

        if ($html->find('.col-md-8 .btn-warning', 0) != null) {
            try {
                $read_online = [];
                $read_more_btn = @$html->find('.col-md-8 .btn-warning', 0)->getAttribute('href');
                if (strpos($read_more_btn, '/bib/i/?book=') !== false) {
                    print $product->id ." iframe". "\n";
                    return false;
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
                    $product->read_online = count($read_online);
                    $product->save();
                    file_put_contents(base_path() . '/public/filemanager/userfiles/read_online/' . $product->id . '.txt', json_encode($read_online));
                }
            } catch (\Exception $ex) {
            }
        }
        print $product->id ." done". "\n";
        return true;
    }

    public function removeDuplicated()
    {
        $skip = 0;
        $take = 100;
        $stop = false;
        while (!$stop) {
            $products = Product::skip($skip)->take($take)->get();
            if (count($products) == 0) {
                $stop = true;
            } else {
                foreach ($products as $product) {
                    print "product: " . $product->id . "\n";
                    $product_duplicated = Product::where('link', $product->link)->where('id', '!=', $product->id)->first();
                    if (is_object($product_duplicated)) {
                        print "  product_duplicated: " . $product_duplicated->id . "\n";
                        if (count(explode('|', $product->categoryids)) > 3) {
                            print "    delete product_duplicated: " . $product_duplicated->id . "\n";
                            $product_duplicated->delete();
                        } else {
                            print "    delete product_duplicated: " . $product->id . "\n";
                            $product->delete();
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
