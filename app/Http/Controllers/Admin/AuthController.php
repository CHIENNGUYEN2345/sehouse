<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Admin;
use App\Models\RoleAdmin;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Session;
use Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;

class AuthController extends Controller
{

    protected $maxLoginAttempts = 2;

    protected $lockoutTime = 300;

    use AuthenticatesUsers;
    public function login(Request $request)
    {
        $data['page_title'] = 'Đăng nhập';
        $data['page_type'] = 'list';
        return view(config('core.admin_theme') . '.auth.login');
    }

    public function authenticate(Request $request)
    {

        $admin = Admin::where('email', $request['email'])->orWhere('tel', $request['email'])->first();

        if (!is_object($admin)) {
            CommonHelper::one_time_message('danger', 'Vui lòng kiểm tra lại tên đăng nhập của bạn');
            return redirect('admin/login');
        }

        if (@$admin->status == 0) {
            CommonHelper::one_time_message('danger', 'Tài khoản của bạn chưa được kích hoạt!');
//            $this->hasTooManyLoginAttempts($request);
            return redirect('admin/login');
        }

        if (@$admin->status == -1) {
            CommonHelper::one_time_message('danger', 'Tài khoản của bạn đã bị khóa!');
//            $this->hasTooManyLoginAttempts($request);
            return redirect('admin/login');
        }

        if (\Auth::guard('admin')->attempt(['email' => trim($request['email']), 'password' => trim($request['password'])], true)) {
            return redirect()->intended('admin/dashboard');
        } elseif (\Auth::guard('admin')->attempt(['tel' => trim($request['email']), 'password' => trim($request['password'])], true)) {
            return redirect()->intended('admin/dashboard');
        } else {
            $this->attemptLogin5Shot($request['email']);
            CommonHelper::one_time_message('danger', 'Sai mật khẩu!');
            return redirect('admin/login');
        }
    }


//    protected function hasTooManyLoginAttempts(Request $request)
//    {
//        $a=$this->limiter()->tooManyAttempts(
//            $this->throttleKey($request), 2, 1
//        );
//        return $a;
//    }

//hàm khóa tài khoản khi đăng nhập sai mật khẩu quá 5 lần
    public function attemptLogin5Shot($email)
    {
        session_start();
        if (!isset($_SESSION['limitLoginFalse1'])) {
            $product_viewed = [$email];
        } else {
            $product_viewed = $_SESSION['limitLoginFalse1'];
            $product_viewed[] = $email;
        }
        $_SESSION['limitLoginFalse1'] = $product_viewed;
        if (isset($_SESSION['limitLoginFalse1'])) {
            $str = implode('|',$_SESSION['limitLoginFalse1']);
            $str_sub = substr_count($str, $email);
            $admi = Admin::where('email',$email)->first();
            if ($str_sub>5 && !empty($admi)){
                $admi->status = 0;
                $admi->save();
            }
        }
    }
    public function validator(array $data)
    {
        $rules = array(
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:50',
        );

        $fieldNames = array(
            'name' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
        );

        $validator = Validator::make($data, $rules);
        $validator->setAttributeNames($fieldNames);
        return $validator;
    }

    public function register(Request $request)
    {
        $settings = @Setting::select(['name', 'value'])->where('type', 'role_tab')->pluck('value', 'name')->toArray();

        if (!$_POST) {
            if (@$settings['allow_admin_account_registration'] != 1) {
                return redirect('/admin/login');
            }

            $data['page_title'] = 'Đăng ký tài khoản';
            $data['page_type'] = 'list';
            return view(config('core.admin_theme') . '.auth.register', $data);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:admin,email',
            'password' => 'required|min:4',
            'password_confimation' => 'required|same:password',
//            'tel' => 'required|unique:admin,tel',
        ], [
            'name.required' => 'Bắt buộc phải nhập tên!',
            'email.required' => 'Bắt buộc phải nhập email!',
            'email.unique' => 'Địa chỉ email đã tồn tại!',
            'password.required' => 'Bắt buộc phải nhập mật khẩu!',
            'password.min' => 'Mật khẩu phải trên 4 ký tự!',
            'password_confimation.required' => 'Bắt buộc nhập lại mật khẩu!',
            'password_confimation.same' => 'Nhập lại sai mật khẩu!',
//            'tel.required' => 'Bắt buộc nhập số điện thoại!',
//            'tel.unique' => 'Số điện thoại đã tồn tại!',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            $data = $request->except('_token');
            $data['password'] = $data['password_md5'] = bcrypt($data['password']);
            $data['api_token'] = base64_encode(rand(1, 100) . time());
            if (isset($data['status'])) {
                unset($data['status']);
            }
            unset($data['password_confimation']);
            unset($data['agree']);

            $admin = new Admin;

            foreach ($data as $k => $v) {
                $admin->{$k} = $v;
            }

            $admin->save();

            $this->setDefaultRoleToAdmin($admin, $settings['role_default_id']);

            //  Gọi đến các sự kiện sau khi đăng ký tài khoản
            try {
                \Eventy::action('admin.register', [
                    'email' => @$admin->email,
                    'password' => @$request->password,
                    'name' => @$admin->name,
                    'id' => @$admin->id
                ]);
            } catch (\Exception $ex) {
            }
            return redirect('/admin/login')->with('success', 'Bạn đã đăng kí tài khoản thành công! Vui lòng kiểm tra email ' . $admin->email . ' để kích hoạt tài khoản! <a href="/admin/resent-mail-active?email=' . $admin->email . '" >Gửi lại mail kích hoạt</a>');
        }
    }

