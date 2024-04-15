<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use Mail;
use App\Http\Helpers\CommonHelper;
use Modules\STBDAutoUpdatePriceWSS\Entities\Manufacturer;
use Modules\STBDAutoUpdatePriceWSS\Entities\Origin;
use Modules\STBDAutoUpdatePriceWSS\Entities\Product;
use Modules\STBDAutoUpdatePriceWSS\Entities\PropertieName;
use Modules\STBDAutoUpdatePriceWSS\Entities\Guarantees;
use Modules\STBDAutoUpdatePriceWSS\Entities\PropertieValue;
use Session;

class CrawlProductBase extends Base
{
    protected $_website;
    protected $_domain;
    protected $_doom_setting;
    protected $module = [
        'code' => 'product',
        'table_name' => 'products',
        'label' => 'Sản phẩm',
        'modal' => '\Modules\STBDAutoUpdatePriceWSS\Entities\Product',
    ];

    function __construct($website)
    {
        parent::__construct();

        $this->_website = $website;

        //  Lấy tên miền website
        $this->_domain = @explode('//', $website->domain)[1];
        $this->_domain = preg_replace('/\//', '', $this->_domain);

        $this->_doom_setting = json_decode($website->doom);

        /*if (Session::get('login_KhosachnoiCom') == null) {
            $this->loginSystem();
            Session::put('login_KhosachnoiCom', true);
        }*/
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

    public function crawlPageList($test = false)
    {

        //  Lấy cấu hình doom
        $doom_setting = json_decode($this->_website->doom);

        //  Thực hiện quét các danh mục đã cấu hình
        foreach ($this->_website->categories as $cat_doom) {
            $i = 0;
            $stop = false;
            while (!$stop) {
                $i++;

                $page_list_link = $this->getPageListLink($cat_doom, $doom_setting, $i);
                $html = file_get_html($page_list_link);

                $products_find = $html->find($doom_setting->target);
                $link_old = '';

                //  Nếu không tìm thấy sản phẩm nào thì dừng lại
                if ($products_find == null || empty($products_find)) {
                    $stop = true;
                    break;
                }

                foreach ($products_find as $k => $product) {
                    //  Lấy link sản phẩm
                    $product_link = $product->find($doom_setting->link, 0)->getAttribute('href');
                    $product_link = $this->attachDomainToLink($product_link);

                    //  Nếu chưa tồn tại lưu nhớ link sản phẩm đầu thì tạo lưu nhớ cho link sản phẩm đầu tiên lấy được
                    if (!isset($product_first_link)) {
                        $product_first_link = $product_link;
                    } else {
                        //  Nếu link sản phẩm này trùng với link sản phẩm đầu tiên lấy được tức là đang bị chạy vòng tròn lặp lại sẽ dừng chạy
                        if ($product_link == $product_first_link) {
                            $stop = true;
                            break;
                        }
                    }

                    if ($product_link != $link_old) {
                        $link_old = $product_link;

                        //  Kiểm tra trong db xem đã crawl sản phẩm này chưa
                        $product_exist = Product::where('crawl_link', $product_link)->first();

                        //  Đã có thì  cập nhật - chưa thì tạo mới
                        if (is_object($product_exist)) {
                            $product_data = $this->getDataProduct($product_link);
                            $product_data['crawl_link'] = $product_link;
                            $product_data = $this->cleanData($product_data);
                            $product_data = $this->appendData($product_data);
                            if ($test) {
                                return $this->printDemo($product_data);
                            }
                            $this->updateProduct($product_exist, $product_data);

                        } else {
                            $product_data = $this->getDataProduct($product_link);
                            $product_data['crawl_link'] = $product_link;

                            $product_data = $this->cleanData($product_data);
                            $product_data = $this->appendData($product_data);
                            if ($test) {
                                return $this->printDemo($product_data);
                            }
                            $prd = $this->createProduct($cat_doom, $product_data);
                        }
                    }
                }
            }
        }
    }

    /**
     * Lấy link danh sách sản phẩm
    */
    public function getPageListLink($cat_doom, $doom_setting, $i) {
        return $cat_doom->link_crawl . str_replace('{i}', $i, $doom_setting->category_pagination);
    }

    /**
     * Hiển thị ra màn hình dữ liệu demo sản phẩm
     */
    public function printDemo($data)
    {
//        dd($data);
        $key_name = [
            'crawl_link' => '<strong>Link sản phẩm:</strong> ',
            'name' => '<strong>Tên:</strong> ',
            'code' => '<strong>Mã:</strong> ',
            'base_price' => '<strong>Giá cũ:</strong> ',
            'final_price' => '<strong>Giá bán:</strong> ',
            'image' => '<strong>Ảnh đại diện:</strong> ',
            'image_extra' => '<strong>Ảnh khác:</strong> ',
            'intro' => '<strong>Mô tả:</strong> ',
            'content' => '<strong>Nội dung:</strong> ',
            'highlight' => '<strong>Nội dung:</strong> ',
            'proprerties_id' => '<strong>Thuộc tính: </strong>',
            'manufacture_id' => '<strong>Hãng: </strong>',
            'origin_id' => '<strong>Nơi sản xuất: </strong>',
            'guarantee' => '<strong>Bảo hành: </strong>'
        ];
        foreach ($data as $key => $v) {
            echo @$key_name[$key] . '<br>';

            switch ($key) {
                case "crawl_link":
                    echo '<a href="' . $v . '" target="_blank">' . $v . '</a><br>';
                    break;
                case "image":
                    echo '<img src="/public/filemanager/userfiles/' . $v . '" style="width: 150px; height: 150px;"></a><br>';
                    break;
                case "image_extra":
                    foreach (explode('|', $v) as $val) {
                        if ($val != '') {
                            echo '<img src="/public/filemanager/userfiles/' . $val . '" style="width: 150px; height: 150px;"></a>';
                        }
                    }
                    echo '<br>';
                    break;
                case "base_price":
                    print number_format($v, 0, '.', '.') . 'đ<br>';
                    break;
                case "final_price":
                    print number_format($v, 0, '.', '.') . 'đ<br>';
                    break;
                case "intro":
                    print $v . '<br>';
                    break;
                case "content":
                    print $v . '<br>';
                    break;
                case "highlight":
                    print $v . '<br>';
                    break;
                case "proprerties_id":
                    if (is_string($v)) {
                        $v = explode('|', $v);
                        $data = PropertieValue::whereIn('id', $v)->get();
                        foreach ($data as $val) {
                            echo @$val->property_name->name . ': '. @$val->value .'<br> ';
                        }
                        echo '<br>';
                    }
                    break;
                case "manufacture_id":
                    echo @Manufacturer::find($v)->name . '<br>';
                    break;
                case "guarantee":
                    echo @Guarantees::find($v)->name . '<br>';
                    break;
                case "origin_id":
                    echo @Origin::find($v)->name . '<br>';
                    break;
                default:
                    echo $v . '<br>';
            }
        }
        return true;
    }

    public function createProduct($cat_doom, $product_data)
    {
        $product = new Product();
        foreach ($product_data as $k => $v) {
            $product->{$k} = $v;
        }
        $product->multi_cat = '|' . $cat_doom->category_id . '|';
        $product->category_id = $cat_doom->category_id;
        $product->slug = $this->renderSlug(false, $product_data['name']);
        $product->status = 0;
        $product->crawl_updated_at = date('Y-m-d H:i:s');
//        dd($product);
        $product->save();
        print "        => Create product " . $product->id . ':' . $product->name . "\n";
        return $product;
    }

    public function updateProduct($product, $data)
    {
        $product->crawl_updated_at = date('Y-m-d H:i:s');
        $product->save();
        print "        => Updated product " . $product->id . ':' . $product->name . "\n";
        return true;
    }

    /**
     * Lấy thông tin sản phẩm từ link sản phẩm
     */
    public function getDataProduct($product_link)
    {
        $html = file_get_html($product_link);

        $data = [];

        $data = $this->getAttribute($data, $html);

        $data = $this->getName($data, $html);

        $data = $this->getImage($data, $html);

        $data = $this->getImageExtra($data, $html);

        $data = $this->getCode($data, $html);

        $data = $this->getManufacturer($data, $html);

        $data = $this->getOrigin($data, $html);

        $data = $this->getBasePrice($data, $html);

        $data = $this->getFinalPrice($data, $html);

        $data = $this->getIntro($data, $html);

        $data = $this->getContent($data, $html);

        return $data;
    }

    /**
     * Lấy nội dung
    */
    public function getContent($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->content, 0);
        if ($v != null) {
            $data['content'] = trim($v->innertext);
            $data['content'] = $this->saveImgInContent($data['content'], $v, 'product/' . str_slug($data['name']) . '/content');
            $data['content'] = $this->cleanContent($data['content'], $html);
        }
        return $data;
    }

