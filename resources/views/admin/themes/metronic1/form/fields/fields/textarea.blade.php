<p id="textarea-{{ $field['name'] }}" style="font-weight: 600; margin: 0;">{!! old($field['name']) != null ? nl2br(old($field['name'])) : nl2br(@$field['value']) !!}</p>
<textarea id="{{ $field['name'] }}" name="{{ @$field['name'] }}" @if(isset($result) && $result->{$field['name']} != '') style="display: none;" @endif
          {!! @$field['inner'] !!} class="form-control {{ @$field['class'] }}" {{ @$field['disabled']=='true'?'disabled':'' }} {{ @strpos(@$field['class'], 'require') !== false ? 'required' : '' }}>{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}</textarea>

<script>
    var callCount{{ $field['name'] }} = 0
    function countCallFunction{{ $field['name'] }}() {
        if (callCount{{ $field['name'] }} == 0) {
            initFunction{{ $field['name'] }}();
        }
        callCount{{ $field['name'] }} ++;
        return true;
    }

    function initFunction{{ $field['name'] }}() {
        textareaInit{{ $field['name'] }}();
    }


    $('#textarea-{{ $field['name'] }}, #form-group-{{ $field['name'] }}').click(function () {
        $('#textarea-{{ $field['name'] }}').hide();
        $('#{{ $field['name'] }}').show().click();
    });
    $(document).ready(function () {
        $('#{{ $field['name'] }}').click(function () {
            countCallFunction{{ $field['name'] }}();
        });
    });
    var observe;
    if (window.attachEvent) {
        observe = function (element, event, handler) {
            element.attachEvent('on' + event, handler);
        };
    } else {
        observe = function (element, event, handler) {
            element.addEventListener(event, handler, false);
        };
    }

    function textareaInit{{ $field['name'] }}() {
        var note = document.getElementById('{{ $field['name'] }}');

        function resize() {
            note.style.height = 'auto';
            note.style.height = note.scrollHeight + 'px';
        }

        /* 0-timeout to get the already changed note */
        function delayedResize() {
            window.setTimeout(resize, 0);
        }

        observe(note, 'change', resize);
        observe(note, 'cut', delayedResize);
        observe(note, 'paste', delayedResize);
        observe(note, 'drop', delayedResize);
        observe(note, 'keydown', delayedResize);

        note.focus();
        note.select();
        resize();
    }
</script>