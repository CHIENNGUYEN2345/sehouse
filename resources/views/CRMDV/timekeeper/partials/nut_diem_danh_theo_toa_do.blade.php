<?php

use Carbon\Carbon;

$diem_danh = \App\CRMDV\Models\Timekeeper::where('may_cham_cong_id', \Auth::guard('admin')->user()->may_cham_cong_id)
    ->where('time', '>', date('Y-m-d 00:01:00'))->count();
$gio_diem_danh = \App\CRMDV\Models\Timekeeper::where('may_cham_cong_id', \Auth::guard('admin')->user()->may_cham_cong_id)
    ->where('time', '>', date('Y-m-d 00:01:00'))->first();
$admin = \App\Models\Admin::find(\Auth::guard('admin')->user()->id);
$time_end = \App\CRMDV\Models\Timekeeper::where('may_cham_cong_id', \Auth::guard('admin')->user()->may_cham_cong_id)
    ->where('time', '>', date('Y-m-d 00:01:00'))
    ->orderBy('created_at', 'desc')
    ->first();
$currentDateTime = Carbon::now();
//dd(\Carbon\Carbon::now()->hour);
//dd(\Carbon\Carbon::parse($time_end->time)->format('H:i:s Y-m-d'));
?>

<style>
    #vi_tri_nv_so_voi_vp {
        font-size: 12px;
        color: #ffffff;
        white-space: normal;
    }

    #nut-diem-danh {
        margin: 5px 5px;
        width: 150px;
    }

    @media (max-width: 425px) {
        #vi_tri_nv_so_voi_vp {
            white-space: normal;
        }

        #nut-diem-danh {
            padding: 15px;
        }

        .box-content-text {
            font-family: inherit;
        }
    }
</style>

<div class="diem-danh-button col-md-4">

    <a class="btn btn-primary" id="nut-diem-danh" href="/diem-danh?admin_id={{ $admin->id }}">Điểm danh hôm nay</a>
</div>
<p style="color: white" id="vi_tri_nv_so_voi_vp"></p>
@if($diem_danh != 0)
    @if(\Carbon\Carbon::now()->hour < 7 || \Carbon\Carbon::now()->hour > 20)
        <p style="width: 100%; color: #ffffff; font-size: 12px">Đã hết thời gian điểm danh</p>
    @elseif ($currentDateTime->hour >= 7 && $currentDateTime->hour < 10 && $diem_danh == 0)
        <p style="width: 100%; color: #ffffff; font-size: 12px">Bạn chưa điểm danh trong ngày hôm nay </p>
    @elseif ($currentDateTime->hour >= 10 && $currentDateTime->hour < 17 && $diem_danh == 0)
        <p style="width: 100%; color: #ffffff; font-size: 12px">Bạn chưa điểm danh trong ngày hôm nay </p>
    @elseif($currentDateTime->hour >= 7 && $currentDateTime->hour < 10 && $diem_danh != 0)
        <p style="width: 100%; color: #ffffff; font-size: 12px">Bạn đã điểm danh buổi sáng
            lúc {{\Carbon\Carbon::parse($time_end->time)->format('H:i:s Y-m-d')}} </p>
    @elseif($currentDateTime->hour >= 10 && $currentDateTime->hour < 12 && $diem_danh == 1)
        <p style="width: 100%; color: #ffffff; font-size: 12px">Bạn chưa điểm danh ra về buổi sáng</p>
    @elseif ($currentDateTime->hour >= 12 && $currentDateTime->hour < 15 && $diem_danh == 0)
        <p style="width: 100%; color: #ffffff; font-size: 12px">Bạn chưa điểm danh trong ngày hôm nay </p>
        @dd(1)
    @elseif($currentDateTime->hour >= 12 && $currentDateTime->hour < 15 && $diem_danh != 0)
        <p style="width: 100%; color: #ffffff; font-size: 12px">Bạn đã điểm danh buổi chiều </p>
    @elseif($currentDateTime->hour >= 15 && $currentDateTime->hour < 18 && \Carbon\Carbon::parse($time_end->time)->hour <=15 && $diem_danh != 0)
        <p style="width: 100%; color: #ffffff; font-size: 12px">Bạn chưa điểm danh ra về</p>
        @dd(2)
    @elseif($currentDateTime->hour >= 15 && $currentDateTime->hour < 18 && \Carbon\Carbon::parse($time_end->time)->hour >= 15 && \Carbon\Carbon::parse($time_end->time)->hour <= 18)
        <p style="width: 100%; color: #ffffff; font-size: 12px">Bạn đã điểm danh ra về
            lúc {{\Carbon\Carbon::parse($time_end->time)->format('H:i:s Y-m-d')}}</p>
        {{--@else--}}
        {{--    <p style="width: 100%; color: #ffffff; font-size: 12px">Đã điểm danh </p>--}}
    @endif
