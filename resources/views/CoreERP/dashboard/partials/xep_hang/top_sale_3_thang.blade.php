@if(!isset($_GET['ajax-load']))
    <script>
        $(document).ready(function () {
            $.ajax({
                url: '/admin/dashboard/ajax/load-khoi?ajax-load=true&file=CoreERP.dashboard.partials.xep_hang.top_sale_3_thang',
                type: 'GET',
                data: {

                },
                success: function (html) {
                    $('#top_sale_3_thang').html(html);
                },
                error: function () {
                    console.log('lỗi load khối partials/xep_hang/top_sale_3_thang');
                }
            });
        });
    </script>
    <div class="kt-portlet kt-portlet--height-fluid">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title bold uppercase">
                    Top sale
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="kt-widget12">
                <div class="kt-widget12__content" id="top_sale_3_thang">
                    <img class="tooltip_info_loading"
                         src="/images_core/icons/loading.gif">

                </div>
            </div>
        </div>
    </div>
@else


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
        ?>
        {{--                    Lấy trong tháng hiện tại--}}
        <?php
        $saler_ids = \App\Models\RoleAdmin::whereIn('role_id', [
            2,      //  quyền sale
            182,    //  quyền trưởng phòng KD
            186,    //  giám đốc kinh doanh
        ])->pluck('admin_id')->toArray();

        $best_sales = \App\CRMDV\Models\Bill::selectRaw('Sum(total_price) as total_price, saler_id')
            //                        ->whereRaw($whereRegistration)    //  lấy ngày theo bộ lọc thời gian
            ->whereRaw("registration_date >= '" . date('Y-m-01 00:00:00') . "' AND registration_date <= '" . date('Y-m-t 23:59:00') . "'")  //  lấy trong tháng này
            ->whereNotIn('saler_id', [170])     //  loại trừ tài khoản Hoàng Hùng
            ->whereIn('saler_id', $saler_ids)    //  chỉ nằm trong các sale của cty, ko tính ctv
            ->groupBy('saler_id')->orderBy('total_price', 'desc')->get();



        $tong_doanh_so = 0;
        $ds_phong = [];
        ?>

        <tr>
            <td style="font-weight: bold;">Tháng {{ date('m') }}</td>
        </tr>
        @foreach($best_sales as $v)
                <?php
                $tong_doanh_so += $v->total_price;
                if (!isset($ds_phong[$v->saler->room_id])) {
                    $ds_phong[$v->saler->room_id] = $v->total_price;
                } else {
                    $ds_phong[$v->saler->room_id] += $v->total_price;
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
            <td></td>
            <td></td>
            <td><strong>TỔNG CỘNG</strong></td>
            <td><strong><a target="_blank" href="/admin/bill?search=true&from_date={{ date('Y-m-01') }}&registration_date=1"
                           style="display: inline-block;">{{ number_format($tong_doanh_so, 0, '.', '.') }}</a></strong></td>
        </tr>
        @foreach($ds_phong as $room_id => $ds)
            <tr>
                <td>{{ $room_ids[$room_id] }}:</td>
                <td>{{ number_format($ds, 0, '.', '.') }}</td>
                <td></td>
                <td></td>
            </tr>
        @endforeach



        {{--Lấy trong tháng trước--}}
        <?php
        $saler_ids = \App\Models\RoleAdmin::whereIn('role_id', [
            2,      //  quyền sale
            182,    //  quyền trưởng phòng KD
            186,    //  giám đốc kinh doanh
        ])->pluck('admin_id')->toArray();

        $best_sales = \App\CRMDV\Models\Bill::selectRaw('Sum(total_price) as total_price, saler_id')
            //                        ->whereRaw($whereRegistration)    //  lấy ngày theo bộ lọc thời gian
            ->whereRaw("registration_date >= '" . date('Y-m-01 00:00:00', strtotime('-1 months')) . "' AND registration_date <= '" . date('Y-m-t 23:59:00', strtotime('-1 months')) . "'")  //  lấy trong tháng trước
            ->whereNotIn('saler_id', [170])     //  loại trừ tài khoản Hoàng Hùng
            ->whereIn('saler_id', $saler_ids)    //  chỉ nằm trong các sale của cty, ko tính ctv
            ->groupBy('saler_id')->orderBy('total_price', 'desc')->get();

        $tong_doanh_so = 0;
        $ds_phong = [];
        ?>
        <tr>
            <td style="font-weight: bold;">Tháng {{ date('m', strtotime('-1 months')) }}</td>
        </tr>
        @foreach($best_sales as $v)
                <?php
                $tong_doanh_so += $v->total_price;
                if (!isset($ds_phong[$v->saler->room_id])) {
                    $ds_phong[$v->saler->room_id] = $v->total_price;
                } else {
                    $ds_phong[$v->saler->room_id] += $v->total_price;
                }
                ?>
            <tr>
                <td><a target="_blank"
                       href="/admin/bill?search=true&saler_id={{ $v->saler_id }}&from_date={{ date('Y-m-01') }}&registration_date=1">{{ @$v->saler->name }}</a>
                </td>
                <td>{{ @$v->saler->code }}</td>
                <td>{{ @$room_ids[$v->saler->room_id] }}</td>
                <td @if($v->total_price < 10000000) class="text-red" @endif>{{ number_format($v->total_price, 0, '.', '.') }}</td>
            </tr>
        @endforeach
        <?php
        arsort($ds_phong);
        ?>
        <tr>
            <td></td>
            <td></td>
            <td><strong>TỔNG CỘNG</strong></td>
            <td><strong><a target="_blank" href="/admin/bill?search=true&from_date={{ date('Y-m-01') }}&registration_date=1"
                           style="display: inline-block;">{{ number_format($tong_doanh_so, 0, '.', '.') }}</a></strong></td>
        </tr>
        @foreach($ds_phong as $room_id => $ds)
            <tr>
                <td>{{ $room_ids[$room_id] }}:</td>
                <td>{{ number_format($ds, 0, '.', '.') }}</td>
                <td></td>
                <td></td>
            </tr>
        @endforeach




        {{--Lấy trong tháng trước nữa--}}
        <?php
        $saler_ids = \App\Models\RoleAdmin::whereIn('role_id', [
            2,      //  quyền sale
            182,    //  quyền trưởng phòng KD
            186,    //  giám đốc kinh doanh
        ])->pluck('admin_id')->toArray();

        $best_sales = \App\CRMDV\Models\Bill::selectRaw('Sum(total_price) as total_price, saler_id')
            //                        ->whereRaw($whereRegistration)    //  lấy ngày theo bộ lọc thời gian
            ->whereRaw("registration_date >= '" . date('Y-m-01 00:00:00', strtotime('-2 months')) . "' AND registration_date <= '" . date('Y-m-t 23:59:00', strtotime('-2 months')) . "'")  //  lấy trong tháng trước nữa
            ->whereNotIn('saler_id', [170])     //  loại trừ tài khoản Hoàng Hùng
            ->whereIn('saler_id', $saler_ids)    //  chỉ nằm trong các sale của cty, ko tính ctv
            ->groupBy('saler_id')->orderBy('total_price', 'desc')->get();

        $tong_doanh_so = 0;
        $ds_phong = [];
        ?>
        <tr>
            <td style="font-weight: bold;">Tháng {{ date('m', strtotime('-2 months')) }}</td>
        </tr>
        @foreach($best_sales as $v)
                <?php
                $tong_doanh_so += $v->total_price;
                if (!isset($ds_phong[$v->saler->room_id])) {
                    $ds_phong[$v->saler->room_id] = $v->total_price;
                } else {
                    $ds_phong[$v->saler->room_id] += $v->total_price;
                }
                ?>
            <tr>
                <td><a target="_blank"
                       href="/admin/bill?search=true&saler_id={{ $v->saler_id }}&from_date={{ date('Y-m-01') }}&registration_date=1">{{ @$v->saler->name }}</a>
                </td>
                <td>{{ @$v->saler->code }}</td>
                <td>{{ @$room_ids[$v->saler->room_id] }}</td>
                <td @if($v->total_price < 10000000) class="text-red" @endif>{{ number_format($v->total_price, 0, '.', '.') }}</td>
            </tr>
        @endforeach
        <?php
        arsort($ds_phong);
        ?>
        <tr>
            <td></td>
            <td></td>
            <td><strong>TỔNG CỘNG</strong></td>
            <td><strong><a target="_blank" href="/admin/bill?search=true&from_date={{ date('Y-m-01') }}&registration_date=1"
                           style="display: inline-block;">{{ number_format($tong_doanh_so, 0, '.', '.') }}</a></strong></td>
        </tr>
        @foreach($ds_phong as $room_id => $ds)
            <tr>
                <td>{{ $room_ids[$room_id] }}:</td>
                <td>{{ number_format($ds, 0, '.', '.') }}</td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <i style="font-size: 8px;">Truy vấn theo bộ lọc thời gian</i>


@endif