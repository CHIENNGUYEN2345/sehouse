@if(@$item->domain->name)
    <a href="{{ route('list-link',['id'=>$item->domain->id])   }}"
       style="font-size: 14px!important;">{!! @$item->domain->name !!}</a>
    <div class="row-actions" style="    font-size: 13px;">
        <span class="edit" title="ID của bản ghi">ID: {{ @$item->domain->id }} | </span>
        @if(in_array('super_admin', $permissions))
            <span class="trash"><a class="delete-warning"
                                   href="{{ url('/admin/domain/delete/' . $item->domain->id) }}"
                                   title="Xóa bản ghi">Xóa</a> | </span>
            <span class="trash"><a class=""
                                   href="{{ url('/admin/domain/' . $item->domain->id) }}"
                                   title="Chỉnh sửa">Chỉnh sửa</a> | </span>
            <span class="trash"><a class=""
                                   href="{{ route('list-link',['id'=>$item->domain->id])   }}"
                                   title="Xem danh sách link">Xem danh sách link</a> | </span>
            <span class="trash"><a class=""
                                   href="{{ route('check_error_link.run',['id'=>$item->domain->id])   }}"
                                   title="Quét theo domain">Quét theo domain</a> | </span>
        @endif
    </div>
@else
    Không thuộc trang web nào
@endif