<?php
namespace CiFrame;

header('Content-Type: text/html; charset=utf-8');
require_once __DIR__  . "/BusinessCrmAPI.php";
require_once __DIR__  . "/helpers.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

use CiFrame\Wb\API\WbAPI;
use CiFrame\Wb\API\WbOrder;
use CiFrame\Wb\API\WbLink;
use CiFrame\DB\DBWorker;

use BusinessCrmAPI;

// loadConfiguration();

global $connect, $config, $options, $systemOptions;

$app_id = '227555';
$secret = 'dBgCGS5FBUEGfNxbJLxRDrHRdLN0RvNd';
$address_class365 = 'https://rusmarta.class365.ru';

$class365api = new BusinessCrmAPI($app_id, "", $address_class365, true);
$class365api->setSecret($secret);
$class365_response = $class365api->repair();
$token = @$class365_response["token"];
$class365api->setToken($token);

if ($class365_response['status']!='ok')
{
    die("Запрос на получение токена с ошибкой. Ошибка: {$class365_response['status']}");
}


if (@$_GET['t'] == 'stores') {
    $params = array();
    $storesResponse = $class365api->request("get", "stores", $params)['result'];
    $business_stores = array();
    foreach ($storesResponse as $bstore) {
        $business_stores[$bstore['id']] = $bstore['name'];
    }
    d($business_stores);
	exit;
}

if (@$_GET['t'] == 'prices') {
    $params = array();
    $pricesResponse = $class365api->request("get", "salepricetypes", $params)['result'];
    $business_priceTypes = array();
    foreach ($pricesResponse as $bprice) {
        $business_priceTypes[$bprice['id']] = $bprice['name'];
    }
    d($business_priceTypes);
	exit;
}

if (@$_GET['t'] == 'employees') {
    $params = array();
    $resp = $class365api->request("get", "employees", $params)['result'];
	d($resp);
    exit;
}

if (@$_GET['t'] == 'statuses') {
    $params = array();
    $resp = $class365api->request("get", $config['entity_type'] == 'realizations' ? 'realizationstatus' : 'customerorderstatus', $params)['result'];
	d($resp);
    exit;
}

if (@$_GET['t'] == 'organizations') {
    $params = array();
    $resp = $class365api->request("get", 'organizations', $params)['result'];
	d($resp);
    exit;
}

if (@$_GET['t'] == 'partners' && $_GET['name']) {
	
	$params = array('name' => $_GET['name']);
	$resp = $class365api->request("get", 'partners', $params);
	$result = array();
	foreach ($resp['result'] as $item) {
		$result[$item['id']] = $item['name'];
	}
	d($result);
    exit;
}

$url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
echo "<pre><a href='$url?t=stores'>Stores</a></pre>";
echo "<pre><a href='$url?t=prices'>Prices</a></pre>";
echo "<pre><a href='$url?t=employees'>Employees</a></pre>";
echo "<pre><a href='$url?t=statuses'>Statuses</a></pre>";
echo "<pre><a href='$url?t=partners'>Partners (with param name)</a></pre>";
echo "<pre><a href='$url?t=organizations'>Organizations</a></pre>";