@extends(config('core.admin_theme').'.template')
@section('main')
    <?php
    $status_text = [
        0 => 'Mới tạo',
        1 => 'Chờ xưởng duyệt',
        2 => 'Đang làm',
        3 => 'Hoàn thành',
        4 => 'Hủy',
    ];
    $services = [
        1 => 'Thiết kế Landing Page',
        2 => 'Hosting',
        3 => 'Tên miền',
        4 => 'Email business',
        5 => 'Wordpress',
    ];

    ?>
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
                            <h3 class="kt-portlet__head-title">Chỉnh
                                sửa {{ $module['label'] }} {{ @$services[$result->service_id] }}
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="/admin/{{ $module['code'] }}" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">Quay lại</span>
                            </a>
                            <div class="btn-group">
                                @if(in_array($module['code'].'_edit', $permissions))
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
                                                <a class="kt-nav__link save_option" data-action="save_continue">
                                                    <i class="kt-nav__link-icon flaticon2-reload"></i>
                                                    Lưu và tiếp tục
                                                </a>
                                            </li>
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
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Thông tin đơn hàng
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">
                                @foreach($module['form']['general_tab'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
                                    <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                         id="form-group-{{ $field['name'] }}">
                                        @if($field['type'] == 'custom')
                                            @include($field['field'], ['fields' => $field])
                                        @else
                                            <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                    <span class="color_btd">*</span>
                                                @endif</label>
                                            <div class="col-xs-12">
                                                @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                                <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Portlet-->

                {{--                {!! Eventy::filter('webbill.bill_info', @$result->id) !!}--}}
            </div>
            <div class="col-xs-12 col-md-4">

                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Thông tin khách
                            </h3>
                        </div>
                        <div class="kt-portlet__head-group pt-3">
                            <a title="Xem thêm" href="#" data-ktportlet-tool="toggle"
                               class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-angle-down"></i></a>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">
                                @foreach($module['form']['customer_tab'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
                                    <div style="padding: 0"
                                         class="form-group-div form-group {{ @$field['group_class'] }}"
                                         id="form-group-{{ $field['name'] }}">
                                        @if($field['type'] == 'custom')
                                            @include($field['field'], ['field' => $field])
                                        @else
                                            <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                    <span class="color_btd">*</span>
                                                @endif</label>
                                            <div class="col-xs-12">
                                                @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                                <span class="form-text text-muted">{!! @$field['des'] !!}</span>
                                                <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Portlet-->
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-8">
                <!--begin::Portlet-->
                @include('CRMWoo.form.fields.thong_tin_thanh_toan')


                <!--end::Portlet-->

                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Thông tin gia hạn
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">
                                @foreach($module['form']['gia_han_tab'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
                                    <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                         id="form-group-{{ $field['name'] }}">
                                        @if($field['type'] == 'custom')
                                            @include($field['field'], ['fields' => $field])
                                        @else
                                            <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                    <span class="color_btd">*</span>
                                                @endif</label>
                                            <div class="col-xs-12">
                                                @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                                <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                                <span class="form-text text-muted">{!! @$field['des'] !!}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
            </div>
            <div class="col-xs-12 col-md-4">
                @if(@$result->bill_parent == null)
                    <!-- Nếu là phụ lục HĐ thì không cho tạo phụ lục của phụ lục -->
                    @include('CRMWoo.form.fields.phu_luc_hd')
                @endif

                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Thông tin {{ trans('CRMWoo_admin.service') }}
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">
                                @foreach($module['form']['service_tab'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
                                    <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                         id="form-group-{{ $field['name'] }}">
                                        @if($field['type'] == 'custom')
                                            @include($field['field'], ['fields' => $field])
                                        @else
                                            <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                    <span class="color_btd">*</span>
                                                @endif</label>
                                            <div class="col-xs-12">
                                                @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                                <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($result->service_id == 1)
            <div class="row">
                <div class="col-xs-12 col-md-8">
                    <!--begin::Portlet-->
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    Thông tin bàn giao landingpage
                                </h3>
                            </div>
                        </div>
                        <!--begin::Form-->
                        <div class="kt-form">
                            <div class="kt-portlet__body">
                                <div class="kt-section kt-section--first">
                                    @foreach($module['form']['ldp_tab'] as $field)
                                        {{--@if($field['name'] == 'name')--}}

                                        {{--@endif--}}
                                        @php
                                            $field['value'] = @$result->{$field['name']};
                                        @endphp
                                        <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                             id="form-group-{{ $field['name'] }}">
                                            @if($field['type'] == 'custom')
                                                @include($field['field'], ['fields' => $field])
                                            @else
                                                <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                        <span class="color_btd">*</span>
                                                    @endif</label>
                                                <div class="col-xs-12">
                                                    @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                                    <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            @if(isset($result->handover_landingpage) && $result->handover_landingpage != 0)
                                <span style="color: green;">Đã bàn giao {{ $result->handover_landingpage }} lần</span>
                            @else
                                <span style="color: red;">Chưa bàn giao</span>
                            @endif
                            <!--end::Portlet-->
                            @if(!in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['customer', 'customer_ldp_vip']))
                                <a class="template-quick-view"
                                   style="cursor: pointer;     text-decoration: underline;color: #5867dd;"
                                   type="button">
                                <span>
                                    <i class="la la-plus"></i>
                                    <span>Xem trước email bàn giao</span>
                                </span>
                                </a>
                                <button class="btn btn btn-primary save_option"
                                        style="color: white; margin-top: 10px; cursor: pointer;"
                                        type="button" data-action="mail_ban_giao_ldp">
                                <span>
                                    <i class="la la-plus"></i>
                                    <span>Gửi mail Bàn giao</span>
                                </span>
                                </button>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <a href="/admin/landingpage/{{ @$result->ldp->id }}"
                               style="cursor: pointer;     text-decoration: underline;color: #5867dd; margin-top: 20px;">Xem
                                cấu hình landingpage</a>
                        </div>
                        <!--end::Form-->
                    </div>
                </div>
            </div>
        @elseif($result->service_id == 5)
            <div class="row">
                <div class="col-xs-12 col-md-8">
                    <!--begin::Portlet-->
                    <div class="kt-portlet">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    Thông tin bàn giao wordpress
                                </h3>
                            </div>
                        </div>
                        <!--begin::Form-->
                        <div class="kt-form">
                            <div class="kt-portlet__body">
                                <div class="kt-section kt-section--first">
                                    @foreach($module['form']['wp_tab'] as $field)
                                        {{--@if($field['name'] == 'name')--}}

                                        {{--@endif--}}
                                        @php
                                            $field['value'] = @$result->{$field['name']};
                                        @endphp
                                        <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                             id="form-group-{{ $field['name'] }}">
                                            @if($field['type'] == 'custom')
                                                @include($field['field'], ['fields' => $field])
                                            @else
                                                <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                        <span class="color_btd">*</span>
                                                    @endif</label>
                                                <div class="col-xs-12">
                                                    @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                                    <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            @if(isset($result->handover_wp) && $result->handover_wp != 0)
                                <span style="color: green;">Đã bàn giao {{ $result->handover_wp }} lần</span>
                            @else
                                <span style="color: red;">Chưa bàn giao</span>
                            @endif
                            <!--end::Portlet-->
                            @if(!in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['customer', 'customer_ldp_vip']))
                                <a class="template-quick-view"
                                   style="cursor: pointer;     text-decoration: underline;color: #5867dd;"
                                   type="button">
                                <span>
                                    <i class="la la-plus"></i>
                                    <span>Xem trước email bàn giao</span>
                                </span>
                                </a>
                                <button class="btn btn-primary save_option"
                                        style="color: white; margin-top: 10px; cursor: pointer;"
                                        type="button" data-action="mail_ban_giao_wp">
                                <span>
                                    <i class="la la-plus"></i>
                                    <span>Gửi mail Bàn giao</span>
                                </span>
                                </button>
                            @endif
                        </div>
                        <!--end::Form-->
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-xs-12 col-md-8">
                <div class="kt-portlet">
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="log_action">
                                <label>Lưu lịch sử chăm sóc</label>
                                <div class="form-group-div form-group col-md-12" style="margin-bottom: 10px;"
                                     id="form-group-name">
                                    <div class="col-xs-12">
                                        <input type="text" name="log_name" placeholder="Chủ đề"
                                               class="form-control required">
                                    </div>
                                </div>
                                <div class="form-group-div form-group col-md-12" id="form-group-name">
                                    <div class="col-xs-12">
                                        <textarea type="text" placeholder="Kết quả chăm sóc" name="log_note"
                                                  class="form-control required"></textarea>
                                    </div>
                                </div>
                                <!-- <div class="form-group-div form-group col-md-12" id="form-group-name">
                                    <button type="button" class="log_submit">Lưu lại</button>
                                </div> -->
                                <!-- <script type="">
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
                                                    type: 'hđ',
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
                                </script> -->
                            </div>

                            <div class="log_logs">
                                <?php
                                $logs = \App\CRMWoo\Models\LeadContactedLog::where('type', 'hđ')->where('lead_id', @$result->id)->orderBy('id', 'desc')->get();
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-4">
                @include('CRMWoo.bill.partials.dich_vu_lien_quan')
            </div>
        </div>
    </form>
    <div id="template-quick-view-content" style="display: none;">
        {!! @$email_content_ban_giao !!}
    </div>
@endsection
@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/form.css') }}">
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
    <script type="text/javascript" src="{{ asset(config('core.admin_asset').'/js/form.js') }}?v={{ time() }}"></script>


    <script>
        $('.template-quick-view').click(function () {
            $('#blank_modal .modal-body').html($('#template-quick-view-content').html());
            $('#blank_modal').modal();
        });
    </script>
@endsection
