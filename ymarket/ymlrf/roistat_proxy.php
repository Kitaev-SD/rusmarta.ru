<?php
$rs_channel = isset($_GET["rs_channel"]) ? $_GET["rs_channel"] : "";
$url = "http://cloud.roistat.com/proxy/market/67124/12/21377530/AQAAAAAaK-l-AAHFLLOh4rChNUxwp0jYamnyXMs/" . $rs_channel;
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