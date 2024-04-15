<style>
    .hd_no_tien table{
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .hd_no_tien table th, .hd_no_tien table td{
        text-align: left;
        width: 25%;
    }

    .hd_no_tien table td{
        border-bottom: 1px solid black;
    }

    .hd_no_tien table tbody{
        display: block;
        overflow: auto !important;
        max-height: 400px;
    }

    .hd_no_tien table thead{
        width:100%;
        display: table;
    }
</style>
<div class="kt-portlet kt-portlet--height-fluid hd_no_tien">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                {{ trans('CRMWoo_admin.hop_dong') }} chưa thu tiền
            </h3>
            <?php
            $du_an_khach_xac_nhan_xong = \App\CRMWoo\Models\BillProgress::whereIn('status', ['Khách xác nhận xong', 'Tạm dừng', 'Kết thúc'])->pluck('bill_id')->toArray();
            $don_chua_thu_tien = \App\CRMWoo\Models\Bill::select('id', 'customer_id', 'total_price_contract', 'total_received', 'registration_date', 'saler_id')
                ->whereRaw('total_price_contract != total_received')
//                ->whereIn('id', $du_an_khach_xac_nhan_xong)
                ->orderBy('saler_id', 'ASC')->orderBy('registration_date', 'ASC')->get();
            ?>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="kt-widget12">
            <div class="kt-widget12__content">
                <table class="table table-striped">
                    <thead class="kt-datatable__head">
                    <tr>
                        <th>Tên khách</th>
                        <th>Tổng tiền</th>
                        <th>Đã thu</th>
                        <th>Chưa thu</th>
                        <th>Ngày ký</th>
                        <th>Sale</th>
                    </tr>
                    </thead>
                    <tbody class="kt-datatable__body ps ps--active-y">
                    <?php $tong_tien_chua_thu = 0; ?>
                    @foreach($don_chua_thu_tien as $v)
                        @if($v->total_price_contract != $v->total_received)
                            <tr>
                                <td><a href="/admin/bill/edit/{{ $v->id }}"
                                       target="_blank">{{ @$v->customer->name }}</a></td>
                                <td>{{ number_format($v->total_price_contract, 0, '.', '.') }}đ</td>
                                <td>{{ number_format($v->total_received, 0, '.', '.') }}đ</td>
                                <td>{{ number_format($v->total_price_contract - $v->total_received, 0, '.', '.') }}
                                    đ
                                </td>
                                <td>{{ date('d/m', strtotime($v->registration_date)) }}</td>
                                <td>{{ @$v->saler->name }}</td>
                            </tr>
                                <?php
                                $tong_tien_chua_thu += $v->total_price_contract - $v->total_received;
                                ?>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                Tổng cộng: {{ number_format($tong_tien_chua_thu, 0, '.', '.') }}đ
            </div>
        </div>
    </div>
</div>