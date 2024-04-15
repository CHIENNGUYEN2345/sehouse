@extends(config('core.admin_theme').'.template')
@section('main')
    <form class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid {{ @$module['code'] }}"
          action="" method="POST"
          enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="return_direct" value="save_exit" type="hidden">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile"
                     id="kt_page_   portlet">
                    <div class="kt-portlet__head kt-portlet__head--lg" style="">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">Editor tính giá sản phẩm
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="/admin/{{ $module['code'] }}/{{ $result->id }}" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">Quay lại</span>
                            </a>
                            {{--<div class="btn-group">
                                @if(in_array($module['code'].'_add', $permissions))
                                    <button type="submit" class="btn btn-brand">
                                        <i class="la la-check"></i>
                                        <span class="kt-hidden-mobile">Lưu</span>
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
                                                    Lưu và tiếp tục
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link save_option" data-action="save_exit">
                                                    <i class="kt-nav__link-icon flaticon2-power"></i>
                                                    Lưu & Thoát
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link save_option" data-action="save_create">
                                                    <i class="kt-nav__link-icon flaticon2-add-1"></i>
                                                    Lưu và tạo mới
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>--}}
                        </div>
                    </div>
                </div>
                <!--end::Portlet-->
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-12">
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Editor
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <div class="kt-form">




                    </div>
                    <!--end::Form-->
                </div>
            </div>
        </div>
    </form>
