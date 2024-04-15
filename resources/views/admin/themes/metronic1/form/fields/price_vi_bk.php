<div class="input-group">
    <input type="text" name="{{ @$field['name'] }}" class="form-control number_price {{ @$field['class'] }}"
           {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
           id="{{ $field['name'] }}" {!! @$field['inner'] !!}
           value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
    >
    <div class="input-group-append">
        <span class="input-group-text">đ</span>
    </div>
</div>
<script src="{{ asset('backend/themes/metronic1/js/format_money.js') }}"></script>
đang lỗi, phải bỏ chuột ra khỏi ô thì mới upadte được giá trị