@if(!isset($_GET['ajax-load']))
    <script>
        $(document).ready(function () {
            $.ajax({
                url: '/admin/dashboard/ajax/load-khoi?ajax-load=true&file=CoreERP.dashboard.partials.thong_bao.hd_moi',
                type: 'GET',
                data: {

                },
                success: function (html) {
                    $('#hd_moi').html(html);
                },
                error: function () {
                    console.log('lỗi load khối partials/thong_bao/hd_moi');
                }
            });
        });
    </script>
    <div class="kt-portlet kt-portlet--height-fluid">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title bold uppercase">
                    Hợp đồng mới
                </h3>

            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="kt-widget12">
                <div class="kt-widget12__content" id="hd_moi">
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
            ->whereRaw("registration_date >= '" . date('Y-m-01 00:00:00') . "'")  //  lấy trong tháng này
            ->orderBy('registration_date', 'desc')->get();
        ?>
    <table class="table table-striped">
        <thead class="kt-datatable__head">
        <tr>
            <th>Tên khách</th>
            <th>Dịch vụ</th>
            <th>Doanh số</th>
            <th>Ngày ký</th>
            <th>Tên miền</th>
            <th>Sale</th>
            <th>Đội triển khai cũ</th>
        </tr>
        </thead>
        <tbody class="kt-datatable__body ps ps--active-y">
        @foreach($bills as $bill)
            <tr>
                <td><a href="/admin/user/edit/{{ $bill->customer_id }}" target="_blank" >{{ @$bill->customer->name }}</a></td>
                <td>{{ @$bill->service->name_vi }}</td>
                <td>{{ number_format($bill->total_price, 0, '.', '.') }}đ</td>
                <td>{{ date('d/m/Y', strtotime($bill->registration_date)) }}</td>
                <td><a href="/admin/bill/edit/{{ $bill->id }}"
                       target="_blank">{{ $bill->domain }}</a></td>
                <td>
                    {{ @$bill->saler->name }}
                </td>
                <td>
                        <?php
                        //  lấy hợp đồng gần đây của khách này
                        $hd_gan_day = \App\CRMDV\Models\Bill::leftJoin('bill_progress', 'bill_progress.bill_id', '=', 'bills.id')
                            ->select('bills.id', 'bills.domain',
                                'bill_progress.dh_id', 'bill_progress.kt_id')
                            ->where('bills.customer_id', $bill->customer_id)->where('bills.id', '!=', $bill->id)
                            ->orderBy('bills.registration_date', 'desc')->limit(1)->first();
                        ?>
                    @if(is_object($hd_gan_day))
                        {{--                                    Nếu có HĐ trước thì hiện ra kỹ thuật & điều hành triển khai dự án đó--}}
                        ĐH: {{ @\App\Models\Admin::find($hd_gan_day->dh_id)->name }}<br>
                        KT: {{ @\App\Models\Admin::find($hd_gan_day->kt_id)->name }}
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <i style="font-size: 8px;">Truy vấn 10 hđ gần nhất</i>
@endif
