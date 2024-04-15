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
<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>@endif</label>
<div class="col-xs-12">
    <fieldset id="buildyourform-{{ $field['name'] }}" class="{{ @$field['class'] }}">
        <?php
        $data1 = [];
        if (isset($result)) {
            $data1 = (array)json_decode($result->form_fields);
        }
        ?>

        @foreach(@$data1 as $k => $v)
            {{--            {{dd($v)}}--}}
            <div class="fieldwrapper" id="field">
                <div class="col-xs-10 col-md-10">
                    <div class="row">
                        <div class="col-xs-6 col-md-6">
                            <input type="text" class="form-control fieldname"
                                   name="{{ $field['name'] }}_names[]" value="{{ @$k }}"
                                   placeholder="Tên trường" required>
                        </div>
                        <div class="col-xs-6 col-md-6">
                            <input type="text" class="form-control fieldname"
                                   name="{{ $field['name'] }}_fields[]" value="{{ @$v }}"
                                   placeholder="GG Field" required>
                        </div>
                    </div>
                </div>
                <div class="col-xs-2 col-md-2" style="float: right; bottom: 37px;">
                    <i type="xóa hàng" style="cursor: pointer;"
                       class="btn remove btn btn-danger btn-icon la la-remove"></i>
                </div>
            </div>
        @endforeach
    </fieldset>
    <a class="btn btn btn-primary btn-add-dynamic" style="color: white; margin-top: 10px; cursor: pointer;">
    <span>
        <i class="la la-plus"></i>
        <span>Thêm</span>
    </span>
    </a>
</div>
<script>
    $(document).ready(function () {
        $(".btn-add-dynamic").click(function () {
            var lastField = $("#buildyourform-{{ $field['name'] }} div:last");
            var intId = (lastField && lastField.length && lastField.data("idx") + 1) || 1;
            var fieldWrapper = $('<div class="fieldwrapper" style="margin-bottom: 5px;" id="field' + intId + '"/>');
            fieldWrapper.data("idx", intId);
            var fields = $('<div class="col-xs-10 col-md-10">\n' +
                '                            <div class="row">\n' +
                '                                <div class="col-xs-6 col-md-6">\n' +
                '                                    <input type="text" class="form-control fieldname"\n' +
                '                                                                           name="{{ $field['name'] }}_names[]" value=""\n' +
                '                                                                           placeholder="Tên trường" required>\n' +
                '                                </div>\n' +
                '                                <div class="col-xs-6 col-md-6">\n' +
                '                                    <input type="text" class="form-control fieldname"\n' +
                '                                                                           name="{{ $field['name'] }}_fields[]" value=""\n' +
                '                                                                           placeholder="GG Field" required>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>');
            var removeButton = $('<div class="col-xs-2 col-md-2" style="float: right; bottom: 38px;"><i type="xóa hàng" style="cursor: pointer;" class="btn remove btn btn-danger btn-icon la la-remove" ></i>');

            fieldWrapper.append(fields);
            fieldWrapper.append(removeButton);
            $("#buildyourform-{{ $field['name'] }}").append(fieldWrapper);
        });
        $('body').on('click', '.remove', function () {
            $(this).parents('.fieldwrapper').remove();
        });
    });
</script>