@extends('Custom.bao_cao_dan_khach.new_header.new_template')
@section('main')

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
			<span class="kt-portlet__head-icon">
                <i class="kt-font-brand flaticon-calendar-with-a-clock-time-tools"></i>
			</span>
                    <h3 class="kt-portlet__head-title">
                        {{ $module['label'] }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="">
                            <input type="text" name="quick_search" value="{{ @$_GET['quick_search'] }}"
                                   class="form-control w-100" title="Chỉ cần enter để thực hiện tìm kiếm"
                                   placeholder="Tìm kiếm nhanh">
                        </div>
                        <div class="kt-portlet__head-actions">
                            <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle btn-closed-search"
                                    onclick="$('.form-search').slideToggle(100); $('.kt-portlet-search').toggleClass('no-padding');">
                                <i class="la la-search"></i> Tìm kiếm
                            </button>
                            <div class="dropdown dropdown-inline">
                                <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="la la-download"></i> Hành động
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end"
                                     style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(114px, 38px, 0px);">
                                    <ul class="kt-nav">
                                        <li class="kt-nav__section kt-nav__section--first">
                                            <span class="kt-nav__section-text">Chọn hành động</span>
                                        </li>
                                        <li class="kt-nav__item">
                                            <a class="kt-nav__link export-excel"
                                               title="Xuất các bản ghi đang lọc ra file excel"
                                               onclick="$('input[name=export]').click();">
                                                <i class="kt-nav__link-icon la la-file-excel-o"></i>
                                                <span class="kt-nav__link-text">Xuất Excel</span>
                                            </a>
                                        </li>
                                        @if(in_array($module['code'].'_delete', $permissions))
                                            <li class="kt-nav__item">
                                                <a href="#" class="kt-nav__link" onclick="multiDelete();"
                                                   title="Xóa tất cả các dòng đang được tích chọn">
                                                    <i class="kt-nav__link-icon la la-copy"></i>
                                                    <span class="kt-nav__link-text">Xóa nhiều</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @if(in_array($module['code'].'_add', $permissions))
                                <a href="{{ url('/admin/'.$module['code'].'/add/') }}"
                                   class="btn btn-brand btn-elevate btn-icon-sm">
                                    <i class="la la-plus"></i>
                                    Tạo mới
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body kt-portlet-search @if(!isset($_GET['search'])) no-padding @endif">
                <!--begin: Search Form -->
                <form class="kt-form kt-form--fit kt-margin-b-20 form-search" id="form-search" method="GET" action=""
                      @if(!isset($_GET['search'])) style="display: none;" @endif>
                    <input name="search" type="hidden" value="true">
                    <input name="limit" type="hidden" value="{{ $limit }}"><input type="hidden" name="quick_search"
                                                                                  value="{{ @$_GET['quick_search'] }}"
                                                                                  id="quick_search_hidden"
                                                                                  class="form-control"
                                                                                  placeholder="Tìm kiếm nhanh">
                    <div class="row">

                        {{--                        @foreach($filter as $filter_name => $field)--}}
                        {{--                            <div class="col-sm-6 col-lg-3 kt-margin-b-10-tablet-and-mobile list-filter-item">--}}
                        {{--                                <label>{{ @trans($field['label']) }}:</label>--}}
                        {{--                                @include(config('core.admin_theme').'.list.filter.' . $field['type'], ['name' => $filter_name, 'field'  => $field])--}}
                        {{--                            </div>--}}
                        {{--                        @endforeach--}}
                        @foreach($filter as $filter_name => $field)
                            @if($field['type'] == 'custom')
                                <div class="col-sm-6 col-lg-3 kt-margin-b-10-tablet-and-mobile list-filter-item">
                                    <label>{{ @$field['label'] }}:</label>
                                    @include($field['field'], ['name' => $filter_name, 'field'  => $field])
                                </div>
                            @else
                                <div class="col-sm-6 col-lg-3 kt-margin-b-10-tablet-and-mobile list-filter-item">
                                    <label>{{ @$field['label'] }}:</label>
                                    @include(config('core.admin_theme').'.list.filter.' . $field['type'], ['name' => $filter_name, 'field'  => $field])
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-primary btn-brand--icon" id="kt_search" type="submit">
						<span>
							<i class="la la-search"></i>
							<span>Lọc</span>
						</span>
                            </button>
                            &nbsp;&nbsp;
                            <a class="btn btn-secondary btn-secondary--icon" id="kt_reset" title="Xóa bỏ bộ lọc"
                               href="/admin/{{ $module['code'] }}">
						<span>
							<i class="la la-close"></i>
							<span>Reset</span>
						</span>
                            </a>
                        </div>
                    </div>
                    <input name="export" type="submit" value="export" style="display: none;">
                    @foreach($module['list'] as $k => $field)
                        <input name="sorts[]" value="{{ @$_GET['sorts'][$k] }}"
                               class="sort sort-{{ $field['name'] }}" type="hidden">
                    @endforeach
                </form>
                <!--end: Search Form -->
            </div>
            <div class="kt-separator kt-separator--md kt-separator--dashed" style="margin: 0;"></div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <!--begin: Datatable -->
                <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded"
                     id="scrolling_vertical" style="">
                    <table class="table table-striped text-center">
                        <thead class="kt-datatable__head">
                        <tr class="kt-datatable__row" style="left: 0px;">
                            <th style="display: none;"></th>
                            <th data-field="id"
                                class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check"><span
                                        style="width: 20px;"><label
                                            class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input
                                                type="checkbox"
                                                class="checkbox-master">&nbsp;<span></span></label></span></th>
                            @if(@$_GET['view'] == 'all')
                                <th data-field="company_id"
                                    class="kt-datatable__cell kt-datatable__cell--sort">
                                    Công ty
                                </th>
                            @endif
                            @php $count_sort = 0; @endphp
                            @foreach($module['list'] as $field)
                                @if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['cvkd_parttime']) && $field['label'] == 'Sản phẩm')
                                    <th></th>
                                @else
                                    <th data-field="{{ $field['name'] }}"
                                        class="kt-datatable__cell kt-datatable__cell--sort {{ @$_GET['sorts'][$count_sort] != '' ? 'kt-datatable__cell--sorted' : '' }}"
                                        @if(isset($field['sort']))
                                            onclick="sort('{{ $field['name'] }}')"
                                            @endif
                                    >
                                        {{ trans($field['label'])}}
                                        @if(isset($field['sort']))
                                            @if(@$_GET['sorts'][$count_sort] == $field['name'].'|asc')
                                                <i class="flaticon2-arrow-up"></i>
                                            @else
                                                <i class="flaticon2-arrow-down"></i>
                                            @endif
                                        @endif

                                    </th>
                                @endif

                                @php $count_sort++; @endphp
                            @endforeach
                        </tr>
                        </thead>
                        <tbody class="kt-datatable__body ps ps--active-y" style="max-height: 496px;">
                        @foreach($listItem as $item)
                            <tr data-row="0" class="kt-datatable__row" style="left: 0px;">
                                <td style="display: none;"
                                    class="id id-{{ $item->id }}">{{ $item->id }}</td>
                                <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check"
                                    data-field="ID"><span style="width: 20px;"><label
                                                class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input
                                                    name="id[]"
                                                    type="checkbox" class="ids"
                                                    value="{{ $item->id }}">&nbsp;<span></span></label></span>
                                </td>
                                @if(@$_GET['view'] == 'all')
                                    <td data-field="company_name"
                                        class="kt-datatable__cell item-company_id">
                                        {{ @$item->company->name }}
                                    </td>
                                @endif
                                @foreach($module['list'] as $field)
                                    <td data-field="{{ @$field['name'] }}"
                                        class="kt-datatable__cell item-{{ @$field['name'] }}">
                                        @if($field['type'] == 'custom')
                                            @include($field['td'], ['field' => $field])
                                        @else
                                            @include(config('core.admin_theme').'.list.td.'.$field['type'])
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                        </div>
                        <div class="ps__rail-y" style="top: 0px; height: 496px; right: 0px;">
                            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 207px;"></div>
                        </div>
                        </tbody>
                    </table>
                    <div class="kt-datatable__pager kt-datatable--paging-loaded">
                        {!! $listItem->appends(isset($param_url) ? $param_url : '')->links() != '' ? $listItem->appends(isset($param_url) ? $param_url : '')->links() : '<ul class="pagination page-numbers nav-pagination links text-center"></ul>' !!}
                        <div class="kt-datatable__pager-info">
                            <div class="dropdown bootstrap-select kt-datatable__pager-size"
                                 style="width: 60px;">
                                <select class="selectpicker kt-datatable__pager-size select-page-size"
                                        onchange="$('input[name=limit]').val($(this).val());$('#form-search').submit();"
                                        title="Chọn số bản ghi hiển thị" data-width="60px"
                                        data-selected="20" tabindex="-98">
                                    <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20</option>
                                    <option value="30" {{ $limit == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <span class="kt-datatable__pager-detail">Hiển thị {{ (($page - 1) * $limit) + 1 }} - {{ ($page * $limit) < $record_total ? ($page * $limit) : $record_total }} của {{ @number_format($record_total) }}</span>
                        </div>
                    </div>
                </div>
                <!--end: Datatable -->


                {{--    include file pop_up.css--}}
                {{--    include file pop_up.css--}}
                <link rel="stylesheet" href="{{ asset('/backend/css/pop_up.css') }}">
                {{--    include font awesome 5.15.4 --}}
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
                      integrity="..." crossorigin="anonymous">
                {{--    include Jquery --}}
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
                <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <div class="modal fade modal-view-code" tabindex="-1" role="dialog" aria-hidden="true" id="myModal">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            {{--                            <div class="modal-header">--}}
                            {{--                                <h4 class="modal-title">Xem bảng hàng</h4>--}}
                            {{--                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>--}}
                            {{--                            </div>--}}
                            <div class="modal-body">
                                <!-- Carousel -->
                                <div class="container main">
                                    <div class="row">
                                        <div class="col-md-12 col-12">
                                            <div class="slider">
                                                <div class="slider__main">
                                                    <div
                                                            style="
                          --swiper-navigation-color: #fff;
                          --swiper-pagination-color: #fff;
                        "
                                                            class="swiper mySwiper2"
                                                    >
                                                        <div class="swiper-wrapper"
                                                             style="height: 540px;padding-bottom: 16px">

                                                        </div>
                                                        <div class="swiper-button-next"></div>
                                                        <div class="swiper-button-prev"></div>
                                                    </div>
                                                    <div thumbsSlider="" class="swiper mySwiper">
                                                        <div class="swiper-wrapper">

                                                        </div>
                                                    </div>


                                                </div>

                                                <p class="slider__name">
                                                    <!-- Trường address trong bảng Codes -->

                                                    <i class="fas fa-map-marker-alt fs-2"></i>
                                                    @if (!in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['cvkd_parttime']))
                                                        <span class="address"
                                                              style="color: #2d1c32;font-weight: bold;"></span> -
                                                    @endif
                                                    <span class="du_an"
                                                          style="color: #2d1c32;font-weight: bold;"></span> -
                                                    <span class="duong"
                                                          style="color: #2d1c32;font-weight: bold;"></span>
                                                </p>
                                            </div>
                                            <div class="info">
                                                <div class="box-price d-flex">
                                                    <div class="info__price mr-2" style="background-color: #0abb87">
                                                        <span class="price_title">HH:</span>
                                                        <span class="info__price__number">
                                          <!-- Hoa hồng -->
                                          <span class="phi_moi_gioi"></span>
                                        </span>
                                                    </div>
                                                    <div class="info__price">
                                                        <span class="price_title">Giá bán:</span>
                                                        <span class="info__price__number">
                                          <!-- Giá bán -->
                                          <span class="price_setup"></span>
                                        </span>
                                                    </div>
                                                </div>


                                                <div class="info__main d-flex justify-content-between align-items-center">
                                                    <div class="info__left">

                                                        <div class="info__left__bottom d-flex justify-content-between align-items-center pt-4">
                                                            <div class="info__room d-flex align-items-center justify-content-center gap-3">
                                                                <div
                                                                        class="info__room__chitiet d-flex align-items-center justify-content-center flex-column gap-1">
                            <span class="info__svg">
                              <i class="fas fa-home custom"></i>
                            </span>

                                                                    <span class="info__number">
                              <!-- trường diện tích -->
                              <span class="dien_tich"></span>m<sup>2</sup>
                            </span>
                                                                </div>
                                                                <div
                                                                        class="info__room__chitiet d-flex align-items-center justify-content-center flex-column gap-2">
                            <span class="info__svg">
                              <i class="fas fa-bed custom"></i>
                            </span>
                                                                    <span class="info__number">
                              <!-- trường so_phong_ngu -->
                              <span class="so_phong_ngu"></span> PN
                            </span>
                                                                </div>
                                                                <div
                                                                        class="info__room__chitiet d-flex align-items-center justify-content-center flex-column gap-1">
                            <span class="info__svg">
                              <i class="fas fa-bath custom"></i>
                            </span>
                                                                    <span class="info__number">
                              <!-- trường số nhà vệ sinh -->
                              <span class="so_nha_ve_sinh">2</span> WC
                            </span>
                                                                </div>
                                                            </div>
                                                            <div class="info__day">
                                                                <span><b>Ngày tạo:</b></span><br/>
                                                                <i class="far fa-clock"></i>
                                                                <span class="info__time">
                            <!-- Ngày tạo -->
                            <span class="created_at"></span>
                          </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="info__right d-flex flex-column gap-4 pt-4 ps-4">
                                                        <div class="info__left__top d-flex align-items-end">
                                                            <div
                                                                    class="btn bg-success d-flex align-items-center justify-content-center text-white p-3 fs-3 text-uppercase bao-cao"
                                                                    style="font-size: 20px; font-weight: bold">
                                                                <i class="fas fa-solid fa-file-code"
                                                                   style="padding-right: 15px"></i> <span
                                                                        class="ma_tin"></span>
                                                            </div>
                                                            <a href="{{url('/admin/bao_cao_dan_khach/add')}}"
                                                               class="btn info__baocao d-flex align-items-center justify-content-center text-white p-3 fs-3 text-uppercase bao-cao"
                                                               style="font-size: 20px; font-weight: bold">
                                                                <i class="fas fa-address-book"></i>
                                                                Báo cáo dẫn khách
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="info__mota">
                                                <p class="info__text">Thông tin cơ bản</p>
                                                <div class="border__1"></div>
                                                <div class="border__2"></div>
                                                <div class="info__mota__list">
                                                    <!-- Nội dung chi tiết: trường content -->
                                                    <b class="h5">Nội dung chi tiết:</b>
                                                    <p class="content"></p>
                                                </div>
                                            </div>
                                            <div class="info__chitiet">
                                                <p class="info__text">Chi tiết</p>
                                                <div class="border__1"></div>
                                                <div class="border__2"></div>
                                                <div class="info__chitiet__list">
                                                    <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                                        <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                            <div class="info__chitiet__image">
                                                                <i class="fas fa-home"></i>
                                                            </div>
                                                            <span>Loại hình</span>
                                                        </div>
                                                        <!-- loại hình -->
                                                        <div class="info__chitiet__dataInfo"><span
                                                                    class="loai_hinh  text-primary"></span>
                                                        </div>
                                                        <!--  -->
                                                    </div>
                                                    <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                                        <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                            <div class="info__chitiet__image">
                                                                <i class="fas fa-home"></i>
                                                            </div>
                                                            <span>Loại nhà đất</span>
                                                        </div>
                                                        <!-- loại nhà đất -->
                                                        <div class="info__chitiet__dataInfo"><span
                                                                    class="loai_nha_dat text-primary"></span>
                                                        </div>
                                                        <!--  -->
                                                    </div>
                                                    <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                                        <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                            <div class="info__chitiet__image">
                                                                <i class="fas fa-square"></i>
                                                            </div>
                                                            <span>Diện tích</span>
                                                        </div>
                                                        <!-- dien tich -->
                                                        <div class="info__chitiet__dataInfo"><span
                                                                    class="dien_tich text-primary"></span>m<sup>2</sup></div>
                                                        <!--  -->
                                                    </div>

                                                    <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                                        <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                            <div class="info__chitiet__image">
                                                                <i class="fas fa-hand-holding-usd"></i>
                                                            </div>
                                                            <span>Phí môi giới</span>
                                                        </div>
                                                        <!-- Phí môi giới -->
                                                        <div class="info__chitiet__dataInfo"><span
                                                                    class="phi_moi_gioi text-primary"></span>
                                                        </div>
                                                        <!--  -->
                                                    </div>
                                                    <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                                        <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                            <div class="info__chitiet__image">
                                                                <i class="far fa-building"></i>
                                                            </div>
                                                            <span>Khoảng tầng</span>
                                                        </div>
                                                        <!-- Khoảng tầng -->
                                                        <div class="info__chitiet__dataInfo"><span
                                                                    class="khoang_tang text-primary"></span></div>
                                                        <!--  -->
                                                    </div>
                                                    <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                                        <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                            <div class="info__chitiet__image d-flex align-items-center">
                                                                <i class="fas fa-bed"></i>
                                                            </div>
                                                            <span>Số phòng ngủ</span>
                                                        </div>
                                                        <!-- Số phòng ngủ -->
                                                        <div class="info__chitiet__dataInfo"><span
                                                                    class="so_phong_ngu text-primary"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{--                                DIV THÔNG TIN LIÊN HỆ ĐẦU CHỦ--}}
                                            <div class="name">
                                                <p class="info__text">Thông tin liên hệ đầu chủ</p>
                                                <div class="border__1"></div>
                                                <div class="border__2"></div>
                                                <div class="name__div d-flex justify-content-around row  my-5">
                                                    <div class="name__image d-flex align-items-center col-md-7">
                                                        <img src=""
                                                             width="100"
                                                             height="100" alt=""
                                                             srcset="" class="anhDauChu">
                                                        <!-- Họ tên đầu chủ, trường intro -->
                                                        <span class="name__text intro_owner h2"></span>
                                                    </div>


                                                    <div class="name__info d-flex flex-column col-md-5">
                                      <span class="contact">
                                          <i class="fas fa-envelope h5 icon-contact"></i>
                                          <!-- Địa chỉ -->
                                        <span class="email_owner h5 ml-2 text-primary"></span>
                                      </span>
                                                        <!-- sđt -->
                                                        <span class="contact">
                                            <i class="fas fa-mobile-alt h5 icon-contact"></i>
                                            <span class="phone_owner h5 ml-2 text-primary"></span>
                                          </span>


                                                        <span class="contact">
                                        <i class="fas fa-house-user h5 icon-contact"></i>
                                                            <!-- trường sdt_chu_nha -->
                                            <span class="room_owner h5 ml-2 text-primary"></span>
                                          </span>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="price d-flex align-items-center justify-content-around">
                                                <div class="price__left d-flex align-items-center">
                                                    <p>Giá bán:</p>
                                                    <p class="price__number">
                                                        <span class="price_setup"></span>
                                                    </p>
                                                </div>
                                                <div class="price__button d-flex align-items-center">
                                                    <div class="price__image">
                                                        <div class="price__logo d-flex align-items-center justify-content-center">
                                                            <div class="price__logo2 d-flex align-items-center justify-content-center"
                                                                 data-dismiss="modal" aria-label="Close"
                                                                 style="cursor: pointer">
                                                                <svg xmlns="http://www.w3.org/2000/svg" height="16"
                                                                     width="16"
                                                                     viewBox="0 0 448 512">
                                                                    <!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a id="bao-cao" href="{{url('/admin/bao_cao_dan_khach/add')}}"
                                                       class="price__right bao-cao">
                                                        Báo cáo dẫn khách
                                                    </a>
                                                </div>
                                            </div>
                                            {{--                                END DIV THÔNG TIN LIÊN HỆ ĐẦU CHỦ --}}

                                            {{--                                DIV THÔNG TIN LIÊN HỆ CHỦ NHÀ --}}

                                            <div class="name" id="contactInfo">

                                                <p class="info__text">Thông tin liên hệ chủ nhà</p>
                                                <div class="border__1"></div>
                                                <div class="border__2"></div>
                                                <div class="name__div d-flex justify-content-around row">
                                                    <div class="name__image d-flex align-items-center col-md-7">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/219/219983.png"
                                                             width="100"
                                                             height="100" alt=""
                                                             srcset="">
                                                        <!-- Họ tên chủ nhà, trường intro -->
                                                        <span class="name__text intro h2"></span>
                                                    </div>
                                                    <div class="name__info d-flex flex-column col-md-5">
                      <span class="contact">
                        <i class="fas fa-map-marker-alt h5 icon-contact"></i>
                          <!-- Địa chỉ trên sổ, trường dia_chi_tren_so -->
                        <span class="dia_chi_tren_so h5 ml-2 text-primary"></span>
                      </span>
                                                        <!-- sđt -->
                                                        <span class="contact">
                        <i class="fas fa-mobile-alt h5 icon-contact"></i>
                                                            <!-- trường sdt_chu_nha -->
                        <span class="sdt_chu_nha h5 ml-2 text-primary"></span>
                      </span>
                                                        <!-- trường số giấy chứng nhận (đang hiện trên frontend: số seri sổ) -->
                                                        <span class="h5">
                        Số seri: <span class="so_giay_chung_nhan h5  text-primary"></span>
                      </span>
                                                        <!-- trường seri (đang hiện trên frontend: số hợp đồng mua bán)  -->
                                                        <span class="h5">
                        Số giấy chứng nhận: <span class="seri  text-primary"></span>
                      </span>
                                                    </div>
                                                </div>
                                                <div class="row imageRedBooks my-5">
                                                    <p class="imglist">

                                                    </p>
                                                </div>
                                            </div>
                                            {{--                                END DIV THÔNG TIN LIÊN HỆ CHỦ NHÀ--}}

                                        </div>

                                    </div>

                                </div>

                                <!-- End Carousel -->
                            </div>
                        </div>
                    </div>
                </div>

                @endsection

                @section('custom_head')
                    <link
                            rel="stylesheet"
                            href="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css"
                    />
                    <link type="text/css" rel="stylesheet" charset="UTF-8"
                          href="{{ asset(config('core.admin_asset').'/css/list.css') }}">

                    <style>
                        .modal-content {
                            width: 900px;
                            margin: auto;
                        }

                        .item-id button {
                            width: 150px;
                        }

                        .btn-view {
                            text-align: start;
                        }

                        .modal-content {
                            width: 900px;
                            margin: auto;
                        }

                        .item-address {
                            width: 270px;
                            text-align: start !important;
                        }

                        .info__day {
                            padding-left: 24px;
                        }
                        .baocao {
                            text-align: start;
                            width: 220px;
                        }

                        @media (max-width: 991.98px) {
                            .modal-dialog {
                                margin: 0;
                            }

                            .modal-content {
                                max-width: 150% !important;
                                margin: 50px 33px;
                            }
                        }

                        @media (max-width: 435px) {
                            .file_image_thumb {
                                height: 100px !important;
                            }

                            .box-price {
                                justify-content: space-between;
                                align-items: center;
                            }

                            .box-active {
                                text-align: center;
                            }

                            .item-address {
                                text-align: start !important;
                            }

                            .item-address button {
                                width: 150px;
                            }

                            .btn-view {
                                text-align: start;
                            }

                            .item-hanh_dong {
                                width: 150px;
                                display: flex;
                                flex-direction: column;
                            }


                            .modal-content {
                                width: 100%;
                                margin: auto;
                            }

                            .swiper-slide.image-small {
                                height: 83px !important;
                            }

                            .swiper-wrapper.swiper.mySwiper2 {
                                padding-bottom: 13px;
                            }

                            .swiper.mySwiper.swiper-initialized,
                            .swiper-slide.image-small img {
                                height: 83px !important;
                            }

                            .swiper-wrapper {
                                height: 540px !important;
                                padding-bottom: 16px;
                            }

                            .swiper.mySwiper2.swiper-initialized.swiper-horizontal.swiper-ios.swiper-backface-hidden {
                                height: 300px !important;
                                margin-bottom: 10px;
                            }

                            .slider__main {
                                width: 100% !important;
                                height: 100% !important;

                            }


                            .modal.modal-view-code {
                                padding-bottom: 50px !important;
                            }

                            .modal-body {
                                padding: 0;
                            }
                            .info__price {
                                width: 48% !important;
                            }

                            .info__price__number span {
                                font-size: 22px !important;
                            }


                            .price_title {
                                font-size: 17px !important;
                            }

                            .info__main {
                                display: flex;
                                flex-direction: column;
                                align-items: normal;
                            }


                            .info__right {
                                width: 100% !important;
                            }

                            .info__left {
                                width: 100%;

                            }

                            .fa-file-code {
                                padding-right: 0
                            }

                            .info__left__top {
                                display: flex;
                                justify-content: space-between;
                                gap: 0 !important;
                            }

                            .info__price__number {
                                font-size: 25px !important;
                            }

                            .info__day span b {

                                font-size: 17px !important;
                            }

                            /*.info__time {*/
                            /*    font-size: 15px !important;*/
                            /*}*/
                            .info__main {
                                gap: 0px !important;
                            }

                            .info__number {
                                width: 40px !important;
                            }

                            .info__chitiet__list {
                                grid-gap: 18px !important;
                            }

                            .swiper-slide.swiper-slide-active img {
                                width: 100%;
                                height: 500px;
                            }

                            .price__number {
                                font-size: 28px !important;
                                margin-left: 20px !important;
                            }

                            .name__image.d-flex.align-items-center.col-md-7 {
                                justify-content: flex-start !important;
                                padding: 0 10px
                            }

                            .name__info.col-md-5 {
                                padding-left: 30px;
                                padding-top: 16px;
                            }

                            .contact {
                                padding: 5px 0;
                            }

                            .icon-contact {
                                width: 20px;
                                text-align: center;
                            }

                            .info__room {
                                gap: 9px !important;
                                text-align: center;
                            }

                            .price {

                                position: fixed;
                                bottom: 0;
                                right: 0;
                                left: 0;
                                z-index: 999;

                            }
                            .image-redbook {
                                width: 100px !important;

                                height: 70px !important;
                            }
                            .box-imageRed.col-3{
                                max-width: 100%;
                                flex: none;
                            }
                            .baocao {
                                text-align: start;
                                width: 120px;
                            }
                        }

                    </style>
                    {{--    <link type="text/css" rel="stylesheet" charset="UTF-8" href="{{ asset('Modules\WebService\Resources\assets\css\custom.css') }}">--}}
                    {{--    <script src="{{asset('Modules\WebService\Resources\assets\js\custom.js')}}"></script>--}}
                @endsection
                @section('custom_footer')
                    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
                            type="text/javascript"></script>
                    <script src="{{ asset(config('core.admin_asset').'/js/list.js') }}"></script>
                    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

                    @include(config('core.admin_theme').'.partials.js_common')
                @endsection
                @push('scripts')
                    <script>
                        var swiper = new Swiper(".mySwiper", {
                            spaceBetween: 10,
                            slidesPerView: 4,
                            freeMode: true,
                            watchSlidesProgress: true,
                        });
                        var swiper2 = new Swiper(".mySwiper2", {
                            spaceBetween: 10,
                            navigation: {
                                nextEl: ".swiper-button-next",
                                prevEl: ".swiper-button-prev",
                            },
                            thumbs: {
                                swiper: swiper,
                            },
                        });
                    </script>
                    @include(config('core.admin_theme').'.partials.js_common_list')

                    <script>
                        $(document).ready(function () {

                            function loadAjaxModal(id) {
                                var luot_xem = $('[data-row="' + id + '"][data-field="luot_xem"]');
                                var newUrl = window.location.href + '?id=' + id;
                                var baseUrl = window.location.origin + "/admin/bao_cao_dan_khach";

                                var getID = new URLSearchParams(window.location.search).get('id');
                                if (getID == null) {
                                    var newUrl = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'id=' + id;
                                    console.log(newUrl);
                                } else {
                                    var newUrl = window.location.origin + "/admin/bao_cao_dan_khach" + '?id=' + id;
                                    console.log(newUrl);
                                }

                                $.ajax({
                                    url: '/admin/bao_cao_dan_khach/ajax-get-info/' + id,
                                    type: 'GET',
                                    success: function (res) {
                                        var response = res.data;
                                        console.log(response)
                                        console.log(res.show);
                                        if (res.show) {
                                            $('#contactInfo').show();
                                        } else {
                                            $('#contactInfo').hide();
                                        }
                                        $('.email_owner').html(res.dauchu.email);
                                        $('.intro_owner').html(res.dauchu.name);
                                        $('.phone_owner').html(res.dauchu.tel);
                                        $('.room_owner').html(res.phongban.name);


                                        $('.modal-view-code').modal('show');
                                        $('.loai_hinh').html(response.loai_hinh);
                                        $('.ma_tin').html(response.id);
                                        $('.loai_nha_dat').html(response.loai_nha_dat);
                                        $('.du_an').html(res.service);
                                        $('.bao-cao').attr('href', '/admin/bao_cao_dan_khach/add?code_id=' + response.id);
                                        if (response.image == null) {
                                            $('.image').attr('src', 'https://sehouse.khoweb.top/filemanager/userfiles/_thumbs/se-house-logo-100x.jpg');
                                        } else {
                                            $('.image').attr('src', response.image);
                                        }
                                        if (res.anhDauChu) {
                                            $('.anhDauChu').attr('src', res.anhDauChu);
                                        } else {
                                            $('.anhDauChu').attr('src', 'https://cdn-icons-png.flaticon.com/512/219/219983.png');
                                        }
                                        if (response.image_extra == null) {
                                            $('.image_extra').html('<img src="https://sehouse.khoweb.top/filemanager/userfiles/_thumbs/se-house-logo-100x.jpg" alt="" class="image_extra" style="width: 100px;height: 100px;">');
                                        } else {
                                            var image_extra = response.image_extra.split('|');
                                            var html = '';
                                            for (var i = 0; i < image_extra.length; i++) {
                                                html += '<img src="https://sehouse.khoweb.top/filemanager/userfiles/' + image_extra[i] + '" alt="" class="image_extra" style="width: 100px;height: 100px;">';
                                            }
                                            $('.image_extra').html(html);
                                        }
                                        $('.address').html(response.address);
                                        $('.duong').html(response.duong);
                                        $('.intro').html(response.intro);
                                        $('.sdt_chu_nha').html(response.sdt_chu_nha);
                                        $('.so_giay_chung_nhan').html(response.so_giay_chung_nhan);
                                        $('.row_id' + response.id + ' .item-luot_xem').html(response.luot_xem);
                                        $('.gia_ha_chao').html(formatValue(response.gia_ha_chao));
                                        $('.price_setup').html(formatValue(response.gia_niem_yet));
                                        $('.dien_tich').html(response.dien_tich);
                                        $('.mat_tien').html(response.mat_tien);
                                        $('.so_tang').html(response.so_tang);
                                        $('.phi_moi_gioi').html(formatValue(response.phi_moi_gioi));

                                        $('.khoang_tang').html(response.khoang_tang);
                                        $('.so_phong_ngu').html(response.so_phong_ngu);
                                        $('.content').html(response.content);
                                        $('.created_at').html(response.created_at);

                                        $('.dia_chi_tren_so').html(response.dia_chi_tren_so);
                                        $('.img-main').attr('src', res.imagePath);
                                        $('.info__copy').val(newUrl);
                                        console.log(res.imagePaths);

                                        $('.mySwiper2 .swiper-wrapper').empty();

                                        $('.mySwiper .swiper-wrapper').empty();

                                        $.each(res.imagePaths, function (index, path) {
                                            $('.mySwiper2 .swiper-wrapper').append(
                                                '<div class="swiper-slide">' +
                                                '<img src="' + path + '" alt="Slide ' + (index + 1) + '">' +
                                                '</div>'
                                            );
                                        });

                                        $.each(res.imagePaths, function (index, path) {
                                            $('.mySwiper .swiper-wrapper').append(
                                                '<div class="swiper-slide image-small">' +
                                                '<img src="' + path + '" alt="Thumb ' + (index + 1) + '">' +
                                                '</div>'
                                            );
                                        });

                                        var imageRedBooksContainer = $('.imglist');
                                        imageRedBooksContainer.empty();
                                        console.log(res.imageRedBooks);

                                        res.imageRedBooks.forEach(function (path) {
                                            var html =
                                                '<a href="' + path + '" data-fancybox="group" data-caption="Image caption" class="col-md-3 col-3 box-imageRed" >' +
                                                '<img src="' + path + '" class="image-redbook py-2" style="cursor: pointer;height: 200px;width: auto">' +
                                                '</a>';
                                            imageRedBooksContainer.append(html);
                                        });

                                        // Initialize Fancybox on the entire .imglist container
                                        $("[data-fancybox]").fancybox({
                                            buttons: ["close"],
                                            wheel: false,
                                            transitionEffect: "slide",
                                            loop: true,
                                            toolbar: false,
                                            clickContent: false,
                                        });

                                        var mySwiper2 = new Swiper('.mySwiper2', {
                                            navigation: {
                                                nextEl: '.swiper-button-next',
                                                prevEl: '.swiper-button-prev',
                                            },
                                            pagination: {
                                                el: '.swiper-pagination',
                                                clickable: true,
                                            },
                                        });

                                        var mySwiper = new Swiper('.mySwiper', {
                                            slidesPerView: 5,
                                            spaceBetween: 10,
                                            navigation: {
                                                nextEl: '.swiper-button-next',
                                                prevEl: '.swiper-button-prev',
                                            },
                                            breakpoints: {
                                                640: {
                                                    slidesPerView: 2,
                                                    spaceBetween: 5,
                                                },
                                                768: {
                                                    slidesPerView: 4,
                                                    spaceBetween: 10,
                                                },
                                            },
                                        });

                                        mySwiper2.controller.control = mySwiper;
                                        mySwiper.controller.control = mySwiper2;


                                    }
                                });

                            }

                            $('.btn-view').click(function () {
                                var id = $(this).data('id')
                                loadAjaxModal(id)
                                window.history.pushState("object or string", "Title", newUrl);
                                $("#myModal").modal("show");

                            });

                            function formatPrice(price) {
                                return new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND'
                                }).format(price);
                            }

                            function formatValue(inputValue) {
                                if (inputValue >= 1000000000) {
                                    return (inputValue / 1000000000) + ' tỷ';
                                } else if (inputValue >= 1000000) {
                                    return (inputValue / 1000000) + ' triệu';
                                } else {
                                    return inputValue + ' đ';
                                }

                            }

                            //  tu dong show popup khi dan link
                            @if(@$_GET['id'] != null)
                            var id = '{{ @$_GET['id'] }}';
                            loadAjaxModal(id)
                            $("#myModal").modal("show");

                            @endif
                            document.getElementById("copyButton").addEventListener("click", function () {
                                // Chọn và sao chép giá trị của nút vào clipboard
                                var valueToCopy = this.value;
                                console.log(1);
                                navigator.clipboard
                                    .writeText(valueToCopy)
                                    .then(function () {
                                        console.log("Đã sao chép thành công: " + valueToCopy);
                                    })
                                    .catch(function (err) {
                                        console.error("Lỗi khi sao chép: ", err);
                                    });
                            });
                        });


                        $(document).on('click', '.file_image_thumb1111', function () {
                            var id = $(this).data('id');
                            $('#imageGallery').remove();

                            $.ajax({
                                url: '/admin/bao_cao_dan_khach/ajax-get-image/' + id,
                                type: 'GET',
                            })
                                .done(function (res) {
                                    if (res && res.fullPaths && res.fullPaths.length > 0) {
                                        var imageArray = res.fullPaths;
                                        var baseUrl = "<?php echo asset('/filemanager/userfiles/'); ?>";
                                        var html = '<div id="imageGallery" class="modal" tabindex="-1" role="dialog">';
                                        html += '<div class="modal-dialog" role="document">';
                                        html += '<div class="modal-content bbbbbbbb">';
                                        html += '<div class="modal-body">';
                                        html += '<div class="row">';
                                        for (var i = 0; i < imageArray.length; i++) {
                                            html += '<div class="col-md-4">';
                                            html += '<img src="' + imageArray[i] + '" class="gallery-image" style="width: 100%; height: 100%" />';
                                            html += '</div>';
                                        }
                                        html += '</div>';
                                        html += '</div>';
                                        html += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                                        html += '</div>';
                                        html += '</div>';
                                        html += '</div>';

                                        $('body').append(html);
                                        $('#imageGallery').modal();
                                    }
                                });
                        });

                    </script>
    @endpush
