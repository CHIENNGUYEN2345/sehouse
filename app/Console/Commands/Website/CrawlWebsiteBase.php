<?php
/**
 * Created by PhpStorm.
 * BillPayment: hoanghung
 * Date: 08/09/2016
 * Time: 19:52
 */

namespace App\Console\Commands\Website;

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
            print "    + Crawl products in " . $url_crawl . "\n";
            if (!$this->checkUrl404($url_crawl)) {
                $html = new \Htmldom($url_crawl);
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

                        //  ->where('category_ids', 'like', "%|" . $category->_id . "|%")
                        $product_exist = Product::where('link', $product_link)->first();
                        if (!is_object($product_exist)) {
                            if (!$this->crawlProduct($product_link, $doom_setting, $website, $category->_id, 'insert')) {
//                                $flag = false;
                            }
                        } elseif (strpos($product_exist->category_ids, '|' . $category->_id . '|') === false) {
//                            dd($product_link);
//                            $product_exist->category_ids = $product_exist->category_ids . $category->_id . '|';
                            $product_exist->update([
                                'category_ids' => $product_exist->category_ids . $category->_id . '|'
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
                    'website_id' => $website->_id,
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
            $html = new \Htmldom($product_link);
            $data['name'] = trim(@$html->find($doom_setting->product->name, 0)->innertext);

            $remove_char = ['.', ','];
//            dd($doom_setting->products->price);
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

            if ($doom_setting->product->manufacturer !== null) $data['manufacturer'] = @$html->find($doom_setting->product->manufacturer, 0)->innertext;
            if ($doom_setting->product->tags !== null) {
                $data['tags'] = '';
                foreach ($html->find($doom_setting->product->tags) as $tag) {
                    $data['tags'] .= $tag->getAttribute('src');
                }
            }

            $data['website_id'] = $website_id;
            $data['link'] = $product_link;

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

    public function crawlProduct($product_link, $doom_setting, $website, $category_id, $action, $product = false)
    {
        print "      > Crawl products " . $product_link . "\n";
        $product_data = $this->getDataProduct($product_link, $doom_setting, $website->_id, $website->name);
//        dd($products->price);
//        dd($product_data);
        if ($action == 'insert') {
            if ($product_data) {
                $product_data['category_ids'] = '|' . $category_id . '|';
                $product_data['status'] = 1;
                $product = Product::create($product_data);
                if ($product) {
                    $this->writeLog([
                        'type' => 1,
                        'action' => 'Thêm sản phẩm',
                        'website_id' => $website->_id,
                        'product_name' => $product_data['name'],
                        'product_code' => $product_data['code'],
                        'link' => $product_data['link'],
                        'product_id' => $product->_id
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
                    if (isset($product_data['price']) && $product_data['price'] < $product_price) {
                        $this->sendEmailChangeProductPrice($product_price, $product, $website);
                    }
                    $this->writeLog([
                        'type' => 1,
                        'action' => 'Cập nhật sản phẩm',
                        'website_id' => $website->_id,
                        'product_name' => $product_data['name'],
                        'product_code' => $product_data['code'],
                        'link' => $product_data['link'],
                        'product_id' => $product->_id
                    ]);
                    return true;
                }
            } else {        //  Xoa san pham neu khong con tren website cu
                print "        =>> Delete products " . $product_link . "\n";
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

        $user_emails = Favorite::where('product_id', $product->id)->where('email', '!=', '')->pluck('email')->toArray();

        $category_id = explode('|', $product->category_ids)[1];
        $category = Category::find($category_id);
        $mail_title = @$category->name . ' ' . str_replace(['.vn', '.com', '.com.vn'], '', explode('/', $website->name)[2]) . ' bạn thích đang giảm giá ' . $this->discount($old_price, $product->price);

        foreach ($user_emails as $email) {
            $html = '<h3 style="text-align: center;text-transform: uppercase">MÌNH ĐÃ TÌM KIẾM GIẢM GIÁ CHO BẠN! SẢN PHẨM BẠN THÍCH ĐANG GIẢM GIÁ</h3>
<div class="products type-products status-publish has-post-thumbnail product_cat-chic product_cat-classic product_cat-tops  column-1_3 first instock shipping-taxable products-type-simple"
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
            print "        => SUCCESS " . $data['action'] . " " . $data['product_name'] . " => id: " . $data['product_id'] . "\n";
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
                    print "products: " . $product->_id . "\n";
                    $product_duplicated = Product::where('link', $product->link)->where('_id', '!=', $product->_id)->first();
                    if (is_object($product_duplicated)) {
                        print "  product_duplicated: " . $product_duplicated->_id . "\n";
                        if (count(explode('|', $product->category_ids)) > 3) {
                            print "    delete product_duplicated: " . $product_duplicated->_id . "\n";
                            $product_duplicated->delete();
                        } else {
                            print "    delete product_duplicated: " . $product->_id . "\n";
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
