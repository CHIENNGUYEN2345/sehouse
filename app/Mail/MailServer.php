<?php

namespace App\Mail;

use App\Http\Helpers\CommonHelper;
use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Queue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Illuminate\Contracts\Mail\Mailer;
use Swift_Mailer;
use Swift_SmtpTransport;



class MailServer extends Mailable //implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $data;

    protected $_settings;

    protected $_account;

    public function __construct($data)
    {
        $this->data = $data;
        $this->_account = $this->configMail();
        $this->_settings = Setting::where('type', 'general_tab')->pluck('value', 'name')->toArray();
    }

    public function build()
    {
        $this->data['content'] = $this->processContentMail($this->data['content']);
        $this->from($this->_account['username'], $this->_account['mail_name'])
            ->subject($this->data['subject']);
        if (isset($this->data['cc'])) {
            $this->cc($this->data['cc']);
        }
        $this->view('emails.template_from_db', $this->data);

        return true;
    }

    public function processContentMail($html)
    {
        $html = str_replace('{site_title}', @$this->_settings['name'], $html);

        $html = str_replace('{site_url}', \URL::to('/'), $html);

        $html = str_replace('{site_logo}', '<a href="//'.env('DOMAIN').'"><img style="max-width: 150px; max-height: 150px;" src="'.CommonHelper::getUrlImageThumbHasDomain(@$this->_settings['logo'], null, 200, env('DOMAIN')).'"/></a>', $html);

        $html = str_replace('{site_hotline}', @$this->_settings['hotline'], $html);

        $html = str_replace('{site_address}', @$this->_settings['address'], $html);

        $html = str_replace('{site_admin_email}', @$this->_settings['email'], $html);

        $html = str_replace('{date_time}', date('d/m'), $html);

        $html = str_replace('{date_year}', date('Y'), $html);

        return $html;
    }

    /*public function processContentMail()
    {
        $this->_settings['header_mail'] = str_replace('{header}', $this->_settings['mail_name'], $this->_settings['header_mail']);
        $this->_settings['footer_mail'] = str_replace('{header}', $this->_settings['mail_name'], $this->_settings['footer_mail']);

        $this->_settings['header_mail'] = str_replace('{footer}', $this->_settings['mail_name'], $this->_settings['header_mail']);
        $this->_settings['footer_mail'] = str_replace('{footer}', $this->_settings['mail_name'], $this->_settings['footer_mail']);

        $this->_settings['header_mail'] = str_replace('{site_title}', $this->_settings['name'], $this->_settings['header_mail']);
        $this->_settings['footer_mail'] = str_replace('{site_title}', $this->_settings['name'], $this->_settings['footer_mail']);

        $this->_settings['header_mail'] = str_replace('{site_url}', \URL::to('/'), $this->_settings['header_mail']);
        $this->_settings['footer_mail'] = str_replace('{site_url}', \URL::to('/'), $this->_settings['footer_mail']);

        $this->_settings['header_mail'] = str_replace('{site_logo}', CommonHelper::getUrlImageThumbHasDomain($this->_settings['logo'], null, 200, env('DOMAIN')), $this->_settings['header_mail']);
        $this->_settings['footer_mail'] = str_replace('{site_logo}', CommonHelper::getUrlImageThumbHasDomain($this->_settings['logo'], null, 200, env('DOMAIN')), $this->_settings['footer_mail']);

        $this->_settings['header_mail'] = str_replace('{date_time}', date('d/m'), $this->_settings['header_mail']);
        $this->_settings['footer_mail'] = str_replace('{date_time}', date('d/m'), $this->_settings['footer_mail']);

        $this->_settings['header_mail'] = str_replace('{date_year}', date('Y'), $this->_settings['header_mail']);
        $this->_settings['footer_mail'] = str_replace('{date_year}', date('Y'), $this->_settings['footer_mail']);

        $this->_settings['header_mail'] = str_replace('{site_admin_email}', $this->_settings['email'], $this->_settings['header_mail']);
        $this->_settings['footer_mail'] = str_replace('{site_admin_email}', $this->_settings['email'], $this->_settings['footer_mail']);
        return true;
    }*/

    function configMail()
    {
        if (isset($this->data['sender_account'])) {
            $sender_account = $this->data['sender_account'];




            /*


            $username = @$sender_account['username'];
            $config =
                [
                    'mail.from' => [
                        'address' => $username,
                        'name' => @$sender_account['mail_name'],
                    ],
                    'mail.driver' => @$sender_account['driver'],
                ];

            if (@$sender_account['driver'] == 'mailgun') {
                $config['services.mailgun'] =
                    [
                        'domain' => trim(@$sender_account['mailgun_domain']),
                        'secret' => trim(@$sender_account['mailgun_secret']),
                    ];
                $config['mail.port'] = @$sender_account['port'];
                $config ['mail.username'] = @$sender_account['username'];
            } else {
                $config['mail.port'] = @$sender_account['port'];
                $config['mail.password'] = @$sender_account['password'];
                $config['mail.encryption'] = @$sender_account['smtp_encryption'];
                $config['mail.host'] = @$sender_account['host'];
                $config['mail.username'] = @$sender_account['username'];
            }
            config($config);*/
        } else {
            $sender_account = [
                'username' => env('MAIL_USERNAME'),
                'mail_name' => env('DOMAIN')
            ];
        }


        if (isset($this->data['sender_account'])) {
            $sender_account = $this->data['sender_account'];

            if (@$sender_account['driver'] == 'mailgun') {
                $config =
                    [
                        'mail.from' => [
                            'address' => @$sender_account['username'],
                            'name' => @$sender_account['mail_name'],
                        ],
                        'mail.driver' => @$sender_account['driver'],
                    ];

                $config['services.mailgun'] =
                    [
                        'domain' => trim(@$sender_account['mailgun_domain']),
                        'secret' => trim(@$sender_account['mailgun_secret']),
                    ];
                $config['mail.port'] = @$sender_account['port'];
                $config ['mail.username'] = @$sender_account['username'];
                config($config);
            } else {
                /*$config['mail.port'] = @$sender_account['port'];
                $config['mail.password'] = @$sender_account['password'];
                $config['mail.encryption'] = @$sender_account['smtp_encryption'];
                $config['mail.host'] = @$sender_account['host'];
                $config['mail.username'] = @$sender_account['username'];*/

                $transport = new Swift_SmtpTransport($sender_account['host'], $sender_account['port'], $sender_account['smtp_encryption']);
                $transport->setUsername($sender_account['username']);
                $transport->setPassword($sender_account['password']);

                $gmail = new \Swift_Mailer($transport);

                \Mail::setSwiftMailer($gmail);
            }
        } else {
            $sender_account = [
                'username' => env('MAIL_USERNAME'),
                'mail_name' => env('DOMAIN')
            ];
        }

//        dd($sender_account);
        return $sender_account;
    }
}
