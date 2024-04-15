<?php

namespace App\Console\CRMDV;

use App\Models\Setting;
use Illuminate\Console\Command;
use App\CRMDV\Controllers\Helpers\CRMDVHelper;
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
        $list_error = [];
        $settings = Setting::where('name', 'links')->pluck('value', 'name')->toArray();
        while (!$stop) {

            //  Không lấy các dự án đang triển khai
            $bill_trang_trien_khai_ids = BillProgress::where(function ($query) {
                                                $query->orWhereIn('status', [
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
                                                    'Chờ khách xác nhận',
                                                    'Tạm dừng',
                                                    'Bỏ',
                                                    null,
                                                ]);
                                                $query->orWhereNull('status');
                                            })
                                            ->pluck('bill_id')->toArray();


            $bills = Bill::select('domain', 'id', 'registration_date')
                            ->where('status', 1)     // lấy hđ đang kich hoạt
                            ->whereNotNull('domain')    //  lấy hđ có điền tên miền
                            ->where('domain', '!=', '.com')
                            ->where('domain', '!=', '.vn')
                            ->where('domain', '!=', '.com.vn')
                            ->where('domain', '!=', '.edu.vn')
                            ->where('domain', '!=', '.net')
                            ->where('domain', 'LIKE', '%.%')    //  lấy hđ có tên miền có dấu .
                            ->whereNotIn('service_id', [    //   ko lấy tên miền ở các  hợp đồng cho các dịch vụ sau:
                                4,  //  dv mail
                                7,  //  dv duy trì
                                8,  // dv nâng cấp hosting
                                9,  // dv nâng cấp web
                                1,  // dv landingpage
                                17,  // Landingpage Tiết kiệm
                                18,  // Landingpage Cơ bản
                                19,  // Landingpage Chuyên nghiệp
                                20,  // Landingpage Cao cấp
                                21,  // Landingpage Thiết kế theo yêu cầu
                            ])
                            ->whereNotIn('id', $bill_trang_trien_khai_ids)  // ko lấy các dự án đang triển khai
                            ->orderBy('id', 'desc')->groupBy('domain')->skip($i)->take($take)
                            ->get();

            if(count($bills) == 0) {
                $stop = true;
            } else {
                $i += $take;
                $arr = [];
                foreach ($bills as $bill) {
                    print "domain: ".$bill->domain."\n";
                    if (strpos($bill->domain, 'http') === false) {
                        $arr[] = [
                            'link' => 'https://' . $bill->domain,
                            'bill_id' => $bill->id,
                            'registration_date' => $bill->registration_date,
                        ];
                    }
                }

                //  Lấy thêm các link cấu hình thêm trong cấu hình
                foreach (preg_split('/\n|\r\n?/', $settings['links']) as $link) {
                    $arr[] = [
                            'link' => 'https://' . $bill->domain,
                            'bill_id' => null,
                            'registration_date' => null
                        ];
                }

                $result = CRMDVHelper::check_link_run($arr);
                if ($result['status']) {
                    $list_error = array_merge($list_error, $result['data']);
                }
            }
        }

        //  Xoá các log cũ
        print "Xoá log từ 7 ngày trước\n";
        LinkErrorLogs::where('created_at', '<', date('Y-m-d', strtotime(" -7 days")))->delete();

        //  Gửi email thông báo các link lỗi
        CRMDVHelper::send_mail_notifications($list_error);

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