@else
    <p style="width: 100%; color: #ffffff; font-size: 12px">Bạn chưa điểm danh trong ngày hôm nay</p>
@endif

@if($diem_danh !== 0)
    <script>
        $('#nut-diem-danh').click(function () {
            window.location.href = $('#nut-diem-danh').attr('href');
        });

        function getLocation() {
            console.log("bắt đầu định vị nhân viên");
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(kiemTraHienThiNutChamCong);
            } else {
                $('#vi_tri_nv_so_voi_vp').html("Định vị địa lý không được hỗ trợ bởi trình duyệt này.");
            }
        }

        getLocation();


        function kiemTraHienThiNutChamCong(position) {
            console.log("nv_lat: " + position.coords.latitude);
            console.log("nv_long: " + position.coords.longitude);
            const nv_lat = position.coords.latitude;
            const nv_long = position.coords.longitude;

                <?php $vp_location = \App\Models\Setting::where('type', 'cham_cong_tab')->pluck('value', 'name')->toArray();
                $diemdanh = \App\CRMDV\Models\Timekeeper::where('may_cham_cong_id', \Auth::guard('admin')->user()->may_cham_cong_id)
                    ->where('time', '>', date('Y-m-d 00:01:00'))
                    ->orderBy('time', 'desc')
                    ->first();
//                    dd($diemdanh);
                ?>
            const diemdanh = {!! json_encode($diemdanh) !!};
            // console.log(typeof diemdanh);
            const vp_lat = {{ @$vp_location['vp_lat'] }}; // Latitude of position 2
            const vp_long = {{ @$vp_location['vp_long'] }}; // Longitude of position 2
            const distance = calculateHaversine(nv_lat, nv_long, vp_lat, vp_long);
            $('#vi_tri_nv_so_voi_vp').html('Bạn cách văn phòng ' + (distance.toFixed(2) * 1000) + ' mét');
            console.log(`Bạn cách văn phòng: ${distance.toFixed(2) * 1000} mét`);
            {{--distance.toFixed(2) * 1000 < {{ @$vp_location['cham_cong_xa_toi_da'] }} &&--}}
            if (diemdanh !== null) {
                const thoigian = new Date("{{ $diemdanh->time }}").getTime();
                // Thực hiện các thao tác khác nếu cần
                // console.log(thoigian)
                var currentTimeVN = new Date().toLocaleString('en-US', {timeZone: 'Asia/Ho_Chi_Minh'});
                var chenhLech = (Date.parse(currentTimeVN) - thoigian) / (60 * 1000);
                console.log(chenhLech);
                var gioHienTai = new Date().getHours();
                console.log(gioHienTai);
                if (gioHienTai > 7 || gioHienTai < 20) {
                    if (chenhLech > 10 && distance.toFixed(2) * 1000 < {{ @$vp_location['cham_cong_xa_toi_da'] }}) {
                        // Nếu trong phạm vi thì cho chấm công
                        $('#nut-diem-danh').css('pointer-events', 'auto');

                    } else {
                        // console.log(2);
                        $('#vi_tri_nv_so_voi_vp').html('Bạn đang không nằm trong vị trí chấm công');
                    }
                } else {
                    $('#vi_tri_nv_so_voi_vp').html('Đã hết thời gian chấm công');

                }

            }


        }


        //  Tính khoảng cách giữa văn phòng công ty và vị trí đang đứng
        function toRadians(degrees) {
            return degrees * Math.PI / 180;
        }

        function calculateHaversine(lat1, lon1, lat2, lon2) {
            // Convert latitude and longitude from degrees to radians
            lat1 = toRadians(lat1);
            lon1 = toRadians(lon1);
            lat2 = toRadians(lat2);
            lon2 = toRadians(lon2);

            // Haversine formula
            const dLat = lat2 - lat1;
            const dLon = lon2 - lon1;

            const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLon / 2) ** 2;
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            // Radius of the Earth in kilometers (you can use 3959 for miles)
            const radius = 6371;

            // Calculate the distance
            const distance = radius * c;

            return distance;
        }


    </script>
