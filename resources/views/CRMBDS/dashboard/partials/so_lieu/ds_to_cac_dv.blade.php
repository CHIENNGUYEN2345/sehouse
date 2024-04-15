<div class="kt-portlet kt-portlet--height-fluid">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                Doanh số từ khoá học
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="kt-widget12">
            <div class="kt-widget12__content">
                <?php
                $service_ids = \App\CRMBDS\Models\Bill::whereRaw($whereRegistration)->groupBy('service_id')->pluck('service_id')->toArray();
                $services = \App\CRMBDS\Models\Service::select('id', 'name_vi')->whereIn('id', $service_ids)->get();

                //  Sắp xếp khoá học nào nhiều doanh số nhất lên đầu
                $service_arr = [];
                foreach ($services as $service) {
                    $service->ds_dv = @\App\CRMBDS\Models\Bill::whereRaw($whereRegistration)->where('service_id', $service->id)->where('status', 1)->sum('total_price');
                    $service_arr[$service->ds_dv] = $service;
                }
                krsort($service_arr);
//                        dd($service_arr);
                ?>
                <div class="kt-widget12__item thong_ke_so">

                    @foreach($service_arr as $service)
                        <div class="col-sm-3 kt-widget12__info">
                            <span class="kt-widget12__desc">{{ $service->name_vi }}</span>
                            <span class="kt-widget12__value">{{number_format($service->ds_dv, 0, '.', '.')}}
                            <i style="    font-weight: 100 !important;
    font-size: 12px;">({{ round(($service->ds_dv / $doanh_so)*100) }}%)</i></span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>