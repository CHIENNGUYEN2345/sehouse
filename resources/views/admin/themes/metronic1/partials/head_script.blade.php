<!--begin::Fonts -->
<link rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,600,700|Roboto:300,400,500,600,700">
<!--end::Fonts -->


<!--begin::Page Vendors Styles(used by this page) -->
<link href="{{ asset('backend/themes/metronic1/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}"
      rel="stylesheet" type="text/css"/>
<!--end::Page Vendors Styles -->


<!--begin::Global Theme Styles(used by all pages) -->
{{--<link href="{{ asset('backend/themes/metronic1/css/luan.css') }}" rel="stylesheet"--}}
{{--      type="text/css"/>--}}
<link href="{{ asset('backend/themes/metronic1/plugins/global/plugins.bundle.css') }}" rel="stylesheet"
      type="text/css"/>
<link href="{{ asset('backend/themes/metronic1/css/style.bundle.css') }}" rel="stylesheet" type="text/css"/>
<!--end::Global Theme Styles -->

<!--begin::Layout Skins(used by all pages) -->
<!-- <link href="{{ asset('backend/themes/metronic1/css/luan.css') }}" rel="stylesheet"
      type="text/css"/> -->
<link href="{{ asset('backend/themes/metronic1/css/skins/header/base/light.css') }}" rel="stylesheet"
      type="text/css"/>
<link href="{{ asset('backend/themes/metronic1/css/skins/header/menu/light.css') }}" rel="stylesheet"
      type="text/css"/>

<!-- <link href="{{ asset('backend/themes/metronic1/css/style_portlet.css') }}" rel="stylesheet"
      type="text/css"/> -->

<?php
$admin_theme_style = Cookie::get('admin_theme_style', 'dark');
?>
<link href="{{ asset('backend/themes/metronic1/css/skins/brand/'.$admin_theme_style.'.css') }}"
      rel="stylesheet" type="text/css"/>
<link href="{{ asset('backend/themes/metronic1/css/skins/aside/'.$admin_theme_style.'.css') }}"
      rel="stylesheet" type="text/css"/>
<!--end::Layout Skins -->

<!--begin::Layout Skins(used by all pages) -->

<!--end::Layout Skins -->

<link href="{{ asset('backend/css/pricing-1.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('libs/datetimepicker/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('backend/themes/metronic1/css/common.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('backend/css/custom.css') }}" rel="stylesheet" type="text/css"/>
<script src="{{ asset('libs/jquery-3.4.0.min.js') }}"></script>
<script src="{{ asset('libs/jquery.validate.min.js') }}"></script>

{!! Eventy::filter('head_script.script', '') !!}
@yield('custom_head')

{!! @$settings['admin_head_code'] !!}

