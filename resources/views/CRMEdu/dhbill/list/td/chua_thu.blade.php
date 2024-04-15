<a href="/admin/{{ @$field['object'] }}/{{ @$item->{$field['object']}->id }}"
   target="_blank">
    {{ number_format(@$item->total_price_contract - @$item->total_received, 0, ',', '.') }}<sup>đ</sup>
</a>
@if(isset($field['tooltip_info']))
    <div id="tooltip-info-{{@$field['name']}}" class="div-tooltip_info" data-modal="{{ $module['modal'] }}"
         data-tooltip_info="{{ json_encode(@$field['tooltip_info']) }}"><img style="margin-top: 20%;" src="/images_core/icons/loading.gif"></div>
@endif