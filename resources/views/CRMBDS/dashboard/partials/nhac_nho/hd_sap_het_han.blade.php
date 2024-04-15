<div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile ">
    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                Nhắc nhở hợp đồng
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-wrapper">
                <div class="kt-portlet__head-actions" style="display: flex; text-align: center">
                    <div class="col-sm-6">
                        <span class="kt-widget12__desc ">Tổng tiền gia hạn cần thu trong tháng này</span><br>
                        <span class="kt-widget12__value font-weight-bold">{{number_format(\App\CRMBDS\Models\Bill::where('expiry_date', 'like', date('Y') . '-' . date('m') . '-%')->where('auto_extend', 1)->where('status', 1)->whereNull('bill_parent')->sum('exp_price'), 0, '.', '.')}}</span>
                    </div>

                    <div class="col-sm-6">
                        <span class="kt-widget12__desc">Tổng tiền gia hạn cần thu trong tháng sau</span><br>
                        <span class="kt-widget12__value font-weight-bold">{{number_format(\App\CRMBDS\Models\Bill::select('exp_price')->where('expiry_date', 'like', date('Y') . '-' . $next_month . '-%')->where('auto_extend', 1)->where('status', 1)->whereNull('bill_parent')->sum('exp_price'), 0, '.', '.')}}</span>
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
                        <span style="width: 25px;">STT</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Tên học viên</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">khoá học</span></th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 200px;">Tên miền</span></th>

                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Giá</span>
                    </th>
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
                        <span style="width: 100px;">Hủy gia hạn</span>
                    </th>
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
                                <td data-field="ShipDate" class="kt-datatable__cell text-white">
                                                    <span style="width: 25px;" class="text-white">
                                                        <span class="kt-font-bold">{{$k+1}}</span>
                                                    </span>
                                </td>

                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 100px;" class="text-white">
                                                        <a href="/admin/admin/edit/{{ @$v->user->id }}"><span
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
                                                    <span style="width: 100px;" class="text-white">
                                                        <span class="fa fa-times-circle fa-2x cancel-extension text-white"
                                                              data-id="{{$v->id}}" style="cursor: pointer"></span>
                                                    </span>
                                </td>
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
                                <td data-field="ShipDate" class="kt-datatable__cell">
                                                    <span style="width: 25px;">
                                                        <span class="kt-font-bold">{{$k+1}}</span>
                                                    </span>
                                </td>

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
                                                    <span style="width: 100px;">
                                                        <span class="fa fa-times-circle fa-2x cancel-extension text-white"
                                                              data-id="{{$v->id}}" style="cursor: pointer"></span>
                                                    </span>
                                </td>
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