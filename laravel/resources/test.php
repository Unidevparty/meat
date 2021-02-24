<?php 
header ("Content-Type:text/html; charset=UTF-8");
ini_set ('display_errors', 1); 
error_reporting (E_ALL); 
 
$url = "http://www.google.ru";
 
$aOptionsCURL = array (
                CURLOPT_URL            => $url,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                //CURLOPT_POSTFIELDS     => $request,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                );
$sOptionsSessionCURL = curl_init();
curl_setopt_array ($sOptionsSessionCURL, $aOptionsCURL);
 
$sResultHTML = curl_exec($sOptionsSessionCURL);
if (curl_errno ($sOptionsSessionCURL)) { 
    echo '<br><br>cURL Error: ' . curl_error ($sOptionsSessionCURL);
};
print_r ($sResultHTML);
?>