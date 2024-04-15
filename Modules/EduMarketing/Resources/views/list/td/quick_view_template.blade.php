<button class="btn btn-brand btn-elevate btn-icon-sm template-quick-view-{{ $item->id }}">
    Xem nhanh
</button>
<div id="template-quick-view-content-{{ $item->id }}" style="display: none;">
    {!! $item->content !!}
</div>
<script>
    $('.template-quick-view-{{ $item->id }}').click(function () {
        $('#blank_modal .modal-body').html($('#template-quick-view-content-{{ $item->id }}').html());
        $('#blank_modal').modal();
    });
</script>