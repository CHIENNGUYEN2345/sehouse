<?php
$images = old($field['name']) != null ? old($field['name']) : explode('|', @$result->{$field['name']});
?>
<div class="dropzone dropzone-default dropzone-brand" id="{{$field['name']}}_dropzone">
    <div class="dropzone-msg dz-message needsclick">
        <h3 class="dropzone-msg-title"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{trans('admin.click_upload')}}.</font></font></h3>
        <p><span class="dropzone-msg-desc"><font style="vertical-align: inherit;"><font
                            style="vertical-align: inherit;">{{trans('admin.upload_max_6')}}</font></font></span>
        </p>
        <span class="dropzone-msg-desc"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{trans('admin.upload_image')}}</font></font></span>
    </div>
    @foreach($images as $key=>$value)
        @if($value != '')
            <div class="dz-preview dz-compele dz-compele-{{$key}}" data-key="{{$key}}">
                <div class="dz-image"
                     data-background="{{\App\Http\Helpers\CommonHelper::getUrlImageThumb($value)}}"
                     style="background-size: cover !important;background: url({{\App\Http\Helpers\CommonHelper::getUrlImageThumb($value, 120, 120)}})">
                    <span class="tooltiptext bg-success">{{trans('admin.click_zoom')}}</span>
                </div>
                <a class="dz-remove text-info" title="Bấm vào để xóa ảnh !" onclick="deleteImg($(this))"
                   data-action="input-compele-{{$key}}">{{trans('admin.delete_image')}}</a>
            </div>
        @endif
    @endforeach
</div>
<input type="file" class="hidden" multiple accept='image/*' id="{{$field['name']}}_file">
<div id="value-dropzone-{{$field['name']}}">
    @foreach($images as $key=>$value)
        @if($value != '')
            <input type="hidden"  class="input-compele-{{$key}}" name="{{$field['name']}}[]"
                   value="{{$value}}">
        @endif
    @endforeach
