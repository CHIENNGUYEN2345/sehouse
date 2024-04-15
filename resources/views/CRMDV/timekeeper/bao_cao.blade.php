@extends('CRMDV.timekeeper.new_header.new_template')
@section('main')
<?php
$cau_hinh = \App\Models\Setting::where('type', 'gio_lam_tab')->pluck('value', 'name')->toArray();
require base_path('resources/views/CRMDV/timekeeper/funtions.php');
$rooms = [
    '' => '',
    1 => 'Phòng kinh doanh 1',
    2 => 'Phòng kinh doanh 2',
    3 => 'Phòng kinh doanh 3',
    4 => 'Phòng kinh doanh 4',
    5 => 'Phòng kinh doanh 5',
    6 => 'Phòng Telesale',
    10 => 'Kỹ thuật',
    15 => 'Điều hành',
    20 => 'Marketing',
    25 => 'Tuyển dụng',
    30 => 'CSKH',
];
?>
<style>
    @media (max-width: 767px) {

        /*.last-column {*/
        /*    position:  absolute;*/
        /*    width: 5em;*/
        /*    height: 100%;*/
        /*    right: -15px;*/
        /*    top: auto;*/
        /*    margin-top: -1px;*/
        /*    background: #fff;*/
        /*}*/

        /*th, td{*/
        /*    padding: 1px ;*/
        /*    text-align: center;*/

        /*}*/

    }
