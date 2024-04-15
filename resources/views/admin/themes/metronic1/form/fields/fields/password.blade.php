<input type="password" name="{{ @$field['name'] }}" class="form-control {{ @$field['class'] }}"
       {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }} {!! @$field['inner'] !!}
       id="{{ $field['name'] }}" value="">