

<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                {{trans('CRMWoo_admin.danh_sach_hd_da_ky')}}
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            <a href="{{ url('/admin/bill/add/?customer_id=' . $result->id) }}"
               class="btn btn-brand btn-elevate btn-icon-sm">
                <i class="la la-plus"></i>
                Tạo {{ trans('CRMWoo_admin.hop_dong') }}
            </a>
        </div>
    </div>
    <!--begin::Form-->
    <div class="kt-form">
        <div class="kt-portlet__body">

            <?php
            $bills = \App\CRMWoo\Models\Bill::select('id', 'service_id', 'total_price', 'total_price_contract', 'total_received', 'registration_date')->where('customer_id', $result->id)->get();
            ?>
            @if(count($bills) > 0)
            <table class="table table-striped">
                <thead class="kt-datatable__head">
                <tr class="kt-datatable__row" style="left: 0px;">
                    <th>ID</th>
                    <th>{{ trans('CRMWoo_admin.dich_vu') }}</th>
                    <th>{{ trans('CRMWoo_admin.doanh_so') }}</th>
                    <th>{{ trans('CRMWoo_admin.tong_tien') }}</th>
                    <th>{{ trans('CRMWoo_admin.da_thu') }}</th>
                    <th>{{ trans('CRMWoo_admin.chua_thu') }}</th>
                    <th>{{ trans('CRMWoo_admin.ngay_ky') }}</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody class="kt-datatable__body ps ps--active-y" style="max-height: 496px;">
                    @foreach($bills as $b)
                        <tr>
                            <td>{{ $b->id }}</td>
                            <td>{{ @$b->service->name_vi }}</td>
                            <td>{{ number_format($b->total_price) }}đ</td>
                            <td>{{ number_format($b->total_price_contract) }}đ</td>
                            <td>{{ number_format($b->total_received) }}đ</td>
                            <td>{{ number_format($b->total_price_contract - $b->total_received) }}đ</td>
                            <td>{{ date('d/m/Y', strtotime($b->registration_date)) }}</td>
                            <td><a href="/admin/bill/edit/{{ $b->id }}" target="_blank">Xem chi tiết</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif



        </div>
    </div>
    <!--end::Form-->
</div>