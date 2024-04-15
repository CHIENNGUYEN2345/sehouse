<?php
$model = new $field['model'];

$data = $model->orderBy($field['display_field'], 'asc')->get();
$value = [];
if (old($field['name']) != null) $value[] = old($field['name']);
if (isset($field['value'])) {
    $value[] = $field['value'];
} else {
    $value = \App\Models\RoleAdmin::where('admin_id', @$result->id)->where(function ($query) {
        $query->orWhere('role_admin.company_id', \Auth::guard('admin')->user()->company_id);
        $query->orWhereNull('role_admin.company_id');
    })->pluck('role_id')->toArray();
}
?>
<div class="form-group-div form-group {{ @$field['group_class'] }}"
     id="form-group-{{ $field['name'] }}">
    <label for="{{ $field['name'] }}">{{ trans(@$field['label']) }} @if(strpos(@$field['class'], 'require') !== false)
            <span class="color_btd">*</span>@endif</label>
    <div class="col-xs-12">
        <select class="form-control {{ $field['class'] or '' }} select2-{{ $field['name'] }}" id="{{ $field['name'] }}"
                {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
                name="{{ $field['name'] }}{{ isset($field['multiple']) ? '[]' : '' }}" {{ isset($field['multiple']) ? 'multiple' : '' }}>
            <option value="">{{trans('admin.choose')}} {{ $field['label'] }}</option>
            @foreach ($data as $v)

                <option value='{{ $v->id }}' {{ in_array($v->id, $value) ? 'selected':'' }}>{{ $v->{$field['display_field']} }}{{ isset($field['display_field2']) ? ' | ' . $v->{$field['display_field2']} : '' }}</option>
            @endforeach
        </select>
    </div>
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