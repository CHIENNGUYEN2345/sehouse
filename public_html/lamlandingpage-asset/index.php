<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$data = file_get_contents('https://service.hobasoft.com/api/v1/landingpages?url=' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$data = (array) json_decode($data);
if ($data['status'] == false) {
    echo $data['msg']; exit;
}
$data = (array) $data['data'];

// example of how to use basic selector to retrieve HTML contents
include('simple_html_dom.php');

// get DOM from URL or file
$html = file_get_html($data['ladi_link']);

//  Backup
if (!file_exists('bk.html') && time() > strtotime($data['updated_at']) + 604800) {
    file_put_contents('bk.html', @$html->find('html', 0)->innertext);
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo @$html->find('title', 0)->innertext;?></title>
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Expires" content="-1">
    <meta name="keywords" content="<?php echo @$html->find('title', 0)->innertext;?>">
    <meta name="description" content="<?php echo @$html->find('meta[name=description]', 0)->content;?>">
    <script id="script_viewport" type="text/javascript">window.ladi_viewport = function () {
            var width = window.outerWidth > 0 ? window.outerWidth : window.screen.width;
            var widthDevice = width;
            var is_desktop = width >= 768;
            var content = "";
            if (typeof window.ladi_is_desktop == "undefined" || window.ladi_is_desktop == undefined) {
                window.ladi_is_desktop = is_desktop;
            }
            if (!is_desktop) {
                widthDevice = 420;
            } else {
                widthDevice = 960;
            }
            content = "width=" + widthDevice + ", user-scalable=no";
            var scale = 1;
            if (!is_desktop && widthDevice != window.screen.width && window.screen.width > 0) {
                scale = window.screen.width / widthDevice;
            }
            if (scale != 1) {
                content += ", initial-scale=" + scale + ", minimum-scale=" + scale + ", maximum-scale=" + scale;
            }
            var docViewport = document.getElementById("viewport");
            if (!docViewport) {
                docViewport = document.createElement("meta");
                docViewport.setAttribute("id", "viewport");
                docViewport.setAttribute("name", "viewport");
                document.head.appendChild(docViewport);
            }
            docViewport.setAttribute("content", content);
        };
        window.ladi_viewport();</script>
    <meta id="viewport" name="viewport" content="width=420, user-scalable=no, initial-scale=0.7619047619047619, minimum-scale=0.7619047619047619, maximum-scale=0.7619047619047619">
    <meta property="og:url" content="https://<?php echo $_SERVER['SERVER_NAME'];?>">
    <meta property="og:title" content="<?php echo @$html->find('meta[property=og:title]', 0)->content;?>">
    <meta property="og:type" content="website">
    <meta property="og:image"
          content="<?php echo @$html->find('meta[property=og:image]', 0)->content;?>">
    <meta property="og:description" content="<?php echo @$html->find('meta[property=og:description]', 0)->content;?>">
    <meta name="format-detection" content="telephone=no">
    <link rel="shortcut icon" type="image/png"
          href="<?php echo @$html->find('meta[property=og:image]', 0)->content;?>">
    <link rel="dns-prefetch">
    <link rel="preconnect" href="https://fonts.googleapis.com/" crossorigin="">
    <link rel="preconnect" href="https://w.ladicdn.com/" crossorigin="">
    <link rel="preconnect" href="https://api.forms.ladipage.com/" crossorigin="">
    <link rel="preconnect" href="https://la.ladipage.com/" crossorigin="">
    <link rel="stylesheet" href="<?php echo @$html->find('link[rel=stylesheet]', 0)->href;?>" as="style" onload="this.onload = null;this.rel = 'stylesheet';">
    <style id="style_ladi" type="text/css">
        <?php
        $str = @$html->find('style[id=style_ladi]', 0)->innertext;
        echo str_replace('background-image:none!important', '', $str);
        ?>
    </style>
    <style id="style_page" type="text/css">
        <?php echo @$html->find('style[id=style_page]', 0)->innertext;?>
    </style>
    <style id="style_element" type="text/css">
        <?php echo @$html->find('style[id=style_element]', 0)->innertext;?>
    </style>

    <link href="//lamlandingpage.com/lamlandingpage-asset/custom.css?v=<?php echo time(); ?>" rel="stylesheet" type="text/css">

    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script>

    <?php
    echo $data['head_code'];
    ?>
</head>
<body>
<div class="ladi-wraper">
    <?php echo @$html->find('.ladi-wraper', 0)->innertext;?>
</div>
<div id="backdrop-popup" class="backdrop-popup"></div>
<div id="lightbox-screen" class="lightbox-screen"></div>
<script id="script_lazyload" type="text/javascript">
    <?php echo @$html->find('script[id=script_lazyload]', 0)->innertext;?>
</script>

<!--[if lt IE 9]>
<script src="https://w.ladicdn.com/v2/source/html5shiv.min.js?v=1576738683613"></script>
<script src="https://w.ladicdn.com/v2/source/respond.min.js?v=1576738683613"></script>
<![endif]-->

<link href="<?php echo @$html->find('link[rel=stylesheet]', 0)->href;?>" rel="stylesheet" type="text/css">
<link href="https://w.ladicdn.com/v2/source/ladipage.min.css?v=1576738683613" rel="stylesheet" type="text/css">

<script>
    var form_action = '<?php echo @$data['form_action'];?>';
    <?php
    $form_fields = (array)$data['form_fields'];
    ?>
    var form_name = '<?php echo @$form_fields['name'];?>';
    var form_phone = '<?php echo @$form_fields['phone'];?>';
    var form_address = '<?php echo @$form_fields['address'];?>';
    var form_message = '<?php echo @$form_fields['message'];?>';
    var form_email = '<?php echo @$form_fields['email'];?>';
    var form_quantity = '<?php echo @$form_fields['quantity'];?>';
    var form_color = '<?php echo @$form_fields['color'];?>';
    var form_size = '<?php echo @$form_fields['size'];?>';

</script>
<script src="//lamlandingpage.com/lamlandingpage-asset/lamlandingpage.js" type="text/javascript"></script>
<script id="script_event_data" type="text/javascript">
    <?php echo @$html->find('script[id=script_event_data]', 0)->innertext;?>
</script>

<script src="//lamlandingpage.com/lamlandingpage-asset/custom.js?v=<?php echo time(); ?>" type="text/javascript"></script>

<?php
echo @$data['body_code'];
?>
</body>
</html>
