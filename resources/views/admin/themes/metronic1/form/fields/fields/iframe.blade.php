{{--{{dd($field['src'])}}--}}
<iframe id="{{ $field['name'] }}" src="{{ str_replace('{id}', @$result->id, @$field['src']) }}"
        onload="resizeIframe(this)" {!! @$field['inner'] !!}></iframe>