@if(@$field['display_field']=='id')
    <button
            style="font-size: 14px!important;width: auto;" class="{{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }} btn-view text-dark font-bold border-0 bg-white outlight-0" data-id="{{ $item->id }}"  >{{ @$item->{@$field['object']}->{@$field['display_field']} }}</button>
@else
    @if (!in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['cvkd_parttime']))
        <button
                style="font-size: 14px!important;" class="{{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }} btn-view text-dark font-bold border-0 bg-white outlight-0 baocao" data-id="{{ $item->id }}"  >{{ @$item->{@$field['object']}->{@$field['display_field']} }}</button>
    @else
        <button
                style="font-size: 14px!important;" class="{{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }} btn-view text-dark font-bold border-0 bg-white outlight-0" data-id="{{ $item->id }}"  >--</button>
    @endif
@endif


