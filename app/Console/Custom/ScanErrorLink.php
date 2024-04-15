<?php

namespace App\Console\Custom;

use App\Models\Setting;
use Illuminate\Console\Command;
use App\CRMDV\Controllers\Helpers\CommonHelper;
use App\CRMDV\Models\Bill;
use App\CRMDV\Models\LinkErrorLogs;
use App\CRMDV\Models\BillProgress;

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
        print "- Bắt đầu chạy\n";
        $stop = false;
        $i = 0;
        $take = 100;
        $log_ids = [];
        $settings = Setting::where('name', 'links')->pluck('value', 'name')->toArray();
        while (!$stop) {

            //  Không lấy các dự án đang triển khai
            $bill_trang_trien_khai_ids = BillProgress::whereIn('status', [
                                            'Thu thập YCTK L1',
                                            'Triển khai L1',
                                            'Nghiệm thu L1 & thu thập YCTK L2',
                                            'Triển khai L2',
                                            'Nghiệm thu L2 & thu thập YCTK L3',
                                            'Triển khai L3',
                                            'Nghiệm thu L3 & thu thập YCTK L4',
                                            'Triển khai L4',
                                            'Nghiệm thu L4 & thu thập YCTK L5',
                                            'Triển khai L5',
                                            'Nghiệm thu L5 & thu thập YCTK L6',
                                            'Triển khai L6',
                                            'Tạm dừng',
                                            'Bỏ',
                                            null,
                                        ])
                                    ->pluck('bill_id')->toArray();


            $bills = Bill::select('domain', 'id', 'registration_date')
                            ->where('status', 1)     // lấy hđ đang kich hoạt
                            ->whereNotNull('domain')    //  lấy hđ có điền tên miền
                            ->where('domain', 'LIKE', '%.%')    //  lấy hđ có tên miền có dấu .
                            ->whereNotIn('service_id', [    //   ko lấy tên miền ở các  hợp đồng cho các dịch vụ sau:
                                4,  //  dv mail
                                7,  //  dv duy trì
                                8,  // dv nâng cấp hosting
                                9,  // dv nâng cấp web

                            ])
//                            ->where('total_price_contract', 'total_received')  // quét các dự án đã thu hết tiền
                            ->whereNotIn('id', $bill_trang_trien_khai_ids)  // ko lấy các dự án đang triển khai
                            ->orderBy('id', 'desc')->groupBy('domain')->skip($i)->take($take)
                            ->get();

            print "skip " . $i . " - take $take\n";

            if(count($bills) == 0) {
                $stop = true;
                print "count(\$bills)  = 0" . "\n";
            } else {
                $i += $take;
                $arr = [];
                foreach ($bills as $bill) {
                    print "domain: ".$bill->domain."\n";
                    if (strpos($bill->domain, 'http') === false) {
                        $arr[] = [
                            'link' => 'https://' . $bill->domain,
                            'id' => $bill->id,
                            'registration_date' => $bill->registration_date,
                            'time_scan' => null,
                            'domain_id' => null,
                        ];
                    }
                }

                //  Lấy thêm các link cấu hình thêm trong cấu hình
                foreach (preg_split('/\n|\r\n?/', $settings['links']) as $link) {
                    $arr[] = (object) [
                            'link' => 'https://' . $bill->domain,
                            'id' => null,
                            'registration_date' => null
                        ];
                }

                $result = CommonHelper::check_link_run($arr);
                if ($result['status']) {
                    $log_ids = array_merge($log_ids, $result['data']);
                }
            }
        }

        //  Xoá các log cũ
        print "Xoá log từ 7 ngày trước\n";
        LinkErrorLogs::where('created_at', '<', date('Y-m-d', strtotime(" -7 days")))->delete();

        //  Gửi email thông báo các link lỗi
        CommonHelper::send_mail_notifications($log_ids);

        print "Xong!\n";
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
