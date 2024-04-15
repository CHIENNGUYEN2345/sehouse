<input class="form-control {{ @$field['class'] }}" type="color" id="example-color-input" name="{{ @$field['name'] }}"
       value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}" {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
       placeholder="{{ trans(@$field['label']) }}" {!! @$field['inner'] !!}>