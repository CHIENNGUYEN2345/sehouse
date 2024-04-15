<div class="form-group">
    <label for="{{ str_slug($field['label'], '-') }}" class="col-sm-3 control-label">{{ $field['label'] }}</label>
    <div class="col-sm-9">
        <input type="datetime-local" name="{{ $field['name'] }}" class="form-control {{ @$field['class'] }}"
               {{ strpos(@$field['class'], 'validate_field') !== false ? 'required' : '' }}
               id="{{ str_slug($field['label'], '-') }}"
               @if(isset($field['value']))value="{{ date('Y-m-d\TH:i:s', strtotime($field['value'])) }}"
               @endif placeholder="{{ @$field['value'] }}" {{ @$field['readonly']=='true'?'readonly':'' }}>
        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
    </div>
</div>