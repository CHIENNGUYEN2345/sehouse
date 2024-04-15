@if(isset($field['height']))
    <style>
        div#cke_1_contents {
            height: {{ @$field['height'] }}  !important;
        }
    </style>
@endif
<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>@endif</label>
<textarea id="ck_{{ $field['name'] }}" name="{{ @$field['name'] }}"
          {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
          placeholder="{{ trans(@$field['label']) }}" {!! @$field['inner'] !!}
          class="form-control {{ @$field['class'] }}" {{ @$field['disabled']=='true'?'disabled':'' }}>{!! old($field['name']) != null ? old($field['name']) : @$field['value'] !!}</textarea>
<span class="text-danger">{{ $errors->first(@$field['name']) }}</span>

{{--<script src="{{asset('libs/ckeditor/ckeditor.js')}}"></script>--}}
{{--<script src="{{asset('libs/ckfinder/ckfinder.js')}}"></script>--}}

<script>
    $(document).ready(function () {
        // Replace the <textarea id="editor1"> with a CKEditor
        // instance, using default configuration.
        CKEDITOR.replace("ck_{{ $field['name'] }}", {
            filebrowserBrowseUrl: '{{route('browser')}}',
            filebrowserImageBrowseUrl: '{{route("browser")}}?Type=Images',
            filebrowserUploadUrl: '../ckfinder/connector?command=QuickUpload&type=Files',
            filebrowserImageUploadUrl: '../ckfinder/connector?command=QuickUpload&type=Images',
            filebrowserWindowWidth: '1000',
            filebrowserWindowHeight: '700',
            removeButtons: 'Underline,Strike,Subscript,Superscript,NumberedList,BulletedList,Outdent,Indent,Blockquote,CreateDiv,JustifyBlock,JustifyLeft,JustifyCenter,JustifyRight,Link,Unlink,Anchor,Image,Flash,Table,HorizontalRule,SpecialChar,Maximize,Source,Save,NewPage,Preview,Print,Templates,Find,Replace,SelectAll,SpellChecker,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField',
        });
        CKEDITOR.config.width = '100%';

    });
</script>
