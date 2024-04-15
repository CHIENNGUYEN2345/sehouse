@if((in_array($module['code'] . '_edit', $permissions)&& $item->admin_id == \Auth::guard('admin')->user()->id) || in_array('super_admin', $permissions) )
    <span class="edit"><a
                href="{{ url('/admin/'.$module['code'].'/edit/' . $item->id) }}"
                title="Sửa bản ghi này"> {{trans('admin.edit')}} </a></span>
@endif
@if((in_array($module['code'] . '_delete', $permissions)&& $item->admin_id == \Auth::guard('admin')->user()->id) || in_array('super_admin', $permissions) )

    | <span class="trash"><a class="delete-warning"
                             href="{{ url('/admin/'.$module['code'].'/delete/' . $item->id) }}"
                             title="Xóa bản ghi"> {{trans('admin.delete')}} </a></span>
@endif