    public function getOrigin($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->origin_name, 0);
        if ($v != null) {
            $data['origin_name'] = trim($v->innertext);
        }
        return $data;
    }

    public function getIntro($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->intro, 0);
        if ($v != null) {
            $data['intro'] = trim($v->innertext);
        }
        return $data;
    }

    public function getFinalPrice($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->final_price, 0);
        if ($v != null) {
            $data['final_price'] = trim($v->innertext);
            $data['final_price'] = $this->cleanPrice($data['final_price']);
        }
        return $data;
    }

    public function getBasePrice($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->base_price, 0);
        if ($v != null) {
            $data['base_price'] = trim($v->innertext);
            $data['base_price'] = $this->cleanPrice($data['base_price']);
        }
        return $data;
    }

    public function getManufacturer($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->manufacturer_name, 0);
        if ($v != null) {
            $data['manufacturer_name'] = trim($v->innertext);
        }
        return $data;
    }

    public function getCode($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->code, 0);
        if ($v != null) {
            $data['code'] = trim($v->innertext);
        }
        return $data;
    }

    /**
     * Lấy ảnh của sản phẩm
     */
    public function getImage($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->image, 0);
        if ($v != null) {
            if ($v->getAttribute('data-src') !== false) {
                $data['image'] = @$v->getAttribute('data-src');
            } else {
                $data['image'] = trim(@$v->getAttribute('src'));
            }

            $data['image'] = explode('?', $data['image'])[0];

            $data['image'] = $this->attachDomainToLink($data['image']);

            $data['image'] = CommonHelper::saveFile($data['image'], 'product/' . str_slug($data['name']));
        }
        return $data;
    }

    public function getName($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->name, 0);
        if ($v != null) {
            $data['name'] = trim(strip_tags($v->innertext));
        }
        return $data;
    }

    /**
     * Lấy ảnh thêm của sản phẩm
     */
    public function getImageExtra($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->image_extra);
        if ($v != null) {
            $image_extra_arr = [];
            foreach ($v as $image_extra) {
                $image_extra_src = trim(@$image_extra->getAttribute('src'));

                $image_extra_src = explode('?', $image_extra_src)[0];

                $image_extra_src = $this->attachDomainToLink($image_extra_src);

                $image_extra_arr[] = CommonHelper::saveFile($image_extra_src, 'product/' . str_slug($data['name']));
            }
            $data['image_extra'] = '|' . implode('|', $image_extra_arr) . '|';
        }
        return $data;
    }

    /**
     * Lấy các thuộc tính của sản phẩm
     */
    public function getAttribute($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->attributes);
