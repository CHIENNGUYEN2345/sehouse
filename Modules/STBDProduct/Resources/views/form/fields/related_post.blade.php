<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>@endif</label>
<div class="col-xs-12">
    <?php
    $v = \Modules\ThemeSTBDAdmin\Models\Post::select('id', 'name')->where('related_products', 'like', '%|'.@$result->id.'|%')->get();
    ?>
    <ul>
        @foreach($v as $k)
            <li><a href="/admin/post/{{ $k->id }}" target="_blank">{{ $k->name }}</a></li>
        @endforeach
    </ul>
</div>
