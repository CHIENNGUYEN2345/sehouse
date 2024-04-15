<?php
$next_month = strftime('%m', strtotime(strtotime(date('m')) . " +1 month"));
$last_month = strftime('%m', strtotime(strtotime(date('m')) . " -1 month"));

$cau_hinh = CommonHelper::getFromCache('settings_web_service', ['settings']);
if (!$cau_hinh) {
    $cau_hinh = \App\Models\Setting::whereIn('type', ['web_service'])->pluck('value', 'name')->toArray();
    CommonHelper::putToCache('settings_web_service', $cau_hinh, ['settings']);
}

$bill_warning = CommonHelper::getFromCache('bills_sap_het_han' . \Auth::guard('admin')->user()->id, ['bills']);
if (!$bill_warning) {
    // ====|Min|======|Now|=====|Closed|======|Max|======>
    $bill_warning = \App\CRMDV\Models\Bill::select('service_id', 'customer_id', 'id', 'created_at', 'total_price', 'exp_price', 'expiry_date', 'domain', 'auto_extend', 'saler_id', 'staff_care', 'customer_note')
//            ->whereRaw($whereCompany)
        ->where('status', 1)->where('auto_extend', 1);   //  trạng thái đang kich hoạt & đang kich hoạt gia hạn


    $whereSaleId = '1 = 1';
    if(!in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['super_admin'])) {
        //  nếu ko phải super_admin thì chỉ xem được hợp đồng của mình
        $whereSaleId .= ' AND saler_id = ' . \Auth::guard('admin')->user()->id;
    }

    $bill_warning = $bill_warning->where(function ($query) use ($whereSaleId) {
        $query->orWhereRaw($whereSaleId);   //  tìm theo sale
        $query->orWhere('staff_care', 'like', '%|' . \Auth::guard('admin')->user()->id . '|%');   //  mình theo dõi hđ thì cũng được xem
    });

    $bill_warning = $bill_warning
//        ->whereNull('bill_parent')  //  là hđ gốc
        ->whereIn('service_id', [
            1,
            2,
            3,
            4,
            5,
            6,
            10, 11, 12, 13, 14, 15, 16, //  wp
            17, 18, 19, 20, 21,  // ldp
            23,
        ])
        ->where('expiry_date', '<>', Null)
        ->where('expiry_date', '>=', date('Y-m-d', strtotime('-' . $cau_hinh['min_day'] . ' day')))
        ->where('expiry_date', '<=', date('Y-m-d', strtotime('+' . $cau_hinh['max_day'] . ' day')))->get();
    CommonHelper::putToCache('bills_sap_het_han' . \Auth::guard('admin')->user()->id, $bill_warning, ['bills']);
}

?>

