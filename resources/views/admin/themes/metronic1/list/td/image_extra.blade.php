<?php
$src = $item->{$field['name']};
if (strpos($item->{$field['name']}, '|') !== false) {
    $src = @explode('|', $item->{$field['name']})[0];
    if ($src != '') {
        $src = @explode('|', $item->{$field['name']})[1];
    }
}
?>
<div class="kt-media {{ @$field['style'] }}">
    <img data-src="{{ CommonHelper::getUrlImageThumb($src, 80, 80) }}" class="file_image_thumb lazy" title="CLick để phóng to ảnh" style="cursor: pointer;">
</div>