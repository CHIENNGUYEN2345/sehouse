<?php
$src = $item->{$field['name']};
$imageArray = [];

if (strpos($item->{$field['name']}, '|') !== false) {
    $imageArray = explode('|', $item->{$field['name']});
    $src = $imageArray[0];

}
?>
<style>
    @media (max-width: 435px) {
        .modal-content.bbbbbbbb {
            margin: 50px 0 0 0 !important;
        }

        .file_image_thumb1111 img {
            width: 100% !important;
            height: 60px !important;
        }

        .file_image_thumb1111 {
            width: 150px !important;
        }
    }
</style>

<div class="kt-media {{ @$field['style'] }}">
    <img data-id="{{$item->id}}" src="{{asset('/filemanager/userfiles/' . $src)}}"
         class="file_image_thumb1111"
         title="Click để xem ảnh" style="cursor: pointer;width: 150px">
</div>
<script>
    var imageArray = <?php echo json_encode($imageArray); ?>;

    var baseUrl = "<?php echo asset('/filemanager/userfiles/'); ?>";

    function showImages() {
        var html = '<div id="imageGallery" class="modal" tabindex="-1" role="dialog">';
        html += '<div class="modal-dialog" role="document">';
        html += '<div class="modal-content">';
        html += '<div class="modal-body">';
        for (var i = 0; i < imageArray.length; i++) {
            var imageSrc = baseUrl + '/' + imageArray[i];
            html += '<img src="' + imageSrc + '" class="gallery-image" />';
        }
        html += '</div>';
        html += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        $('body').append(html);
        $('#imageGallery').modal();
    }

</script>
