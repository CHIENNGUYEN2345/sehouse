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

class AnbaowebCom extends CrawlProductBase
{

//    protected $tags = '[{"value":"Điện máy"}]';
    protected $tags = null;


//    protected $cat_name = 'Bán hàng';
//    protected $cat_link = 'https://webrt.vn/danhmuc/ban-dien-lanh-dien-may-dien-dan-dung/';

    protected $cat_name = 'Bán hàng';
    protected $cat_link = 'https://anbaoweb.com/mau-website/du-an-thiet-ke-web-noi-bat/';

    // protected $cat_name = 'Bán hàng';
    // protected $cat_link = 'https://webrt.vn/danhmuc/landingpage/';
// 
   // protected $cat_name = 'Công ty';
   // protected $cat_link = 'https://webrt.vn/danhmuc/son/';

   // protected $cat_name = 'Thời trang, quần áo';
   // protected $cat_link = 'https://webrt.vn/danhmuc/trang-suc/';

   // protected $cat_name = 'Mỹ phẩm';
   // protected $cat_link = 'https://webrt.vn/danhmuc/my-pham/';

   // protected $cat_name = 'Thời trang, quần áo';
   // protected $cat_link = 'https://webrt.vn/danhmuc/thoi-trang-quan-ao/';

   // protected $cat_name = 'Thời trang, quần áo';
   // protected $cat_link = 'https://webrt.vn/danhmuc/thoi-trang/';

   // protected $cat_name = 'Công ty';
   // protected $cat_link = 'https://webrt.vn/danhmuc/web-gioi-thieu-ket-hop-ban-hang/';
//
//    protected $cat_name = 'Bất động sản';
//    protected $cat_link = 'https://bizhostvn.com/bat-dong-san/';
//
   // protected $cat_name = 'Nội thất & Nhà cửa';
   // protected $cat_link = 'https://webrt.vn/danhmuc/web-mau-noi-that/';

   // protected $cat_name = 'Nội thất & Nhà cửa';
   // protected $cat_link = 'https://webrt.vn/danhmuc/thiet-ke-thi-cong-sua-chua-noi-that/';
//
   // protected $cat_name = 'Tin tức';
   // protected $cat_link = 'https://webrt.vn/danhmuc/web-tin-tuc-gioi-thieu/';
// 
//    protected $cat_name = 'Tin tức';
//    protected $cat_link = 'https://webrt.vn/danhmuc/web-xuat-khau-lao-dong/';
//
   // protected $cat_name = 'Ô tô & Xe máy';
   // protected $cat_link = 'https://webrt.vn/danhmuc/web-o-to/';
//
   // protected $cat_name = 'Du lịch & Nghỉ dưỡng';
   // protected $cat_link = 'https://webrt.vn/danhmuc/web-du-lich-khach-san/';
//
   // protected $cat_name = 'Bất động sản';
   // protected $cat_link = 'https://webrt.vn/danhmuc/web-bat-dong-san/';

   // protected $cat_name = 'Xây dựng';
   // protected $cat_link = 'https://webrt.vn/danhmuc/web-xay-dung/';

   // protected $cat_name = 'Nhà hàng & Quán ăn';
   // protected $cat_link = 'https://webrt.vn/danhmuc/nha-hang/';

   // protected $cat_name = 'Giáo dục';
   // protected $cat_link = 'https://webrt.vn/danhmuc/web-du-hoc/';
// 
   // protected $cat_name = 'Giáo dục';
   // protected $cat_link = 'https://webrt.vn/danhmuc/giao-duc/';

   // protected $cat_name = 'Spa, làm đẹp';
   // protected $cat_link = 'https://webrt.vn/danhmuc/web-my-pham-lam-dep/';

   // protected $cat_name = 'Spa, làm đẹp';
   // protected $cat_link = 'https://webrt.vn/danhmuc/spa/';

   // protected $cat_name = 'In ấn';
   // protected $cat_link = 'https://webrt.vn/danhmuc/in-an/';

//    protected $cat_name = 'Bảo hiểm';
//    protected $cat_link = 'https://webrt.vn/danhmuc/web-bao-hiem/';

   // protected $cat_name = 'Thực phẩm';
   // protected $cat_link = 'https://webrt.vn/danhmuc/thuc-pham/';

//    protected $cat_name = 'Tài chính';
//    protected $cat_link = 'https://webrt.vn/danhmuc/web-vay-tien-nhanh/';

//    protected $cat_name = 'Thuốc, Y dược';
//    protected $cat_link = 'https://webrt.vn/danhmuc/web-nha-thuoc-phong-kham/';

   // protected $cat_name = 'Gia dụng - Tạp hóa';
   // protected $cat_link = 'https://webrt.vn/danhmuc/hoa-chat/';

       // protected $cat_name = 'Gia dụng - Tạp hóa';
   // protected $cat_link = 'https://webrt.vn/danhmuc/web-dien-tu-dien-lanh/';
    

