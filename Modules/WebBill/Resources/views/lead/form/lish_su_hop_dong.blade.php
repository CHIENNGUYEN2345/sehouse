<div class="kt-portlet">
    <!--begin::Form-->
    <div class="kt-form">
        <div class="kt-portlet__body">
            <?php 
                $customer = \Modules\WebBill\Models\Admin::select('id', 'name', 'created_at', 'sale_id')->where('tel', $result->tel)->first();
                if (is_object($customer)) {
                    echo 'Đã tạo khách hàng: <a href="/admin/customer/'.$customer->id.'" target="_blank">'.$customer->name.'</a><br>';
                    $bills = \Modules\WebBill\Models\Bill::select('domain', 'total_price', 'registration_date')->where('customer_id', $customer->id)->get();
                    if (count($bills) > 0) {
                        foreach($bills as $bill) {
                            echo 'Đã ký HĐ: <a href="'.$bill->domain.'/" target="_blank">'.$bill->domain.'</a> - lúc: '. date('d-m-Y', strtotime($bill->registration_date)) . ' - D.số: ' . round($bill->total_price / 1000000) . 'tr<br>';
                        }
                    }
                }
            ?>
        </div>
    </div>
</div>