@endsection
@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/form.css') }}">
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset('public/backend/css/order.css') }}">
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset('public/backend/css/styles-fix.css') }}">
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset('public/backend/css/bao_gia.css') }}">
    <link rel="stylesheet" href="/public/backend/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <style>


        .title-item {
            width: 51px!important;
            font-size: 12px;
        }

        .table-container-edit-order > div.row {
            width: 100%;
            margin: 0;
            display: grid;
        }
        .navbar-fix {
            min-height: 0px;
            border: none;
            margin-bottom: 0;
        }
        @media (min-width: 768px) {
            .navbar {
                border-radius: 4px;
            }
        }
        .navbar .navbar-inner {
            background: #bbbdbf;
            padding: 4px 13px;
        }
        .navbar-fix > .navbar-inner {
            padding: 4px 10px !important;
        }
        .navbar-fix .navbar-inner .order_group_item {
            margin-right: 18px;
            /* display: flex; */
        }
        .pop-tool {
            position: relative !important;
            cursor: pointer;
        }
        .order_group_item {
            display: inline-block;
            float: left;
        }

        .navbar-fix .navbar-inner .order_group_item a.active {
            background: #d0d2d3;
        }
        .navbar-fix .navbar-inner .order_group_item .brand {
            border-top-right-radius: 13px;
            border-top-left-radius: 5px;
            background: #a6a8ab;
            display: block;
            min-width: 85px;
            position: relative;
            z-index: 2;
            border: none;
            max-width: 400px;
            display: -webkit-box;
            word-break: break-word;
        }
        .navbar .navbar-inner a.brand {
            position: relative;
            display: inline-block;
        }
        .navbar .navbar-inner a.active {
            background: #fff;
            font-weight: 700;
        }
        .navbar .navbar-inner a {
            padding: 5px 13px !important;
            font-weight: 600;
        }
        .navbar .navbar-inner a {
            padding: 6px 13px;
            color: #000000;
        }
        .navbar-fix > .navbar-inner a {
            color: #333 !important;
        }
        .navbar-fix .navbar-inner .order_group_item a.active:after {
            border-bottom: 24px solid #d0d2d3;
        }
        .navbar-fix .navbar-inner .order_group_item .brand:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 98%;
            width: 0;
            height: 0;
            border-right: 10px solid transparent;
            border-bottom: 24px solid #a6a8ab;
            z-index: 1;
        }
        .pop-show {
            display: none!important;
        }
        .order_group_item:hover .order_group_action {
            display: inline-block !important;
        }
        .pop-show {
            position: absolute !important;
            top: 98% !important;
            left: 50% !important;
            z-index: 2;
            background: #000000b3;
            padding: 3px 6px;
            border-radius: 5px;
            box-shadow: 0px 3px 6px rgba(0,0,0,.26);
            display: flex;
            justify-content: space-around;
            align-items: center;
            -webkit-transform: translateX(-50%);
            -moz-transform: translateX(-50%);
            -ms-transform: translateX(-50%);
            -o-transform: translateX(-50%);
            transform: translateX(-50%);
            text-align: center;
            color: #fff;
            min-width: 80px;
            max-width: 200px;
        }
        .pop-show * {
            color: #fff !important;
        }
        .pop-title {
            position: relative;
            z-index: 2;
        }
        .option_more {
            display: flex;
            justify-content: center;
        }
        .navbar-fix .navbar-inner .order_group_action button {
            margin-left: 5px;
        }
        .navbar .navbar-inner .edit-orderGroup {
            padding: 0 !important;
        }
        .pop-show button {
            background: none;
        }
        .option_more > * {
            margin: 0 5px;
        }
        .btn {
            font-weight: 500;
            border-radius: 2px;
        }
        .navbar-fix .navbar-inner .order_group_action button>i {
            padding: 0 2px 0 1px;
            color: #0000ff;
        }
        .no-margin {
            margin: 0 !important;
        }
        .modal-header {
            padding: 12px 18px 0 12px;
        }
        .modal-header .close {
            font-size: 33px;
        }
        .modal-header .close {
            margin-top: -2px;
        }
        button.close {
            -webkit-appearance: none;
            padding: 0;
            cursor: pointer;
            background: 0 0;
            border: 0;
        }
        .close {
            float: right;
            font-size: 21px;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            filter: alpha(opacity=20);
            opacity: .2;
        }
        input, button, select, textarea {
            font-family: inherit;
            /* border-color: #545ca6; */
        }.modal-header h3 {
             margin: 8px;
             margin-left: 2px;
         }
        .modal .modal-body, .modal-body .form-group {
            width: 100%;
        }
        .modal .modal-body {
            display: inline-block;
        }
        .modal-body {
            position: relative;
            padding: 15px;
        }
        .box-grey input {
            color: #444;
        }

        .modal .form-group {
            display: inline-block;
            width: 100%;
        }
        .form-group {
            margin-right: -15px;
            margin-left: -15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .modal-footer {
            border-top-color: #f4f4f4;
        }
        .modal-footer {
            padding: 15px;
            text-align: right;
            border-top: 1px solid #e5e5e5;
        }
        modal-footer>.btn {
            margin-bottom: 0;
        }
        .modal-footer .btn+.btn, #setPriceCol #add-item-set_price {
            margin-right: 12px;
            height: 32px;
            padding-bottom: 3px;
            padding-top: 2px;
        }
        .modal-footer .btn+.btn {
            margin-bottom: 0;
            margin-left: 5px;
        }
        .navbar {
            display: inline-block;
        }
        .navbar {
            background: #fff;
            width: 100%;
            margin: 0;
        }
        .kt-aside-menu .kt-menu__nav > .kt-menu__item > .kt-menu__heading .kt-menu__link-text, .kt-aside-menu .kt-menu__nav > .kt-menu__item > .kt-menu__link .kt-menu__link-text{
            font-size: 1.3rem!important;
        }
        .kt-aside-menu .kt-menu__nav > .kt-menu__section .kt-menu__section-text{
            font-size: 1.3rem!important;
        }
        .kt-aside-menu .kt-menu__nav > .kt-menu__item > .kt-menu__heading .kt-menu__link-icon, .kt-aside-menu .kt-menu__nav > .kt-menu__item > .kt-menu__link .kt-menu__link-icon{
            font-size: 1.7rem!important;
        }
        .navbar {
            position: relative;
            margin: 0;
            padding: 0;
        }
        .navbar-fix .bg-fix {
            background: #d0d2d3;
        }
        .navbar-fix .navbar-inner {
            padding-bottom: 0 !important;
            display: flex;
            align-items: flex-end;
        }
        .navbar-fix .bg-fix a.brand {
            font-size: 12px;
            padding-bottom: 2px !important;
        }
        .hide {
            display: none !important;
        }
        #wrapper2-scroll .footer-table-action {
            position: relative;
        }
        #order-total-price{
            top: 40%;
        }
        #add_order_row{
            bottom: 0px !important;
            right: 15px !important;
            top: 0;
            height: fit-content;
        }
    </style>
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
@push('scripts')
    <script>
        $('.save_editor').click(function() {
            $('input[name=return_direct]').val($(this).data('action'));
            $('form.{{ @$module['code'] }}').submit();
        });
    </script>
@endpush
