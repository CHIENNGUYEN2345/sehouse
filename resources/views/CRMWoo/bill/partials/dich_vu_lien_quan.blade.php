<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {{ trans('CRMWoo_admin.hop_dong_khac_cua_khach_nay') }}
            </h3>
        </div>
    </div>
    <!--begin::Form-->
    <div class="kt-form">
        <div class="kt-portlet__body">
            <div class="kt-section kt-section--first">
                <?php 
                $bill_relate = \App\CRMWoo\Models\Bill::select('id', 'domain', 'service_id', 'expiry_date')->where('status', 1)->where('customer_id', $result->customer_id)->where('id', '!=', $result->id)->get();
                ?>
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ trans('CRMWoo_admin.hop_dong') }}</th>
                            <th>{{ trans('CRMWoo_admin.service') }}</th>
                            <th>Ngày hết hạn</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bill_relate as $v)
                            <tr>
                                <td style="    border: 1px dotted #ccc;">{{ $v->domain }}</td>
                                <td style="    border: 1px dotted #ccc;">{{ $v->service->name_vi }}</td>
                                <td style="    border: 1px dotted #ccc;">{{ date('d-m-Y', strtotime($v->expiry_date)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </ul>
            </div>
        </div>
    </div>
</div>