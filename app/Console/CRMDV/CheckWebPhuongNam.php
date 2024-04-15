<?php

namespace App\Console\CRMDV;

use App\Mail\MailServer;
use App\Models\MarketingMail;
use App\Models\Setting;
use Illuminate\Console\Command;
use App\CRMDV\Controllers\Helpers\CRMDVHelper;

class CheckWebPhuongNam extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'quet_loi:phuongnam';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quét lỗi chết web phuongnamedu.vn';

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
        $filename = base_path() . "/public_html/filemanager/userfiles/phuongnamedu-vn_log_quet_loi.txt";


        $file = fopen($filename, "r");

        if ($file) {
            // Đọc dữ liệu từ tệp tin
            $time_log = fread($file, filesize($filename));

            if ($time_log > (time() - 30 * 60)) {
                //  nếu thời gian quét trong 30p gần đây thì không quét nữa
                print "- Mới báo lỗi gần đây\n";
                die;
            } else {
                print "- time_log:".$time_log."\n";
            }

            // Đóng tệp tin sau khi đọc xong
            fclose($file);

        } else {
            print "Không thể mở tệp tin để đọc.\n";
        }


        $links_error_html = "";

        print "- Bắt đầu chạy\n";


        $ch = curl_init();


        //  check link 1
        $link = 'https://phuongnamedu.vn/';

        curl_setopt($ch, CURLOPT_URL, $link);

// Đặt thời gian timeout (ví dụ: 10 giây)
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Đặt timeout là 10 giây

