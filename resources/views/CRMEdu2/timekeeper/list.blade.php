@extends(config('core.admin_theme').'.template')
@section('main')
<?php
$cau_hinh = \App\Models\Setting::where('type', 'gio_lam_tab')->pluck('value', 'name')->toArray();

function checkChamCong($item, $ls_cham_cong, $tong_cong, $di_muon, $cau_hinh) {

    $cham_cong_trung = false;
    $di_muon_co_phep = false;
    $phut_tre = 'Đúng giờ';
    // $item->time = '2022-12-11 07:23:00';
    if (strtotime($item->time) < strtotime(date('Y-m-d 10:00:00', strtotime($item->time)))) {
        //  Buổi sáng đến làm

        if (isset($ls_cham_cong[date('d', strtotime($item->time)) . 's_den'])) {
            $cham_cong_trung = true;
        }
        $ls_cham_cong[date('d', strtotime($item->time)) . 's_den'] = 1;
        $tong_cong[date('d', strtotime($item->time)) . 's'] = 1;


        if (strtotime($item->time) > strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_sang'], strtotime($item->time)))) {
            //  đi muộn
            if ($item->status != 1) {
                //  ko có phép
                $di_muon[date('d', strtotime($item->time)) . 's'] = 1;  //  đi muộn ngày này buổi sáng
            } elseif ($item->status == 1) {
                $di_muon_co_phep = true;
            }

            $phut_tre = ceil((strtotime($item->time) - strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_sang'], strtotime($item->time)))) / 60);


        } else {
            //  đúng giờ

        }
    } elseif (strtotime($item->time) >= strtotime(date('Y-m-d 10:00:00', strtotime($item->time))) & strtotime($item->time) < strtotime(date('Y-m-d 12:15:00', strtotime($item->time)))) {
        //  buổi sáng đi về
        if (isset($ls_cham_cong[date('d', strtotime($item->time)) . 's_ve'])) {
            $cham_cong_trung = true;
        }
        $ls_cham_cong[date('d', strtotime($item->time)) . 's_ve'] = 1;
    } elseif (strtotime($item->time) >= strtotime(date('Y-m-d 12:15:00', strtotime($item->time))) & strtotime($item->time) < strtotime(date('Y-m-d 15:00:00', strtotime($item->time)))) {
        //  Buổi chiều đến làm
        if (isset($ls_cham_cong[date('d', strtotime($item->time)) . 'c_den'])) {
            $cham_cong_trung = true;
        }

        $ls_cham_cong[date('d', strtotime($item->time)) . 'c_den'] = 1;
        $tong_cong[date('d', strtotime($item->time)) . 'c'] = 1;

        if ( ( strtotime($item->time) > strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_chieu'], strtotime($item->time))) )) {
            //  đi muộn
            if ($item->status != 1) {
                $di_muon[date('d', strtotime($item->time)) . 'c'] = 1;  //  đi muộn ngày này buổi chiều
            } elseif ($item->status == 1) {
                $di_muon_co_phep = true;
            }

            $phut_tre = ceil((strtotime($item->time) - strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_chieu'], strtotime($item->time)))) / 60);

        } else {
            //  đúng giờ

        }
    } elseif (strtotime($item->time) >= strtotime(date('Y-m-d 15:00:00', strtotime($item->time))) ) {
        //  Buổi chiều về
        if (isset($ls_cham_cong[date('d', strtotime($item->time)) . 'c_ve'])) {
            $cham_cong_trung = true;
        }
        $ls_cham_cong[date('d', strtotime($item->time)) . 'c_ve'] = 1;
    }

    return [
        'ls_cham_cong' => $ls_cham_cong,
        'tong_cong' => $tong_cong,
        'di_muon' => $di_muon,
        'di_muon_co_phep' => $di_muon_co_phep,
        'phut_tre' => $phut_tre,
        'cham_cong_trung' => $cham_cong_trung,
    ];
}
?>
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon-calendar-with-a-clock-time-tools"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        {{ $module['label'] }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        @if(in_array('timekeeper_edit', $permissions))
                        <a href="/admin/{{ $module['code'] }}/bao-cao" class="btn btn-primary">Báo cáo</a>
                        @endif

                        <div class="">
                            <input type="text" name="quick_search" value="{{ @$_GET['quick_search'] }}"
                                   class="form-control" title="Chỉ cần enter để thực hiện tìm kiếm"
                                   placeholder="Tìm kiếm nhanh theo {{ @$quick_search['label'] }}">
                        </div>
                        <div class="kt-portlet__head-actions">
                            <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle btn-closed-search"
                                    onclick="$('.form-search').slideToggle(100); $('.kt-portlet-search').toggleClass('no-padding');">
                                <i class="la la-search"></i> Tìm kiếm
                            </button>
                            <div class="dropdown dropdown-inline">
                                <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="la la-download"></i> Hành động
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end"
                                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(114px, 38px, 0px);">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__section kt-nav__section--first">
                                            <span class="kt-nav__section-text">Chọn hành động</span>
                                        </li>
                                        @if(in_array('timekeeper_view', $permissions))
                                        <li class="kt-nav__item">
                                            <a href="/admin/import/add?table=timekeeper&table_label=Dữ liệu chấm công" class="kt-nav__link" 
                                                title="Nhập file excel lên để đẩy dữ liệu vào hệ thống">
                                                <i class="kt-nav__link-icon la la-copy"></i>
                                                <span class="kt-nav__link-text">Import excel</span>
                                            </a>
                                        </li>
                                        @endif
                                        <li class="kt-nav__item">
                                            <a class="kt-nav__link export-excel"
                                               title="Xuất các bản ghi đang lọc ra file excel"
                                               onclick="$('input[name=export]').click();">
                                                <i class="kt-nav__link-icon la la-file-excel-o"></i>
                                                <span class="kt-nav__link-text">Xuất Excel</span>
                                            </a>
                                        </li>
                                        @if(in_array('timekeeper_edit', $permissions))
                                            <li class="kt-nav__item">
                                                <a href="#" class="kt-nav__link" onclick="multiDelete();"
                                                   title="Xóa tất cả các dòng đang được tích chọn">
                                                    <i class="kt-nav__link-icon la la-copy"></i>
                                                    <span class="kt-nav__link-text">Xóa nhiều</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @if(in_array('timekeeper_edit', $permissions))
                            <a href="{{ url('/admin/'.$module['code'].'/add/') }}"
                               class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="la la-plus"></i>
                                Tạo mới
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body kt-portlet-search @if(!isset($_GET['search'])) no-padding @endif">
                <!--begin: Search Form -->
                <form class="kt-form kt-form--fit kt-margin-b-20 form-search" id="form-search" method="GET" action=""
                      @if(!isset($_GET['search'])) style="display: none;" @endif>
                    <input name="search" type="hidden" value="true">
                    <input name="limit" type="hidden" value="{{ $limit }}"><input type="hidden" name="quick_search"
                                                                                  value="{{ @$_GET['quick_search'] }}"
                                                                                  id="quick_search_hidden"
                                                                                  class="form-control"
                                                                                  placeholder="Tìm kiếm nhanh theo {{ @$quick_search['label'] }}">
                    <div class="row">

                        @foreach($filter as $filter_name => $field)
                            <div class="col-sm-6 col-lg-3 kt-margin-b-10-tablet-and-mobile list-filter-item">
                                <label>{{ @$field['label'] }}:</label>
                                @include(config('core.admin_theme').'.list.filter.' . $field['type'], ['name' => $filter_name, 'field'  => $field])
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-primary btn-brand--icon" id="kt_search" type="submit">
                        <span>
                            <i class="la la-search"></i>
                            <span>Lọc</span>
                        </span>
                            </button>
                            &nbsp;&nbsp;
                            <a class="btn btn-secondary btn-secondary--icon" id="kt_reset" title="Xóa bỏ bộ lọc"
                               href="/admin/{{ $module['code'] }}">
                        <span>
                            <i class="la la-close"></i>
                            <span>Reset</span>
                        </span>
                            </a>
                        </div>
                    </div>
                    <input name="export" type="submit" value="export" style="display: none;">
                    @foreach($module['list'] as $k => $field)
                        <input name="sorts[]" value="{{ @$_GET['sorts'][$k] }}"
                               class="sort sort-{{ $field['name'] }}" type="hidden">
                    @endforeach
                </form>
                <!--end: Search Form -->
            </div>
            <div class="kt-separator kt-separator--md kt-separator--dashed" style="margin: 0;"></div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <!--begin: Datatable -->
                <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded"
                     id="scrolling_vertical" style="">
                    <table class="table table-striped">
                        <thead class="kt-datatable__head">
                        <tr class="kt-datatable__row" style="left: 0px;">
                            <th style="display: none;"></th>
                            <th data-field="id"
                                class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check"><span
                                        style="width: 20px;"><label
                                            class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input
                                                type="checkbox"
                                                class="checkbox-master">&nbsp;<span></span></label></span></th>
                       
                            @php $count_sort = 0; @endphp
                            @foreach($module['list'] as $field)
                                <th data-field="{{ $field['name'] }}"
                                    class="kt-datatable__cell kt-datatable__cell--sort {{ @$_GET['sorts'][$count_sort] != '' ? 'kt-datatable__cell--sorted' : '' }}"
                                    @if(isset($field['sort']))
                                    onclick="sort('{{ $field['name'] }}')"
                                        @endif
                                >
                                    {{ $field['label'] }}
                                    @if(isset($field['sort']))
                                        @if(@$_GET['sorts'][$count_sort] == $field['name'].'|asc')
                                            <i class="flaticon2-arrow-up"></i>
                                        @else
                                            <i class="flaticon2-arrow-down"></i>
                                        @endif
                                    @endif

                                </th>
                                @php $count_sort++; @endphp
                            @endforeach
                        </tr>
                        </thead>
                        <tbody class="kt-datatable__body ps ps--active-y" style="max-height: 496px;">
                            <?php 
                            $di_muon = [];
                            $tong_cong = [];
                            $ls_cham_cong = [];
                            ?>
                        @foreach($listItem as $item)
                            <?php 
                            $cham_cong_trung = false;
                            $val = checkChamCong($item, $ls_cham_cong, $tong_cong, $di_muon, $cau_hinh);
                            $ls_cham_cong = $val['ls_cham_cong'];
                            $tong_cong = $val['tong_cong'];
                            $di_muon = $val['di_muon'];
                            $cham_cong_trung = $val['cham_cong_trung'];
                            ?>
                            <tr data-row="0" class="kt-datatable__row {{ $cham_cong_trung ? 'trung' : '' }}" style="left: 0px;">
                                <td style="display: none;"
                                    class="id id-{{ $item->id }}">{{ $item->id }}</td>
                                <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check"
                                    data-field="ID"><span style="width: 20px;"><label
                                                class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input
                                                    name="id[]"
                                                    type="checkbox" class="ids"
                                                    value="{{ $item->id }}">&nbsp;<span></span></label></span>
                                </td>
                       
                                @foreach($module['list'] as $field)
                                    <td data-field="{{ @$field['name'] }}"
                                        class="kt-datatable__cell item-{{ @$field['name'] }}">
                                        @if($field['type'] == 'custom')
                                            @include($field['td'], ['field' => $field, 'cau_hinh' => $cau_hinh])
                                        @else
                                            @include(config('core.admin_theme').'.list.td.'.$field['type'])
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                        </div>
                        <div class="ps__rail-y" style="top: 0px; height: 496px; right: 0px;">
                            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 207px;"></div>
                        </div>
                        </tbody>
                    </table>
                    <div class="kt-datatable__pager kt-datatable--paging-loaded">
                        {!! $listItem->appends(isset($param_url) ? $param_url : '')->links() != '' ? $listItem->appends(isset($param_url) ? $param_url : '')->links() : '<ul class="pagination page-numbers nav-pagination links text-center"></ul>' !!}
                        <div class="kt-datatable__pager-info">
                            <div class="dropdown bootstrap-select kt-datatable__pager-size"
                                 style="width: 60px;">
                                <select class="selectpicker kt-datatable__pager-size select-page-size"
                                        onchange="$('input[name=limit]').val($(this).val());$('#form-search').submit();"
                                        title="Chọn số bản ghi hiển thị" data-width="60px"
                                        data-selected="20" tabindex="-98">
                                    <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20</option>
                                    <option value="30" {{ $limit == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <span class="kt-datatable__pager-detail">Hiển thị {{ (($page - 1) * $limit) + 1 }} - {{ ($page * $limit) < $record_total ? ($page * $limit) : $record_total }} của {{ @number_format($record_total) }}</span>
                        </div>
                    </div>
                </div>
                <!--end: Datatable -->
            </div>
        </div>
        <div class="lead-chart">
            <span>Đi muộn: {{ count($di_muon) }} buổi.</span>&nbsp;&nbsp;|&nbsp;&nbsp;
            <span>Tổng công: {{ count($tong_cong) }} buổi.</span>&nbsp;&nbsp;|&nbsp;&nbsp;
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
    @include(config('core.admin_theme').'.partials.js_common_list')
@endpush
