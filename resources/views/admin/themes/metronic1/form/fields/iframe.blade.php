<iframe id="{{ $field['name'] }}" src="{{ str_replace('{id}', @$result->id, @$field['src']) }}"
    
         {!! @$field['inner'] !!}></iframe>