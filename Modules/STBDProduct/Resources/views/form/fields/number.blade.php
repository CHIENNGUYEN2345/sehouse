<input type="number" name="{{ @$field['name'] }}" class="form-control {{ @$field['class'] }}"
       {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
       id="{{ $field['name'] }}" {!! @$field['inner'] !!}
{{--       value="{{ number_format(, 0, '',',') }}"--}}
       value="{{ old($field['name']) != null ? number_format(old($field['name']), 0, '',',') : number_format(@$field['value'], 0, '',',') }}"
       placeholder="{{ @$field['label'] }}"
       >