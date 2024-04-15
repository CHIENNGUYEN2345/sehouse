<a href="">
    {{ number_format(\Modules\STBDProduct\Models\Product::where('proprerties_id', 'like', '%|'.@$item->id.'|%')->count(), 0, '.', '.') }}
</a>