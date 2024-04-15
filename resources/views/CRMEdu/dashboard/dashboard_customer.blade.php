@extends(config('core.admin_theme').'.template')
@section('main')
    <?php
    $this_month = date('m');
    $next_month = strftime('%m', strtotime(strtotime($this_month) . " +1 month"));

    $min_day = \App\Models\Setting::select('value')->where('name', 'min_day')->first()->value;
    $max_day = \App\Models\Setting::select('value')->where('name', 'max_day')->first()->value;
    ?>
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="row">
            <div class="col-xl-12 order-lg-2 order-xl-1">
                <div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
                    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title bold uppercase">
                                Hợp đồng sắp đến hạn thanh toán
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
                                        <span style="width: 100px;">khoá học</span></th>
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
                                {{--Hợp đồng sắp hết hạn lấy theo trong setting--}}
                                <?php
                                $bill_deadline = \App\CRMEdu\Models\Bill::select('service_id', 'id', 'created_at', 'total_price', 'exp_price', 'expiry_date', 'domain')
                                    ->where('status', 1)->where('customer_id', \Auth::guard('admin')->user()->id)
                                    ->where('expiry_date', '<>', Null)->get();
                                ?>
                                @if($bill_deadline->count()>0)
                                    @foreach($bill_deadline as $k=>$v)
                                        <?php
                                        $today = date('Y-m-d');
                                        //Khoảng cách từ hnay đến hạn deadline
                                        $days = (strtotime($v->expiry_date) - strtotime($today)) / (60 * 60 * 24);

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
                                                        <span class="kt-font-bold">{{date('d-m-Y',strtotime(@$v->expiry_date))}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{number_format(@$v->exp_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                                </td>
                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                        <span style="width: 50px;">
                                                            <a href="/admin/bill/edit/{{ $v->id }}"
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
                                Hợp đồng quá hạn thanh toán
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
                                        <span style="width: 100px;">khoá học</span></th>
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
                                {{--Hợp đồng quá hạn lấy theo trong setting--}}
                                @if($bill_deadline->count()>0)
                                    @foreach($bill_deadline as $k=>$v)
                                        <?php
                                        $today = date('Y-m-d');
                                        //Khoảng cách từ hnay đến hạn deadline
                                        $days = (strtotime($today) - strtotime($v->expiry_date)) / (60 * 60 * 24);
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
                                                        <span class="kt-font-bold">{{date('d-m-Y',strtotime(@$v->expiry_date))}}</span>
                                                    </span>
                                                </td>

                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{number_format(@$v->exp_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                                </td>
                                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                        <span style="width: 50px;">
                                                            <a href="/admin/bill/edit/{{ $v->id }}"
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
            div#kt_datatable_latest_orders {
                overflow: auto;
            }
            table.kt-datatable__table {
                width: unset !important;
                display: inline-block !important;
            }
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

@endpush

