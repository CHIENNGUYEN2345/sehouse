<p class="note_count_char" style="margin: 0"></p>
<textarea id="{{ $field['name'] }}" name="{{ @$field['name'] }}"
          {!! @$field['inner'] !!} class="form-control count_char {{ @$field['class'] }}" {{ @$field['disabled']=='true'?'disabled':'' }} {{ @strpos(@$field['class'], 'require') !== false ? 'required' : '' }}>{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}</textarea>
<script>
    $(document).ready(function () {
       $('.count_char').keyup(){
           var char = $(this).val();
           console.log(char)
        }
    });
</script>