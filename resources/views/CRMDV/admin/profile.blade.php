@extends('CRMDV.admin.new_header.new_template')
@section('main')

    <!-- begin:: Content -->
    <form class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid" action=""
          method="POST"
          enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile"
                     id="kt_page_portlet">
                    <div class="kt-portlet__head kt-portlet__head--lg" style="">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">{{trans('admin.profile')}} {{ trans($module['label']) }}
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">

                            @if(in_array('admin_view', $permissions))
                                <a href="/admin/{{ $module['code'] }}" class="btn btn-clean kt-margin-r-10">
                                    <i class="la la-arrow-left"></i>
                                    <span class="kt-hidden-mobile">{{trans('admin.back')}}</span>
                                </a>
                            @endif


                            <div class="btn-group">
                                @if(in_array('admin_edit', $permissions) || $result->id == \Auth::guard('admin')->user()->id)
                                    <button type="submit" class="btn btn-brand">
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">{{trans('admin.save')}}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Portlet-->
            </div>
        </div>

        <!--Begin::App-->
        <div class="kt-grid kt-grid--desktop kt-grid--ver kt-grid--ver-desktop kt-app">
            <!--Begin:: App Aside Mobile Toggle-->
            <button class="kt-app__aside-close" id="kt_user_profile_aside_close">
                <i class="la la-close"></i>
            </button>
            <!--End:: App Aside Mobile Toggle-->

            <!--Begin:: App Aside-->
            <div class="kt-grid__item kt-app__toggle kt-app__aside" id="kt_user_profile_aside">
                <!--begin:: Widgets/Applications/BillPayment/Profile1-->
                <div class="kt-portlet kt-portlet--height-fluid-">
                    <div class="kt-portlet__head  kt-portlet__head--noborder">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fit-y">
                        <!--begin::Widget -->
                        <div class="kt-widget kt-widget--user-profile-1">
                            <div class="kt-widget__head">
                                <div class="kt-widget__media">
                                    <img style=""
                                         data-rc="{{ \App\Http\Helpers\CommonHelper::getUrlImageThumb(@$result->image, null, null) }}"
                                         class="lazy user-image user-lg-img"
                                         alt="{{ @$result->name }}"
                                         title="{{ @$result->name }}">
                                </div>
                                <div class="kt-widget__content">
                                    <div class="kt-widget__section">
                                        <a href="#" class="kt-widget__username">
                                            {{ $result->name }}
                                        </a>
                                        <span class="kt-widget__subtitle">
                                            {{\App\Http\Helpers\CommonHelper::getRoleName($result->id)}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="kt-widget__body">
                                <div class="kt-widget__content">
                                    <div class="kt-widget__info">
                                        <span class="kt-widget__label">{{trans('admin.email')}}:</span>
                                        <a href="#" class="kt-widget__data">{{$result->email}}</a>
                                    </div>
                                    <div class="kt-widget__info">
                                        <span class="kt-widget__label">{{trans('admin.phone')}}:</span>
                                        <a href="#" class="kt-widget__data">{{$result->tel}}</a>
                                    </div>
                                    <div class="kt-widget__info">
                                        <span class="kt-widget__label">{{trans('admin.address')}}:</span>
                                        <span class="kt-widget__data">{{$result->address}}</span>
                                    </div>
                                </div>
                                <div class="kt-widget__items">
                                    <a href="{{ $result->id == \Auth::guard('admin')->user()->id ? '/admin/profile' : '/admin/profile/' . $result->id }}"
                                       class="kt-widget__item kt-widget__item--active">
                                        <span class="kt-widget__section">
                                            <span class="kt-widget__icon">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     xmlns:xlink="http://www.w3.org/1999/xlink"
                                                     width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                                     class="kt-svg-icon">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                                        <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z"
                                                              fill="#000000" fill-rule="nonzero"/>
                                                        <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z"
                                                              fill="#000000" opacity="0.3"/>
                                                    </g>
                                                </svg>
                                            </span>
                                            <span class="kt-widget__desc">
                                                {{trans('admin.main_information')}}
                                            </span>
                                        </span>
                                    </a>
                                    @if(@$result->id == \Auth::guard('admin')->user()->id)
                                        <a href="/admin/profile/change-password"
                                           class="kt-widget__item">
                                            <span class="kt-widget__section">
                                                <span class="kt-widget__icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         xmlns:xlink="http://www.w3.org/1999/xlink"
                                                         width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                                         class="kt-svg-icon">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                           fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"/>
                                                            <path d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z"
                                                                  fill="#000000" opacity="0.3"/>
                                                            <path d="M12,11 C10.8954305,11 10,10.1045695 10,9 C10,7.8954305 10.8954305,7 12,7 C13.1045695,7 14,7.8954305 14,9 C14,10.1045695 13.1045695,11 12,11 Z"
                                                                  fill="#000000" opacity="0.3"/>
                                                            <path d="M7.00036205,16.4995035 C7.21569918,13.5165724 9.36772908,12 11.9907452,12 C14.6506758,12 16.8360465,13.4332455 16.9988413,16.5 C17.0053266,16.6221713 16.9988413,17 16.5815,17 C14.5228466,17 11.463736,17 7.4041679,17 C7.26484009,17 6.98863236,16.6619875 7.00036205,16.4995035 Z"
                                                                  fill="#000000" opacity="0.3"/>
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span class="kt-widget__desc">
                                                    {{trans('admin.change_password')}}
                                                </span>
                                            </span>
                                        </a>
                                    @endif
                                    {{--                                    Reset pass--}}
                                    @if(@$result->id != \Auth::guard('admin')->user()->id && in_array('super_admin', $permissions))
                                        <a href="/admin/profile/reset-password/{{$id_user}}"
                                           class="kt-widget__item">
                                            <span class="kt-widget__section">
                                                <span class="kt-widget__icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                         height="24px" viewBox="0 0 24 24" version="1.1"
                                                         class="kt-svg-icon">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                           fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"/>
                                                            <path d="M8.43296491,7.17429118 L9.40782327,7.85689436 C9.49616631,7.91875282 9.56214077,8.00751728 9.5959027,8.10994332 C9.68235021,8.37220548 9.53982427,8.65489052 9.27756211,8.74133803 L5.89079566,9.85769242 C5.84469033,9.87288977 5.79661753,9.8812917 5.74809064,9.88263369 C5.4720538,9.8902674 5.24209339,9.67268366 5.23445968,9.39664682 L5.13610134,5.83998177 C5.13313425,5.73269078 5.16477113,5.62729274 5.22633424,5.53937151 C5.384723,5.31316892 5.69649589,5.25819495 5.92269848,5.4165837 L6.72910242,5.98123382 C8.16546398,4.72182424 10.0239806,4 12,4 C16.418278,4 20,7.581722 20,12 C20,16.418278 16.418278,20 12,20 C7.581722,20 4,16.418278 4,12 L6,12 C6,15.3137085 8.6862915,18 12,18 C15.3137085,18 18,15.3137085 18,12 C18,8.6862915 15.3137085,6 12,6 C10.6885336,6 9.44767246,6.42282109 8.43296491,7.17429118 Z"
                                                                  fill="#000000" fill-rule="nonzero"/>
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span class="kt-widget__desc">
                                                    Reset Password
                                                </span>
                                            </span>
                                        </a>
                                        <a href="/admin/profile/{{$id_user}}/login"
                                           class="kt-widget__item">
                                            <span class="kt-widget__section">
                                                <span class="kt-widget__icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                         height="24px" viewBox="0 0 24 24" version="1.1"
                                                         class="kt-svg-icon">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                           fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24"/>
                                                            <path d="M8.43296491,7.17429118 L9.40782327,7.85689436 C9.49616631,7.91875282 9.56214077,8.00751728 9.5959027,8.10994332 C9.68235021,8.37220548 9.53982427,8.65489052 9.27756211,8.74133803 L5.89079566,9.85769242 C5.84469033,9.87288977 5.79661753,9.8812917 5.74809064,9.88263369 C5.4720538,9.8902674 5.24209339,9.67268366 5.23445968,9.39664682 L5.13610134,5.83998177 C5.13313425,5.73269078 5.16477113,5.62729274 5.22633424,5.53937151 C5.384723,5.31316892 5.69649589,5.25819495 5.92269848,5.4165837 L6.72910242,5.98123382 C8.16546398,4.72182424 10.0239806,4 12,4 C16.418278,4 20,7.581722 20,12 C20,16.418278 16.418278,20 12,20 C7.581722,20 4,16.418278 4,12 L6,12 C6,15.3137085 8.6862915,18 12,18 C15.3137085,18 18,15.3137085 18,12 C18,8.6862915 15.3137085,6 12,6 C10.6885336,6 9.44767246,6.42282109 8.43296491,7.17429118 Z"
                                                                  fill="#000000" fill-rule="nonzero"/>
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span class="kt-widget__desc">
                                                    Đăng nhập vào tài khoản này
                                                </span>
                                            </span>
                                        </a>
                                    @endif

                                    {!! Eventy::filter('admin.profile_aside_menu', '') !!}
                                </div>
                            </div>
                        </div>
                        <!--end::Widget -->
                    </div>
                </div>
                <!--end:: Widgets/Applications/BillPayment/Profile1-->
            </div>
            <!--End:: App Aside-->

            <!--Begin:: App Content-->
            <div class="kt-grid__item kt-grid__item--fluid kt-app__content">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">{{trans('admin.basic_information')}}</h3>
                                </div>
                            </div>

                            <div class="kt-form kt-form--label-right">
                                <div class="kt-portlet__body">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="row">
                                                <label class="col-xl-3"></label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <h3 class="kt-section__title kt-section__title-sm">{{trans('admin.basic_information')}}
                                                        :</h3>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{trans('admin.avatar')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <?php $field = ['name' => 'image', 'type' => 'file_image', 'label' => 'Ảnh đại diện', 'value' => @$reulst->image,];
                                                    if (@$result->id != \Auth::guard('admin')->user()->id) {
                                                        $field['disabled'] = true;
                                                    }
                                                    ?>
                                                    @include(config('core.admin_theme').'.form.fields.file_image', ['field' => $field])
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{trans('admin.full_name')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <input class="form-control" type="text" name="name"
                                                           @if(@$result->id != \Auth::guard('admin')->user()->id) disabled
                                                           @endif
                                                           class="form-control require" id="name"
                                                           value="{{ @$result->name }}">
                                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{trans('admin.phone')}}
                                                </label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span
                                                                    class="input-group-text"><i
                                                                        class="la la-phone"></i></span></div>
                                                        <input type="number" name="tel" class="form-control " id="tel"
                                                               @if(@$result->id != \Auth::guard('admin')->user()->id) disabled
                                                               @endif
                                                               value="{{ @$result->tel }}" placeholder="Số điện thoại"
                                                               aria-describedby="basic-addon1">
                                                    </div>
                                                    <span class="text-danger">{{ $errors->first('tel') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{trans('admin.email')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span
                                                                    class="input-group-text"><i
                                                                        class="la la-at"></i></span></div>
                                                        <input type="email" name="email" class="form-control require"
                                                               id="email"
                                                               @if(@$result->id != \Auth::guard('admin')->user()->id) disabled
                                                               @endif
                                                               value="{{ @$result->email }}" required=""
                                                               placeholder="Email"
                                                               aria-describedby="basic-addon1">
                                                    </div>
                                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group form-group-status row" id="">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{trans('admin.active')}}</label>
                                                <div class="col-lg-9 col-xl-6 pt-2">
                                                    <label class="kt-checkbox kt-checkbox--success">
                                                        <input type="checkbox" name="status" value="1"
                                                               @if(Auth::guard('admin')->user()->id == @$result->id || !in_array('admin_edit', $permissions)) disabled @endif
                                                                {{ $result->status == 1 ? 'checked' : '' }}><span></span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group form-group-role row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{trans('admin.role')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <?php $roles = \App\Models\Roles::pluck('display_name', 'id');
                                                    $role_id = @\App\Models\RoleAdmin::where('admin_id', $result->id)->first()->role_id;
                                                    echo @\App\Models\Roles::find($role_id)->display_name;
                                                    ?>
                                                    <select name="role_id" style="display: none;"
                                                            class="form-control" {{ in_array('admin_edit', $permissions) ? '' : 'disabled' }}>
                                                        @foreach($roles as $id => $name)
                                                            <option value="{{ $id }}" {{ $role_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            {!! Eventy::filter('admin.block_profile_general', '') !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--End:: App Content-->
        </div>
        <!--End::App-->
    </form>
    <!-- end:: Content -->
@endsection
@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/form.css') }}">
    <style>
        @media (max-width: 768px) {
            div#kt_user_profile_aside {
                left: 0;
                width: 100% !important;
                position: relative;
                margin-bottom: 10px;
            }
        }
    </style>
@endsection
