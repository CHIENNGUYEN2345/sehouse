<input type="email" name="{{ @$field['name'] }}" class="form-control {{ @$field['class'] }}"
       id="{{ $field['name'] }}" {!! @$field['inner'] !!}
       value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
       {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
       >