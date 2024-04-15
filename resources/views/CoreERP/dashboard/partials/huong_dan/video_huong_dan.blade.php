<style>
    .min-h-180px {
        min-height: 100px !important;
    }

    .w-100 {
        width: 100% !important;
    }

    .bgi-no-repeat {
        background-repeat: no-repeat;
    }

    .bgi-size-cover {
        background-size: cover;
    }

    .hd-video {
        width: 200px;
    }

</style>
<div class="col-xs-12 col-lg-12 col-xl-12 col-lg-12 order-lg-1 order-xl-1">
    <!--begin:: Widgets/Finance Summary-->
    <div class="kt-portlet kt-portlet--height-fluid">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title bold uppercase">
                    Hướng dẫn sử dụng
                </h3>
                <?php
                $role_id = @\App\Models\RoleAdmin::where('admin_id', \Auth::guard('admin')->user()->id)->first()->role_id;
                $hd = \App\CRMDV\CoreERP\Models\Guide::where('status', 1)->where('role_ids', 'like', '%|' . $role_id . '|%')
                    ->orderBy('order_no', 'desc')->orderBy('id', 'desc')
                    ->get();
                ?>
            </div>
        </div>
        <div class="kt-portlet__body">
            @foreach($hd as $v)
                <div class="d-flex flex-column flex-center hd-video">
                    <!--begin::Image-->
                    <div class="bgi-no-repeat bgi-size-cover rounded min-h-180px w-100"
                         style="background-image: url(/filemanager/userfiles/{{ $v->image }})"></div>
                    <!--end::Image-->

                    <!--begin::Title-->
                    <a href="{{ $v->link }}" target="_blank"
                       class="card-title font-weight-bolder text-dark-75 text-hover-primary font-size-h4 m-0 pt-7 pb-1">{{ $v->name }}</a>
                    <!--end::Title-->
                </div>
            @endforeach
        </div>
    </div>
</div>