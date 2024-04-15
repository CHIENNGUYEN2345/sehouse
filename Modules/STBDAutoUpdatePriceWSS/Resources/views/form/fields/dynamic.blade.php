<style>
    .form-group-dynamic .fieldwrapper > div:nth-child(1) {
        padding-left: 0;
    }

    .fieldwrapper {
        padding: 5px;
        border: 1px solid #ccc;
        margin-bottom: 5px;
    }

    div#form-group-{{ $field['name'] }}   {
        display: inline-block;
        padding: 0;
        width: 100%;
    }

    div#form-group-{{ $field['name'] }}   > li {
        width: 100% !important;
    }

    div#form-group-{{ $field['name'] }}  li {
        width: 100%;
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

    div#form-group-{{ $field['name'] }}  li:first-child {
        margin: 0;
    }

    div#form-group-{{ $field['name'] }}  .up_img_t {
        text-align: center;
        overflow: hidden;
        cursor: pointer;
    }

    div#form-group-{{ $field['name'] }} .up_img_t img {
        width: 100%;
        max-height: 108px;
        margin: auto;
    }

    div#form-group-{{ $field['name'] }} .item-field {
        display: inline-block;
        float: left;
    }
</style>
<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>@endif</label>
<div class="col-xs-12">
    <ul class="ul_multiple_image_editor" style="display: inline-block; width: 100%; padding: 0;">
        @php
            $categories = \Modules\STBDAutoUpdatePriceWSS\Entities\DoomCategory::where('website_id', @$result->id)->get();
        @endphp
        @foreach($categories as $cat)
            @include('stbdautoupdatepricewss::form.partials.ajax_html_select_category', ['cat' => $cat])
        @endforeach
    </ul>
</div>
<a class="btn btn btn-primary btn-add-dynamic{{ $field['name'] }}"
   style="color: white; margin-top: 20px; cursor: pointer;">
    <span>
        <i class="la la-plus"></i>
        <span>Thêm Chuyên mục</span>
    </span>
</a>
<script>
    $(document).ready(function () {
        $(".btn-add-dynamic{{ $field['name'] }}").click(function () {
            loading();
            var object = $(this);
            $.ajax({
                url: '/admin/website/ajax_html_select_category',
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