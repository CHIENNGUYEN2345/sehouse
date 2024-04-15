<a href="/admin/{{ $module['code'] }}/edit/{{ $item->id }}"
   target="_blank">
    {{ @$item->{$field['object']}->{$field['display_field'] . '_' . $language} }}
</a>
@include('admin.themes.metronic1.list.td.partials.row_actions')
@if(isset($field['tooltip_info']))
    <div id="tooltip-info-{{$field['name']}}" class="div-tooltip_info" data-modal="{{ $module['modal'] }}"
         data-tooltip_info="{{ json_encode($field['tooltip_info']) }}"><img style="margin-top: 20%;"
                                                                            src="/images_core/icons/loading.gif">
    </div>
@endif