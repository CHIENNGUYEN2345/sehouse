@if($item->base_price != 0 && ((($item->base_price - $item->final_price) * 100) % $item->base_price) == 0)
    {{ ($item->base_price - $item->final_price)/$item->base_price * 100 }}%
@elseif($item->base_price != 0)
    {{ number_format(($item->base_price - $item->final_price)/$item->base_price * 100, 2, ',', '.') }}%
@endif