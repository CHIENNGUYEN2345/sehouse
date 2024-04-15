<!DOCTYPE html>
<html lang="en">

<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title>{{@$settings['name']}} | {{$page_title}}</title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <link rel="stylesheet" href="{{asset('libs/bootstrap/css/bootstrap.min.css')}}">

    <script>
        WebFont.load({
            google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!--end::Web font -->

    <link rel="stylesheet" href="{{asset('login/css/vendors.bundle.css')}}">
    <link rel="stylesheet" href="{{asset('login/css/vendors.bundle.rtl.css')}}">
    <link rel="stylesheet" href="{{asset('login/css/style.bundle.css')}}">


    <!--end::Global Theme Styles -->
    <link rel="shortcut icon" href="">
</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default"
        style="background-image: url(/login/images/bg-1.jpg) !important;">

<!--Preloader-->
<style>
    .sp-header {
        display: none !important;

    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4" style="padding-top: 70px;">
            <div class="wrapper pa-0" style="border: 1px solid #ddd; line-height: 25px; padding: 20px">
                <div class="notification hidden">
                </div>
{{--                <header class="sp-header">--}}
{{--                    <div class="sp-logo-wrap pull-left">--}}
{{--                        <a href="/">--}}
{{--                            <img class="brand-img mr-10" style="width: 50px"--}}
{{--                                 src="/filemanager/userfiles/eworking_logo.png" alt="brand">--}}
{{--                            <b class="brand-text"></b>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                </header>--}}

                <!-- Main Content -->
                <div class="page-wrapper pa-0 ma-0 auth-page">
                    <div class="container-fluid">
                        <!-- Row -->
                        <div class="table-struct full-width full-height">
                            <div class="table-cell vertical-align-middle auth-form-wrap">
                                <div class="auth-form  ml-auto mr-auto no-float">
                                    <div class="row">
                                        <div class="col-sm-12 col-xs-12">
                                            <div class="sp-logo-wrap text-center pa-0 mb-30">
                                                <a href="/">
                                                    <img class="brand-img mr-10 lazy"
                                                         data-src="/filemanager/userfiles/eworking_logo.png"
{{--                                                         src="{{ asset('filemanager/userfiles/' . @$settings['logo']) }}"--}}
                                                         alt="brand">
{{--                                                    <span class="brand-text">{{ @$settings['name'] }}</span>--}}
                                                </a>
                                            </div>
                                            <div class="form-wrap">
                                                <form method="post" action="forgot-password">
                                                    {{csrf_field()}}
                                                    <input name="change_password" value=""
                                                           style="display: none;">
                                                    <div class="form-group">
                                                        <label class="control-label mb-10" for="email" style="color: #fff;">{{trans('admin.password')}}</label>
                                                        <input type="hidden" name="change_password" value="{{ @$_GET['change_password'] }}">
                                                        <input style="color: #000;!important;" type="password"
                                                               class="form-control" name="password" required=""
                                                               id="password" placeholder="Nhập mật khẩu mới">
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label mb-10" for="email" style="color: #fff;">{{trans('admin.re_password')}}</label>
                                                        <input style="color: #000;!important;" type="password"
                                                               class="form-control" name="re_password"
                                                                required=""
                                                               id="re_password" placeholder="Nhập lại mật khẩu mới">
                                                        @if (session('alert_re_password'))
                                                            <p class="danger text-danger">{{session('alert_re_password')}}</p>
                                                        @endif
                                                    </div>
                                                    <div class="form-group text-center" style="float: right">
                                                        <a href="/email-forgot-password"
                                                           style="color: #fff; text-decoration: underline; padding: 10px; font-size: 13px">
                                                            Quay lại
                                                        </a>
                                                        <button type="submit" class="btn btn-primary btn-rounded">{{trans('admin.password_retrieval')}}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Row -->
                    </div>

                </div>
                <!-- /Main Content -->
            </div>
        </div>
    </div>
</div>
<!-- JavaScript -->
</body>
</html>

