<div class="form-group form-hide" id="form-group-{{ $field['name'] }}">
    <label for="{{ $field['name'] }}" class="control-label">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)<span class="color_btd">*</span>@endif</label>
    <div class="">
        <?php
        $model = new $field['model'];
        if(isset($field['where']))
            $model = $model->where('status', 1)->orderBy('order_no', 'asc');
        if(isset($field['where_attr']) && isset($result))
            $model = $model->where('status', 1)->orderBy('order_no', 'asc');
        $data = $model->orderBy($field['display_field'], 'asc')->get();
        $value = [];
        if (isset($field['multiple']) && isset($result)) {
            if (is_array($result->{$field['name']}) || is_object($result->{$field['name']})) {
                foreach ($result->{$field['name']} as $item) {
                    $value[] = $item->id;
                }
            } elseif(is_string($result->{$field['name']})) {
                $value = explode('|', $result->{$field['name']});
            }
        } else {
            if (old($field['name']) != null) $value[] = old($field['name']);
            if (isset($field['value'])) $value[] = $field['value'];
        }
        ?>
        <select class="form-control {{ $field['class'] or '' }} select2-{{ $field['name'] }}" id="{{ $field['name'] }}" {{ strpos($field['class'], 'require') !== false ? 'required' : '' }}
        name="{{ $field['name'] }}{{ isset($field['multiple']) ? '[]' : '' }}" {{ isset($field['multiple']) ? 'multiple' : '' }}>
            <option value="">Chọn {{ $field['label'] }}</option>
            @foreach ($data as $v)
                <option value='{{ $v->id }}' {{ in_array($v->id, $value) ? 'selected':'' }}>{{ $v->{$field['display_field']} }}{{ isset($field['display_field2']) ? ' | ' . $v->{$field['display_field2']} : '' }}</option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2-{{ $field['name'] }}').select2();
    });
</script>