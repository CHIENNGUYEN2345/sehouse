<?php

namespace App\Custom\Controllers\Helpers;

use App\Http\Helpers\CommonHelper;
use App\Mail\MailServer;
use App\Models\Setting;
use Auth;
use Mail;
use Modules\EduMarketing\Models\MarketingMail;
use App\CRMDV\Models\LinkErrorLogs;
use View;

class CustomHelper
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
}
