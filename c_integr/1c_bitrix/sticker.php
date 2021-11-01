<?php

namespace CiFrame;

use BusinessCrmAPI;
use CiFrame\BeruAPI\BeruAPI;

ini_set("error_log", __DIR__ .  "/php-error.log");
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once __DIR__  . "/DBWorker.php";
require_once __DIR__  . "/BeruAPI.php";

$ym_campaign = '22451905';

$ord_id = $_GET['ord_id'];

$db_settings = (include_once __DIR__ . '/../../bitrix/.settings.php')['connections']['value']['default'];
$db = mysqli_connect($db_settings['host'], $db_settings['login'], $db_settings['password'], $db_settings['database']);

if ($db == false) {
    exit();
}

mysqli_query($db, "SET NAMES 'utf8'");

$client_id = @end(mysqli_fetch_all(mysqli_query($db, "select VALUE from yamarket_trading_settings where NAME='OAUTH_CLIENT_ID'"), MYSQLI_ASSOC))['VALUE'];
$token = @end(mysqli_fetch_all(mysqli_query($db, "select ACCESS_TOKEN from yamarket_api_oauth2_token where CLIENT_ID='$client_id'")))[0];

if ($token == false || $client_id == false) {
    die('no find token');
}

$beruAPI = new BeruAPI($client_id, $token, $ym_campaign, 'business');

$printLabel = printLabel($beruAPI,$ord_id,$ym_campaign);

header("Location: https://rusmarta.ru/c_integr/1c_bitrix/pdf/" . $printLabel);

function printLabel($beruAPI,$order_id,$campaign_id) {
    $beruAPI->curlLoadLabel("/campaigns/$campaign_id/orders/$order_id/delivery/labels.json", __DIR__ . "/pdf/$order_id.pdf");
    print_r($beruAPI);
    return "$order_id.pdf";
}