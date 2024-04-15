
<!DOCTYPE html>
<html lang="en">

<!-- begin::Head -->
<head>
    <meta charset="utf-8"/>
    <title>{{ $settings['name'] }} | {{$page_title}}</title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <link rel="stylesheet" href="{{asset('libs/bootstrap/css/bootstrap.min.css')}}">
    <script>
        WebFont.load({
            google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>


    <!--end::Web font -->

    <link rel="stylesheet" href="{{asset('login/css/vendors.bundle.css')}}">
    <link rel="stylesheet" href="{{asset('login/css/vendors.bundle.rtl.css')}}">
    <link rel="stylesheet" href="{{asset('login/css/style.bundle.css')}}">


    <!--end::Global Theme Styles -->
    <link rel="shortcut icon" href="{{ @$favicon }}">
</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

<!-- begin:: Page -->
<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-1"
         id="m_login" style="background-image: url({{asset('/filemanager/userfiles/'.@$settings['background_login'])}}) !important;">
        <div class="m-grid__item m-grid__item--fluid m-login__wrapper">
            <div class="m-login__container">
                <div class="m-login__logo">
                    <a href="#">
                        <img style="width: 50%" src="{{asset('/filemanager/userfiles/'.@$settings['logo'])}}" class="lazy"
                             title="{{ @$settings['name'] }}" alt="{{ @$settings['name'] }}"/>
                    </a>
                </div>

                <div class="forget-password">
                    <div class="m-login__head">
                        <h3 class="m-login__title">{{trans('admin.forget_password')}}</h3>
                        <div class="m-login__desc">{{trans('admin.please_enter_your_email_to_retrieve_your_password')}} :</div>
                    </div>

                    @if(Session::has('message') && !Auth::check())
                        <div class="flash-container" style="left:15px;">
                            <div class="alert text-center text-danger" role="alert"
                                 style=" margin: 0; font-size: 16px; color: red;">
                                <a href="#" class="alert-close" data-dismiss="alert">&times;</a>
                                {{ Session::get('message') }}
                            </div>
                        </div>
                    @endif


                    <form class="m-login__form m-form" action="" id="register-form" role="form" method="post">
                        <div class="form-group m-form__group">
                            <input style="color: #000;!important;" class="form-control m-input" type="email"
                                   placeholder="Email" name="email" id="email" autocomplete="off">
                        </div>
                        <div class="m-login__form-action">
                            <button id="m_login_forget_password_submit"
                                    class="btn m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary"
                                    name="recover-submit" type="submit">{{trans('admin.send_again_password')}}
                            </button>&nbsp;&nbsp;
                            <a href="/" class="btn m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn">{{trans('admin.cancel')}}</a>
                        </div>
                        <input style="color: #000;!important;" type="hidden" class="hide" name="token" id="token"
                               value="">
                    </form>
                </div>
                <div class="m-login__account">
							<span class="m-login__account-msg">{{trans('admin.do_not_have_an_account')}}
							</span>&nbsp;&nbsp;
                    <a href="/admin/register" class="m-link m-link--light m-login__account-link">{{trans('admin.register')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- end:: Page -->

<!--begin::Global Theme Bundle -->
<script src="/login/js/vendors.bundle.js" type="text/javascript"></script>
<script src="/login/js/scripts.bundle.js" type="text/javascript"></script>

<!--end::Global Theme Bundle -->

<!--begin::Page Scripts -->
<script src="/login/js/login.js" type="text/javascript"></script>

<!--end::Page Scripts -->
</body>

<!-- end::Body -->
</html>