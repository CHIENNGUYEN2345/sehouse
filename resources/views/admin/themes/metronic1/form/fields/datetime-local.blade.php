
<input type="datetime-local" name="{{ $field['name'] }}" class="form-control {{ @$field['class'] }}"
       {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
       id="{{ $field['name'] }}" {!! @$field['inner'] !!}
       @if(isset($field['value']))value="{{ date('Y-m-d\TH:i:s', strtotime(old($field['name']) != null ? old($field['name']) : @$field['value'])) }}"
       @endif placeholder="{{ @$field['value'] }}">