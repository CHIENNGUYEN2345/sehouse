<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Khóa học khác đã ký
            </h3>
        </div>
    </div>
    <!--begin::Form-->
    <div class="kt-form">
        <div class="kt-portlet__body">
            <div class="kt-section kt-section--first">
                <?php 
                $bill_relate = \App\CRMEdu\Models\Bill::select('id', 'total_price_contract', 'service_id', 'registration_date')->where('status', 1)->where('customer_id', $result->customer_id)->where('id', '!=', $result->id)->get();
                ?>
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Lớp học</th>
                            <th>Ngày ký</th>
                            <th>Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bill_relate as $v)
                            <tr>
                                <td style="    border: 1px dotted #ccc;">{{ $v->service->name_vi }}</td>
                                <td style="    border: 1px dotted #ccc;">{{ date('d-m-Y', strtotime($v->registration_date)) }}</td>
                                <td style="    border: 1px dotted #ccc;">{{ number_format($v->total_price_contract, 0, '.', '.') }}<sup>đ</sup></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </ul>
            </div>
        </div>
    </div>
</div>