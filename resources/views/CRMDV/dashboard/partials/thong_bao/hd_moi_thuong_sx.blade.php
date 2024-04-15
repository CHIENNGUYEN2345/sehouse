@if(!isset($_GET['ajax-load']))
    <script>
        $(document).ready(function () {
            $.ajax({
                url: '/admin/dashboard/ajax/load-khoi?ajax-load=true&file=CRMDV.dashboard.partials.thong_bao.hd_moi_thuong_sx',
                type: 'GET',
                data: {

                },
                success: function (html) {
                    $('#hd_moi_thuong_sx').html(html);
                },
                error: function () {
                    console.log('lỗi load khối CRMDV/partials/thong_bao/hd_moi_thuong_sx');
                }
            });
        });
    </script>
    <div class="kt-portlet kt-portlet--height-fluid">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title bold uppercase">
                    Vinh danh thưởng team sản xuất
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="kt-widget12">
                <div class="kt-widget12__content" id="hd_moi_thuong_sx">
                    <img class="tooltip_info_loading"
                         src="/images_core/icons/loading.gif">
                </div>
            </div>
        </div>
    </div>
@else
        <?php
        $bills = \App\CRMDV\Models\Bill::leftJoin('bill_progress', 'bill_progress.bill_id', '=', 'bills.id')
            ->select('bills.id', 'bills.domain', 'bills.total_price', 'bills.service_id', 'bills.saler_id', 'bills.registration_date', 'bills.customer_id',
                'bill_progress.dh_id', 'bill_progress.kt_id')
            ->whereIn('service_id', [   //  thưởng các dịch vụ làm mới khi liên quan đến dv chính
                1,  //  ldp
                10,  //  wp tiết kiệm
                11,  //  wp cơ bản
                12,  //  wp giao diện
                13,  //  wp chuyên nghiệp
                14,  //  wp special
                17,  //  ldp tiết kiệm
                18,  //  ldp cơ bản
                19,  //  ldp chuyên nghiệp
                20,  //  ldp cao cấp
            ])
            ->whereRaw("registration_date >= '" . date('Y-m-01 00:00:00', strtotime('-3 months')) . "'")  //  lấy trong 3 tháng gần đây
            ->orderBy('registration_date', 'desc')
            ->get();
        ?>
    <table class="table table-striped">
        <thead class="kt-datatable__head">
        <tr>
            <th>Thưởng thành viên</th>
            <th>Khách hàng cũ</th>
            <th>Dự án cũ</th>
            <th>Ngày ký dự án mới</th>
            <th>Lý do thưởng</th>
        </tr>
        </thead>
        <tbody class="kt-datatable__body ps ps--active-y">
        @foreach($bills as $bill)
            <?php
            //  lấy hợp đồng gần đây của khách này
            $hd_cu = \App\CRMDV\Models\Bill::leftJoin('bill_progress', 'bill_progress.bill_id', '=', 'bills.id', 'registration_date')
                ->select('bills.id', 'bills.domain',
                    'bill_progress.dh_id', 'bill_progress.kt_id', 'bills.registration_date')
                ->whereIn('service_id', [   //  thưởng các dịch vụ mà gói cũ là thiết kế web
                    1,  //  ldp
                    10,  //  wp tiết kiệm
                    11,  //  wp cơ bản
                    12,  //  wp giao diện
                    13,  //  wp chuyên nghiệp
                    14,  //  wp special
                    17,  //  ldp tiết kiệm
                    18,  //  ldp cơ bản
                    19,  //  ldp chuyên nghiệp
                    20,  //  ldp cao cấp
                ])
                ->whereRaw("registration_date < '" . date('Y-m-d 00:00:00', strtotime($bill->registration_date  . ' -30 days')) . "'")  //  lấy trong 30 ngày trước đó
                ->where('bills.customer_id', $bill->customer_id)->where('bills.id', '!=', $bill->id)
                ->orderBy('bills.registration_date', 'desc')->limit(1)->first();
            ?>
            @if (is_object($hd_cu))
                <?php
                $dh = \App\Models\Admin::where('id', $hd_cu->dh_id)->where('status', 1)->first();
                $kt = \App\Models\Admin::where('id', $hd_cu->kt_id)->where('status', 1)->first();
                ?>
                @if( is_object($dh) || is_object($kt) )
                    <tr>
                        <td>
                            ĐH: {{ @$dh->name }}<br>
                            KT: {{ @$kt->name }}
                        </td>
                        <td><a href="/admin/user/edit/{{ $bill->customer_id }}" target="_blank" >{{ @$bill->customer->name }}</a></td>
                        <td>{{ @$hd_cu->domain }}</td>
                        <td><a href="/admin/bill/edit/{{ $bill->id }}" target="_blank">{{ date('d/m/Y', strtotime(@$bill->registration_date)) }}</a></td>
                        <td>
                            Khách làm thêm dự án mới
                        </td>
                    </tr>
                @endif
            @endif
        @endforeach
        </tbody>
    </table>
    <i style="font-size: 8px;">Truy vấn trong 3 tháng gần đây</i>
@endif
