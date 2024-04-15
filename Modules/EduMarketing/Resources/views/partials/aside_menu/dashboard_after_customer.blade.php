@if(in_array('customer', $permissions))
    <li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a
                href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-icon">
                        <i
                                class="kt-menu__link-icon flaticon-customer"></i>
                    </span><span class="kt-menu__link-text">Khách hàng</span><i
                    class="kt-menu__ver-arrow la la-angle-right"></i></a>
        <div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
                    class="kt-menu__arrow"></span>
            <ul class="kt-menu__subnav">
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/customer" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Khách hàng tiềm năng</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/tag" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Tag</span></a></li>
            </ul>
        </div>
    </li>
@endif