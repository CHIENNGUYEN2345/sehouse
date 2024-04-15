<style>
    div#form-group-price_options .ul_multiple_image_editor {
        display: inline-block;
        padding: 0;
        width: 100%;
    }

    div#form-group-price_options .ul_multiple_image_editor > li {
        width: 100% !important;
    }

    div#form-group-price_options .ul_multiple_image_editor li {
        width: unset !important;
        height: unset !important;
        float: left;
        background-color: #fff;
        border-radius: 4px;
        /*color: white;*/
        display: inline-block;
        font-weight: bold;
        text-decoration: none;
        position: relative;
        margin-left: 0 !important;
    }

    div#form-group-price_options .ul_multiple_image_editor li:first-child {
        margin: 0;
    }

    div#form-group-price_options .ul_multiple_image_editor .up_img_t {
        text-align: center;
        overflow: hidden;
        cursor: pointer;
    }

    div#form-group-price_options .ul_multiple_image_editor .up_img_t img {
        width: 100%;
        max-height: 108px;
        margin: auto;
    }
</style>
@if (\Schema::hasTable('product_attributes'))
    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
            <span class="color_btd">*</span>@endif</label>
    <div class="col-xs-12">
        <ul class="ul_multiple_image_editor">
            @php
                $attibutes = \Modules\STBDProduct\Models\ProductAttribute::where('product_id', @$result->id)->get();
            @endphp
            @foreach($attibutes as $attr)
                @include('stbdproduct::partials.ajax_html_price_option', ['attr' => $attr])
            @endforeach
            <li id="add_img-{{ $field['name'] }}" style="cursor: pointer;
    line-height: 150px;
    text-align: center;
"><a style="color: green;text-decoration: underline;">Thêm tùy chọn giá</a></li>
        </ul>
    </div>
    <script>
        $(document).ready(function () {
            $('body').on('click', '.handle-delete', function () {
                $('.item-' + $(this).attr('name')).find('.wrap-thumbnail .kt-avatar__holder').attr('style', 'background-image: url(/public/backend/themes/metronic1/media/misc/no-image-icon.png);');
                $(this).parents('.item-' + $(this).attr('name')).find('.kt-avatar').removeClass('kt-avatar--changed');
                $('#' + $(this).attr('name')).val('');
            });

            $('#add_img-{{ $field['name'] }}').click(function () {
                loading();
                var object = $(this);
                $.ajax({
                    url: '/admin/product/ajax-get-html-price-option',
                    success: function (resp) {
                        stopLoading();
                        object.before(resp);
                    },
                    error: function () {
                        alert('Có lỗi xảy ra! Vui lòng load lại website & thử lại');
                        stopLoading();
                    }
                });
            });

            $('body').on('click', '.attr-delete', function () {
                $(this).parents('li').remove();
            });
        });

    </script>
@endif