@extends(config('core.admin_theme').'.template')
@section('main')
<?php
$cau_hinh = \App\Models\Setting::where('type', 'gio_lam_tab')->pluck('value', 'name')->toArray();

function checkChamCong($item, $ls_cham_cong, $tong_cong, $di_muon, $cau_hinh) {

    $cham_cong_trung = false;
    $di_muon_co_phep = false;
    $phut_tre = 'Đúng giờ';
    // $item->time = '2022-12-11 07:23:00';
    if (strtotime($item->time) < strtotime(date('Y-m-d 10:00:00', strtotime($item->time)))) {
        //  Buổi sáng đến làm

        if (isset($ls_cham_cong[date('d', strtotime($item->time)) . 's_den'])) {
            $cham_cong_trung = true;
        }
        $ls_cham_cong[date('d', strtotime($item->time)) . 's_den'] = 1;
        $tong_cong[date('d', strtotime($item->time)) . 's'] = 1;


        if (strtotime($item->time) > strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_sang'], strtotime($item->time)))) {
            //  đi muộn
            if ($item->status != 1) {
                //  ko có phép
                $di_muon[date('d', strtotime($item->time)) . 's'] = 1;  //  đi muộn ngày này buổi sáng
            } elseif ($item->status == 1) {
                $di_muon_co_phep = true;
            }

            $phut_tre = ceil((strtotime($item->time) - strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_sang'], strtotime($item->time)))) / 60);


        } else {
            //  đúng giờ

        }
    } elseif (strtotime($item->time) >= strtotime(date('Y-m-d 10:00:00', strtotime($item->time))) & strtotime($item->time) < strtotime(date('Y-m-d 12:15:00', strtotime($item->time)))) {
        //  buổi sáng đi về
        if (isset($ls_cham_cong[date('d', strtotime($item->time)) . 's_ve'])) {
            $cham_cong_trung = true;
        }
        $ls_cham_cong[date('d', strtotime($item->time)) . 's_ve'] = 1;
    } elseif (strtotime($item->time) >= strtotime(date('Y-m-d 12:15:00', strtotime($item->time))) & strtotime($item->time) < strtotime(date('Y-m-d 15:00:00', strtotime($item->time)))) {
        //  Buổi chiều đến làm
        if (isset($ls_cham_cong[date('d', strtotime($item->time)) . 'c_den'])) {
            $cham_cong_trung = true;
        }

        $ls_cham_cong[date('d', strtotime($item->time)) . 'c_den'] = 1;
        $tong_cong[date('d', strtotime($item->time)) . 'c'] = 1;

        if ( ( strtotime($item->time) > strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_chieu'], strtotime($item->time))) )) {
            //  đi muộn
            if ($item->status != 1) {
                $di_muon[date('d', strtotime($item->time)) . 'c'] = 1;  //  đi muộn ngày này buổi chiều
            } elseif ($item->status == 1) {
                $di_muon_co_phep = true;
            }

            $phut_tre = ceil((strtotime($item->time) - strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_chieu'], strtotime($item->time)))) / 60);

        } else {
            //  đúng giờ

        }
    } elseif (strtotime($item->time) >= strtotime(date('Y-m-d 15:00:00', strtotime($item->time))) ) {
        //  Buổi chiều về
        if (isset($ls_cham_cong[date('d', strtotime($item->time)) . 'c_ve'])) {
            $cham_cong_trung = true;
        }
        $ls_cham_cong[date('d', strtotime($item->time)) . 'c_ve'] = 1;
    }

    return [
        'ls_cham_cong' => $ls_cham_cong,
        'tong_cong' => $tong_cong,
        'di_muon' => $di_muon,
        'di_muon_co_phep' => $di_muon_co_phep,
        'phut_tre' => $phut_tre,
    ];
}
?>
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
            <span class="kt-portlet__head-icon">
                <i class="kt-font-brand flaticon-calendar-with-a-clock-time-tools"></i>
            </span>
                    <h3 class="kt-portlet__head-title">
                        Báo cáo chấm công trong tháng {{ date('m', strtotime(date('Y-m')." -1 month")) }}
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        @if(in_array('timekeeper_view', $permissions))
                        <a href="/admin/{{ $module['code'] }}" class="btn btn-primary">Lịch sử chấm công</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__body">
                        <div class="kt-widget12">
                            <div class="kt-widget12__content">
                                <table class="table table-striped">
                                    <thead class="kt-datatable__head">
                                        <tr>
                                            <th>Thành viên</th>
                                            <th>Mã</th>
                                            <th>Phòng ban</th>
                                            <th>Số buổi đi làm</th>
                                            <th>Số buổi đi muộn</th>
                                            <th>Số buổi đi muộn có phép</th>
                                        </tr>
                                    </thead>
                                    <tbody class="kt-datatable__body ps ps--active-y">
                                    	<?php 

                                    	$timekeepers = \App\CRMEdu\Models\Timekeeper::leftJoin('admin', 'timekeeper.may_cham_cong_id', '=', 'admin.may_cham_cong_id')
                                    					->select('admin.id as ad_id', 'admin.name as ad_name', 'admin.code as ad_code', 'admin.may_cham_cong_id', 'timekeeper.*')
                                    					->where('timekeeper.time', '>', date('Y-m-01 00:00:00', strtotime(date('Y-m')." -1 month")))
                                    					->where('timekeeper.time', '<', date('Y-m-t 23:59:00', strtotime(date('Y-m')." -1 month")))
                                                        ->orderBy('timekeeper.time', 'desc')->get();
                                    	
                                    	$admins = [];
                                    	foreach ($timekeepers as $timekeeper) {
                                            
                                    		if (!isset($admins[$timekeeper->ad_id])) {
                                    			//	nếu chưa có thì khởi tạo
                                    			$admins[$timekeeper->ad_id] = [
	                                    			'name' => $timekeeper->ad_name,
	                                    			'code' => $timekeeper->ad_code,
	                                    			'room_name' => $timekeeper->room_id,
	                                    			'tong_cong' => [],
	                                    			'di_muon' => [],
	                                    			'di_muon_co_phep' => 0,
                                                    'admin_id' => $timekeeper->ad_id,
	                                    		];
                                    		}

                                    		$val = checkChamCong($timekeeper, [], $admins[$timekeeper->ad_id]['tong_cong'], $admins[$timekeeper->ad_id]['di_muon'], $cau_hinh);

                                    		$admins[$timekeeper->ad_id] = [
                                    			'name' => $timekeeper->ad_name,
                                    			'code' => $timekeeper->ad_code,
                                    			'room_name' => $timekeeper->room_id,
                                    			'tong_cong' => $val['tong_cong'],
                                    			'di_muon' => $val['di_muon'],
                                    			'di_muon_co_phep' => $val['di_muon_co_phep'] ? $admins[$timekeeper->ad_id]['di_muon_co_phep'] + 1: $admins[$timekeeper->ad_id]['di_muon_co_phep'],
                                                'admin_id' => $timekeeper->ad_id,
                                    		];

                                    	}

                                        ?>
                                        @foreach($admins as $key => $value)
                                            <tr data-admin_id="{{ $value['admin_id'] }}">
                                                <td>{{ $value['name'] }}</td>
                                                <td>{{ $value['code'] }}</td>
                                                <td>{{ $value['room_name'] }}</td>
                                                <td>{{ count($value['tong_cong']) }}</td>
                                                <td>{{ count($value['di_muon']) }}</td>
                                                <td>{{ $value['di_muon_co_phep'] }}</td>
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
