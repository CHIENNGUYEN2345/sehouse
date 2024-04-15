<style>
    .height-min {
        max-height: 300px;
        overflow: scroll;
    }
</style>
<div class="height-min height-min{{ $item->id }}">
    {!! $item->log_price !!}
</div>
