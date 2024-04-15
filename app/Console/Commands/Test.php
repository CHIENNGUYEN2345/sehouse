<?php

namespace App\Console\Commands;

use App\Console\Commands\Website\CrawlWebsiteBase;
use App\Models\Product;
use App\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Test extends Command
{

    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

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
        /*$crawler->removeDuplicated();
        die('xong');

        //  Crawl
        $websites = Website::with(['products'=>function($query){
            $query->select(['_id', 'name', 'link', 'website_id']);
        }])->select(['name', '_id', 'doom'])->where('name', 'https://ivymoda.com/')->where('status', 1)->orderBy('created_at', 'desc')->get();
        foreach ($websites as $website) {
            print "Crawl website ".$website->name."\n";
            $crawler->crawlProducts($website);

        }
        print "Hoàn Tất!\n";*/


        //  Crawl   products
        /*$websites = Website::with(['products'=>function($query){
            $query->select(['_id', 'name', 'link', 'website_id']);
        }])->select(['name', '_id', 'doom'])->where('status', 1)->orderBy('created_at', 'desc')->get();
        foreach ($websites as $website) {
            print "Crawl website ".$website->name."\n";
            $crawler->crawlProducts($website);

        }
        print "Hoàn Tất!\n";*/

//  update 1 san pham
        $product = Product::find('5b54078786c0c16179581b52');
        $website = Website::find($product->website_id);

        $crawler->updateProducts($website, $product);
        die('ok');


//  Update website
        $paginate = 10;
        $websites = Website::select(['name', '_id', 'doom'])->where('status', 1)->where('name', 'http://lemino.vn/')->get();
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
