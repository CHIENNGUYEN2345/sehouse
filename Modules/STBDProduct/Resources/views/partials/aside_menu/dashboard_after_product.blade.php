@if(in_array('product_view', $permissions))
    <li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a
                href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-icon">
                        <i
                                class="kt-menu__link-icon flaticon-folder-1"></i>
                    </span><span class="kt-menu__link-text">Sản phẩm</span><i
                    class="kt-menu__ver-arrow la la-angle-right"></i></a>
        <div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
                    class="kt-menu__arrow"></span>
            <ul class="kt-menu__subnav">
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/product" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Tất cả sản phẩm</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/product/add" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Tạo sản phẩm mới</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/category_product" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Chuyên mục</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/category_discount" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Chuyên mục khuyến mãi</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/product_sale" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Sản phẩm khuyến mãi</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/tag_product" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Thẻ</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/manufacturer" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Thương hiệu</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/properties_name" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Thuộc tính</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/guarantees" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Bảo hành</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/origin" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Xuất xứ</span></a></li>
            </ul>
        </div>
    </li>
@endif