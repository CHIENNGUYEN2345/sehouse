<?php
if (strpos($code->link, '.translate.goog') !== false) {
    //  nếu là mẫu nước ngoài thì mở trực tiếp link đó
    header("Location: " . $code->link);
    die();
}

    if (strpos($code->link, 'https://') === false && @$_SERVER['HTTPS'] == 'on') {
        //  nếu mẫu ko có https thì chuyển hướng link demo sang http
        header("Location: http://".env('DOMAIN')."/mau-web/" . $code->id);
        die();
    }
    ?>
<!DOCTYPE html>
<html style="height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <meta name="google-site-verification" content="0xA-3pP2vnyHSV0fyrp5-WNGcbp8Pzapsk6i7ET43Jo" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if(!isset($_GET['hb']))
    <link rel="shortcut icon" href="https://hbweb.vn/wp-content/uploads/2022/09/favico-200x200-1.png"
          type="image/x-icon"/>
    @endif

    <title>{{ $code->owned != 'server mình' ? 'Mẫu giao diện:' : '' }} {{ $code->name }} @if(!isset($_GET['hb'])) - HBWEB @endif</title>
    <meta name="robots" content="NOINDEX, NOFOLLOW">
    <script type="text/javascript" src="https://hbweb.vn/wp-content/themes/generatepress_child/cuongdc//asset/demo/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="https://hbweb.vn/wp-content/themes/generatepress_child/cuongdc//asset/demo/bootstrap.min.js"></script>
    <link href="https://hbweb.vn/wp-content/themes/generatepress_child/cuongdc//asset/demo/demopage.css"
          data-minify="1" rel="stylesheet">
    <link rel='stylesheet' id='bfa-font-awesome-css'
          href='//cdn.jsdelivr.net/fontawesome/4.7.0/css/font-awesome.min.css?ver=4.7.0' type='text/css' media='all'/>

    @if(!isset($_GET['hb']))
    <meta name="description" content="HBWEB - chuyên thiết kế website, quảng cáo & xây dựng các kênh bán hàng hiệu quả"/>
    <meta property="og:title" content="Mẫu giao diện: {{ $code->name }} - HBWEB" />
    <meta property="og:description" content="HBWEB - chuyên thiết kế website, quảng cáo & xây dựng các kênh bán hàng hiệu quả" />
    <meta property="og:url" content="https://hbweb.vn/" />
    <meta property="og:site_name" content="HBWEB" />
    <meta name="twitter:title" content="Mẫu giao diện: {{ $code->name }} - HBWEB" />
    <meta name="twitter:description" content="HBWEB - chuyên thiết kế website, quảng cáo & xây dựng các kênh bán hàng hiệu quả" />
    @endif

    <meta property="og:locale" content="vi_VN" />
    <meta property="og:type" content="website" />
    
    
    <meta property="og:image" content="{{ asset('filemanager/userfiles/' . $code->image) }}" />
    <meta property="og:image:secure_url" content="{{ asset('filemanager/userfiles/' . $code->image) }}" />
    <meta property="og:image:width" content="1024" />
    <meta property="og:image:height" content="684" />
    <meta name="twitter:card" content="summary_large_image" />
    
    
    <meta name="twitter:image" content="{{ asset('filemanager/userfiles/' . $code->image) }}" />
    @if(!isset($_GET['hb']))
    <script type='application/ld+json' class='yoast-schema-graph yoast-schema-graph--main'>{"@context":"https://schema.org","@graph":[{"@type":"Organization","@id":"https://hbweb.vn/#organization","name":"HBWEB","url":"https://hbweb.vn/","sameAs":[],"logo":{"@type":"ImageObject","@id":"https://hbweb.vn/#logo","url":"https://hbweb.vn/wp-content/uploads/2018/10/LOGO-HOBA-SOFT-svg.svg","caption":"HBWEB"},"image":{"@id":"https://hbweb.vn/#logo"}},{"@type":"WebSite","@id":"https://hbweb.vn/#website","url":"https://hbweb.vn/","name":"HBWEB","publisher":{"@id":"https://hbweb.vn/#organization"},"potentialAction":{"@type":"SearchAction","target":"https://hbweb.vn/?s={search_term_string}","query-input":"required name=search_term_string"}},{"@type":"WebPage","@id":"https://hbweb.vn/#webpage","url":"https://hbweb.vn/","inLanguage":"vi","name":"Thi\u1ebft k\u1ebf website, app mobile chuy\u00ean nghi\u1ec7p, uy t\u00edn - HBWEB","isPartOf":{"@id":"https://hbweb.vn/#website"},"about":{"@id":"https://hbweb.vn/#organization"},"image":{"@type":"ImageObject","@id":"https://hbweb.vn/#primaryimage","url":"/wp-content/uploads/2018/10/insa-web.png"},"primaryImageOfPage":{"@id":"https://hbweb.vn/#primaryimage"},"datePublished":"2017-05-16T21:27:14+00:00","dateModified":"2020-01-03T05:04:06+00:00","description":"C\u00f4ng ty HBWEB chuy\u00ean cung c\u1ea5p c\u00e1c d\u1ecbch v\u1ee5 thi\u1ebft k\u1ebf website (landingpage, tin t\u1ee9c, b\u00e1n h\u00e0ng, gi\u1edbi thi\u1ec7u c\u00f4ng ty) - App mobile - Ph\u1ea7n m\u1ec1m qu\u1ea3n l\u00fd"}]}</script>
    <!-- / Yoast SEO plugin. -->
    @endif

    <style type="text/css">
        @media(min-width: 768px) {
            .logo {
                margin-right: 10px;
            }
        }
    </style>
