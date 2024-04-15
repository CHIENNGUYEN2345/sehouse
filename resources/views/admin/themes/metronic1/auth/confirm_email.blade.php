<!DOCTYPE html>
<html lang="en">

<!-- begin::Head -->
<head>
	<meta charset="utf-8" />
	<title>{{ $settings['name'] }} | {{$page_title}}</title>
	<meta name="description" content="Latest updates and statistic charts">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

	<!--begin::Web font -->
	<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
	<script>
		WebFont.load({
			google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

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
	<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-1" id="m_login" style="background-image: url({{asset('/filemanager/userfiles/'.@$settings['background_login'])}}) !important;">
		<div class="m-grid__item m-grid__item--fluid m-login__wrapper">
			<div class="m-login__container">
				<div class="m-login__logo">
					<a href="#">
						<img style="width: 50%" class="lazy" data-src="{{asset('/filemanager/userfiles/'.@$settings['logo'])}}" title="{{ @$settings['name'] }}" alt="{{ @$settings['name'] }}"/>
					</a>
				</div>

				<div class="signup">
					<div class="m-login__head">
						<h3 class="m-login__title">{{trans('admin.confirm_successful_email_change')}}</h3>
						<div class="m-login__desc">{{trans('admin.enter_your_account_information_has_been_updated')}}</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<!-- end:: Page -->

<!--begin::Global Theme Bundle -->
<link rel="stylesheet" href="{{asset('login/js/login.js')}}">
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
{{--<script src="{{URL::to('backend/plugins/jQuery/jquery-2.2.3.min.js')}}"></script>--}}
<!-- Bootstrap 3.3.6 -->
<script src="{{URL::to('libs/bootstrap/js/bootstrap.min.js')}}"></script>

<!--end::Page Scripts -->
</body>

<!-- end::Body -->
</html>