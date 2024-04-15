<?php

namespace App\CRMDV\Controllers\Helpers;

use App\CRMDV\Controllers\Admin\DomainErrorLogController;
use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Setting;
use Auth;
use Mail;
use App\Models\MarketingMail;
use App\CRMDV\Models\LinkErrorLogs;
use View;

class CRMDVHelper
{

    public static function getRoleType($admin_id)
    {
        if (in_array(CommonHelper::getRoleName($admin_id, 'name'), [
            'customer',
            'customer_ldp_vip'])) {
            return 'customer';
        }
        return 'admin';
    }

    public static function logs_error_link($code, $linkErrorLog)
    {
        // dd($code, $linkErrorLog, 'logs_error_link');
        $log = LinkErrorLogs::create([
            'links' => $linkErrorLog->links,
            'error_code' => $code,
            'error_messenger' => $linkErrorLog->error_messenger,
            'time_scan' => $linkErrorLog->time_scan,
            'registration_date' => $linkErrorLog->registration_date,
            'bill_id' => @$linkErrorLog->bill_id,
        ]);
        
        return $log->id;
    }

    public static function send_mail_notifications($list)
    {
        print "Bắt đầu gửi mail\n";

        //  Lấy template gửi mail
        $camp = MarketingMail::find(17);

        //  Lấy danh sách email của admin thành dạng mảng
        $mailSetting = Setting::whereIn('type', ['mail'])->pluck('value', 'name')->toArray();
        $admin_emails = $mailSetting['admin_emails'];
        $admin_emails = explode(',', $admin_emails);
        //  Lấy thông tin người nhận là email đầu tiên trong mảng
        $user = (object)[
            'email' => trim($admin_emails[0]),
            'name' => 'Admin',
            'id' => null
        ];

        //  Lấy những email khác cho vào cc
        foreach($admin_emails as $v) {
            $cc[] = trim($v);    
        }
        unset($cc[0]);  //  xoá mail đầu tiên đi vì mail đầu tiên được cho vào user nhận rồi

//        echo CRMDVHelper::processContentMail($camp->email_template->content); die;

        $data = [
            'sender_account' => $camp->email_account,
            'user' => $user,
            'subject' => $camp->subject,
            'content' => CRMDVHelper::processContentMail(@$camp->email_template->content),
            'cc' => $cc
        ];

        \Mail::to($data['user'])->send(new MailServer($data));
        return true;
    }

    public static function processContentMail($html)
    {

        $linkErrorLogs = LinkErrorLogs::where(function ($query) {
            $query->orwhereNull('edit_status');
            $query->orWhere('edit_status', 0);
        });  //  lấy các web chưa sửa

        $DomainErrorLogController = new DomainErrorLogController();
        $linkErrorLogs = $DomainErrorLogController->appendWhere($linkErrorLogs, (object)['time_scan' => null]);
        $linkErrorLogs = $linkErrorLogs->get();


        $links_error_html = '';
        $time_status = false;
        $ignore_links = \App\Models\Setting::where('name', 'ignore_links')->where('type', 'check_link_error')->first();
        foreach ($linkErrorLogs as $item) {
            if(strpos($ignore_links->value, $item->links) === false) {
//                Không hiện các web nằm trong mục được loại trừ theo dõi
                $links_error_html .= '<tr style="height: 35px">
                    <td style="border: 1px solid #ddd;padding: 8px;"></td>
                    <td style="border: 1px solid #ddd;padding: 8px;">'.$item->links.'</td>
                    <td style="border: 1px solid #ddd;padding: 8px;">'.$item->error_code.'</td>
                    <td style="border: 1px solid #ddd;padding: 8px;">'.$item->error_messenger.'</td>
                    <td style="border: 1px solid #ddd;padding: 8px;">'.date('d/m/Y', strtotime($item->registration_date)).'</td>
                </tr>';
                $time_scan = $item->time_scan;
                $time_status = true;
            }

        }
        $html = str_replace('{links_error}', $links_error_html, $html);
        if ($time_status) {
            $html = str_replace('{time_scan}', date('d/m/Y H:i:s', strtotime(@$time_scan)), $html);
        }
        $html = str_replace('{view_more}', '<a href="https://'.env('DOMAIN').'/admin/error_link_logs?time_scan=' . urlencode(@$list[0]->time_scan).'">Xem chi tiết tại admin >></a>', $html);

        return $html;
    }

    public static function check_link_run($linkchecks)
    {
        try {
            print "Bắt đầu check link\n";
            $time_scan = date('Y-m-d H:i:s');
            $list = [];

            //  Lấy các link cấu hình direct
            $links_direct = Setting::where('name', 'links_direct')->first()->value;
            $lines = preg_split('/\n|\r\n?/', $links_direct);
            $linksDirect = [];
            foreach ($lines as $line) {
                $linksDirect[trim(explode('=>', $line)[0])] = trim(explode('=>', $line)[1]);
            }

            $ch = curl_init();
            foreach ($linkchecks as $item) {
                $item = (object) $item;
                // dd($item);
                print "Check link: ".$item->link."\n";
                if (strpos($item->link, '.') != false) {
            
                    $linkErrorLog = (object) [
                        'links' => $item->link,
                        'time_scan' => $time_scan,
                        'error_messenger' => '',
                        'registration_date' => $item->registration_date,
                        'bill_id' => $item->bill_id,
                    ];

                    curl_setopt($ch, CURLOPT_URL, $linkErrorLog->links);
                    curl_setopt($ch, CURLOPT_HEADER, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $data = curl_exec($ch);
                    $headers = curl_getinfo($ch);
                
                    // dd('1');
                    $code = $headers['http_code'];

                    if ($code == 0 || $code == 301 || $code == 302) {
                        //  Nếu link bị lỗi ko tìm thấy máy chủ hoặc chuyển hướng thì quét lại
                        $code = CRMDVHelper::reCheckLink($code, $linksDirect, $item->link, $ch);
                    }

                    

                    if ($code == 0) {
                        $linkErrorLog->error_messenger = 'Không thể tìm thấy địa chỉ IP máy chủ';

                        $list[] = CRMDVHelper::logs_error_link($code, $linkErrorLog);
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
                            $linkErrorLog->error_messenger = @$http_code[$code];
                        } else {
                            $linkErrorLog->error_messenger = 'Lỗi khác !!!';
                        }
                        print "link lỗi " . $item->link . "\n";
                        // dd($code, $linkErrorLog);
                        $list[] = CRMDVHelper::logs_error_link($code, $linkErrorLog);
                    }
                    
                }
            }
            curl_close($ch);
            // dd($list);

            return [
                'status' => true,
                'data' => $list
            ];
        } catch (\Exception $ex) {
            dd($ex->getMessage());
            return [
                'status' => false,
                'msg' => $ex->getMessage()
            ];
        }
    }

    /**
     * Kiểm tra lại các link web được cấu hình chuyển hướng hoặc ko quét link đó trong admin/setting
    */
    public static function reCheckLink($code, $linksDirect, $link, $ch) {
        if (isset($linksDirect[$link])) {
            if ($linksDirect[$link] == 'false') {
                return 200;
            }

            //  Quét các link cấu hình chuyển hướng trước
            $link = $linksDirect[$link];
            curl_setopt($ch, CURLOPT_URL, $link);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            $headers = curl_getinfo($ch);
            $code = $headers['http_code'];
        }
        return $code;
    }
}
