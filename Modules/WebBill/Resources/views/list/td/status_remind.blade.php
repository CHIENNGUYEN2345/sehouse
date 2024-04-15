@if(in_array('remind', $permissions))
    @if($item->{$field['name']} == 0)
        <a href="{{URL::to('/admin/comment/publish?id='.$item->id.'&column=status')}}"
           class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill"

           style="cursor:pointer;" data-column="{{ $field['name'] }}">Chưa kích hoạt</a>
    @elseif($item->{$field['name']} == 1)
        <a href="{{URL::to('/admin/comment/publish?id='.$item->id.'&column=status')}}"
           class="kt-badge  kt-badge--warning kt-badge--inline kt-badge--pill"
           style="cursor:pointer;" data-column="{{ $field['name'] }}">Kích hoạt</a>
    @endif
@else
    @if($item->{$field['name']} == 0)
        <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill"

              style="cursor:pointer;" data-column="{{ $field['name'] }}">Chưa kích hoạt</span>
    @elseif($item->{$field['name']} == 1)
        <span class="kt-badge kt-badge--warning kt-badge--inline kt-badge--pill"
              style="cursor:pointer;" data-column="{{ $field['name'] }}">Kích hoạt</span>

    @endif
@endif


