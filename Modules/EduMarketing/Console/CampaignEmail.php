<?php

namespace Modules\EduMarketing\Console;

use App\Mail\MailServer;
use App\Models\Admin;
use App\Models\AdminLog;
use App\Models\Error;
use App\Models\Setting;
use Illuminate\Console\Command;
use Modules\EduMarketing\Models\Customer;
use Modules\EduMarketing\Models\MaketingMail;
use Modules\EduMarketing\Models\Student;

class CampaignEmail extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'campaign:email';

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
    public function handle($campaign_id = false, $send_now = false)
    {
        //  Truy vấn ra các chiến dịch cần gửi hiện tại
        $campaigns = MaketingMail::whereRaw('1=1');
        if ($campaign_id) {
            $campaigns = $campaigns->where('id', $campaign_id);
        }
        if (!$send_now) {
            $campaigns = $campaigns->where('status', 1)->where(function ($query) {
                $query->orWhere('date_send', null);
                $query->orWhere('date_send', '<=', date('Y-m-d'));
            })->where(function ($query) {
                $query->orWhere('finish_send', null);
                $query->orWhere('finish_send', '>=', date('Y-m-d'));
            });
        }
        $campaigns = $campaigns->get();
        $count = 0;
        $msg = '';
        foreach ($campaigns as $campaign) {
            //  Nếu là mail chúc mừng sinh nhật thì tìm kiếm & gửi luôn
            if ($campaign->name == 'Chúc mừng sinh nhật tự động') {
                $mailBirthDay = $this->sendMailBirthDay($campaign);
                $msg .= @$mailBirthDay['msg'] . ". ";
            } else {
                //  Gửi cho từng mailLog đã tạo từ trước
                foreach ($campaign->maillogActive as $mailLog) {
                    try {

                        $this->sendMail($campaign, $mailLog);
                        $mailLog->sent = 1;
                        $count ++;
                    } catch (\Exception $ex) {
                        $mailLog->error = 1;
                        Error::create([
                            'module' => 'campaign marketing',
                            'message' => $ex->getMessage(),
                            'file' => 'Modules/EduMarketing/Console/CampaignEmail@handle'
                        ]);
                    }
                    $mailLog->save();
                }
            }
        }
        if ($count > 0) {
            $msg .= "Đã gửi thành công " . $count . " mail chiến dịch. ";
        }
        print $msg;
        return [
            'status' => true,
            'msg' => $msg == '' ? 'Không gửi được mail nào!' : $msg
        ];
    }

    /*
     * Gửi mail sinh nhật tự động
     * **/
    public function sendMailBirthDay($campaign) {
        $count = 0;
        $users_receive = [];

        //  Lấy danh sách các thằng có ngày sinh là hôm nay
        foreach (explode('|', $campaign->object) as $obj) {
            if ($obj != '') {
                if ($obj == 'customer') {
                    $users = \Modules\EduMarketing\Models\Customer::select('id', 'name', 'email')->where('birthday', 'LIKE', '%' . date('-m-d'))->where(function ($query) use ($campaign) {
                        if ($campaign->customer_tags != null) {
                            foreach ($campaign->customer_tags as $tag) {
                                $query->orWhere('tags', 'LIKE', '%|' . $tag . '|%');
                            }
                        }
                    })->get()->toArray();
                    $users_receive = array_merge($users_receive, $users);
                }

                if ($obj == 'student') {
                    $users = \Modules\EduMarketing\Models\Student::select('id', 'name', 'email')->where('birthday', 'LIKE', '%' . date('-m-d'))->where(function ($query) use ($campaign) {
                        if ($campaign->student_tags != null) {
                            foreach ($campaign->student_tags as $tag) {
                                $query->orWhere('tags', 'LIKE', '%|' . $tag . '|%');
                            }
                        }
                    })->get()->toArray();
                    $users_receive = array_merge($users_receive, $users);
                }


                if ($obj == 'lecturer') {
                    $users = \App\Models\Admin::select('id', 'name', 'email')->where('birthday', 'LIKE', '%' . date('-m-d'))->where(function ($query) use ($campaign) {
                        if ($campaign->lecturer_tags != null) {
                            foreach ($campaign->lecturer_tags as $tag) {
                                $query->orWhere('tags', 'LIKE', '%|' . $tag . '|%');
                            }
                        }
                    })->get()->toArray();
                    $users_receive = array_merge($users_receive, $users);
                }
            }
        }

        //  Foreach ra rồi gửi mail cho từng thằng
        foreach ($users_receive as $user) {
            $user = (object) $user;
            try {
                $mailLog = (object) [
                    'object_id' => $user->id,
                    'type' => 'customer',
                    'email' => $user->email
                ];
                $this->sendMail($campaign, $mailLog);
                $count ++;
            } catch (\Exception $ex) {
                Error::create([
                    'module' => 'campaign marketing',
                    'message' => $ex->getMessage(),
                    'file' => 'Modules/EduMarketing/Console/CampaignEmail@handle'
                ]);
            }
        }
        print "Đã gửi thành công " . $count . " mail chúc mừng sinh nhật";
        return [
            'status' => 1,
            'msg' => "Đã gửi thành công " . $count . " mail chúc mừng sinh nhật",
        ];
    }

    //  Gửi mail cho 1 đối tượng trong log
    public function sendMail($campaign, $mailLog) {
        $this->_mailSetting = Setting::whereIn('type', ['mail'])->pluck('value', 'name')->toArray();

        if ($mailLog->type == 'customer') {
            $object = Customer::find($mailLog->object_id);
        } elseif ($mailLog->type == 'student') {
            $object = Student::find($mailLog->object_id);
        } elseif ($mailLog->type == 'lecturer') {
            $object = Admin::find($mailLog->object_id);
        } elseif ($mailLog->type == 'other') {
            $object = (object)[
                'email' => $mailLog->email,
                'name' => 'bạn',
                'type' => 'other',
                'id' => null
            ];
        }
        $user = (object)[
            'email' => $object->email,
            'name' => $object->name,
            'type' => $mailLog->type,
            'id' => $object->id
        ];

        $campaign = $this->processContentMail($campaign, $user);

        $data = [
            'name' => $this->_mailSetting['mail_name'],
            'user' => $user,
            'subject' => $campaign->subject,
            'email_content' => $campaign->content,
            'view' => 'emails.template_from_db',
            'campaign' => $campaign,
        ];

        \Mail::to($data['user'])->send(new MailServer($data));
        return true;
    }

    public function processContentMail($campaign, $user) {
        $campaign->content = str_replace('{customer_name}', @$user->name, $campaign->content);
        $campaign->content = str_replace('{customer_phone}', @$user->tel, $campaign->content);
        $campaign->content = str_replace('{customer_email}', @$user->email, $campaign->content);
        $campaign->content = str_replace('{customer_address}', @$user->address, $campaign->content);
        return $campaign;
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
