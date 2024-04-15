{{--@extends('admin.themes.metronic1.template')--}}
@extends(config('core.admin_theme').'.backup.new_header.new_template')
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
                            <h3 class="kt-portlet__head-title">
                                {{ trans($module['label']) }}
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="/admin/dashboard" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">{{trans('admin.back')}}</span>
                            </a>
                            <div class="btn-group">
                                @if(in_array('setting', $permissions))
                                    <button type="submit" class="btn btn-brand">
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">{{trans('admin.save')}}</span>
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
                                                    {{trans('admin.save_and_continue')}}
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link save_option" data-action="save_exit">
                                                    <i class="kt-nav__link-icon flaticon2-power"></i>
                                                    {{trans('admin.save_and_quit')}}
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
            <div class="col-lg-12">
                <div class="kt-portlet">
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div class="kt-grid  kt-wizard-v2 kt-wizard-v2--white" id="kt_wizard_v2"
                             data-ktwizard-state="between">
                            <div class="kt-grid__item kt-wizard-v2__aside">
                                <!--begin: Form Wizard Nav -->
                                <div class="kt-wizard-v2__nav">
                                    <div class="kt-wizard-v2__nav-items">
                                        <!--doc: Replace A tag with SPAN tag to disable the step link click -->
                                        @foreach($module['tabs'] as $key => $tab)
                                            <div class="kt-wizard-v2__nav-item" data-ktwizard-type="step"
                                                 data-ktwizard-state="@if(!isset($active)) current @php $active = true; @endphp @else pending @endif">
                                                <div class="kt-wizard-v2__nav-body">
                                                    <div class="kt-wizard-v2__nav-icon">
                                                        {!! @$tab['icon'] !!}
                                                    </div>
                                                    <div class="kt-wizard-v2__nav-label">
                                                        <div class="kt-wizard-v2__nav-label-title">
                                                            {{trans(@$tab['label'])}}
                                                        </div>
                                                        <div class="kt-wizard-v2__nav-label-desc">
                                                            {{trans(@$tab['intro'])}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!--end: Form Wizard Nav -->
                            </div>
                            <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v2__wrapper">
                                <!--begin: Form Wizard Form-->
                                <div class="kt-form" id="kt_form" novalidate="novalidate" style="width: 100%">
                                @foreach($module['tabs'] as $key => $tab)
                                    <!--begin: Form Wizard Step 2-->
                                        <div class="kt-wizard-v2__content {{ $key }}" data-ktwizard-type="step-content"
                                             @if(!isset($active_div)) data-ktwizard-state="current" @php $active_div = true; @endphp @endif >
                                            <div class="kt-heading kt-heading--md">{{trans('admin.list')}} {{trans($tab['label'] )}}
                                                <a class="btn btn-success" style="position: absolute;right: 150px" href="{{URL::to('admin/'.$module['code'].'/'.$key)}}">
                                                    <i class="flaticon2-arrow"></i>{{trans('admin.run_now')}}
                                                </a>
                                            </div>
                                            @if(isset($tab['view']))
                                                @include($tab['view'])
                                            @endif
                                            <div class="kt-form__section kt-form__section--first">
                                                <div class="row">
                                                    <div class="kt-wizard-v2__form">
                                                        <div class="kt-heading kt-heading--md">{{trans('admin.configure_backup_schedule')}} {{trans($tab['label'] )}} {{trans('admin.auto')}}</div>
                                                        <div class="row">
                                                            @foreach($tab['td'] as $field)
                                                                <div class="col-xs-12 col-md-12 col-xl-6">
                                                                    <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                                                         id="form-group-{{ $field['name'] }}">
                                                                        <label for="{{ $field['name'] }}">{{ trans(@$field['label']) }} @if(strpos(@$field['class'], 'require') !== false)
                                                                                <span class="color_btd">*</span>@endif
                                                                        </label>
                                                                        <div class="col-xs-12 col-md-12">
                                                                            @php $field['value'] = @$tabs[$key][$field['name']]; @$field['name'] = $key.'_'.$field['name']; @endphp
                                                                            @if($field['type'] == 'custom')
                                                                                @include($field['field'], ['field' => $field])
                                                                            @else
                                                                                @include("admin.themes.metronic1.form.fields.".$field['type'], ['field' => $field])
                                                                                <span class="form-text text-muted">{!! @$field['des'] !!}</span>
                                                                                <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end: Form Wizard Step 2-->
                                    @endforeach
                                </div>
                                <!--end: Form Wizard Form-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--    {{dd($tabs['scan_error']['type_scan_error'])}}--}}
@endsection
@section('custom_head')
    <link href="{{ asset('backend/themes/metronic1/css/pages/wizard/wizard-2.css') }}"
          rel="stylesheet" type="text/css">
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset('backend/themes/metronic1/css/form.css') }}">
    <style>
        .kt-wizard-v2__form {
            width: 100%;
        }

    </style>
    <script>

        //  Ẩn hiện cấu hình mail
        function showMailChosse(option) {
            $('.mail-option').parents('.col-xs-12').hide();
            $('.' + option).parents('.col-xs-12').show();
        }

        //  Ẩn hiện cấu hình check error
        function showTypeCheckError(option) {
            $('.scan_option').parents('.col-xs-12').hide();
            $('.' + option).parents('.col-xs-12').show();
        }

        function showTimeCheckError(option, type) {
            $('select[name=scan_error_type_scan_error] option').each(function () {

                let search_string1 = $(this).text().search('lúc');

                if (search_string1 != -1 && type == 'time') {
                    $(this).text($(this).text().slice(0, search_string1) + ' lúc ' + option);
                }
            });
        }

        $(document).ready(function () {

            showMailChosse('{{ isset($tabs['mail']['driver']) ? $tabs['mail']['driver'] : 'smtp' }}');
            showTypeCheckError('{{ isset($tabs['scan_error']['type_scan_error']) ? $tabs['scan_error']['type_scan_error'] : '1' }}');

            $('select[name=mail_driver]').change(function () {
                var driver = $(this).val();
                showMailChosse(driver);
            });

            $('select[name=scan_error_type_scan_error]').change(function () {
                var driver = $(this).val();
                showTypeCheckError(driver);
            });

            $('input[name=scan_error_time_scan_error]').change(function () {
                var driver = $(this).val();
                showTimeCheckError(driver, 'time');
            });
            $('input[name=scan_error_day_in_monthly]').change(function () {
                var driver = $(this).val();
                showTimeCheckError(driver, 'month');
            });

            $('select[name=scan_error_day_in_weekly]').change(function () {
                var driver = $('select[name=scan_error_day_in_weekly] option:selected').text().toLowerCase();
                showTimeCheckError(driver, 'week');
            });
        });
    </script>
@endsection
@push('scripts')
    <script src="{{ asset('backend/themes/metronic1/js/pages/crud/file-upload/ktavatar.js') }}"></script>
    <script src="{{ asset('backend/themes/metronic1/js/pages/custom/wizard/wizard-2.js') }}"
            type="text/javascript"></script>

    <script src="{{asset('ckeditor/ckeditor.js') }}"></script>
    <script src="{{asset('ckfinder/ckfinder.js') }}"></script>
    <script src="{{asset('libs/file-manager.js') }}"></script>
    <script type="text/javascript" src="{{ asset('tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('tinymce/tinymce_editor.js') }}"></script>
    <script type="text/javascript">
        editor_config.selector = ".editor";
        editor_config.path_absolute = "{{ (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" }}/";
        tinymce.init(editor_config);
    </script>
    <script type="text/javascript" src="{{ asset('backend/themes/metronic1/js/form.js') }}"></script>
@endpush
