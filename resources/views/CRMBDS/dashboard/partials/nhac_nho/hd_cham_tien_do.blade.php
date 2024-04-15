<div class="kt-portlet kt-portlet--height-fluid">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                lớp học chậm tiến độ
            </h3>
            <?php
            $hd_nho_cham = \App\CRMBDS\Models\Bill::rightJoin('bill_progress', 'bill_progress.bill_id', '=', 'bills.id')
                ->select('bills.id', 'bills.domain', 'bills.registration_date', 'bills.saler_id', 'bill_progress.status', 'bills.service_id',
                    'bill_progress.dh_id', 'bill_progress.kt_id', 'bills.total_received', 'bills.total_price_contract')
                ->whereNotIn('bill_progress.status', ['Khách xác nhận xong', 'Tạm dừng', 'Kết thúc', 'Bỏ'])
                ->whereIn('bills.service_id', [ //  Các gói LDP, WP tiết kiệm - cơ bản phải xong trong 10 ngày
                    1,  //  ldp
                    10, //  wp tiết kiệm
                    11, //  wp cơ bản
                    17, //  ldp tiết kiệm
                    18, //  ldp cơ bản
                    19, //  ldp chuyên nghiệp
                    20, //  ldp cao cấp
                    21  //  ldp theo yêu cầu
                ])
                ->where('bills.registration_date', '<', date('Y-m-d H:i:s', time() - 20 * 24 * 60 * 60))
                ->orderBy('bills.registration_date', 'ASC')->orderBy('bill_progress.dh_id', 'ASC')->get();

            $hd_lon_cham = \App\CRMBDS\Models\Bill::rightJoin('bill_progress', 'bill_progress.bill_id', '=', 'bills.id')
                ->select('bills.id', 'bills.domain', 'bills.registration_date', 'bills.saler_id', 'bill_progress.status', 'bills.service_id',
                    'bill_progress.dh_id', 'bill_progress.kt_id', 'bills.total_received', 'bills.total_price_contract')
                ->whereNotIn('bill_progress.status', ['Khách xác nhận xong', 'Tạm dừng', 'Kết thúc', 'Bỏ'])
                ->whereNotIn('bills.service_id', [ //  Các gói LDP, WP tiết kiệm - cơ bản phải xong trong 10 ngày
                    1,  //  ldp
                    10, //  wp tiết kiệm
                    11, //  wp cơ bản
                    17, //  ldp tiết kiệm
                    18, //  ldp cơ bản
                    19, //  ldp chuyên nghiệp
                    20, //  ldp cao cấp
                    21  //  ldp theo yêu cầu
                ])
            ->where('bills.registration_date', '<', date('Y-m-d H:i:s', time() - 40 * 24 * 60 * 60))
                ->orderBy('bills.registration_date', 'ASC')->orderBy('bill_progress.dh_id', 'ASC')->get();
            ?>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="kt-widget12">
            <div class="kt-widget12__content">
                <table class="table table-striped">
                    <thead class="kt-datatable__head">
                    <tr>
                        <th>Tên miền</th>
                        <th>khoá học</th>
                        <th>Tiến độ</th>
                        <th>Ngày ký</th>
                        <th>Thanh toán</th>
                        <th>Sale</th>
                        <th>Điều hành</th>
                        <th>Kỹ thuật</th>
                    </tr>
                    </thead>
                    <tbody class="kt-datatable__body ps ps--active-y">
                    @foreach($hd_nho_cham as $v)
                        <tr>
                            <td><a href="/admin/bill/edit/{{ $v->id }}"
                                   target="_blank">{{ $v->domain }}</a></td>
                            <td>{{ @$v->service->name_vi }}</td>
                            <td>{{ $v->status }}</td>
                            <td @if(strtotime($v->registration_date) < time() - 30 * 24 * 60 * 60) class="text-red" @endif>{{ date('d/m', strtotime($v->registration_date)) }}</td>
                            <td>
                                @if($v->total_received == $v->total_price_contract)
                                    Đã hết
                                @else
                                    <span class="text-red">{{ round($v->total_received/1000000) }}/{{ round($v->total_price_contract/1000000) }}tr</span>
                                @endif
                            </td>
                            <td>{{ @$v->saler->name }}</td>
                            <td>{{ @\App\Models\Admin::find($v->dh_id)->name }}</td>
                            <td>{{ @\App\Models\Admin::find($v->kt_id)->name }}</td>
                        </tr>
                    @endforeach

                    <tr style="border-top: 3px solid;">
                        <td></td>
                    </tr>

                    @foreach($hd_lon_cham as $v)
                        <tr>
                            <td><a href="/admin/bill/edit/{{ $v->id }}"
                                   target="_blank">{{ $v->domain }}</a></td>
                            <td>{{ @$v->service->name_vi }}</td>
                            <td>{{ $v->status }}</td>
                            <td @if(strtotime($v->registration_date) < time() - 60 * 24 * 60 * 60) class="text-red" @endif>{{ date('d/m', strtotime($v->registration_date)) }}</td>
                            <td>
                                @if($v->total_received == $v->total_price_contract)
                                    Đã hết
                                @else
                                    <span class="text-red">{{ round($v->total_received/1000000) }}/{{ round($v->total_price_contract/1000000) }}tr</span>
                                @endif
                            </td>
                            <td>{{ @$v->saler->name }}</td>
                            <td>{{ @\App\Models\Admin::find($v->dh_id)->name }}</td>
                            <td>{{ @\App\Models\Admin::find($v->kt_id)->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <strong>Tổng: {{ count($hd_lon_cham) + count($hd_nho_cham) }}</strong>
                <i style="font-size: 8px;">Truy vấn: Trạng thái != (Khách xác nhận xong', 'Tạm dừng', 'Kết thúc', 'Bỏ). 45 ngày chưa xong</i>
            </div>
        </div>
    </div>
</div>