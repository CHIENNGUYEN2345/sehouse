<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
try {
    if (!isset($data)) {
        try {
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );
            $data = file_get_contents('https://service.hobasoft.com/api/v1/landingpages?url=' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], false, stream_context_create($arrContextOptions));
        } catch (Exception $ex) {
            include_once 'bk.php';
            exit;
        }
        $data = (array)json_decode($data);
    }

    $data = (array)$data['data'];

// example of how to use basic selector to retrieve HTML contents
    include('simple_html_dom.php');

// get DOM from URL or file
    $html = file_get_html($data['ladi_link']);

    if (!$html || @$html->find('title', 0)->innertext == '404' || $html == '') {
        include_once 'bk.php';
        exit;
    }
    $body_html = $html->find('body', 0)->innertext;
} catch (Exception $ex) {
    include_once 'bk.php';
    exit;
}
?>
<html>
<head>
    <?php
    $str = str_replace('background-image:none!important', '', @$html->find('head', 0)->innertext);
    $str = str_replace($data['ladi_link'], 'https://' . $_SERVER['SERVER_NAME'], $str);
    echo str_replace('.js', '', $str);
    ?>

    <link href="//lamlandingpage.com/lamlandingpage-asset/custom.css?v=<?php echo time(); ?>" rel="stylesheet"
          type="text/css">

    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.1.min.js"></script>

    <?php
    echo $data['head_code'];
    ?>
</head>
<body>
<?php
echo '<span style="display: none;">file bk.php cập nhật lúc : ' . date('H:i:s d/m/Y', @filemtime('bk.php')) . '</span>';
?>
<?php echo str_replace('.js', '', @$body_html); ?>

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
    var form_field_1 = '<?php echo @$form_fields['field_1'];?>';
    var form_field_2 = '<?php echo @$form_fields['field_2'];?>';
    var form_field_3 = '<?php echo @$form_fields['field_3'];?>';
    var form_state = '<?php echo @$form_fields['state'];?>';
    var form_district = '<?php echo @$form_fields['district'];?>';
    var form_ward = '<?php echo @$form_fields['ward'];?>';
</script>
<script src="//lamlandingpage.com/lamlandingpage-asset/lamlandingpage.js?v=<?php echo time(); ?>"
        type="text/javascript"></script>


<script src="//lamlandingpage.com/lamlandingpage-asset/custom.js?v=<?php echo time(); ?>"
        type="text/javascript"></script>

<?php
echo @$data['body_code'];
?>

<?php
if (!isset($_GET['backup']) && @$html->find('title', 0)->innertext != '404'):
    ?>
    <script>

        $(document).ready(function () {
            $.ajax({
                url: 'landingpage-backup.php',
                type: 'GET',
                data: {
                    link: '<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?backup=true";?>'
                },
                success: function (resp) {
                    console.log(resp.msg);
                },
                error: function () {
                    console.log('Gọi ajax backup thất bại!');
                }
            });

        });
    </script>
<?php
endif;
?>
</body>
</html>
