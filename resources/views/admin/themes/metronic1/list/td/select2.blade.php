<?php
$value = trim($item->{$field['name']}, '|');
$value = trim($value, '|');
$ids = explode('|', $value);
$query = $field['model']::whereIn('id', $ids)->pluck($field['display_field'])->toArray();

?>
{{implode(' | ',$query)}}