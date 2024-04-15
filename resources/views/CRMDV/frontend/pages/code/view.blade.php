<?php
try {
    $file_name = explode('//', $_GET['link'])[1];
    $file_name = str_replace('/', '', $file_name);
    $file_name = str_replace('.', '_', $file_name);
//    dd(base_path() . '/public_html/ldp-template/' . $file_name . '.html');
    if (file_exists(base_path() . '/public_html/ldp-template/' . $file_name . '.html')) {
        $html = file_get_contents(base_path() . '/public_html/ldp-template/' . $file_name . '.html');
        $html = str_replace('<head>', '<head><meta name="robots" content="NOINDEX, NOFOLLOW">', $html);
        print $html;

        exit();
    }


} catch (Exception $ex) {

}




$handle = curl_init($_GET['link']);
curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

/* Get the HTML or whatever is linked in $url. */
$response = curl_exec($handle);

/* Check for 404 (file not found). */
$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

if($httpCode == 404 || strpos($_SERVER['REQUEST_URI'], 'preview.pagedemo.me') !== false) {
    $file_name = explode('//', @$_GET['link'])[1];
    $file_name = str_replace('/', '', $file_name);
    $file_name = str_replace('.', '_', $file_name);
//                dd($file_name);
    if (file_exists(base_path() . '/public_html/ldp-template/' . $file_name . '.html')) {
    	require base_path() . '/public_html/ldp-template/' . $file_name . '.html';
    	exit();
    }
}
header("Location: ". $_GET['link']);
exit();
curl_close($handle);
?>