</head>
<body>
@if(!isset($_GET['hb']))
<form action="" method="post">
    <div id="header" style="    position: fixed;
    bottom: 0;
    top: auto;">
   
        <div class="template_id">
            <a href="https://hbweb.vn/" target="_blank" class="logo"><img style="max-height: 25px;" src="https://hbweb.vn/wp-content/uploads/logo_hbweb_188x50.png"></a>
            <a target="_blank">{{ $code->owned != 'server mình' ? 'Mẫu giao diện' : 'Website' }}: {{ $code->name }}</a>
        </div>

        <div class="devices">
            <ul class="responsive">
                <li class="computer active"><a title="Hiển thị trên máy tính, laptop" class="pc selected" id="pc">PC,Laptop</a>
                </li>
                <li class="tablet"><a title="Hiển thị trên máy tính bảng ngang" class="tablet_landscape" id="ipadh">Tablet
                        Landscape</a></li>
                <li class="tablet_portrait"><a title="Hiển thị trên máy tính bảng đứng" class="tablet_portrait"
                                               id="ipadv2">Tablet Portrait</a></li>
                <li class="mobile"><a title="Hiển thị điện thoại ngang" class="phone_landscape" id="iphoneh">Phone
                        Landscape</a></li>
                <li class="mobile-portrait"><a title="Hiển thị trên điện thoại đứng" class="phone_portrait"
                                               id="iphonev">Phone Portrait</a></li>
            </ul>
        </div>
        <div class="template_id">
            <a target="_blank">@if($code->owned != 'server mình') Liên hệ hotline mua web: 0877783626 @endif</a>
        </div>
        
        <!--		<span class="trigger"><em></em></span>-->
    </div>
</form>
@endif
<div id="iframelive" style="height: 750px;">
    <div id="frameWrapper">
        <div class="computer" id="frameBody">
            @if(isset($_GET['hb']))
                <div class="hb" style="width: 100%;
    display: inline-block;
    height: 55px;
    position: fixed;
    background: #fff;
    bottom: 0;
    z-index: 999999999999;"></div>
            @endif
            <iframe src="{{ $code->link }}" width="100%" height="100%" frameborder="0" name="frame"
                    id="frame" style="width: 100%; height: 100%; display: inline-block;"> [Trình duyệt của bạn quá cũ
                không thể xem được mẫu, vui lòng bấm vào đây để xem mẫu website trực tiếp]
            </iframe>
        </div>
    </div>
</div>

<form action="" id="orderForm" method="post"></form>

<style>
    #logo {
        min-width: 137px;
        height: auto;
        margin: 0px;
        margin-top: 5px;
    }

    #header {
        background: #ccc;
        box-shadow: 0 1px #FFFFFF inset, 0 1px 3px rgba(34, 25, 25, 0.4);
    }

    #header .template_id a {
        color: #333;
    }

    .modal-header .close {
        margin-top: -11px;
    }

    #header .template_id span {
        margin-left: 5px;
    }
