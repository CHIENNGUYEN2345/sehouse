<div class="kt-portlet kt-portlet--height-fluid">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                Top sale
            </h3>
            <?php
            $saler_ids = \App\Models\RoleAdmin::whereIn('role_id', [
                2,      //  quyền sale
                182,    //  quyền trưởng phòng KD
                186,    //  giám đốc kinh doanh
            ])->pluck('admin_id')->toArray();

            $best_sales = \App\CRMBDS\Models\Bill::selectRaw('Sum(total_price) as total_price, saler_id')
                ->whereRaw($whereRegistration)
                ->whereNotIn('saler_id', [170])     //  loại trừ tài khoản Hoàng Hùng
                ->whereIn('saler_id', $saler_ids)    //  chỉ nằm trong các sale của cty, ko tính ctv
                ->groupBy('saler_id')->orderBy('total_price', 'desc')->get();
            ?>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="kt-widget12">
            <div class="kt-widget12__content">
                <table class="table table-striped">
                    <thead class="kt-datatable__head">
                    <tr>
                        <th>Sale</th>
                        <th>Mã</th>
                        <th>Phòng</th>
                        <th>Doanh số</th>
                    </tr>
                    </thead>
                    <tbody class="kt-datatable__body ps ps--active-y">
                    <?php
                    $room_ids = [
                        '' => 'Khác',
                        1 => 'Phòng kinh doanh 1',
                        2 => 'Phòng kinh doanh 2',
                        3 => 'Phòng kinh doanh 3',
                        4 => 'Phòng kinh doanh 4',
                        5 => 'Phòng kinh doanh 5',
                        6 => 'Phòng Telesale',
                        20 => 'Marketing',
                    ];
                    $tong_doanh_so = 0;
                    $ds_phong = [];
                    ?>
                    @foreach($best_sales as $v)
                            <?php
                            $tong_doanh_so += $v->total_price;
                            if (!isset($ds_phong[@$v->saler->room_id])) {
                                @$ds_phong[@$v->saler->room_id] = $v->total_price;
                            } else {
                                @$ds_phong[@$v->saler->room_id] += $v->total_price;
                            }
                            ?>
                        <tr>
                            <td><a target="_blank"
                                   href="/admin/bill?search=true&saler_id={{ $v->saler_id }}&from_date={{ date('Y-m-01') }}&registration_date=1">{{ @$v->saler->name }}</a>
                            </td>
                            <td>{{ @$v->saler->code }}</td>
                            <td>{{ @$room_ids[$v->saler->room_id] }}</td>
                            <td @if(date('d')/30 > $v->total_price/10000000) class="text-red" @endif>{{ number_format($v->total_price, 0, '.', '.') }}</td>
                        </tr>
                    @endforeach
                    <?php
                    arsort($ds_phong);
                    ?>
                    <tr>
                        <td><strong>TỔNG CỘNG</strong></td>
                        <td></td>
                        <td></td>
                        <td><strong><a target="_blank" href="/admin/bill?search=true&from_date={{ date('Y-m-01') }}&registration_date=1"
                                    style="display: inline-block;">{{ number_format($tong_doanh_so, 0, '.', '.') }}</a></strong></td>
                    </tr>
                    </tbody>
                </table>
                @foreach($ds_phong as $room_id => $ds)
                    {{ $room_ids[$room_id] }}: {{ number_format($ds, 0, '.', '.') }}<br>
                @endforeach
                <i style="font-size: 8px;">Truy vấn theo bộ lọc thời gian</i>
            </div>
        </div>
    </div>
</div>