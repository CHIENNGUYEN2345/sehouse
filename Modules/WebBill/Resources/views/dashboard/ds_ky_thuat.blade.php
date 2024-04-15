@extends(config('core.admin_theme').'.template')
@section('main')    
<!--begin::Entry-->
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
        @if(in_array('bill_view', $permissions) || in_array('dhbill_view', $permissions))
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title bold uppercase">
                    Điều hành
                </h3>
            </div>
        </div>
        <!--begin::Row-->
        <div class="row">
            <?php 
            $admin_ids = \App\Models\RoleAdmin::where('role_id', 178)->pluck('admin_id');
            $ds_dieu_hanh = \App\Models\Admin::select('id', 'name', 'tel', 'zalo', 'email', 'image', 'code', 'work_time', 'maximum_projects')->where('status', 1)->whereIn('id', $admin_ids)->orderBy('id', 'asc')->get();
            ?>
            @foreach($ds_dieu_hanh as $admin)
                <?php 
                    $so_da_dang_lam = Modules\WebBill\Models\BillProgress::whereIn('status', [
                                    'Thu thập YCTK L1',
                                    'Triển khai L1',
                                    'Nghiệm thu L1 & thu thập YCTK L2',
                                    'Triển khai L2',
                                    'Nghiệm thu L2 & thu thập YCTK L3',
                                    'Triển khai L3',
                                    'Nghiệm thu L3 & thu thập YCTK L4',
                                    'Triển khai L4',
                                    'Nghiệm thu L4 & thu thập YCTK L5',
                                    'Triển khai L5',
                                    'Nghiệm thu L5 & thu thập YCTK L6',
                                    'Triển khai L6',
                                    'Tạm dừng',
                                    'Bỏ',
                                    null,
                                ])
                            ->where('dh_id', $admin->id)
                            ->count();

                    $so_da_da_lam = Modules\WebBill\Models\BillProgress::whereIn('status', [
                                    'Khách xác nhận xong',
                                    'Kết thúc',
                                ])
                            ->where('dh_id', $admin->id)
                            ->count();
                ?>
            <!--begin::Col-->
            <div class="col-lg-4 col-md-6 col-sm-6">
                <!--begin::Card-->
                <div class="card card-custom gutter-b card-stretch">
                    <!--begin::Body-->
                    <div class="card-body pt-4">
                        <!--begin::Toolbar-->
                            <!--begin::User-->
                            <div class="d-flex align-items-end mb-7">
                                <!--begin::Pic-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Pic-->
                                    <div class="flex-shrink-0 mr-4 mt-lg-0 mt-3">
                                        <div class="symbol symbol-circle symbol-lg-75 dh-avatar">
                                            <img src="{{ \App\Http\Helpers\CommonHelper::getUrlImageThumb($admin->image,100,100) }}" alt="{{ $admin->name }}">
                                        </div>
                                        <div class="symbol symbol-lg-75 symbol-circle symbol-primary d-none">
                                            <span class="font-size-h3 font-weight-boldest">JM</span>
                                        </div>
                                    </div>
                                    <!--end::Pic-->
                                    <!--begin::Title-->
                                    <div class="d-flex flex-column">
                                        <a href="javascript:;" class="text-dark font-weight-bold text-hover-primary font-size-h4 mb-0">{{ $admin->name }}</a>
                                        @if ($admin->maximum_projects <= $so_da_dang_lam)
                                            <span class="dang-ban">Đang Bận</span>
                                        @else
                                            <span class="dang-ranh">Đang Rảnh</span>
                                        @endif
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <!--end::Title-->
                            </div>
                            <!--end::User-->
                            <!--begin::Desc-->
                            <!-- <p class="mb-7">I distinguish three 
                                <a href="#" class="text-primary pr-1">#XRS-54PQ</a>objectives First objectives and nice cooked rice</p> -->
                                <!--end::Desc-->
                                <!--begin::Info-->
                                <div class="mb-7 noi-dung">
                                    <div class="d-flex justify-content-between align-items-cente my-1">
                                        <span class="text-dark-75 font-weight-bolder mr-2">Đánh giá:</span>
                                        <a href="https://zalo.me/{{ $admin->tel }}" class="text-muted text-hover-primary"></a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark-75 font-weight-bolder mr-2">Dự án đã làm:</span>
                                        <a href="mailto:{{ $admin->email }}" class="text-muted text-hover-primary">{{ $so_da_da_lam }}</a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-cente my-1">
                                        <span class="text-dark-75 font-weight-bolder mr-2">Dự án đang làm:</span>
                                        <a href="https://zalo.me/{{ $admin->tel }}" class="text-muted text-hover-primary">{{ $so_da_dang_lam }}</a>
                                    </div>
                                </div>
                                <div class="mb-7 noi-dung">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark-75 font-weight-bolder mr-2">Email:</span>
                                        <a href="mailto:{{ $admin->email }}" target="_blank" class="text-muted text-hover-primary">{{ $admin->email }}</a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-cente my-1">
                                        <span class="text-dark-75 font-weight-bolder mr-2">SĐT:</span>
                                        <a target="_blank" class="text-muted text-hover-primary">{{ $admin->tel }}</a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-cente my-1">
                                        <span class="text-dark-75 font-weight-bolder mr-2">Zalo:</span>
                                        <a href="https://zalo.me/{{ $admin->tel }}" target="_blank" class="text-muted text-hover-primary">{{ $admin->zalo }}</a>
                                    </div>
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->
                    @endforeach
                </div>
                <!--end::Row-->

                
        @endif

        @if(in_array('dhbill_view', $permissions))
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title bold uppercase">
                    Kỹ thuật
                </h3>
            </div>
        </div>
        <!--begin::Row-->
        <div class="row">
            <?php 
            $admin_ids = \App\Models\RoleAdmin::where('role_id', 173)->pluck('admin_id');
            $ds_ky_thuat = \App\Models\Admin::select('id', 'name', 'tel', 'zalo', 'email', 'image', 'code', 'work_time', 'maximum_projects')->where('status', 1)->whereIn('id', $admin_ids)->orderBy('id', 'asc')->get();
            ?>
            @foreach($ds_ky_thuat as $admin)
                <?php 
                    $so_da_dang_lam = Modules\WebBill\Models\BillProgress::leftJoin('bills', 'bills.id', '=', 'bill_progress.bill_id')
                            ->whereIn('bill_progress.status', [
                                    'Thu thập YCTK L1',
                                    'Triển khai L1',
                                    'Nghiệm thu L1 & thu thập YCTK L2',
                                    'Triển khai L2',
                                    'Nghiệm thu L2 & thu thập YCTK L3',
                                    'Triển khai L3',
                                    'Nghiệm thu L3 & thu thập YCTK L4',
                                    'Triển khai L4',
                                    'Nghiệm thu L4 & thu thập YCTK L5',
                                    'Triển khai L5',
                                    'Nghiệm thu L5 & thu thập YCTK L6',
                                    'Triển khai L6',
                                    'Tạm dừng',
                                    'Bỏ',
                                    null,
                                ])
                            ->whereIn('bills.service_id', [ //  ko tính dự án landingpage
                                5,
                                9,
                                10,
                                11,
                                12,
                                13,
                                14,
                                15,
                                16,
                            ])
                            ->where('bill_progress.kt_id', $admin->id)
                            ->count();

                    $so_da_da_lam = Modules\WebBill\Models\BillProgress::whereIn('status', [
                                    'Khách xác nhận xong',
                                    'Kết thúc',
                                ])
                            ->where('kt_id', $admin->id)
                            ->count();
                ?>
            <!--begin::Col-->
            <div class="col-lg-4 col-md-6 col-sm-6">
                <!--begin::Card-->
                <div class="card card-custom gutter-b card-stretch">
                    <!--begin::Body-->
                    <div class="card-body pt-4">
                        <!--begin::Toolbar-->
                            <!--begin::User-->
                            <div class="d-flex align-items-end mb-7">
                                <!--begin::Pic-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Pic-->
                                    <div class="flex-shrink-0 mr-4 mt-lg-0 mt-3">
                                        <div class="symbol symbol-circle symbol-lg-75 dh-avatar">
                                            <img src="{{ \App\Http\Helpers\CommonHelper::getUrlImageThumb($admin->image,100,100) }}" alt="{{ $admin->name }}">
                                        </div>
                                        <div class="symbol symbol-lg-75 symbol-circle symbol-primary d-none">
                                            <span class="font-size-h3 font-weight-boldest">JM</span>
                                        </div>
                                    </div>
                                    <!--end::Pic-->
                                    <!--begin::Title-->
                                    <div class="d-flex flex-column">
                                        <a href="javascript:;" class="text-dark font-weight-bold text-hover-primary font-size-h4 mb-0">{{ $admin->name }}</a>
                                        @if ($admin->maximum_projects <= $so_da_dang_lam)
                                            <span class="dang-ban">Đang Bận</span>
                                        @else
                                            <span class="dang-ranh">Đang Rảnh</span>
                                        @endif
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <!--end::Title-->
                            </div>
                            <!--end::User-->
                            <!--begin::Desc-->
                            <!-- <p class="mb-7">I distinguish three 
                                <a href="#" class="text-primary pr-1">#XRS-54PQ</a>objectives First objectives and nice cooked rice</p> -->
                                <!--end::Desc-->
                                <!--begin::Info-->
                                <div class="mb-7 noi-dung">
                                    <div class="d-flex justify-content-between align-items-cente my-1">
                                        <span class="text-dark-75 font-weight-bolder mr-2">Đánh giá:</span>
                                        <a href="https://zalo.me/{{ $admin->tel }}" class="text-muted text-hover-primary"></a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark-75 font-weight-bolder mr-2">Dự án đã làm:</span>
                                        <a href="mailto:{{ $admin->email }}" class="text-muted text-hover-primary">{{ $so_da_da_lam }}</a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-cente my-1">
                                        <span class="text-dark-75 font-weight-bolder mr-2">Dự án đang làm:</span>
                                        <a href="https://zalo.me/{{ $admin->tel }}" class="text-muted text-hover-primary">{{ $so_da_dang_lam }}</a>
                                    </div>
                                </div>
                                <div class="mb-7 noi-dung">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark-75 font-weight-bolder mr-2">Email:</span>
                                        <a href="mailto:{{ $admin->email }}" target="_blank" class="text-muted text-hover-primary">{{ $admin->email }}</a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-cente my-1">
                                        <span class="text-dark-75 font-weight-bolder mr-2">SĐT:</span>
                                        <a target="_blank" class="text-muted text-hover-primary">{{ $admin->tel }}</a>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-cente my-1">
                                        <span class="text-dark-75 font-weight-bolder mr-2">Zalo:</span>
                                        <a href="https://zalo.me/{{ $admin->tel }}" target="_blank" class="text-muted text-hover-primary">{{ $admin->zalo }}</a>
                                    </div>
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Col-->
                    @endforeach
                </div>
                <!--end::Row-->
                @endif
            </div>                                                                                         <!--end::Container-->
        </div>
        <!--end::Entry-->
        