    public function setDefaultRoleToAdmin($admin, $role_id = false) {
        if (!$role_id) {
            $settings = @Setting::select(['name', 'value'])->where('type', 'role_tab')->pluck('value', 'name')->toArray();
            $role_id = $settings['role_default_id'];
        }

        //  Set quyền mặc định khi mới đăng ký tài khoản
        if (isset($role_id)) {
            RoleAdmin::insert([
                'admin_id' => $admin->id,
                'role_id' => $role_id,
            ]);
        }
        return true;
    }

    public function activeAccount(Request $request)
    {
        $email = base64_decode($request->code);
        $admin = Admin::where('email', $email)->first();
        if (!is_object($admin)) {
            CommonHelper::one_time_message('error', 'Tài khoản không tồn tại!');
            return redirect('/admin/login');
        }
        if ($admin->status == -1) {
            CommonHelper::one_time_message('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ với admin để kích hoạt lại!');
            return redirect('/admin/login');
        }

        $admin->status = 1;
        $admin->save();

        CommonHelper::one_time_message('success', 'Tài khoản đã được kích hoạt!');
        return redirect('/admin/login');
    }

    public function getEmailForgotPassword(Request $request)
    {
        if (!$_POST) {
            $data['page_title'] = 'Quên mật khẩu';
            return view(config('core.admin_theme') . '.auth.email_forgot_password')->with($data);

        } else {
            $query = Admin::where('email', $request->email);

            if (!$query->exists()) {
                abort(404);
            }
            $admin = $query->first();
            $admin->change_password = $admin->id . '_' . time();
            $admin->save();

            try {
                $mailSetting = Setting::whereIn('type', ['mail'])->pluck('value', 'name')->toArray();
//                dd($mailSetting);
//                if (empty($mailSetting)){
//                    $settingk = Setting::where('name', 'email_notifi')->where('type', 'common_tab')->pluck('value', 'name')->toArray();
//                    $user = (object)[
//                        'email' => $admin->email,
//                        'name' => @$admin->name,
//                        'link' => \URL::to('forgot-password') . '?change_password=' . @$admin->change_password,
//                    ];
//                    $data = [
//                        'view' => config('core.admin_theme') . '.emails.forgot_password',
//                        'user' => $user,
//                        'name' => $settingk['value'],
//                        'subject' => 'Đổi mật khẩu'
//                    ];
//                    Mail::to($user)->send(new MailServer($data));
//                }else{
                    \Eventy::action('admin.restorePassword', [
                        'link' => \URL::to('forgot-password') . '?change_password=' . @$admin->change_password,
                        'name' => @$admin->name,
                        'email' => $admin->email
                    ]);
//                }


                CommonHelper::one_time_message('success', 'Email sẽ được gửi trong ít phút. Bạn vui lòng kiểm tra mail để lấy lại mật khẩu!');
                return back();
            } catch (\Exception $ex) {
                CommonHelper::one_time_message('error', 'Xin vui lòng thử lại!');
            }
        }
    }

