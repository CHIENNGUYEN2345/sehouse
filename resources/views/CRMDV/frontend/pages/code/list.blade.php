<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kho giao diện website chuẩn seo - HBWEB.VN</title>
    <meta name='robots' content='noindex,nofollow' />

    <meta property="og:url" content="https://hbsoft.top/" />
    <meta property="og:site_name" content="HBWEB" />
    <meta property="og:image" content="{{ asset('filemanager/userfiles/upload/2022/05/08/kho-giao-dien.png') }}" />

    <link rel="shortcut icon" href="https://hbweb.vn/wp-content/uploads/2022/09/favico-200x200-1.png"
          type="image/x-icon"/>
    <style>
        body {
            background-color: unset !important;
        }

        @media(min-width: 768px) {
            .col-md-3 {
                float: left;
                padding-left: 10px;
            }
            .col-md-2 {
                padding-left: 10px;
            }
        }

        @media(max-width: 768px) {
            .col-xs-6 {
                display: inline-block;
                float: left;
                width: 50%;
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
            }
        }

        .col-md-1, .col-md-2, .col-md-3 {
            display:inline-block;
        }

        {{----------------------------------CSS Show sản phẩm---------------------------------}}
            .kho-code .content-theme-item .action .demo, .content-theme-item .action .choose {
            box-shadow: none;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            margin: 0 10px;
            padding: .5em 1em;
            text-decoration: none;
            text-shadow: none;
        }

        .kho-code .content-theme-item .action .demo {
            background: #ff7200 none repeat scroll 0 0;
        }

        .kho-code .content-theme-item:hover .action {
            bottom: 0;
            visibility: visible;
        }

        .kho-code .content-theme-item {
            border: 1px solid #e5e5e5;
            box-sizing: border-box;
            cursor: pointer;
            float: left;
            height: 420px;
            margin-bottom: 40px;
            margin-right: 2%;
            overflow: hidden;
            position: relative;
            transition: all 0.3s ease 0s;
            width: 31%;
        }

        .kho-code .screen-shoot {
            width: 100%;
            height: 400px;
            left: 0;
            position: absolute;
            top: 0;
            transition: all 3s ease-in-out 0s;
        }

        .kho-code .content-theme-item .title {
            background: #fff none repeat scroll 0 0;
            border-top: 1px solid #ddd;
            bottom: 0;
            box-sizing: border-box;
            color: #2194d2;
            font-size: 20px;
            font-weight: bold;
            left: 0;
            line-height: normal;
            min-height: 32px;
            padding: 5px 10px;
            position: absolute;
            text-align: left;
            width: 100%;
            z-index: 1;
        }

        .kho-code .content-theme-item .action {
            background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
            bottom: 100%;
            height: 100%;
            left: 0;
            position: absolute;
            transition: all 0.5s ease 0s;
            visibility: hidden;
            width: 100%;
        }

        .kho-code .screen-shoot img {
            width: 100%;
            height: auto;
            max-width: 100%;
        }

        .kho-code #related-themes .price {
            color: #e06d18;
        }

        .kho-code .content-theme-item .title span.price {
            color: #e10000;
            display: block;
            font-size: 16px;
            font-weight: 700;
            margin-top: 5px;
        }

        .kho-code .theme-info {
            box-sizing: border-box;
            color: #fff;
            float: left;
            font-size: 15px;
            padding: 10px;
            width: 100%;
            display: none;
        }

        .kho-code .theme-links {
            line-height: 20px;
            margin-bottom: 10px;
            position: absolute;
            text-align: center;
            width: 100%;
        }

        .kho-code .content-theme-item .action .demo, .content-theme-item .action .choose {
            box-shadow: none;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            margin: 0 10px;
            padding: .5em 1em;
            text-decoration: none;
            text-shadow: none;
        }

        {{----------------------------------CSS Phân Trang---------------------------------}}

        {{----------------------------------CSS chọn loại Ladipage---------------------------------}}
        .kho-code-form .form-control {
            display: block;
            width: 100%;
            height: 34px;
            padding: 8px 13px;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #e2e5ec;
            border-radius: 4px;
            -webkit-transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
            transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
        }

        .kho-code-form .form-select {
            display: inline-block;
            width: 100%;
            padding: .375rem 2.25rem .375rem .75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-repeat: no-repeat;
            background-position: right .75rem center;
            background-size: 20px 12px;
            border: 1px solid #ced4da;
            border-radius: 25px;
        }

        .kho-code-form .form-select:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
        }

        .kho-code-form .form-select:focus option {
            border-color: #86b7fe;
        }

        .theme-container {
            max-width: 1140px;
            margin-right: auto;
            margin-left: auto;
        }

        ul.pagination {
            margin: 2px 0;
            padding: 0;
            float: left;
            white-space: nowrap;
            -webkit-box-pack: end;
            -ms-flex-pack: end;
            justify-content: flex-end;
        }

        ul.pagination li {
            margin-left: 0.4rem;
            list-style: none;
        }

        .kt-datatable__pager-info {
            display: inline-block;
            float: right;
        }

        .dropdown.bootstrap-select.kt-datatable__pager-size {
            display: inline-block;
        }

        ul.pagination li.active span {
            border-radius: 3px;
            cursor: pointer;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            height: 2rem;
            min-width: 2rem;
            vertical-align: middle;
            padding: 1.5rem;
            text-align: center;
            position: relative;
            font-size: 1rem;
            line-height: 1rem;
            font-weight: 400;
            background: #2c77f4;
            color: #ffffff;
        }

        ul.pagination li a {
            color: #595d6e;
            border: 0;
            outline: none !important;
            border-radius: 3px;
            cursor: pointer;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            height: 2rem;
            min-width: 2rem;
            vertical-align: middle;
            padding: 1.5rem;
            text-align: center;
            position: relative;
            font-size: 1rem;
            line-height: 1rem;
            font-weight: 400;
        }

        .d-none {
            display: none !important;
        }

        #form-search {
            width: 100%;
            display: inline-block;
        }

        .kt-datatable__pager.kt-datatable--paging-loaded {
            display: inline-block;
            width: 100%;
        }

        .kho-code-form {
            display: flex;
            position: fixed;
            top: 0;
            z-index: 99999999;
            width: 100%;
            /*background: #fff;*/
            padding: 10px 0px;
        }

        #form-search {
            display: inline-block;
            width: unset;
            margin: auto;
        }

        .themes-list {
            display: inline-block;
            width: 100%;
            margin-top: 63px;
        }

        @media(max-width: 768px) {
            .themes-list {
                margin-top: 87px;
            }
        }

        .theme-container {
            background: none;
        }

        @media (min-width: 768px) {
            .col-md-4 {
                float: left;
                -webkit-box-flex: 0;
                -ms-flex: 0 0 33.33333%;
                flex: 0 0 33.33333%;
                max-width: 33.33333%;
            }
        }

        @media (max-width: 768px) {
            .kho-code .content-theme-item {
                width: calc(50% - 8px);
                float: left;
                margin: 0;
                height: 301px;
                margin-top: 15px;
                padding: 5px !important;
                margin-left: 5px;
            }

            .generality-page {
                margin-top: 10px;
            }

            .kt-datatable__pager-info,
            ul.pagination {
                width: 100%;
                text-align: center;
            }

            .kho-code .content-theme-item .title {
                font-size: 14px;
            }

        }
        .kho-code .content-theme-item .title .p-title {
            text-align: center;
            margin: 0;
        }
        .kho-code .content-theme-item .title .type {
            color: #000;
            display: inline-block;
            float: right;
            font-size: 13px;
            font-weight: 400;
        }
        .kho-code .content-theme-item .title span.price {
            display: inline-block;
        }
        /*.kho-code .screen-shoot img {
            bottom: -1210px;
            width: 100%;
            height: auto;
            position: absolute;
            z-index: 0;
            margin:0;
            padding:0;
            -webkit-transition: top 11s;
            -moz-transition: top 11s;
            -ms-transition: top 11s;
            -o-transition: top 11s;
            transition: bottom 11s;
        }
        .kho-code .screen-shoot:hover img {
            bottom: 0;
            -webkit-transition: all 11s;
            -moz-transition: all 11s;
            -ms-transition: all 11s;
            -o-transition: all 11s;
            transition: all 11s;
        }*/
    </style>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="/libs/jquery-3.4.0.min.js"></script>

