<a href="/admin/{{ $module['code'] }}/edit?code={{ $item->tel }}-{{ date('d-m-Y', strtotime($item->created_at)) }}-{{ $item->id }}"
        style="    font-size: 14px!important;" class="{{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">{!! $item->{$field['name']} !!}</a>
<div class="row-actions" style="    font-size: 13px;">
    <span class="edit" title="ID của bản ghi">{{trans('admin.id')}}: {{ @$item->id }} | </span>
    <span class="edit"><a
                href="{{ url('/admin/'.$module['code'].'/edit?code=' . $item->tel .'-' .date('d-m-Y', strtotime($item->created_at)) . '-' . $item->id ) }}"
                title="Sửa bản ghi này">Xem chi tiết</a> | </span>
    @if(in_array($module['code'] . '_delete', $permissions) || in_array('super_admin', $permissions))
    <span class=""><a
                href="{{ url('/admin/'.$module['code'].'/' . $item->id . '/duplicate') }}"
                title="Nhân bản bản ghi này">Nhân bản</a> | </span>
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
                                                                            src="/public/images_core/icons/loading.gif">
    </div>
@endif