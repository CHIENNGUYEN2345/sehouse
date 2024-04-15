@extends(config('core.admin_theme').'.template')
@section('main')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet kt-portlet--tabs">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-toolbar">
                            <ul class="nav nav-tabs nav-tabs-space-xl nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand"
                                role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active"
                                       href="/admin/plugin">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                             viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"/>
                                                <rect fill="#000000" opacity="0.3" x="4" y="4" width="4" height="4"
                                                      rx="1"/>
                                                <path d="M5,10 L7,10 C7.55228475,10 8,10.4477153 8,11 L8,13 C8,13.5522847 7.55228475,14 7,14 L5,14 C4.44771525,14 4,13.5522847 4,13 L4,11 C4,10.4477153 4.44771525,10 5,10 Z M11,4 L13,4 C13.5522847,4 14,4.44771525 14,5 L14,7 C14,7.55228475 13.5522847,8 13,8 L11,8 C10.4477153,8 10,7.55228475 10,7 L10,5 C10,4.44771525 10.4477153,4 11,4 Z M11,10 L13,10 C13.5522847,10 14,10.4477153 14,11 L14,13 C14,13.5522847 13.5522847,14 13,14 L11,14 C10.4477153,14 10,13.5522847 10,13 L10,11 C10,10.4477153 10.4477153,10 11,10 Z M17,4 L19,4 C19.5522847,4 20,4.44771525 20,5 L20,7 C20,7.55228475 19.5522847,8 19,8 L17,8 C16.4477153,8 16,7.55228475 16,7 L16,5 C16,4.44771525 16.4477153,4 17,4 Z M17,10 L19,10 C19.5522847,10 20,10.4477153 20,11 L20,13 C20,13.5522847 19.5522847,14 19,14 L17,14 C16.4477153,14 16,13.5522847 16,13 L16,11 C16,10.4477153 16.4477153,10 17,10 Z M5,16 L7,16 C7.55228475,16 8,16.4477153 8,17 L8,19 C8,19.5522847 7.55228475,20 7,20 L5,20 C4.44771525,20 4,19.5522847 4,19 L4,17 C4,16.4477153 4.44771525,16 5,16 Z M11,16 L13,16 C13.5522847,16 14,16.4477153 14,17 L14,19 C14,19.5522847 13.5522847,20 13,20 L11,20 C10.4477153,20 10,19.5522847 10,19 L10,17 C10,16.4477153 10.4477153,16 11,16 Z M17,16 L19,16 C19.5522847,16 20,16.4477153 20,17 L20,19 C20,19.5522847 19.5522847,20 19,20 L17,20 C16.4477153,20 16,19.5522847 16,19 L16,17 C16,16.4477153 16.4477153,16 17,16 Z"
                                                      fill="#000000"/>
                                            </g>
                                        </svg>
                                        Tất cả
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link "
                                       href="/admin/plugin?status=1">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                             viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <mask fill="white">
                                                    <use xlink:href="#path-1"/>
                                                </mask>
                                                <g/>
                                                <path d="M15.6274517,4.55882251 L14.4693753,6.2959371 C13.9280401,5.51296885 13.0239252,5 12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L14,10 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C13.4280904,3 14.7163444,3.59871093 15.6274517,4.55882251 Z"
                                                      fill="#000000"/>
                                            </g>
                                        </svg>
                                        Bán hàng
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link "
                                       href="/admin/plugin?status=0">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                             viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <mask fill="white">
                                                    <use xlink:href="#path-1"/>
                                                </mask>
                                                <g/>
                                                <path d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z"
                                                      fill="#000000"/>
                                            </g>
                                        </svg>
                                        Giáo dục
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <?php
                        $arr_code = \App\Models\EmailTemplate::pluck('code')->toArray();
                        $data = json_decode(@file_get_contents('https://service.webhobasoft.com/api/v1/email_template?arr_code=' . implode(',', $arr_code)));
                        $data = (array)@$data->data->data;
                        foreach ($data as $v) {
                            $module_data_arr[$v->code] = (array)$v;
                        }
                        ?>
                        @foreach($data as $item)
                                <div class="col-lg-6 col-md-6 col-sm-6 col-12 p-3 moudle-item">
                                    <div class="border">
                                        <div class="row border-bottom p-3 m-0">
                                            <div class="col-lg-3 col-md-3 col-sm-12 col-12 p-0">
                                                <img data-src="{!! @$item['image'] !!}"
                                                     class="lazy" alt="{!! @$item['name'] !!}"
                                                     style="max-width: 100%">
                                            </div>

                                            <div class="col-lg-7 col-md-7 col-sm-12 col-12">
                                                <h3 style="max-width: 100%;
                                                            text-overflow: ellipsis;
                                                            white-space: nowrap;
                                                            overflow: hidden; ">
                                                    <a href="{!! @$item['link_detail'] !!}" target="_blank"
                                                       class="thickbox open-plugin-details-modal">
                                                        {!! @$item['name'] !!}</a>
                                                </h3>
                                                <p style="line-height: 16px;
                                                            width: 100%;
                                                            -webkit-line-clamp: 10;
                                                            overflow: hidden;
                                                            text-overflow: ellipsis;
                                                            max-height: 160px;
                                                            display: -webkit-box;
                                                            -webkit-box-orient: vertical;
                                                ">{!! @$item['intro'] !!}</p>
                                                <p style="margin: 0" class=""><cite>Bởi <a
                                                                href="{!! @$item['author_link'] !!}">{!! @$item['author'] !!}</a></cite>
                                                </p>
                                                <p style="margin: 0">Mã: {{ $module_name }}</p>
                                            </div>

                                            <div class="col-lg-2 col-md-2 col-sm-12 col-12">
                                                <div class=" row p-0 text-center">
                                                    <div class="btn p-0 pt-2 pb-2 mb-2 border col-lg-12 col-md-12 col-sm-6 col-6 btn-action">
                                                        @if(@$_GET['status'] == 1 || array_key_exists($module_name, $modules_active))
                                                            <a class="btn-trigger-change-status" data-slug="akismet"
                                                               aria-label="Cài đặt {{ @$item['name'] }} ngay"
                                                               data-status="0"
                                                               data-name="{{ @$module_name }}">Hủy kích hoạt</a>
                                                        @elseif(array_key_exists($module_name, $modules))
                                                            <a class="btn-trigger-change-status" data-slug="akismet"
                                                               aria-label="Cài đặt {{ @$item['name'] }} ngay"
                                                               data-status="1"
                                                               data-name="{{ @$module_name }}">Kích hoạt</a>
                                                        @else
                                                            <a class="btn-trigger-change-status" data-slug="akismet"
                                                               aria-label="Cài đặt {{ @$item['name'] }} ngay"
                                                               data-status="0"
                                                               data-name="{{ @$module_name }}">Cài đặt</a>
                                                        @endif
                                                    </div>
                                                    <div class="btn col-lg-12 col-md-12 col-sm-6 col-6 p-0 pt-2 pb-2 mb-2">
                                                        <a href="{!! @$item['link_detail'] !!}"
                                                           class=""
                                                           aria-label="{{ @$item['name'] }}"
                                                           data-title="{{ @$item['name'] }}">Chi tiết</a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row p-3 m-0" style="    background-color: #fafafa;">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-12 col-12 text-left">
                                                        <div class="">
                                                            {!! \Modules\Plugin\Http\Helpers\PluginHelper::voteStar($item['review']) !!}
                                                            {{--<span class="">4,5 đánh giá dựa trên 843 đánh giá</span>--}}
                                                            <span class="" aria-hidden="true">({{ @number_format($item['review_count'], 0, '.', '.') }})</span>
                                                        </div>


                                                        <div class="">
                                                            {{ @number_format($item['actived'], 0, '.', '.') }}
                                                            lượt kích hoạt
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12 text-right">
                                                        <div class="">
                                                            <strong>Cập nhật lần
                                                                cuối:</strong> {{ @date('d/m/Y H:i', strtotime($item['updated_at'])) }}
                                                        </div>

                                                        <div class="">
                                                            <?php
                                                            $max_version_required = explode('.', $item['max_version_required']);
                                                            $max_version_required = (int)@$max_version_required[0] * 10000 + (int)@$max_version_required[1] * 100 + (int)@$max_version_required[2];

                                                            $min_version_required = explode('.', $item['min_version_required']);
                                                            $min_version_required = (int)@$min_version_required[0] * 10000 + (int)@$min_version_required[1] * 100 + (int)@$min_version_required[2];

                                                            $current_version = 40000;
                                                            ?>
                                                            @if($min_version_required <= $current_version && $current_version <= $max_version_required)
                                                                <span class=""><i class="flaticon2-check-mark"></i> <strong>Tương thích</strong> với phiên bản HobaCore của bạn.</span>
                                                            @else
                                                                <span class=""><i class="flaticon2-cross"></i> <strong>Không tương thích</strong> với phiên bản HobaCore của bạn.</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @endforeach
                    </div>
                </div>
                <!--end::Portlet-->
            </div>
        </div>
    </div>