</style>
<script type="text/javascript">


    //end preload
    function calcHeight() {
        var headerDimensions = $('#header').height();
        if ($('#header').hasClass('stick_head')) {
            $('#iframelive').height($(window).height());
        } else {
            $('#iframelive').height($(window).height());
        }

    }

    $("#header span.trigger").click(function (e) {
        $('#frame').attr('src', function (i, val) {
            return val;
        });
        $(this).toggleClass("trigger-up");
        $("#header").toggleClass("stick_head");
        calcHeight();
    });

    $(window).resize(function () {
        calcHeight();
    });

    $(window).load(function () {
        calcHeight();
    });

    jQuery('.responsive a').click(function () {
        jQuery('.responsive li').removeClass('active');
        jQuery(this).parent('li').addClass('active');

        if (jQuery(this).parent('li').hasClass('computer')) {
            jQuery('#frame').css({"width": "100%"});
            jQuery('#frame').css({"height": jQuery(window).height()});
            jQuery('#frame').animate({"width": "100%"});
            jQuery('#frame').animate({"width": "100%"});
            jQuery('#frameBody').removeClass('ipadh');

            jQuery('body').removeClass('srcoll');

            jQuery('#frameBody').removeClass('iphoneh');
            jQuery('#frameBody').removeClass('iphonev');
            jQuery('#frameBody').removeClass('ipad-v');
            jQuery('#frameBody').addClass('computer');

        } else if (jQuery(this).parent('li').hasClass('tablet')) {
            jQuery('body').addClass('srcoll');
            jQuery('#frame').css({"width": 1024});
            jQuery('#frame').css({"height": 768});
            jQuery('#frame').animate({"width": 1024});
            jQuery('#frameBody').removeClass('iphoneh');
            jQuery('#frameBody').removeClass('iphonev');
            jQuery('#frameBody').removeClass('computer');
            jQuery('#frameBody').removeClass('ipad-v');
            jQuery('#frameBody').addClass('ipadh');
        } else if (jQuery(this).parent('li').hasClass('tablet_portrait')) {
            jQuery('body').addClass('srcoll');
            jQuery('#frame').css({"width": 768});
            jQuery('#frame').css({"height": 1024});
            jQuery('#frame').animate({"width": 768});
            jQuery('#frameBody').removeClass('iphoneh');
            jQuery('#frameBody').removeClass('iphonev');
            jQuery('#frameBody').removeClass('computer');
            jQuery('#frameBody').removeClass('ipadh');
            jQuery('#frameBody').addClass('ipad-v');
        } else if (jQuery(this).parent('li').hasClass('mobile')) {
            jQuery('body').addClass('srcoll');
            jQuery('#frame').css({"width": 568});
            jQuery('#frame').css({"height": 320});
            jQuery('#frame').animate({"width": 568});
            jQuery('#frameBody').removeClass('iphonev');
            jQuery('#frameBody').removeClass('ipadh');
            jQuery('#frameBody').addClass('iphoneh');
            jQuery('#frameBody').removeClass('ipad-v');
            jQuery('#frameBody').removeClass('computer');

        } else if (jQuery(this).parent('li').hasClass('mobile-portrait')) {
            jQuery('body').addClass('srcoll');
            jQuery('#frame').css({"width": 320});
            jQuery('#frame').css({"height": 568});
            jQuery('#frame').animate({"width": 320});
            jQuery('#frameBody').addClass('iphonev');
            jQuery('#frameBody').removeClass('iphoneh');
            jQuery('#frameBody').removeClass('ipad-v');
            jQuery('#frameBody').removeClass('computer');
        }

        $('#frame').attr('src', function (i, val) {
            return val;
        });

        return false;

    });


    $(document).ready(function () {

        $("#tmpFrame").on("load", function () {
            console.log('iframe loaded ');
            doReplaceHtml();
        });

        calcHeight();


        $('.link-demo').click(function () {
            var $this = $(this);
            $this.siblings().removeClass('selected');
            $this.addClass('selected');
            var code = $this.attr('data-code');
            $('#TemplateCode').val(code);
            $('#frameBody').html('<iframe width="100%" height="90%" frameborder="0" name="frame" id="frame">[Your user agent does not support frames or is currently configured not to display frames]</iframe>');
            $('#templateCode').text(code);
        });

        $("#ServiceName").on('keyup blur', function () {
            var val = $(this).val();
            var $error = $('#errorDomain');
            if (val !== "") {
                $error.hide();
            } else {
                $error.show();
            }
        });
    });
