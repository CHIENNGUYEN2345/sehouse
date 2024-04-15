<input type="text" name="{{ @$field['name'] }}" class="form-control format_number {{ @$field['class'] }}"
       {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
       id="{{ $field['name'] }}" {!! @$field['inner'] !!}
       value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
       >