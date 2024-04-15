<lable>{{ $field['label'] }}</lable><span class="{{ @$field['name'] }}note_count_char" style="color: red"></span>
<textarea maxlength="{{ isset($field['max_char']) ? $field['max_char'] : 120 }}" id="{{ $field['name'] }}" name="{{ @$field['name'] }}"
          {!! @$field['inner'] !!} class="form-control {{ @$field['name'] }}count_char {{ @$field['class'] }}" {{ @$field['disabled']=='true'?'disabled':'' }} {{ @strpos(@$field['class'], 'require') !== false ? 'required' : '' }}>{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}</textarea>
<script>
    $(document).ready(function () {
       $('.{{ @$field['name'] }}count_char').keyup(function () {
           var char = $(this).val().length;
           $('.{{ @$field['name'] }}note_count_char').text(' - Số ký tự đã nhập: '+char);
       })
    })
</script>