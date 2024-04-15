@extends(config('core.admin_theme').'.template')
@section('main')
    <?php
    $this_month = date('m');
    $next_month = strftime('%m', strtotime(strtotime($this_month) . " +1 month"));

    $before_date = \Modules\WebBill\Models\Bill::select('service_id', 'customer_id', 'id', 'created_at', 'total_price', 'exp_price', 'date', 'domain')->where('status', 1)
        ->where('date', '<>', Null)->get();
    $min_day = \App\Models\Setting::select('value')->where('name', 'min_day')->first()->value;
    $max_day = \App\Models\Setting::select('value')->where('name', 'max_day')->first()->value;
    ?>
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="row">
            <div class="col-xs-12 col-lg-12 col-xl-12 col-lg-12 order-lg-1 order-xl-1">
                <!--begin:: Widgets/Finance Summary-->
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title bold uppercase">
                                Tổng quan
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-widget12">
                            <div class="kt-widget12__content">
                                <div class="kt-widget12__item thong_ke_so">
                                    <?php
                                    $total_bills = \Modules\WebBill\Models\Bill::select('id')->get()->count();
                                    ?>
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Tổng HĐ</span>
                                        <span class="kt-widget12__value">{{number_format(@$total_bills, 0, '.', '.')}}</span>
                                    </div>

                                    <?php
                                    $total_bills_validity = \Modules\WebBill\Models\Bill::select('id')->where('status', 1)->get()->count();
                                    ?>
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">HĐ hiệu lực</span>
                                        <span class="kt-widget12__value">{{number_format(@$total_bills_validity, 0, '.', '.')}}</span>
                                    </div>

                                    <?php
                                    $total_auto_bill = \Modules\WebBill\Models\Bill::select('id')->where('auto_extend', 1)->get()->count();
                                    ?>
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">HĐ tự động gia hạn</span>
                                        <span class="kt-widget12__value">{{number_format(@$total_auto_bill, 0, '.', '.')}}</span>
                                    </div>

                                    <?php
                                    $total_user = \Modules\WebBill\Models\Bill::select('user_name')->get()->count();
                                    ?>
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Tổng khách</span>
                                        <span class="kt-widget12__value">{{number_format(@$total_user, 0, '.', '.')}}</span>
                                    </div>

                                    <?php
                                    $total_price = 0;
                                    $total_bill = \Modules\WebBill\Models\Bill::select('id', 'total_price')->where('status', '<>', 3)->get();
                                    foreach ($total_bill as $total_price_bill) {
                                        $total_price += $total_price_bill->total_price;
                                    }
                                    ?>
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Tổng tiền</span>
                                        <span class="kt-widget12__value">{{number_format($total_price, 0, '.', '.')}}<sup>đ</sup></span>
                                    </div>

                                    <?php
                                    $total_exp_price_this_month = \Modules\WebBill\Models\Bill::select('exp_price')->where('date', 'like', '%-' . $this_month . '-%')->get()->sum('exp_price');
                                    ?>
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Tổng tiền gia hạn cần thu trong tháng này</span>
                                        <span class="kt-widget12__value">{{number_format($total_exp_price_this_month, 0, '.', '.')}}<sup>đ</sup></span>
                                    </div>

                                    <?php
                                    $total_exp_price_next_month = \Modules\WebBill\Models\Bill::select('exp_price')->where('date', 'like', '%-' . $next_month . '-%')->get()->sum('exp_price');
                                    ?>
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Tổng tiền gia hạn cần thu trong tháng sau</span>
                                        <span class="kt-widget12__value">{{number_format($total_exp_price_next_month, 0, '.', '.')}}<sup>đ</sup></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end:: Widgets/Finance Summary-->
            </div>

            <div class="col-xl-12 order-lg-2 order-xl-1">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title bold uppercase">
                                Hóa đơn sắp đến hạn thanh toán
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <!--begin: Datatable -->
                        <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded"
                             id="kt_datatable_latest_orders" style="">
                            <table class="kt-datatable__table" style=" width: 100%;">
                                <thead class="kt-datatable__head" style="    overflow: unset;">
                                <tr class="kt-datatable__row" style="left: 0px;">
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 25px;">STT</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Tên khách hàng</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Dịch vụ</span></th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 200px;">Tên miền</span></th>

                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Giá</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Ngày bắt đầu</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Ngày hết hạn</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Giá gia hạn</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 50px;">Chi tiết</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="kt-datatable__body ps ps--active-y"
                                       style="">
                                {{--Hóa đơn sắp hết hạn lấy theo trong setting--}}
                                @if($before_date->count()>0)
                                    @foreach($before_date as $k=>$v)
                                        <?php
                                        $today = date('Y-m-d');
                                        //Khoảng cách từ hnay đến hạn deadline
                                        $days = (strtotime($v->date) - strtotime($today)) / (60 * 60 * 24);

                                        ?>
                                        @if($days<=$min_day && $days>=0)
                                            <tr data-row="0" class="kt-datatable__row" style="left: 0px;">
                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 25px;">
                                                        <span class="kt-font-bold">{{$k+1}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <a href="/admin/bill/{{$v->id}}"><span
                                                                    class="kt-font-bold">{{@$v->user->name}}</span></a>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{@$v->service->name_vi}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 200px;">
                                                        <span class="kt-font-bold">{{@$v->domain}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{number_format(@$v->total_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{date('d-m-Y',strtotime(@$v->created_at))}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{date('d-m-Y',strtotime(@$v->date))}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{number_format(@$v->exp_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                                </td>
                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                        <span style="width: 50px;">
                                                            <a href="/admin/bill/{{$v->id}}"
                                                               class="btn btn-sm btn-label-brand btn-bold">Xem</a>
                                                        </span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <!--end: Datatable -->
                        {{--<div class="paginate">{{$bill_news->appends(Request::all())->links()}}</div>--}}
                    </div>

                </div>
            </div>

            <div class="col-xl-12 order-lg-2 order-xl-1">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title bold uppercase">
                                Hóa đơn quá hạn thanh toán
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <!--begin: Datatable -->
                        <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded"
                             id="kt_datatable_latest_orders" style="">
                            <table class="kt-datatable__table" style=" width: 100%;">
                                <thead class="kt-datatable__head" style="    overflow: unset;">
                                <tr class="kt-datatable__row" style="left: 0px;">
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 25px;">STT</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Tên khách hàng</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Dịch vụ</span></th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 200px;">Tên miền</span></th>

                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Giá</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Ngày bắt đầu</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Ngày hết hạn</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Giá gia hạn</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 50px;">Chi tiết</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="kt-datatable__body ps ps--active-y"
                                       style="">
                                {{--Hóa đơn quá hạn lấy theo trong setting--}}
                                @if($before_date->count()>0)
                                    @foreach($before_date as $k=>$v)
                                        <?php
                                        $today = date('Y-m-d');
                                        //Khoảng cách từ hnay đến hạn deadline
                                        $days = (strtotime($today) - strtotime($v->date)) / (60 * 60 * 24);
                                        ?>
                                        @if($days<=$max_day && $days>0)
                                            <?php ?>

                                            <tr data-row="0" class="kt-datatable__row" style="left: 0px;">
                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 25px;">
                                                        <span class="kt-font-bold">{{$k+1}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <a href="/admin/bill/{{$v->id}}"><span
                                                                    class="kt-font-bold">{{@$v->user->name}}</span></a>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{@$v->service->name_vi}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 200px;">
                                                        <span class="kt-font-bold">{{@$v->domain}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{number_format(@$v->total_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{date('d-m-Y',strtotime(@$v->created_at))}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{date('d-m-Y',strtotime(@$v->date))}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{number_format(@$v->exp_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                                </td>
                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                        <span style="width: 50px;">
                                                            <a href="/admin/bill/{{$v->id}}"
                                                               class="btn btn-sm btn-label-brand btn-bold">Xem</a>
                                                        </span>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <!--end: Datatable -->
                        {{--<div class="paginate">{{$bill_news->appends(Request::all())->links()}}</div>--}}
                    </div>

                </div>
            </div>

            {{-- Danh sách các sản phẩm mới đăng --}}
            <div class="col-xl-12 order-lg-2 order-xl-1">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title bold uppercase">
                                Đồ thị thể hiện số lượng hóa đơn, số tiên thu được theo tháng
                            </h3>
                        </div>
                    </div>


                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <!--begin: Datatable -->
                        <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded"
                             id="kt_datatable_latest_orders" style="">
                            <canvas id="myChart"></canvas>

                        </div>
                        <!--end: Datatable -->
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
            {{--Danh sách tin mới đăng--}}
            <div class="col-xl-12 order-lg-2 order-xl-1">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title bold uppercase">
                                Hóa đơn mới tạo
                            </h3>
                        </div>
                    </div>

                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <!--begin: Datatable -->
                        <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded"
                             id="kt_datatable_latest_orders" style="">
                            <table class="kt-datatable__table" style=" width: 100%;">
                                <thead class="kt-datatable__head" style="    overflow: unset;">
                                <tr class="kt-datatable__row" style="left: 0px;">
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 25px;">STT</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Tên khách hàng</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Dịch vụ</span></th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 200px;">Tên miền</span></th>

                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Giá</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Ngày bắt đầu</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Ngày hết hạn</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 100px;">Giá gia hạn</span>
                                    </th>
                                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                                        <span style="width: 50px;">Chi tiết</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="kt-datatable__body ps ps--active-y"
                                       style="">
                                <?php
                                $bill_news = \Modules\WebBill\Models\Bill::select('service_id', 'customer_id', 'id', 'created_at', 'total_price', 'exp_price', 'date', 'domain')->orderBy('id', 'desc')
                                    ->where('status', 0)->take(6)->get();
                                ?>
                                @if($bill_news->count()>0)
                                    @foreach($bill_news as $k=>$v)
                                        <tr data-row="0" class="kt-datatable__row" style="left: 0px;">
                                            <td data-field="ShipDate" class="kt-datatable__cell">
                                                <span style="width: 25px;">
                                                    <span class="kt-font-bold">{{$k+1}}</span>
                                                    </span>
                                            </td>

                                            <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <a href="/admin/bill/{{$v->id}}"><span
                                                                    class="kt-font-bold">{{@$v->user->name}}</span></a>
                                                    </span>
                                            </td>

                                            <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{@$v->service->name_vi}}</span>
                                                    </span>
                                            </td>

                                            <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 200px;">
                                                        <span class="kt-font-bold">{{@$v->domain}}</span>
                                                    </span>
                                            </td>

                                            <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{number_format(@$v->total_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                            </td>

                                            <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{date('d-m-Y',strtotime(@$v->created_at))}}</span>
                                                    </span>
                                            </td>

                                            <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{date('d-m-Y',strtotime(@$v->date))}}</span>
                                                    </span>
                                            </td>

                                            <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{number_format(@$v->exp_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                            </td>
                                            <td data-field="ShipDate" class="kt-datatable__cell">
                                                        <span style="width: 50px;">
                                                            <a href="/admin/bill/{{$v->id}}"
                                                               class="btn btn-sm btn-label-brand btn-bold">Xem</a>
                                                    </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <!--end: Datatable -->
                        {{--<div class="paginate">{{$bill_news->appends(Request::all())->links()}}</div>--}}
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
@section('custom_head')
    {{--    <link href="https://www.keenthemes.com/preview/metronic/theme/assets/global/css/components.min.css" rel="stylesheet"--}}
    {{--          type="text/css">--}}
    <style type="text/css">
        .kt-datatable__cell > span > a.cate {
            color: #5867dd;
            margin-bottom: 3px;
            background: rgba(88, 103, 221, 0.1);
            height: auto;
            display: inline-block;
            width: auto;
            padding: 0.15rem 0.75rem;
            border-radius: 2px;
        }

        .paginate > ul.pagination > li {
            padding: 5px 10px;
            border: 1px solid #ccc;
            margin: 0 5px;
            cursor: pointer;
        }

        .paginate > ul.pagination span {
            color: #000;
        }

        .paginate > ul.pagination > li.active {
            background: #0b57d5;
            color: #fff !important;
        }

        .paginate > ul.pagination > li.active span {
            color: #fff !important;
        }

        .kt-widget12__desc, .kt-widget12__value {
            text-align: center;
        }

        @-webkit-keyframes chartjs-render-animation {
            from {
                opacity: 0.99 list_user
            }
            to {
                opacity: 1
            }
        }

        @keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        .chartjs-render-monitor {
            -webkit-animation: chartjs-render-animation 0.001s;
            animation: chartjs-render-animation 0.001s;
        }

        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
    <style type="text/css">
        @-webkit-keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        @keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        .chartjs-render-monitor {
            -webkit-animation: chartjs-render-animation 0.001s;
            animation: chartjs-render-animation 0.001s;
        }

        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }


        @media (max-width: 768px) {
            .kt-widget12 .kt-widget12__content .thong_ke_so {
                display: inline-block;
            }

            .thong_ke_so .col-sm-3 {
                display: inline-block;
                width: 50%;
                float: left;
                padding: 0;
                margin-bottom: 20px;
            }
        }
    </style>

@endsection
@push('scripts')
    <script src="{{ url('public/libs/chartjs/js/Chart.bundle.js') }}"></script>
    <script src="{{ url('public/libs/chartjs/js/utils.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script>
        $(document).ready(function () {
            $('#active_service a').click(function (event) {
                event.preventDefault();
                var object = $(this);
                $.ajax({
                    url: '/admin/service_history/ajax-publish',
                    data: {
                        id: object.data('service_history_id')
                    },
                    success: function (result) {
                        if (result.status == true) {
                            toastr.success(result.msg);
                            object.parents('tr').remove();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function (e) {
                        console.log(e.message);
                    }
                })
            });


        })
    </script>
    <script>
        var lineChartData = {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Số lượng hóa đơn',
                borderColor: window.chartColors.red,
                backgroundColor: window.chartColors.red,
                fill: false,
                data: [
                    //    dữ liệu của số lượng hóa đơn
                    <?php $total_bill_month = [];
                    for ($i = 0; $i < 12; $i++) {
                        $month = strftime('%m', strtotime(strtotime(date('01')) . " +" . $i . " month"));
                        $total_bill_month[] = \Modules\WebBill\Models\Bill::select('id')->where('created_at', 'like', '%-' . $month . '-%')
                            ->where('created_at', 'like', date('Y') . '-%')->get()->count();
                        echo($total_bill_month[$i] . ",");
                    }
                    ?>

                ],
                yAxisID: 'y-axis-1',
            }, {
                label: 'Số tiên thu được',
                borderColor: window.chartColors.blue,
                backgroundColor: window.chartColors.blue,
                fill: false,
                data: [
                    //    dữ liệu số tiên thu được theo tháng
                    <?php $total_price1 = [];
                    for ($i = 0; $i < 12; $i++) {
                        $month = strftime('%m', strtotime(strtotime(date('01')) . " +" . $i . " month"));
                        $total_price1[] = \Modules\WebBill\Models\Bill::where('created_at', 'like', '%-' . $month . '-%')->where('created_at', 'like', date('Y') . '-%')->get()->sum('total_price');
                        echo($total_price1[$i] . ",");
                    }
                    ?>

                ],
                yAxisID: 'y-axis-2'
            }]
        };

        window.onload = function () {
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
                        }],
                    }
                }
            });
        };
    </script>
@endpush

