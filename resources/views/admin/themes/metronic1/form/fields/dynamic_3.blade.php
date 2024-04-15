<fieldset id="buildyourform-{{ $field['name'] }}" class="buildyourform {{ @$field['class'] }}">
    <div class="fieldwrapper">
        <div class="col-xs-3">{{ @$field['cols'][0] }}</div>
        <div class="col-xs-3">{{ @$field['cols'][1] }}</div>
        <div class="col-xs-3">{{ @$field['cols'][2] }}</div>
        <div class="col-xs-3">
        </div>
    </div>
    @php $data = \App\Models\Attribute::select(['value', 'key', 'value2'])->where('table', $module['table_name'])->where('type', $field['name'])->where('item_id', @$result->id)->get(); @endphp
    @foreach($data as $v)
        <div class="fieldwrapper">
            <div class="col-xs-3"><input type="text" class="form-control fieldname" name="{{ $field['name'] }}_key[]"
                                         value="{{ $v->key }}"
                                         placeholder="{{ @$field['cols'][0] }}"></div>
            <div class="col-xs-3"><input type="text" class="form-control fieldValue" value="{{ $v->value }}"
                                         name="{{ $field['name'] }}_value[]" placeholder="{{ @$field['cols'][1] }}">
            </div>
            <div class="col-xs-3"><input type="text" class="form-control fieldValue2" value="{{ $v->value2 }}"
                                         name="{{ $field['name'] }}_value2[]" placeholder="{{ @$field['cols'][2] }}">
            </div>
            <div class="col-xs-3"><input type="button" class="col-xs-2 btn remove" value="Xóa" title="Xóa hàng">
            </div>
        </div>
    @endforeach
</fieldset>
<input type="button" value="Thêm hàng" class="btn btn-primary add" id="add-{{ $field['name'] }}"/>
<span class="text-danger">{{ $errors->first($field['name']) }}</span>
<script>
    $(document).ready(function () {
        $("#add-{{ $field['name'] }}").click(function () {
            var lastField = $("#buildyourform-{{ $field['name'] }} div:last");
            var intId = (lastField && lastField.length && lastField.data("idx") + 1) || 1;
            var fieldWrapper = $('<div class="fieldwrapper" id="field' + intId + '"/>');
            fieldWrapper.data("idx", intId);
            var fKey = $('<div class="col-xs-3"><input type="text" class="form-control fieldname" name="{{ $field["name"] }}_key[]" placeholder="{{ @$field['cols'][0] }}"/></div>');
            var fValue = $('<div class="col-xs-3"><input type="text" class="form-control fieldValue" name="{{ $field["name"] }}_value[]" placeholder="{{ @$field['cols'][1] }}"/></div>');
            var fValue2 = $('<div class="col-xs-3"><input type="text" class="form-control fieldValue2" name="{{ $field["name"] }}_value2[]" placeholder="{{ @$field['cols'][2] }}"/></div>');
            var removeButton = $('<div class="col-xs-2"><input type="button" class="col-xs-2 btn remove" value="Xóa" title="Xóa hàng"/></div>');
            fieldWrapper.append(fKey);
            fieldWrapper.append(fValue);
            fieldWrapper.append(fValue2);
            fieldWrapper.append(removeButton);
            $("#buildyourform-{{ $field['name'] }}").append(fieldWrapper);
        });

        $('body').on('click', '.remove', function () {
            $(this).parents('.fieldwrapper').remove();
        });
    });
</script>