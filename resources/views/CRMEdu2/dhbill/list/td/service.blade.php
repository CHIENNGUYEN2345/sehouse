{{ @$item->service->name_vi }}<br>

<?php 
$dh = [
	17 => 1,	//	ldp tiết kiệm
	18 => 1,	//	ldp cơ bản
	19 => 1,	//	ldp chuyên nghiệp
	20 => 3,	//	ldp cao cấp

	10 => 1,	//	wp tiết kiệm
	11 => 3,	//	wp cơ bản
	12 => 2,	//	wp giao diện
	13 => 5,	//	wp chuyên nghiệp
	14 => '6,5',	//	wp special
	15 => 5,	//	wp cao cấp
];

$kt = [
	17 => 2,	//	ldp tiết kiệm
	18 => 3,	//	ldp cơ bản
	19 => 5,	//	ldp chuyên nghiệp
	20 => 5,	//	ldp cao cấp

	10 => 1,	//	wp tiết kiệm
	11 => 2,	//	wp cơ bản
	12 => 8,	//	wp giao diện
	13 => 8,	//	wp chuyên nghiệp
	14 => 11,	//	wp special
	15 => 8,	//	wp cao cấp
];

?>
<span style="    font-size: 10px;
    color: #1c1b1b;">ĐH: {{ @$dh[$item->service_id] }}đ | KT: {{ @$kt[$item->service_id] }}đ</span>