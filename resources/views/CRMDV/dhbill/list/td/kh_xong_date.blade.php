<?php
$bill_process = \App\CRMDV\Models\BillProgress::where('bill_id', $item->id)->first();
if (@$bill_process->kh_xong_date != null) {
    echo date('d/m/Y', strtotime($bill_process->kh_xong_date));
}
?>
