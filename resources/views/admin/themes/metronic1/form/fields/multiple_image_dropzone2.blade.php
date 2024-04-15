<?php
$images = old($field['name']) != null ? old($field['name']) : explode('|', @$result->{$field['name']});
?>
{{--<div class="dropzone-previews-{{$field['name']}} dropzone" style="display: contents;"></div>--}}
<div class="dropzone dropzone-default dropzone-brand dz-clickable" id="{{ $field['name'] }}_dropzone">
    <div class="dropzone-msg dz-message needsclick">
        <h3 class="dropzone-msg-title"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{trans('admin.click_upload')}}.</font></font></h3>
        @if(isset($field['count']))<p><span class="dropzone-msg-desc"><font style="vertical-align: inherit;"><font
                            style="vertical-align: inherit;">{{trans('admin.click_upload')}} {{$field['count']}} {{trans('admin.files')}}</font></font></span>
        </p>
        @endif
        <span class="dropzone-msg-desc"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{trans('admin.upload_image')}}</font></font></span>
    </div>
</div>
<div id="value-dropzone-{{$field['name']}}">
    @foreach($images as $key=>$value)
        @if($value != '')
            <input type="hidden" name="{{$field['name']}}[]" value="{{$value}}">
        @endif
    @endforeach
</div>
<style>
    .dz-progress, .dz-size, .dz-filename {
        display: none !important;
    }

    .dz-details {
        position: relative;
        display: inline-block;
        height: 80% !important;
       cursor:pointer !important; ;
    }
    .dz-details .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: red;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;

        /* Position the tooltip */
        position: absolute;
        z-index: 1;
        top: -40px;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .dz-details:hover .tooltiptext {
        visibility: visible;
    }
    .dz-remove {
        color: red;
    }
</style>
@push('scripts')
    <script>
        function setMaxFile(myDropzone, file) {
            var number = "{{$field['count']}}" - $("#value-dropzone-{{$field['name']}}").children().length;
            if (number < 0) {
                myDropzone.removeFile(file);
                toastr.error('Bạn không thể cập nhật file vượt quá mức cho phép');
            }
        }

        Dropzone.prototype.defaultOptions.dictRemoveFile = 'Xóa ảnh';
        Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = 'Bạn không thể cập nhật file vượt quá mức cho phép';
        Dropzone.prototype.defaultOptions.dictCancelUpload = 'Hủy';
        Dropzone.prototype.defaultOptions.dictUploadCanceled = 'Đã hủy tải lên';

        $("#{{ $field['name'] }}_dropzone").dropzone({
            url: "{{route('ajax-up-file')}}",
            paramName: "file",
            addRemoveLinks: !0,
            acceptedFiles: "image/*,",
            // previewsContainer: ".dropzone-previews-{{$field['name']}}",
            init: function () {

                var thisDropzone = this;
                thisDropzone.on("complete", function (file) {
                    if (file.status == 'success') {
                        let response = JSON.parse(file.xhr.response);
                        $("#value-dropzone-{{$field['name']}}").append("<input type='hidden' name='{{$field['name']}}[]' value='" + response.value + "'>");
                        setMaxFile(this, file);
                        $('.dz-preview.dz-processing.dz-image-preview.dz-success.dz-complete:last-child .dz-image img').attr('sub-src',response.file)
                        $('.dz-preview.dz-processing.dz-image-preview.dz-success.dz-complete:last-child .dz-details').append('<span class="tooltiptext">Bấm vào ảnh để phóng to</span>');
                    }
                });

                thisDropzone.on("canceled", function (file) {
                   console.log(file)
                });

                thisDropzone.on("removedfile", function (file) {
                    if (file.status == 'success') {
                        let response = JSON.parse(file.xhr.response);
                        $("#value-dropzone-{{$field['name']}}").children().each(function () {
                            let input = $(this);
                            if (response.value == input.val()) {
                                input.remove();
                            }
                        });
                    } else if (file.type == 'current') {
                        $("#value-dropzone-{{$field['name']}}").children().each(function () {
                            let input = $(this);
                            if (file.url == input.val()) {
                                input.remove();
                            }
                        });
                    }
                });
                        @foreach($images as $key=>$value)
                        @if($value != '')
                    <?php
                    $imageValue = \App\Http\Helpers\CommonHelper::getUrlImageThumb($value, 120, 120);
                    //                    $imageValue = 'filemanager/userfiles/' . $value;
                    ?>
                var mockFile = {
                        name: '{{ $field['label'] }}',
                        size: '{{ @getimagesize($imageValue)['bits'] }}',
                        url: '{{ $value }}',
                        type: 'current'
                    };
                thisDropzone.emit("complete", mockFile);
                thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                thisDropzone.options.thumbnail.call(thisDropzone, mockFile, '{{$imageValue}}'.replace(/amp;/g,''));
                @endif
                @endforeach

                $('.dz-details').each(function () {
                    if (!$(this).children().hasClass('tooltiptext')) {
                        $(this).append('<span class="tooltiptext">{{trans('admin.click_zoom')}}</span>');
                    }
                });

            }
        });
    </script>
@endpush
