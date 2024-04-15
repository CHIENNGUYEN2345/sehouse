<a href="/admin/{{ @$field['object'] }}/{{ @$item->{$field['object']}->id }}"
   target="_blank">
   <?php 
   $da_thu = @$item->bill_finance->received;
   $tong_tien = @$item->bill_finance->total;
   ?>
    {{ number_format($tong_tien - $da_thu, 0, ',', '.') }}<sup>đ</sup>
</a>
@if(isset($field['tooltip_info']))
    <div id="tooltip-info-{{@$field['name']}}" class="div-tooltip_info" data-modal="{{ $module['modal'] }}"
         data-tooltip_info="{{ json_encode(@$field['tooltip_info']) }}"><img style="margin-top: 20%;" src="/images_core/icons/loading.gif"></div>
@endif