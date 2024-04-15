<style type="text/css">
    .lich_su_hop_dong a {
        display: inline-block;
    }
    .lich_su_hop_dong {
        font-size: 11px;
    }
</style>
<div class="kt-portlet lich_su_hop_dong">
    <!--begin::Form-->
    <div class="kt-form">
        <div class="kt-portlet__body">
            <?php 
                $customer = \App\CRMEdu\Models\Admin::select('id', 'name', 'created_at', 'sale_id')->where('tel', $result->tel)->first();
                if (is_object($customer)) {
                    echo 'Đã tạo khách hàng: <a href="/admin/user/'.$customer->id.'" target="_blank">'.$customer->name.'</a> - '.$result->tel.'<br>';
                    $bills = \App\CRMEdu\Models\Bill::select('domain', 'total_price', 'registration_date')->where('customer_id', $customer->id)->get();
                    if (count($bills) > 0) {
                        foreach($bills as $bill) {
                            echo 'Đã ký HĐ: <a href="https://'.$bill->domain.'/" target="_blank">'.$bill->domain.'</a> - lúc: '. date('d-m-Y', strtotime($bill->registration_date)) . ' - D.số: ' . round($bill->total_price / 1000000) . 'tr<br>';
                        }
                    }
                }
            ?>
        </div>
    </div>
</div>