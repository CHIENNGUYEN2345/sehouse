<div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                Ký mới
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body kt-portlet__body--fit">

        <!--end: Datatable -->
        <canvas id="myChart"></canvas>
    </div>
</div>

<script>

    <?php
    $char = \App\CRMEdu\Models\Bill::selectRaw('Sum(total_price) as total_price, COUNT(id) as total_bill, MONTH(registration_date) as month');
    if (isset($_GET['start_date']) && $_GET['start_date'] != '') {
        $char = $char->where('registration_date', '>=', date('Y-m-d 00:00:00', strtotime($_GET['start_date'])));
    } else {
        $char = $char->where('registration_date', '>=', date('Y-m-d 00:00:00', strtotime('-11 month')));
    }
    if (isset($_GET['end_date']) && $_GET['end_date'] != '') {
        $char = $char->where('registration_date', '<=', date('Y-m-d 23:59:00', strtotime($_GET['end_date'])));
    } else {
        $char = $char->where('registration_date', '<=', date('Y-m-d 23:59:00'));
    }

    $char = $char->groupBy(\DB::raw('MONTH(registration_date)'))->orderBy('registration_date', 'asc')->get();

    $admin_ids = \App\Models\RoleAdmin::where('role_id', '!=', 3)->where('role_id', '!=', 4)->pluck('admin_id');
    $char_user = \App\Models\Admin::selectRaw('COUNT(id) as total, MONTH(created_at) as month')
        ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime('-11 month')))->where('created_at', '<=', date('Y-m-d 23:59:00'))->whereNotIn('id', $admin_ids)->groupBy(\DB::raw('MONTH(created_at)'))->orderBy('created_at', 'asc')->get()->toArray();

    if (count($char_user) < 11) {
        $arr = [];
        for ($i = 11; $i > count($char_user); $i--) {
            $arr[] = 0;
        }
        $char_user = array_merge($arr, $char_user);
    }

    ?>
    var lineChartData = {
        labels: [
            @foreach($char as $item)
                'T{{ $item->month }}',
            @endforeach
        ],
        datasets: [{
            label: 'Số Hợp đồng',
            borderColor: window.chartColors.red,
            backgroundColor: window.chartColors.red,
            fill: false,
            data: [
                //    dữ liệu của số lượng Hợp đồng
                @foreach($char as $item)
                    '{{ $item->total_bill }}',
                @endforeach
            ],
            yAxisID: 'y-axis-1',
        }, {
            label: 'Số tiền (đv: triệu)',
            borderColor: window.chartColors.blue,
            backgroundColor: window.chartColors.blue,
            fill: false,
            data: [
                //    dữ liệu số tiên thu được theo tháng
                @foreach($char as $item)
                    '{{ round($item->total_price/1000000) }}',
                @endforeach
            ],
            yAxisID: 'y-axis-2'
        }, {
            label: 'Số học viên mới',
            borderColor: window.chartColors.black,
            backgroundColor: window.chartColors.black,
            fill: false,
            data: [
                //    dữ liệu số tiên thu được theo tháng
                @foreach($char_user as $item)
                    '{{ round(@$item['total']) }}',
                @endforeach
            ],
            yAxisID: 'y-axis-3'
        }]
    };


    var ctx = document.getElementById('myChart').getContext('2d');

    window.myLine = Chart.Line(ctx, {
        data: lineChartData,
        options: {
            responsive: true,
            hoverMode: 'index',
            stacked: false,
            title: {
                display: true,
                // text: 'Chart.js Line Chart - Multi Axis'
            },
            scales: {
                yAxes: [{
                    type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                    display: true,
                    position: 'left',
                    id: 'y-axis-1',
                    ticks: {
                        beginAtZero: true,
                    }

                }, {
                    type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                    display: true,
                    position: 'right',
                    id: 'y-axis-2',
                    ticks: {
                        beginAtZero: true,
                    },
                    // grid line settings
                    gridLines: {
                        drawOnChartArea: false, // only want the grid lines for one axis to show up
                    },
                }, {
                    type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                    display: true,
                    position: 'left',
                    id: 'y-axis-3',
                    ticks: {
                        beginAtZero: true,
                    },
                    // grid line settings
                    gridLines: {
                        drawOnChartArea: false, // only want the grid lines for one axis to show up
                    },
                }],
            }
        }
    });

</script>