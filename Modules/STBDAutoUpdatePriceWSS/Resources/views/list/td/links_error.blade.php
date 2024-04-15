<?php
$data = (array)json_decode($item->links_error);
?>
@foreach ($data as $k => $v)
    {{ $v->link }}<br>
    @if($k == 5)
        <a class="show_link_error_more{{ $item->id }}" data-key="{{ $item->id }}" style="color: #5867dd;
    text-decoration: underline; cursor: pointer;">Xem thêm</a>
        <div id="links_error_{{ $item->id }}" style="display: none">
        <?php $end_div = true;?>
    @endif

    <script>
        $(document).ready(function () {
            $('.show_link_error_more{{ $item->id }}').click(function () {
                $('#links_error_{{ $item->id }}').css('display', 'block');
            });
        });
    </script>
@endforeach

@if(isset($end_div))
    </div>
@endif