<?php

namespace App\Http\Controllers\Admin;

use App\Mail\MailServer;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Mail;
use URL;

class MailController extends Controller
{
    protected $_mailSetting;

    public function __construct()
    {
        $this->_mailSetting = Setting::whereIn('type', ['mail'])->pluck('value', 'name')->toArray();

    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    /**
     * Gửi mail kích hoạt toài khoản
     * $admin: bản ghi tài khoản vừa đăng kí
     */
    public function registerSendMail($admin)
    {
        $user = (object)[
            'email' => $admin['email'],
            'name' => $admin['name'],
            'link' => \URL::to('active-account') . '?code=' . base64_encode($admin['email']),
            'password' => $admin['password']
        ];
        $data = [
            'view' => config('core.admin_theme') . '.emails.register_account',
            'name' => $this->_mailSetting['mail_name'],
            'subject' => 'Kích hoạt tài khoản!',
            'user' => $user,

        ];
        Mail::to($user)->send(new MailServer($data));
    }

    /**
     *  Gửi mail khi quên mật khẩu
     *
     *
     */
    public function postEmailRestorePasswordSendMail($admin)
    {
        $user = (object)[
            'email' => $admin['email'],
            'name' => $admin['name'],
            'link' => $admin['link'],
        ];
        $data = [
            'view' => config('core.admin_theme') . '.emails.forgot_password',
            'user' => $user,
            'name' => $this->_mailSetting['mail_name'],
            'subject' => 'Đổi mật khẩu'
        ];
//dd($this->_mailSetting['mail_name']);
        Mail::to($user)->send(new MailServer($data));
    }

    public function changeEMail($admin)
    {
        $user = (object)[
            'email' => $admin['email'],
            'name' => $admin['name'],
            'link' => $admin['link'],
        ];
        $data = [
            'view' => config('core.admin_theme') . '.emails.change_email',
            'user' => $user,
            'name' => $this->_mailSetting['mail_name'],
            'subject' => 'Đổi email đăng nhập'
        ];
        Mail::to($user)->send(new MailServer($data));
    }


    public function testMail(Request $request)
    {
        $settings = Setting::where('type', 'mail')->pluck('value', 'name')->toArray();
        $admins = explode(',', $settings['admin_emails']);
        foreach ($admins as $admin) {
            $user = (object)[
                'email' => trim($admin),
                'name' => $settings['mail_name'],
            ];
            $data = [
                'view' => 'admin.themes.metronic1.emails.test_send_mail',
                'user' => $user,
                'name' => $settings['mail_name'],
                'subject' => 'Test email'
            ];
            Mail::to($user)->send(new MailServer($data));
        }

        return response()->json([
            'status' => true,
            'msg' => 'Gửi mail thành công, vui lòng kiểm tra!'
        ]);
    }
}