    public function changeEmail(Request $request)
    {
        if (!$_POST) {
            $data['page_title'] = 'Đổi email đăng nhập';
            return view(config('core.admin_theme') . '.auth.email_change')->with($data);

        } else {
//            $rule = [
//                'email_present' => 'required',
//                'email_new' => 'required|unique:admin,email',
//                'password' => 'required|min:4',
//                'password_confimation' => 'required|same:password',
//
//            ];
//            $message = [
//                'email_present.required' => 'Bắt buộc phải nhập email!',
//                'email_new.required' => 'Bắt buộc phải nhập lại email!',
//                'email_new.unique' => 'Địa chỉ email đã tồn tại!',
//                'password.required' => 'Bắt buộc phải nhập mật khẩu!',
//                'password.min' => 'Mật khẩu phải trên 4 ký tự!',
//                'password_confimation.required' => 'Bắt buộc nhập lại mật khẩu!',
//                'password_confimation.same' => 'Nhập lại sai mật khẩu!',
//            ];

//            $request->validate($rule, $message);

            $query = Admin::where('email', $request->email_present);

            if (!$query->exists()) {
                abort(404);
            }
            $admin = $query->first();
            $admin->change_email = $admin->id . '_' . time();
            $admin->email_new = $request->email_new;
            $admin->save();
            try {
                \Eventy::action('admin.change_email', [
                    'email' => $request->email_new,
                    'name' => $admin['name'],
                    'link' => \URL::to('confirm-email-change') . '?change_email=' . @$admin->change_email,
                ]);
                CommonHelper::one_time_message('success', 'Email sẽ được gửi trong ít phút. Bạn vui lòng kiểm tra mail để lấy lại mật khẩu!');
                return back();
            } catch (\Exception $ex) {
                CommonHelper::one_time_message('error', 'Xin vui lòng thử lại!');
            }
        }
    }

    public function confirmChangeEmail(Request $request)
    {

        $query = Admin::where('change_email', $request->change_email);
        if (!$query->exists() || !isset($request->change_email)) {
            abort(404);
        }
        $admin = $query->first();
        $admin->change_email = '';
        $admin->email = $admin->email_new;
        $admin->email_new = '';
        $admin->save();

        $data['admin'] = $admin;
        $data['page_title'] = 'Xác nhận email đăng nhập';
        return view(config('core.admin_theme') . '.auth.confirm_email')->with($data);
    }

    public function resentMailActive(Request $request)
    {
        $admin = Admin::where('email', $request->email)->first();
        try {
            \Eventy::action('admin.register', [
                'email' => @$admin->email,
                'password' => @$request->password,
                'name' => @$admin->name,
                'id' => @$admin->id
            ]);
        } catch (\Exception $ex) {

        }
        return redirect('/admin/login')->with('success', 'Đã gửi lại email kích hoạt tài khoản! Vui lòng kiểm tra email ' . $admin->email . ' để kích hoạt tài khoản! <a href="/admin/resent-mail-active?email=' . $admin->email . '" >Gửi lại mail kích hoạt</a>');
    }

    public function forgotPassword(Request $request)
    {
        if (!$_POST) {

            $query = Admin::where('change_password', $request->change_password);

            if (!$query->exists() || !isset($request->change_password)) {
                abort(404);
            }
            $data['page_title'] = 'Lấy lại mật khẩu';
            $data['page_type'] = 'list';
            return view(config('core.admin_theme') . '.auth.forgot_password')->with($data);
        } else {
            if ($request->password == $request->re_password) {
                $admin = Admin::where('change_password', $request->change_password)->first();
                $admin->password = $admin->password = bcrypt($request->password);
                $admin->change_password = $admin->id . '_' . time();
                $admin->save();
                CommonHelper::one_time_message('success', 'Đổi mật khẩu thành công! vui lòng đăng nhập!');
                return redirect('/admin/login');
            } else {
                return back()->with('alert_re_password', 'Nhập lại mật khâu không khớp!');
            }
        }
    }

    public function logout()
    {
        \Auth::guard('admin')->logout();
        return redirect('admin/login');
    }

    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $admin_social = Socialite::driver($provider)->user();

        if ($admin_social->email == null || $admin_social->email == '') {
            //  nếu fb//gg không trả về email thì set bừa cho họ cái email là id facebook/gg
            $admin_social->email = $admin_social->id . '@gmail.com';
        }
        $admin = Admin::where('email', $admin_social->email)->first();
        if (!is_object($admin)) {
            $admin = new Admin();
            $prd = $provider . '_id';
            $admin->{$prd} = $admin_social->id;
            $admin->name = $admin_social->name;
            $admin->email = $admin_social->email;

            $admin->save();

            //  Set quyền mặc định cho admin
            $this->setDefaultRoleToAdmin($admin);
        }
        \Auth::guard('admin')->login($admin);
        return redirect('/');
    }
}