<a href="/admin/{{ $module['code'] }}/edit/{{ $item->id }}/draft"
        style="    font-size: 14px!important;" class="{{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">{!! $item->{$field['name']} !!}</a>
<div class="row-actions" style="    font-size: 13px;">
    <span class="edit" title="ID của bản ghi">{{trans('admin.id')}}: {{ @$item->id }} | </span>
    <span class="edit"><a
                href="{{ url('/admin/'.$module['code'].'/edit/' . $item->id.'/draft') }}"
                title="Sửa bản ghi này">{{trans('admin.edit')}}</a> | </span>
    @if(in_array($module['code'] . '_delete', $permissions))
        <span class="trash"><a class="delete-warning"
                               href="{{ url('/admin/'.$module['code'].'/delete/' . $item->id) }}"
                               title="Xóa bản ghi">{{trans('admin.delete')}}</a> | </span>
    @endif
</div>

@if(isset($field['tooltip_info']))
    {{--{{dd($field['tooltip_info'])}}--}}
    <div id="tooltip-info-{{$field['name']}}" class="dropdown-menu div-tooltip_info"
         data-modal="{{ $module['modal'] }}"
         data-tooltip_info="{{ json_encode($field['tooltip_info']) }}"><img class="tooltip_info_loading"
                                                                            src="/images_core/icons/loading.gif">
    </div>
@endif
