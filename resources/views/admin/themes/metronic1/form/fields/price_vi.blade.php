{{--<div class="input-group">--}}

{{--    <input type="text" name="{{ @$field['name'] }}" class="form-control {{ @$field['class'] }}"--}}
{{--           id="{{ $field['name'] }}" {!! @$field['inner'] !!}--}}

{{--           @if(isset($field['value']) && $field['value'] != '') style="display: none;" @endif--}}

{{--           value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"--}}
{{--            {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}--}}
{{--    >--}}
{{--    <div class="input-group-append"--}}
{{--         @if(isset($field['value']) && $field['value'] != '') style="display: none;" @endif--}}
{{--    >--}}
{{--        <span class="input-group-text">đ</span>--}}
{{--    </div>--}}

{{--    <p id="input-{{ $field['name'] }}" style="color: #000; margin: 0;">--}}
{{--        @php--}}
{{--            $formattedValue = old($field['name']) !== null ? old($field['name']) : @$field['value'];--}}

{{--            if ($formattedValue >= 1000000000) {--}}
{{--                $formattedValue = number_format($formattedValue / 1000000000, 1, '.', '') . ' tỷ';--}}
{{--            } elseif ($formattedValue >= 1000000) {--}}
{{--                $formattedValue = number_format($formattedValue / 1000000, 1, '.', '') . ' triệu';--}}
{{--            } else {--}}
{{--                $formattedValue = number_format($formattedValue, 0, '.', '') . ' đ';--}}
{{--            }--}}
{{--        @endphp--}}
{{--        {!! $formattedValue !!}--}}

{{--    </p>--}}

{{--    <script>--}}

{{--    </script>--}}
{{--    <script>--}}
{{--        $(document).ready(function () {--}}
{{--            $('input#{{ $field['name'] }}, #form-group-{{ $field['name'] }}').click(function () {--}}
{{--                $('#input-{{ $field['name'] }}').hide();--}}
{{--                $('#{{ $field['name'] }}').show().click();--}}
{{--                updateDisplayedValue($(this));--}}
{{--            });--}}
{{--            $('input#{{ $field['name'] }}').on('input', function () {--}}
{{--                var inputValue = parseFloat($(this).val().replace(/,/g, ''));--}}

{{--                if (!isNaN(inputValue)) {--}}
{{--                    if (inputValue >= 1000000000) {--}}
{{--                        $('#input-{{ $field['name'] }}').text((inputValue / 1000000000).toFixed(1) + ' tỷ');--}}
{{--                    } else if (inputValue >= 1000000) {--}}
{{--                        $('#input-{{ $field['name'] }}').text((inputValue / 1000000).toFixed(1) + ' triệu');--}}
{{--                    } else {--}}
{{--                        $('#input-{{ $field['name'] }}').text(number_format(inputValue, 0, '.', ',') + ' đ');--}}
{{--                    }--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
{{--</div>--}}

{{--<script>--}}
{{--     function number_format(number, decimals, dec_point, thousands_sep) {--}}
{{--        number = number.toFixed(decimals);--}}
{{--        var nstr = number.toString();--}}
{{--        nstr += '';--}}
{{--        x = nstr.split('.');--}}
{{--        x1 = x[0];--}}
{{--        x2 = x.length > 1 ? dec_point + x[1] : '';--}}
{{--        var rgx = /(\d+)(\d{3})/;--}}
{{--        while (rgx.test(x1)) {--}}
{{--            x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');--}}
{{--        }--}}
{{--        return x1 + x2;--}}
{{--    }--}}
{{--</script>--}}


<input
        type="text"
        name="{{ @$field['name'] }}"
        class="form-control required"
        id="{{ @$field['name'] }}"
        value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
        {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
/>

<span id="{{ @$field['name'] }}_show"></span>


<script>
    // Lấy phần tử input và span
    var inputElement{{ $field['name'] }} = document.getElementById('{{ $field['name'] }}');
    var formattedValueElement{{ $field['name'] }} = document.getElementById("{{ $field['name'] }}_show");

    // Biến lưu trữ giá trị gốc và giá trị chưa định dạng
    var originalValue{{ $field['name'] }} = "";
    var originalUnformattedValue{{ $field['name'] }} = "";

    // Thêm sự kiện 'input' để theo dõi thay đổi giá trị
    inputElement{{ $field['name'] }}.addEventListener("input", function (event) {
        // Lấy giá trị nhập vào
        var inputValue{{ $field['name'] }} = event.target.value;

        // Lưu trữ giá trị gốc và giá trị chưa định dạng
        originalValue{{ $field['name'] }} = inputValue{{ $field['name'] }};
        originalUnformattedValue{{ $field['name'] }} = inputValue{{ $field['name'] }}.replace(/\./g, "");

        // Loại bỏ tất cả các dấu chấm hiện tại
        inputValue{{ $field['name'] }} = inputValue{{ $field['name'] }}.replace(/\./g, "");

        // Định dạng lại giá trị với dấu chấm hàng nghìn
        inputValue{{ $field['name'] }} = formatNumber(inputValue{{ $field['name'] }});

        // Gán lại giá trị vào input
        event.target.value = inputValue{{ $field['name'] }};

        // Hiển thị giá trị đã định dạng trong đơn vị tỷ hoặc triệu
        formattedValueElement{{ $field['name'] }}.textContent = formatNumberWithUnit(inputValue{{ $field['name'] }});
    });

    // Trigger the input event after defining the listener
    var event = new Event("input");
    inputElement{{ $field['name'] }}.dispatchEvent(event);

    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function formatNumberWithUnit(number) {
        // Loại bỏ tất cả các dấu chấm trong giá trị
        var numberString = number.replace(/\./g, "");

        // Chuyển đổi giá trị nhập vào thành số
        var numericValue = parseFloat(numberString);

        // Kiểm tra xem giá trị có phải là một số không
        if (isNaN(numericValue)) {
            console.log(numericValue);
            return "Không đúng định dạng";
        }

        if (numericValue < 1000000) {
            return formatNumber(numericValue) + " VND";
        } else if (numericValue < 1000000000) {
            var millions = Math.floor(numericValue / 1000000);
            var remainder = numericValue % 1000000;

            // Display decimal part if not exactly divisible by 1 million
            var decimalPart =
                remainder !== 0
                    ? "." + parseFloat(remainder.toFixed(3)).toString().replace(/\.?0+$/, '')
                    : "";
            return millions + decimalPart + " triệu";
        } else {
            var billions = Math.floor(numericValue / 1000000000);
            var remainder = numericValue % 1000000000;
            // Display decimal part if not exactly divisible by 1 billion
            var decimalPart =
                remainder !== 0
                    ? "." + parseFloat(remainder.toFixed(3)).toString().replace(/\.?0+$/, '')
                    : "";

            return numericValue / 1000000000 + " tỷ";
        }
    }
</script>

