<?php
$val = checkChamCong($item, [], [], [],[], $cau_hinh);
//dd($val);
if ($val['phut_tre'] == 'Đúng giờ') {
    echo '<span style="">Đúng giờ</span>';
} elseif ($val['phut_som'] == 'Đúng giờ') {
    echo '<span style="">Đúng giờ</span>';
} elseif ($val['phut_tre'] !== 'Đúng giờ' && $val['phut_som']=='') {
    echo '<span style="background: #951b00; color: #fff;">Vào muộn ' . $val['phut_tre'] . ' phút</span>';
} elseif ($val['phut_som'] !== 'Đúng giờ' && $val['phut_tre']=='') {
    echo '<span style="background: #951b00; color: #fff;">Về sớm ' . $val['phut_som'] . ' phút</span>';
}
?>
