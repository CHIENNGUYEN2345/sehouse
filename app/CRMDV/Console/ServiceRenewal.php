<?php

namespace App\CRMDV\Console;

use App\Mail\MailServer;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\WebBill\Models\Bill;

class ServiceRenewal extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'services:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cảnh báo sắp hết hạn dịch vụ.';

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


        $settings = Setting::where('type', 'web_service')->pluck('value', 'name')->toArray();
        $settings2 = Setting::whereIn('name', ['admin_emails', 'mail_name', 'admin_email', 'admin_receives_mail'])->pluck('value', 'name')->toArray();

        // ====|Min|======|Now|====[EXPIRY_DATE]=====|Closed|======|Max|======>
        $group_bills = Bill::where('expiry_date', '<>', Null)->where('expiry_date', '>=', date('Y-m-d'))
            ->where('expiry_date', '<=', date('Y-m-d', strtotime('+' . $settings['close_day'] . ' day')))
            ->where('status', 1)->where('auto_extend', 1)->get()->groupBy('customer_id');

        $mail = 0;

        foreach ($group_bills as $id_user => $bills) {
            dd($bills);
            $code = '#';
            $total_price = 0;
            foreach ($bills as $key => $bill) {
                $code .= $key == 0 ? $bill->id : '-' . $bill->id;
                $total_price += $bill->total_price;
                $bills->customer_id = $bill->customer_id;
            }

            $user = User::find($bills->customer_id);

            $bills->user = $user;
            $bills->price = $total_price;
            $data = [
                'view' => 'webbill::emails.gia_han_dich_vu',
                'user' => $user,
                'list' => $bills,
                'code' => $code,
                'name' => $settings2['mail_name'],
                'subject' => 'Danh sách dịch vụ cần gia hạn'
            ];
            Mail::to($user)->send(new MailServer($data));
            $bills->send_mail = true;
            $mail++;
        }


        if ($settings2['admin_receives_mail'] == 1) {
            $admins = explode(',', $settings2['admin_emails']);
            foreach ($admins as $admin) {
                $user = (object)[
                    'email' => trim($admin),
                    'name' => $settings2['mail_name'],
                ];
                $data = [
                    'view' => 'webbill::emails.gia_han_dich_vu_admin',
                    'user' => $user,
                    'list' => $group_bills,
                    'name' => $settings2['mail_name'],
                    'subject' => 'Danh sách dịch vụ cần gia hạn'
                ];

                Mail::to($user)->send(new MailServer($data));
            }
        }
        echo "Co " . $mail . " email da gui di";
    }
}
