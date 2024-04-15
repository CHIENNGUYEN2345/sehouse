@extends(config('core.admin_theme').'.template')
@section('main')
    <form class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid {{ @$module['code'] }}"
          action="" method="POST"
          enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="return_direct" value="save_exit" type="hidden">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile"
                     id="kt_page_portlet">
                    <div class="kt-portlet__head kt-portlet__head--lg" style="">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">{{trans('admin.add')}} {{ trans($module['label']) }}
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="/admin/{{ $module['code'] }}" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">{{trans('admin.back')}}</span>
                            </a>
                            <div class="btn-group">
{{--                                @if(in_array($module['code'].'_add', $permissions))--}}
                                    <button type="submit" class="btn btn-brand">
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">{{trans('admin.save')}}</span>
                                    </button>
{{--                                @endif--}}
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Portlet-->
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-4">
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Hướng dẫn
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">
                                <div class="flexbox-annotated-section-annotation">
                                    <div class="annotated-section-description pd-all-20 p-none-t">
                                        <p class="color-note">
                                            Email sẽ sử dụng HTML và các biến hệ thống!.
                                        </p>
                                        <div class="available-variable">
                                            <p><code>header</code>: Tiêu đề header Email</p>
                                            <p><code>footer</code>: Tiêu đề footer Email</p>
                                            <p><code>site_title</code>: Tiêu đề website</p>
                                            <p><code>site_url</code>: Url website</p>
                                            <p><code>site_logo</code>: Logo website</p>
                                            <p><code>date_time</code>: Ngày, tháng hiện tại</p>
                                            <p><code>date_year</code>: Năm hiện tại</p>
                                            <p><code>site_admin_email</code>: Email quản trị</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Portlet-->
            </div>
            <div class="col-xs-12 col-md-8">
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                               Nội dung
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body" style="padding-top: 0;">
                            <div class="kt-section kt-section--first">
                                <div class="flexbox-annotated-section-annotation">
                                    <textarea id="header_mail" name="header_mail">{{ @\App\Models\Setting::where('name', 'header_mail')->where('type', 'mail')->first()->value }}</textarea>
                                </div>
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
    <link rel="stylesheet" href="{{asset('libs/codemirror/css/codemirror.css')}}">
@endsection
@section('custom_footer')
    <script src="{{asset('libs/codemirror/js/codemirror.js')}}"></script>
    <script src="{{asset('libs/codemirror/js/xml.js')}}"></script>
    <script type="text/javascript" src="{{ asset(config('core.admin_asset').'/js/form.js') }}"></script>
    <script>
        CodeMirror.fromTextArea(document.getElementById("header_mail"), {
            lineNumbers: true,
            mode: "text/html",
            matchBrackets: true
        });
    </script>
@endsection