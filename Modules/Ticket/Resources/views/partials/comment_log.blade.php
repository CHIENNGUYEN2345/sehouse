<style>
    .card.card-custom > .card-body {
        padding: 2rem 2.25rem;
    }

    .card-body {
        -webkit-box-flex: 1;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        min-height: 1px;
        padding: 2.25rem;
    }

    .scroll.scroll-pull {
        padding-right: 12px;
        margin-right: -12px;
    }

    .align-items-start {
        -webkit-box-align: start !important;
        -ms-flex-align: start !important;
        align-items: flex-start !important;
    }

    .flex-column {
        -webkit-box-orient: vertical !important;
        -webkit-box-direction: normal !important;
        -ms-flex-direction: column !important;
        flex-direction: column !important;
    }

    .align-items-center {
        -webkit-box-align: center !important;
        -ms-flex-align: center !important;
        align-items: center !important;
    }

    .symbol.symbol-circle {
        border-radius: 50%;
    }

    .symbol {
        display: inline-block;
        -ms-flex-negative: 0;
        flex-shrink: 0;
        position: relative;
        border-radius: 0.42rem;
    }

    .symbol.symbol-40 > img {
        width: 100%;
        max-width: 40px;
        height: 40px;
    }

    .symbol.symbol-circle > img {
        border-radius: 50%;
    }

    a.text-hover-primary, .text-hover-primary {
        -webkit-transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease, -webkit-box-shadow 0.15s ease;
        transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease, -webkit-box-shadow 0.15s ease;
        transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
        transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease, -webkit-box-shadow 0.15s ease;
    }

    .font-size-h6 {
        font-size: 1.175rem !important;
    }

    .text-dark-75 {
        color: #3F4254 !important;
    }

    .font-weight-bold {
        font-weight: 500 !important;
    }

    .font-size-sm {
        font-size: 0.925rem;
    }

    .text-muted {
        color: #B5B5C3 !important;
    }

    .text-muted {
        color: #B5B5C3 !important;
    }

    .font-size-lg {
        font-size: 1.08rem;
    }

    .text-dark-50 {
        color: #7E8299 !important;
    }

    .max-w-400px {
        max-width: 400px !important;
    }

    .bg-light-success {
        background-color: #C9F7F5 !important;
    }

    .font-weight-bold {
        font-weight: 500 !important;
    }

    .text-left {
        text-align: left !important;
    }

    .align-items-end {
        -webkit-box-align: end !important;
        -ms-flex-align: end !important;
        align-items: flex-end !important;
    }

    .bg-light-primary {
        background-color: #E1F0FF !important;
    }

    .card.card-custom > .card-footer {
        background-color: transparent;
    }

    .card-footer:last-child {
        border-radius: 0 0 calc(0.42rem - 1px) calc(0.42rem - 1px);
    }

    .card-footer {
        padding: 2rem 2.25rem;
        background-color: #ffffff;
        border-top: 1px solid #EBEDF3;
    }

    .comment-item {
        position: relative;
    }
    .delete {
        display: none;
        position: absolute;
        color: red;
        top: 3px;
        left: 5px;
    }
    .comment-item:hover .delete {
        display: block;
    }

    .bg-comment {
        padding: 6px 12px 7px 12px !important;
        word-wrap: break-word;
        border-bottom-left-radius: 1.3em !important;
        border-top-left-radius: 1.3em !important;
        border-bottom-right-radius: 1.3em !important;
        border-top-right-radius: 1.3em !important;
    }
    .bg-left {
        background-color: #f1f0f0 !important;
        color: rgba(0, 0, 0, 1) !important;
    }
    .bg-right {
        background-color: rgb(0, 153, 255) !important;
        color: #fff !important;
    }
    textarea#comment_content {
        padding: 9px 8px 9px 12px !important;
        background-color: rgba(0, 0, 0, .05);
    }
