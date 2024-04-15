{{--@if(in_array('phone_view', $permissions))--}}
{{--{!! @$item->{$field['name']} !!}--}}
{{--@else--}}
{{--    @php--}}
{{--        $phoneNumber = $item->{$field['name']};--}}
{{--        $visiblePart = substr($phoneNumber, 0, 4);--}}
{{--        if (strlen($phoneNumber) >= 4) {--}}
{{--            $maskedPart = str_repeat('*', strlen($phoneNumber) - 4);--}}
{{--            $maskedPhoneNumber = $visiblePart.$maskedPart;--}}
{{--        } else {--}}
{{--            $maskedPhoneNumber = $phoneNumber;--}}
{{--        }--}}
{{--    @endphp--}}
{{--    {{ $maskedPhoneNumber }}--}}
{{--@endif--}}
{{--{{\Auth::guard('admin')->user()->id}}--}}
@if (in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['tpkd']) && $item->admin_id == \Auth::guard('admin')->user()->id)
    {!! @$item->{$field['name']} !!}
@elseif(in_array(CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name'), ['super_admin','cvkd_fulltime', 'cvkd_parttime']))
    {!! @$item->{$field['name']} !!}
@else
    @php
        $phoneNumber = $item->{$field['name']};
        $visiblePart = substr($phoneNumber, 0, 4);
        if (strlen($phoneNumber) >= 4) {
            $maskedPart = str_repeat('*', strlen($phoneNumber) - 4);
            $maskedPhoneNumber = $visiblePart.$maskedPart;
        } else {
            $maskedPhoneNumber = $phoneNumber;
        }
    @endphp
    {{ $maskedPhoneNumber }}
@endif
