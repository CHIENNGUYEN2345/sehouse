<style>
    .kt-avatar .btn-xoa-anh {
        display: block !important;
        text-align: center;
    }
    .kt-avatar .btn-xoa-anh i {
        color: #000;
    }
</style>
<div class="kt-avatar kt-avatar--outline {{isset($result->{$field['name']}) && $result->{$field['name']} != '' ? 'kt-avatar--changed': ''}}"
     id="kt_user_avatar_{{ $field['name'] }}">
    <div class="kt-avatar__holder">
        @if(isset($field['value']) && $field['value'] != '')
            <img style="max-width:100%;padding-top:5px; cursor: pointer;"
                 src="{{ CommonHelper::getUrlImageThumb($field['value'], 120, 120) }}"
                 class="file_image_thumb" title="CLick để phóng to ảnh">
        @endif
    </div>
    @if(!isset($field['disabled']))
        <label class="kt-avatar__upload" data-toggle="kt-tooltip" title=""
               data-original-title="Đổi {{ trans(@$field['label']) }}">
            <i class="fa fa-pen"></i>
            <input type="file" name="{{ @$field['name'] }}" accept=".png, .jpg, .jpeg"
                   class="{{ @$field['class'] }}"
                   <?php if( strpos(@$field['class'], 'require') !== false && !isset($field['value']) ) echo 'required'; ?>
                   id="{{ $field['name'] }}"
                   value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
                    {{ @$field['multiple'] }}>
            <input type="hidden" name="{{ @$field['name'] }}_delete" value="0">
        </label>
        <span class="kt-avatar__cancel btn-xoa-anh" data-toggle="kt-tooltip" title="" data-original-title="Xóa">
                            <i class="fa fa-times"></i>
                        </span>
    @endif
</div>
@push('scripts')
    <script>
        "use strict";

        // Class definition
        var KTAvatarDemo{{ $field['name'] }} = function () {
            // Private functions
            var initDemos = function () {
                new KTAvatar('kt_user_avatar_{{ $field['name'] }}');
            }

            return {
                // public functions
                init: function () {
                    initDemos();
                }
            };
        }();

        KTUtil.ready(function () {
            KTAvatarDemo{{ $field['name'] }}.init();
        });


        $('input[name={{ @$field['name'] }}]').change(function () {
            $(this).parents('.kt-avatar').find('.kt-avatar__holder img').css('opacity', '0');
            $(this).parents('.kt-avatar').find('input[name={{ @$field['name'] }}_delete]').val(0);
        });

        $('.kt-avatar .btn-xoa-anh').click(function () {
            $(this).parents('.kt-avatar').find('input[name={{ @$field['name'] }}_delete]').val(1);
            $(this).parents('.kt-avatar').find('.kt-avatar__holder img').remove();
        });
    </script>
@endpush
