@php $idPhoto2 = str_replace(['[', ']'], '', $field['name']);
if(old(@$field['name']) != null) {
    $domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $field['value'] = str_replace($domain . '/filemanager/userfiles/', '', old(@$field['name']));
}
@endphp
<div class="thumb">
    <div class="div0-{{ $idPhoto2 }}">
        <div class="div1-{{ $idPhoto2 }}" style="display: none;">
            <div class="thumbactions_img">
                <div class="wrap-thumbnail"></div>
                <div class="thumbactions" data-thumb="thumbnail">
                    <a class="btn btn-block btn-danger deleteimage" name="{{ $idPhoto2 }}"><i
                                class="fa fa-trash"></i></a>
                </div>
            </div>
        </div>
        <div class="div2-{{ $idPhoto2 }}" style="display: block;">
            <i class="fa fa-plus fa-2x {{ $idPhoto2 }}" style="cursor: pointer"
               onclick="BrowseFile('{{ $field['name']}}');"></i>
            <h4 class="text-muted {{ $idPhoto2 }}"
                onclick="BrowseFile('{{ $field['name']}}');" style="cursor: pointer">
                Chọn {{ $field['label'] }}.</h4>
            <input type="hidden" id="{{ $idPhoto2 }}"
                   name="{{ $field['name'] }}" {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}>
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {
        @if(isset($field['value']) && $field['value'] != '')
        @if(in_array(pathinfo($field['value'], PATHINFO_EXTENSION), ['jpg', 'JPG', 'jpeg', 'png', 'gif']))     //  Neu != file anh thi hien  thi link
        $('.div0-{{ $idPhoto2 }}').find('.wrap-thumbnail').html('<img data-src="{{ CommonHelper::getUrlImageThumb($field['value'], null, 215) }}" class="lazy file_image_thumb">');
        @else
        $('.div0-{{ $idPhoto2 }}').find('.wrap-thumbnail').html('<a href="{{ URL::asset('filemanager/userfiles/' . $field['value']) }}" target="_blank">{{ $field['value'] }}</a>');
        @endif
        $('.thumb .div1-{{ $idPhoto2 }}').css('display', 'block');
        $('.thumb .div2-{{ $idPhoto2 }}').css('display', 'none');
        $('#{{ $idPhoto2 }}').val('{{ $field['value'] }}');
        @endif
    });


</script>
