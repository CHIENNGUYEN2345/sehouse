<?php

if (old($field['name']) != null) {
       $value = old($field['name']);
} elseif (isset($field['value'])) {
       if ($field['value'] != '') {
              $value = date('Y-m-d', strtotime($field['value']));
       } else {
              $value = '';
       }
} else {
       $value = date('Y-m-d');
}
?>
<input type="date" name="{{ $field['name'] }}" class="form-control {{ @$field['class'] }}"
       {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }} {!! @$field['inner'] !!}
       id="{{ $field['name'] }}" value="{{ $value }}"
       >