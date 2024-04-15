<?php

namespace Modules\WebBill\Console\Website;

use App\Http\Helpers\CommonHelper;
use App\Models\Error;
use Mail;
use Modules\WebBill\Entities\Manufacturer;
use Modules\WebBill\Entities\Origin;
use Modules\WebBill\Entities\PropertieName;
use Modules\WebBill\Entities\PropertieValue;
use Modules\WebBill\Models\Category;
use Modules\WebBill\Models\Codes;
use Session;

class Bizhostvncom extends CrawlProductBase
{

//    protected $cat_name = 'Bán hàng';
//    protected $cat_link = 'https://bizhostvn.com/ban-hang/';

//    protected $cat_name = 'Công ty';
//    protected $cat_link = 'https://bizhostvn.com/cong-ty/';
//
//    protected $cat_name = 'Bất động sản';
//    protected $cat_link = 'https://bizhostvn.com/bat-dong-san/';
//
//    protected $cat_name = 'Nội thất & Nhà cửa';
//    protected $cat_link = 'https://bizhostvn.com/noi-that/';
//
//    protected $cat_name = 'Tin tức';
//    protected $cat_link = 'https://bizhostvn.com/tin-tuc/';
//
//    protected $cat_name = 'Ô tô & Xe máy';
//    protected $cat_link = 'https://bizhostvn.com/oto-xe-may/';
//
//    protected $cat_name = 'Du lịch & Nghỉ dưỡng';
//    protected $cat_link = 'https://bizhostvn.com/du-lich/';
//
//    protected $cat_name = 'Bất động sản';
//    protected $cat_link = 'https://bizhostvn.com/khach-san/';

//    protected $cat_name = 'Nhà hàng & Quán ăn';
//    protected $cat_link = 'https://bizhostvn.com/nha-hang/';

    protected $cat_name = 'Giáo dục';
    protected $cat_link = 'https://bizhostvn.com/giao-duc/';

    public function crawlPageList($test = false)
    {
        $result = [
            'total_created' => 0,
            'total_updated' => 0
        ];

        //  Thực hiện quét các danh mục đã cấu hình

        $page_list_link = $this->cat_link;
        $html = file_get_html($page_list_link);

        $products_find = $html->find('.website-apps-item');
        $link_old = '';

        //  Nếu không tìm thấy sản phẩm nào thì dừng lại
        if ($products_find == null || empty($products_find)) {

            return true;
        }

        foreach ($products_find as $k => $product) {

            try {

                //  Lấy link sản phẩm
                $product_link = $product->find('a', 0);

                if ($product_link == null) {
                    return false;
                }
                $product_link = $product_link->getAttribute('href');

                $html2 = file_get_html($product_link);

                if ($html2->find('iframe', 0) != null) {
                    $product_data['link'] = $html2->find('iframe', 0)->getAttribute('src');
                    $product_data['link'] .= '/';
                }

                //  Nếu chưa tồn tại lưu nhớ link sản phẩm đầu thì tạo lưu nhớ cho link sản phẩm đầu tiên lấy được
                if (!isset($product_first_link)) {
                    $product_first_link = $product_link;
                } else {
                    //  Nếu link sản phẩm này trùng với link sản phẩm đầu tiên lấy được tức là đang bị chạy vòng tròn lặp lại sẽ dừng chạy
                    if ($product_link == $product_first_link) {
                        return true;
                    }
                }

                if ($product_link != $link_old) {

                    $link_old = $product_link;

                    //  Kiểm tra trong db xem đã crawl sản phẩm này chưa
                    $product_exist = Codes::where('link', $product_data['link'])->first();


                    if (is_object($product_exist)) {
                        $this->updateProduct($product_exist, false, $product_data);
                        print "is_object(".$product_data['link'].")" . "\n";
                    } else {
                        //  Chưa thì tạo mới
                        if ($product->find('.website-apps-item-img', 0) != null) {
                            $product_data['image'] = $product->find('.website-apps-item-img', 0)->getAttribute('style');
                            $product_data['image'] = str_replace('background-image: url(', '', $product_data['image']);
                            $product_data['image'] = str_replace(');', '', $product_data['image']);
                        }
                        if ($product->find('.website-apps-item-text i.width-70', 0) != null) {
                            $product_data['name'] = $product->find('.website-apps-item-text i.width-70', 0)->innertext;
                            $product_data['name'] = preg_replace( "/\r|\n/", "", $product_data['name'] );
                            $product_data['name'] = trim($product_data['name']);
                        }

                        $cat_doom = false;
                        $prd = $this->createProduct($cat_doom, $product_data);
                        $result['total_created']++;
                    }
                } else {
                    print "$product_link == $link_old" . "\n";
                }
            } catch (\Exception $ex) {
                Error::create([
                    'module' => 'WebBill',
                    'message' => $ex->getLine() . ' : ' . $ex->getMessage(),
                    'file' => $ex->getFile(),
                    'code' => $this->_domain
                ]);
            }
        }

        return $result;
    }

    public function setCategoryCodes($product) {
        $cat = Category::where('name', $this->cat_name)->first();
        if (!is_object($cat)) {
            $cat = new Category();
            $cat->name = $this->cat_name;
            $cat->slug = str_slug($this->cat_name, '-');
            $cat->save();
        }

        if (strpos($product->multi_cat, '|'.$cat->id.'|') === false) {
            if (substr($product->multi_cat, -1) == '|') {
                $product->multi_cat .= $cat->id . '|';
            } else {
                $product->multi_cat = '|' . $cat->id . '|';
            }
            $product->save();
        }
        print "        => update product  cat_id " . $cat->id . "\n";
    }

    public function updateProduct($product, $cat_doom, $product_data) {
        $this->setCategoryCodes($product);


//        $product->type = $product->type . $this->cat_name.'|';
//        $product->save();
        return $product;
    }

    public function createProduct($cat_doom, $product_data)
    {
        $code = new Codes();
        if (isset($product_data['image']) && $product_data['image'] != '') {
            $product_data['image'] = CommonHelper::saveFile($product_data['image'], 'codes_crawl/' . date('Y/m/d'));
        }

        foreach ($product_data as $k => $v) {
            $code->{$k} = $v;
        }
        $code->type = '|'.$this->cat_name.'|';
        $code->admin_id = 1;
        $code->source = '|wordpress|';
        $code->owned = 'bizhostvn.com';
        $code->save();

        $this->setCategoryCodes($code);


        print "        => Create product " . $code->id . ':' . $code->name . "\n";
        return $code;
    }
}
