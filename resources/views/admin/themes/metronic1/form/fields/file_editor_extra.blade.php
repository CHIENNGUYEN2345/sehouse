<?php
if (!isset($field['value'])) $field['value'] = '';
?>
<div class="row">
    @for($i = 1; $i <= $field['count']; $i++)
        <div class="col-sm-6" id="form-group-{{ $field['name'] . $i }}">
            <div class="thumb">
                <div class="div0-{{ $field['name'] . $i }}">
                    <div class="div1-{{ $field['name'] . $i }}" style="display: none;">
                        <div class="thumbactions_img">
                            <div class="wrap-thumbnail"></div>
                            <div class="thumbactions" data-thumb="thumbnail">
                                <a class="btn btn-block btn-danger deleteimage"
                                   name="{{ $field['name'] . $i }}"><i
                                            class="fa fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="div2-{{ $field['name'] . $i }}" style="display: block;">
                        <i class="fa fa-plus fa-2x" onclick="BrowseFile('{{ $field['name'] . $i }}');"
                           style="cursor: pointer"></i>
                        <h4 class="text-muted" onclick="BrowseFile('{{ $field['name'] . $i }}');"
                            style="cursor: pointer">Chọn {{ $field['label'] }}.</h4>
                        <input type="hidden" id="{{ $field['name'] . $i }}"
                               name="{{ $field['name'] . $i }}" {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}>
                    </div>
                </div>
            </div>
        </div>
    @endfor
</div>
<script>
    $(document).ready(function () {
        @if($field['value'] != '')
        <?php $img_arr = explode('|', $field['value']);?>

        @foreach($img_arr as $i => $value)
        <?php $i += 1;?>
        @if(in_array(pathinfo($value, PATHINFO_EXTENSION), ['jpg', 'JPG', 'jpeg', 'png', 'gif']))     //  Neu != file anh thi hien  thi link
        $('.div0-{{ $field['name'] . $i }}').find('.wrap-thumbnail').html('<img data-src="{{ CommonHelper::getUrlImageThumb($value, null, 215) }}" class="lazy file_image_thumb" title="CLick để phóng to ảnh">');
        @else
        $('.div0-{{ $field['name'] . $i }}').find('.wrap-thumbnail').html('<a href="{{ URL::asset('filemanager/userfiles/' . $value) }}" target="_blank">{{ $value }}</a>');
        @endif

        $('.thumb .div1-{{ $field['name'] . $i }}').css('display', 'block');
        $('.thumb .div2-{{ $field['name'] . $i }}').css('display', 'none');
        $('input[name={{ $field['name'] . $i }}]').val('{{ $value }}');

        $('.thumb input#{{ $field['name'] . $i }}').change(function () {
            $('.div0-' + $(this).attr('name')).find('.wrap-thumbnail').html('<img data-src="' + $(this).val() + '" class="lazy">');
            $('.div1-' + $(this).attr('name')).css('display', 'block');
            $('.div2-' + $(this).attr('name')).css('display', 'none');
        });
        @endforeach
        @endif
    });
</script>
