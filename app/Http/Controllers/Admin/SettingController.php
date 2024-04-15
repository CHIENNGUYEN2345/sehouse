<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class SettingController extends CURDBaseController
{

    protected $module = [
        'code' => 'setting',
        'label' => 'admin.general_setting',
        'modal' => '\App\Models\Setting',
        'table_name' => 'settings',
        'tabs' => [
            'general_tab' => [
                'label' => 'admin.general_configuration',
                'icon' => '<i class="kt-menu__link-icon flaticon-settings-1"></i>',
                'intro' => 'admin.general_configuration',
                'td' => [
                    ['name' => 'name', 'type' => 'text', 'class' => 'require', 'label' => 'admin.web_name'],
                    ['name' => 'hotline', 'type' => 'text', 'label' => 'admin.hotline', 'group_class' => 'col-xs-12 col-md-6'],
                    ['name' => 'email', 'type' => 'text', 'label' => 'admin.email', 'group_class' => 'col-xs-12 col-md-6'],
                    ['name' => 'address', 'type' => 'textarea', 'label' => 'admin.address_main'],
                    ['name' => 'logo', 'type' => 'file_editor', 'label' => 'admin.logo_admin', 'group_class' => 'col-xs-12 col-md-6'],
                    ['name' => 'favicon', 'type' => 'file_editor', 'label' => 'admin.favicon', 'des' => 'Biểu tượng Site là ảnh icon bạn nhìn thấy ở bên trái tên website ở đầu mỗi tab trên cửa sổ trình duyệt. Icon phải là hình vuông và ít nhất 512 × 512 pixel.', 'group_class' => 'col-xs-12 col-md-6'],
                    ['name' => 'background_login', 'type' => 'file_editor', 'label' => 'Hình nền màn hình đăng nhập', 'des' => 'Hình ảnh là hình nền mà bạn nhìn thấy ở bên màn hình đăng nhập website ', 'group_class' => 'col-xs-12 col-md-6'],
                ]
            ],

            'mail' => [
                'label' => 'admin.configuration_send_mail',
                'icon' => '<i class="flaticon-mail"></i>',
                'intro' => 'admin.configuration_send_mail',
                'td' => [
                    ['name' => 'driver', 'type' => 'select', 'label' => 'admin.species', 'options' => [
                        'mailgun' => 'Mailgun',
                        'smtp' => 'Smtp',
                    ], 'class' => ''],

                    ['name' => 'mail_name', 'type' => 'text', 'label' => 'admin.name_send', 'class' => ''],

//                    ['name' =>'mailgun_host','type' =>'text','label' =>'Máy chủ','class' =>'mail-option mailgun'],
                    ['name' => 'smtp_host', 'type' => 'text', 'label' => 'admin.sever', 'class' => 'mail-option smtp'],

                    ['name' => 'mailgun_port', 'type' => 'number', 'label' => 'admin.gate', 'class' => 'mail-option mailgun'],
                    ['name' => 'smtp_port', 'type' => 'number', 'label' => 'admin.gate', 'class' => 'mail-option smtp'],

//                    ['name' =>'mailgun_encryption','type' =>'select','label' =>'Mã hóa','options' => [
//                       'tls' =>'tls',
//                       'ssl' =>'ssl',
//                    ],'class' =>'mail-option mailgun'],
                    ['name' => 'smtp_encryption', 'type' => 'select', 'label' => 'admin.encode', 'options' => [
                        'tls' => 'tls',
                        'ssl' => 'ssl',
                    ], 'class' => 'mail-option smtp'],

                    ['name' => 'mailgun_username', 'type' => 'text', 'label' => 'admin.mail_send', 'class' => 'mail-option mailgun'],
                    ['name' => 'smtp_username', 'type' => 'text', 'label' => 'admin.mail_send', 'class' => 'mail-option smtp'],

//                    ['name' =>'mailgun_mail','type' =>'text','label' =>'Mail hiển thị','class' =>'mail-option mailgun'],
//                    ['name' =>'smtp_mail','type' =>'text','label' =>'Mail hiển thị','class' =>'mail-option smtp'],

//                    ['name' =>'mailgun_password','type' =>'text','label' =>'Mật khẩu','class' =>'mail-option mailgun'],
                    ['name' => 'smtp_password', 'type' => 'text', 'label' => 'admin.password', 'class' => 'mail-option smtp'],

                    ['name' => 'mailgun_domain', 'type' => 'text', 'label' => 'admin.domain_mailgun', 'class' => 'mail-option mailgun'],
                    ['name' => 'mailgun_secret', 'type' => 'text', 'label' => 'admin.key_mailgun', 'class' => 'mail-option mailgun'],
                    ['name' => 'admin_receives_mail', 'type' => 'checkbox', 'label' => 'admin.mail_admin_send'],
                    ['name' => 'admin_emails', 'type' => 'textarea', 'label' => 'admin.mail_admin_take', 'des' => 'Các mail cách nhau bởi dấu phẩy. VD: example@gmail.com, example2@gmail.com'],
                    ['name' => 'btn_test_mail', 'type' => 'btn_test_mail', 'label' => 'admin.try_send_mail'],
                    ['name' => 'inner', 'type' => 'inner', 'label' => 'Tùy chỉnh mail', 'html' => '<a href="/admin/setting/mail-header" target="_blank">Sửa đầu mail</a><br>
<a href="/admin/setting/mail-footer" target="_blank">Sửa chân mail</a>'],
                ]
            ],
            'seo_tab' => [
                'label' => 'admin.configuration_seo',
                'icon' => '<i class="flaticon-globe"></i>',
                'intro' => 'admin.configuration_seo',
                'td' => [
                    ['name' => 'robots', 'type' => 'select', 'options' =>
                        [
                            'noindex,nofollow' => 'noindex, nofollow',
                            'index,follow' => 'index, follow',
                            'index,nofollow' => 'index, nofollow',
                            'noindex,follow' => 'noindex, follow',
                        ], 'label' => 'admin.status', 'value' => 'noindex, nofollow'],
                    ['name' => 'default_meta_title', 'type' => 'text', 'label' => 'admin.meta_title'],
                    ['name' => 'default_meta_description', 'type' => 'text', 'label' => 'admin.meta_description'],
                    ['name' => 'default_meta_keywords', 'type' => 'text', 'label' => 'admin.meta_keywords'],
                ]
            ],

            'role_tab' => [
                'label' => 'Phân quyền',
                'icon' => '<i class="flaticon-globe"></i>',
                'intro' => 'Cấu hình phân quyền',
                'td' => [
                    ['name' => 'allow_admin_account_registration', 'type' => 'checkbox', 'label' => 'Cho phép đăng ký tài khoản trang admin'],
                    ['name' => 'role_default_id', 'type' => 'select_model', 'model' => \App\Models\Roles::class, 'display_field' => 'display_name', 'label' => 'Quyền mặc định khi đăng ký tài khoản trong admin'],
                ]
            ],
            'admin_setting_tab' => [
                'label' => 'admin.configuration_admin',
                'icon' => '<i class="flaticon-more-v4"></i>',
//               'intro' =>'',
                'td' => [
                    ['name' => 'admin_head_code', 'type' => 'textarea', 'label' => 'admin.insert_code', 'inner' => 'rows=20'],
                    ['name' => 'admin_footer_code', 'type' => 'textarea', 'label' => 'admin.insert_code_footer', 'inner' => 'rows=20'],
                ]
            ],

        ]
    ];

    public function getIndex(Request $request)
    {

        $data['page_type'] = 'list';

        $module = \Eventy::filter('setting.custom_module', $this->module);
        if (!$_POST) {
            $listItem = $this->model->get();
            $tabs = [];
            foreach ($listItem as $item) {
                $tabs[$item->type][$item->name] = $item->value;
            }
            #
            $data['tabs'] = $tabs;
            $data['page_title'] = $module['label'];
            $data['module'] = $module;
            return view(config('core.admin_theme') . '.setting.view')->with($data);
        } else {
            foreach ($module['tabs'] as $type => $tab) {
                $data = $this->processingValueInFields($request, $tab['td'], $type . '_');

                foreach ($data as $key => $value) {
                    $item = Setting::where('name', $key)->where('type', $type)->first();

                    if (!is_object($item)) {
                        $item = new Setting();
                        $item->name = $key;
                        $item->type = $type;
                    }
                    $item->value = $value;
                    $item->save();

                }
            }

            if (Schema::hasTable('admin_logs')) {
                $this->adminLog($request, $item = false, 'setting');
            }

            CommonHelper::flushCache($this->module['table_name']);
            CommonHelper::one_time_message('success', 'Cập nhật thành công!');

            if ($request->return_direct == 'save_exit') {
                return redirect('admin/dashboard');
            }

            return redirect('/admin/setting');
        }
    }

    public function testMail()
    {
        dd(1);
    }

    public function configMailHeader(Request $request)
    {
        $data['page_type'] = 'list';
        if (!$_POST) {
            $data['module'] = $this->module;
            $data['page_title'] = $data['module']['label'] = 'Sửa đầu email';

            return view(config('core.admin_theme') . '.emails.option_header_mail', $data);
        } else {
            Setting::where('name', 'header_mail')->where('type', 'mail')->update(['value' => $request->header_mail]);

            CommonHelper::one_time_message('success', 'Cập nhật thành công!');
            return redirect('admin/setting/mail-header');
        }
    }

    public function configMailFooter(Request $request)
    {
        $data['page_type'] = 'list';
        if (!$_POST) {
            $data['page_title'] = $data['module']['label'] =  'Sửa chân email';
            $data['module'] = $this->module;
            return view(config('core.admin_theme') . '.emails.option_footer_mail', $data);
        } else {
            Setting::where('name', 'footer_mail')->where('type', 'mail')->update(['value' => $request->footer_mail]);

            CommonHelper::one_time_message('success', 'Cập nhật thành công!');
            return redirect('admin/setting/mail-footer');
        }
    }

    public function ajaxUpdate(Request $r) {
        Setting::where('name', $r->name)->where('type', $r->type)->update(['value' => $r->value]);
        return response()->json([
            'status' => true,
            'msg' => 'Thành công'
        ]);
    }
}