<div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile " style="display: inline-block;">
    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                Nhắc nhở HĐ sắp hết hạn
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-wrapper">
                <div class="kt-portlet__head-actions" style="display: flex; text-align: center">
                    <div class="col-sm-6">
                        <span class="kt-widget12__desc ">Tổng tiền gia hạn cần thu trong tháng này</span><br>
                        <?php
                        $thang_nay = \App\CRMDV\Models\Bill::where('expiry_date', 'like', date('Y') . '-' . date('m') . '-%')->where('auto_extend', 1)->where('status', 1)->whereIn('service_id', [
                            1,
                            2,
                            3,
                            4,
                            5,
                            6,
                            10, 11, 12, 13, 14, 15, 16, //  wp
                            17, 18, 19, 20, 21,  // ldp
                            23,
                        ]);
                        if(!in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['super_admin'])) {
                            //  nếu ko phải super_admin thì chỉ xem được hợp đồng của mình
                            $thang_nay = $thang_nay->where('saler_id', \Auth::guard('admin')->user()->id)->orWhere('staff_care', 'like', '%|' . \Auth::guard('admin')->user()->id . '|%');;
                        }
                        $thang_nay = $thang_nay->sum('exp_price');
                        ?>
                        <span class="kt-widget12__value font-weight-bold">{{number_format($thang_nay, 0, '.', '.')}}</span>
                    </div>

                    <div class="col-sm-6">
                        <span class="kt-widget12__desc">Tổng tiền gia hạn cần thu trong tháng sau</span><br>
                        <?php
                        $thang_sau = \App\CRMDV\Models\Bill::select('exp_price')->where('expiry_date', 'like', date('Y') . '-' . $next_month . '-%')->where('auto_extend', 1)->where('status', 1)->whereIn('service_id', [
                            1,
                            2,
                            3,
                            4,
                            5,
                            6,
                            10, 11, 12, 13, 14, 15, 16, //  wp
                            17, 18, 19, 20, 21,  // ldp
                            23,
                        ]);
                        if(!in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['super_admin'])) {
                            //  nếu ko phải super_admin thì chỉ xem được hợp đồng của mình
                            $thang_sau = $thang_sau->where('saler_id', \Auth::guard('admin')->user()->id)->orWhere('staff_care', 'like', '%|' . \Auth::guard('admin')->user()->id . '|%');;
                        }
                        $thang_sau = $thang_sau->sum('exp_price');
                        ?>
                        <span class="kt-widget12__value font-weight-bold">{{number_format($thang_sau, 0, '.', '.')}}</span>
                    </div>
                </div>
            </div>
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
                        <span style="width: 100px;">Tên khách</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Gói DV</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Tên miền</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Doanh số</span></th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Ngày hết hạn</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Giá gia hạn</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Sale</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">NV theo dõi</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Tên miền</span>
                    </th>
                    @if(in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['super_admin', ]))
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Hủy gia hạn</span>
                    </th>
                    @endif
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 50px;">Chi tiết</span>
                    </th>
                </tr>
                </thead>
                <tbody class="kt-datatable__body ps ps--active-y"
                       style="">

                {{--====|Min|===[EXPIRY_DATE]===|Now|=====|Closed|======|Max|======>--}}
                {{-- Hợp đồng đã hết hạn. --}}
                @if($bill_warning->count()>0)
                    @foreach($bill_warning as $k=>$v)
                        @if(strtotime($v->expiry_date) < time())
                            <tr data-row="0" class="kt-datatable__row"
                                style="left: 0px; background: tomato">
                                {{--<td data-field="ShipDate" class="kt-datatable__cell text-white">
                                                    <span style="width: 25px;" class="text-white">
                                                        <span class="kt-font-bold">{{$k+1}}</span>
                                                    </span>
                                </td>--}}

                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="text-white">
                                                        <a href="/admin/user/edit/{{ @$v->user->id }}"><span
                                                                    class="kt-font-bold text-white  ">{{@$v->user->name}}</span></a>
                                                    </span>
                                </td>

                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="text-white">
                                                        <span class="kt-font-bold">{{@$v->service->name_vi}}</span>
                                                    </span>
                                </td>

                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 200px;" class="text-white">
                                                        <span class="kt-font-bold">{{@$v->domain}}</span>
                                                    </span>
                                </td>

                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="text-white">
                                                        <span class="kt-font-bold">{{number_format(@$v->total_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                </td>

                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="text-white">
                                                        <span class="kt-font-bold">{{date('d-m-Y',strtotime(@$v->expiry_date))}}</span>
                                                    </span>
                                </td>

                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="text-white">
                                                        <span class="kt-font-bold">{{number_format(@$v->exp_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                </td>
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="">
                                                        <span class="kt-font-bold">{{ @$v->saler->name }}</span>
                                                    </span>
                                </td>
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="">
                                                        <span class="kt-font-bold">
                                                            <?php
                                                                $nv_phu_trach = \App\Models\Admin::select(['id', 'name'])->whereIn('id', explode('|', $v->staff_care))->get();
                                                                ?>
                                                            @foreach($nv_phu_trach as $nv)
                                                                {{ $nv->name }}<br>
                                                            @endforeach

                                                        </span>
                                                    </span>
                                </td>
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="">
                                                        <span class="kt-font-bold">Tên miền: {{ @$v->customer_note }}</span>
                                                    </span>
                                </td>
                                @if(in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['super_admin', ]))
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="text-white">
                                                        <span class="fa fa-times-circle fa-2x cancel-extension text-white"
                                                              data-id="{{$v->id}}" style="cursor: pointer"></span>
                                                    </span>
                                </td>
                                @endif
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                        <span style="width: 50px;">
                                                            <a href="/admin/bill/edit/{{ $v->id }}"
                                                               class="btn btn-sm btn-label-brand btn-bold text-white">Xem</a>
                                                        </span>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endif

                {{--====|Min|======|Now|====[EXPIRY_DATE]====|Closed|======|Max|======>--}}
                {{--Hợp đồng sát hạn--}}
                @if($bill_warning->count()>0)
                    @foreach($bill_warning as $k=>$v)
                            <?php

                            //  Khoảng cách từ hnay đến ngày hết hạn expiry_date (đv : ngày)
                            $day_check = (strtotime($v->expiry_date) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);
                            ?>
                        @if(strtotime($v->expiry_date) >= time() && $day_check <= $cau_hinh['close_day'])
                            <tr data-row="0" class="kt-datatable__row"
                                style="left: 0px; background: LightSalmon">
                                {{--                                                style="left: 0px; background: LightSalmon">--}}
{{--                                <td data-field="ShipDate" class="kt-datatable__cell">--}}
{{--                                                    <span style="width: 25px;">--}}
{{--                                                        <span class="kt-font-bold">{{$k+1}}</span>--}}
{{--                                                    </span>--}}
{{--                                </td>--}}

                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <a target="_blank" href="/admin/user/edit/{{$v->customer_id}}"><span
                                                                    class="kt-font-bold ">{{@$v->user->name}}</span></a>
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
                                                        <span class="kt-font-bold">{{date('d-m-Y',strtotime(@$v->expiry_date))}}</span>
                                                    </span>
                                </td>

                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{number_format(@$v->exp_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                </td>
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="">
                                                        <span class="kt-font-bold">{{ @$v->saler->name }}</span>
                                                    </span>
                                </td>
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="">
                                                        <span class="kt-font-bold">
                                                            <?php
                                                                $nv_phu_trach = \App\Models\Admin::select(['id', 'name'])->whereIn('id', explode('|', $v->staff_care))->get();
                                                                ?>
                                                            @foreach($nv_phu_trach as $nv)
                                                                {{ $nv->name }}<br>
                                                            @endforeach

                                                        </span>
                                                    </span>
                                </td>
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="">
                                                        <span class="kt-font-bold">Tên miền: {{ @$v->customer_note }}</span>
                                                    </span>
                                </td>
                                @if(in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['super_admin', ]))
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="fa fa-times-circle fa-2x cancel-extension text-white"
                                                              data-id="{{$v->id}}" style="cursor: pointer"></span>
                                                    </span>
                                </td>
                                @endif
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                        <span style="width: 50px;">
                                                            <a href="/admin/bill/edit/{{ $v->id }}" target="_blank"
                                                               class="btn btn-sm btn-label-brand btn-bold">Xem</a>
                                                        </span>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endif

                {{--====|Min|======|Now|========|Closed|=====[EXPIRY_DATE]====|Max|======>--}}
                {{--Báo trước các Hợp đồng tiếp theo hết hạn--}}
                @if($bill_warning->count()>0)
                    @foreach($bill_warning as $k=>$v)
                            <?php
                            //  Khoảng cách từ hnay đến hạn deadline expiry_date
                            $day_check = (strtotime($v->expiry_date) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);
                            ?>
                        @if($day_check > $cau_hinh['close_day'])
                            {{--<tr data-row="0" class="kt-datatable__row"
                                style="left: 0px; ">
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 25px;">
                                                        <span class="kt-font-bold">{{$k+1}}</span>
                                                    </span>
                                </td>

                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <a href="/admin/bill/edit/{{ $v->id }}"><span
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
                                                        <span class="kt-font-bold">{{date('d-m-Y',strtotime(@$v->expiry_date))}}</span>
                                                    </span>
                                </td>

                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="kt-font-bold">{{number_format(@$v->exp_price, 0, '.', '.')}}đ</span>
                                                    </span>
                                </td>
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="">
                                                        <span class="kt-font-bold">{{ @$v->saler->name }}</span>
                                                    </span>
                                </td>
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;">
                                                        <span class="fa fa-times-circle fa-2x cancel-extension"
                                                              data-id="{{$v->id}}" style="cursor: pointer"></span>
                                                    </span>
                                </td>
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                        <span style="width: 50px;">
                                                            <a href="/admin/bill/edit/{{ $v->id }}"
                                                               class="btn btn-sm btn-label-brand btn-bold">Xem</a>
                                                        </span>
                                </td>
                            </tr>--}}
                        @endif
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <!--end: Datatable -->
        {{--                        <div class="paginate">{{$bill_warning->render()}}</div>--}}
    </div>

</div>

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
        $('.cancel-extension ').click(function (event) {

            var id = $(this).data('id');
            $.ajax({
                url: '{{route('dashboard.cancel_extension')}}',
                data: {
                    id: id
                },
                success: function (result) {
                    if (result.status == true) {
                        toastr.success(result.msg);
                        location.reload();
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