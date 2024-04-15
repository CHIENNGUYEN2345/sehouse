<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\BackupController;
use App\Http\Helpers\CommonHelper;
use Illuminate\Console\Command;

class BackUpDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sao luu co so du lieu';

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
        $backupDB = new BackupController();
        $result = $backupDB->BackUpDB();
        echo $result['msg'];
    }
}
