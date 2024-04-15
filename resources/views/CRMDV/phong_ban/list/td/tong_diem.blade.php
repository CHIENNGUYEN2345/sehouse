<?php
$thanh_vien_ids = \App\Models\Admin::where('phong_ban_id', $item->id)->pluck('id')->toArray();
$tong_diem = \Modules\KitCareBooking\Models\SuKienThanhVien::whereIn('admin_id', $thanh_vien_ids)->sum('diem');
?>
{{ number_format($tong_diem, 0, '.', ',') }} điểm
