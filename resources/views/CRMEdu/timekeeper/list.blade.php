@extends(config('core.admin_theme').'.template')
@section('main')
<?php
$cau_hinh = \App\Models\Setting::where('type', 'gio_lam_tab')->pluck('value', 'name')->toArray();
require base_path('resources/views/CRMEdu/timekeeper/funtions.php');
?>
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon-calendar-with-a-clock-time-tools"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        {{ trans($module['label']) }}
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
                                   placeholder="Tìm kiếm nhanh">
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
                                        @if(in_array('timekeeper_edit', $permissions))
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
                                                                                  placeholder="Tìm kiếm nhanh">
                    <div class="row">

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
                            <th data-field="id" class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input type="checkbox" class="checkbox-master">&nbsp;<span></span></label></span></th>
                            <th>STT</th>

                            <th data-field="admin_id" class="kt-datatable__cell kt-datatable__cell--sort ">
                                Họ tên

                            </th>
                            <th data-field="admin_id" class="kt-datatable__cell kt-datatable__cell--sort ">
                                Mã NV

                            </th>
                            <th data-field="may_cham_cong_id" class="kt-datatable__cell kt-datatable__cell--sort ">
                                ID máy chấm công

                            </th>
                            <th data-field="time" class="kt-datatable__cell kt-datatable__cell--sort ">
                                Thời gian chấm

                            </th>
                            <th data-field="thoi_gian_muon" class="kt-datatable__cell kt-datatable__cell--sort ">
                                Thời gian muộn

                            </th>
                            <th data-field="ly_do_muon" class="kt-datatable__cell kt-datatable__cell--sort ">
                                Lý do muộn

                            </th>
                            <th data-field="status" class="kt-datatable__cell kt-datatable__cell--sort ">
                                Cho phép

                            </th>

                        </tr>
                        </thead>
                        <tbody class="kt-datatable__body ps ps--active-y" style="max-height: 496px;">
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18457">18457</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18457">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                1
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18457" target="_blank">
                                    Hoàng Hiệu

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18457 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18457" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18457/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18457" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/373" target="_blank">

                                    hieuh
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                15                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                01/07/2023 07:20:47                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18458">18458</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18458">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18458" target="_blank">
                                    Nguyễn Văn Khánh

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18458 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18458" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18458/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18458" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/474" target="_blank">

                                    khanhnv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                9                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                01/07/2023 07:24:34                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18459">18459</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18459">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18459" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18459 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18459" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18459/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18459" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                119                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                01/07/2023 07:24:39                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18460">18460</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18460">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18460" target="_blank">
                                    Nguyễn Thị Ngân

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18460 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18460" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18460/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18460" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/932" target="_blank">

                                    ngannt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                117                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                01/07/2023 07:34:37                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 5 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18460" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18461">18461</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18461">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18461" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18461 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18461" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18461/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18461" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                01/07/2023 07:34:43                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 5 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18461" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18462">18462</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18462">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18462" target="_blank">
                                    Nguyễn Thị Ngân

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18462 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18462" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18462/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18462" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/932" target="_blank">

                                    ngannt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                117                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                01/07/2023 11:33:45                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18463">18463</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18463">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18463" target="_blank">
                                    Hoàng Hiệu

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18463 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18463" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18463/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18463" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/373" target="_blank">

                                    hieuh
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                15                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                01/07/2023 11:47:31                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18464">18464</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18464">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18464" target="_blank">
                                    Nguyễn Văn Khánh

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18464 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18464" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18464/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18464" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/474" target="_blank">

                                    khanhnv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                9                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                01/07/2023 11:47:35                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18465">18465</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18465">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18465" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18465 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18465" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18465/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18465" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                01/07/2023 11:55:09                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18466">18466</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18466">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                2
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18466" target="_blank">
                                    Nguyễn Văn Khánh

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18466 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18466" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18466/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18466" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/474" target="_blank">

                                    khanhnv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                9                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                01/07/2023 13:17:56                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 18 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18466" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18467">18467</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18467">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18467" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18467 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18467" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18467/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18467" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                119                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                01/07/2023 13:18:07                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 19 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18467" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18468">18468</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18468">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                3
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18468" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18468 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18468" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18468/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18468" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 07:22:41                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18469">18469</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18469">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18469" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18469 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18469" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18469/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18469" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                119                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 07:25:00                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18470">18470</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18470">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18470" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18470 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18470" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18470/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18470" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 07:25:04                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18471">18471</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18471">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18471" target="_blank">
                                    Nguyễn Thị Mai

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18471 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18471" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18471/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18471" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/897" target="_blank">

                                    maint
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                112                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 07:26:37                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18472">18472</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18472">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18472" target="_blank">
                                    Phùng Thị Vân Trang

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18472 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18472" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18472/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18472" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/918" target="_blank">

                                    trangptv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                114                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 07:43:51                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 14 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18472" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18473">18473</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18473">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18473" target="_blank">
                                    Đỗ Thị Ngọc Ánh

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18473 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18473" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18473/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18473" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/889" target="_blank">

                                    anhdtn
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                111                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 07:50:38                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 21 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18473" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18474">18474</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18474">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18474" target="_blank">
                                    Nguyễn Thị Mai

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18474 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18474" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18474/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18474" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/897" target="_blank">

                                    maint
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                112                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 11:31:20                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18475">18475</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18475">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                4
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18475" target="_blank">
                                    Võ Khắc Trường

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18475 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18475" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18475/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18475" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/838" target="_blank">

                                    truongvk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                106                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 12:59:00                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18476">18476</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18476">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18476" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18476 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18476" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18476/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18476" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                115                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 12:59:09                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18477">18477</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18477">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18477" target="_blank">
                                    Nguyễn Huy Khang Lâm

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18477 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18477" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18477/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18477" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/892" target="_blank">

                                    lamnhk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                110                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 12:59:22                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18478">18478</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18478">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18478" target="_blank">
                                    Nguyễn Huy Khang Lâm

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18478 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18478" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18478/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18478" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/892" target="_blank">

                                    lamnhk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                110                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 17:03:10                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18479">18479</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18479">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18479" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18479 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18479" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18479/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18479" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                115                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 17:03:17                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18480">18480</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18480">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18480" target="_blank">
                                    Đỗ Thị Ngọc Ánh

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18480 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18480" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18480/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18480" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/889" target="_blank">

                                    anhdtn
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                111                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 17:06:22                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18481">18481</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18481">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18481" target="_blank">
                                    Võ Khắc Trường

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18481 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18481" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18481/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18481" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/838" target="_blank">

                                    truongvk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                106                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 17:07:45                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18482">18482</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18482">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18482" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18482 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18482" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18482/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18482" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 17:07:53                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18483">18483</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18483">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18483" target="_blank">
                                    Phùng Thị Vân Trang

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18483 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18483" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18483/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18483" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/918" target="_blank">

                                    trangptv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                114                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                03/07/2023 17:08:13                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18484">18484</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18484">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                5
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18484" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18484 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18484" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18484/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18484" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 07:27:52                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18485">18485</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18485">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18485" target="_blank">
                                    Trần Thị Thương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18485 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18485" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18485/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18485" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/917" target="_blank">

                                    thuongtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                113                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 07:28:54                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18486">18486</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18486">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18486" target="_blank">
                                    Nguyễn Thị Ngân

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18486 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18486" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18486/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18486" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/932" target="_blank">

                                    ngannt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                117                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 07:29:13                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18487">18487</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18487">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18487" target="_blank">
                                    Nguyễn Thị Tuyết Hương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18487 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18487" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18487/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18487" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/929" target="_blank">

                                    huongntt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                116                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 07:31:58                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 2 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18487" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18488">18488</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18488">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18488" target="_blank">
                                    Phùng Thị Vân Trang

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18488 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18488" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18488/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18488" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/918" target="_blank">

                                    trangptv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                114                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 07:42:58                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 13 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18488" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18489">18489</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18489">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                6
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18489" target="_blank">
                                    Hoàng Hiệu

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18489 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18489" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18489/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18489" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/373" target="_blank">

                                    hieuh
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                15                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 12:50:45                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18490">18490</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18490">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18490" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18490 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18490" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18490/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18490" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                115                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 12:50:53                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18491">18491</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18491">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18491" target="_blank">
                                    Nguyễn Huy Khang Lâm

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18491 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18491" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18491/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18491" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/892" target="_blank">

                                    lamnhk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                110                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 12:58:35                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18492">18492</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18492">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18492" target="_blank">
                                    Đỗ Thị Ngọc Ánh

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18492 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18492" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18492/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18492" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/889" target="_blank">

                                    anhdtn
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                111                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 13:32:36                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 33 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18492" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18493">18493</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18493">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18493" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18493 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18493" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18493/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18493" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                115                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 17:04:30                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18494">18494</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18494">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18494" target="_blank">
                                    Hoàng Hiệu

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18494 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18494" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18494/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18494" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/373" target="_blank">

                                    hieuh
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                15                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 17:04:56                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18495">18495</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18495">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18495" target="_blank">
                                    Nguyễn Thị Ngân

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18495 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18495" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18495/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18495" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/932" target="_blank">

                                    ngannt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                117                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 17:07:12                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18496">18496</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18496">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18496" target="_blank">
                                    Nguyễn Thị Tuyết Hương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18496 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18496" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18496/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18496" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/929" target="_blank">

                                    huongntt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                116                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 17:07:22                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18497">18497</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18497">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18497" target="_blank">
                                    Nguyễn Huy Khang Lâm

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18497 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18497" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18497/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18497" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/892" target="_blank">

                                    lamnhk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                110                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 17:11:10                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18498">18498</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18498">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18498" target="_blank">
                                    Phùng Thị Vân Trang

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18498 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18498" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18498/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18498" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/918" target="_blank">

                                    trangptv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                114                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 17:18:52                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18499">18499</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18499">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18499" target="_blank">
                                    Trần Thị Thương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18499 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18499" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18499/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18499" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/917" target="_blank">

                                    thuongtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                113                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                04/07/2023 17:18:55                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18500">18500</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18500">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                7
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18500" target="_blank">
                                    Hoàng Hiệu

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18500 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18500" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18500/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18500" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/373" target="_blank">

                                    hieuh
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                15                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 07:24:31                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18501">18501</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18501">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18501" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18501 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18501" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18501/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18501" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 07:26:16                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18502">18502</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18502">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18502" target="_blank">
                                    Võ Khắc Trường

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18502 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18502" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18502/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18502" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/838" target="_blank">

                                    truongvk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                106                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 07:27:19                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18503">18503</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18503">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18503" target="_blank">
                                    Nguyễn Thị Tuyết Hương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18503 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18503" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18503/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18503" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/929" target="_blank">

                                    huongntt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                116                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 07:34:01                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 5 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18503" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18504">18504</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18504">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18504" target="_blank">
                                    Phùng Thị Vân Trang

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18504 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18504" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18504/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18504" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/918" target="_blank">

                                    trangptv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                114                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 07:47:05                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 18 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18504" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18505">18505</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18505">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18505" target="_blank">
                                    Trần Thị Thương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18505 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18505" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18505/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18505" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/917" target="_blank">

                                    thuongtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                113                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 07:47:19                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 18 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18505" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18506">18506</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18506">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18506" target="_blank">
                                    Hoàng Hiệu

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18506 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18506" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18506/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18506" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/373" target="_blank">

                                    hieuh
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                15                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 11:41:00                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18507">18507</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18507">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18507" target="_blank">
                                    Nguyễn Thị Tuyết Hương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18507 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18507" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18507/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18507" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/929" target="_blank">

                                    huongntt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                116                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 11:45:22                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18508">18508</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18508">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                8
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18508" target="_blank">
                                    Trần Tuấn Anh

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18508 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18508" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18508/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18508" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/514" target="_blank">

                                    anhtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                21                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 12:38:14                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18509">18509</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18509">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18509" target="_blank">
                                    Nguyễn Huy Khang Lâm

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18509 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18509" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18509/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18509" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/892" target="_blank">

                                    lamnhk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                110                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 12:57:27                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18510">18510</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18510">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18510" target="_blank">
                                    Nguyễn Thị Mai

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18510 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18510" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18510/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18510" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/897" target="_blank">

                                    maint
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                112                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 12:57:41                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18511">18511</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18511">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18511" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18511 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18511" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18511/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18511" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                115                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 12:57:55                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18512">18512</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18512">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18512" target="_blank">
                                    Hoàng Hiệu

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18512 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18512" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18512/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18512" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/373" target="_blank">

                                    hieuh
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                15                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 12:59:24                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18513">18513</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18513">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18513" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18513 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18513" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18513/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18513" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                115                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 17:09:21                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18514">18514</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18514">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18514" target="_blank">
                                    Nguyễn Thị Mai

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18514 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18514" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18514/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18514" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/897" target="_blank">

                                    maint
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                112                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 17:12:33                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18515">18515</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18515">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18515" target="_blank">
                                    Hoàng Hiệu

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18515 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18515" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18515/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18515" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/373" target="_blank">

                                    hieuh
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                15                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 17:16:36                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18516">18516</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18516">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18516" target="_blank">
                                    Nguyễn Huy Khang Lâm

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18516 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18516" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18516/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18516" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/892" target="_blank">

                                    lamnhk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                110                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 17:17:06                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18517">18517</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18517">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18517" target="_blank">
                                    Phùng Thị Vân Trang

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18517 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18517" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18517/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18517" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/918" target="_blank">

                                    trangptv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                114                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 17:25:27                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18518">18518</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18518">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18518" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18518 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18518" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18518/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18518" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 17:25:35                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18519">18519</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18519">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18519" target="_blank">
                                    Trần Tuấn Anh

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18519 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18519" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18519/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18519" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/514" target="_blank">

                                    anhtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                21                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 17:25:38                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18520">18520</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18520">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18520" target="_blank">
                                    Võ Khắc Trường

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18520 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18520" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18520/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18520" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/838" target="_blank">

                                    truongvk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                106                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 17:25:46                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18521">18521</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18521">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18521" target="_blank">
                                    Trần Thị Thương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18521 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18521" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18521/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18521" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/917" target="_blank">

                                    thuongtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                113                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                05/07/2023 17:25:54                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18522">18522</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18522">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                9
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18522" target="_blank">
                                    Trần Tuấn Anh

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18522 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18522" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18522/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18522" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/514" target="_blank">

                                    anhtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                21                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 07:34:03                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 5 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18522" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18523">18523</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18523">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18523" target="_blank">
                                    Nguyễn Thị Ngân

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18523 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18523" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18523/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18523" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/932" target="_blank">

                                    ngannt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                117                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 07:34:40                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 5 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18523" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18524">18524</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18524">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18524" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18524 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18524" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18524/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18524" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 07:34:49                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 5 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18524" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18525">18525</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18525">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18525" target="_blank">
                                    Trần Thị Thương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18525 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18525" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18525/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18525" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/917" target="_blank">

                                    thuongtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                113                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 07:35:09                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 6 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18525" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18526">18526</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18526">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18526" target="_blank">
                                    Phùng Thị Vân Trang

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18526 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18526" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18526/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18526" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/918" target="_blank">

                                    trangptv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                114                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 07:35:13                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 6 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18526" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18527">18527</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18527">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18527" target="_blank">
                                    Nguyễn Thị Tuyết Hương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18527 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18527" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18527/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18527" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/929" target="_blank">

                                    huongntt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                116                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 07:39:40                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 10 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18527" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18528">18528</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18528">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18528" target="_blank">
                                    Trần Thị Thương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18528 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18528" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18528/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18528" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/917" target="_blank">

                                    thuongtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                113                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 07:45:03                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 16 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18528" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18529">18529</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18529">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18529" target="_blank">
                                    Trần Tuấn Anh

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18529 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18529" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18529/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18529" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/514" target="_blank">

                                    anhtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                21                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 12:06:59                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18530">18530</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18530">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                10
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18530" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18530 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18530" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18530/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18530" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                115                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 12:59:25                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18531">18531</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18531">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18531" target="_blank">
                                    Nguyễn Huy Khang Lâm

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18531 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18531" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18531/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18531" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/892" target="_blank">

                                    lamnhk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                110                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 13:01:51                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 2 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18531" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18532">18532</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18532">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18532" target="_blank">
                                    Nguyễn Huy Khang Lâm

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18532 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18532" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18532/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18532" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/892" target="_blank">

                                    lamnhk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                110                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 17:06:48                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18533">18533</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18533">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18533" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18533 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18533" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18533/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18533" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 17:09:25                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18534">18534</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18534">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18534" target="_blank">
                                    Trần Thị Thương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18534 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18534" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18534/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18534" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/917" target="_blank">

                                    thuongtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                113                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 17:09:50                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18535">18535</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18535">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18535" target="_blank">
                                    Phùng Thị Vân Trang

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18535 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18535" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18535/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18535" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/918" target="_blank">

                                    trangptv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                114                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 17:10:08                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18536">18536</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18536">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18536" target="_blank">
                                    Nguyễn Thị Ngân

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18536 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18536" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18536/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18536" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/932" target="_blank">

                                    ngannt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                117                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 17:15:44                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18537">18537</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18537">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18537" target="_blank">
                                    Nguyễn Thị Tuyết Hương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18537 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18537" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18537/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18537" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/929" target="_blank">

                                    huongntt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                116                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 17:15:50                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18538">18538</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18538">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18538" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18538 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18538" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18538/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18538" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                115                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                06/07/2023 17:17:09                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18539">18539</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18539">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                11
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18539" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18539 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18539" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18539/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18539" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 07:32:18                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 3 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18539" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18540">18540</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18540">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18540" target="_blank">
                                    Trần Thị Thương

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18540 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18540" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18540/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18540" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/917" target="_blank">

                                    thuongtt
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                113                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 07:44:26                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 15 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18540" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18541">18541</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18541">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18541" target="_blank">
                                    Võ Khắc Trường

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18541 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18541" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18541/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18541" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/838" target="_blank">

                                    truongvk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                106                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 07:52:43                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 23 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18541" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18542">18542</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18542">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18542" target="_blank">
                                    Lê Văn Cường

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18542 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18542" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18542/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18542" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/966" target="_blank">

                                    cuonglv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                124                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 10:29:19                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18542" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18543">18543</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18543">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18543" target="_blank">
                                    Nguyễn Anh Tú

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18543 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18543" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18543/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18543" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/967" target="_blank">

                                    tuna
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                123                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 11:35:15                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18544">18544</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18544">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                12
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18544" target="_blank">
                                    Hoàng Hiệu

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18544 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18544" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18544/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18544" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/373" target="_blank">

                                    hieuh
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                15                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 12:56:40                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18545">18545</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18545">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18545" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18545 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18545" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18545/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18545" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                115                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 13:00:00                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18546">18546</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18546">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18546" target="_blank">
                                    Nguyễn Huy Khang Lâm

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18546 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18546" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18546/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18546" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/892" target="_blank">

                                    lamnhk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                110                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 13:00:12                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 1 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18546" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18547">18547</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18547">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18547" target="_blank">
                                    Nguyễn Thị Mai

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18547 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18547" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18547/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18547" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/897" target="_blank">

                                    maint
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                112                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 13:02:56                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 3 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18547" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18548">18548</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18548">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18548" target="_blank">
                                    Nguyễn Huy Khang Lâm

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18548 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18548" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18548/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18548" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/892" target="_blank">

                                    lamnhk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                110                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 17:03:49                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18549">18549</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18549">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18549" target="_blank">
                                    Lê Văn Cường

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18549 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18549" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18549/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18549" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/966" target="_blank">

                                    cuonglv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                124                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 17:13:18                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18550">18550</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18550">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18550" target="_blank">
                                    Hoàng Hiệu

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18550 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18550" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18550/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18550" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/373" target="_blank">

                                    hieuh
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                15                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 17:18:26                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18551">18551</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18551">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18551" target="_blank">
                                    Nguyễn Thị Mai

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18551 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18551" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18551/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18551" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/897" target="_blank">

                                    maint
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                112                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 17:18:31                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18552">18552</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18552">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18552" target="_blank">


                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18552 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18552" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18552/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18552" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/" target="_blank">


                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                115                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 17:18:52                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18553">18553</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18553">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18553" target="_blank">
                                    Nguyễn Bá Đạt

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18553 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18553" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18553/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18553" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/369" target="_blank">

                                    datnb
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                12                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 17:19:52                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18554">18554</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18554">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18554" target="_blank">
                                    Võ Khắc Trường

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18554 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18554" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18554/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18554" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/838" target="_blank">

                                    truongvk
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                106                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                07/07/2023 17:20:42                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="">Đúng giờ</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row " style="left: 0px;">
                            <td style="display: none;" class="id id-18555">18555</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18555">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                                13
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18555" target="_blank">
                                    Nguyễn Anh Tú

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18555 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18555" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18555/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18555" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/967" target="_blank">

                                    tuna
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                123                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                08/07/2023 08:14:37                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 45 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18555" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>
                        <tr data-row="0" class="kt-datatable__row trung" style="left: 0px;">
                            <td style="display: none;" class="id id-18556">18556</td>
                            <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check" data-field="ID"><span style="width: 20px;"><label class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input name="id[]" type="checkbox" class="ids" value="18556">&nbsp;<span></span></label></span>
                            </td>

                            <td>
                            </td>

                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/timekeeper/edit/18556" target="_blank">
                                    Lê Văn Cường

                                </a>
                                <div class="row-actions" style="    font-size: 13px;">
                                    <span class="edit" title="ID của bản ghi">ID: 18556 | </span>
                                    <span class="edit"><a href="https://khoweb.top/admin/timekeeper/edit/18556" title="Sửa bản ghi này">Chỉnh sửa</a> | </span>
                                    <span class=""><a href="https://khoweb.top/admin/timekeeper/18556/duplicate" title="Nhân bản bản ghi này">Nhân bản</a> | </span>
                                    <span class="trash"><a class="delete-warning" href="https://khoweb.top/admin/timekeeper/delete/18556" title="Xóa bản ghi">Xóa</a> | </span>
                                </div>
                            </td>
                            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                                <a href="/admin/admin/edit/966" target="_blank">

                                    cuonglv
                                </a>
                            </td>
                            <td data-field="may_cham_cong_id" class="kt-datatable__cell item-may_cham_cong_id">
                                124                                                                            </td>
                            <td data-field="time" class="kt-datatable__cell item-time">
                                08/07/2023 08:14:45                                                                            </td>
                            <td data-field="thoi_gian_muon" class="kt-datatable__cell item-thoi_gian_muon">
                                <span style="background: #951b00; color: #fff;">Trễ 45 phút</span>                                                                            </td>
                            <td data-field="ly_do_muon" class="kt-datatable__cell item-ly_do_muon">
                            </td>
                            <td data-field="status" class="kt-datatable__cell item-status">
                                <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="" data-id="18556" style="cursor:pointer;" data-column="status">Không lý do</span>
                            </td>
                        </tr>


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
{{--            <span><strong>Đi muộn:</strong> {{ count($di_muon) }} buổi.</span>&nbsp;&nbsp;|&nbsp;&nbsp;--}}
{{--            <span><strong>Tổng công:</strong> {{ count($tong_cong) }} buổi = {{ count($tong_cong)/2 }} ngày công.</span>&nbsp;&nbsp;|&nbsp;--}}
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