</script>

<style>

    .wpcf7 label {
        width: 100%;
    }

    .wpcf7 input[type="text"],
    .wpcf7 input[type="email"],
    .wpcf7 input[type="tel"],
    textarea {
        font-size: 16px;
        background-color: #f5f5f5;
        border: none;
        color: #000;
        width: 100%;
        padding: 2%;
    }


    .wpcf7 input[type=submit] {
        cursor: pointer;
        width: 100%;
        border: none;
        background: rgba(232, 89, 36, 0.8);
        color: #FFF;
        margin: 0 0 5px;
        padding: 10px;
        font-size: 15px;
        margin-top: 20px;
    }

    .wpcf7 input[type=submit]:hover {
        background: #E85924;
        -webkit-transition: background 0.3s ease-in-out;
        -moz-transition: background 0.3s ease-in-out;
        transition: background-color 0.3s ease-in-out;
    }

    .wpcf7-response-output {
        border: none !important;
        text-align: center;
        font-size: 15px;
        color: #E85924;
        font-weight: bold;
    }

    .modal-footer, .modal-header {
        border: none;
        margin: 0;
        padding: 10px;
    }

    .modal-content {
        border-radius: 0;
        padding: 10px;
    }

    /*css contact form */


    .form-store {
        width: 100%;
    }

    .form-store .form-title {
        width: 97%;
        background: #e26c14;
        height: 40px;
        line-height: 40px;
        padding-left: 1em;
        color: #fff;
    }

    .form-store .form-content {
        padding: 1em;
    }

    .form-store .form-content .form-web {
        width: 100%;
        margin: 20px 0 20px 0;
        color: #ff3333;
    }

    .form-store .form-content i.icon-form. {
        color: #ff3333;
        width: 6%;
        height: 30px;
        line-height: 30px;
        border: 2px solid #ddd;
        text-align: center;
    }

    .form-store .form-content p {
        padding-bottom: 0px !important;
        margin-bottom: 0px !important;
        font-size: 15px;
    }

    .form-store .form-left {
        width: 49%;
        margin: 10px 0 10px 0;
        float: left;
    }

    .form-store .form-right {
        width: 48%;
        float: left;
        margin-left: 10px;
        margin: 10px 0 10px 10px;
    }

    .form-store .form-left label, .form-store .form-right label, .form-store .form-text label {
        font-size: 15px;
        color: #000;
        font-weight: 700;
        margin: 5px 0 5px 0;
    }

    .form-store input.wpcf7-form-control.wpcf7-text.wpcf7-validates-as-required.txt_username {
        border: 1px solid #ddd;
        height: 40px;
        border-radius: 10px;
        margin: 10px 0 10px 0;
        color: #000;
    }

    .form-store input.wpcf7-form-control.wpcf7-text.wpcf7-validates-as-required.txt_phone {
        border: 1px solid #ddd;
        height: 40px;
        border-radius: 10px;
        margin: 10px 0 10px 0;
        color: #000;
    }

    .form-store input.wpcf7-form-control.wpcf7-text.wpcf7-email.wpcf7-validates-as-required.wpcf7-validates-as-email.txt_email {
        border: 1px solid #ddd;
        height: 40px;
        border-radius: 10px;
        margin: 10px 0 10px 0;
        width: 100%;
        color: #000;
    }

    .form-store textarea.wpcf7-form-control.wpcf7-textarea.wpcf7-validates-as-required.txt_ghichu {
        border: 1px solid #ddd;
        height: 60px;
        border-radius: 10px;
        margin: 10px 0 10px 0;
        width: 100%;
        color: #000;
    }

    .form-store .form-text {
        width: 100%;
    }

    .form-store input.wpcf7-form-control.wpcf7-submit.btn-store {
        background: #fff;
        color: #000;
        border: 1px solid #ddd;
    }
</style>


<script type='text/javascript'>
    /* <![CDATA[ */
    var wpcf7 = {
        "apiSettings": {
            "root": "https:\/\/hbweb.vn\/wp-json\/contact-form-7\/v1",
            "namespace": "contact-form-7\/v1"
        },
        "recaptcha": {"messages": {"empty": "H\u00e3y x\u00e1c nh\u1eadn r\u1eb1ng b\u1ea1n kh\u00f4ng ph\u1ea3i l\u00e0 robot."}},
        "cached": "1"
    };
    /* ]]> */
