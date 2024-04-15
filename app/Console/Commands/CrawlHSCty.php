<?php

namespace App\Console\Commands;

use App\Console\Commands\Website\CrawlHSCtyBase;
use App\Console\Commands\Website\CrawlWebsiteBase;
use App\Models\Product;
use App\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CrawlHSCty extends Command
{

    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawldata:ho_so';

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
    public function handle(CrawlHSCtyBase $crawler)
    {

        $province_id = 2;
        $link = 'https://hosocongty.vn/p/ha-noi/page-{i}';

//        $province_id = 1;
//        $link = 'https://hosocongty.vn/p/ho-chi-minh/page-{i}';

        $crawler->crawlCtyInTinh($province_id, $link);
        print "Hoàn Tất!\n";
    }
}
