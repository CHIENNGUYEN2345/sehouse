<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.themes.metronic1.partials.head_meta')
    @include('admin.themes.metronic1.partials.head_script')
</head>
<!-- end::Head -->

<!-- begin::Body -->
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">
<!-- begin:: Page -->

<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
        <!-- begin:: Aside -->
        <!-- end:: Aside -->
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper" style="position: relative; padding: 0">
            <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
                <!-- begin:: Content -->
                @yield('main')
                <!-- end:: Content -->
            </div>
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
