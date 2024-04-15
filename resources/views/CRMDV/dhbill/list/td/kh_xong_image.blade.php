<?php
$item->kh_xong_image = @$item->bill_progress->kh_xong_image;
$field['type'] = 'image';
?>
@include(config('core.admin_theme').'.list.td.'.$field['type'])
