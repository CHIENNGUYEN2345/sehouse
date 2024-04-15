<div class="col-xs-12 col-lg-12 col-xl-12 col-lg-12 order-lg-1 order-xl-1">
                <!--begin:: Widgets/Finance Summary-->
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title bold uppercase">
                                Từ marketing
                            </h3>
                            <?php
                            $nhan_vien_marketing_ids = \App\Models\RoleAdmin::leftJoin('roles', 'roles.id', '=', 'role_admin.role_id')->whereIn('roles.name', [
                                'marketing',
                            ])->pluck('role_admin.admin_id');
                            ?>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-widget12">
                            <div class="kt-widget12__content">
                                <div class="kt-widget12__item thong_ke_so">
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Tổng HĐ</span>
                                        <span class="kt-widget12__value">{{number_format(@\App\CRMBDS\Models\Bill::whereRaw($whereCompany)->whereRaw($whereRegistration)->where(function ($query) use ($nhan_vien_marketing_ids) {
                                            foreach ($nhan_vien_marketing_ids as $admin_id) {
                                                $query = $query->orWhere('marketer_ids', 'LIKE', '%|' . $admin_id . '|%');
                                            }
                                        })->count(), 0, '.', '.')}}</span>
                                    </div>

                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Landingpage</span>
                                        <span class="kt-widget12__value">{{number_format(@\App\CRMBDS\Models\Bill::whereRaw($whereCompany)->whereRaw($whereRegistration)->where(function ($query) use ($nhan_vien_marketing_ids) {
                                            foreach ($nhan_vien_marketing_ids as $admin_id) {
                                                $query = $query->orWhere('marketer_ids', 'LIKE', '%|' . $admin_id . '|%');
                                            }
                                        })->where('service_id', 1)->where('status', 1)->count(), 0, '.', '.')}}</span>
                                    </div>
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Wordpress</span>
                                        <span class="kt-widget12__value">{{number_format(@\App\CRMBDS\Models\Bill::whereRaw($whereCompany)->whereRaw($whereRegistration)->where(function ($query) use ($nhan_vien_marketing_ids) {
                                            foreach ($nhan_vien_marketing_ids as $admin_id) {
                                                $query = $query->orWhere('marketer_ids', 'LIKE', '%|' . $admin_id . '|%');
                                            }
                                        })->where('service_id', 5)->where('status', 1)->count(), 0, '.', '.')}}</span>
                                    </div>
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Hosting</span>
                                        <span class="kt-widget12__value">{{number_format(@\App\CRMBDS\Models\Bill::whereRaw($whereCompany)->whereRaw($whereRegistration)->where(function ($query) use ($nhan_vien_marketing_ids) {
                                            foreach ($nhan_vien_marketing_ids as $admin_id) {
                                                $query = $query->orWhere('marketer_ids', 'LIKE', '%|' . $admin_id . '|%');
                                            }
                                        })->where('service_id', 2)->where('status', 1)->count(), 0, '.', '.')}}</span>
                                    </div>
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Tên miền</span>
                                        <span class="kt-widget12__value">{{number_format(@\App\CRMBDS\Models\Bill::whereRaw($whereCompany)->whereRaw($whereRegistration)->where(function ($query) use ($nhan_vien_marketing_ids) {
                                            foreach ($nhan_vien_marketing_ids as $admin_id) {
                                                $query = $query->orWhere('marketer_ids', 'LIKE', '%|' . $admin_id . '|%');
                                            }
                                        })->where('service_id', 3)->where('status', 1)->count(), 0, '.', '.')}}</span>
                                    </div>
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Khác</span>
                                        <span class="kt-widget12__value">{{number_format(@\App\CRMBDS\Models\Bill::whereRaw($whereCompany)->whereRaw($whereRegistration)->where(function ($query) use ($nhan_vien_marketing_ids) {
                                            foreach ($nhan_vien_marketing_ids as $admin_id) {
                                                $query = $query->orWhere('marketer_ids', 'LIKE', '%|' . $admin_id . '|%');
                                            }
                                        })->where('service_id', 6)->where('status', 1)->count(), 0, '.', '.')}}</span>
                                    </div>
                                </div>
                                <div class="kt-widget12__item thong_ke_so">
                                    <div class="col-sm-3 kt-widget12__info">
                                        <span class="kt-widget12__desc">Doanh thu từ Marketing</span>
                                        <?php
                                        $doanh_thu_marketing = \App\CRMBDS\Models\Bill::whereRaw($whereCompany)->whereRaw($whereRegistration)->where(function ($query) use ($nhan_vien_marketing_ids) {
                                            foreach ($nhan_vien_marketing_ids as $admin_id) {
                                                $query = $query->orWhere('marketer_ids', 'LIKE', '%|' . $admin_id . '|%');
                                            }
                                        })->sum('total_price');
                                        ?>
                                        <span class="kt-widget12__value">{{number_format($doanh_thu_marketing, 0, '.', '.')}} <small>({{ $doanh_thu != 0 ? (int) (($doanh_thu_marketing/$doanh_thu) * 100) : 0 }}%)</small></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end:: Widgets/Finance Summary-->

            </div>

