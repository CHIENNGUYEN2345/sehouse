<div class="form-group-div form-group {{ @$field['group_class'] }}"
                                             id="form-group-{{ $field['name'] }}">
    <label for="{{ $field['name'] }}">{{ trans(@$field['label']) }} @if(strpos(@$field['class'], 'require') !== false)
            <span class="color_btd">*</span>@endif</label>
    <div class="col-xs-12">
{{--        <p id="input-{{ $field['name'] }}"--}}
{{--           style="font-weight: 600; margin: 0;">{!! old($field['name']) != null ? nl2br(old($field['name'])) : nl2br(@$field['value']) !!}</p>--}}
        <input type="text" name="{{ @$field['name'] }}" class="form-control {{ @$field['class'] }}"
               id="{{ $field['name'] }}"
               {!! @$field['inner'] !!} @if(isset($result) && $result->{$field['name']} != '')
               @endif
               value="{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}"
                {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
        >
        <span class="text-danger hide error-{{ $field['name'] }}"></span>
    </div>
</div>
<script>
    $('input#{{ $field['name'] }}').change(function () {
        $('.error-{{ $field['name'] }}').hide();
        $('.error-{{ $field['name'] }}').html('');
        var tel = $(this).val();
        $.ajax({
            url: '/check-exist',
            data: {
                tel: tel,
                @if(isset($result))
                id: '{{ $result->id }}'
                @endif
            },
            success: function (resp) {
                if (resp.status == false) {
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
