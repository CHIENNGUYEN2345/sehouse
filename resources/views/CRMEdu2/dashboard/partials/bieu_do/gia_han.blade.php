
<div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                Gia hạn
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body kt-portlet__body--fit">

        <!--end: Datatable -->
        <canvas id="myChart-gia-han"></canvas>
    </div>
</div>

<script>
    /*Biểu đồ gia hạn*/
    <?php
    $char = \App\CRMDV\Models\Bill::whereNotNull('bill_parent')->selectRaw('Sum(total_price) as total_price, COUNT(id) as total_bill, MONTH(registration_date) as month');
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

    ?>
    var lineChartData_gia_han = {
        labels: [
            @foreach($char as $item)
                'T{{ $item->month }}',
            @endforeach
        ],
        datasets: [{
            label: 'Số Hợp đồng',
            borderColor: window.chartColors.blue,
            backgroundColor: window.chartColors.blue,
            fill: false,
            data: [
                //    dữ liệu của số lượng Hợp đồng
                @foreach($char as $item)
                    '{{ $item->total_bill }}',
                @endforeach
            ],
            yAxisID: 'gia-han-y-axis-1',
        }, {
            label: 'Số tiền (đv: triệu)',
            borderColor: window.chartColors.red,
            backgroundColor: window.chartColors.red,
            fill: false,
            data: [
                //    dữ liệu số tiên thu được theo tháng
                @foreach($char as $item)
                    '{{ round($item->total_price/1000000) }}',
                @endforeach
            ],
            yAxisID: 'gia-han-y-axis-2'
        }]
    };

    var ctx_gia_han = document.getElementById('myChart-gia-han').getContext('2d');

    window.myLinee = Chart.Line(ctx_gia_han, {
        data: lineChartData_gia_han,
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
                    id: 'gia-han-y-axis-1',
                    ticks: {
                        beginAtZero: true,
                    }

                }, {
                    type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
                    display: true,
                    position: 'right',
                    id: 'gia-han-y-axis-2',
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