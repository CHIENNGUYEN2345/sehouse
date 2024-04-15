<?php

namespace App\Console\Commands;

use App\Console\Commands\Website\CrawlWebsiteBase;
use App\Models\Product;
use App\Models\Website;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CrawlProduct extends Command
{

    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawldata:products';

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
        $websites = Website::with(['products'=>function($query){
            $query->select(['_id', 'name', 'link', 'website_id']);
        }])->select(['name', '_id', 'doom'])->where('status', 1)->orderBy('created_at', 'desc')->get();
        foreach ($websites as $website) {
            print "Crawl website ".$website->name."\n";
            $crawler->crawlProducts($website);

        }
        print "Hoàn Tất!\n";
    }
}
