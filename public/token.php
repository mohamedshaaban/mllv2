<?php



$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://identity.xero.com/connect/token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_POSTFIELDS => array('grant_type' => 'refresh_token','refresh_token' => 'd540d5b162f19f4b5e8f0ba64d00297b1eaed49272b0083530ca17a14223070d','client_id' => '8920CEA30F9D43AAA1041890507ED411','client_secret' => 'Pw3-sZIpCmtqPKjf8vgH88xtgcLSu1lvO3g1qM9ngoUEbeOO'),
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "postman-token: b54abdc0-17be-f38f-9aba-dbf8f007de99",
    ),


));

$response = curl_exec($curl);
$res = json_decode($response, true);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    var_dump($res['access_token']);
    var_dump($res['refresh_token']);
 }
