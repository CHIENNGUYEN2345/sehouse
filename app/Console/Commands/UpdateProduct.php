<?php

namespace App\Console\Commands;

use App\Console\Commands\Website\CrawlWebsiteBase;
use App\Models\Product;
use App\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UpdateProduct extends Command
{

    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updatedata:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl du lieu products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CrawlWebsiteBase $crawler)
    {
//        $crawler->sendEmailChangeProductPrice(1000000, Product::first()); dd('f');
        $paginate = 10;
        $websites = Website::select(['name', '_id', 'doom'])->where('status', 1)->get();
        foreach ($websites as $website) {
            print "Update website ".$website->name."\n";
            $flag = true;
            $i = 0;
            while ($flag) {
                $products = Product::where('status', 1)->where('website_id', $website->_id)->skip($i)->take($paginate)->get();
                if(count($products) > 0) {
                    foreach ($products as $product) {
                        $crawler->updateProducts($website, $product);
                    }
                    $i += $paginate;
                } else {
                    $flag = false;
                }
            }
        }
        print "Hoàn Tất!\n";
    }
}
