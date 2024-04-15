<style>

    .ul_multiple_image_link li {
        /*width: 110px !important;*/
        height: 150px !important;
        float: left;
        background-color: #fff;
        color: white;
        /*display: inline-block;*/
        font-weight: bold;
        text-decoration: none;
        margin-top: 10px !important;
        position: relative;
    }

    .ul_multiple_image_link li:first-child {
        margin: 0;
    }

    .ul_multiple_image_link .close_t {
        width: 10px;
        height: 10px;
        position: absolute;
        left: 34px;
        top: 7px;
        background: url({{ asset('public/images_core/close.png') }}) no-repeat center #FFFFFF;
        display: none;
        padding: 8px;
    }

    .ul_multiple_image_link .close_t.show {
        display: block;
    }

    .ul_multiple_image_link .up_img_t {
        text-align: center;
        overflow: hidden;
        cursor: pointer;
    }

    .ul_multiple_image_link .up_img_t img {
        width: 73%;
        min-height: 108px;
        max-height: 108px;
        margin: auto;
        border: 1px solid #cdcdcd;
        border-radius: 4px;
    }

    .ul_multiple_image_link .up_img_t .add-link {
        padding: 5px 0 20px 0;
    }

    .ul_multiple_image_link .up_img_t .add-link input {
        width: 100%;
    }
</style>
<div class="row ul_multiple_image_link">
    @php
        $images = explode('|', @$result->{$field['name']});
        $inputs = explode('|', @$result->{'input_'.$field['name']});
    @endphp
    @for($i = 0; $i <= $field['count'] - 1; $i ++)
{{--        @if(isset($images[$i]))--}}
            <div class="col-xs-6 col-md-6">
                <div class="up_img_t">
                    <img class="{{ $field['name'] }}upload file_image_thumb {{ $field['name'] }}{{ $i }}"
                         value="{{ $i }}"
                         @if(@$images[$i] == '')
                         src="{{ asset('public/images_core/dang-hinh.png') }}"
                         @else
                         src="{{ asset('public/filemanager/userfiles/' . @$images[$i]) }}"
                            @endif>
                    <input type="file" name="{{ $field['name'] }}" class="{{ $field['name'] }}"
                           value="{{ $i }}"
                           id="{{ $field['name'] }}{{ $i }}"
                           style="display:none;">
                    <input type="hidden" id="{{ $field['name'] }}_{{ $i }}" name="{{ $field['name'] }}[]"
                           value="{{@$images[$i]}}">
                    <span class="{{ @$images[$i] == '' ? '' : 'show'}} close_t close_t{{ $field['name'] }}"
                          id="close_{{ $field['name'] }}_{{ $i }}"
                          value="{{ $i }}"
                          style=""> </span>
                    <div class="add-link">
                        <input type="text" placeholder="Nhập link {{$field['label']}}"
                               name="input_{{ $field['name'] }}[]" value="{{@$inputs[$i]}}">
                    </div>
                </div>
            </div>
        {{--@endif--}}
    @endfor
</div>
<script>

    $(document).ready(function () {
        // my upload image ver 1.0

        $(".ul_multiple_image_link .{{ $field['name'] }}upload").click(function () {

            var id = $(this).attr("value");
            var valueTest = $(".ul_multiple_image_link #{{ $field['name'] }}_" + id).attr("value");

            if (valueTest == "" || valueTest == "public/images_core/dang-hinh.png") $(".ul_multiple_image_link input[id='{{ $field['name'] }}" + id + "']").click();

            $(".ul_multiple_image_link input[id='{{ $field['name'] }}" + id + "']").change(function () {

                var filename = $(".ul_multiple_image_link #{{ $field['name'] }}" + id).val();

                // Use a regular expression to trim everything before final dot
                var extension = filename.replace(/^.*\./, '');

                // Iff there is no dot anywhere in filename, we would have extension == filename,
                // so we account for this possibility now
                if (extension == filename) {
                    extension = '';
                } else {
                    // if there is an extension, we convert to lower case
                    // (N.B. this conversion will not effect the value of the extension
                    // on the file upload.)
                    extension = extension.toLowerCase();
                }

                switch (extension) {
                    case 'jpg':/* do no thing*/
                        ;
                        break;
                    case 'jpeg':/* do no thing*/
                        ;
                        break;
                    case 'png': /* do no thing*/
                        ;
                        break;

                    // uncomment the next line to allow the form to submitted in this case:
                    //          break;

                    default:
                        // Cancel the form submission
                        alert("Loại tập tin này không được chấp nhận");
                        submitEvent.preventDefault();
                }

                obj = this;
                var fd;
                fd = new FormData();
                fd.append('file', $('#{{ $field['name'] }}' + id)[0].files[0]);

                $(".ul_multiple_image_link .{{ $field['name'] }}" + id).attr({
                    src: "{{ asset('public/images_core/loading.gif') }}",
                    title: "loading",
                    alt: "loading Logo"
                });

                $.ajax({
                    type: "POST",
                    url: '{{ URL::to('admin/ajax-up-file') }}?id=' + id,
                    processData: false,
                    contentType: false,
                    data: fd,
                    cache: false,
                    success: function (data) {
                        if (data.status) {
                            $(".ul_multiple_image_link .{{ $field['name'] }}" + id).attr({
                                src: data.file,
                                title: "loading",
                                alt: "loading Logo"
                            });
                            $(".ul_multiple_image_link #{{ $field['name'] }}_" + id).attr("value", data.value);
                            $(".ul_multiple_image_link #close_{{ $field['name'] }}_" + id).css("display", "block");
                        } else {
                            toastr.error(data.msg)
                        }
                    }
                });
            });

        });

        $(".ul_multiple_image_link .close_t{{ $field['name'] }}").click(function () {
            var id = $(this).attr("value");
            $(".ul_multiple_image_link .{{ $field['name'] }}" + id).attr({
                src: "{{ asset('public/images_core/dang-hinh.png') }}",
                title: "loading",
                alt: "loading Logo"
            }).removeClass('file_image_thumb');
            $(".ul_multiple_image_link #{{ $field['name'] }}_" + id).attr("value", "");
            $(this).css("display", "none");
        });

    });
</script>
