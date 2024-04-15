<?php

namespace Modules\CheckErrorLink\Console;

use Illuminate\Console\Command;
use Modules\CheckErrorLink\Models\DomainCheck;
use Modules\CheckErrorLink\Models\LinkCheck;

class ScanErrorLink extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'link:check';

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
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $list_domain = DomainCheck::where('status', 1)->pluck('id')->toArray();
        $linkchecks = LinkCheck::whereIn('domain_id', $list_domain)->where('status', 1)->get();
        \Modules\CheckErrorLink\Http\Helpers\CommonHelper::check_link_run($linkchecks);
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
