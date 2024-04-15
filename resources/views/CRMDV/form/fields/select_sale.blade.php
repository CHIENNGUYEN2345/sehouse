<?php
$model = new $field['model'];
if (isset($field['where'])) {
    $model = $model->whereRaw($field['where']);
}
if (isset($field['where_attr']) && isset($result)) {
    $model = $model->where($field['where_attr'], $result->{$field['where_attr']});
}

//  lấy các quyền sale
$sale_ids = \App\Models\RoleAdmin::whereIn('role_id', [
    2,  //  sale
    182,    // trưởng phòng sale
    186,    //  giám đốc kd sale,
    176,    //   ctv sale
])->pluck('admin_id')->toArray();
$model = $model->whereIn('id', $sale_ids);

if (isset($field['orderByRaw'])) {
    $model = $model->orderByRaw($field['orderByRaw']);
} else {
    $model = $model->orderBy($field['display_field'], 'asc');
}

$data = $model->get();
$value = [];
if (isset($field['multiple']) && isset($result)) {
    if (is_array($result->{$field['name']}) || is_object($result->{$field['name']})) {
        foreach ($result->{$field['name']} as $item) {
            $value[] = $item->id;
        }
    } elseif (is_string($result->{$field['name']})) {
        $value = explode('|', $result->{$field['name']});
    }
} else {
    if (old($field['name']) != null) $value[] = old($field['name']);
    if (isset($field['value'])) $value[] = $field['value'];
}
if (empty($value) & !isset($result)) {
    //  nếu tạo mới & không có giá tị mặc định thì mặc định lấy tk đang đăng nhập
    $value[] = \Auth::guard('admin')->user()->id;
}
?>

<label for="{{ $field['name'] }}">{{ trans($field['label']) }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>
    @endif</label>

<select class="form-control {{ $field['class'] or '' }} select2-{{ $field['name'] }}" id="{{ $field['name'] }}"
        {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
        name="{{ $field['name'] }}{{ isset($field['multiple']) ? '[]' : '' }}" {{ isset($field['multiple']) ? 'multiple' : '' }} {!! @$field['inner'] !!}>
    <option value="">{{trans('admin.choose')}} {{ trans($field['label']) }}</option>
    @foreach ($data as $v)

        <option value='{{ $v->id }}' {{ in_array($v->id, $value) ? 'selected':'' }}>{{ $v->{$field['display_field']} }}{{ isset($field['display_field2']) ? ' | ' . $v->{$field['display_field2']} : '' }}</option>
    @endforeach
</select>
<script>
    $(document).ready(function () {
        $('.select2-{{ $field['name'] }}').select2({
            @if(isset($field['multiple']))
            closeOnSelect: false,
            @endif
        });
    });
</script>