<?php

require_once('config.php');

define("API_KEY_SEC", "ELEARNING");

function encrypt_key($string)
{
    $secret_key = 'bgt_key';
    $secret_iv = 'bgt_secret_iv';
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));

    return $output;
}

//ThoLd
function decodeJWT($jwt)
{
    if (!$jwt) return '';

    $arrData = explode('.', $jwt);
    $count_arr = count($arrData);
    $payload = '';
    if ($count_arr > 0) {
        $base64UrlPayload = $arrData[1]; // check theo cấu trúc json web token
        // $base64UrlPayload = strtr($base64UrlPayload, '+', '/', '=', '-', '_', '');
        $payload = base64_decode($base64UrlPayload);
    }

    return $payload;
}
