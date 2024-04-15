<li class="kt-menu__section ">
  <h4 class="kt-menu__section-text">WEB</h4>
  <i class="kt-menu__section-icon flaticon-more-v2"></i>
</li>
@if(in_array('landingpage_view', $permissions))
    <li class="kt-menu__item" aria-haspopup="true"><a href="/admin/landingpage"
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
</svg></span><span class="kt-menu__link-text">LDP</span></a></li>
@endif

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

@if(in_array('cskh-bill_view', $permissions))
  <li class="kt-menu__item" aria-haspopup="true"><a href="/admin/gh-bill"
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
</svg></span><span class="kt-menu__link-text">Gia hạn HĐ</span></a></li>
@endif


@if(in_array('codes_view', $permissions))
    <li class="kt-menu__item" aria-haspopup="true"><a href="/admin/codes"
                                                      class="kt-menu__link "><span
                    class="kt-menu__link-icon">
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
     viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect x="0" y="0" width="24" height="24"/>
        <path d="M7.14319965,19.3575259 C7.67122143,19.7615175 8.25104409,20.1012165 8.87097532,20.3649307 L7.89205065,22.0604779 C7.61590828,22.5387706 7.00431787,22.7026457 6.52602525,22.4265033 C6.04773263,22.150361 5.88385747,21.5387706 6.15999985,21.0604779 L7.14319965,19.3575259 Z M15.1367085,20.3616573 C15.756345,20.0972995 16.3358198,19.7569961 16.8634386,19.3524415 L17.8320512,21.0301278 C18.1081936,21.5084204 17.9443184,22.1200108 17.4660258,22.3961532 C16.9877332,22.6722956 16.3761428,22.5084204 16.1000004,22.0301278 L15.1367085,20.3616573 Z"
              fill="#000000"/>
        <path d="M12,21 C7.581722,21 4,17.418278 4,13 C4,8.581722 7.581722,5 12,5 C16.418278,5 20,8.581722 20,13 C20,17.418278 16.418278,21 12,21 Z M19.068812,3.25407593 L20.8181344,5.00339833 C21.4039208,5.58918477 21.4039208,6.53893224 20.8181344,7.12471868 C20.2323479,7.71050512 19.2826005,7.71050512 18.696814,7.12471868 L16.9474916,5.37539627 C16.3617052,4.78960984 16.3617052,3.83986237 16.9474916,3.25407593 C17.5332781,2.66828949 18.4830255,2.66828949 19.068812,3.25407593 Z M5.29862906,2.88207799 C5.8844155,2.29629155 6.83416297,2.29629155 7.41994941,2.88207799 C8.00573585,3.46786443 8.00573585,4.4176119 7.41994941,5.00339833 L5.29862906,7.12471868 C4.71284263,7.71050512 3.76309516,7.71050512 3.17730872,7.12471868 C2.59152228,6.53893224 2.59152228,5.58918477 3.17730872,5.00339833 L5.29862906,2.88207799 Z"
              fill="#000000" opacity="0.3"/>
        <path d="M11.9630156,7.5 L12.0475062,7.5 C12.3043819,7.5 12.5194647,7.69464724 12.5450248,7.95024814 L13,12.5 L16.2480695,14.3560397 C16.403857,14.4450611 16.5,14.6107328 16.5,14.7901613 L16.5,15 C16.5,15.2109164 16.3290185,15.3818979 16.1181021,15.3818979 C16.0841582,15.3818979 16.0503659,15.3773725 16.0176181,15.3684413 L11.3986612,14.1087258 C11.1672824,14.0456225 11.0132986,13.8271186 11.0316926,13.5879956 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z"
              fill="#000000"/>
    </g>
</svg></span><span class="kt-menu__link-text">Mã nguồn</span></a></li>
@endif


@if(in_array('super_admin', $permissions))
    <li class="kt-menu__item kt-menu__item--submenu" aria-haspopup="true" data-ktmenu-submenu-toggle="hover"><a
                href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-icon">
                                        <i
                                                class="kt-menu__link-icon flaticon-download-1"></i>
                                    </span><span class="kt-menu__link-text">Tool</span><i
                    class="kt-menu__ver-arrow la la-angle-right"></i></a>
        <div class="kt-menu__submenu " kt-hidden-height="80" style="display: none; overflow: hidden;"><span
                    class="kt-menu__arrow"></span>
            <ul class="kt-menu__subnav">
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/landingpage/update-to-bill" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">LDP -> Bill</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/codes/update-bill-to-codes" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Bill -> Codes</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/codes/backup-to-html" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Codes -> .html</span></a></li>
                <li class="kt-menu__item " aria-haspopup="true"><a
                            href="/admin/codes/quick-add" class="kt-menu__link "><i
                                class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span
                                class="kt-menu__link-text">Tạo nhanh codes</span></a></li>
            </ul>
        </div>
    </li>
@endif


<li class="kt-menu__item" aria-haspopup="true"><a target="_blank" href="https://docs.google.com/forms/d/e/1FAIpQLSchECzgu4ms_MOzoMC2HkTgra7eU7GKDiyIz6MG2yf6vZdZmg/viewform"
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
</svg></span><span class="kt-menu__link-text">Đóng góp ý kiến</span></a></li>

@if(in_array('setting', $permissions))
<li class="kt-menu__item" aria-haspopup="true"><a href="/admin/setting"
                                                  class="kt-menu__link ">
    <span class="kt-menu__link-icon"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo1/dist/../src/media/svg/icons/Shopping/Settings.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <rect opacity="0.200000003" x="0" y="0" width="24" height="24"/>
        <path d="M4.5,7 L9.5,7 C10.3284271,7 11,7.67157288 11,8.5 C11,9.32842712 10.3284271,10 9.5,10 L4.5,10 C3.67157288,10 3,9.32842712 3,8.5 C3,7.67157288 3.67157288,7 4.5,7 Z M13.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L13.5,18 C12.6715729,18 12,17.3284271 12,16.5 C12,15.6715729 12.6715729,15 13.5,15 Z" fill="#000000" opacity="0.3"/>
        <path d="M17,11 C15.3431458,11 14,9.65685425 14,8 C14,6.34314575 15.3431458,5 17,5 C18.6568542,5 20,6.34314575 20,8 C20,9.65685425 18.6568542,11 17,11 Z M6,19 C4.34314575,19 3,17.6568542 3,16 C3,14.3431458 4.34314575,13 6,13 C7.65685425,13 9,14.3431458 9,16 C9,17.6568542 7.65685425,19 6,19 Z" fill="#000000"/>
    </g>
</svg><!--end::Svg Icon--></span>
    <span class="kt-menu__link-text">Cài đặt</span></a></li>
@endif