//        dd($v);
        if ($v != null) {
            $data['proprerties_id'] = [];
            foreach ($v as $attribute) {
                $name = strip_tags(explode(':', $attribute->innertext)[0]);
                $val = strip_tags(trim(@explode(':', $attribute->innertext)[1]));
                $val = str_replace('&nbsp;', '', $val);
                $data['proprerties_id'][trim($name)] = trim($val);
            }
        }
        return $data;
    }

    /**
     * Lưu ảnh trong phần nội dung về server
    */
    public function saveImgInContent($content, $content_doom, $path = 'uploads/')
    {
        //  Tìm và lấy các thẻ <img
        $img_doom_arr = $content_doom->find('img');
        foreach ($img_doom_arr as $img_doom) {
            $img_doom_src2 = false;

            // lấy link ảnh trong data-src hay trong src
            if ($img_doom->getAttribute('data-src') !== false) {
                $img_doom_src = @$img_doom->getAttribute('data-src');
                $img_doom_src2 = @$img_doom->getAttribute('src');
            } else {
                $img_doom_src = trim(@$img_doom->getAttribute('src'));
            }

            //  Xóa các ký tự thừa trong link ảnh
            $img_doom_src = explode('?', $img_doom_src)[0];

            //  Gắn tên miền vào link ảnh
            $img_src = $this->attachDomainToLink($img_doom_src);

            //  Lưu link ảnh
            $img_src = CommonHelper::saveFile($img_src, $path);

            //  Thay link ảnh mới ở server mình vào link ảnh cũ ở server web nguồn
            $content = str_replace($img_doom_src, '/public/filemanager/userfiles/' . $img_src, $content);
            if ($img_doom_src2) {
                $content = str_replace($img_doom_src2, '/public/filemanager/userfiles/' . $img_src, $content);
            }
        }
        return $content;
    }

    /**
     * Xóa các ký tự thừa trong giá
    */
    public function cleanPrice($price)
    {
        //  Nếu giá tiền có khoảng cách thì chỉ lấy phần chứa chữ số
        $arr = explode(' ', $price);
        if (count($arr) > 1) {
            foreach ($arr as $item) {
                if (preg_match('/[0-9]|[0-9]/', $item))
                {
                    $price = $item;
                }
            }
        }

        //  Xóa các ký tự . , đ chữ trong giá
        $price = strip_tags($price);
        $price = preg_replace('/\./', '', $price);
        $price = preg_replace('/\,/', '', $price);
        $price = preg_replace('/đ/', '', $price);
        $price = preg_replace('/\s+/', '', $price);
        return (int)$price;
    }

    /**
     * Xóa các ký tự thừa trong nội dung
     */
    public function cleanContent($content, $html)
    {

        return $content;
    }

    /**
     * Gắn tên miền vào link
     */
    public function attachDomainToLink($link)
    {
        if (strpos($link, 'http') !== false) {
            return $link;
        }

        if (substr($link, 0, 2) == '//') {
            return 'http:' . $link;
        }

        //  Nếu link ko gắn domain thì gắn vào
        if (strpos($link, $this->_domain) === false) {
            //  Xóa 2 dấu // liên tiếp
            $link = preg_replace('/\/\//', '', $link);

            if (substr($link, 0, 1) == '/') {
                $link = substr($link, 1);
            }

            $link = $this->_website->domain . $link;
        }
        return $link;
    }

    public function cleanData($product_data)
    {
        return $product_data;
    }

    public function appendData($data)
    {
        return $data;
    }
}