@endsection
@section('custom_head')
    <style>
        @media (min-width: 768px) {
            .kt-portlet--head-lg {
                margin-bottom: 20px !important;
            }
        }

        .app-item .app-icon {
            display: block;
            margin: 0 auto;
            max-width: 310px;
            height: 100px;
            background-color: #38a1cc;
            border-radius: 3px 3px 0 0;
            overflow: hidden;
        }

        .kt-portlet .kt-portlet__body {
            display: inline-block;
        }

        .app-card-item {
            float: left;
        }

        .app-icon img {
            max-width: 100%;
        }

        .app-item {
            margin-bottom: 20px;
            box-sizing: border-box;
            overflow: hidden;
            border-radius: 3px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
            background: #fff;
        }

        .app-details,
        .app-footer {
            padding: 10px;
        }

        .app-footer {
            background: #f5f5f5;
            border-top: 1px solid #e8e8e8;
            padding-left: 10px;
            padding-bottom: 12px;
            position: relative;
        }
        .btn-action {
            padding: 0 !important;
        }
        .btn-action a {
            width: 100%;
            display: inline-block;
            padding: 5px 10px;
            cursor: pointer;
        }
        .moudle-item {
            display: inline-block;
            float: left;
            height: 265px;
        }
    </style>
@endsection
@push('scripts')
    <script>
        $('.btn-trigger-change-status').click(function () {
            var status = $(this).data('status');
            var name = $(this).data('name');
            $.ajax({
                url: '/admin/plugin/active',
                data: {
                    name: name,
                    status: status
                },
                success: function (resp) {
                    location.reload();
                },
                error: function () {
                    toastr.error("Có lỗi xảy ra! Vui lòng load lại website và thử lại");
                }
            });
        });
    </script>
@endpush

