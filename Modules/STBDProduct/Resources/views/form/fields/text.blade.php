<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>@endif</label>
<div class="col-xs-12">
    <?php
    $value = old($field['name']) != null ? old($field['name']) : @$field['value'];
    ?>
    @if($value == null)
        <input type="text" name="{{ @$field['name'] }}" class="form-control {{ @$field['class'] }}"
               id="{{ $field['name'] }}" {!! @$field['inner'] !!}
               value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
               {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
               placeholder="{{$field['label']}}"
        >
    @else
        <a target="_blank" href="{{$value}}"> Tải {{$field['label']}}</a>
    @endif
</div>
