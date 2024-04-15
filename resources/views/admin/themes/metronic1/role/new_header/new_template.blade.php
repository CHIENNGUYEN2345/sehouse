<!DOCTYPE html>
<html lang="vi">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('admin.themes.metronic1.partials.head_meta')
    @include('admin.themes.metronic1.partials.head_script')
</head>
<!-- end::Head -->

<style>
    .kt-header__topbar-item kt-header__topbar-item--user item-header logo-mobile {
        display: none;
    }
    @media (max-width: 1024px){

        .kt-header__topbar-item.kt-header__topbar-item--user.item-header.logo-desktop {
            display: none !important;
        }
        .kt-header__topbar-item kt-header__topbar-item--user item-header logo-mobile {
            display: block !important;
        }
        .kt-header__topbar-item.kt-header__topbar-item--user.item-header.logo-mobile {
            max-width: 45px;
            margin-top: 5.5px;
        }
        img.lazy {
            max-width: 45px;
            max-height: 40px;
            border-radius: 5px;
        }
    }
</style>

<!-- begin::Body -->
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

<!-- begin:: Page -->

<!-- begin:: Header Mobile -->
{{--@include('CRMDV.codes.new_header.new_header_mobile')--}}
@include(config('core.admin_theme').'.role.new_header.new_header_mobile')
<!-- end:: Header Mobile -->

<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
        <!-- begin:: Aside -->
        <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>

        <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop"
             id="kt_aside">
            <!-- begin:: Aside -->
            @include('admin.themes.metronic1.partials.aside')
            <!-- end:: Aside -->
            <!-- begin:: Aside Menu -->
            @include('admin.themes.metronic1.partials.aside_menu')
            <!-- end:: Aside Menu -->
        </div>
        <!-- end:: Aside -->
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper"
             style="position: relative">
            {{--@include('admin.themes.metronic1.partials.check_exp_date')--}}

            <!-- begin:: Header -->
            <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">

                {{--                    <div class="page-title h3 px-3">--}}
                {{--                        {{ isset($page_title) ? trans(@$page_title) : '' }}--}}
                {{--                    </div>--}}
                {{--                    --}}
{{--                @include('CRMDV.codes.new_header.new_nv_xuat_sac')--}}
                @include(config('core.admin_theme').'.role.new_header.new_nv_xuat_sac')
                <!-- begin: Header Menu -->
                @include('admin.themes.metronic1.partials.header_menu')
                <!-- end: Header Menu -->

                <!-- begin:: Header Topbar -->
                <div class="kt-header__topbar" style="position: static;">
                    <!--begin: BillPayment Bar -->
{{--                    @include('CRMDV.codes.new_header.new_user_bar')--}}
                    @include(config('core.admin_theme').'.role.new_header.new_user_bar')
                    <!--end: BillPayment Bar -->
                </div>
                <!-- end:: Header Topbar -->
            </div>
            <!-- end:: Header -->

            <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
                {!! Eventy::filter('block.main_before', '') !!}
                <!-- begin:: Content -->
                @yield('main')
                <!-- end:: Content -->
            </div>

            <!-- begin:: Footer -->
{{--            @include('admin.themes.metronic1.partials.footer')--}}
            <!-- end:: Footer -->
        </div>
    </div>
</div>
<!-- end:: Page -->

@include('admin.themes.metronic1.modal.blank_modal')
@include('admin.themes.metronic1.modal.delete_warning_modal')
@include('admin.themes.metronic1.modal.confirm_action_modal')
@include('admin.themes.metronic1.modal.something_went_wrong')

@include('admin.themes.metronic1.partials.footer_script')

</body>
<!-- end::Body -->
</html>