</style>
<?php
$can_delete_comment = \App\Http\Helpers\CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name') == 'super_admin' ? true : false;
?>
<div class="card-body">
    <!--begin::Scroll-->
    <div class="scroll scroll-pull ps ps--active-y" data-height="375" data-mobile-height="300"
         style="">
        <!--begin::Messages-->
        <div class="messages">
            <?php
            $comments = \Modules\Ticket\Models\CommentLog::where('item_id', @$result->id)->orderBy('id', 'asc')->where('reply', null)->get();
            ?>
            @foreach($comments as $comment)
                @if(in_array(\App\Http\Helpers\CommonHelper::getRoleName($comment->admin_id, 'name'), ['customer',
                'customer_ldp_vip']))
                    <div class="d-flex flex-column mb-5 align-items-end" data-id="{{ $comment->id }}">
                        <div class="d-flex align-items-center">
                            <div>
                                <a href="/admin/profile/{{ @$comment->admin->id }}" data-admin_id="{{ $comment->admin_id }}"
                                   class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">{{ @$comment->admin->name }}</a>
                            </div>
                            <div class="symbol symbol-circle symbol-40 ml-3">
                                <img alt="Khách hàng" class="lazy"
                                     data-src="{{ \App\Http\Helpers\CommonHelper::getUrlImageThumb(@$comment->admin->image, 100, 100) }}">
                            </div>
                        </div>
                        <div class="mt-2 rounded p-5 bg-light-primary bg-right bg-comment text-dark-50 font-weight-bold font-size-lg max-w-400px"  title="{{ date('H:i d/m/Y', strtotime($comment->created_at)) }}">
                            {!! nl2br($comment->content) !!}
                            <br>
                            <?php $imgs = explode('|', $comment->image_present);?>
                            @foreach($imgs as $img)
                                @if($img != '')
                                    <img class="lazy file_image_thumb" title="Click vào để phóng to ảnh" data-src="{{ \App\Http\Helpers\CommonHelper::getUrlImageThumb($img, 62, null) }}">
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="d-flex flex-column mb-5 align-items-start" data-id="{{ $comment->id }}">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-circle symbol-40 mr-3">
                                <img alt="Nhân viên" class="lazy"
                                     data-src="{{ \App\Http\Helpers\CommonHelper::getUrlImageThumb(@$comment->admin->image, 100, 100) }}">
                            </div>
                            <div>
                                <a href="/admin/profile/{{ @$comment->admin->id }}" title="{{ date('H:i d/m/Y', strtotime($comment->created_at)) }}" data-admin_id="{{ $comment->admin_id }}"
                                   class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">{{ @$comment->admin->name }}</a>
                            </div>
                        </div>
                        <div class="mt-2 rounded p-5 bg-light-success bg-left bg-comment text-dark-50 font-weight-bold font-size-lg text-left max-w-400px comment-item" title="{{ date('H:i d/m/Y', strtotime($comment->created_at)) }}">
                            @if($can_delete_comment)
                            <a href="/admin/{{ $module['code'] }}/{{ @$result->id }}/comment/{{ $comment->id }}/delete" class="delete">
                                <i class="fa fa-trash"></i>
                            </a>
                            @endif
                            {!! nl2br($comment->content) !!}
                            <br>
                            <?php $imgs = explode('|', $comment->image_present);?>
                            @foreach($imgs as $img)
                                @if($img != '')
                                    <img class="lazy file_image_thumb" title="Click vào để phóng to ảnh" data-src="{{ \App\Http\Helpers\CommonHelper::getUrlImageThumb($img, 62, null) }}">
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <!--end::Messages-->
        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; right: -2px; height: 375px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 133px;"></div>
        </div>
    </div>
    <!--end::Scroll-->
</div>
<div class="card-footer align-items-center">
    <!--begin::Compose-->
    <textarea class="form-control border-0 p-0" rows="2" id="comment_content" name="comment_content" placeholder="Nhập nội dung"></textarea>
    <span class="form-text text-muted">Để xuống dòng: ấn tổ hợp phím alt + enter hoặc ctrl + enter</span>
    <div class="d-flex align-items-center justify-content-between mt-5">
        <div class="col-sm-12">
            <?php
            $field = ['name' => 'image_present', 'type' => 'multiple_image_dropzone', 'count' => '6', 'label' => 'Ảnh khác'];
            ?>
            @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
        </div>
    </div>
    <!--begin::Compose-->
</div>
<script>
    $('.chose-file').click(function () {
        $('#attach').click();
    });
</script>
<script>
    $(document).ready(function () {
        $('#comment_content').keydown(function (e) {
            if (e.keyCode == 13) {
                if (e.ctrlKey || e.altKey) {
                    $('#comment_content').val( $('#comment_content').val() + "\n");
                    return true;
                }
                $('form.ticket').submit();
            }
        });
    });
</script>

<script>
    var callCountCommentContent = 0
    function countCallFunctionCommentContent() {
        console.log(callCountCommentContent);
        if (callCountCommentContent == 0) {
            initFunctionCommentContent();
        }
        callCountCommentContent ++;
        return true;
    }

    function initFunctionCommentContent() {
        textareaInitCommentContent();
    }


    $('#comment_content').click(function () {
        $(this).hide();
        $('#comment_content').show().click();
    });
    $(document).ready(function () {
        $('#comment_content').click(function () {
            console.log('e');
            countCallFunctionCommentContent();
        });
    });
    var observe;
    if (window.attachEvent) {
        observe = function (element, event, handler) {
            element.attachEvent('on' + event, handler);
        };
    } else {
        observe = function (element, event, handler) {
            element.addEventListener(event, handler, false);
        };
    }

    function textareaInitCommentContent() {
        var note = document.getElementById('comment_content');

        function resize() {
            note.style.height = 'auto';
            note.style.height = note.scrollHeight + 'px';
        }

        /* 0-timeout to get the already changed note */
        function delayedResize() {
            window.setTimeout(resize, 0);
        }

        observe(note, 'change', resize);
        observe(note, 'cut', delayedResize);
        observe(note, 'paste', delayedResize);
        observe(note, 'drop', delayedResize);
        observe(note, 'keydown', delayedResize);

        note.focus();
        note.select();
        resize();
    }
</script>