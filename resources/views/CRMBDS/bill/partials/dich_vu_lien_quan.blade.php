<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {{ trans('CRMBDS_admin.hop_dong_da_ky') }}
            </h3>
        </div>
    </div>
    <!--begin::Form-->
    <div class="kt-form">
        <div class="kt-portlet__body">
            <div class="kt-section kt-section--first">
                <?php 
                $bill_relate = \App\CRMBDS\Models\Bill::select('id', 'domain', 'service_id', 'registration_date', 'total_price_contract')
                    ->where('status', 1)->where('customer_id', $customer_id)->where('id', '!=', @$bill_id)->get();
                ?>
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Dự án</th>
                            <th>{{ trans('CRMBDS_admin.service') }}</th>
                            <th>Tổng tiền</th>
                            <th>Ngày ký</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bill_relate as $v)
                            <tr>
                                <td style="    border: 1px dotted #ccc;">{{ $v->domain }}</td>
                                <td style="    border: 1px dotted #ccc;">{{ @$v->service->name_vi }}</td>
                                <td style="    border: 1px dotted #ccc;">{{ number_format($v->total_price_contract) }}</td>
                                <td style="    border: 1px dotted #ccc;">{{ date('d-m-Y', strtotime($v->registration_date)) }}</td>
                                <td style="    border: 1px dotted #ccc;"><a href="/admin/bill/edit/{{ $v->id }}" target="_blank">Xem</a> </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </ul>
            </div>
        </div>
    </div>
</div>