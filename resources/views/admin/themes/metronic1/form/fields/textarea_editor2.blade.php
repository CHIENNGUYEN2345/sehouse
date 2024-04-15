<textarea id="{{ $field['name'] }}" name="{{ @$field['name'] }}" {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
 {!! @$field['inner'] !!}
          class="form-control {{ @$field['class'] }} editor" {{ @$field['disabled']=='true'?'disabled':'' }}>{!! old($field['name']) != null ? old($field['name']) : @$field['value'] !!}</textarea>