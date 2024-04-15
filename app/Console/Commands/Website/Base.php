<?php
/**
 * Created by PhpStorm.
 * BillPayment: hoanghung
 * Date: 08/09/2016
 * Time: 19:52
 */

namespace App\Console\Commands\Website;

class Base
{

    public function __construct()
    {
        require_once base_path('app/Console/Commands/simple_html_dom.php');
        ini_set("user_agent", "Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0");
    }

    public function checkUrl404($url) {
        $handle = curl_init($url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($handle);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if($httpCode == 404) {
            return  true;
        }
        return false;
    }
}