<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">

    <div class="kt-header-mobile__toolbar">
        <button style="
    transform: scaleX(-1);
" class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
        <button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i
                    class="flaticon-more"></i></button>
    </div>
    <div class="kt-header-mobile__logo">
        <a href="/admin/dashboard">
            <img alt="{{ @$settings['name'] }}" class="lazy"
                 data-src="{{ \App\Http\Helpers\CommonHelper::getUrlImageThumb(@$settings['logo']) }}"
                 style="max-width:30px; max-height: 30px;">
        </a>
    </div>
    <div class="kt-header__topbar-item kt-header__topbar-item--user item-header logo-mobile">
        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
            <div class="kt-header__topbar-user">

                @if(@\Auth::guard('admin')->user()->image != null)
                    <img alt="{{ @\Auth::guard('admin')->user()->name }}" class="lazy"
                         data-src="{{ \App\Http\Helpers\CommonHelper::getUrlImageThumb(@\Auth::guard('admin')->user()->image,100,100) }}"/>
                @else
                    <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                    <span class=" kt-header__topbar-welcome kt-hidden-mobile">{{trans('admin.hello')}}</span>
                    <span class=" kt-header__topbar-username kt-hidden-mobile"
                          style="color: #636177">{{ @\Auth::guard('admin')->user()->name }}</span>
                @endif
            </div>
        </div>
        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl item-header-popup"
             style="position: absolute;     top: 53px; right: 0;
    left: unset; will-change: transform;"
        >
            <!--begin: Head -->
            <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x"
                 style="background-image: url({{ asset('/backend/themes/metronic1/media/misc/bg-1.jpg') }})">
                <div class="kt-user-card__avatar">
                    @if(@\Auth::guard('admin')->user()->image != null)
                        <img alt="Pic" class="lazy"
                             data-src="{{ \App\Http\Helpers\CommonHelper::getUrlImageThumb(@\Auth::guard('admin')->user()->image,100,100) }}"/>
                        <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                    @else
                        <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">{{ mb_substr(@\Auth::guard('admin')->user()->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="kt-user-card__name">
                    {{ @\Auth::guard('admin')->user()->name }}
                </div>
            </div>
            <!--end: Head -->

            <!--begin: Navigation -->
            <div class="kt-notification">
                <a href="/admin/profile"
                   class="kt-notification__item" style="margin: 10px">
                    <div class="kt-notification__item-icon">
                        <i class="flaticon2-calendar-3 kt-font-success"></i>
                    </div>
                    <div class="kt-notification__item-details">
                        <div class="kt-notification__item-title kt-font-bold">
                            {{trans('admin.my_info')}}
                        </div>
                        <div class="kt-notification__item-time">
                            {{trans('admin.setting_account')}}
                        </div>
                    </div>
                </a>

                {!! Eventy::filter('user_bar.profile_after', '') !!}

                <div class="theme-color">
                    <label style="padding: 0 1.75rem;margin: 0;">Màu giao diện</label>
                    <ul class="kt-nav kt-margin-t-10 kt-margin-b-10">
                        <?php
                        $admin_theme_style = Cookie::get('admin_theme_style', 'dark');
                        ?>
                        <li class="kt-nav__item {{ $admin_theme_style == 'dark' ? 'kt-nav__item--active' : '' }}">
                            <a href="/admin/theme/change?style=dark" class="kt-nav__link">
                                <span class="kt-nav__link-text">{{trans('admin.dark')}}</span>
                            </a>
                        </li>
                        <li class="kt-nav__item {{ $admin_theme_style == 'light' ? 'kt-nav__item--active' : '' }}">
                            <a href="/admin/theme/change?style=light" class="kt-nav__link">
                                <span class="kt-nav__link-text">{{trans('admin.light')}}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <?php
                $btn_setting = in_array('setting', $permissions) ? '<a href="/admin/setting"
                   class="btn btn-clean btn-sm btn-bold">'.trans('admin.settings').'</a>' : '';
                ?>
                {!! Eventy::filter('user_bar.footer', '<div class="kt-notification__custom kt-space-between">
                    <a href="/admin/logout"
                       class="btn btn-label btn-label-brand btn-sm btn-bold">'.trans('admin.logout').'</a>

                    '.$btn_setting.'
                </div>') !!}
            </div>
            <!--end: Navigation -->
        </div>
    </div>
</div>