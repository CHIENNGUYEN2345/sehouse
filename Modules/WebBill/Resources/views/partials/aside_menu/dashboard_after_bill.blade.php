@if(in_array('bill_view', $permissions))
<li class="kt-menu__item" aria-haspopup="true"><a href="/admin/bill"
  class="kt-menu__link "><span
  class="kt-menu__link-icon"><svg xmlns="http://www.w3.org/2000/svg"
  xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
  height="24px"
  viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
    <rect x="0" y="0" width="24" height="24"/>
    <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
    fill="#000000" opacity="0.3"/>
    <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
    fill="#000000"/>
    <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1"/>
    <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1"/>
</g>
</svg></span><span class="kt-menu__link-text">Hợp đồng</span></a></li>
@endif

@if(in_array('cskh-bill_view', $permissions))
<li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a
            href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-icon">
                                    <i
                                            class="kt-menu__link-icon flaticon-download-1"></i>
                                </span><span class="kt-menu__link-text">CSKH</span><i
                class="kt-menu__ver-arrow la la-angle-right"></i></a>
    <div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
                class="kt-menu__arrow"></span>
        <ul class="kt-menu__subnav">
            <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/cskh-bill" class="kt-menu__link "><i
                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                            class="kt-menu__link-text">CSKH</span></a></li>
            <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/gh-bill" class="kt-menu__link "><i
                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                            class="kt-menu__link-text">Gia hạn HĐ</span></a></li>
        </ul>
    </div>
</li>

@endif

@if(in_array('truong_phong', $permissions))
<li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a
            href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-icon">
                                    <i
                                            class="kt-menu__link-icon flaticon-download-1"></i>
                                </span><span class="kt-menu__link-text">Q.lý phòng</span><i
                class="kt-menu__ver-arrow la la-angle-right"></i></a>
    <div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
                class="kt-menu__arrow"></span>
        <ul class="kt-menu__subnav">
            <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/tpbill" class="kt-menu__link "><i
                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                            class="kt-menu__link-text">Hợp đồng</span></a></li>
            <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/tp-lead" class="kt-menu__link "><i
                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                            class="kt-menu__link-text">Đầu mối</span></a></li>
        </ul>
    </div>
</li>
@endif

@if(in_array('mktlead_view', $permissions))
<li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a
            href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-icon">
                                    <i
                                            class="kt-menu__link-icon flaticon-download-1"></i>
                                </span><span class="kt-menu__link-text">MKT</span><i
                class="kt-menu__ver-arrow la la-angle-right"></i></a>
    <div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
                class="kt-menu__arrow"></span>
        <ul class="kt-menu__subnav">
            <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/mkt-lead" class="kt-menu__link "><i
                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                            class="kt-menu__link-text">Data</span></a></li>
            <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/mkt-lead?source=member group" class="kt-menu__link "><i
                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                            class="kt-menu__link-text">Data (member group)</span></a></li>
            <li class="kt-menu__item " aria-haspopup="true"><a
        href="/admin/mkt-lead?source=fanpage" class="kt-menu__link "><i
            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
            class="kt-menu__link-text">Data (fanpage)</span></a></li>
            <li class="kt-menu__item " aria-haspopup="true"><a
        href="/admin/mkt-lead?source=cty mới" class="kt-menu__link "><i
            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
            class="kt-menu__link-text">Data (cty mới)</span></a></li>
        </ul>
    </div>
</li>
@endif

@if (\Auth::guard('admin')->user()->super_admin == 1)
<li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true"><a
    href="/admin/bill_receipts" target="_blank" class="kt-menu__link "><span class="kt-menu__link-icon">
        <i
        class="kt-menu__link-icon flaticon-security"></i>
    </span><span class="kt-menu__link-text">Phiếu thu</span>
</a>
<div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
    class="kt-menu__arrow"></span>
</div>
</li>
@endif

@if(in_array('lead_view', $permissions))
<li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true"><a
        href="/admin/lead" class="kt-menu__link "><span class="kt-menu__link-icon">
            <i
            class="kt-menu__link-icon flaticon-security"></i>
        </span><span class="kt-menu__link-text">Đầu mối</span>
        
    </a>
    <div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
        class="kt-menu__arrow"></span>
    </div>
</li>
<li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true"><a
        href="/admin/lead/doi-tac" class="kt-menu__link "><span class="kt-menu__link-icon">
            <i
            class="kt-menu__link-icon flaticon-security"></i>
        </span><span class="kt-menu__link-text">Đối tác</span>
    </a>
    <div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
        class="kt-menu__arrow"></span>
    </div>
</li>
@endif

@if(in_array('course_view', $permissions))
<li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true"><a 
    href="/admin/course/view" class="kt-menu__link "><span class="kt-menu__link-icon">
        <i
        class="kt-menu__link-icon flaticon-security"></i>
    </span><span class="kt-menu__link-text">Đào tạo</span>
    
