<div class="kt-radio-list mt-3">
    @foreach ($field['options'] as $k => $v)
        <label class="kt-radio">
            <input type="radio" name="{{ $field['name'] }}"
                   {{ $k == @$field['value'] ? 'checked':'' }} value="{{ $k }}"> {{ $v }}
            <span></span>
        </label>
    @endforeach
</div>