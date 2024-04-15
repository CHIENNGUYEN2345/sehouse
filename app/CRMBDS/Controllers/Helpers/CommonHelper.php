<?php

namespace App\CRMBDS\Controllers\Helpers;

use App\Mail\MailServer;
use App\Models\Category;
use App\Models\Meta;
use App\Models\Setting;
use Auth;
use Mail;
use App\CRMBDS\Models\LinkErrorLogs;
use App\Models\MarketingMail;
use mysql_xdevapi\Exception;
use Session;
use View;

class CommonHelper
{

    function __construct()
    {
        setlocale(LC_ALL, 'vi_VN.UTF8');

    }

    public static function logs_error_link($code, $url)
    {
        $logs = LinkErrorLogs::create([
//            'domain_id' => $url->domain_id,
            'links' => $url->link,
            'error_code' => $code,
            'error_messenger' => $url->error_messenger,
            'time_scan' => $url->time_scan,
        ]);
        return $logs->id;
    }

    public static function send_mail_notifications($list)
    {
        $list = is_array($list) ? LinkErrorLogs::whereIn('id', $list)->get()->groupBy('domain_id') : null;

        //  Lấy camp
        $camp = MarketingMail::find(17);

        //  Lấy danh sách email của admin thành dạng mảng
        $mailSetting = Setting::whereIn('type', ['mail'])->pluck('value', 'name')->toArray();
        $admin_emails = $mailSetting['admin_emails'];
        $admin_emails = explode(',', $admin_emails);
        //  Lấy thông tin người nhận là email đầu tiên trong mảng
        $user = (object)[
            'email' => $admin_emails[0],
            'name' => 'Admin',
            'id' => null
        ];

        //  Lấy những email khác cho vào cc
        $cc = $admin_emails;
        unset($cc[0]);

        $data = [
            'sender_account' => $camp->email_account,
            'user' => $user,
            'subject' => $camp->subject,
            'content' => CommonHelper::processContentMail($camp->email_template->content),
            'cc' => $cc
        ];

        \Mail::to($data['user'])->send(new MailServer($data));

        $settings = Setting::whereIn('name', ['admin_emails', 'mail_name', 'admin_email', 'admin_receives_mail'])->pluck('value', 'name')->toArray();
        if ($settings['admin_receives_mail'] == 1) {
            $admins = explode(',', $settings['admin_emails']);
            foreach ($admins as $admin) {
                $user = (object)[
                    'email' => trim($admin),
                    'name' => $settings['mail_name'],
                ];
                $data = [
                    'view' => 'checkerrorlink::emails.list_error',
                    'link_view_more' => \URL::to('/admin/error_link_logs') . '?time_scan=' . urlencode(@$list[0]->time_scan),
                    'user' => $user,
                    'list' => $list,
                    'name' => $settings['mail_name'],
                    'subject' => 'Danh sách link web lỗi'
                ];
                Mail::to($user)->send(new MailServer($data));
            }
        }
    }

    public static function processContentMail($html)
    {
        $settings = Setting::where('type', 'general_tab')->pluck('value', 'name')->toArray();

        $html = str_replace('{site_title}', @$settings['name'], $html);

        $html = str_replace('{site_url}', \URL::to('/'), $html);

        $html = str_replace('{site_logo}', '<a href="//'.env('DOMAIN').'"><img style="max-width: 150px; max-height: 150px;" src="'. \App\Http\Helpers\CommonHelper::getUrlImageThumbHasDomain(@$settings['logo'], null, 200, env('DOMAIN')).'"/></a>', $html);

        $html = str_replace('{site_hotline}', @$settings['hotline'], $html);

        $html = str_replace('{site_address}', @$settings['address'], $html);

        $html = str_replace('{site_admin_email}', @$settings['email'], $html);

        $html = str_replace('{date_time}', date('d/m'), $html);

        $html = str_replace('{date_year}', date('Y'), $html);

        return $html;
    }

    public static function check_link_run($linkchecks)
    {
        try {
            $time_scan = date('Y-m-d H:i:s');
            $log_ids = [];
            foreach ($linkchecks as $url) {
                $url = (object) $url;
                if (is_object($url)) {

                    $url->time_scan = $time_scan;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url->link);
                    curl_setopt($ch, CURLOPT_HEADER, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $data = curl_exec($ch);
                    $headers = curl_getinfo($ch);
                    curl_close($ch);
                    $code = $headers['http_code'];
                    if ($code == 0) {
                        $url->error_messenger = 'Không thể tìm thấy địa chỉ IP máy chủ';

                        $log_ids[] = CommonHelper::logs_error_link($code, $url);
                    } elseif ($code != 200) {
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
                            $url->error_messenger = $http_code[$code];
                        } else {
                            $url->error_messenger = 'Lỗi khác !!!';
                        }
                        $log_ids[] = CommonHelper::logs_error_link($code, $url);
                    }
                } else {
                    var_dump($url);
                    print "không phải \$url object\n";
                }
            }
//            CommonHelper::send_mail_notifications($log_ids);
            return [
                'status' => true,
                'data' => $log_ids,
            ];
        } catch (Exception $ex) {
            return [
                'status' =>  false,
                'data' => [],
                'msg' => $ex->getMessage()
            ];
        }
    }
}
