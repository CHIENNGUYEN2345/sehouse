<?php
$tong_luong = ((int) $item->luong_co_ban + (int) $item->kpi) * \App\Models\Admin::where('status', 1)->where('phong_ban_id', $item->id)->count();
?>
<span title="(lương cơ bản + kpi) x số thành viên">{{ number_format($tong_luong, 0, '.', '.') }}</span>