</style>

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
            <span class="kt-portlet__head-icon">
                <i class="kt-font-brand flaticon-calendar-with-a-clock-time-tools"></i>
            </span>
                    <h3 class="kt-portlet__head-title">
                        Báo cáo chấm công trong tháng {{ date('m', strtotime(date('Y-m')." -0 month")) }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        @if(in_array('timekeeper_edit', $permissions))
                        <a href="/admin/{{ $module['code'] }}" class="btn btn-primary">Lịch sử chấm công</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__body">
                        <div class="kt-widget12">
                            <div class="kt-widget12__content table-responsive">
                                <table class="table table-striped ">
                                    <thead class="kt-datatable__head">
                                        <tr>
                                            <th>STT</th>
                                            <th>Ảnh</th>
                                            <th>Thành viên</th>
                                            <th>Mã</th>
                                            <th>Phòng</th>
                                            <th>Buổi đi làm</th>
                                            <th>Ngày đi làm</th>
                                            <th>Buổi muộn</th>
                                            <th>Về sớm</th>
                                        </tr>
                                    </thead>
                                    <tbody class="kt-datatable__body ps ps--active-y">
                                    	<?php 

                                    	$timekeepers = \App\CRMDV\Models\Timekeeper::leftJoin('admin', 'timekeeper.may_cham_cong_id', '=', 'admin.may_cham_cong_id')
                                    					->select('admin.id as ad_id', 'admin.name as ad_name', 'admin.code as ad_code', 'admin.room_id',
                                                            'admin.may_cham_cong_id', 'admin.image', 'timekeeper.*')
                                    					->where('timekeeper.time', '>', date('Y-m-01 00:00:00', strtotime(date('Y-m')." -0 month")))
                                    					->where('timekeeper.time', '<', date('Y-m-t 23:59:00', strtotime(date('Y-m')." -0 month")))
                                                        ->orderBy('timekeeper.time', 'desc')->get();
                                    	
                                    	$admins = [];
                                        $i = 1;
                                    	foreach ($timekeepers as $timekeeper) {

                                            if (!isset($admins[$timekeeper->ad_id])) {
                                                //	nếu chưa có bản ghi này thì khởi tạo
                                                $admins[$timekeeper->ad_id] = [
                                                    'name' => $timekeeper->ad_name,
                                                    'code' => $timekeeper->ad_code,
                                                    'room_name' => @$rooms[$timekeeper->room_id],
                                                    'tong_cong' => [],
                                                    'di_muon' => [],
                                                    've_som' => [],
                                                    'di_muon_co_phep' => 0,
                                                    'admin_id' => $timekeeper->ad_id,
                                                    'image' => $timekeeper->image,
                                                ];
                                            }
                                            

                                    		$val = checkChamCong($timekeeper, [], $admins[$timekeeper->ad_id]['tong_cong'], $admins[$timekeeper->ad_id]['di_muon'],$admins[$timekeeper->ad_id]['ve_som'], $cau_hinh);

                                    		$admins[$timekeeper->ad_id]['tong_cong'] = $val['tong_cong'];
                                            $admins[$timekeeper->ad_id]['di_muon'] = $val['di_muon'];
                                            $admins[$timekeeper->ad_id]['ve_som'] = $val['ve_som'];
                                            $admins[$timekeeper->ad_id]['di_muon_co_phep'] = $val['di_muon_co_phep'] ? $admins[$timekeeper->ad_id]['di_muon_co_phep'] + 1: $admins[$timekeeper->ad_id]['di_muon_co_phep'];

                                    	}

                                        ?>
                                        @foreach($admins as $key => $value)
                                            <tr data-admin_id="{{ $value['admin_id'] }}">
                                                <td><?php echo $i; $i++;?></td>
                                                <td>
                                                    <div class="kt-media" style="width: 40px;">
                                                        <img data-src="{{ CommonHelper::getUrlImageThumb($value['image'], 80, 80) }}" class="file_image_thumb lazy" title="CLick để phóng to ảnh" style="cursor: pointer;">
                                                    </div>
                                                </td>
                                                <td><a href="/admin/timekeeper/bao-cao/{{ $value['admin_id'] }}">{{ $value['name'] }}</a></td>
                                                <td>{{ $value['code'] }}</td>
                                                <td>{{ $value['room_name'] }}</td>
                                                <td>{{ count($value['tong_cong']) }}</td>
                                                <td>{{ count($value['tong_cong'])/2 }}</td>
                                                <td class="{{ count($value['di_muon']) > 3 ? 'text-red' : '' }}">{{ count($value['di_muon']) }}</td>
                                                <td class="{{ count($value['di_muon']) > 3 ? 'text-red' : '' }}">{{ count($value['ve_som']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
    <style>
        /*fix cứng dòng đầu*/
        /*.kt-portlet thead.kt-datatable__head {*/
        /*    top: 45px;*/
        /*    z-index: 1;*/
        /*    background: #ffffff;*/
        /*    margin-right: 2%;*/
        /*}*/
        /*.scrolled thead.kt-datatable__head th:nth-child(1) {*/
        /*    padding-right: 6vw;*/
        /*}*/
        /*.scrolled thead.kt-datatable__head th:nth-child(2) {*/
        /*    width: 9vw;*/
        /*}*/
        /*.scrolled thead.kt-datatable__head th:nth-child(3) {*/
        /*    width: 13vw;*/
        /*}*/
        /*.scrolled thead.kt-datatable__head th:nth-child(4) {*/
        /*    width: 7vw;*/
        /*}*/
        /*.scrolled thead.kt-datatable__head th:nth-child(5) {*/
        /*    width: 8vw;*/
        /*}*/
        /*.scrolled thead.kt-datatable__head th:nth-child(6) {*/
        /*    width: 9vw;*/
        /*}*/
        /*.scrolled thead.kt-datatable__head th:nth-child(7) {*/
        /*    width: 8vw;*/
        /*}*/
        /*.scrolled thead.kt-datatable__head th:nth-child(8) {*/
        /*    width: 9vw;*/
        /*}*/
        /*.kt-portlet .kt-portlet__body {*/
        /*    margin-bottom: 300px;*/
        /*}*/

        /*.kt-portlet td {*/
        /*    width: 100px; !* Set the desired fixed width for the table cells *!*/
        /*    white-space: nowrap; !* Prevent line breaks within cells *!*/
        /*    overflow: hidden; !* Hide any overflowing content *!*/
        /*    text-overflow: ellipsis; !* Show ellipsis (...) for text that overflows *!*/
        /*}*/
        /*.text-red {*/
        /*    color: red !important;*/
        /*}*/
    </style>
@endsection
@section('custom_footer')
    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset(config('core.admin_asset').'/js/list.js') }}"></script>
    @include(config('core.admin_theme').'.partials.js_common')

    <script>
        //  fix cứng dòng đầu
        // window.addEventListener('scroll', function() {
        //     var header = document.querySelector('.kt-datatable__head');
        //     var container = document.querySelector('.kt-widget12__content');
        //     var rect = container.getBoundingClientRect();
        //
        //     if (rect.top <= 0 && rect.bottom > 0) {
        //         header.style.position = 'fixed';
        //         header.style.top = '20';
        //         container.classList.add('scrolled');
        //     } else {
        //         header.style.position = 'static';
        //         container.classList.remove('scrolled');
        //     }
        // });
    </script>
@endsection
@push('scripts')
    @include(config('core.admin_theme').'.partials.js_common_list')
@endpush
