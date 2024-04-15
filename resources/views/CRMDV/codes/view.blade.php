@extends('CRMDV.codes.new_header.new_template')
@section('main')
    111
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
                                   class="form-control" title="Chỉ cần enter để thực hiện tìm kiếm"
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
                                    </ul>
                                </div>
                            </div>
                            <a href="{{ url('/admin/'.$module['code'].'/add/') }}"
                               class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="la la-plus"></i>
                                Tạo mới
                            </a>
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

                        @foreach($filter as $filter_name => $field)
                            @if($field['type'] == 'custom')
                                <div class="col-sm-6 col-lg-3 kt-margin-b-10-tablet-and-mobile list-filter-item">
                                    <label>{{ @$field['label'] }}:</label>
                                    @include($field['field'], ['name' => $filter_name, 'field'  => $field])
                                </div>
                            @elseif($field['type'] == 'location')
                                <div class="col-sm-6 col-lg-12 kt-margin-b-10-tablet-and-mobile list-filter-item">
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

            <div class="kt-separator kt-separator--md kt-separator--dashed" style="margin: 0;">
            </div>

            <div class="kt-portlet__body kt-portlet__body--fit">
                <!--begin: Datatable -->

                <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded"
                     id="scrolling_vertical" style="">
                    <table class="table table-striped">
                        <thead class="kt-datatable__head">
                        <tr class="kt-datatable__row" style="left: 0px;">
                            <th style="display: none;"></th>
                            <th data-field="id"
                                class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check"><span
                                        style="width: 20px;"><label
                                            class="kt-checkbox kt-checkbox--single kt-checkbox--all kt-checkbox--solid"><input
                                                type="checkbox"
                                                class="checkbox-master">&nbsp;<span></span></label></span></th>

                            @php $count_sort = 0; @endphp
                            @foreach($module['list'] as $field)
                                <th data-field="{{ $field['name'] }}"
                                    class="kt-datatable__cell kt-datatable__cell--sort {{ @$_GET['sorts'][$count_sort] != '' ? 'kt-datatable__cell--sorted' : '' }}"
                                    @if(isset($field['sort']))
                                        onclick="sort('{{ $field['name'] }}')"
                                        @endif
                                >
                                    {{ $field['label'] }}
                                    @if(isset($field['sort']))
                                        @if(@$_GET['sorts'][$count_sort] == $field['name'].'|asc')
                                            <i class="flaticon2-arrow-up"></i>
                                        @else
                                            <i class="flaticon2-arrow-down"></i>
                                        @endif
                                    @endif

                                </th>
                                @php $count_sort++; @endphp
                            @endforeach
                        </tr>
                        </thead>
                        <tbody class="kt-datatable__body ps ps--active-y" style="max-height: 496px;">
                        @foreach($listItem as $item)
                            <tr data-row="{{ $item->id }}" class="kt-datatable__row  row_id{{ $item->id }}"
                                style="left: 0px;">
                                <td style="display: none;"
                                    class="id id-{{ $item->id }}">{{ $item->id }}</td>
                                <td class="kt-datatable__cell--center kt-datatable__cell kt-datatable__cell--check"
                                    data-field="ID"><span style="width: 20px;"><label
                                                class="kt-checkbox kt-checkbox--single kt-checkbox--solid"><input
                                                    name="id[]"
                                                    type="checkbox" class="ids"
                                                    value="{{ $item->id }}">&nbsp;<span></span></label></span>
                                </td>

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
            </div>
        </div>
    </div>

    {{--    include file pop_up.css--}}
    <link rel="stylesheet" href="{{ asset('/backend/css/pop_up.css') }}">
    {{--    include font awesome 5.15.4 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
          integrity="..." crossorigin="anonymous">
    {{--    include Jquery --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <div class="modal fade modal-view-code" tabindex="-1" role="dialog" aria-hidden="true" id="myModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="width: 1200px; margin-left: -200px">
                <div class="modal-header">
                    <h4 class="modal-title">Xem bảng hàng</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Carousel -->
                    <div class="container main">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="slider">
                                    <div class="slider__main">
                                        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                            <div id="myCarousel" class="carousel-inner" style="height: 600px">
                                                <!-- Slides will be dynamically added here -->
                                            </div>

                                            <a class="carousel-control-prev" href="#carouselExampleIndicators"
                                               role="button"
                                               data-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="carousel-control-next" href="#carouselExampleIndicators"
                                               role="button"
                                               data-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
                                        </div>

                                        <div class="row mt-4 slide-image">
                                        </div>


                                    </div>

                                    <p class="slider__name">
                                        <!-- Trường address trong bảng Codes -->
                                        <span class="address"></span>
                                    </p>
                                </div>
                                <div class="info">
                                    <div class="info__main d-flex justify-content-between align-items-center">
                                        <div class="info__left">
                                            <div class="info__left__top d-flex justify-content-between align-items-center">
                                                <a href="" class="info__no">
                                                    <!-- trường id trong bảng Codes -->
                                                    <span class="id"></span>
                                                </a>
                                                <a id="bao-cao-dan-khach" href="{{url('/admin/bao_cao_dan_khach/add')}}"
                                                   class="info__baocao d-flex align-items-center justify-content-center text-white p-2">
                                                    <i class="fas fa-address-book"></i>
                                                    Báo cáo dẫn khách
                                                </a>
                                                <a href=""
                                                   class="info__copy d-flex align-items-center justify-content-center text-white p-2">
                                                    <i class="far fa-copy"></i>
                                                    Copy link
                                                </a>
                                            </div>
                                            <div class="info__left__bottom d-flex justify-content-between align-items-center pt-4">
                                                <div class="info__room d-flex align-items-center justify-content-center gap-3">
                                                    <div
                                                            class="info__room__chitiet d-flex align-items-center justify-content-center flex-column gap-1">
                            <span class="info__svg">
                              <i class="fas fa-home custom"></i>
                            </span>

                                                        <span class="info__number">
                              <!-- trường diện tích -->
                              <span class="dien_tich"></span>
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
                                            <div class="info__price">
                                                <span>Giá bán:</span><br/>
                                                <span class="info__price__number">
                          <!-- Giá bán -->
                          <span class="price_setup"></span>
                        </span>
                                            </div>
                                            <a href="{{url('/admin/bao_cao_dan_khach/add')}}"
                                               class="info__home bg-primary text-white d-flex align-items-center justify-content-center p-2">
                                                <i class="fas fa-house-user"></i>
                                                <b>Xem nhà</b>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                                <div class="info__mota">
                                    <p class="info__text">Thông tin cơ bản</p>
                                    <div class="border__1"></div>
                                    <div class="border__2"></div>
                                    <div class="info__mota__list">
                                        <!-- Giá bán -->
                                        <b>Giá:</b>
                                        <p class="price_setup"></p>
                                        <!-- phí môi giới  -->
                                        <b>Phí môi giới:</b>
                                        <p class="phi_moi_gioi"></p>
                                        <!-- Nội dung chi tiết: trường content -->
                                        <b>Nội dung chi tiết:</b>
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
                                            <div class="info__chitiet__dataInfo"><span class="loai_hinh">ggg</span>
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
                                            <div class="info__chitiet__dataInfo"><span class="loai_nha_dat"></span>
                                            </div>
                                            <!--  -->
                                        </div>
                                        <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                            <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                <div class="info__chitiet__image">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                                <span>Dự án</span>
                                            </div>
                                            <!-- dự án -->
                                            <div class="info__chitiet__dataInfo"><span class="du_an"></span></div>
                                            <!--  -->
                                        </div>
                                        <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                            <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                <div class="info__chitiet__image">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </div>
                                                <span>Địa chỉ</span>
                                            </div>
                                            <!-- address -->
                                            <div class="info__chitiet__dataInfo"><span class="address"></span></div>
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
                                            <div class="info__chitiet__dataInfo"><span class="dien_tich"></span></div>
                                            <!--  -->
                                        </div>
                                        <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                            <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                <div class="info__chitiet__image">
                                                    <i class="far fa-square"></i>
                                                </div>
                                                <span>Mặt tiền</span>
                                            </div>
                                            <!-- Mặt tiền -->
                                            <div class="info__chitiet__dataInfo"><span class="mat_tien"></span></div>
                                            <!--  -->
                                        </div>
                                        <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                            <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                <div class="info__chitiet__image">
                                                    <i class="far fa-building"></i>
                                                </div>
                                                <span>Số tầng</span>
                                            </div>
                                            <!-- Số tầng -->
                                            <div class="info__chitiet__dataInfo"><span class="so_tang"></span></div>
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
                                            <div class="info__chitiet__dataInfo"><span class="phi_moi_gioi"></span>
                                            </div>
                                            <!--  -->
                                        </div>
                                        <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                            <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                <div class="info__chitiet__image">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                                <span>Toà</span>
                                            </div>
                                            <!-- Toà -->
                                            <div class="info__chitiet__dataInfo"><span class="toa"></span></div>
                                            <!--  -->
                                        </div>
                                        <div class="info__chitiet__data d-flex justify-content-between align-items-center">
                                            <div class="info__chitiet__dataName d-flex justify-content-between align-items-center">
                                                <div class="info__chitiet__image">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                                <span>Tầng</span>
                                            </div>
                                            <!-- Tầng -->
                                            <div class="info__chitiet__dataInfo"><span class="tang"></span></div>
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
                                            <div class="info__chitiet__dataInfo"><span class="khoang_tang"></span></div>
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
                                            <div class="info__chitiet__dataInfo"><span class="so_phong_ngu"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="name">
                                    <p class="info__text">Thông tin liên hệ</p>
                                    <div class="border__1"></div>
                                    <div class="border__2"></div>
                                    <div class="name__div d-flex justify-content-around">
                                        <div class="name__image d-flex align-items-center">
                                            <img src="https://cdn-icons-png.flaticon.com/512/219/219983.png" width="100"
                                                 height="100" alt=""
                                                 srcset="">
                                            <!-- Họ tên chủ nhà, trường intro -->
                                            <span class="name__text intro"></span>
                                        </div>
                                        <div class="name__info d-flex flex-column">
                      <span>
                        <i class="fas fa-map-marker-alt"></i>
                          <!-- Địa chỉ trên sổ, trường dia_chi_tren_so -->
                        <span class="dia_chi_tren_so"></span>
                      </span>
                                            <!-- sđt -->
                                            <span>
                        <i class="fas fa-mobile-alt"></i>
                                                <!-- trường sdt_chu_nha -->
                        <span class="sdt_chu_nha"></span>
                      </span>
                                            <!-- trường số giấy chứng nhận (đang hiện trên frontend: số seri sổ) -->
                                            <span>
                        Số seri: <span class="so_giay_chung_nhan"></span>
                      </span>
                                            <!-- trường seri (đang hiện trên frontend: số hợp đồng mua bán)  -->
                                            <span>
                        Số giấy chứng nhận: <span class="seri"></span>
                      </span>
                                        </div>
                                    </div>
                                </div>
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
                                    <div class="price__logo2 d-flex align-items-center justify-content-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16"
                                             viewBox="0 0 512 512">
                                            <!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2023 Fonticons, Inc.-->
                                            <path
                                                    d="M96 0C60.7 0 32 28.7 32 64V448c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H96zM208 288h64c44.2 0 80 35.8 80 80c0 8.8-7.2 16-16 16H144c-8.8 0-16-7.2-16-16c0-44.2 35.8-80 80-80zm-32-96a64 64 0 1 1 128 0 64 64 0 1 1 -128 0zM512 80c0-8.8-7.2-16-16-16s-16 7.2-16 16v64c0 8.8 7.2 16 16 16s16-7.2 16-16V80zM496 192c-8.8 0-16 7.2-16 16v64c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm16 144c0-8.8-7.2-16-16-16s-16 7.2-16 16v64c0 8.8 7.2 16 16 16s16-7.2 16-16V336z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <a href="{{url('/admin/bao_cao_dan_khach/add')}}" class="price__right">
                                Báo cáo dẫn khách
                            </a>
                        </div>
                    </div>
                    <!-- End Carousel -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/list.css') }}">
    {{--    <link type="text/css" rel="stylesheet" charset="UTF-8" href="{{ asset('Modules\WebService\Resources\assets\css\custom.css') }}">--}}
    {{--    <script src="{{asset('Modules\WebService\Resources\assets\js\custom.js')}}"></script>--}}
@endsection
@section('custom_footer')
    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset(config('core.admin_asset').'/js/list.js') }}"></script>
    @include(config('core.admin_theme').'.partials.js_common')
@endsection
@push('scripts')
    {{--    slide --}}
    <script>
        // Get all sub-slides
        const subSlides = document.querySelectorAll('.sub-slide');

        // Add click event listeners to all sub-slides
        subSlides.forEach(function (slide, index) {
            slide.addEventListener('click', function () {
                $('#carouselExampleIndicators').carousel(index);
            });
        });
    </script>
    </script>
    {{--end slide--}}
    @include(config('core.admin_theme').'.partials.js_common_list')
    <script>
        $(document).ready(function () {
            function loadAjaxModal(id) {
                var luot_xem = $('[data-row="' + id + '"][data-field="luot_xem"]');
                var newUrl = window.location.href + '?id=' + id;

                $.ajax({
                    url: '/admin/codes/ajax-get-info/' + id,
                    type: 'GET',
                    success: function (res) {
                        var response = res.data;
                        console.log(response)
                        $('.modal-view-code').modal('show');
                        $('.loai_hinh').html(response.loai_hinh);
                        $('.loai_nha_dat').html(response.loai_nha_dat);
                        $('.du_an').html(res.service);
                        $('#bao-cao-dan-khach').attr('href', '/admin/bao_cao_dan_khach/add?code_id=' + response.id);
                        if (response.image == null) {
                            $('.image').attr('src', 'https://sehouse.khoweb.top/filemanager/userfiles/_thumbs/se-house-logo-100x.jpg');
                        } else {
                            $('.image').attr('src', response.image);
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
                        $('.intro').html(response.intro);
                        $('.sdt_chu_nha').html(response.sdt_chu_nha);
                        $('.so_giay_chung_nhan').html(response.so_giay_chung_nhan);
                        $('.row_id' + response.id + ' .item-luot_xem').html(response.luot_xem);
                        $('.gia_ha_chao').html(formatValue(response.gia_ha_chao));
                        $('.price_setup').html(formatValue(response.gia_niem_yet));
                        $('.dien_tich').html(response.dien_tich);
                        $('.mat_tien').html(response.mat_tien);
                        $('.so_tang').html(response.so_tang);
                        $('.phi_moi_gioi').html(response.phi_moi_gioi);
                        $('.toa').html(response.toa);
                        $('.tang').html(response.tang);
                        $('.khoang_tang').html(response.khoang_tang);
                        $('.so_phong_ngu').html(response.so_phong_ngu);
                        $('.content').html(response.content);
                        $('.dia_chi_tren_so').html(response.dia_chi_tren_so);
                        $('.img-main').attr('src', res.imagePath);
                        console.log(res.imagePaths);
                        $('#myCarousel').empty().append(
                            '<div class="carousel-item active">' +
                            '<img class="d-block w-100" src="' + res.imagePath + '" alt="Main Slide">' +
                            '</div>'
                        );

                        // Add additional slides
                        $.each(res.imagePaths, function (index, path) {
                            $('#myCarousel').append(
                                '<div class="carousel-item">' +
                                '<img class="d-block w-100" src="' + path + '" alt="Slide ' + (index + 1) + '">' +
                                '</div>'
                            );
                        });

                    }
                });

            }
            $('.btn-view').click(function (){
                var id = $(this).data('id')
                loadAjaxModal(id)
                window.history.pushState("object or string", "Title", newUrl);
                console.log(newUrl);
                $("#myModal").modal("show");
            });



            function formatPrice(price) {
                // Định dạng số tiền theo kiểu tiền tệ, ví dụ: 1,000,000 đ
                return new Intl.NumberFormat('vi-VN', {style: 'currency', currency: 'VND'}).format(price);
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
        });

    </script>

    <script>

    </script>
@endpush
