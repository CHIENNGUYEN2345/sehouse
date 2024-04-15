<?php 
$val = checkChamCong($item, [], [], [], $cau_hinh);

if ($val['phut_tre'] == 'Đúng giờ') {
	echo '<span style="">Đúng giờ</span>';
} else {
	echo '<span style="background: #951b00; color: #fff;">Trễ '.$val['phut_tre'].' phút</span>';
}
?>
