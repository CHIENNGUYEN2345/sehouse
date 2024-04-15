@php $idPhoto = str_replace(['[', ']'], '', $field['name']);
if(old(@$field['name']) != null) {
    $domain = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $field['value'] = str_replace($domain . '/public/filemanager/userfiles/audio/', '', old(@$field['name']));
}
@endphp

<div class="form-group center-content" id="form-group-{{ $field['name'] }}">
    <div class="thumb">
        <div class="div0-{{ $idPhoto }}">
            <div class="div1-{{ $idPhoto }}">
                <div class="thumbactions_img">
                    <div class="wrap-thumbnail center-content">
                        <div class="kt-avatar kt-avatar--outline">
                            <div class="kt-avatar__holder"></div>
                            <label class="kt-avatar__upload" data-toggle="kt-tooltip"
                                   onclick="BrowseFile('{{ $idPhoto }}');"
                                   data-original-title="Thay đổi {{ trans($field['label']) }}">
                                <i class="fa fa-pen"></i>
                            </label>
                            <a class="kt-avatar__cancel center-content deleteimage handle-delete" name="{{ $idPhoto }}"
                               data-toggle="kt-tooltip"
                               data-original-title="Hủy {{ trans($field['label']) }}">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <br>
                    <input type="hidden" id="{{ $idPhoto }}"
                           name="{{ $field['name'] }}" {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        @if(isset($field['value']) && $field['value'] != '')
        @if(in_array(pathinfo($field['value'], PATHINFO_EXTENSION), ['jpg', 'JPG', 'jpeg', 'png', 'gif']))     //  Neu != file anh thi hien  thi link
        $('.div0-{{ $idPhoto }}').find('.wrap-thumbnail .kt-avatar__holder').attr('style', 'background-image: url(' + '{{ CommonHelper::getUrlImageThumb($field['value'], null, 215) }}' + ');');
        @else
        $('.div0-{{ $idPhoto }}').find('.wrap-thumbnail .kt-avatar__holder').attr('style', 'background-image: url(' + '{{ URL::asset('public/filemanager/userfiles/audio' . $field['value']) }}' + ');');
        @endif
        $('.div1-{{$idPhoto}}').find('.kt-avatar').addClass('kt-avatar--changed');
        $('#{{ $idPhoto }}').val('{{ $field['value'] }}');
        @else
        $('.div0-{{ $idPhoto }}').find('.wrap-thumbnail .kt-avatar__holder').attr('style', 'background-image: url(/public/backend/themes/metronic1/media/misc/no-image-icon.png);');
        @endif

        $('.thumb input#{{ $idPhoto }}').change(function () {
            $('.div0-' + $(this).attr('name')).find('.wrap-thumbnail').html('<img style="width: 150px" src="' + $(this).val() + '">');
            $('.div1-{{$idPhoto}}').find('.kt-avatar').addClass('kt-avatar--changed');
        });
    });

    $('.deleteimage').each(function () {          // Delete
        $(this).click(function () {
            $('.div0-' + $(this).attr('name')).find('.wrap-thumbnail .kt-avatar__holder').attr('style', 'background-image: url(/public/backend/themes/metronic1/media/misc/no-image-icon.png);');
            $(this).parents('.div1-{{$idPhoto}}').find('.kt-avatar').removeClass('kt-avatar--changed');
            $('#' + $(this).attr('name')).val('');
        });
    });

</script>
