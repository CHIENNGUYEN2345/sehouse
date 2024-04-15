<input type="hidden" name="{{ $name }}" placeholder="{{ @$field['label'] }}"
       value="{{ isset($_GET[$name]) ? $_GET[$name] : @$field['value'] }}"
       class="form-control kt-input {{ @$field['class'] }}">