    public function crawlPageList($test = false)
    {
        $result = [
            'total_created' => 0,
            'total_updated' => 0
        ];

        $i = 0;
        $stop = false;
        $link_old = '';
        while (!$stop) {
            $i++;
            $page_list_link = $this->cat_link . 'page/'.$i.'/';
            print "Page link: " . $page_list_link . "\n";

            //  Thực hiện quét các danh mục đã cấu hình

            $html = file_get_html($page_list_link);

            $products_find = $html->find('.elementor-post');

            //  Nếu không tìm thấy sản phẩm nào thì dừng lại
            if ($products_find == null || empty($products_find)) {

                return true;
            }

            foreach ($products_find as $k => $product) {

                try {

                    //  Lấy link sản phẩm
                    $product_link = $product->find('a.elementor-post__thumbnail__link', 0);

                    if ($product_link == null) {
                        return false;
                    }
                    $product_data['link'] = $product_link = $product_link->getAttribute('href');

                    //   Lấy link web demo ở trang con
                    $html2 = file_get_html($product_link);

                    if ($html2->find('.elementor-column-wrap .elementor-widget-button:nth-child(1) a', 0) != null) {
                        $product_data['link'] = $html2->find('.elementor-button-link', 0)->getAttribute('href');
                        $product_data['link'] = $product_link = @explode('?link=', $product_data['link'])[1];
                        
                    }

                    //  Nếu chưa tồn tại lưu nhớ link sản phẩm đầu thì tạo lưu nhớ cho link sản phẩm đầu tiên lấy được
                    if (!isset($product_first_link)) {
                        $product_first_link = $product_link;
                    } else {
                        //  Nếu link sản phẩm này trùng với link sản phẩm đầu tiên lấy được tức là đang bị chạy vòng tròn lặp lại sẽ dừng chạy
                        if ($product_link == $product_first_link) {
                            $stop = true;
                            return true;
                        }
                    }

                    if ($product_link != $link_old) {

                        $link_old = $product_link;

                        //  Kiểm tra trong db xem đã crawl sản phẩm này chưa
                        $product_exist = Codes::where('link', $product_data['link'])->first();

                        if (is_object($product_exist)) {
                            if ($product->find('.elementor-post__thumbnail img', 0) != null) {
                                $product_data['image'] = $product->find('.elementor-post__thumbnail img', 0)->getAttribute('srcset');
                                if ($product_data['image'] == null) {
                                    $product_data['image'] == $product->find('.elementor-post__thumbnail img', 0)->getAttribute('src');
                                }
                                $product_data['image'] = @explode(' ', $product_data['image'])[0];
                            }
//                            dd($product_data);
                            print "        => trùng product  id= " . $product->id . "\n";
                            // $this->updateProduct($product_exist, false, $product_data);
                        } else {
                            //  Chưa thì tạo mới
                            if ($product->find('.elementor-post__thumbnail img', 0) != null) {
                                $product_data['image'] = $product->find('.elementor-post__thumbnail img', 0)->getAttribute('srcset');
                                if ($product_data['image'] == null) {
                                    $product_data['image'] == $product->find('.elementor-post__thumbnail img', 0)->getAttribute('src');
                                }
                            }
                            $product_data['image'] = @explode(' ', $product_data['image'])[0];
                            if ($product->find('.elementor-post__title a', 0) != null) {
                                $product_data['name'] = $product->find('.elementor-post__title a', 0)->innertext;
                                $product_data['name'] = preg_replace( "/\r|\n/", "", $product_data['name'] );
                                $product_data['name'] = trim($product_data['name']);
                            }

                            $cat_doom = false;
                            $prd = $this->createProduct($cat_doom, $product_data);
                            $result['total_created']++;
                        }
                    } else {
                        $stop = true;
                        print "$product_link == $link_old" . "\n";
                    }
                } catch (\Exception $ex) {
                    print $ex->getLine() . ' : ' . $ex->getMessage();
                    Error::create([
                        'module' => 'WebBill',
                        'message' => $ex->getLine() . ' : ' . $ex->getMessage(),
                        'file' => $ex->getFile(),
                        'code' => $this->_domain
                    ]);
                }
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
        return $product;
    }

    public function updateProduct($product, $cat_doom, $product_data) {

        if ($product->image == '0') {
            $product->image = CommonHelper::saveFile($product_data['image'], 'codes_crawl/' . date('Y/m/d'));
            $product->save();
            return $product;
        }

//        $this->setCategoryCodes($product);
//        $product->tags = $this->tags;
        $product->save();
        print "        => update product  id= " . $product->id . "\n";
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
        // dd($product_data);
        $code->type = '|'.$this->cat_name.'|';
        $code->admin_id = 1;
        $code->status = 1;
        $code->source = '|wordpress|';
        $code->owned = 'anbaoweb.com';
        $code->price_setup = 1500000;
        $code->tags = $this->tags;
        $code->save();

        $this->setCategoryCodes($code);

        print "        => Create product " . $code->id . ':' . $code->name . "\n";
        return $code;
    }
}
