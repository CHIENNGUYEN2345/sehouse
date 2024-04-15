@extends(config('core.admin_theme').'.template')
@section('main')
    <form class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid {{ @$module['code'] }}"
          action="" method="POST"
          enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="return_direct" value="save_continue" type="hidden">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile"
                     id="kt_page_portlet">
                    <div class="kt-portlet__head kt-portlet__head--lg" style="">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">Thông tin khách: {{ $result->name }}
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="{{ isset($_SERVER['HTTP_REFERER']) ? (strpos($_SERVER['HTTP_REFERER'],"/edit") ? '/admin/' . $module['code'] : $_SERVER['HTTP_REFERER']) : '/admin/' . $module['code'] }}"
                               class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">Quay lại</span>
                            </a>

                            <div class="btn-group">
                                @if(in_array('lead_edit', $permissions))
                                    @if (\Auth::guard('admin')->user()->super_admin != 1 && in_array($result->status, ['Đang chăm sóc', 'Tạm dừng', 'Đã ký HĐ']) && strpos($result->saler_ids, '|'.\Auth::guard('admin')->user()->id.'|') === false)
                                        <!-- Nếu ko phải super_admin & trạng thái đang chăm sóc & không phải của mình là sale thì không được sửa -->

                                    @else
                                        <button type="submit" class="btn btn-brand">
                                            <i class="la la-check"></i>
                                            <span class="kt-hidden-mobile">Lưu</span>
                                        </button>
                                        <button type="button"
                                                class="btn btn-brand dropdown-toggle dropdown-toggle-split"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <ul class="kt-nav">
                                                <li class="kt-nav__item">
                                                    <a class="kt-nav__link save_option" data-action="save_exit">
                                                        <i class="kt-nav__link-icon flaticon2-power"></i>
                                                        Lưu & Thoát
                                                    </a>
                                                </li>
                                                <li class="kt-nav__item">
                                                    <a class="kt-nav__link save_option" data-action="save_create">
                                                        <i class="kt-nav__link-icon flaticon2-add-1"></i>
                                                        Lưu và tạo mới
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Portlet-->
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-8">
                <!--begin::Portlet-->
                <div class="kt-portlet">

                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first" style="margin: 0;">
                                @foreach($module['form']['general_tab'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
                                    @if($field['type'] == 'custom')
                                        @include($field['field'], ['field' => $field])
                                    @else
                                        <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                             id="form-group-{{ $field['name'] }}">
                                            <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                    <span class="color_btd">*</span>
                                                @endif</label>
                                            <div class="col-xs-12">
                                                @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                                <span class="form-text text-muted">{!! @$field['des'] !!}</span>
                                                <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Portlet-->

                <div class="kt-portlet">

                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">

                            <span class="text-danger">Quá {{ env('LEAD_MAX_DATE') }} ngày không cập nhật lịch sử tư vấn thì hệ thống tự động thu hồi đầu mối và chuyển cho sale khác</span>
                            <div class="log_action">
                                <label>Lưu lịch sử tư vấn</label>
                                <div class="form-group-div form-group col-md-12" style="margin-bottom: 10px;"
                                     id="form-group-name">
                                    <div class="col-xs-12">
                                        <input type="text" name="log_name" placeholder="Chủ đề"
                                               class="form-control required">
                                    </div>
                                </div>
                                <div class="form-group-div form-group col-md-12" id="form-group-name">
                                    <div class="col-xs-12">
                                        <textarea type="text" placeholder="Nội dung tư vấn" name="log_note"
                                                  class="form-control required"></textarea>
                                    </div>
                                </div>
                                <div class="form-group-div form-group col-md-12" id="form-group-name">
                                    @if($result->status == 'Thả nổi')
                                        <p style="color: red; font-weight: bold;">Nhớ chuyển trạng thái sang "Đang chăm
                                            sóc"</p>
                                    @endif
                                    <button type="button" class="log_submit">Lưu lại</button>
                                </div>
                                <script type="">
                                    $('.log_action .log_submit').click(function() {
                                        if ($('textarea[name=log_note]').val() == '') {
                                            alert('Không được để trống Nội dung tư vấn');
                                        } else {
                                            $.ajax({
                                                url: '/admin/lead/lead-contacted-log',
                                                type: 'POST',
                                                data: {
                                                    title: $('input[name=log_name]').val(),
                                                    note: $('textarea[name=log_note]').val(),
                                                    lead_id: '{{ @$result->id }}',
                                                    type: 'lead',
                                                },
                                                success: function() {
                                                    location.reload();
                                                    // window.location.href = "/admin/lead";
                                                },
                                                error: function() {
                                                    alert('Có lỗi xảy ra. Vui lòng load lại trang và thử lại!');
                                                }
                                            });
                                        }
                                    });


                                </script>
                            </div>

                            <div class="log_logs">
                                <?php
                                $logs = \App\CRMEdu\Models\LeadContactedLog::where('type', 'lead')->where('lead_id', @$result->id)->orderBy('id', 'desc')->get();
                                ?>
                                @foreach($logs as $log)
                                    <hr>
                                    <div class="log-item" data-id="{{ $log->id }}" style="color: #000;">
                                        <i></i>
                                        <div class="log-content">
                                            <span><strong>{{ $log->title }}</strong></span>
                                            <p style="font-size: 13px; margin: 0;">{!! $log->note !!}</p>
                                        </div>
                                        <i style="font-size: 11px;">{{ date('H:i d/m/Y', strtotime($log->created_at)) }}
                                            - Bởi: {{ @$log->admin->name }}</i>
                                    </div>
                                @endforeach
                                <hr>
                                <div class="log-item" data-id="" style="color: #000;">
                                    <i></i>
                                    <div class="log-content">
                                        <p style="font-size: 13px; margin: 0;">Tạo mới</p>
                                    </div>
                                    <i style="font-size: 11px;">{{ date('H:i d/m/Y', strtotime($result->created_at)) }}
                                        - Bởi: {{ @$result->admin->name }}</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-4">
                <!--begin::Portlet-->
                <div class="kt-portlet">

                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">
                                @foreach($module['form']['tab_2'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
                                    @if($field['type'] == 'custom')
                                        @include($field['field'], ['field' => $field])
                                    @else
                                        <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                             id="form-group-{{ $field['name'] }}">
                                            <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                    <span class="color_btd">*</span>
                                                @endif</label>
                                            <div class="col-xs-12">
                                                @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                                <span class="form-text text-muted">{!! @$field['des'] !!}</span>
                                                <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            @if(in_array('lead_assign', $permissions))
                                    <?php
                                    $field = ['name' => 'saler_ids', 'type' => 'custom', 'field' => 'CRMEdu.lead.form.saler_ids', 'label' => 'Sale phụ trách', 'model' => \App\Models\Admin::class, 'object' => 'admin', 'display_field' => 'name', 'multiple' => true, 'group_class' => 'col-md-12'];
                                    $field['value'] = @$result->{$field['name']};
                                    ?>
                                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                     id="form-group-{{ $field['name'] }}">
                                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                            <span class="color_btd">*</span>
                                        @endif</label>
                                    <div class="col-xs-12">
                                        @include('CRMEdu.lead.form.saler_ids', ['field' => $field])
                                    </div>
                                </div>
                            @endif

                            @if(\Auth::guard('admin')->user()->super_admin == 1)

                                    <?php
                                    $field = ['name' => 'marketer_ids', 'type' => 'select2_ajax_model', 'label' => 'Người giới thiệu', 'model' => \App\Models\Admin::class, 'object' => 'admin', 'display_field' => 'name', 'display_field2' => 'tel', 'multiple' => true, 'group_class' => 'col-md-12'];
                                    $field['value'] = @$result->{$field['name']};
                                    ?>
                                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                     id="form-group-{{ $field['name'] }}">
                                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                            <span class="color_btd">*</span>
                                        @endif</label>
                                    <div class="col-xs-12">
                                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!--end::Form-->
                </div>

                <div class="kt-portlet">

                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <label>Lưu sử hoạt động</label>
                            <div class="log_logs">
                                <hr>
                                <div class="log-item" data-id="1179" style="color: #000;">
                                    <i></i>
                                    <div class="log-content">
                                        <span><strong></strong></span>
                                        <p style="font-size: 13px; margin: 0;">Chuyển trạng thái: thả nối -> đang chăm
                                            sóc</p>
                                    </div>
                                    <i style="font-size: 11px;">16:00 08/04/2022 - Bởi: Thanh Bình</i>
                                </div>
                                <hr>
                                <div class="log-item" data-id="" style="color: #000;">
                                    <i></i>
                                    <div class="log-content">
                                        <p style="font-size: 13px; margin: 0;">Chuyển đánh giá: Tìm hiểu -> Quan tâm
                                            cao</p>
                                    </div>
                                    <i style="font-size: 11px;">15:59 08/04/2022 - Bởi: Thanh Bình</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Portlet-->
            </div>


            <div>

                <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper"
                     style="position: relative; padding: 0">
                    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

                        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                            <div class="kt-portlet kt-portlet--mobile">
                                <div class="kt-portlet__head kt-portlet__head--lg">
                                    <div class="kt-portlet__head-toolbar">
                                        <div class="kt-portlet__head-wrapper">
                                            <div class="">
                                                <input type="text" name="quick_search" value="" class="form-control"
                                                       title="Chỉ cần enter để thực hiện tìm kiếm"
                                                       placeholder="Tìm kiếm nhanh theo ID, giá trị">
                                            </div>
                                            <div class="kt-portlet__head-actions">
                                                <div class="dropdown dropdown-inline">
                                                    <button type="button"
                                                            class="btn btn-default btn-icon-sm dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                        <i class="la la-download"></i> Hành động
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                         x-placement="bottom-end"
                                                         style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(114px, 38px, 0px);">
                                                        <ul class="kt-nav">
                                                            <li class="kt-nav__section kt-nav__section--first">
                                                                <span class="kt-nav__section-text">Chọn hành động</span>
                                                            </li>
                                                            <li class="kt-nav__item">
                                                                <a class="kt-nav__link export-excel"
                                                                   title="Xuất các bản ghi đang lọc ra file excel"
                                                                   onclick="$('input[name=export]').click();">
                                                                    <i class="kt-nav__link-icon la la-file-excel-o"></i>
                                                                    <span class="kt-nav__link-text">Xuất Excel</span>
                                                                </a>
                                                            </li>
                                                            <li class="kt-nav__item">
                                                                <a href="#" class="kt-nav__link"
                                                                   onclick="multiDelete();"
                                                                   title="Xóa tất cả các dòng đang được tích chọn">
                                                                    <i class="kt-nav__link-icon la la-copy"></i>
                                                                    <span class="kt-nav__link-text">Xóa nhiều</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                &nbsp;
                                                <a href="https://hbsoft.top/admin/bill_histories/add?bill_id=15"
                                                   class="btn btn-brand btn-elevate btn-icon-sm">
                                                    <i class="la la-plus"></i>
                                                    Tạo mới
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-portlet__body kt-portlet-search  no-padding ">

                                    <form class="kt-form kt-form--fit kt-margin-b-20 form-search" id="form-search"
                                          method="GET" action="" style="display: none;">
                                        <input name="search" type="hidden" value="true">
                                        <input name="limit" type="hidden" value="20"><input type="hidden"
                                                                                            name="quick_search" value=""
                                                                                            id="quick_search_hidden"
                                                                                            class="form-control"
                                                                                            placeholder="Tìm kiếm nhanh theo ID, giá trị"><input
                                                type="hidden" name="quick_search" value="" id="quick_search_hidden"
                                                class="form-control" placeholder="Tìm kiếm nhanh theo ID, giá trị">
                                        <div class="row">
                                            <div class="col-sm-6 col-lg-3 kt-margin-b-10-tablet-and-mobile list-filter-item">
                                                <label>:</label>
                                                <input type="text" name="bill_id" placeholder="" value="15"
                                                       class="form-control kt-input hidden"></div>
                                        </div>
                                        <div class="row">
                                            <label>Trao đổi quản lý</label>
                                            <div class="col-lg-12">

                                                <button class="btn btn-primary btn-brand--icon" id="kt_search"
                                                        type="submit">

<span>
<i class="la la-search"></i>
<span>Lọc</span>
</span>
                                                </button>
                                                &nbsp;&nbsp;
                                                <a class="btn btn-secondary btn-secondary--icon" id="kt_reset"
                                                   title="Xóa bỏ bộ lọc" href="/admin/bill_histories?bill_id=15">
<span>
<i class="la la-close"></i>
<span>Reset</span>
</span>
                                                </a>
                                            </div>
                                        </div>
                                        <input name="export" type="submit" value="export" style="display: none;">
                                        <input name="sorts[]" value="" class="sort sort-expiry_date" type="hidden">
                                        <input name="sorts[]" value="" class="sort sort-price" type="hidden">
                                        <input name="sorts[]" value="" class="sort sort-note" type="hidden">
                                    </form>

                                </div>
                                <div class="kt-separator kt-separator--md kt-separator--dashed"
                                     style="margin: 0;"></div>
                                <div class="kt-portlet__body kt-portlet__body--fit">

                                    <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded"
                                         id="scrolling_vertical" style="">
                                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                                            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                                        </div>
                                        <div class="ps__rail-y" style="top: 0px; height: 496px; right: 0px;">
                                            <div class="ps__thumb-y" tabindex="0"
                                                 style="top: 0px; height: 207px;"></div>
                                        </div>
                                        <table class="table table-striped">
                                            <thead class="kt-datatable__head">
                                            <tr class="kt-datatable__row" style="left: 0px;">
                                                <th style="display: none;"></th>
                                                <th data-field="id"
                                                    class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check">
                                                    <span style="width: 20px;"><label
                                                                class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input
                                                                    type="checkbox"
                                                                    class="checkbox-master">&nbsp;<span></span></label></span>
                                                </th>
                                                <th data-field="expiry_date"
                                                    class="kt-datatable__cell kt-datatable__cell--sort "
                                                    onclick="sort('expiry_date')">
                                                    Nội dung
                                                    <i class="flaticon2-arrow-down"></i>
                                                </th>
                                                <th data-field="price"
                                                    class="kt-datatable__cell kt-datatable__cell--sort "
                                                    onclick="sort('price')">
                                                    Hành động
                                                    <i class="flaticon2-arrow-down"></i>
                                                </th>
                                                <th data-field="note"
                                                    class="kt-datatable__cell kt-datatable__cell--sort "
                                                    onclick="sort('note')">

                                                    <i class="flaticon2-arrow-down"></i>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody class="kt-datatable__body ps ps--active-y"
                                                   style="max-height: 496px;">
                                            <tr data-row="0" class="kt-datatable__row" style="left: 0px;">
                                                <td style="display: none;" class="id id-14">14</td>
                                                <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check"
                                                    data-field="ID"><span style="width: 20px;"><label
                                                                class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input
                                                                    name="id[]" type="checkbox" class="ids" value="14">&nbsp;<span></span></label></span>
                                                </td>
                                                <td data-field="expiry_date"
                                                    class="kt-datatable__cell item-expiry_date">
                                                    Đơn này được sửa chưa?<br>09:29 10/04/2022 - Bởi: Đỗ Xuân Bách
                                                </td>
                                                <td data-field="price" class="kt-datatable__cell item-price">
                                                    <span style="    width: 100%;display: inline-block;text-align: right;">Sửa | Xóa</span>
                                                </td>
                                                <td data-field="note" class="kt-datatable__cell item-note">
                                                </td>
                                            </tr>


                                            </tbody>
                                        </table>
                                        <div class="kt-datatable__pager kt-datatable--paging-loaded">
                                            <ul class="pagination page-numbers nav-pagination links text-center"></ul>
                                            <div class="kt-datatable__pager-info">
                                                <div class="dropdown bootstrap-select kt-datatable__pager-size"
                                                     style="width: 60px;">
                                                    <div class="dropdown bootstrap-select kt-datatable__pager-size select-page-size"
                                                         style="width: 60px;"><select
                                                                class="selectpicker kt-datatable__pager-size select-page-size"
                                                                onchange="$('input[name=limit]').val($(this).val());$('#form-search').submit();"
                                                                title="Chọn số bản ghi hiển thị" data-width="60px"
                                                                data-selected="20" tabindex="-98">
                                                            <option class="bs-title-option" value=""></option>
                                                            <option value="20" selected="">20</option>
                                                            <option value="30">30</option>
                                                            <option value="50">50</option>
                                                            <option value="100">100</option>
                                                        </select>
                                                        <button type="button" class="btn dropdown-toggle btn-light"
                                                                data-toggle="dropdown" role="combobox"
                                                                aria-owns="bs-select-1" aria-haspopup="listbox"
                                                                aria-expanded="false" title="20">
                                                            <div class="filter-option">
                                                                <div class="filter-option-inner">
                                                                    <div class="filter-option-inner-inner">20</div>
                                                                </div>
                                                            </div>
                                                        </button>
                                                        <div class="dropdown-menu ">
                                                            <div class="inner show" role="listbox" id="bs-select-1"
                                                                 tabindex="-1">
                                                                <ul class="dropdown-menu inner show"
                                                                    role="presentation"></ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="kt-datatable__pager-detail">Hiển thị 1 - 1 của 1</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </form>
@endsection
@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/form.css') }}">
    <script src="{{asset('ckeditor/ckeditor.js') }}"></script>
    <script src="{{asset('ckfinder/ckfinder.js') }}"></script>
    <script src="{{asset('libs/file-manager.js') }}"></script>
    <style type="">
        .form-group-div p {
            font-size: 13px;
        }
    </style>
    <style type="text/css">
        @media (min-width: 768px) {
            div#form-group-address {
                height: 123px;
            }

        }
    </style>
@endsection

@section('custom_footer')
    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
            type="text/javascript"></script>

    <script type="text/javascript" src="{{ asset('tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('tinymce/tinymce_editor.js') }}"></script>
    <script type="text/javascript">
        editor_config.selector = ".editor";
        editor_config.path_absolute = "{{ (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" }}/";
        tinymce.init(editor_config);
    </script>
    <script type="text/javascript" src="{{ asset(config('core.admin_asset').'/js/form.js') }}"></script>
@endsection
