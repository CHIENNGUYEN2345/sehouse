<style>
    li.select2-selection__choice {
        width: 100%;
    }
    #form-group-bill_ids .select2-container {
        display: initial;
    }
</style>
<?php
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

$bills = \App\CRMBDS\Models\Bill::where('status', 1)->get();

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

<div style="padding: 0"
     class="form-group-div form-group {{ @$field['group_class'] }}"
     id="form-group-{{ $field['name'] }}">

    <label for="{{ @$field['name'] }}">{{ trans($field['label']) }}
    @if(strpos(@$field['class'], 'require') !== false)
            <span class="color_btd">*</span>
        @endif
    </label>

    <select class="form-control {{ $field['class'] or '' }} select2-{{ @$field['name'] }}" id="{{ @$field['name'] }}"
            {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
            name="{{ @$field['name'] }}{{ isset($field['multiple']) ? '[]' : '' }}" {{ isset($field['multiple']) ? 'multiple' : '' }} {!! @$field['inner'] !!}>
        <option value="">{{trans('admin.choose')}} {{ trans($field['label']) }}</option>
        @foreach ($bills as $bill)
            <option {{ in_array(@$bill->id, @$value) ? 'selected':'' }}
                    value="{{ @$bill->id }}">{{ @$bill->user->name }} - Khoá: {{ @$bill->service->name_vi }} - Ký ngày: {{ date('d/m/Y', strtotime(@$bill->registration_date)) }}</option>
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
