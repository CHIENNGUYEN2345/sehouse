<?php
    $tag_ids = [$item->{$field['name']}];
$tags = \App\Models\Tag::whereIn('id', $tag_ids)->get();
?>
@foreach($tags as $tag)
    <span style="font-weight: bold; color: {{ $tag->color }}">{{ $tag->name }}</span>
@endforeach