</div>
<script>
    $(document).ready(function () {
        $('body').on('click', "#{{$field['name']}}_dropzone", function (e) {
            if (e.target !== this)
                return;
            $("#{{$field['name']}}_file").click();
        });

        var count_change = 0;

        $('#{{$field['name']}}_dropzone').dropzone({
            url: "{{ route('ajax-up-file2') }}", // Set the url for your upload script location
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 10,
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            accept: function(file, done) {
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }


            },
            init: function() {
                this.on("success", function(file, result) {
                    console.log(result.data);

                    var j;
                    var count_img = k = $('#{{$field['name']}}_dropzone .dz-preview').length;
                    for (j = 0; j < result.data.length; j++) {
                        let class_input = count_change.toString() + '-' + j;
                        let btn_delete = result.data[j].status ? '<a class="dz-remove text-info" title="Bấm vào để xóa ảnh !" onclick="deleteImg($(this))" data-action="input-' + class_input + '">Xóa ảnh</a>' : '<a class="dz-remove text-danger">Lỗi</a>';
                        let class_name = result.data[j].status ? 'dz-success' : 'dz-error';
                        let tooltip = result.data[j].status ? '<span class="tooltiptext bg-success">Bấm vào ảnh để phóng to</span>' : '<span class="tooltiptext bg-danger">Ảnh lỗi</span>';
                        $('#{{$field['name']}}_dropzone .dz-preview:nth-child('+(count_img - k)+')').append(tooltip);
                        $('#{{$field['name']}}_dropzone .dz-preview:nth-child('+(count_img - k)+')').append(btn_delete).addClass(class_name);
                        $('#{{$field['name']}}_dropzone .dz-preview:nth-child('+(count_img - k)+')').addClass(class_name).addClass('dz-' + class_input);
                        if (result.data[j].status) {
                            let input = '<input type="hidden" class=" ' + 'input-' + class_input + '" name="{{$field['name']}}[]" value="' + result.data[j].value + '">';
                            $('.dz-preview.dz-success.dz-' + class_input).append(input);
                            $("#value-dropzone-{{$field['name']}}").append(input);
                        }
                        k --;
                    }
                    count_change++;
                });
            }
        });


                {{--$("#{{$field['name']}}_dropzone .dz-remove").click(function () {--}}
                {{--    $(this).parents('.dz-preview').remove();--}}
                {{--    $('.' + $(this).data('action')).remove();--}}
                {{--})--}}


        $('body').on('change', "#{{$field['name']}}_file", function () {
            //Lấy ra files
            let input = this;
            var i;
            for (i = 0; i < input.files.length; i++) {
                if (input.files && input.files[i]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $("#{{$field['name']}}_dropzone").append(' <div class="dz-preview dz-processing"><div class="dz-image" data-background="' + e.target.result + '" style="background-size: cover !important;background: url(' + e.target.result + ')"></div></div>');
                    }
                    reader.readAsDataURL(input.files[i]);
                }
            }

            var file_data = $("#{{$field['name']}}_file").prop('files');
            var i;
            var img_true = 0;
            var length = file_data.length;
            var form_data = new FormData();

            for (i = 0; i < length; i++) {
                //lấy ra kiểu file
                var type = file_data[i].type;
                //Xét kiểu file được upload
                var match = ["image/gif", "image/png", "image/jpg", "image/jpeg"];
                //kiểm tra kiểu file
                if (type == match[0] || type == match[1] || type == match[2] || type == match[3]) {
                    img_true++;
                    //thêm files vào trong form data
                    form_data.append('file[]', file_data[i]);

                } else {
                    $("#{{$field['name']}}_file").val('');
                    toastr.error('Chỉ được upload file ảnh');
                    return false;
                }
            }

            // console.log(form_data)
            $.ajax({
                type: "POST",
                url: '{{ route('ajax-up-file2') }}',
                processData: false,
                contentType: false,
                data: form_data,
                cache: false,
                success: function (result) {
                    var j;
                    for (j = 0; j < result.data.length; j++) {
                        let class_input = count_change.toString() + '-' + j;
                        let text = result.data[j].status ? '<a class="dz-remove text-info" title="Bấm vào để xóa ảnh !" onclick="deleteImg($(this))" data-action="input-' + class_input + '">Xóa ảnh</a>' : '<a class="dz-remove text-danger">Lỗi</a>';
                        let class_name = result.data[j].status ? 'dz-success' : 'dz-error';
                        let tooltip = result.data[j].status ? '<span class="tooltiptext bg-success">Bấm vào ảnh để phóng to</span>' : '<span class="tooltiptext bg-danger">Ảnh lỗi</span>';
                        $("#{{$field['name']}}_dropzone .dz-processing .dz-image").eq(j).append(tooltip);
                        $("#{{$field['name']}}_dropzone .dz-processing").eq(j).append(text).addClass(class_name);
                        $("#{{$field['name']}}_dropzone .dz-processing").eq(j).addClass(class_name).addClass('dz-' + class_input);
                        if (result.data[j].status) {
                            let input = '<input type="hidden" class=" ' + 'input-' + class_input + '" name="{{$field['name']}}[]" value="' + result.data[j].value + '">'
                            $("#value-dropzone-{{$field['name']}}").append(input);
                        }
                    }
                    count_change++;
                    $("#{{$field['name']}}_dropzone .dz-processing").removeClass('dz-processing');
                    $("#{{$field['name']}}_file").val('');
                    let limit = false
                    $("#{{$field['name']}}_dropzone .dz-preview.dz-success,#{{$field['name']}}_dropzone .dz-compele.dz-preview").each(function (index) {
                        if (index > 5) {
                            $('.' + $(this).find('.dz-remove.text-info').data('action')).remove();
                            $(this).remove();
                            limit = true
                        }
                    });
                    if (limit) {
                        toastr.error('Chỉ cho phép upload 6 ảnh');
                    }
                }
            });


        })
    });


    function deleteImg(image) {
        image.parents('.dz-preview').remove();
        $('.' + image.data('action')).remove();
    }

</script>
<style>
    .dz-preview {
        position: relative;
        display: inline-block;
        height: 80% !important;
        cursor: pointer !important;;
    }

    .dz-image .tooltiptext {
        visibility: hidden;
        width: 120px;
        color: #fff;
        text-align: center;
        border-radius: 5px;
        padding: 5px 0;
        position: absolute;
        z-index: 100;
        top: 23px;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .dz-image:hover .tooltiptext {
        visibility: visible;
    }
</style>