<input type="date" name="{{ $field['name'] }}" class="form-control {{ @$field['class'] }}"
       {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }} {!! @$field['inner'] !!}
       id="{{ $field['name'] }}"
       value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
{{--       value="{{ date('Y-m-d',strtotime(old($field['name']) != null ? old($field['name']) : @$field['value'] ))}}"--}}
       >
{{--{{dd(date('d-m-Y',strtotime(old($field['name']) != null ? old($field['name']) : @$field['value'] ))}}--}}