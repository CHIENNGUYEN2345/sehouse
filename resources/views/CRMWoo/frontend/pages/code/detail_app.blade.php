<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <meta name="google-site-verification" content="0xA-3pP2vnyHSV0fyrp5-WNGcbp8Pzapsk6i7ET43Jo" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://hbweb.vn/wp-content/uploads/2022/09/favico-200x200-1.png"
          type="image/x-icon"/>
    <title>App: {{ @$result->name }} - HBWEB</title>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
    <script type="text/javascript" src="https://hbweb.vn/wp-content/themes/generatepress_child/cuongdc//asset/demo/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="https://hbweb.vn/wp-content/themes/generatepress_child/cuongdc//asset/demo/bootstrap.min.js"></script>
    <link href="https://hbweb.vn/wp-content/themes/generatepress_child/cuongdc//asset/demo/demopage.css"
          data-minify="1" rel="stylesheet">
    <link rel='stylesheet' id='bfa-font-awesome-css'
          href='//cdn.jsdelivr.net/fontawesome/4.7.0/css/font-awesome.min.css?ver=4.7.0' type='text/css' media='all'/>


    <meta name="description" content="HBWEB - chuyên thiết kế website, app mobile"/>
    <meta property="og:locale" content="vi_VN" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="App: {{ @$result->name }} - HBWEB" />
    <meta property="og:description" content="HBWEB - chuyên thiết kế website, app mobile" />
    <meta property="og:url" content="https://hbweb.vn/" />
    <meta property="og:site_name" content="HBWEB" />
    <meta property="og:image" content="{{ asset('filemanager/userfiles/' . @$result->image) }}" />
    <meta property="og:image:secure_url" content="{{ asset('filemanager/userfiles/' . @$result->image) }}" />
    <meta property="og:image:width" content="1024" />
    <meta property="og:image:height" content="684" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:description" content="HBWEB - chuyên thiết kế website, app mobile" />
    <meta name="twitter:title" content="App: {{ @$result->name }} - HBWEB" />
    <meta name="twitter:image" content="{{ asset('filemanager/userfiles/' . @$result->image) }}" />
    <script type='application/ld+json' class='yoast-schema-graph yoast-schema-graph--main'>{"@context":"https://schema.org","@graph":[{"@type":"Organization","@id":"https://hbweb.vn/#organization","name":"HBWEB","url":"https://hbweb.vn/","sameAs":[],"logo":{"@type":"ImageObject","@id":"https://hbweb.vn/#logo","url":"https://hbweb.vn/wp-content/uploads/2018/10/LOGO-HOBA-SOFT-svg.svg","caption":"HBWEB"},"image":{"@id":"https://hbweb.vn/#logo"}},{"@type":"WebSite","@id":"https://hbweb.vn/#website","url":"https://hbweb.vn/","name":"HBWEB","publisher":{"@id":"https://hbweb.vn/#organization"},"potentialAction":{"@type":"SearchAction","target":"https://hbweb.vn/?s={search_term_string}","query-input":"required name=search_term_string"}},{"@type":"WebPage","@id":"https://hbweb.vn/#webpage","url":"https://hbweb.vn/","inLanguage":"vi","name":"Thi\u1ebft k\u1ebf website, app mobile chuy\u00ean nghi\u1ec7p, uy t\u00edn - HBWEB","isPartOf":{"@id":"https://hbweb.vn/#website"},"about":{"@id":"https://hbweb.vn/#organization"},"image":{"@type":"ImageObject","@id":"https://hbweb.vn/#primaryimage","url":"/wp-content/uploads/2018/10/insa-web.png"},"primaryImageOfPage":{"@id":"https://hbweb.vn/#primaryimage"},"datePublished":"2017-05-16T21:27:14+00:00","dateModified":"2020-01-03T05:04:06+00:00","description":"C\u00f4ng ty HBWEB chuy\u00ean cung c\u1ea5p c\u00e1c d\u1ecbch v\u1ee5 thi\u1ebft k\u1ebf website (landingpage, tin t\u1ee9c, b\u00e1n h\u00e0ng, gi\u1edbi thi\u1ec7u c\u00f4ng ty) - App mobile - Ph\u1ea7n m\u1ec1m qu\u1ea3n l\u00fd"}]}</script>
    <!-- / Yoast SEO plugin. -->

    <style>
        body {
            background-color: unset !important;
        }

        .col-md-3 {
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
            padding: 0.5rem;
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
            padding: 0.5rem;
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
            position: fixed;
            top: 0;
            z-index: 99999999;
            width: 100%;
            background: #fff;
            padding: 10px 0px;
        }

        #form-search {
            margin: 0;
        }

        .themes-list {
            display: inline-block;
            width: 100%;
            margin-top: 51px;
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
        <h4>Tên: {{ $result->name }}</h4>
        <img src="{{ asset('/filemanager/userfiles/' . $result->image) }}">
        <?php 
        $imgs = explode('|', $result->image_extra);
        ?>
        @foreach($imgs as $img)
            @if($img != '')
                <img src="{{ asset('/filemanager/userfiles/' . $img) }}">
            @endif
        @endforeach
        <p><strong>Thể loại:</strong> </p>
        <strong>Link app:</strong>
        <p><a href="{{ $result->link }}" target="_blank">{!! $result->link !!}</a></p>
        <strong>Mô tả:</strong>
        <p>{!! $result->intro !!}</p>
        <strong>Chi tiết:</strong>
        <p>{!! $result->content !!}</p>
    </div>
</body>
</html>

