<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="utf-8"/>
    <meta property="og:image" content="https://static.ladipage.net/6059905fc69c140013fd3555/xong-1-20230703062155-0pr1n.png">
    <title>{{@$settings['name']}} | Đăng nhập</title>
    <meta name="description" content="SeHouse Land - Sàn môi giới chuyển nhượng nhà đất - chung cư số 1 Việt Nam. Bất động sản SEHOUSE liên tục tuyển dụng các vị trí chuyên viên môi giới chuyển nhượng nhà đất, chung cư cơ chế đãi ngộ tốt nhất thị trường.">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!--end::Web font -->

    <!--begin::Global Theme Styles -->
    <link rel="stylesheet" href="{{asset('login/css/vendors.bundle.css')}}">
    <link rel="stylesheet" href="{{asset('login/css/vendors.bundle.rtl.css')}}">


    <!--RTL version:<link href="../../../assets/vendors/base/vendors.bundle.rtl.css" rel="stylesheet" type="text/css" />-->
    <link rel="stylesheet" href="{{asset('login/css/style.bundle.css')}}">
    <link rel="stylesheet" href="{{asset('libs/bootstrap/js/bootstrap.min.js')}}">

    <!--RTL version:<link href="../../../assets/demo/default/base/style.bundle.rtl.css" rel="stylesheet" type="text/css" />-->

    <!--end::Global Theme Styles -->
    <link rel="shortcut icon" href="{{ CommonHelper::getUrlImageThumb(@$settings['favicon'], null, 16) }}">
    <style>
        a.with-smedia {
            color: #fff;
            display: inline-block;
            font-weight: normal;
            margin: 10px auto 0;
            padding: 10px 30px;
            text-transform: capitalize;
            border-radius: 30px;
            font-size: 12px;
        }

        a.facebook {
            background: #516eab;
        }

        a.google {
            background: #dd4b39;
        }
    </style>
</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="hold-transition login-page m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

<!-- begin:: Page -->
<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-1"
         id="m_login" style="background-image: url({{asset('/filemanager/userfiles/'.@$settings['background_login'])}}) !important;">
        <div class="m-grid__item m-grid__item--fluid m-login__wrapper">
            <div class="m-login__container">
                <div class="m-login__logo">
                    <a href="#">
                        <img style="width: 50%" class=""
                             src="{{asset('/filemanager/userfiles/'.@$settings['logo'])}}"
                             title="{{ @$settings['name'] }}" alt="{{ @$settings['name'] }}"/>
                    </a>
                </div>
                @if (session('success'))
                    <div class="alert bg-success" role="alert">
                        {!!session('success')!!}
                    </div>
                @endif
                <div class="flash-container" style="left:15px;">
                    @if(Session::has('message') && !Auth::check())
                        <div class="alert text-center text-danger" role="alert"
                             style=" margin: 0; font-size: 16px;">
                            <a href="#" style="float:right;" class="alert-close" data-dismiss="alert">&times;</a>
                            {{ Session::get('message') }}
                        </div>
                    @endif
                </div>
                <div class="m-login__signin">
                    <div class="m-login__head">
                        <h3 class="m-login__title">{{ @$settings['name'] }}</h3>
                    </div>
                    <form method="post" class="m-login__form m-form" action="{{ url('admin/authenticate') }}">
                        <div class="form-group m-form__group has-feedback">
                            <input style="color: #000;!important;" type="text" name="email" class="form-control m-input"
                                   placeholder="Email hoặc điện thoại">
                        </div>
                        <div class="form-group m-form__group has-feedback">
                            <input style="color:#000!important;" type="password" name="password"
                                   class="form-control m-input m-login__form-input--last" placeholder="Mật khẩu">
                        </div>
                        <div class="row m-login__form-sub">
                            <div class="col m--align-left m-login__form-left">
                                <label class="m-checkbox  m-checkbox--light">
                                    <input type="checkbox"
                                           name="remember_account"> {{trans('admin.remember_my_account')}}
                                    <span></span>
                                </label>
                            </div>
                            <div class="col m--align-right m-login__form-right">
                                <a href="/email-forgot-password" id="m_login_forget_password"
                                   class="m-link">{{trans('admin.forget_password')}}</a>
                            </div>
                        </div>
                        <div class="m-login__form-action">
                            <button type="submit"
                                    class="btn btn-primary btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn--primary">{{trans('admin.login')}}</button>
                        </div>
                    </form>
                </div>

                @if(@\App\Models\Setting::where('type', 'role_tab')->where('name', 'allow_admin_account_registration')->first()->value == 1)
                    <div class="m-login__account">
                        <a class="with-smedia facebook" href="/admin/login/facebook/redirect/" title="" data-ripple="">Đăng
                            nhập với facebook</a>
                        <a class="with-smedia google" href="/admin/login/google/redirect/" title="" data-ripple="">Đăng
                            nhập với google</a>
                        <span class="m-login__account-msg">
								{{trans('admin.do_not_have_an_account')}}
							</span>&nbsp;&nbsp;
                        <a href="/admin/register" id="m_login_signup"
                           class="m-link m-link--light m-login__account-link">{{trans('admin.register')}}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- end:: Page -->

<!--begin::Global Theme Bundle -->
{{--<link rel="stylesheet" href="{{asset('login/js/vendors.bundle.js')}}">--}}
{{--<link rel="stylesheet" href="{{asset('login/js/scripts.bundle.js')}}">--}}


<!--end::Global Theme Bundle -->

<!--begin::Page Scripts -->
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<script src="{{URL::to('libs/jquery-3.4.0.min.js')}}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{URL::to('libs/bootstrap/js/bootstrap.min.js')}}"></script>

<!--end::Page Scripts -->
</body>

<!-- end::Body -->
</html>