</head>
<body>


{{----------------------------------Show sản phẩm---------------------------------}}
<div class="kho-code theme-container">
    <div class="kho-code-form">
        <form method="GET" action="" id="form-search">
            <input type="hidden" name="limit" value="12">
            <div class="col-xs-6 col-md-4">
                <input type="text" name="quick_search" value="{{ @$_GET['quick_search'] }}"
                       class="form-control" title="Enter để tìm kiếm"
                       placeholder="Nhập từ khóa tìm kiếm" ondragenter="$('#form-search').submit();">
            </div>
            <div class="col-xs-6 col-md-3">
                <?php
                $cats = \App\CRMDV\Models\Category::select('id', 'name')->where('type', 10)->where('status', 1)->orderBy('name', 'asc')->get();
                ?>
                <select class="form-select" name="cat_id" onchange="$('select[name=tag_id]').val('');$('#form-search').submit();">
                    <option value="">Tất cả Nghành hàng</option>
                    @foreach($cats as $k => $v)
                        <option value="{{ $v->id }}" {{ @$_GET['cat_id'] == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-6 col-md-3">
                <?php
                $tag_ids = \App\CRMDV\Models\PostTag::where('multi_cat', 'like', '%|'.@$_GET['cat_id'].'|%')->groupBy('tag_id')->pluck('tag_id')->toArray();
                $tags = \App\CRMDV\Models\Tag::whereIn('id', $tag_ids)->orderBy('name', 'asc')->get();
                ?>
                <select class="form-select" name="tag_id" onchange="$('#form-search').submit();">
                    <option value="">Tất cả Sản phẩm</option>
                    @foreach($tags as $k => $v)
                        <option value="{{ $v->id }}" {{ @$_GET['tag_id'] == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-6 col-md-2">
                <select class="form-select" name="owned" onchange="$('#form-search').submit();">
                    <option value="Kho" {{ @$_GET['owned'] == 'Kho' ? 'selected' : '' }}>Kho</option>
                    <option value="server mình" {{ @$_GET['owned'] == 'server mình' ? 'selected' : '' }}>Hosting</option>
                    <option value="Mẫu nước ngoài" {{ @$_GET['owned'] == 'Mẫu nước ngoài' ? 'selected' : '' }}>Mẫu nước ngoài</option>
                    <option value="Tất cả" {{ @$_GET['owned'] == 'Tất cả' ? 'selected' : '' }}>Tất cả</option>
                </select>
            </div>
        </form>
    </div>
    <div class="themes-list">
        <div class="" id="theme-list-wrap">
            @foreach($listItem as $item)
                <div class="content-theme-item theme-item-3col">
                    <div class="screen-shoot" style="top: 0px;">
                        <img src="{{ asset('filemanager/userfiles/' . $item->image) }}">
                    </div>
                    <div class="title">
                        <p class="p-title">{{$item->name}}</p>
                        @if($item->owned != 'server mình') {{--web đã làm thì không hiện giá bán--}}
                            @if(strpos($_SERVER['REQUEST_URI'], 'landingpage') == false
                                        && strpos($_SERVER['REQUEST_URI'], 'ldp') == false)
                                @if($item->price_setup == 0)
                                    {{--<span
                                        class="price">Miễn phí</span>--}}
                                @else
                                    {{--<span
                                        class="price">{{ number_format($item->price_setup) }}đ</span>--}}
                                @endif
                            @endif
                        @endif
                    </div>
                    <div class="action">
                        <div class="theme-info"></div>
                        <div class="theme-links" style="top: 70%;">
                            @if(@$hoba_iframe === false)
                                <a rel="nofollow" target="_blank" class="demo"
                                   href="{{$item->link}}">Xem
                                    Demo</a>
                            @else
                                @if(strpos($_SERVER['REQUEST_URI'], 'landingpage') !== false
                                    || strpos($_SERVER['REQUEST_URI'], 'ldp') !== false)
                                    <a rel="nofollow" target="_blank" class="demo"
                                       href="/mau-landingpage/view?link={{$item->link}}">Xem
                                        Demo</a>
                                @else
                                    @if($item->owned == 'server mình' || $item->owned == null)
                                        <a rel="nofollow" target="_blank" class="demo"
                                           href="/mau-web/{{$item->id}}?hb=1">Xem web
                                        </a>
                                    @else
                                        <a rel="nofollow" target="_blank" class="demo"
                                           href="/mau-web/{{$item->id}}">Xem demo
                                        </a>
                                    @endif

                                @endif
                            @endif
                        <!--<a rel="nofollow" href="#" class="choose choose_theme choose_theme_dgd">Đăng ký ngay</a>-->
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="generality-page">
        <div class="kt-datatable__pager kt-datatable--paging-loaded"
             style=" justify-content: space-between; align-items: center;">
            {!! $listItem->appends(isset($param_url) ? $param_url : '')->links() != '' ? $listItem->appends(isset($param_url) ? $param_url : '')->links() : '<ul class="pagination page-numbers nav-pagination links text-center"></ul>' !!}

            <div class="kt-datatable__pager-info" style="">
                <div class="dropdown bootstrap-select kt-datatable__pager-size" style="width: 60px;">
                    <div class="dropdown bootstrap-select kt-datatable__pager-size select-page-size"
                         style="width: 60px;">
                        <select class="selectpicker kt-datatable__pager-size select-page-size"
                                onchange="$('input[name=limit]').val($(this).val());$('#form-search').submit();"
                                title="Chọn số bản ghi hiển thị" data-width="60px"
                                data-selected="20" tabindex="-98">
                            <option value="12" {{ $limit == 12 ? 'selected' : '' }}>12</option>
                            <option value="18" {{ $limit == 18 ? 'selected' : '' }}>18</option>
                            <option value="24" {{ $limit == 24 ? 'selected' : '' }}>24</option>
                            <option value="30" {{ $limit == 30 ? 'selected' : '' }}>30</option>
                        </select>
                        {{--<button type="button" class="btn dropdown-toggle btn-light" data-toggle="dropdown" role="combobox" aria-owns="bs-select-1" aria-haspopup="listbox" aria-expanded="false" title="20">
                            <div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">20</div></div> </div>
                        </button>--}}
                        <div class="dropdown-menu ">
                            <div class="inner show" role="listbox" id="bs-select-1" tabindex="-1">
                                <ul class="dropdown-menu inner show" role="presentation"></ul>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="kt-datatable__pager-detail">Hiển thị {{ (($page - 1) * $limit) + 1 }} - {{ ($page * $limit) < $record_total ? ($page * $limit) : $record_total }} của {{ @number_format($record_total) }}</span>
            </div>
        </div>
    </div>
</div>


<script type='text/javascript'>/* <![CDATA[ */
    var wpcf7 = {
        "apiSettings": {
            "root": "https:\/\/hobasoft.com\/wp-json\/contact-form-7\/v1",
            "namespace": "contact-form-7\/v1"
        }, "cached": "1"
    }; /* ]]> */</script>
<script type='text/javascript'>/* <![CDATA[ */
    var smooth = {
        "elements": [".smooth-scroll", "li.smooth-scroll a"],
        "duration": "800"
    }; /* ]]> */</script> <!--[if lte IE 11]>
<script type='text/javascript'
        src='https://hobasoft.com/wp-content/themes/generatepress/js/classList.min.js?ver=2.1.4'></script> <![endif]-->
<script type='text/javascript'>/* <![CDATA[ */
    var q2w3_sidebar_options = [{
        "sidebar": "sidebar-1",
        "margin_top": 10,
        "margin_bottom": 0,
        "stop_id": "",
        "screen_max_width": 0,
        "screen_max_height": 0,
        "width_inherit": false,
        "refresh_interval": 1500,
        "window_load_hook": false,
        "disable_mo_api": false,
        "widgets": ["recent-posts-widget-with-thumbnails-2"]
    }]; /* ]]> */</script>
<script>(function (w, d) {
        var b = d.getElementsByTagName("body")[0];
        var s = d.createElement("script");
        s.async = true;
        s.src = !("IntersectionObserver" in w) ? "https://hobasoft.com/wp-content/plugins/wp-rocketxx/inc/front/js/lazyload-8.15.2.min.js" : "https://hobasoft.com/wp-content/plugins/wp-rocketxx/inc/front/js/lazyload-10.17.min.js";
        w.lazyLoadOptions = {
            elements_selector: "img,iframe",
            data_src: "lazy-src",
            data_srcset: "lazy-srcset",
            data_sizes: "lazy-sizes",
            skip_invisible: false,
            class_loading: "lazyloading",
            class_loaded: "lazyloaded",
            threshold: 300,
            callback_load: function (element) {
                if (element.tagName === "IFRAME" && element.dataset.rocketLazyload == "fitvidscompatible") {
                    if (element.classList.contains("lazyloaded")) {
                        if (typeof window.jQuery != "undefined") {
                            if (jQuery.fn.fitVids) {
                                jQuery(element).parent().fitVids();
                            }
                        }
                    }
                }
            }
        }; // Your options here. See "recipes" for more information about async.
        b.appendChild(s);
    }(window, document));

    // Listen to the Initialized event
    window.addEventListener('LazyLoad::Initialized', function (e) {
        // Get the instance and puts it in the lazyLoadInstance variable
        var lazyLoadInstance = e.detail.instance;

        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                lazyLoadInstance.update();
            });
        });

        var b = document.getElementsByTagName("body")[0];
        var config = {childList: true, subtree: true};

        observer.observe(b, config);
    }, false);</script>
<script src="https://hobasoft.com/wp-content/cache/min/1/b4267eb4df56d19265ec293e8dee6955.js" data-minify="1"></script>
<script>!function (f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function () {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '3211776515534126');
    fbq('track', 'PageView');
</script>


<script>
    function loading() {
        if ($('body').find('#loading').length == 0) {
            $('body').append('<div id="loading" style="width: 100%;position: fixed;height: 100%;z-index: 999999;top: 0;text-align: center;background-color: rgba(0, 0, 0, 0.3);"><img style="margin-top: 20%;" src="/images_core/icons/loading.gif"></div>');
        } else {
            $('#loading').show();
        }
    }

    function stopLoading() {
        $('#loading').hide();
    }

</script>


<script>
    $('.content-theme-item').hover(function () {
        var top = $(this).find('img').height() - ($(this).height() - $(this).find('.title').height());
        $(this).find('.screen-shoot').css("top", "" + -top + "px");
        $(this).find('.theme-links').css("bottom", $(this).find('.title').outerHeight() + "px");
    }, function () {
        $(this).find('.screen-shoot').css("top", "0px");
    });

</script>

</body>
</html>