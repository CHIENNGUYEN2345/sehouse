<style>
    .fieldwrapper > div:nth-child(1) {
        padding-left: 0;
    }
    .fieldwrapper >div {
        display: inline-block;
    }
</style>
<fieldset id="buildyourform-{{ $field['name'] }}" class="{{ @$field['class'] }}">
    <div class="fieldwrapper" id="field1">
        <div class="col-xs-5 col-md-5">{{ @$field['cols'][0] }}</div>
        <div class="col-xs-5 col-md-5">{{ @$field['cols'][1] }}</div>
        <div class="col-xs-2 col-md-2">
        </div>
    </div>
    @php
        $data = \App\Models\Attribute::where('table', $module['table_name'])->where('type', $field['name'])->where('item_id', @$result->id)->pluck('value', 'key');
    @endphp
    @if(is_array($data))
        @foreach($data as $v)
            <div class="fieldwrapper"  id="field1">
                <div class="col-xs-5 col-md-5 mb-2"><input type="text" class="form-control fieldname"
                                                           name="{{ $field['name'] }}_key[]" value="{{ $v->key }}"
                                                           placeholder="{{ @$field['cols'][0] }}"></div>
                <div class="col-xs-5 col-md-5" style="right: 4px;"><input type="text"
                                                                          class="form-control fieldValue"
                                                                          value="{{ $v->value }}"
                                                                          name="{{ $field['name'] }}_value[]" placeholder="{{ @$field['cols'][1] }}">
                </div>
                <div class="col-xs-2 col-md-2 " style="display: initial;"><i type="xóa hàng" style="cursor: pointer;" class="btn remove btn btn-outline-hover-danger btn-sm btn-icon btn-circle flaticon-delete" ></i>
                </div>
            </div>
        @endforeach
    @endif
</fieldset>
<a class="btn btn-icon btn btn-label btn-label-brand btn-bold btn-add-dynamic" title="Thêm dòng mới">
    <i class="flaticon2-add-1"></i>
</a>
<script>
    $(document).ready(function () {
        $(".btn-add-dynamic").click(function () {
            var lastField = $("#buildyourform-{{ $field['name'] }} div:last");
            var intId = (lastField && lastField.length && lastField.data("idx") + 1) || 1;
            var fieldWrapper = $('<div class="fieldwrapper" style="margin-bottom: 8px;" id="field' + intId + '"/>');
            fieldWrapper.data("idx", intId);
            var fKey = $('<div class="col-xs-5 col-md-5"><input type="text" class="form-control fieldname" name="{{ $field["name"] }}_key[]" placeholder="{{ @$field['cols'][0] }}"/></div>');
            var fValue = $('<div class="col-xs-5 col-md-5"><input type="text" class="form-control fieldValue" name="{{ $field["name"] }}_value[]" placeholder="{{ @$field['cols'][1] }}"/></div>');
            var removeButton = $('<div class="col-xs-2 col-md-2" style="left: 7px;"><i title="xóa hàng" style="cursor: pointer;" class="btn remove btn btn-outline-hover-danger btn-sm btn-icon btn-circle flaticon-delete" ></i>');

            fieldWrapper.append(fKey);
            fieldWrapper.append(fValue);
            fieldWrapper.append(removeButton);
            $("#buildyourform-{{ $field['name'] }}").append(fieldWrapper);
        });
        $('body').on('click', '.remove', function () {
            $(this).parents('.fieldwrapper').remove();
        });
    });
</script>