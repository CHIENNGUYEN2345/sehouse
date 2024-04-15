@if(@$field['value'] != '')
       <a href="{{ asset('filemanager/userfiles/'. @$field['value']) }}" target="_blank">{{ @$field['value'] }}</a>
@endif
<input type="file" name="{{ @$field['name'] }}" class="form-control {{ $field['class'] or ''}}"
       {{ strpos(@$field['class'], 'require') !== false && @$field['value'] == '' ? 'required' : '' }}
       id="{{ $field['name'] }}"
       value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
        {{ @$field['multiple'] }}>