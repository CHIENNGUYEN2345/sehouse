{{--<div class="form-group" id="form-group-{{ $field['name'] }}">--}}

<label for="{{ $field['name'] }}" class=" control-label">
    {{--        <input type="text" id="sale_number" name="sale_number " value="" disabled>--}}
    {{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)<span class="color_btd">*</span>@endif
    <span id="sale_number">

       </span>
</label>
{{--    <div class="col-sm-9">--}}
<input type="text" name="{{ @$field['name'] }}" class="form-control {{ @$field['class'] }}"
       onkeyup="salePrice();"
       {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
       id="{{ @$field['name'] }}" {!! @$field['inner'] !!}
       value="{{ old($field['name']) != null ? old(@$field['name']) : @$field['value'] }}"
       placeholder="{{ @$field['label'] }}">
<span class="text-danger">{{ $errors->first($field['name']) }}</span>
{{--    </div>--}}
{{--</div>--}}
<script>

    function salePrice() {

        var final_price, sale, base_price;

        base_price = document.getElementById("base_price").value;
        base_price = base_price.replace(",", "");
        base_price = base_price.replace(".", "");
        final_price = document.getElementById("final_price").value;
        final_price = final_price.replace(",", "");
        final_price = final_price.replace(".", "");
        if ((base_price !== '' && parseInt(base_price) > 0) && (final_price !== '' && parseInt(final_price)) > 0) {
            sale = '(Giảm ' + ((parseInt(base_price) - parseInt(final_price)) * 100 / parseInt(base_price)).toFixed(2) + '%)';
        } else {
            sale = '';
        }
        document.getElementById('sale_number').innerHTML = sale;
    }
    $(document).ready(function(){
        salePrice();
    });
</script>