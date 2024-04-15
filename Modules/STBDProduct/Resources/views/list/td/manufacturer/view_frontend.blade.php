{{--<a href="/admin/product/{{$item->id}}" target="_blank">Xem</a>--}}
<a href="{{route('manufacturer.detail', ['slug' => $item->slug])}}" target="_blank">Xem</a>