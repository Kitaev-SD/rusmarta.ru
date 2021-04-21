<?php
$rs_channel = array_key_exists("rs_channel", $_GET) ? $_GET["rs_channel"] : "";
$list_id = array_key_exists("list", $_GET) ? $_GET["list"] - 1 : 0;
$url = "http://cloud.roistat.com/proxy/market/67124/17/21475009/AQAAAAAkNVyLAAHFLEm78Pt89U3Vp1QW7JJteaU/{$rs_channel}?list={$list_id}";

if((int)ini_get("allow_url_fopen") === 1) {
    $result = file_get_contents($url);
} else {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $result = curl_exec($ch);
    curl_close($ch);
}

if ($result[0] === "<") {
    header("Content-type: application/xml");
} else {
    header("Content-type: text/html");
}
echo $result;