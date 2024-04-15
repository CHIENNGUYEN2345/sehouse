<!DOCTYPE html>

<html lang="en">

<!-- begin::Head -->
<head>
    <title>Không có quyền truy cập</title>
    @include(config('core.admin_theme') . '.partials.head_meta')

    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,600,700|Roboto:300,400,500,600,700">

    <!--begin::Global Theme Styles -->
    <link href="{{asset('/public/backend/themes/metronic1/css/error/style.bundle.css')}}" rel="stylesheet"
          type="text/css"/>

</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

<!-- begin:: Page -->
<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid  m-error-1" style="background-image: url(/public/error/bg1.jpg);">
        <div class="m-error_container">
					<span class="m-error_number">
						<h1>403</h1>
					</span>
            <p class="m-error_desc">
                Bạn không có quyền truy cập trang này!
            </p>
            <button class="btn btn-outline-success" style="margin-left: 6%" onclick="window.history.back();">Quay lại</button>
        </div>
    </div>
</div>

<!-- end:: Page -->
</body>

<!-- end::Body -->
</html>