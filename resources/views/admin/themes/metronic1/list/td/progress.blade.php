<?php
$chia = ($item->{$field['data']['value']} == 0 || $item->{$field['data']['total']}) ? 0 : @$item->{$field['data']['value']} / @$item->{$field['data']['total']};
?>
<div class="progress">
    <div class="progress-bar" role="progressbar" style="width: {{ $chia }}%"
         aria-valuenow="{{ $chia }}" aria-valuemin="0" aria-valuemax="100"></div>
</div>