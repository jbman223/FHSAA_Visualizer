<?php
//session_start();

function post($url, $data, $header)
{

    $fields_string = "";

    foreach ($data as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
    }
    rtrim($fields_string, '&');

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt( $ch, CURLOPT_COOKIESESSION, true );
    curl_setopt( $ch, CURLOPT_COOKIEJAR, "cookie.txt" );
    curl_setopt( $ch, CURLOPT_COOKIEFILE, "cookie.txt" );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);

    //var_dump($result);
    if ($result) {
        return $result;
    } else {
        return curl_error($ch);
    }


}

function postJSON($url, $data, $header)
{


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSLVERSION, 3);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    //var_dump($result);

    //var_dump($result);
    if ($result) {
        return $result;
    } else {
        return curl_error($ch);
    }
}

function get($url, $header)
{

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt( $ch, CURLOPT_COOKIESESSION, true );
    curl_setopt( $ch, CURLOPT_COOKIEJAR, "cookie.txt" );
    curl_setopt( $ch, CURLOPT_COOKIEFILE, "cookie.txt" );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    //var_dump($result);

    curl_close($ch);

    return $result;
}