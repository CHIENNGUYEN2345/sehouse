@if(!isset($_GET['ajax-load']))
    <script>
        $(document).ready(function () {
            $.ajax({
                url: '/admin/dashboard/ajax/load-khoi?ajax-load=true&file=CoreERP.dashboard.partials.xep_hang.xep_hang_dich_vu',
                type: 'GET',
                data: {

                },
                success: function (html) {
                    $('#xep_hang_dich_vu').html(html);
                },
                error: function () {
                    console.log('lỗi load khối partials/xep_hang/xep_hang_dich_vu');
                }
            });
        });
    </script>
    <div class="kt-portlet kt-portlet--height-fluid">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title bold uppercase">
                    Top dịch vụ
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="kt-widget12">
                @if($doanh_so > 0)
                <div class="kt-widget12__content" id="xep_hang_dich_vu">
                    <img class="tooltip_info_loading"
                         src="/images_core/icons/loading.gif">
                </div>
                @endif
            </div>
        </div>
    </div>

@else
    <?php
    //  Mặc định lấy ngày đầu tháng
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01 00:00:00');

    //  Mặc định lấy ngày hôm nay
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d 23:59:00');

    $where = "created_at >= '" . $start_date . " 00:00:00' AND created_at <= '" . $end_date . " 23:59:59'";
    $whereRegistration = "registration_date >= '" . $start_date . " 00:00:00' AND registration_date <= '" . $end_date . " 23:59:59'";
    $whereCreated_at = "created_at >= '" . $start_date . " 00:00:00' AND created_at <= '" . $end_date . " 23:59:59'";
    $whereDate = "date >= '" . $start_date . " 00:00:00' AND date <= '" . $end_date . " 23:59:59'";


    if (isset($_GET['admin_id']) && $_GET['admin_id'] != '') {
        $where .= " AND admin_id = " . $_GET['admin_id'];
        $whereRegistration .= " AND admin_id = " . $_GET['admin_id'];
    }


    $doanh_so = \App\CRMDV\Models\Bill::whereRaw($whereRegistration)->sum('total_price');

    $service_ids = \App\CRMDV\Models\Bill::whereRaw($whereRegistration)->groupBy('service_id')->pluck('service_id')->toArray();

    $services = \App\CRMDV\Models\Service::select('id', 'name_vi')->whereIn('id', $service_ids)->get();

    //  Sắp xếp dịch vụ nào nhiều doanh số nhất lên đầu
    $service_arr = [];
    foreach ($services as $service) {
        $service->ds_dv = @\App\CRMDV\Models\Bill::whereRaw($whereRegistration)->where('service_id', $service->id)->where('status', 1)->sum('total_price');
        $service_arr[$service->ds_dv] = $service;
    }
    krsort($service_arr);
    //                        dd($service_arr);
    ?>
    <table class="table table-striped">
        <thead class="kt-datatable__head">
        <tr>
            <th>Dịch vụ</th>
            <th>Số hợp đồng</th>
            <th>Doanh số</th>
            <th>% doanh số</th>
        </tr>
        </thead>
        <tbody class="kt-datatable__body ps ps--active-y">
        @foreach($service_arr as $service)
            <tr>
                <td>
                    {{ $service->name_vi }}
                </td>
                <td>{{ number_format(\App\CRMDV\Models\Bill::whereRaw($whereRegistration)->where('service_id', $service->id)->count()) }}</td>
                <td>{{number_format($service->ds_dv, 0, '.', '.')}}</td>
                <td>{{ round(($service->ds_dv / $doanh_so)*100) }}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <i style="font-size: 8px;">Truy vấn theo: bộ lọc thời gian</i>
@endif
