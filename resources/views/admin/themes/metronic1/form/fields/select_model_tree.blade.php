<?php
$model = new $field['model'];
if(isset($field['where']))
    $model = $model->whereRaw($field['where']);

$model = $model->select(['id', $field['display_field']])->where('parent_id', null)->orderBy('order_no', 'desc');
$level1 = $model->get();
?>
        <?php $value[] = old($field['name']) != null ? old($field['name']) : @$field['value'];?>
<select class="form-control {{ $field['class'] or '' }}" id="{{ $field['name'] }}" {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
name="{{ $field['name'] }}">
    <option value="">{{trans('admin.choose')}} {{ $field['label'] }}</option>
    @foreach ($level1 as $k => $lv1)
        <option value='{{ $lv1->id }}' {{ in_array($lv1->id, $value) ? 'selected' : '' }}>{{ $lv1->{$field['display_field']} }}</option>
        @php $level2 = $lv1->childs; @endphp
        @foreach ($level2 as $k => $lv2)
            <option value='{{ $lv2->id }}' {{ in_array($lv2->id, $value) ? 'selected' : '' }}>— {{ $lv2->{$field['display_field']} }}</option>
            @php $level3 = $lv2->childs; @endphp
            @foreach ($level3 as $k => $lv3)
                <option value='{{ $lv3->id }}' {{ in_array($lv3->id, $value) ? 'selected' : '' }}>— — {{ $lv3->{$field['display_field']} }}</option>
            @endforeach
        @endforeach
    @endforeach
</select>