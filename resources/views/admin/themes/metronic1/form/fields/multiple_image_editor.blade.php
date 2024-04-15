<style>
    .ul_multiple_image_editor {
        display: inline-block;
        padding: 0;
        width: 100%;
    }

    .ul_multiple_image_editor li {
        width: 140px !important;
        height: 150px !important;
        float: left;
        background-color: #fff;
        border-radius: 4px;
        color: white;
        display: inline-block;
        font-weight: bold;
        text-decoration: none;
        position: relative;
        margin-left: 0 !important;
    }

    .ul_multiple_image_editor li:first-child {
        margin: 0;
    }

    .ul_multiple_image_editor .up_img_t {
        text-align: center;
        overflow: hidden;
        cursor: pointer;
    }

    .ul_multiple_image_editor .up_img_t img {
        width: 100%;
        max-height: 108px;
        margin: auto;
    }
</style>

<ul class="ul_multiple_image_editor">
    @php
        $images = explode('|', @$result->{$field['name']}); $count_available = 1;

    @endphp
    @if($images != null && is_array($images))
        @foreach($images as $image)
            @if($image != '')
                <li style="margin-left: 20px">
                    <div class="up_img_t item-{{ $field['name'] }}_{{ $count_available }}">
                        <div class="thumbactions_img" style="padding-top: 20px;">
                            <div class="wrap-thumbnail center-content">
                                <div class="kt-avatar kt-avatar--outline kt-avatar--changed">
                                    <div class="kt-avatar__holder image-value-{{$count_available}}"
                                         style="background-image: url({{ asset('filemanager/userfiles/' . $image) }});"></div>
                                    <label class="kt-avatar__upload" data-toggle="kt-tooltip"
                                           onclick="BrowseMultiFile('{{ $field['name'] }}_{{ $count_available }}');"
                                           data-original-title="Thay đổi Ảnh sản phẩm">
                                        <i class="fa fa-pen"></i>
                                    </label>
                                    <a class="kt-avatar__cancel center-content deleteimage-{{$count_available}} handle-delete"
                                       name="{{ $field['name'] }}_{{ $i }}"
                                       data-toggle="kt-tooltip" data-original-title="Hủy Ảnh sản phẩm">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </div>
                            <br>
                            <input type="hidden" id="{{ $field['name'] }}_{{ $i }}" value=""
                                   name="{{ $field['name'] }}_{{ $i }}">
                        </div>
                    </div>
                </li>
                @php $count_available ++; @endphp
            @endif
        @endforeach
    @endif
    @for($i = $count_available; $i <= $field['count']; $i ++)
        <li style="margin-left: 20px">
            <div class="up_img_t item-{{ $field['name'] }}_{{ $i }}">
                <div class="thumbactions_img" style="padding-top: 20px;">
                    <div class="wrap-thumbnail center-content">
                        <div class="kt-avatar kt-avatar--outline">
                            <div class="kt-avatar__holder image-value-{{$i}}"
                                 style="background-image: url(/backend/themes/metronic1/media/misc/no-image-icon.png);"></div>
                            <label class="kt-avatar__upload" data-toggle="kt-tooltip"
                                   onclick="BrowseMultiFile('{{ $field['name'] }}_{{ $i }}');"
                                   data-original-title="Thay đổi Ảnh sản phẩm">
                                <i class="fa fa-pen"></i>
                            </label>
                            <a class="kt-avatar__cancel center-content deleteimage handle-delete"
                               name="{{ $field['name'] }}_{{ $i }}"
                               data-toggle="kt-tooltip" data-original-title="Hủy Ảnh sản phẩm">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <br>
                    <input type="hidden" id="{{ $field['name'] }}_{{ $i }}" name="{{ $field['name'] }}_{{ $i }}">
                </div>
            </div>
        </li>
    @endfor
</ul>
<script>
    $('.deleteimage.handle-delete').each(function () {          // Delete
        $(this).click(function () {
            $('.item-' + $(this).attr('name')).find('.wrap-thumbnail .kt-avatar__holder').attr('style', 'background-image: url(/backend/themes/metronic1/media/misc/no-image-icon.png);');
            $(this).parents('.item-' + $(this).attr('name')).find('.kt-avatar').removeClass('kt-avatar--changed');
            $('#' + $(this).attr('name')).val('');
        });
    });
</script>
