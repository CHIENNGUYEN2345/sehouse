<?php

namespace App\Http\Controllers\Admin;

use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class BackupController extends CURDBaseController
{

    protected $module = [
        'code' => 'backup',
        'label' => 'admin.backup',
        'modal' => '\App\Models\Setting',
        'tabs' => [
            'backup_database' => [
                'label' => 'admin.database',
                'icon' => '<i class="kt-menu__link-icon flaticon-settings-1"></i>',
                'intro' => 'admin.backup_db',
                'view' => 'admin.themes.metronic1.backup.database.list',
                'td' => [
                    ['name' => 'inner', 'type' => 'inner', 'label' => '', 'html' => '<p>Cấu hình lịch tự động</p>'],
                    ['name' => 'status', 'type' => 'checkbox', 'label' => 'admin.active'],
                    ['class' => 'required', 'name' => 'minute_backup', 'type' => 'text', 'label' => 'admin.minute', 'des' => 'Nhập vào số phút, có thể nhập vào 2 giá trị các nhau bởi dấu phảy :<br> Ví dụ ( phút 20 và tháng 50 ) : 20, 50'],
                    ['class' => 'required', 'name' => 'hour_backup', 'type' => 'text', 'label' => 'admin.hours', 'des' => 'Nhập vào số tháng, có thể nhập vào 2 giá trị các nhau bởi dấu phảy'],
                    ['class' => 'required', 'name' => 'day_in_month_backup', 'type' => 'text', 'label' => 'admin.date_month', 'des' => 'Nhập vào số ngày trong tháng, có thể nhập vào 2 giá trị các nhau bởi dấu phảy'],
                    ['class' => 'required', 'name' => 'month_backup', 'type' => 'text', 'label' => 'admin.month', 'des' => 'Nhập vào số tháng, có thể nhập vào 2 giá trị các nhau bởi dấu phảy'],
                    ['class' => 'required', 'name' => 'day_in_week_backup', 'type' => 'text', 'label' => 'admin.day', 'des' => 'Nhập vào thứ trong tuần, có thể nhập vào 2 giá trị các nhau bởi dấu phảy (chủ nhật = 0 hoặc 7)'],
                ]
            ],
        ]
    ];

    public function getIndex(Request $request)
    {

        $data['page_type'] = 'list';
        if (!$_POST) {
            $listItem = $this->model->get();
            $tabs = [];
            foreach ($listItem as $item) {
                $tabs[$item->type][$item->name] = $item->value;
            }
            #
            $data['tabs'] = $tabs;
            $data['page_title'] = $this->module['label'];
            $data['module'] = $this->module;
            return view(config('core.admin_theme') . '.backup.view')->with($data);
        } else {

            foreach ($this->module['tabs'] as $type => $tab) {

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
            CommonHelper::one_time_message('success', 'Cập nhật thành công!');

            if ($request->return_direct == 'save_exit') {
                return redirect('admin/dashboard');
            }

            return redirect('/admin/backup');
        }
    }

    //  Backup database
    public function backupDatabase()
    {

        $result = $this->BackUpDB();
        $class = 'error';
        if ($result['status']) {
            $class = 'success';
        }
        CommonHelper::one_time_message($class, $result['msg']);
        return back();
    }

    public function BackUpDB()
    {
        try {
            Config::set('filesystems.disks.local.root', storage_path('app/file-backup'));
            Artisan::call('backup:run', [
                '--only-db' => true
            ]);
            if (is_dir(storage_path('app/file-backup'))) {
                $files = array_diff(scandir(storage_path('app/file-backup')), array('.', '..'));
                $files = array_reverse($files);
                foreach ($files as $key => $file) {
                    if ($key >= 3) {
                        $file_url = storage_path('app/file-backup/' . $file);
                        unlink($file_url);
                    }
                }
            }
            return [
                'status' => true,
                'msg' => 'Sao lưu thành công'
            ];
        } catch (\Exception $exception) {
            return [
                'status' => false,
                'msg' => $exception->getMessage()
            ];
        }
    }

    public function downloadDB($name)
    {
        $file_url = storage_path('app/file-backup/' . $name);;
        if (file_exists($file_url)) {
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
            readfile($file_url);
        } else {
            CommonHelper::one_time_message('error', "Không tìm thấy file");
            return back();
        }
    }

    public function deleteDB($name)
    {
        $file_url = storage_path('app/file-backup/' . $name);
        if (unlink($file_url)) {
            CommonHelper::one_time_message('success', 'Xóa thành công!');
        } else {
            CommonHelper::one_time_message('error', "Không tìm thấy file");
        }
        return back();
    }
}
