<?php
require base_path('resources/views/CRMDV/dhbill/partials/du_an_quy_diem.php');
    ?>
<div class="kt-portlet kt-portlet--height-fluid">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                Top kỹ thuật
            </h3>
            <?php

            $admins = \App\Models\Admin::leftJoin('role_admin', 'role_admin.admin_id', '=', 'admin.id')
                            ->select(['admin.name', 'admin.id', 'admin.code'])
                            ->whereIn('role_admin.role_id', [
                                173,      //  kỹ thật
                                188,    //  trưởng phòng KT
                                178,    //  điều hành
                            ])->groupBy('role_admin.admin_id')->get();

            ?>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="kt-widget12">
            <div class="kt-widget12__content">
                <table class="table table-striped">
                    <thead class="kt-datatable__head">
                    <tr>
                        <th>Tên</th>
                        <th>Mã</th>
                        <th>Điểm số</th>
                    </tr>
                    </thead>
                    <tbody class="kt-datatable__body ps ps--active-y">
                    <?php
                    $tong_diem = 0;
                    $ds_admin = [];
                    foreach($admins as $admin){

                        $bill_progress = \App\CRMDV\Models\Bill::rightJoin('bill_progress', 'bill_progress.bill_id', '=', 'bills.id')
                            ->select(['bills.service_id', 'bill_progress.dh_id', 'bill_progress.kt_id'])
                            ->whereIn('bill_progress.status', [
                                            'Khách xác nhận xong'   //  chỉ lấy dự án đã xác nhận xong
                                        ])
                            ->where(function ($query) use ($admin) {
                                $query->orWhere('bill_progress.dh_id', $admin->id);   //  lấy dự án mình là điều hành
                                $query->orWhere('bill_progress.kt_id', $admin->id);   //  lấy dự án mình là kỹ thuật
                            })->get();

                        $diem = 0;

                        foreach($bill_progress as $bill_progres) {
                            if ($bill_progres->dh_id == $admin->id) {
                                //  nếu dự án do mình điều hành thì tính điểm điều hành
                                if (isset($diem_dh[$bill_progres->service_id])) {

                                    $diem += (float) @$diem_dh[$bill_progres->service_id];
                                }
                            }
                            if ($bill_progres->kt_id == $admin->id) {
                                //  nếu dự án do mình triển khai thì tính điểm kỹ thuật
                                if (isset($diem_kt[$bill_progres->service_id])) {
                                    $diem += @$diem_kt[$bill_progres->service_id];
                                }

                            }
                        }
                        $tong_diem += $diem;
                        $ds_admin[$diem][] = $admin;
                    }
                    unset($ds_admin[0]);
                    krsort($ds_admin);
                    ?>
                    @foreach($ds_admin as $diem => $val)
                        @foreach($val as $admin)
                            <tr>
                                <td>{{ $admin->name }}</td>
                                <td>{{ $admin->code }}</td>
                                <td>{{ $diem }}</td>
                            </tr>
                        @endforeach
                    @endforeach


                    <tr>
                        <td></td>
                        <td><strong>TỔNG CỘNG</strong></td>
                        <td><strong>{{ number_format($tong_diem, 0, '.', '.') }}</strong></td>
                    </tr>
                    </tbody>
                </table>
                <i style="font-size: 8px;">Truy vấn theo tháng hiện tại</i>
            </div>
        </div>
    </div>
</div>