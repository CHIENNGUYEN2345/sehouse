@extends(config('core.admin_theme').'.template')
@section('main')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
			<span class="kt-portlet__head-icon">
                <i class="kt-font-brand flaticon2-avatar"></i>
			</span>
                    <h3 class="kt-portlet__head-title">
                        Hiệu suất công việc
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="">
                            <input type="text" name="quick_search" value="{{ @$_GET['quick_search'] }}"
                                   class="form-control" title="Chỉ cần enter để thực hiện tìm kiếm"
                                   placeholder="Tìm kiếm nhanh theo {{ @$quick_search['label'] }}">
                        </div>
                        <div class="kt-portlet__head-actions">
                            <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle btn-closed-search"
                                    onclick="$('.form-search').slideToggle(100); $('.kt-portlet-search').toggleClass('no-padding');">
                                <i class="la la-search"></i> {{trans('admin.search')}}
                            </button>
                            <div class="dropdown dropdown-inline">
                                <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="la la-download"></i> {{trans('admin.action')}}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end"
                                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(114px, 38px, 0px);">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__section kt-nav__section--first">
                                            <span class="kt-nav__section-text">{{trans('admin.choose_action')}}</span>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a class="kt-nav__link export-excel"
                                               title="Xuất các bản ghi đang lọc ra file excel"
                                               onclick="$('input[name=export]').click();">
                                                <i class="kt-nav__link-icon la la-file-excel-o"></i>
                                                <span class="kt-nav__link-text">{{trans('admin.export_excel')}}</span>
                                            </a>
                                        </li>
                                        
                                    </ul>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body kt-portlet-search @if(!isset($_GET['search'])) no-padding @endif">
                <!--begin: Search Form -->
                <form class="kt-form kt-form--fit kt-margin-b-20 form-search" id="form-search" method="GET" action=""
                      @if(!isset($_GET['search'])) style="display: none;" @endif>
                    <input name="search" type="hidden" value="true">
                    <input name="limit" type="hidden" value="{{ @$limit }}">
                    <input type="hidden" name="quick_search"
                    value="{{ @$_GET['quick_search'] }}"
                    id="quick_search_hidden"
                    class="form-control"
                    placeholder="Tìm kiếm nhanh theo {{ @$quick_search['label'] }}">
                    <div class="row">
                        <div class="col-sm-6 col-lg-1 kt-margin-b-10-tablet-and-mobile list-filter-item">
                            <label>ID:</label>
                            <input type="number" name="id"
                                   placeholder="ID"
                                   value="{{ @$_GET['id'] }}"
                                   class="form-control kt-input">
                        </div>
                        @foreach($filter as $filter_name => $field)
                            <div class="col-sm-6 col-lg-3 kt-margin-b-10-tablet-and-mobile list-filter-item">
                                <label>{{ trans(@$field['label']) }}:</label>
                                @include(config('core.admin_theme').'.list.filter.' . $field['type'], ['name' => $filter_name, 'field'  => $field])
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-primary btn-brand--icon" id="kt_search" type="submit">
						<span>
							<i class="la la-search"></i>
							<span>{{trans('admin.filter')}}</span>
						</span>
                            </button>
                            &nbsp;&nbsp;
                            <a class="btn btn-secondary btn-secondary--icon" id="kt_reset" title="Xóa bỏ bộ lọc"
                               href="/admin/{{ $module['code'] }}">
						<span>
							<i class="la la-close"></i>
							<span>{{trans('admin.reset')}}</span>
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
                                <th rowspan="2">Họ tên</th>
                                <th rowspan="2">Phòng</th>
                            @foreach($dates as $date)
                                <th colspan="5" style="text-align: center;">{{ date('d/m', strtotime($date)) }}
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($dates as $date)
                                <th>Tạo mới</th>
                                <th>Tương tác</th>
                                <th>Quan tâm</th>
                                <th>Quan tâm mới</th>
                                <th>Quan tâm cao</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody class="kt-datatable__body ps ps--active-y" style="max-height: 496px;">
                            <?php $phong = null;

                            //  Thông báo cảnh báo siệu suất công việc không đạt
                            function canh_bao($action, $chi_so, $admin_id) {

                                $role_name = CommonHelper::getRoleName($admin_id, 'name');
                                if ($action == 'tao_moi') {
                                    //  Hành động tạo mới
                                    if (in_array($role_name, ['marketing'])) {
                                        //  Nếu là MKT
                                        if ($chi_so > 0 && $chi_so < 100) {
                                            return 'canh_bao';
                                        }
                                    }
                                } elseif ($action == 'tuong_tac') {
                                    //  Hành động tương tác
                                    if (in_array($role_name, ['telesale'])) {
                                        if ($chi_so > 0 && $chi_so < 200) {
                                            return 'canh_bao';
                                        }
                                    } elseif (in_array($role_name, ['sale'])) {
                                        if ($chi_so > 0 && $chi_so < 10) {
                                            return 'canh_bao';
                                        }
                                    }
                                } elseif ($action == 'khqt_moi') {
                                    //  Số khách quan tâm mới
                                    if (in_array($role_name, ['sale'])) {
                                        if ($chi_so < 2) {
                                            return 'canh_bao';
                                        }
                                    }
                                } elseif ($action == 'khqt_cao') {
                                    //  Số khách quan tâm mới
                                    if (in_array($role_name, ['sale'])) {
                                        if ($chi_so < 2) {
                                            return 'canh_bao';
                                        }
                                    }
                                }
                                return '';
                            }
                            ?>
                        @foreach($reports as $report)
                            <tr data-row="0" class="kt-datatable__row" style="left: 0px;">
                                <td
                                    class="id id-"><a target="_blank" href="/admin/profile/{{ @$report['admin_id'] }}">{{ @$report['name'] }}</a></td>
                                <td
                                    class="id id-"><?php if(@$report['phong'] != $phong) {echo $report['phong']; $phong = $report['phong'];}?></td>
                                @foreach($dates as $date)
                                    @if(!isset($report[$date]))
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @else
                                        <td class="{{ canh_bao('tao_moi', $report[$date]->tao_moi, $report['admin_id']) }}">{{ $report[$date]->tao_moi }}
                                        </td>
                                        <td class="{{ canh_bao('tuong_tac', $report[$date]->tuong_tac, $report['admin_id']) }}" style="background-color: #F3F6F9;">
                                            {{ $report[$date]->tuong_tac }}
                                        </td>
                                        <td>
                                            {{ $report[$date]->khqt }}
                                        </td>
                                        <td class="{{ canh_bao('khqt_moi', $report[$date]->khqt_moi, $report['admin_id']) }}" style="background-color: #F3F6F9;">
                                            {{ $report[$date]->khqt_moi }}
                                        </td>
                                        <td class="{{ canh_bao('khqt_cao', $report[$date]->khqt_cao, $report['admin_id']) }}" style="    border-right-color: #000;">
                                            {{ $report[$date]->khqt_cao }}
                                        </td>
                                    @endif
                                    
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
    .table-striped th, .table-striped td {
        font-weight: normal;
    }
    table.table.table-striped td, table.table.table-striped th {
        border: 1px solid #ccc;
    }
    table.table.table-striped thead, table.table.table-striped td:nth-child(1), table.table.table-striped td:nth-child(2) {
        background: #eee;
    }

    .table-striped tr:hover,
    .table-striped tr:hover td {
        background-color: #beddf7 !important;
    }
    td.canh_bao {
        color: red;
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
