<?php
$roles = \App\Models\Roles::all();
?>
@extends(config('core.admin_theme').'.template')
@section('main')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        {{trans('admin.dashboard')}}
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="la la-unlock fa-2x"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        Quyền
                    </h3>
                </div>
            </div>
            <div class="kt-portlet__body kt-portlet__body--fit">
                <!--begin: Datatable -->
                <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded"
                     id="scrolling_vertical" style="">
                    <table class="table table-striped">
                        <thead class="kt-datatable__head">
                        <tr class="kt-datatable__row" style="left: 0px;">
                            <th data-field="image" class="kt-datatable__cell kt-datatable__cell--sort ">
                                STT
                            </th>
                            <th data-field="name" class="kt-datatable__cell kt-datatable__cell--sort ">
                                Quyền
                            </th>
                            <th data-field="slug" class="kt-datatable__cell kt-datatable__cell--sort ">
                                Số tài khoản
                            </th>
                        </tr>
                        </thead>
                        <tbody class="kt-datatable__body ps ps--active-y">
                        @foreach($roles as $r=>$role)
                            <tr class="kt-datatable__row">
                                <td class="kt-datatable__cell">{{$r+=1}}</td>
                                <td class="kt-datatable__cell">{{ $role->display_name }}</td>
                                <td class="kt-datatable__cell">
                                    <a href="/admin/admin?role_id={{$role->id}}">{{ @\App\Models\RoleAdmin::where('role_id',$role->id)->where('status',1)->get()->count() }}</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!--end: Datatable -->
            </div>
        </div>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <i class="la la-users fa-2x"></i>
                    </span>
                    <h3 class="kt-portlet__head-title">
                        Số khách hàng: <a href="/admin/user">{{ \App\Models\User::get()->count() }}</a>
                    </h3>
                </div>
            </div>

        </div>
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="/cache-flush" class="btn btn-default btn-icon-sm" >
                                <i class="la la-refresh"></i> Xóa cache
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/list.css') }}">
@endsection
@section('custom_footer')
    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset(config('core.admin_asset').'/js/list.js') }}"></script>
    @include(config('core.admin_theme').'.partials.js_common')
@endsection
@push('scripts')
    @include(config('core.admin_theme').'.partials.js_common_list')
@endpush