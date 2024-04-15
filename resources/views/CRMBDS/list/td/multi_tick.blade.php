<?php
$cat_ids = explode('|', $item->{$field['name']});
$data = \App\Models\Tag::whereIn('id', $cat_ids)->get(); // Lấy cả dữ liệu, không chỉ name và id
foreach ($data as $tag) {
    echo '<p class="kt-badge kt-badge--bolder kt-badge kt-badge--inline kt-badge--unified-primary"
            style="margin-right: 5px; color: ' . $tag->color . ';">' . $tag->name . '</p>';
}
?>
