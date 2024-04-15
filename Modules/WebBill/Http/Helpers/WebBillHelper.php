<?php

namespace Modules\WebBill\Http\Helpers;

use App\Http\Helpers\CommonHelper;

class WebBillHelper
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