@endsection
@section('custom_head')
    <style type="text/css">
        .kt-datatable__cell > span > a.cate {
            color: #5867dd;
            margin-bottom: 3px;
            background: rgba(88, 103, 221, 0.1);
            height: auto;
            display: inline-block;
            width: auto;
            padding: 0.15rem 0.75rem;
            border-radius: 2px;
        }

        .paginate > ul.pagination > li {
            padding: 5px 10px;
            border: 1px solid #ccc;
            margin: 0 5px;
            cursor: pointer;
        }

        .paginate > ul.pagination span {
            color: #000;
        }

        .paginate > ul.pagination > li.active {
            background: #0b57d5;
            color: #fff !important;
        }

        .paginate > ul.pagination > li.active span {
            color: #fff !important;
        }

        .kt-widget12__desc, .kt-widget12__value {
            text-align: center;
        }

        @-webkit-keyframes chartjs-render-animation {
            from {
                opacity: 0.99 list_user
            }
            to {
                opacity: 1
            }
        }

        @keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        .chartjs-render-monitor {
            -webkit-animation: chartjs-render-animation 0.001s;
            animation: chartjs-render-animation 0.001s;
        }

        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
    <style type="text/css">
        @-webkit-keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        @keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        .chartjs-render-monitor {
            -webkit-animation: chartjs-render-animation 0.001s;
            animation: chartjs-render-animation 0.001s;
        }

        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }


        @media (max-width: 768px) {
            .kt-widget12 .kt-widget12__content .thong_ke_so {
                display: inline-block;
            }

            .thong_ke_so .col-sm-3 {
                display: inline-block;
                width: 50%;
                float: left;
                padding: 0;
                margin-bottom: 20px;
            }
        }

        .symbol.symbol-lg-75 > img {
    width: 100%;
    max-width: 75px;
    height: 75px;
}
.symbol.symbol-circle > img {
    border-radius: 50%;
}
.card.card-custom.card-stretch.gutter-b {
    height: calc(100% - 25px);
}
.gutter-b {
    margin-bottom: 25px;
}
.card.card-custom {
    -webkit-box-shadow: 0px 0px 30px 0px rgb(82 63 105 / 5%);
    box-shadow: 0px 0px 30px 0px rgb(82 63 105 / 5%);
    border: 0;
}
.card.card-custom > .card-body {
    padding: 2rem 2.25rem;
}
.noi-dung {
    margin: 10px 0px;
    border-bottom: 1px dotted;
}
.dang-ban {
    color: red;
}
.dang-ranh {
    color: green;
}
</style>

@endsection
@push('scripts')
    
@endpush

