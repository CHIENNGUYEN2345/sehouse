<?php

if (old($field['name']) != null) {
       $value = old($field['name']);
} elseif (isset($field['value'])) {
       if ($field['value'] != '') {
              $value = date('Y-m-d', strtotime($field['value']));
       } else {
              $value = '';

       }
} elseif (@$field['value'] == 'now') {
       $value = date('Y-m-d');
} else {
       $value = '';
}
//dd($value, $field);

?>


<input type="date" name="{{ $field['name'] }}" class="form-control {{ @$field['class'] }}"
       {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }} {!! @$field['inner'] !!}
       id="{{ $field['name'] }}"
       {{--       value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"--}}
       value="{{ $value }}"
>
{{--{{dd(date('d-m-Y',strtotime(old($field['name']) != null ? old($field['name']) : @$field['value'] ))}}--}}
