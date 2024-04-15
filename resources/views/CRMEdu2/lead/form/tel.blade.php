<div class="form-group-div form-group {{ @$field['group_class'] }}"
     id="form-group-{{ $field['name'] }}">
    <label for="{{ $field['name'] }}">{{ trans(@$field['label']) }} @if(strpos(@$field['class'], 'require') !== false)
            <span class="color_btd">*</span>@endif</label>
    <div class="col-xs-12">
        <p id="input-{{ $field['name'] }}"
           style="font-weight: 600; margin: 0;">{!! old($field['name']) != null ? nl2br(old($field['name'])) : @number_format(nl2br(@$field['value']), 0, '.', '.') !!}</p>
        <input type="number" name="{{ @$field['name'] }}" class="form-control {{ @$field['class'] }}"
               id="{{ $field['name'] }}"
               {!! @$field['inner'] !!} @if(isset($result) && $result->{$field['name']} != '') style="display: none;"
               @endif
               value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
                {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
        >
        <span class="text-danger 
        hide error-{{ $field['name'] }}"  style="font-size: 10px;"></span>
    </div>
</div>
<script>
    $('input#{{ $field['name'] }}').change(function () {
        $('.error-{{ $field['name'] }}').hide();
        $('.error-{{ $field['name'] }}').html('');
        var tel = $(this).val();
        $.ajax({
            url: '/admin/lead/check-exist',
            data: {
                tel: tel,
                @if(isset($result))
                id: '{{ $result->id }}'
                @endif
            },
            success: function (resp) {
                if (resp.html != '') {
                    $('.error-{{ $field['name'] }}').show();
                    $('.error-{{ $field['name'] }}').html(resp.html);
                }
            }
        })
    });
</script>

<script>
    $('input#{{ $field['name'] }}, #form-group-{{ $field['name'] }}').click(function () {
        $('#input-{{ $field['name'] }}').hide();
        $('#{{ $field['name'] }}').show().click();
    });
</script>


<style type="text/css">
        .nha-mang img {
            width: 30px;
        }
        .nha-mang {
            position: absolute;
            right: 0;
            top: 22px;
        }
    </style>
<script type="text/javascript">
    //  Kiểm tra nhà mạng
    sdt = $('input[name=tel]').val().trim();
    sdt = sdt.replace(" ", "").replace(".", "").replace(".", "");
    tel = sdt.substring(0, 3);

    const viettel = ['086', '096', '097', '098', '032', '033', '034', '035', '036', '037', '038', '039'];
    const vinaphone = ['091', '094', '083', '084', '085', '081', '082'];
    const mobiphone = ['090', '093', '012', '012', '012', '012', '012', '089'];
    const vietnamobile = ['092', '056', '058'];

    if (viettel.indexOf(tel) != -1) {
        $('input[name=tel]').parents('#form-group-tel').append('<a href="tel:'+sdt+'" title="viettel" class="nha-mang"><img src="https://salt.tikicdn.com/assets/img/vas/viettel.png" alt="Provider"></a>');
    } else if (vinaphone.indexOf(tel) != -1) {
        $('input[name=tel]').parents('#form-group-tel').append('<a href="tel:'+sdt+'" title="vinaphone" class="nha-mang"><img src="https://salt.tikicdn.com/assets/img/vas/vinaphone.png" alt="Provider"></a>');
    } else if (mobiphone.indexOf(tel) != -1) {
        $('input[name=tel]').parents('#form-group-tel').append('<a href="tel:'+sdt+'" title="mobifone" class="nha-mang"><img src="https://salt.tikicdn.com/assets/img/vas/mobifone.png" alt="Provider"></a>');
    } else if (vietnamobile.indexOf(tel) != -1) {
        $('input[name=tel]').parents('#form-group-tel').append('<a href="tel:'+sdt+'" title="vietnammobile" class="nha-mang"><img src="https://salt.tikicdn.com/assets/img/vas/vietnammobile.png" alt="Provider"></a>');
    } else {
        $('input[name=tel]').parents('#form-group-tel').append('<span class="nha-mang"></span>');
    }
</script>