@else
    <script>
        $('#nut-diem-danh').click(function () {
            window.location.href = $('#nut-diem-danh').attr('href');
        });

        function getLocation() {
            console.log("bắt đầu định vị nhân viên");
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(kiemTraHienThiNutChamCong);
            } else {
                $('#vi_tri_nv_so_voi_vp').html("Định vị địa lý không được hỗ trợ bởi trình duyệt này.");
            }
        }

        getLocation();


        function kiemTraHienThiNutChamCong(position) {
            console.log("nv_lat: " + position.coords.latitude);
            console.log("nv_long: " + position.coords.longitude);
            const nv_lat = position.coords.latitude;
            const nv_long = position.coords.longitude;

                <?php $vp_location = \App\Models\Setting::where('type', 'cham_cong_tab')->pluck('value', 'name')->toArray();
                $diemdanh = \App\CRMDV\Models\Timekeeper::where('may_cham_cong_id', \Auth::guard('admin')->user()->may_cham_cong_id)
                    ->where('time', '>', date('Y-m-d 00:01:00'))
                    ->orderBy('time', 'desc')
                    ->first();
//                    dd($diemdanh);
                ?>
            const diemdanh = {!! json_encode($diemdanh) !!};
            // console.log(typeof diemdanh);
            const vp_lat = {{ @$vp_location['vp_lat'] }}; // Latitude of position 2
            const vp_long = {{ @$vp_location['vp_long'] }}; // Longitude of position 2
            console.log(vp_lat, vp_long);
            const distance = calculateHaversine(nv_lat, nv_long, vp_lat, vp_long);
            $('#vi_tri_nv_so_voi_vp').html('Bạn cách văn phòng ' + (distance.toFixed(2) * 1000) + ' mét');
            console.log(`Bạn cách văn phòng: ${distance.toFixed(2) * 1000} mét`);
            console.log({{ @$vp_location['cham_cong_xa_toi_da'] }})
            var gioHienTai = new Date().getHours();
            if (gioHienTai > 7 && gioHienTai < 20) {
                console.log(distance.toFixed(2) * 1000);
                if (distance.toFixed(2) * 1000 < {{ @$vp_location['cham_cong_xa_toi_da'] }}) {
                    // Nếu trong phạm vi thì cho chấm công
                    $('#nut-diem-danh').css('pointer-events', 'auto');

                } else {
                    // console.log(2);
                    $('#vi_tri_nv_so_voi_vp').html('Bạn đang không nằm trong vị trí chấm công');
                }
            } else {
                $('#vi_tri_nv_so_voi_vp').html('Đã hết thời gian chấm công');

            }
        }


        //  Tính khoảng cách giữa văn phòng công ty và vị trí đang đứng
        function toRadians(degrees) {
            return degrees * Math.PI / 180;
        }

        function calculateHaversine(lat1, lon1, lat2, lon2) {
            // Convert latitude and longitude from degrees to radians
            lat1 = toRadians(lat1);
            lon1 = toRadians(lon1);
            lat2 = toRadians(lat2);
            lon2 = toRadians(lon2);

            // Haversine formula
            const dLat = lat2 - lat1;
            const dLon = lon2 - lon1;

            const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dLon / 2) ** 2;
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            // Radius of the Earth in kilometers (you can use 3959 for miles)
            const radius = 6371;

            // Calculate the distance
            const distance = radius * c;

            return distance;
        }


    </script>

@endif
