<?php
$create_by = isset($result) ? $result->admin_id : \Auth::guard('admin')->user()->id;
$model = new $field['model'];
if (isset($field['where'])) {
    $model = $model->whereRaw($field['where']);
}
if (isset($field['where_attr']) && isset($result)) {
    $model = $model->where($field['where_attr'], $result->{$field['where_attr']});
}
//  Lấy các HĐ là mình tạo ra hoặc mình phụ trách
$data = $model->where(function ($query) use ($create_by) {
    $query->orWhere('customer_id', $create_by);
    $query->orWhere('curator_ids', 'LIKE', '%|' . $create_by . '|%');
})->orderBy($field['display_field'], 'asc')->get();
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
?>
<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>@endif</label>
<div class="col-xs-12">
    <select class="form-control {{ $field['class'] or '' }} select2-{{ $field['name'] }}" id="{{ $field['name'] }}"
            {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
            name="{{ $field['name'] }}{{ isset($field['multiple']) ? '[]' : '' }}" {{ isset($field['multiple']) ? 'multiple' : '' }}>
        <option value="">--</option>
        @foreach ($data as $v)
            <option value='{{ $v->id }}' {{ in_array($v->id, $value) ? 'selected':'' }}>{{ $v->{$field['display_field']} }}
                [ {{ $v->service->name_vi }} ]
            </option>
        @endforeach
    </select>
</div>
<script>
    $(document).ready(function () {
        $('.select2-{{ $field['name'] }}').select2({
            @if(isset($field['multiple']))
            closeOnSelect: false,
            @endif
        });
    });
</script>

@if(isset($result))
    <div>
        <?php
        @$field = ['name' => 'customer_note', 'type' => 'textarea', 'class' => '', 'label' => 'Thông tin hosting / tên miền', 'inner' => 'rows=5', 'value' => @$result->bill->customer_note];
        $result->customer_note = @$result->bill->customer_note;
        ?>
        <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                <span class="color_btd">*</span>@endif</label>
        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
    </div>
@endif