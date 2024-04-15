@extends('admin.themes.metronic1.template')
@section('main')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid {{ @$module['code'] }}"
          action="" method="POST"
          enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-12">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="kt-portlet kt-portlet--tabs kt-portlet--height-fluid">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand flaticon-refresh"></i>
                                </span>
                                <h3 class="kt-portlet__head-title">
                                    {{trans('admin.basic_caching_commands')}}
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="kt_widget4_tab1_content">
                                    <div class="kt-widget4">
                                        <div class="kt-widget4__item">
                                            <div class="kt-widget4__info">
                                                <p class="kt-widget4__username">
                                                    {{trans('admin.clear_the_cache')}}
                                                </p>
                                            </div>
                                            <a style="width: 25%;" href="/cache/clear/all" class="btn btn-bold btn-sm btn-font-sm  btn-label-success">{{trans('admin.clear_all_memory')}}</a>
                                        </div>
                                        <div class="kt-widget4__item">
                                            <div class="kt-widget4__info">
                                                <p class="kt-widget4__username">
                                                    {{trans('admin.refresh_the_interface_buffer')}}
                                                </p>
                                            </div>
                                            <a style="width: 25%;" href="/cache/clear/view" class="btn btn-bold btn-sm btn-font-sm  btn-label-success">{{trans('admin.refresh_the_interface')}}</a>
                                        </div>
                                        <div class="kt-widget4__item">
                                            <div class="kt-widget4__info">
                                                <p class="kt-widget4__username">
                                                    {{trans('admin.you_need_refresh')}}
                                                </p>
                                            </div>
                                            <a style="width: 25%;" href="/cache/clear/setting" class="btn btn-bold btn-sm btn-font-sm  btn-label-success"> {{trans('admin.delete_memory')}}</a>
                                        </div>

                                        <div class="kt-widget4__item">
                                            <div class="kt-widget4__info">
                                                <p class="kt-widget4__username">
                                                    {{trans('admin.this_action_is_required')}}
                                                </p>
                                            </div>
                                            <a style="width: 25%;" href="/cache/clear/route" class="btn btn-bold btn-sm btn-font-sm  btn-label-success"> {{trans('admin.delete_cache')}}</a>
                                        </div>
                                        <div class="kt-widget4__item">
                                            <div class="kt-widget4__info">
                                                <p class="kt-widget4__username">
                                                    {{trans('admin.delete_history_error')}}
                                                </p>
                                            </div>
                                            <a style="width: 25%;" href="/cache/clear/error" class="btn btn-bold btn-sm btn-font-sm  btn-label-success"> {{trans('admin.delete_history_error')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset('backend/themes/metronic1/css/form.css') }}">
@endsection
@push('scripts')

@endpush
