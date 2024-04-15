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
    <div class="" id="field1">
        <div class="col-xs-10 col-md-10">
            <div class="row">
                <div class="col-xs-6 col-md-6">
                    <label>Số ngày</label>
                </div>
                <div class="col-xs-6 col-md-6">
                    <label>Giá tiền</label>
                </div>
            </div>
        </div>
    </div>
    @if(is_array($data))
        @foreach($data as $v)
            <div class="fieldwrapper" id="field1">
                <div class="col-xs-10 col-md-10">
                    <div class="row">
                        <div class="col-xs-6 col-md-6">

                            <input type="text" class="form-control fieldname"
                                   name="{{ $field['name'] }}_day[]" value="{{ @$v->day }}"
                                   placeholder="Số ngày" required>
                        </div>
                        <div class="col-xs-6 col-md-6">
                            <input type="text" class="number_price form-control fieldname"
                                   name="{{ $field['name'] }}_price[]" value="{{ @$v->price }}"
                                   placeholder="Giá tiền" required>
                        </div>
                    </div>
                </div>
                <div class="col-xs-2 col-md-2" style="left: 33px; display: contents;">
                    <i type="xóa hàng" style="cursor: pointer;"
                       class="btn remove btn btn-danger btn-icon la la-remove"></i>
                </div>
            </div>
        @endforeach
    @endif
</fieldset>
<a class="btn btn btn-primary btn-add-dynamic" style="color: white; margin-top: 20px">
    <span>
        <i class="la la-plus"></i>
        <span>Thêm</span>
    </span>
</a>
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
                '                                                                           name="{{ $field['name'] }}_day[]" value=""\n' +
                '                                                                           placeholder="Số ngày" required>\n' +
                '                                </div>\n' +
                '                                <div class="col-xs-6 col-md-6">\n' +
                '                                    <input type="text" class="number_price form-control fieldname"\n' +
                '                                           name="{{ $field['name'] }}_price[]" value=""\n' +
                '                                           placeholder="Giá tiền" required>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '                        </div>');
            var removeButton = $('<div class="col-xs-2 col-md-2"><i type="xóa hàng" style="cursor: pointer;" class="btn remove btn btn-danger btn-icon la la-remove" ></i>');

            fieldWrapper.append(fields);
            fieldWrapper.append(removeButton);
            $("#buildyourform-{{ $field['name'] }}").append(fieldWrapper);
        });
        $('body').on('click', '.remove', function () {
            $(this).parents('.fieldwrapper').remove();
        });
    });
</script>