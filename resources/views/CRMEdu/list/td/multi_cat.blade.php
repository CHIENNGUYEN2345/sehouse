<?php
if ($item->{$field['name']} == null) {echo '';}
$cat_id_arr = [];
foreach (explode('|', $item->{$field['name']}) as $v) {
    if ($v != '') $cat_id_arr[] = $v;
}
$categories = \App\CRMEdu\Models\Category::select(['name'])->whereIn('id', $cat_id_arr)->pluck('name');
$html = '';
foreach ($categories as $v) {
    $html .= $v . ' | ';
}
?>
{{substr($html, 0, -3)}}

