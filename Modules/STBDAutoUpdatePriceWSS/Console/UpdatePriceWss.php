<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console;

use App\Models\Setting;
use Illuminate\Console\Command;
use Modules\STBDAutoUpdatePriceWSS\Entities\LogUpdateProductPrice;
use Modules\STBDAutoUpdatePriceWSS\Entities\Product;
use Modules\STBDAutoUpdatePriceWSS\Entities\UpdateWssProductsLog;


class UpdatePriceWss extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'update:product-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quet link loi.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        require_once base_path('app/Console/Commands/simple_html_dom.php');
        ini_set("user_agent", "Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0");

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle($website_id = false, $test = false)
    {
        //  Xoá bản ghi log cũ quá 2 ngày trước
        UpdateWssProductsLog::where('created_at', '<', date('Y-m-d 23:59:00', strtotime(" -2 days")))->delete();

        $log = new LogUpdateProductPrice();
        $log->save();

        $error = [];

        $settings = Setting::where('type', 'update_price_product_tab')->pluck('value', 'name')->toArray();

        $page = 0;
        $stop = false;
        $merchantId = @$settings['merchant_id'];      //  6357043277263234663
        $log_price = '';
        while (!$stop) {
            $page++;
            $data = file_get_contents('https://websosanh.vn/MerchantCms/ProductSearchWithCompanyByFilter?merchantId=' . $merchantId . '&cat=0&pageIndex=' . $page . '&pageSize=20&keyword=&sortExpr=ViewCount&sortDir=DESC&isOriginalt=1');
            $data = json_decode($data);


            //  Nếu hết phân trang thì dừng
            if ($data->status == 0) {
                $stop = true;
                break;
            } else {
                $data = json_decode($data->data);
                if (empty($data)) {
                    $stop = true;
                    break;
                }

                foreach ($data as $item) {
                
                    //  Truy vấn ra sản phẩm
                    $prd_url = $item->DetailUrl;
                    $prd_url = str_replace('.html', '', $prd_url);
                    $prd_url = explode('/', $prd_url)[count(explode('/', $prd_url)) - 1];


//  khai bao
                    // $item->UrlCompare = 'https://websosanh.vn/may-rua-bat-bosch-sms4evi14e/2126162847/so-sanh.htm';
                    // $prd_url = 'bosch-sms4evi14e';



                    $product = Product::where('slug', $prd_url)->first();

                    $log_update_product = new UpdateWssProductsLog();
                    $log_update_product->url = $prd_url;
                    $log_update_product->product_id = @$product->id;
                    $log_update_product->log = '';
                    $log_update_product->url_compare = $item->UrlCompare;
                    
                    if ($item->UrlCompare == '') {
                        //  nếu link lỗi thì tăng giá lên
                        if (@$settings['link_error_plus_mn'] != '') {
                            $product->final_price = $product->final_price + (int) $settings['link_error_plus_mn'];
                            $product->save();
                        }
                        $error[] = [
                            'link' => @$item->DetailUrl,
                            'msg' => '1. Không có link so sánh'
                        ];
                        $log_update_product->log .= '||| ' . json_encode($error);
                    } else {
                        try {

                            //  Lấy html trang so sánh
                            $html = file_get_html($item->UrlCompare);
                            $top = @$settings['top'];
                            // dd($top);

                            //  Lấy vị trí sp đang đứng ở top muốn đẩy sp mình lên đó
                            $prd = $html->find('.compare-wrap .compare-item', ($top - 1));
                            
                            if ($prd != null) {
                                //  Kiểm tra xem nếu vị trí đó mình chưa ở đó thì mới update giá

                                // lấy ra url
                                $web_link = $prd->find('.store-item', 0);
                                
                                if ($web_link != null) {

                                    if (strpos($web_link->getAttribute('href'), $settings['merchant_id']) === false) {

                                        $price = $prd->find('.compare-product-price', 0);
                                        $price = $this->cleanPrice($price->innertext);
                                        // dd($price);
                                        $discount = @$settings['discount'];

                                        $price_new = $price - $discount;
                                        // dd($prd->find('.compare-name', 0)->innertext, $price, $price_new);

                                        $log_update_product->old_price = $product->final_price;
                                        $log_update_product->price_new = $price_new;

                                        //  Kiểm tra nếu tên hoặc url ko chứa từ khóa ignore thì chạy tiếp
                                        if ($this->checkIgnore($settings, $product)) {
                                            $old_price = $product->final_price;
                                            $product->final_price = $price_new;
                                            $product->link_wss = $product->link_wss ? $product->link_wss : $item->UrlCompare;
                                            /*if ($product->base_price < $price_new) {
                                                $product->base_price = $price_new;
                                            }*/
                                            $product->save();
                                            $log_price .= 'Product_id: ' . $product->id . ' | Giá cũ: ' . number_format($old_price) . ' | Giá mới: ' . number_format($product->final_price) . ' | Gía wss: ' . number_format($price) . '<hr>';
                                            // dd($log_price);
                                        } else{
                                            // dd('checkIgnore');
                                            $log_update_product->log .= '||| checkIgnore';
                                        }
                                    }
                                } else {
                                    $log_update_product->log .= '||| $web_link == null';
                                }
                            } else {
                                $error[] = [
                                    'link' => @$item->DetailUrl,
                                    'msg' => '2. Không tìm thấy sản phẩm trong top set'
                                ];
                                $log_update_product->log .= '|||' . json_encode($error);
                            }
                        } catch (\Exception $ex) {
                            // dd($ex->getMessage());
                            $error[] = [
                                'link' => @$item->DetailUrl,
                                'msg' => '3.' .$ex->getMessage()
                            ];
                            $log_update_product->log .= '|||' . json_encode($error);
                        }
                    }

                    $log_update_product->save();
                }
            }
        }
        $log->links_error = json_encode($error);
        $log->end = date('Y-m-d H:i:s');
        $log->log_price = $log_price;
        $log->save();

        die('xong!');
    }

    public function checkIgnore($settings, $product) {
        $ignore = preg_split('/\n|\r\n?/', @$settings['ignore']);
        foreach($ignore as $in) {
            if ($in != '') {
                if (strpos(strtolower($product->name), strtolower($in)) !== false || strpos(strtolower($product->slug), strtolower($in)) !== false) {
                    // dd($product->id, strtolower($product->name), strtolower($product->slug), strtolower($in) );
                     return false;
                }
            }
        }
        return true;
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
                if (preg_match('/[0-9]|[0-9]/', $item)) {
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
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
//            ['check', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
//            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}


