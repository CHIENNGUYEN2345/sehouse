@extends('CRMDV.timekeeper.new_header.new_template')
@section('main')
    <?php
    $cau_hinh = \App\Models\Setting::where('type', 'gio_lam_tab')->pluck('value', 'name')->toArray();
    require base_path('resources/views/CRMDV/timekeeper/funtions.php');
    ?>
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon-calendar-with-a-clock-time-tools"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        Bảng chấm công của {{$data[0]->admin->name}}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        Bảng chấm công của {{$data[0]->admin->name}}
                    </div>
                </div>
            </div>
            {{--            </div>--}}
            {{--            <div class="kt-separator kt-separator--md kt-separator--dashed kt-portlet kt-portlet--mobile" style="margin: 0;"></div>--}}
            {{--            <div class="kt-portlet__body kt-portlet__body--fit">--}}
            <!--begin: Datatable -->
            <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded"
                 id="scrolling_vertical" style="">
                <table class="table table-striped">
                    <thead class="kt-datatable__head">
                    <tr class="kt-datatable__row" style="left: 0px;">

                        <th>STT</th>
                        <th class="kt-datatable__cell kt-datatable__cell--sort">
                            Họ tên
                        </th>
                        <th class="kt-datatable__cell kt-datatable__cell--sort">Mã NV</th>
                        <th class="kt-datatable__cell kt-datatable__cell--sort">ID máy chấm công</th>
                        <th class="kt-datatable__cell kt-datatable__cell--sort">Thời gian chấm</th>
                        <th class="kt-datatable__cell kt-datatable__cell--sort">Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody class="kt-datatable__body ps ps--active-y" style="max-height: 496px;">
                    <?php
                    $di_muon = [];
                    $ve_som = [];
                    $tong_cong = [];
                    $ls_cham_cong = [];
                    $stt = 1;
                    ?>
                    @foreach($data as $item)
                            <?php
                            $cham_cong_trung = false;
                            $val = checkChamCong($item, $ls_cham_cong, $tong_cong, $di_muon, $ve_som, $cau_hinh);
//                            dd($val);
                            $ls_cham_cong = $val['ls_cham_cong'];
                            $tong_cong = $val['tong_cong'];
                            $di_muon = $val['di_muon'];
                            $ve_som = $val['ve_som'];
                            $cham_cong_trung = $val['cham_cong_trung'];

                            ?>
                        <tr data-row="0" class="kt-datatable__row {{ $cham_cong_trung ? 'trung' : '' }}"
                            style="left: 0px;">
                            <td>
                                @if(!$cham_cong_trung)
                                    {{ $stt }}
                                        <?php $stt++; ?>
                                @endif
                            </td>
                            <td class="kt-datatable__cell">
                                {{ $item->admin->name }}
                            </td>
                            <td class="kt-datatable__cell">
                                {{ $item->admin_id }}
                            </td>
                            <td class="kt-datatable__cell">
                                {{ $item->may_cham_cong_id }}8
                            </td>
                            <td class="kt-datatable__cell">
                                {{ $item->time }}
                            </td>
                            <td class="kt-datatable__cell">
                                @include('CRMDV.timekeeper.list.td.thoi_gian_muon',['field' => $item->status , 'cau_hinh' => $cau_hinh])
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>

            </div>
            <div class="lead-chart">
                <span><strong>Đi muộn:</strong> {{ count($di_muon) }} buổi.</span>&nbsp;&nbsp;|&nbsp;&nbsp;
                <span><strong>Đi muộn:</strong> {{ count($ve_som) }} buổi.</span> |
                <span><strong>Tổng công:</strong> {{ count($tong_cong) }} buổi = {{ count($tong_cong)/2 }} ngày công.</span>&nbsp;&nbsp;|&nbsp;
            </div>
            <!--end: Datatable -->
        </div>
    </div>

    </div>
@endsection

@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/list.css') }}">
    <style type="text/css">
        .table-striped tbody tr.trung {
            background-color: #f2f3f8 !important;
        }
    </style>
@endsection
@section('custom_footer')
    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset(config('core.admin_asset').'/js/list.js') }}"></script>
    @include(config('core.admin_theme').'.partials.js_common')
@endsection
@push('scripts')
    {{--    @include(config('core.admin_theme').'.partials.js_common_list')--}}
@endpush
