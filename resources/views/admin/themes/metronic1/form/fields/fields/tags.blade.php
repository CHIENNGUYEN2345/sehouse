<?php
$tags = new $field['model'];
if (isset($field['where'])) {
    $tags = $tags->whereRaw($field['where']);
}
$tags = $tags->select('name', 'id')->get();
?>
<style>
    tags.tagify.form-control {
        height: unset;
    }
</style>
<input id="{{ @$field['name'] }}" class="form-control tagify" name='{{ @$field['name'] }}'
       placeholder='Nhập {{ @$field['label'] }}'
       value='{{ old($field['name']) != null ? old($field['name']) : @$field['value'] }}'
       {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }} autofocus=""/>
<div class="">
    <a href="javascript:;" id="{{ @$field['name'] }}_remove"
       class="btn btn-sm btn-light-primary font-weight-bold">Xóa {{ @$field['label'] }}</a>
</div>
<script>
    var KTTagifyDemos = function () {
        // Private functions
        var demo1 = function () {
            var input = document.getElementById('{{ @$field['name'] }}'),
                // init Tagify script on the above inputs
                tagify = new Tagify(input, {
                    whitelist: [
                        @foreach($tags as $tag)
                            '{{ $tag->name }}',
                        @endforeach
                    ],
                    // blacklist: ['aaa', 'bbb'], // <-- passed as an attribute in this demo
                });


            // "remove all tags" button event listener
            document.getElementById('{{ @$field['name'] }}_remove').addEventListener('click', tagify.removeAllTags.bind(tagify));

            // Chainable event listeners
            tagify.on('add', onAddTag)
                .on('remove', onRemoveTag)
                .on('input', onInput)
                .on('edit', onTagEdit)
                .on('invalid', onInvalidTag)
                .on('click', onTagClick)
                .on('dropdown:show', onDropdownShow)
                .on('dropdown:hide', onDropdownHide)

            // tag added callback
            function onAddTag(e) {
                console.log("onAddTag: ", e.detail);
                console.log("original input value: ", input.value)
                tagify.off('add', onAddTag) // exmaple of removing a custom Tagify event
            }

            // tag remvoed callback
            function onRemoveTag(e) {
                console.log(e.detail);
                console.log("tagify instance value:", tagify.value)
            }

            // on character(s) added/removed (user is typing/deleting)
            function onInput(e) {
                console.log(e.detail);
                console.log("onInput: ", e.detail);
            }

            function onTagEdit(e) {
                console.log("onTagEdit: ", e.detail);
            }

            // invalid tag added callback
            function onInvalidTag(e) {
                console.log("onInvalidTag: ", e.detail);
            }

            // invalid tag added callback
            function onTagClick(e) {
                console.log(e.detail);
                console.log("onTagClick: ", e.detail);
            }

            function onDropdownShow(e) {
                console.log("onDropdownShow: ", e.detail)
            }

            function onDropdownHide(e) {
                console.log("onDropdownHide: ", e.detail)
            }
        }

        return {
            // public functions
            init: function () {
                demo1();
            }
        };
    }();

    jQuery(document).ready(function () {
        KTTagifyDemos.init();
    });

</script>