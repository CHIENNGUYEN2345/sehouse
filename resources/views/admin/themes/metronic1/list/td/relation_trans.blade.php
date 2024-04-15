{{--<a href="/admin/{{ $field['object'] }}/{{ @$item->{$field['object']}->id }}"--}}
{{--   target="_blank">--}}
{{--    {{ @$item->{$field['object']}->{$field['display_field'] . '_' . $language} }}--}}
{{--</a>--}}
{{--@if(isset($field['tooltip_info']))--}}
{{--    <div id="tooltip-info-{{$field['name']}}" class="div-tooltip_info" data-modal="{{ $module['modal'] }}"--}}
{{--         data-tooltip_info="{{ json_encode($field['tooltip_info']) }}"><img style="margin-top: 20%;" src="/images_core/icons/loading.gif"></div>--}}
{{--@endif--}}


<a href="/admin/{{ $module['code'] }}/edit/{{ $item->id }}"
   target="_blank">
    {{ @$item->{$field['object']}->{$field['display_field'] . '_' . $language} }}
</a>
<div class="row-actions" style="    font-size: 13px;">
    <span class="edit" title="ID của bản ghi">{{trans('handymanservicesbooking::admin.id')}}: {{ @$item->id }} | </span>
    <span class="edit"><a
                href="{{ url('/admin/'.$module['code'].'/edit/' . $item->id) }}"
                title="xem">{{trans('handymanservicesbooking::admin.see')}}</a></span>
</div>
