<?php

namespace Modules\WebBill\Console;

use Illuminate\Console\Command;
use Modules\WebBill\Console\Website\Asitevn;
use Modules\WebBill\Console\Website\Bizhostvncom;
use Modules\WebBill\Console\Website\CrawlProductBase;
use Modules\WebBill\Console\Website\MauwebsitedepNet;
use Modules\WebBill\Console\Website\ThaibinhwebNet;
use Modules\WebBill\Console\Website\Webrtvn;
use Modules\WebBill\Console\Website\AnbaowebCom;


class CrawlWeb extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crawl:web';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl website.';

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
    public function handle($website_id = false, $test = false)
    {
//        $crawler = new Bizhostvncom(null);
       $crawler = new Webrtvn(null);
//        $crawler = new Asitevn(null);
//        $crawler = new MauwebsitedepNet(null);
        // $crawler = new ThaibinhwebNet(null);
       $crawler = new AnbaowebCom(null);
        $result = $crawler->crawlPageList($test);
        print "Xong!" . "\n";
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