</a>
<div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
    class="kt-menu__arrow"></span>
</div>
</li>
@endif


@if(in_array('dhbill_view', $permissions))
<li class="kt-menu__item" aria-haspopup="true"><a href="/admin/dhbill"
  class="kt-menu__link "><span
  class="kt-menu__link-icon"><svg xmlns="http://www.w3.org/2000/svg"
  xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
  height="24px"
  viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
    <rect x="0" y="0" width="24" height="24"/>
    <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
    fill="#000000" opacity="0.3"/>
    <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
    fill="#000000"/>
    <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1"/>
    <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1"/>
</g>
</svg></span><span class="kt-menu__link-text">Triển khai</span></a></li>
@endif

<li class="kt-menu__item" aria-haspopup="true"><a href="/admin/dashboard/ds-ky-thuat"
  class="kt-menu__link "><span
  class="kt-menu__link-icon"><svg xmlns="http://www.w3.org/2000/svg"
  xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
  height="24px"
  viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
    <rect x="0" y="0" width="24" height="24"/>
    <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
    fill="#000000" opacity="0.3"/>
    <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
    fill="#000000"/>
    <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1"/>
    <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1"/>
</g>
</svg></span><span class="kt-menu__link-text">DS kỹ thuật</span></a></li>

@if(in_array('check_error_link_logs', $permissions))
<li class="kt-menu__item" aria-haspopup="true"><a href="/admin/check_error_link_logs"
  class="kt-menu__link "><span
  class="kt-menu__link-icon"><svg xmlns="http://www.w3.org/2000/svg"
  xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
  height="24px"
  viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
    <rect x="0" y="0" width="24" height="24"/>
    <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
    fill="#000000" opacity="0.3"/>
    <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
    fill="#000000"/>
    <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1"/>
    <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1"/>
</g>
</svg></span><span class="kt-menu__link-text">Website lỗi</span></a></li>
@endif

@if(in_array('plan_view', $permissions))
<li class="kt-menu__item" aria-haspopup="true"><a href="/admin/plan"
  class="kt-menu__link "><span
  class="kt-menu__link-icon"><svg xmlns="http://www.w3.org/2000/svg"
  xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
  height="24px"
  viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
    <rect x="0" y="0" width="24" height="24"/>
    <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
    fill="#000000" opacity="0.3"/>
    <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
    fill="#000000"/>
    <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1"/>
    <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1"/>
</g>
</svg></span><span class="kt-menu__link-text">Kế hoạch</span></a></li>
@endif

@if(in_array('hradmin_view', $permissions))
<li class="kt-menu__item" aria-haspopup="true"><a href="/admin/hradmin"
  class="kt-menu__link "><span
  class="kt-menu__link-icon"><svg xmlns="http://www.w3.org/2000/svg"
  xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
  height="24px"
  viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
    <rect x="0" y="0" width="24" height="24"/>
    <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z"
    fill="#000000" opacity="0.3"/>
    <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z"
    fill="#000000"/>
    <rect fill="#000000" opacity="0.3" x="7" y="10" width="5" height="2" rx="1"/>
    <rect fill="#000000" opacity="0.3" x="7" y="14" width="9" height="2" rx="1"/>
</g>
</svg></span><span class="kt-menu__link-text">Thành viên</span></a></li>
@endif


<li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a
            href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-icon">
                                    <i
                                            class="kt-menu__link-icon flaticon-download-1"></i>
                                </span><span class="kt-menu__link-text">Hành chính - nhân sự</span><i
                class="kt-menu__ver-arrow la la-angle-right"></i></a>
    <div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
                class="kt-menu__arrow"></span>
        <ul class="kt-menu__subnav">
            <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/timekeeper" class="kt-menu__link "><i
                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                            class="kt-menu__link-text">Chấm công</span></a></li>
            <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/penalty_ticket" class="kt-menu__link "><i
                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                            class="kt-menu__link-text">Phiếu phạt</span></a></li>
            
        </ul>
    </div>
</li>

<li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a
            href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-icon">
                                    <i
                                            class="kt-menu__link-icon flaticon-download-1"></i>
                                </span><span class="kt-menu__link-text">Báo cáo</span><i
                class="kt-menu__ver-arrow la la-angle-right"></i></a>
    <div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
                class="kt-menu__arrow"></span>
        <ul class="kt-menu__subnav">
            <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/hradmin/hieu-suat-cong-viec" class="kt-menu__link "><i
                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                            class="kt-menu__link-text">Tất cả ngày</span></a></li>
            @if(in_array('timekeeping_view', $permissions))
                <li class="kt-menu__item " aria-haspopup="true"><a
                        href="/admin/timekeeping/add" class="kt-menu__link "><i
                            class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                            class="kt-menu__link-text">Hôm nay</span></a></li>
            @endif
        </ul>
    </div>
</li>



