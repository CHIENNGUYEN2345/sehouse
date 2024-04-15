<div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
    {{--{{dd(in_array('setting', $permissions))}}--}}
    <div id="kt_aside_menu" class="kt-aside-menu kt-scroll ps ps--active-y" data-ktmenu-vertical="1"
         data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500" style="height: 191px; overflow: hidden;">
        <ul class="kt-menu__nav ">
            {!! Eventy::filter('aside_menu.dashboard_after', '') !!}

            <li class="kt-menu__section ">
                <h4 class="kt-menu__section-text">Tài khoản</h4>
                <i class="kt-menu__section-icon flaticon-more-v2"></i>
            </li>


            @if(in_array('admin_view', $permissions))
                {!! Eventy::filter('aside_menu.admin', '<li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/admin" class="kt-menu__link "><span
                                class="kt-menu__link-icon"><i
                                class="kt-menu__link-icon flaticon2-avatar"></i></span><span class="kt-menu__link-text">Tài khoản</span></a></li>') !!}
            @endif

            @if(in_array('role_view', $permissions))
                {!! Eventy::filter('aside_menu.role', '<li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/role" class="kt-menu__link "><span class="kt-menu__link-icon"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Code/Stop.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M12,22 C6.4771525,22 2,17.5228475 2,12 C2,6.4771525 6.4771525,2 12,2 C17.5228475,2 22,6.4771525 22,12 C22,17.5228475 17.5228475,22 12,22 Z M12,20 C16.418278,20 20,16.418278 20,12 C20,7.581722 16.418278,4 12,4 C7.581722,4 4,7.581722 4,12 C4,16.418278 7.581722,20 12,20 Z M19.0710678,4.92893219 L19.0710678,4.92893219 C19.4615921,5.31945648 19.4615921,5.95262146 19.0710678,6.34314575 L6.34314575,19.0710678 C5.95262146,19.4615921 5.31945648,19.4615921 4.92893219,19.0710678 L4.92893219,19.0710678 C4.5384079,18.6805435 4.5384079,18.0473785 4.92893219,17.6568542 L17.6568542,4.92893219 C18.0473785,4.5384079 18.6805435,4.5384079 19.0710678,4.92893219 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
    </g>
</svg><!--end::Svg Icon--></span><span class="kt-menu__link-text">'.trans('admin.role').'</span></a></li>') !!}
            @endif

            @if(in_array('setting', $permissions))
            <li class="kt-menu__section ">
                <h4 class="kt-menu__section-text">Cấu hình</h4>
                <i class="kt-menu__section-icon flaticon-more-v2"></i>
            </li>
            @endif



            @if(in_array('setting', $permissions))
            <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/setting" class="kt-menu__link ">
                    <span class="kt-menu__link-icon"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/General/Settings-2.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M5,8.6862915 L5,5 L8.6862915,5 L11.5857864,2.10050506 L14.4852814,5 L19,5 L19,9.51471863 L21.4852814,12 L19,14.4852814 L19,19 L14.4852814,19 L11.5857864,21.8994949 L8.6862915,19 L5,19 L5,15.3137085 L1.6862915,12 L5,8.6862915 Z M12,15 C13.6568542,15 15,13.6568542 15,12 C15,10.3431458 13.6568542,9 12,9 C10.3431458,9 9,10.3431458 9,12 C9,13.6568542 10.3431458,15 12,15 Z" fill="#000000"/>
    </g>
</svg><!--end::Svg Icon--></span>
                    <span class="kt-menu__link-text">Cấu hình chung</span></a></li>
            @endif

            @if(in_array('setting', $permissions))
                <li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true"
                    data-ktmenu-submenu-toggle="hover"><a
                            href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Tools/Tools.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M15.9497475,3.80761184 L13.0246125,6.73274681 C12.2435639,7.51379539 12.2435639,8.78012535 13.0246125,9.56117394 L14.4388261,10.9753875 C15.2198746,11.7564361 16.4862046,11.7564361 17.2672532,10.9753875 L20.1923882,8.05025253 C20.7341101,10.0447871 20.2295941,12.2556873 18.674559,13.8107223 C16.8453326,15.6399488 14.1085592,16.0155296 11.8839934,14.9444337 L6.75735931,20.0710678 C5.97631073,20.8521164 4.70998077,20.8521164 3.92893219,20.0710678 C3.1478836,19.2900192 3.1478836,18.0236893 3.92893219,17.2426407 L9.05556629,12.1160066 C7.98447038,9.89144078 8.36005124,7.15466739 10.1892777,5.32544095 C11.7443127,3.77040588 13.9552129,3.26588995 15.9497475,3.80761184 Z" fill="#000000"/>
        <path d="M16.6568542,5.92893219 L18.0710678,7.34314575 C18.4615921,7.73367004 18.4615921,8.36683502 18.0710678,8.75735931 L16.6913928,10.1370344 C16.3008685,10.5275587 15.6677035,10.5275587 15.2771792,10.1370344 L13.8629656,8.7228208 C13.4724413,8.33229651 13.4724413,7.69913153 13.8629656,7.30860724 L15.2426407,5.92893219 C15.633165,5.5384079 16.26633,5.5384079 16.6568542,5.92893219 Z" fill="#000000" opacity="0.3"/>
    </g>
</svg><!--end::Svg Icon--></span>
                        <span class="kt-menu__link-text">Hệ thống</span><i
                                class="kt-menu__ver-arrow la la-angle-right"></i></a>
                    <div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
                                class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item " aria-haspopup="true" title="Quản lý bộ nhớ đệm"><a
                                        href="/admin/cache" class="kt-menu__link "><i
                                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                            class="kt-menu__link-text">{{trans('admin.cache')}}</span></a></li>
                            <li class="kt-menu__item " aria-haspopup="true" title="Xem lịch sử ghi nhận lỗi của hệ thống"><a
                                        href="/admin/error" class="kt-menu__link "><i
                                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                            class="kt-menu__link-text">{{trans('admin.history_error')}}</span></a></li>
                            {{--<li class="kt-menu__item " aria-haspopup="true"><a
                                        href="/admin/queue" class="kt-menu__link "><i
                                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                            class="kt-menu__link-text">{{trans('admin.the_process_run_hidden')}}</span></a></li>--}}
                            <li class="kt-menu__item " aria-haspopup="true" title="Sao lưu dữ liệu hệ thống"><a
                                        href="/admin/backup" class="kt-menu__link "><i
                                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                            class="kt-menu__link-text">{{trans('admin.data_backup')}}</span></a></li>
                            <li class="kt-menu__item " aria-haspopup="true" title="Nhập dữ liệu vào hệ thống nhanh bằng file excel"><a
                                        href="/admin/import" class="kt-menu__link "><i
                                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                            class="kt-menu__link-text">Import</span></a></li>
                            <li class="kt-menu__item " aria-haspopup="true" title="Lịch sử hoạt động của các tài khoản"><a
                                        href="/admin/admin_logs" class="kt-menu__link "><i
                                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                            class="kt-menu__link-text">{{trans('admin.history_admin')}}</span></a></li>
                        </ul>
                    </div>
                </li>
            @endif

            <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/logout" class="kt-menu__link ">
                    <span class="kt-menu__link-icon"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Electric/Shutdown.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M7.62302337,5.30262097 C8.08508802,5.000107 8.70490146,5.12944838 9.00741543,5.59151303 C9.3099294,6.05357769 9.18058801,6.67339112 8.71852336,6.97590509 C7.03468892,8.07831239 6,9.95030239 6,12 C6,15.3137085 8.6862915,18 12,18 C15.3137085,18 18,15.3137085 18,12 C18,9.99549229 17.0108275,8.15969002 15.3875704,7.04698597 C14.9320347,6.73472706 14.8158858,6.11230651 15.1281448,5.65677076 C15.4404037,5.20123501 16.0628242,5.08508618 16.51836,5.39734508 C18.6800181,6.87911023 20,9.32886071 20,12 C20,16.418278 16.418278,20 12,20 C7.581722,20 4,16.418278 4,12 C4,9.26852332 5.38056879,6.77075716 7.62302337,5.30262097 Z" fill="#000000" fill-rule="nonzero"/>
        <rect fill="#000000" opacity="0.3" x="11" y="3" width="2" height="10" rx="1"/>
    </g>
</svg><!--end::Svg Icon--></span>
                    <span class="kt-menu__link-text">{{ trans('admin.logout') }}</span></a></li>
        </ul>
        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 191px; right: 3px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 40px;"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        <?php $uri = "$_SERVER[REQUEST_URI]";?>
        $("#kt_aside_menu a[href='{{ $uri }}']").parents('li').addClass('kt-menu__item--active');
        $("#kt_aside_menu a[href='{{ $uri }}']").parents('li').parents('li').addClass('kt-menu__item--here kt-menu__item--open').removeClass('kt-menu__item--active');
        $("#kt_aside_menu a[href='{{ $uri }}']").parents('li').parents('li').find('.kt-menu__submenu').attr('style', '');
    });
</script>