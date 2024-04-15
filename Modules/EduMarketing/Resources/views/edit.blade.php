@extends(config('core.admin_theme').'.template')
@section('main')
    <?php
    if (!isset($_GET['marketing_mail_id']) && isset($result)) {
        $_GET['marketing_mail_id'] = $result->id;
        $course = $result;
    } else {
        $course = \Modules\EduMarketing\Models\MaketingMail::find($_GET['marketing_mail_id']);
    }
    ?>
    <form class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid {{ @$module['code'] }}"
          action="" method="POST"
          enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="return_direct" value="save_continue" type="hidden">
        <input name="marketing_mail_id" value="{{ @$_GET['marketing_mail_id'] }}" type="hidden">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile"
                     id="kt_page_portlet">
                    <div class="kt-portlet__head kt-portlet__head--lg" style="">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">Chỉnh sửa {{ $module['label'] }}
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="/admin/{{ $module['code'] }}" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">Quay lại</span>
                            </a>
                            <a href="/admin/marketing-mail-log?marketing_mail_id={{ @$_GET['marketing_mail_id']}}"
                               class="btn btn-clean kt-margin-r-10">
                                <i class="la la-book"></i>
                                <span class="kt-hidden-mobile">Lịch sử gửi mail</span>
                            </a>
                            <a class="btn btn-clean kt-margin-r-10 save_preview" data-action="save_preview">
                                <i class="la la-book"></i>
                                <span class="kt-hidden-mobile" style="cursor: pointer;">Lưu & Xem trước</span>
                            </a>
                            <a class="btn btn-clean kt-margin-r-10 save_preview" id="send-now"
                               data-action="save_send_now">
                                <i class="la la-book"></i>
                                <span class="kt-hidden-mobile" style="cursor: pointer;">Gửi ngay</span>
                            </a>
                            <div class="btn-group">
                                @if(in_array('marketing-mail_view', $permissions))
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
            <div class="col-xs-12 col-md-7">
                <!--begin::Portlet-->
                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Đối tượng nhận email
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
                                @foreach($module['form']['general_tab'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
                                    <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                         id="form-group-{{ $field['name'] }}">
                                        @if($field['type'] == 'custom')
                                            @include($field['field'], ['field' => $field])
                                        @else
                                            <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                    <span class="color_btd">*</span>@endif</label>
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

            <div class="col-xs-12 col-md-5">
                <!--begin::Portlet-->
                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Lịch gửi mail
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
                                @foreach($module['form']['time_tab'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
                                    <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                         id="form-group-{{ $field['name'] }}">
                                        @if($field['type'] == 'custom')
                                            @include($field['field'], ['field' => $field])
                                        @else
                                            <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                    <span class="color_btd">*</span>@endif</label>
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

                <!--begin::Portlet-->
                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Thống kê gửi
                            </h3>
                        </div>
                        <div class="kt-portlet__head-group pt-3">
                            <a title="Xem thêm" href="#" data-ktportlet-tool="toggle"
                               class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-angle-down"></i></a>
                        </div>
                    </div>
                @if(isset($result))
                    <!--begin::Form-->
                        <div class="kt-form">
                            <div class="kt-portlet__body">
                                <div class="kt-section kt-section--first">
                                    <div class="kt-widget12__item thong_ke_so">
                                        <div class="col-sm-3 kt-widget12__info">
                                            <span class="kt-widget12__desc">Tổng cần gửi:</span>
                                            <span class="kt-widget12__value"><strong>{!! \Modules\EduMarketing\Models\MarketingMailLog::where('marketing_mail_id', $result->id)->count() !!}</strong></span>
                                        </div>

                                        <div class="col-sm-3 kt-widget12__info">
                                            <span class="kt-widget12__desc">Đã gửi:</span>
                                            <span class="kt-widget12__value"><strong>{!! \Modules\EduMarketing\Models\MarketingMailLog::where('marketing_mail_id', $result->id)->where('sent', 1)->count() !!}</strong></span>
                                        </div>

                                        <div class="col-sm-3 kt-widget12__info">
                                            <span class="kt-widget12__desc">Khách đã xem:</span>
                                            <span class="kt-widget12__value"><strong>{!! \Modules\EduMarketing\Models\MarketingMailLog::where('marketing_mail_id', $result->id)->where('opened', 1)->count() !!}</strong></span>
                                        </div>

                                        <div class="col-sm-3 kt-widget12__info">
                                            <span class="kt-widget12__desc">Chờ gửi:</span>
                                            <span class="kt-widget12__value"><strong>{!! \Modules\EduMarketing\Models\MarketingMailLog::where('marketing_mail_id', $result->id)->where('sent', 0)->where('error', 1)->count() !!}</strong></span>
                                        </div>

                                        <div class="col-sm-3 kt-widget12__info">
                                            <span class="kt-widget12__desc">Bị lỗi:</span>
                                            <span class="kt-widget12__value"><strong>{!! \Modules\EduMarketing\Models\MarketingMailLog::where('marketing_mail_id', $result->id)->where('error', 0)->count() !!}</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Form-->
                    @endif
                </div>
                <!--end::Portlet-->
            </div>

            <div class="col-xs-12 col-md-12">
                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Nội dung email
                            </h3>
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


                                @foreach($module['form']['info_tab'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
                                    <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                         id="form-group-{{ $field['name'] }}">
                                        @if($field['type'] == 'custom')
                                            @include($field['field'], ['field' => $field])
                                        @else
                                            <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                    <span class="color_btd">*</span>@endif</label>
                                            <div class="col-xs-12">
                                                @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                                <span class="form-text text-muted">{!! @$field['des'] !!}</span>
                                                <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                <p class="pl-3">{ten}:Tên người nhận</p>
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Portlet-->
            </div>
        </div>

    </form>
@endsection
@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/form.css') }}">
    <script src="{{asset('public/ckeditor/ckeditor.js') }}"></script>
    <script src="{{asset('public/ckfinder/ckfinder.js') }}"></script>
    <script src="{{asset('public/libs/file-manager.js') }}"></script>

@endsection
@section('custom_footer')
    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
            type="text/javascript"></script>


    <script src="{{asset('public/ckeditor/ckeditor.js') }}"></script>
    <script src="{{asset('public/ckfinder/ckfinder.js') }}"></script>
    <script src="{{asset('public/libs/file-manager.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/tinymce/tinymce_editor.js') }}"></script>
    <script type="text/javascript">
        editor_config.selector = ".editor";
        editor_config.path_absolute = "{{ (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" }}/";
        tinymce.init(editor_config);
    </script>
    <script type="text/javascript" src="{{ asset(config('core.admin_asset').'/js/form.js') }}"></script>


    <script>
        $('.save_preview').click(function () {
            var action = $(this).data('action');
            $('input[name=return_direct]').val(action);
            $('form.kt-container').submit();
        });
    </script>
@endsection
@push('scripts')
    <script>
        $(document)
        $('select[name=email_template_id]').change(function () {
            var email_template_id = $(this).val();
            $.ajax({
                url : '/admin/email_template/' + email_template_id + '/ajax-get-info',
                success: function (resp) {
                    if (resp.status) {
                        CKEDITOR.instances.ck_content.setData(resp.data.content);
                        // CKEDITOR.instances.ck_content.insertHtml(resp.data.content);
                    } else {
                        alert(resp.msg);
                    }
                },
                error: function () {
                    alert('Có lỗi xảy ra khi load email template! Vui lòng lại lại website và thử lại');
                }
            });
        });
    </script>
@endpush