</script>
<script type='text/javascript'
        src='https://hbweb.vn/wp-content/plugins/contact-form-7/includes/js/scripts.js?ver=5.0.5'></script>

<div id="callNowBtn" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!--				shortcode form go here-->


                <div role="form" class="wpcf7" id="wpcf7-f1448-o1" lang="vi" dir="ltr">
                    <div class="screen-reader-response"></div>
                    <form action="/demo?id=4117#wpcf7-f1448-o1" method="post" class="wpcf7-form"
                          novalidate="novalidate">
                        <div style="display: none;">
                            <input type="hidden" name="_wpcf7" value="1448"/>
                            <input type="hidden" name="_wpcf7_version" value="5.1.1"/>
                            <input type="hidden" name="_wpcf7_locale" value="vi"/>
                            <input type="hidden" name="_wpcf7_unit_tag" value="wpcf7-f1448-o1"/>
                            <input type="hidden" name="_wpcf7_container_post" value="0"/>
                            <input type="hidden" name="g-recaptcha-response" value=""/>
                        </div>
                        <div class="form-store">
                            <div class="form-title">Đặt mua giao diện website</div>
                            <div class="form-content">
                                <div class="form-web"><i class="icon-form fa fa-files-o"></i> <span
                                        class="tieudemau"></span></div>
                                <p> Bạn vui lòng điền đầy đủ thông tin của bạn theo mẫu bên dưới.</p>
                                <p>Nhân viên của chúng tôi sẽ liên hệ trực tiếp với bạn để xác nhận và tiến hành cài
                                    đặt</p>
                                <p>website theo mong muốn của bạn.</p>
                                <div class="form-left">
                                    <label><i class="fa fa-user-o"></i> Họ & tên</label><br/>
                                    <span class="wpcf7-form-control-wrap username"><input type="text" name="username"
                                                                                          value="" size="40"
                                                                                          class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required txt_username"
                                                                                          aria-required="true"
                                                                                          aria-invalid="false"/></span>
                                </div>
                                <div class="form-right">
                                    <label><i class="fa fa-phone"></i> Điện thoại</label><br/>
                                    <span class="wpcf7-form-control-wrap phone"><input type="text" name="phone" value=""
                                                                                       size="40"
                                                                                       class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required txt_phone"
                                                                                       aria-required="true"
                                                                                       aria-invalid="false"/></span>
                                </div>
                                <div class="form-text">
                                    <label><i class="fa fa-phone"></i> Email</label><br/>
                                    <span class="wpcf7-form-control-wrap email"><input type="email" name="email"
                                                                                       value="" size="40"
                                                                                       class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email txt_email"
                                                                                       aria-required="true"
                                                                                       aria-invalid="false"/></span>
                                </div>
                                <div class="form-text">
                                    <label><i class="fa fa-pencil-square-o"></i> Ghi chú</label><br/>
                                    <span class="wpcf7-form-control-wrap ghichu"><textarea name="ghichu" cols="40"
                                                                                           rows="10"
                                                                                           class="wpcf7-form-control wpcf7-textarea txt_ghichu"
                                                                                           aria-invalid="false"></textarea></span>
                                </div>
                                <div id="mau-dat-hang">
                                    <input type="hidden" name="mau-dat-hang" value=""
                                           class="wpcf7-form-control wpcf7-hidden"/></div>
                                <div id="link-mau-dat-hang">
                                    <input type="hidden" name="link-mau-dat-hang" value=""
                                           class="wpcf7-form-control wpcf7-hidden"/></div>
                                <div class="form-btn">
                                    <input type="submit" value="Đặt ngay"
                                           class="wpcf7-form-control wpcf7-submit btn-store"/>
                                </div>
                                </p></div>
                        </div>
                        <div class="wpcf7-response-output wpcf7-display-none"></div>
                    </form>
                </div>
            </div>
            <div class="modal-footer"><span>(*) chúng tôi sẽ liên hệ lại với bạn trong vòng 2 giờ</span></div>
        </div>
    </div>
</div>



<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116118238-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-116118238-1');
</script>

</body>
</html>