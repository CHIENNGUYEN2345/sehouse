@if(in_array('ticket_view', $permissions))
    @if($item->{$field['name']} == 0)
        <a href="{{URL::to('/admin/ticket/publish?id='.$item->id.'&column=status')}}"
           class="kt-badge kt-badge--inline kt-badge--pill"

           style="cursor:pointer;" data-column="{{ $field['name'] }}">Chờ xử lý</a>
    @elseif($item->{$field['name']} == 1)
        <a href="{{URL::to('/admin/ticket/publish?id='.$item->id.'&column=status')}}"
           class="kt-badge  kt-badge--warning kt-badge--inline kt-badge--pill"

           style="cursor:pointer;" data-column="{{ $field['name'] }}">Nhân viên trả lời</a>
    @elseif($item->{$field['name']} == 2)
        <a href="{{URL::to('/admin/ticket/publish?id='.$item->id.'&column=status')}}"
           class="kt-badge  kt-badge--info kt-badge--inline kt-badge--pill"
           style="cursor:pointer;" data-column="{{ $field['name'] }}">Khách trả lời</a>
    @elseif($item->{$field['name']} == 3)
        <a href="{{URL::to('/admin/ticket/publish?id='.$item->id.'&column=status')}}"
           class="kt-badge   kt-badge--inline kt-badge--pill"
           style="cursor:pointer;" data-column="{{ $field['name'] }}">Đã đóng</a>
    @endif
@else
    @if($item->{$field['name']} == 0)
        <span class="kt-badge kt-badge--inline kt-badge--pill"

              style="cursor:pointer;" data-column="{{ $field['name'] }}">Chờ xử lý</span>
    @elseif($item->{$field['name']} == 1)
        <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill"
              style="cursor:pointer;" data-column="{{ $field['name'] }}">Nhân viên trả lời</span>
    @elseif($item->{$field['name']} == 2)
        <span class="kt-badge kt-badge--info kt-badge--inline kt-badge--pill"
              style="cursor:pointer;" data-column="{{ $field['name'] }}">Khách trả lời</span>
    @elseif($item->{$field['name']} == 3)
        <span class="kt-badge  kt-badge--inline kt-badge--pill"
              style="cursor:pointer;" data-column="{{ $field['name'] }}">Đã đóng</span>
    @endif
@endif