// Bật các tùy chọn khác nếu cần
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Thực hiện request
        $data = curl_exec($ch);
        $headers = curl_getinfo($ch);

        $code = $headers['http_code'];

        $error_messenger = false;
        if ($code == 0) {
            $error_messenger = 'Không thể tìm thấy địa chỉ IP máy chủ';
        } elseif ($code != 200 && $code != 500) {
            $http_code = [
                '100' => 'Yêu cầu đã được hoàn thành và phần còn lại của tiến trình có thể tiếp tục (Continue) .',
                '101' => 'Máy chủ đang thay đổi sang phiên bản HTTP khác (Switching Protocols).',
                '102' => 'Yêu cầu đã được hoàn thành và đang xử lí (Processing).',
                '202' => 'Yêu cầu đã được chấp nhận, nhưng chưa được xử lý (Accepted).',
                '203' => 'Thông tin chứa trong tiêu đề thực thể không phải từ trang web gốc (Non-Authoritative Information).',
                '204' => 'Phản hồi này được máy chủ suy ra và không cảnh báo người dùng về bất cứ điều gì (No Content).',
                '205' => 'Máy chủ reset lại bất kỳ nội dung nào được CGI trả về (Reset Content).',
                '206' => 'Các file được yêu cầu không được tải xuống hoàn toàn (Partial Content).',
                '300' => 'Địa chỉ được yêu cầu đề cập đến nhiều hơn một file (Multiple Choices).',
                '301' => 'Trang yêu cầu chuyển hướng (Moved Permanently).',
                '302' => 'Trang yêu cầu chuyển hướng (Found).',
                '303' => 'Dữ liệu ở một nơi khác (See Other).',
                '304' => 'Header yêu cầu bao gồm tham số "if modified since" (Not Modified).',
                '305' => 'Yêu cầu thông qua proxy (Use Proxy).',
                '307' => 'Chuyển hướng tạm thời (Temporary Redirect).',
                '308' => 'Chuyển hướng (Permanent Redirect).',
                '400' => 'Có một lỗi cú pháp trong yêu cầu và yêu cầu bị từ chối (Bad Request).',
                '401' => 'Header không chứa mã xác thực cần thiết và client bị từ chối truy cập (Unauthorized).',
                '402' => 'Việc thanh toán là bắt buộc. Code này vẫn chưa hoạt động (Payment Required).',
                '403' => 'Client không được phép truy cập (Forbidden).',
                '404' => 'Đường dẫn không tồn tại (Not Found).',
                '405' => 'Phương pháp đang sử dụng để truy cập file không được cho phép (Method Not Allowed).',
                '406' => 'Yêu cầu tồn tại nhưng không thể được sử dụng (Not Acceptable).',
                '407' => 'Yêu cầu phải được cho phép trước khi diễn ra (Proxy Authentication Required).',
                '408' => 'Máy chủ mất quá nhiều thời gian để xử lý yêu cầu (Request Time-out).',
                '409' => 'Quá nhiều yêu cầu (Conflict).',
                '410' => 'Các file đã được sử dụng ở vị trí này, nhưng không còn nữa (Gone).',
                '411' => 'Yêu cầu thiếu header Content-Length (Length Required).',
                '412' => 'Một cấu hình nhất định được yêu cầu để chuyển file này, nhưng client chưa thiết lập cấu hình đó (Precondition Failed).',
                '413' => 'Các yêu cầu là quá lớn để xử lý (Request Entity Too Large).',
                '414' => 'Địa chỉ đã nhập quá dài cho máy chủ (Request-URI Too Large).',
                '415' => 'Loại file của yêu cầu không được hỗ trợ (Unsupported Media Type).',
                '416' => 'Request Range Not Satisfiable.',
                '417' => 'Expectation Failed.',
                '421' => 'Misdirected Request.',
                '422' => 'Unprocessable Entity.',
                '423' => 'Locked.',
                '424' => 'Failed Dependency.',
                '425' => 'Unordered Collection.',
                '426' => 'Upgrade Required.',
                '428' => 'Precondition Required.',
                '429' => 'Too Many Requests.',
                '431' => 'Request Header Fields Too Large.',
                '451' => 'Unavailable For Legal Reasons.',
                '500' => 'Phản hồi khó chịu thường xảy ra do sự cố trong code Perl, khi chương trình CGI chạy (Internal Server Error)',
                '501' => 'Yêu cầu không thể được máy chủ thực hiện (Not Implemented)',
                '502' => 'Máy chủ cố truy cập đang gửi lại lỗi (Bad Gateway)',
                '503' => 'Yêu cầu không có sẵn (Service Unavailable)',
                '504' => 'Cổng của máy chủ hết hạn (Gateway Time-out)',
                '505' => 'Giao thức HTTP yêu cầu không được hỗ trợ (HTTP Version Not Supported)',
                '506' => 'Variant Also Negotiates',
                '507' => 'Insufficient Storage',
                '508' => 'Loop Detected',
                '510' => 'Not Extended',
                '511' => 'Network Authentication Required',
            ];

            if (key_exists($code, $http_code)) {
                $error_messenger = @$http_code[$code];
            } else {
                $error_messenger = 'Lỗi khác !!!';
            }
            print "link lỗi " . $link . "\n";

        } else {
            print "web không lỗi: " . $link . "\n";
        }

        if ($error_messenger) {
            //  check lại lần 2

            curl_setopt($ch, CURLOPT_URL, $link);

// Đặt thời gian timeout (ví dụ: 10 giây)
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Đặt timeout là 10 giây

// Bật các tùy chọn khác nếu cần
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Thực hiện request
            $data = curl_exec($ch);
            $headers = curl_getinfo($ch);

            $code = $headers['http_code'];

            if ($code == 200 || $code == 500) {
                $error_messenger = false;
            }

            if ($error_messenger) {
                //                Không hiện các web nằm trong mục được loại trừ theo dõi
                $links_error_html .= '<tr style="height: 35px">
                    <td style="border: 1px solid #ddd;padding: 8px;"></td>
                    <td style="border: 1px solid #ddd;padding: 8px;">'.$link.'</td>
                    <td style="border: 1px solid #ddd;padding: 8px;">'.$code.'</td>
                    <td style="border: 1px solid #ddd;padding: 8px;">'.$error_messenger.'</td>
                    <td style="border: 1px solid #ddd;padding: 8px;"></td>
                </tr>';
            }

        }



        //  check lỗi link 2
        $link = 'https://phuongnam.edu.vn/';

        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        $headers = curl_getinfo($ch);

        $code = $headers['http_code'];

        $error_messenger = false;
        if ($code == 0) {
            $error_messenger = 'Không thể tìm thấy địa chỉ IP máy chủ';
        } elseif ($code != 200 && $code != 500) {
            $http_code = [
                '100' => 'Yêu cầu đã được hoàn thành và phần còn lại của tiến trình có thể tiếp tục (Continue) .',
                '101' => 'Máy chủ đang thay đổi sang phiên bản HTTP khác (Switching Protocols).',
                '102' => 'Yêu cầu đã được hoàn thành và đang xử lí (Processing).',
                '202' => 'Yêu cầu đã được chấp nhận, nhưng chưa được xử lý (Accepted).',
                '203' => 'Thông tin chứa trong tiêu đề thực thể không phải từ trang web gốc (Non-Authoritative Information).',
                '204' => 'Phản hồi này được máy chủ suy ra và không cảnh báo người dùng về bất cứ điều gì (No Content).',
                '205' => 'Máy chủ reset lại bất kỳ nội dung nào được CGI trả về (Reset Content).',
                '206' => 'Các file được yêu cầu không được tải xuống hoàn toàn (Partial Content).',
                '300' => 'Địa chỉ được yêu cầu đề cập đến nhiều hơn một file (Multiple Choices).',
                '301' => 'Trang yêu cầu chuyển hướng (Moved Permanently).',
                '302' => 'Trang yêu cầu chuyển hướng (Found).',
                '303' => 'Dữ liệu ở một nơi khác (See Other).',
                '304' => 'Header yêu cầu bao gồm tham số "if modified since" (Not Modified).',
                '305' => 'Yêu cầu thông qua proxy (Use Proxy).',
                '307' => 'Chuyển hướng tạm thời (Temporary Redirect).',
                '308' => 'Chuyển hướng (Permanent Redirect).',
                '400' => 'Có một lỗi cú pháp trong yêu cầu và yêu cầu bị từ chối (Bad Request).',
                '401' => 'Header không chứa mã xác thực cần thiết và client bị từ chối truy cập (Unauthorized).',
                '402' => 'Việc thanh toán là bắt buộc. Code này vẫn chưa hoạt động (Payment Required).',
                '403' => 'Client không được phép truy cập (Forbidden).',
                '404' => 'Đường dẫn không tồn tại (Not Found).',
                '405' => 'Phương pháp đang sử dụng để truy cập file không được cho phép (Method Not Allowed).',
                '406' => 'Yêu cầu tồn tại nhưng không thể được sử dụng (Not Acceptable).',
                '407' => 'Yêu cầu phải được cho phép trước khi diễn ra (Proxy Authentication Required).',
                '408' => 'Máy chủ mất quá nhiều thời gian để xử lý yêu cầu (Request Time-out).',
                '409' => 'Quá nhiều yêu cầu (Conflict).',
                '410' => 'Các file đã được sử dụng ở vị trí này, nhưng không còn nữa (Gone).',
                '411' => 'Yêu cầu thiếu header Content-Length (Length Required).',
                '412' => 'Một cấu hình nhất định được yêu cầu để chuyển file này, nhưng client chưa thiết lập cấu hình đó (Precondition Failed).',
                '413' => 'Các yêu cầu là quá lớn để xử lý (Request Entity Too Large).',
                '414' => 'Địa chỉ đã nhập quá dài cho máy chủ (Request-URI Too Large).',
                '415' => 'Loại file của yêu cầu không được hỗ trợ (Unsupported Media Type).',
                '416' => 'Request Range Not Satisfiable.',
                '417' => 'Expectation Failed.',
                '421' => 'Misdirected Request.',
                '422' => 'Unprocessable Entity.',
                '423' => 'Locked.',
                '424' => 'Failed Dependency.',
                '425' => 'Unordered Collection.',
                '426' => 'Upgrade Required.',
                '428' => 'Precondition Required.',
                '429' => 'Too Many Requests.',
                '431' => 'Request Header Fields Too Large.',
                '451' => 'Unavailable For Legal Reasons.',
                '500' => 'Phản hồi khó chịu thường xảy ra do sự cố trong code Perl, khi chương trình CGI chạy (Internal Server Error)',
                '501' => 'Yêu cầu không thể được máy chủ thực hiện (Not Implemented)',
                '502' => 'Máy chủ cố truy cập đang gửi lại lỗi (Bad Gateway)',
                '503' => 'Yêu cầu không có sẵn (Service Unavailable)',
                '504' => 'Cổng của máy chủ hết hạn (Gateway Time-out)',
                '505' => 'Giao thức HTTP yêu cầu không được hỗ trợ (HTTP Version Not Supported)',
                '506' => 'Variant Also Negotiates',
                '507' => 'Insufficient Storage',
                '508' => 'Loop Detected',
                '510' => 'Not Extended',
                '511' => 'Network Authentication Required',
            ];

            if (key_exists($code, $http_code)) {
                $error_messenger = @$http_code[$code];
            } else {
                $error_messenger = 'Lỗi khác !!!';
            }
            print "link lỗi " . $link . "\n";

        } else {
            print "web không lỗi: " . $link . "\n";
        }

        if ($error_messenger) {
            //  check lại lần 2
            curl_setopt($ch, CURLOPT_URL, $link);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            $headers = curl_getinfo($ch);

            $code = $headers['http_code'];

            if ($code == 200 || $code == 500) {
                $error_messenger = false;
            }

            if ($error_messenger) {
                //                Không hiện các web nằm trong mục được loại trừ theo dõi
                $links_error_html .= '<tr style="height: 35px">
                    <td style="border: 1px solid #ddd;padding: 8px;"></td>
                    <td style="border: 1px solid #ddd;padding: 8px;">'.$link.'</td>
                    <td style="border: 1px solid #ddd;padding: 8px;">'.$code.'</td>
                    <td style="border: 1px solid #ddd;padding: 8px;">'.$error_messenger.'</td>
                    <td style="border: 1px solid #ddd;padding: 8px;"></td>
                </tr>';
            }
        }




        if ($links_error_html != '') {
            //  gửi mail báo web lỗi
            //  Gửi email thông báo các link lỗi
            print "Bắt đầu gửi mail\n";

            //  Lấy template gửi mail
            $camp = MarketingMail::find(17);

            //  Lấy thông tin người nhận là email đầu tiên trong mảng
            $user = (object)[
                'email' => 'hoanghung.developer@gmail.com',
                'name' => 'Admin',
                'id' => null
            ];

            $admin_emails = [
                'webhobasoft@gmail.com',
                'thanhtunghalat@gmail.com',
                'phucnh@phuongnam.edu.vn',
                'vinhn@phuongnam.edu.vn',
            ];
            //  Lấy những email khác cho vào cc
            foreach($admin_emails as $v) {
                $cc[] = trim($v);
            }


            $html = str_replace('{links_error}', $links_error_html, @$camp->email_template->content);
            $html = str_replace('{time_scan}', date('d/m/Y H:i:s'), $html);
            $html = str_replace('{view_more}', '', $html);


            $data = [
                'sender_account' => $camp->email_account,
                'user' => $user,
                'subject' => $camp->subject,
                'content' => $html,
                'cc' => $cc
            ];

            \Mail::to($data['user'])->send(new MailServer($data));


            //  log lại thời gian gui báo lỗi
            $file = fopen($filename, "w");
            if ($file) {
                // Ghi dữ liệu vào tệp tin
                fwrite($file, time());

                // Đóng tệp tin sau khi ghi xong
                fclose($file);

                print "Ghi dữ liệu vào tệp log thành công.\n";
            } else {
                print "Không thể mở tệp log để ghi.\n";
            }
        }


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
