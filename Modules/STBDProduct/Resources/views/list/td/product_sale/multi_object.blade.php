   <?php
    $arr_id = explode('|', $item->{$field['name']});
    $model = new $field['model'];
    $data = $model->select([$field['display_field'], 'id'])->whereIn('id', $arr_id)->get()->toArray();

    ?>
    @for($i = 0; $i < 10; $i ++)
        @if(isset($data[$i]))
            <?php $v = $data[$i];?>
            <a href="/admin/{{ $field['object'] }}/{{ $v['id'] }}"
               target="_blank">{{ $v[$field['display_field']] }}
                ({{ $v['id'] }}) |</a>
        @endif
    @endfor

    @if(isset($data[10]))
        <a href="#" id="view_more_{{ $field['name'] }}_{{ $item->id }}">Xem thêm</a>
    @endif
    <div id="div_view_more_{{ $field['name'] }}_{{ $item->id }}" style="display: none;">
        @for($i = 10; $i < count($data); $i ++)
            <?php $v = $data[$i];?>
            <a href="/admin/{{ $field['object'] }}/{{ $v['id'] }}"
               target="_blank">{{ $v[$field['display_field']] }}
                ({{ $v['id'] }}) |</a>
        @endfor
    </div>
<script>
    $('#view_more_{{ $field['name'] }}_{{ $item->id }}').click(function () {
        $('#div_view_more_{{ $field['name'] }}_{{ $item->id }}').slideToggle();
    });
</script>