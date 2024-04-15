<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
              <span class="color_btd">*</span>@endif</label>
<input type="text" name="{{ @$field['name'] }}" class="form-control {{ @$field['class'] }}"
       id="{{ $field['name'] }}" {!! @$field['inner'] !!} @if(isset($result) && $result->{$field['name']} != '') @endif
       value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
        {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
>
<span class="text-danger">{{ $errors->first(@$field['name']) }}</span>
