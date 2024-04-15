<?php

namespace App\Providers;

use App\Http\Controllers\Admin\MailController;
use App\Http\Helpers\CommonHelper;
use App\Models\Setting;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\ServiceProvider;
use Queue;
use View;

class AppServiceProvider extends ServiceProvider
{

    public function boot()
    {

//        $settings = Setting::where('type', 'scan_error')->pluck('value', 'name')->toArray();
//        dd(  (in_array('CheckErrorLink', \Nwidart\Modules\Facades\Module::allEnabled()) && $settings['scan_status'] == 1) );
//        Schema::defaultStringLength(191);
        $settings = $this->settings();
//        $this->configMail($settings);
        $this->mailServerListen();

//        $this->QueueMailAfter();


        $provider = new \App\CRMDV\Providers\CRMDVServiceProvider();
//        $provider = new \App\CRMEdu\Providers\CRMEduServiceProvider();
        $provider->boot();

        $provider = new \App\Custom\Providers\ServiceProvider();
        $provider->boot();
    }

    public function QueueMailAfter()
    {
        Queue::after(function (JobProcessed $queue) {
            dd($queue->get());
        });
        Queue::before(function (JobProcessing $queue) {
            dd($queue->get());
        });
        Queue::failing(function ($connection, $job, $data) {
            dd("lỗi xảy ra");
        });
    }

    public function register()
    {
        //  Cấu hình link https
        //$this->app['request']->server->set('HTTPS', env('HTTPS'));
		$this->app->bind('path.public', function() {
       return base_path('public_html');
    });
    }

    public function settings()
    {
        $settings = CommonHelper::getFromCache('settings', ['settings']);
        if (!$settings) {
            $settings = Setting::whereIn('type', ['general_tab'])->pluck('value', 'name')->toArray();
            CommonHelper::putToCache('settings', $settings, ['settings']);
        }

        View::share('settings', $settings);
        return $settings;
    }

    /*function configMail($settings)
    {
        if (isset($settings['driver'])) {
            $username = $settings['driver'] == 'mailgun' ? @$settings['mailgun_username'] : @$settings['smtp_username'];
            $config =
                [
                    'mail.from' => [
                        'address' => $username,
                        'name' => @$settings['mail_name'],
                    ],
                    'mail.driver' => @$settings['driver'],
                ];

            if ($settings['driver'] == 'mailgun') {
                $config['services.mailgun'] =
                    [
                        'domain' => trim(@$settings['mailgun_domain']),
                        'secret' => trim(@$settings['mailgun_secret']),
                    ];
                $config['mail.port'] = @$settings['mailgun_port'];
                $config ['mail.username'] = @$settings['mailgun_username'];
            } else {
                $config['mail.port'] = @$settings['smtp_port'];
                $config['mail.password'] = @$settings['smtp_password'];
                $config['mail.encryption'] = @$settings['smtp_encryption'];
                $config['mail.host'] = @$settings['smtp_host'];
                $config['mail.username'] = @$settings['smtp_username'];
            }
//            $config['services.onesignal'] = [
//                'app_id' => '420af10d-5030-4f34-af19-68078fd6467c',
//                'rest_api_key' => 'MTY0MjA5NTktNjgwNS00NGM3LTg3YmYtNzcwMmRhZDUyZmE2'
//            ];
            config($config);
        }
    }*/

    public function mailServerListen()
    {
        \Eventy::addAction('admin.restorePassword', function ($admin) {
            $mailController = new MailController();
            $mailController->postEmailRestorePasswordSendMail($admin);
            return true;
        }, 1, 1);

        \Eventy::addAction('admin.register', function ($admin) {
            $mailController = new MailController();
            $mailController->registerSendMail($admin);
            return true;
        }, 1, 1);
        \Eventy::addAction('admin.change_email', function ($admin) {
            $mailController = new MailController();
            $mailController->changeEMail($admin);
            return true;
        }, 1, 1);
    }
}
