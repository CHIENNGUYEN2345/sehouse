<!DOCTYPE html>
<html lang="en">

<!-- begin::Head -->
<head>
	<meta charset="utf-8" />
	<title>{{ $settings['name'] }} | {{trans('admin.register')}}</title>
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
	<link rel="shortcut icon" href="{{ CommonHelper::getUrlImageThumb(@$settings['favicon'], null, 16) }}">
</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

<!-- begin:: Page -->
<div class="m-grid m-grid--hor m-grid--root m-page">
	<div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-1" id="m_login" style="background-image: url({{ asset('/login/images/bg-1.jpg') }}) !important;">
		<div class="m-grid__item m-grid__item--fluid m-login__wrapper">
			<div class="m-login__container">
				<div class="m-login__logo">
					<a href="#">
						<img style="width: 50%" class="" src="{{asset('/filemanager/userfiles/'.@$settings['logo'])}}" title="{{ @$settings['name'] }}" alt="{{ @$settings['name'] }}"/>
					</a>
				</div>

				<div class="signup">
					<div class="m-login__head">
						<h3 class="m-login__title">{{trans('admin.register')}}</h3>
						<div class="m-login__desc">{{trans('admin.enter_information_to_create_your_account')}}:</div>
					</div>
					<form method="post" class="m-login__form m-form" action="">
						<div class="form-group m-form__group">
							<input style="color: #000;!important;" class="form-control m-input" type="text" placeholder="Họ & Tên" name="name" id="firstname" value="{{old('name')}}">
							@if ($errors->has('name'))
								<p class="text-danger" style="padding-left: 10px;">{{$errors->first('name')}}</p>
							@endif
						</div>
						<div class="form-group m-form__group">
							<input style="color: #000;!important;" class="form-control m-input" type="email" placeholder="Email" name="email" id="inputEmail" value="{{old('email')}}" autocomplete="off">
							@if ($errors->has('email'))
								<p class="text-danger" style="padding-left: 10px;">{{$errors->first('email')}}</p>
							@endif
						</div>
						<div class="form-group m-form__group">
							<input style="color: #000;!important;" class="form-control m-input" name="tel" type="text" placeholder="Số điện thoại"  id="tel" value="{{old('tel')}}">
							@if ($errors->has('tel'))
								<p class="text-danger" style="padding-left: 10px;">{{$errors->first('tel')}}</p>
							@endif
						</div>
						<div class="form-group m-form__group">
							<input style="color: #000;!important;" class="form-control m-input" type="password" name="password" id="inputPassword" placeholder="Mật khẩu">
							@if ($errors->has('password'))
								<p class="text-danger" style="padding-left: 10px;">{{$errors->first('password')}}</p>
							@endif
						</div>
						<div class="form-group m-form__group">
							<input style="color:#000!important;" class="form-control m-input m-login__form-input--last" name="password_confimation" type="password" id="inputPasswordConfirm" placeholder="Nhập lại mật khẩu">
							@if ($errors->has('password_confimation'))
								<p class="text-danger" style="padding-left: 10px;">{{$errors->first('password_confimation')}}</p>
							@endif
						</div>
						<div class="row form-group m-form__group m-login__form-sub">
							<div class="col m--align-left">
								<label class="m-checkbox m-checkbox--light">
									<input type="checkbox" name="agree">{{trans('admin.i_agree')}} <a href="#" class="m-link m-link--focus">{{trans('admin.terms_and_conditions')}}.</a>.
									<span></span>
								</label>
								<span class="m-form__help"></span>
							</div>
						</div>
						<div class="m-login__form-action">
							<button type="submit" class="btn m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary">{{trans('admin.register')}}</button>&nbsp;&nbsp;
							<a href="/admin/login" class="btn m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn">{{trans('admin.do_you_already_have_an_account')}}</a>
						</div>
					</form>
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