<style>
    .form-group-dynamic .fieldwrapper > div:nth-child(1) {
        padding-left: 0;
    }

    .fieldwrapper {
        padding: 5px;
        border: 1px solid #ccc;
        margin-bottom: 5px;
    }
</style>
<fieldset id="buildyourform-{{ $field['name'] }}" class="{{ @$field['class'] }}">
    @if(isset($result))
        <?php
        $image_extra = [];
        if (!empty($result->image_extra)) {
            $image_extra = explode('|', $result->image_extra);
        }
        ?>
        @foreach ($image_extra as $k=>$v)
            <div class="fieldwrapper row" id="field">
                <div class="col-xs-10 col-md-10">
                    <div class="row ">
                        <div class="col-xs-12 col-md-2">
                            <img class="img-thumbs_{{$k}}" src="{{ $v }}" alt=""  style="width:100%; border: 1px solid #ddd">
                        </div>
                        <div class="col-xs-12 col-md-10 ">
                            <input type="text" class="form-control fieldname image_extra_{{$k}}"
                                   name="image_extra[]"
                                   value="{{ $v }}"
                                   placeholder="Link Ảnh" required>
                        </div>
                    </div>
                </div>
                <div class="col-xs-2 col-md-2 " style="text-align: right;">
                    <i type="xóa hàng" style="cursor: pointer;"
                       class="btn remove btn btn-danger btn-icon la la-remove"></i>
                </div>
            </div>
        @endforeach
    @else
        <div class="fieldwrapper row" id="field">
            <div class="col-xs-10 col-md-10">
                <div class="row">
                    <div class="col-xs-12 col-md-2">
                        <img class="img-thumbs_0" src="" alt="" style="width:100%; border: 1px solid #ddd"">
                    </div>
                    <div class="col-xs-12 col-md-10">
                        <input type="text" class="form-control fieldname image_extra_0"
                               name="image_extra[]" value=""
                               placeholder="Link Ảnh" required>
                    </div>
                </div>
            </div>
            <div class="col-xs-2 col-md-2 " style="text-align: right;">
                <i type="xóa hàng" style="cursor: pointer;"
                   class="btn remove btn btn-danger btn-icon la la-remove"></i>
            </div>
        </div>
    @endif
</fieldset>
<a class="btn btn btn-primary btn-add-dynamic" style="color: white; margin-top: 20px; cursor: pointer;">
    <span>
        <i class="la la-plus"></i>
        <span>Thêm Ảnh</span>
    </span>
</a>
<script>
    $(document).ready(function () {
        var i = '{{isset($k)?$k:0}}';
        $(".btn-add-dynamic").click(function () {
            i = parseInt(i);
            i++;
            var lastField = $("#buildyourform-{{ $field['name'] }} div:last");
            var intId = (lastField && lastField.length && lastField.data("idx") + 1) || 1;
            var fieldWrapper = $('<div class="fieldwrapper row" style="margin-bottom: 5px;" id="field' + intId + '"/>');
            fieldWrapper.data("idx", intId);
            var fields = $('<div class="col-xs-10 col-md-10">\n' +
                '                            <div class="row">\n' +
                '                                <div class="col-xs-12 col-md-2">\n' +
                '                                    <img class="img-thumbs_'+ i +'" src="" alt="" style="width:100%; border: 1px solid #ddd"">\n' +
                '                                </div>\n' +
                '                                <div class="col-xs-12 col-md-10">\n' +
                '                                    <input type="text" class="form-control fieldname image_extra_'+ i +'"\n' +
                '                                                                           name="image_extra[]" value=""\n' +
                '                                                                           placeholder="Link ảnh" required>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>');
            var removeButton = $('<div class="col-xs-2 col-md-2" style="text-align: right;"><i type="xóa hàng" style="cursor: pointer;" class="btn remove btn btn-danger btn-icon la la-remove" ></i></div>');

            fieldWrapper.append(fields);
            fieldWrapper.append(removeButton);
            $("#buildyourform-{{ $field['name'] }}").append(fieldWrapper);
        });
        $('body').on('keyup', '.image_extra_'+ i , function () {
            $(this).parents('.col-xs-12.col-md-10').parents('.row').children('.col-xs-12.col-md-2').children('img').attr('src',$(this).val());
        });
        $('body').on('click', '.remove', function () {
            $(this).parents('.fieldwrapper').remove();
        });
    });
</script>