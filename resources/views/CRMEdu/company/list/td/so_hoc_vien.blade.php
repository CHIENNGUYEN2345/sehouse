<?php
$bill_ids = explode('|', $item->bill_ids);
echo \App\CRMEdu\Models\Bill::where('status', 1)->whereIn('id', $bill_ids)->count();