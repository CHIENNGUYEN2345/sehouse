{{ @$item->service->name_vi }}<br>

<?php
require base_path('resources/views/CRMBDS/dhbill/partials/du_an_quy_diem.php');
    ?>
<span style="    font-size: 10px;
    color: #1c1b1b;">ĐH: {{ @$diem_dh[$item->service_id] }}đ | KT: {{ @$diem_kt[$item->service_id] }}đ</span>