<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>@endif</label>
<input type="number" name="{{ @$field['name'] }}" class="form-control {{ @$field['class'] }}"
       {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
       id="{{ $field['name'] }}" {!! @$field['inner'] !!} @if(isset($result) && $result->{$field['name']} != '') @endif
       value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
        {{--placeholder="{{ @$field['label'] }}"--}}
>
<span class="text-danger">{{ $errors->first(@$field['name']) }}</span>

