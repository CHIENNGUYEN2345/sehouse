<?php
$thang_sau = date("m", strtotime($start_date)) + 1;
if ($thang_sau < 10) $thang_sau = '0' . $thang_sau;
$luong_start_date = date("Y-m-21 00:00:01", strtotime($start_date));    //  từ ngày 21 tháng trước
$luong_end_date = date("Y-" . $thang_sau . "-20 00:00:01", strtotime($start_date)); //   đến ngày 20 tháng sau
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d 23:59:00');

$tong_luong = 0;
?>
<div class="kt-portlet kt-portlet--height-fluid">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                Chi phí
            </h3>
        </div>
    </div>
    <?php
        
    $whereDate = "date >= '" . $start_date . " 00:00:00' AND date <= '" . $end_date . " 23:59:59'";
    ?>
    <div class="kt-portlet__body">
        <div class="kt-widget12">
            <div class="kt-widget12__content">
                <div class="kt-widget12__item thong_ke_so">
                    <div class="col-sm-3 kt-widget12__info">
                        <span class="kt-widget12__desc font-vua">Lương KD</span>
                        <?php
                        $val = \App\CRMDV\Models\BillReceipts::where('price', '<', 0)
                            ->whereRaw("date >= '" . $luong_start_date . "' AND date <= '" . $luong_end_date . "'")
                            ->whereIn('type', ['luong_kd'])
                            ->sum('price');
                        $tong_luong += $val;
                        ?>
                        <span class="kt-widget12__value font-vua">{{number_format($val, 0, '.', '.')}}</span>
                    </div>
                    <div class="col-sm-3 kt-widget12__info">
                        <span class="kt-widget12__desc font-vua">Lương KT</span>
                        <?php
                        $val = \App\CRMDV\Models\BillReceipts::where('price', '<', 0)
                            ->whereRaw("date >= '" . $luong_start_date . "' AND date <= '" . $luong_end_date . "'")
                            ->whereIn('type', ['luong_kt'])
                            ->sum('price');
                        $tong_luong += $val;
                        ?>
                        <span class="kt-widget12__value font-vua">{{number_format($val, 0, '.', '.')}}</span>
                    </div>
                    <div class="col-sm-3 kt-widget12__info">
                        <span class="kt-widget12__desc font-vua">Lương khác</span>
                        <?php
                        $val = \App\CRMDV\Models\BillReceipts::where('price', '<', 0)
                            ->whereRaw("date >= '" . $luong_start_date . "' AND date <= '" . $luong_end_date . "'")
                            ->whereIn('type', ['luong'])
                            ->sum('price');
                        $tong_luong += $val;
                        ?>
                        <span class="kt-widget12__value font-vua">{{number_format($val, 0, '.', '.')}}</span>
                    </div>
                    <div class="col-sm-3 kt-widget12__info">
                        <span class="kt-widget12__desc font-vua">Phúc lợi</span>
                        <?php
                        $val = \App\CRMDV\Models\BillReceipts::where('price', '<', 0)
                            ->whereRaw("date >= '" . $luong_start_date . "' AND date <= '" . $luong_end_date . "'")
                            ->whereIn('type', ['phuc_loi'])
                            ->sum('price');
                        $tong_luong += $val;
                        ?>
                        <span class="kt-widget12__value font-vua">{{number_format($val, 0, '.', '.')}}</span>
                    </div>
                </div>
                <div class="kt-widget12__item thong_ke_so">
                    <div class="col-sm-3 kt-widget12__info">
                        <span class="kt-widget12__desc font-vua">Cơ sở VC</span>
                        <?php
                        $val = \App\CRMDV\Models\BillReceipts::whereRaw($whereDate)
                            ->where('price', '<', 0)
                            ->whereIn('type', ['co_so'])
                            ->sum('price');
                        ?>
                        <span class="kt-widget12__value font-vua">{{number_format($val, 0, '.', '.')}}</span>
                    </div>
                    <div class="col-sm-3 kt-widget12__info">
                        <span class="kt-widget12__desc font-vua">Cơ sở số</span>
                        <?php
                        $val = \App\CRMDV\Models\BillReceipts::whereRaw($whereDate)
                            ->where('price', '<', 0)
                            ->whereIn('type', ['co_so_so'])
                            ->sum('price');
                        ?>
                        <span class="kt-widget12__value font-vua">{{number_format($val, 0, '.', '.')}}</span>
                    </div>

                    <div class="col-sm-3 kt-widget12__info">
                        <span class="kt-widget12__desc font-vua">Khác</span>
                        <?php
                        $val = \App\CRMDV\Models\BillReceipts::whereRaw($whereDate)
                            ->where('price', '<', 0)
                            ->whereIn('type', ['khac'])
                            ->sum('price');
                        ?>
                        <span class="kt-widget12__value font-vua">{{number_format($val, 0, '.', '.')}}</span>
                    </div>
                </div>
                <div class="kt-widget12__item thong_ke_so">
                    <div class="col-sm-3 kt-widget12__info">
                        <span class="kt-widget12__desc">Tổng chi</span>
                        <?php
                        $chi_ko_gom_luong = \App\CRMDV\Models\BillReceipts::where('price', '<', 0)->whereRaw($whereDate)
                            ->whereNotIn('type', ['luong', 'luong_kd', 'luong_kt', 'phuc_loi'])->sum('price');
                        ?>
                        <span class="kt-widget12__value">{{number_format($chi_ko_gom_luong + $tong_luong, 0, '.', '.')}}</span>
                    </div>
                    <div class="col-sm-3 kt-widget12__info">
                        <span class="kt-widget12__desc">Tổng lương</span>
                        <span class="kt-widget12__value">{{number_format($tong_luong, 0, '.', '.')}}</span>
                    </div>
                    <div class="col-sm-3 kt-widget12__info">
                        <span class="kt-widget12__desc">Đầu tư</span>
                        <?php
                        $val = \App\CRMDV\Models\BillReceipts::whereRaw($whereDate)
                            ->where('price', '<', 0)
                            ->whereRaw("date >= '" . $luong_start_date . "' AND date <= '" . $luong_end_date . "'")
                            ->whereIn('type', ['dt'])
                            ->sum('price');
                        ?>
                        <span class="kt-widget12__value">{{number_format($val, 0, '.', '.')}}</span>
                    </div>
                </div>

                <p><i style="font-size: 8px;">Truy vấn theo: bộ lọc thời gian, lương lấy từ 1-15 tháng sau của lọc ngày bắt đầu</i></p>
            </div>
        </div>
    </div>
</div>