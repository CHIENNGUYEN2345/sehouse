
<?php
$src = $item->{$field['name']};
if (strpos($item->{$field['name']}, '|') !== false) {
    $src = @explode('|', $item->{$field['name']})[0];
    if ($src != '') {
        $src = @explode('|', $item->{$field['name']})[1];
    }
}
//dd($src);
?>
{{--<div class="kt-media {{ @$field['style'] }}">--}}
{{--    <img data-src="{{ CommonHelper::getUrlImageThumb($src, 80, 80) }}" class="file_image_thumb lazy" title="CLick để phóng to ảnh" style="cursor: pointer;">--}}
{{--</div>--}}
<button class="text-dark bg-white font-bold border-0 outlight-0 btn-view"
        data-id="{{ $item->id }}"
>
        <img src="{{ asset('/filemanager/userfiles/' . $src) }}" class="" title="CLick để phóng to ảnh" style="width: 70px;height:70px;border-radius: 5%; cursor: pointer;">
</button>
