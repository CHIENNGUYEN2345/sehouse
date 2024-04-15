<?php 
$trang_thai = [
	'Thu thập YCTK L1' => 'YC1',
	'Triển khai L1' => 'TK1',
	'Nghiệm thu L1 & thu thập YCTK L2' => 'YC2',
	'Triển khai L2' => 'TK2',
	'Nghiệm thu L2 & thu thập YCTK L3' => 'YC3',
	'Triển khai L3' => 'TK3',
	'Nghiệm thu L3 & thu thập YCTK L4' => 'YC4',
	'Triển khai L4' => 'TK4',
	'Nghiệm thu L4 & thu thập YCTK L5' => 'YC5',
	'Triển khai L5' => 'TK5',
	'Nghiệm thu L5 & thu thập YCTK L6' => 'YC6',
	'Triển khai L6' => 'TK6',
	'Khách xác nhận xong' => 'Xong', 
	'Kết thúc' => 'Kết thúc',
	'Tạm dừng' => 'Tạm dừng',
];
?>
<label for="progress_status">Lịch sử triển khai:</label>
<div>
	{{ date('d/m', strtotime($bill->registration_date)) }}: <span title="{{ date('H:i', strtotime($bill->registration_date)) }}">Ký HĐ</span>

<!-- Hiển thị ra các mốc thời gian thay đổi trạng thái -->
<?php
$point = (object) [
	'created_at' => date('Y-m-d H:i:s', strtotime($bill->registration_date)),
];
?>

@foreach($listItem as $k => $item)
	<?php 
		echo '<font style="color:red;">|</font>';
		$point = new DateTime($point->created_at);
		$ngay_tao = new DateTime($item->created_at);
		$khoang_cach_ngay = $point->diff($ngay_tao);
		for($i = 0; $i < $khoang_cach_ngay->days; $i ++) {
			echo '+';
		}
		echo '<font style="color:red;">|</font>';
	?>

	{{ date('d/m', strtotime($item->created_at)) }}: <span title="{{ date('H:i', strtotime($item->created_at)) }}">{{ @$trang_thai[$item->new_value] }}</span>
	<?php 
	$point = $item;
	?>
@endforeach


<!-- Hiện thị ra ngày cuối cập nhật tới ngày hnay -->
@if(isset($item) && !empty($listItem) && $item->new_value != 'Khách xác nhận xong' && $item->new_value != 'Kết thúc')
	<?php 
	$hom_nay = (object) [
		'created_at' => date('Y-m-d H:i:s'),
	];
	echo '<font style="color:red;">|</font>';
	$hom_nay = new DateTime($hom_nay->created_at);
	$ngay_cuoi = new DateTime($item->created_at);
	$khoang_cach_ngay = $hom_nay->diff($ngay_cuoi);
	for($i = 0; $i < $khoang_cach_ngay->days; $i ++) {
		echo '+';
	}
	echo '<font style="color:red;">|</font>';
	?>
@endif
</div>