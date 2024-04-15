@extends(config('core.admin_theme').'.template')
@section('main')
    <form class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid {{ @$module['code'] }}"
          action="" method="POST"
          enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="return_direct" value="save_continue" type="hidden">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile"
                     id="kt_page_portlet">
                    <div class="kt-portlet__head kt-portlet__head--lg" style="">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">{{trans('admin.edit')}} {{ trans($module['label']) }}
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="/admin/{{ $module['code'] }}" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">{{trans('admin.back')}}</span>
                            </a>
                            <div class="btn-group">
                                @if(in_array($module['code'].'_edit', $permissions))
                                    <button type="submit" class="btn btn-brand">
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">{{trans('admin.save')}}</span>
                                    </button>
                                    <button type="button"
                                            class="btn btn-brand dropdown-toggle dropdown-toggle-split"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <ul class="kt-nav">
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link save_option" data-action="save_continue">
                                                    <i class="kt-nav__link-icon flaticon2-reload"></i>
                                                    {{trans('admin.save_and_continue')}}
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link save_option" data-action="save_exit">
                                                    <i class="kt-nav__link-icon flaticon2-power"></i>
                                                    {{trans('admin.save_and_quit')}}
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link save_option" data-action="save_create">
                                                    <i class="kt-nav__link-icon flaticon2-add-1"></i>
                                                    {{trans('admin.save_and_create')}}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Portlet-->
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-8">
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {{trans('admin.basic_information')}}
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">
                                @foreach($module['form']['general_tab'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                        if($field['name'] == 'password' || $field['name'] == 'password_confimation') {
                                            $field['class'] = str_replace('required', '', $field['class']);
                                        }
                                    @endphp
                                    @if($field['type'] == 'custom')
                                        @include($field['field'], ['field' => $field])
                                    @else
                                        <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                             id="form-group-{{ $field['name'] }}">
                                            <label for="{{ $field['name'] }}">{{ trans(@$field['label']) }} @if(strpos(@$field['class'], 'require') !== false)
                                                    <span class="color_btd">*</span>@endif</label>
                                            <div class="col-xs-12">
                                                @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                                <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Portlet-->


                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Khóa học đã đăng ký
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">
                                <?php
                                $bills = \App\CRMEdu\Models\Bill::where('customer_id', @$result->id)->where('status', 1)->get();
                                ?>
                                <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded" id="scrolling_vertical" style="">
                                    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                                        <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                                    </div><div class="ps__rail-y" style="top: 0px; height: 496px; right: 0px;">
                                        <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 207px;"></div>
                                    </div><table class="table table-striped">
                                        <thead class="kt-datatable__head">
                                        <tr class="kt-datatable__row" style="left: 0px;">

                                            <th data-field="total_price_contract" class="kt-datatable__cell kt-datatable__cell--sort ">
                                                Tổng $
                                            </th>
                                            <th data-field="total_received" class="kt-datatable__cell kt-datatable__cell--sort ">
                                                $ đã thu
                                            </th>
                                            <th data-field="finance_id" class="kt-datatable__cell kt-datatable__cell--sort ">
                                                $ chưa thu
                                            </th>
                                            <th data-field="service_id" class="kt-datatable__cell kt-datatable__cell--sort " onclick="sort('service_id')">
                                                Lớp học
                                                <i class="flaticon2-arrow-down"></i>
                                            </th>
                                            <th data-field="registration_date" class="kt-datatable__cell kt-datatable__cell--sort " onclick="sort('registration_date')">
                                                Ngày ký
                                                <i class="flaticon2-arrow-down"></i>
                                            </th>
                                            <th data-field="status" class="kt-datatable__cell kt-datatable__cell--sort " onclick="sort('status')">
                                                Trạng thái
                                                <i class="flaticon2-arrow-down"></i>
                                            </th>
                                            <!-- Nếu được xem hết dữ liệu thì hiển thị ra cột sale phụ trách -->
                                            <th data-field="company_id" class="kt-datatable__cell kt-datatable__cell--sort">
                                                Sales
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="kt-datatable__body ps ps--active-y" style="max-height: 496px;">

                                            <tr data-row="0" class="kt-datatable__row" style="left: 0px;">

                                                <td data-field="total_price_contract" class="kt-datatable__cell item-total_price_contract">
                                                    <span style="width: 100%;display: inline-block;text-align: right; ">10.000.000<sup>đ</sup></span>
                                                </td>
                                                <td data-field="total_received" class="kt-datatable__cell item-total_received">
                                                    <span style="width: 100%;display: inline-block;text-align: right; ">5.000.000<sup>đ</sup></span>
                                                </td>
                                                <td data-field="finance_id" class="kt-datatable__cell item-finance_id">
                                                    <a href="/admin//" target="_blank">
                                                        5.000.000<sup>đ</sup>
                                                    </a>
                                                </td>
                                                <td data-field="service_id" class="kt-datatable__cell item-service_id">
                                                    <a href="/admin/service/8" target="_blank">

                                                        Ielts
                                                    </a>
                                                </td>
                                                <td data-field="registration_date" class="kt-datatable__cell item-registration_date">
                                                    01/02/2023                                                                            </td>
                                                <td data-field="status" class="kt-datatable__cell item-status">
                                                    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill publish" data-url="" data-id="29" style="cursor:pointer;" data-column="status">Kích hoạt</span>
                                                </td>

                                                <!-- Nếu được xem hết dữ liệu thì hiển thị ra cột sale phụ trách -->
                                                <td data-field="company_id" class="kt-datatable__cell kt-datatable__cell--sort">
                                                    Trần Đại Hiệp
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
            </div>
            <div class="col-xs-12 col-md-4">
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                {{trans('admin.other_information')}}
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">
                                @foreach($module['form']['more_info_tab'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
                                    <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                         id="form-group-{{ $field['name'] }}">
                                        <label for="{{ $field['name'] }}">{{ trans(@$field['label']) }} @if(strpos(@$field['class'], 'require') !== false)
                                                <span class="color_btd">*</span>@endif</label>
                                        <div class="col-xs-12">
                                            @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                            <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Portlet-->
            </div>
        </div>
    </form>
@endsection
@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/form.css') }}">
    <script src="{{asset('public/ckeditor/ckeditor.js') }}"></script>
    <script src="{{asset('public/ckfinder/ckfinder.js') }}"></script>
    <script src="{{asset('public/libs/file-manager.js') }}"></script>
@endsection
@section('custom_footer')
    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
            type="text/javascript"></script>

    <script type="text/javascript" src="{{ asset('public/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/tinymce/tinymce_editor.js') }}"></script>
    <script type="text/javascript">
        editor_config.selector = ".editor";
        editor_config.path_absolute = "{{ (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" }}/";
        tinymce.init(editor_config);
    </script>
    <script type="text/javascript" src="{{ asset(config('core.admin_asset').'/js/form.js') }}"></script>
@endsection
