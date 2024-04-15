@extends(config('core.admin_theme').'.template')
@section('main')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <i>Nhân viên tuyệt đối không chia sẻ tài liệu nội bộ công ty cho người khác</i>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
            <span class="kt-portlet__head-icon">
                <i class="kt-font-brand flaticon-calendar-with-a-clock-time-tools"></i>
            </span>
                    <h3 class="kt-portlet__head-title">
                        Tài liệu đào tạo
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="">
                            <input type="text" name="quick_search" value="{{ @$_GET['quick_search'] }}"
                                   class="form-control" title="Chỉ cần enter để thực hiện tìm kiếm"
                                   placeholder="Tìm kiếm nhanh theo {{ @$quick_search['label'] }}">
                        </div>
                        <div class="kt-portlet__head-actions">
                            <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle btn-closed-search"
                                    onclick="$('.form-search').slideToggle(100); $('.kt-portlet-search').toggleClass('no-padding');">
                                <i class="la la-search"></i> Tìm kiếm
                            </button>
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet__body kt-portlet-search @if(!isset($_GET['search'])) no-padding @endif">
                <!--begin: Search Form -->
                <form class="kt-form kt-form--fit kt-margin-b-20 form-search" id="form-search" method="GET" action=""
                      @if(!isset($_GET['search'])) style="display: none;" @endif>
                    <input name="search" type="hidden" value="true">
                    <input type="hidden" name="quick_search"
                                                                                  value="{{ @$_GET['quick_search'] }}"
                                                                                  id="quick_search_hidden"
                                                                                  class="form-control"
                                                                                  placeholder="Tìm kiếm nhanh theo {{ @$quick_search['label'] }}">
                    <div class="row">

                        
                    </div>
                    
                    <input name="export" type="submit" value="export" style="display: none;">
                    
                </form>
                <!--end: Search Form -->
            </div>
            <div class="kt-separator kt-separator--md kt-separator--dashed" style="margin: 0;"></div>
            <div class="kt-portlet__body kt-portlet__body--fit">   
                <?php 

                    
                    if (in_array(@\Auth::guard('admin')->user()->room_id, [1, 2, 3, 4, 5])) {
                        //  Nếu thuộc phòng kinh doanh
                        if (\Auth::guard('admin')->user()->work_time == 1) {
                            //  Nếu là fulltime thì show ra lương nvkd fulltime

                        } elseif (\Auth::guard('admin')->user()->work_time == 2) {
                            //  Nếu là parttime thì show ra lương nvkd parttime

                        }
                    } elseif (in_array(\Auth::guard('admin')->user()->room_id, [6])) {
                        //  Nếu thuộc phòng telesale
                        if (\Auth::guard('admin')->user()->work_time == 1) {
                            //  Nếu là fulltime thì show ra lương nvkd fulltime

                        } elseif (\Auth::guard('admin')->user()->work_time == 2) {
                            //  Nếu là parttime thì show ra lương nvkd parttime

                        }
                    } elseif (in_array(\Auth::guard('admin')->user()->room_id, [10])) {
                        //  Nếu thuộc phòng giảng viên
                        if (\Auth::guard('admin')->user()->work_time == 1) {
                            //  Nếu là fulltime thì show ra lương nvkd fulltime

                        } elseif (\Auth::guard('admin')->user()->work_time == 2) {
                            //  Nếu là parttime thì show ra lương nvkd parttime

                        }
                    } elseif (in_array(\Auth::guard('admin')->user()->room_id, [15])) {
                        //  Nếu thuộc phòng Trợ giảng
                        if (\Auth::guard('admin')->user()->work_time == 1) {
                            //  Nếu là fulltime thì show ra lương nvkd fulltime

                        } elseif (\Auth::guard('admin')->user()->work_time == 2) {
                            //  Nếu là parttime thì show ra lương nvkd parttime

                        }
                    } elseif (in_array(\Auth::guard('admin')->user()->room_id, [20])) {
                        //  Nếu thuộc phòng MKT
                        if (\Auth::guard('admin')->user()->work_time == 1) {
                            //  Nếu là fulltime thì show ra lương nvkd fulltime

                        } elseif (\Auth::guard('admin')->user()->work_time == 2) {
                            //  Nếu là parttime thì show ra lương nvkd parttime

                        }
                    } elseif (in_array(\Auth::guard('admin')->user()->room_id, [25])) {
                        //  Nếu thuộc phòng tuyển dụng
                        if (\Auth::guard('admin')->user()->work_time == 1) {
                            //  Nếu là fulltime thì show ra lương nvkd fulltime

                        } elseif (\Auth::guard('admin')->user()->work_time == 2) {
                            //  Nếu là parttime thì show ra lương nvkd parttime

                        }
                    } elseif (in_array(\Auth::guard('admin')->user()->room_id, [30])) {
                        //  Nếu thuộc phòng CSKH
                        if (\Auth::guard('admin')->user()->work_time == 1) {
                            //  Nếu là fulltime thì show ra lương nvkd fulltime

                        } elseif (\Auth::guard('admin')->user()->work_time == 2) {
                            //  Nếu là parttime thì show ra lương nvkd parttime

                        }
                    }
                ?>

                
                <?php 
                $chang = \App\CRMEdu\Models\Category::select('id', 'name')->where('type', null);
                
                if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['ctv_sale', 'sale', 'truong_phong_sale', 'telesale', 'content'])) {
                    //  Nếu là kinh doanh thì truy vấn các khóa học của kinh doanh
                    $chang->where(function ($query) {
                                $query->orWhere('parent_id', 246);  //  Kinh doanh
                                if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['truong_phong_sale'])) {
                                    //  Nếu là trưởng phòng kinh doanh thì truy vấn khoá học của trưởng phòng
                                    $query->orWhere('parent_id', 288);  //  Trưởng phòng kinh doanh
                                }
                            });
                } elseif (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['marketing'])) {
                    //  Nếu là giảng viên thì truy vấn các khóa học của giảng viên
                    $chang = $chang->where('parent_id', 280);
                } elseif (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['technicians'])) {
                    //  Nếu là giảng viên thì truy vấn các khóa học của giảng viên
                    $chang = $chang->where('parent_id', 245);
                } elseif (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['operating'])) {
                    //  Nếu là giảng viên thì truy vấn các khóa học của Trợ giảng
                    $chang = $chang->where('parent_id', 259);
                } elseif (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['hr'])) {
                    //  Nếu là HR thì truy vấn các khóa học của HR
                    $chang = $chang->where('parent_id', 274);
                } else {
                    $chang = $chang->where(function ($query) {
                                $query->orWhere('parent_id', 246);  //  Kinh doanh
                                $query->orWhere('parent_id', 288);  //  Trưởng phòng kinh doanh
                                $query->orWhere('parent_id', 280);  //  giảng viên
                                $query->orWhere('parent_id', 245);  //  Trợ giảng
                                $query->orWhere('parent_id', 259);  //  Trợ giảng
                                $query->orWhere('parent_id', 274);  //  Trợ giảng
                            });
                }

                $chang = $chang->orderBy('order_no', 'asc')->get();
                ?>
                
                @foreach($chang as $ch => $chang_item)
                    <label style="font-weight: bold; font-size: 20px;" data-id="{{ $chang_item->id }}">{{ $chang_item->name }}</label>

                    <?php 
                    $muc = \App\CRMEdu\Models\Category::select('id', 'name')->where('parent_id', $chang_item->id)->orderBy('order_no', 'asc')->get();
                    ?>
                    <div class="accordion" id="accordion2" style="padding-left: 15px;">
                        @foreach($muc as $k => $muc_item)
                          <div class="accordion-group">
                            <div class="accordion-heading">
                              <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse{{ $muc_item->id }}" style="color: #000;" data-id="{{ $muc_item->id }}">
                                {{ $k + 1 }}. {{ $muc_item->name }}
                              </a>
                            </div>
                            <div id="collapse{{ $muc_item->id }}" class="accordion-body {{ $k == 0 ? 'collapse in' : 'collapse' }}">
                              <div class="accordion-inner">
                                <?php 
                                $courses = \App\CRMEdu\Models\Course::where('status', 1)->where('multi_cat', 'like', '%|'.$muc_item->id.'|%')->orderBy('order_no', 'asc')->get();
                                ?>
                                <ul>
                                @foreach($courses as $course)
                                    <li><a href="{{ $course->link }}" data-id="{{ $course->id }}" target="_blank">{{ $course->name }}</a></li>
                                @endforeach
                                </ul>
                              </div>
                            </div>
                          </div>
                        @endforeach                  
                    </div>
                @endforeach     
            </div>
        </div>
    </div>
@endsection

@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/list.css') }}">
 
    <style type="text/css">
        table tr:hover .div-tooltip_info {
            opacity: 1;
            display: block;
        }
    </style>
@endsection
@section('custom_footer')
    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset(config('core.admin_asset').'/js/list.js') }}"></script>
    @include(config('core.admin_theme').'.partials.js_common')
@endsection
@push('scripts')
  

@endpush
