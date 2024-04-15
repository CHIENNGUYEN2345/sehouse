<input type="text" name="{{ $name }}" placeholder="{{ trans(@$field['label']) }}"
       value="{{ isset($_GET[$name]) ? $_GET[$name] : @$field['value'] }}"
       class="form-control kt-input {{ @$field['class'] }}">