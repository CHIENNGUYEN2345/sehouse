<?php
$values = explode('|',$item->{$field['name']});
$model = new $field['model'];
$data = $model->whereIn('id', $values)->pluck($field['display_field'], 'id');
?>
@foreach($data as $k => $v)
[<a href="/admin/{{ @$field['object'] }}/edit/{{ $k }}"
   target="_blank">
    {{ $v }}
</a>]
@endforeach
@if(isset($field['tooltip_info']))
    <div id="tooltip-info-{{@$field['name']}}" class="div-tooltip_info" data-modal="{{ $module['modal'] }}"
         data-tooltip_info="{{ json_encode(@$field['tooltip_info']) }}"><img style="margin-top: 20%;" src="/images_core/icons/loading.gif"></div>
@endif