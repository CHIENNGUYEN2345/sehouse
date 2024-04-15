<?php
$bill_ids = explode('|', $item->bill_ids);
echo \App\CRMBDS\Models\Bill::where('status', 1)->whereIn('id', $bill_ids)->count();