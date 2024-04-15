<div class="form-group" id="form-group-ck-{{ $field['name'] }}">
    <label for="{{ $field['name'] }}"
           class="col control-label">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)<span
                class="color_btd">*</span>@endif</label>
    <div class="col">
        <textarea id="ck-{{ $field['name'] }}" name="{{ @$field['name'] }}"
                  {{ strpos($field['class'], 'require') !== false ? 'required' : '' }}
                  placeholder="{{ @$field['label'] }}" {!! @$field['inner'] !!}
                  class="form-control {{ @$field['class'] }} ckeditor" {{ @$field['disabled']=='true'?'disabled':'' }}>{!! old($field['name']) != null ? old($field['name']) : @$field['value'] !!}</textarea>
        <span class="text-danger">{{ $errors->first(@$field['name']) }}</span>
    </div>
</div>
{{--<script src="{{asset('public/libs/ckeditor/ckeditor.js')}}"></script>--}}
{{--<script src="{{asset('public/libs/ckfinder/ckfinder.js')}}"></script>--}}

<script>
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace("ck-{{ $field['name'] }}", {
        filebrowserBrowseUrl: "{{route('browser')}}",
        filebrowserImageBrowseUrl: "{{route('browser')}}?Type=Images",
        filebrowserUploadUrl: '../public/ckfinder/connector?command=QuickUpload&type=Files',
        filebrowserImageUploadUrl: '../public/ckfinder/connector?command=QuickUpload&type=Images',
        filebrowserWindowWidth: '1000',
        filebrowserWindowHeight: '700'
    });
</script>
