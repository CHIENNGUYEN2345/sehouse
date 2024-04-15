<?php

namespace Modules\LandingPage\Console;

use App\Models\Setting;
use Illuminate\Console\Command;
use Modules\STBDAutoUpdatePriceWSS\Entities\LogUpdateProductPrice;
use Modules\STBDAutoUpdatePriceWSS\Entities\Product;

class Tool extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tool:product-price';

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
//        require_once base_path('app/Console/Commands/simple_html_dom.php');
//        ini_set("user_agent", "Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0");

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle($link)
    {
        $html = file_get_html($link);

        dd($html);
    }

    public function checkIgnore($settings, $product) {
        $ignore = preg_split('/\n|\r\n?/', @$settings['ignore']);
        foreach($ignore as $in) {
            if ($in != '') {
                if (strpos(strtolower($product->name), strtolower($in)) !== false || strpos(strtolower($product->slug), strtolower($in)) !== false) {
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
