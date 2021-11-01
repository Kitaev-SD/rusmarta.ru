<?php

namespace CiFrame;

use BusinessCrmAPI;
use CiFrame\BeruAPI\BeruAPI;

header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Europe/Moscow');
ini_set("error_log", __DIR__ .  "/php-error.log");
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once __DIR__  . "/DBWorker.php";
require_once __DIR__  . "/BeruAPI.php";
require_once __DIR__  . "/BusinessCrmAPI.php";
require_once __DIR__  . "/helpers.php";

// 'host' => 'localhost',
// 'database' => 'www.rusmarta.ru',
// 'login' => 'root',
// 'password' => 'AQ6e0ga6R2Tn',

//$ym_campaign = '22114084';
$ym_campaign = '22451905';

$app_id = '227555';
$secret = 'dBgCGS5FBUEGfNxbJLxRDrHRdLN0RvNd';
$address_class365 = 'https://rusmarta.class365.ru';

$class365api = new BusinessCrmAPI($app_id, "", $address_class365, true);
$class365api->setSecret($secret);
$class365_response = $class365api->repair();
$token = $class365_response["token"];
$class365api->setToken($token);

if ($class365_response['status']!='ok')
{
    die("Запрос на получение токена с ошибкой. Ошибка: {$class365_response['status']}");
}

$db_settings = (include_once __DIR__ . '/../../bitrix/.settings.php')['connections']['value']['default'];
$db = mysqli_connect($db_settings['host'], $db_settings['login'], $db_settings['password'], $db_settings['database']);

if ($db == false) {
    exit();
}

mysqli_query($db, "SET NAMES 'utf8'");

$YM_INDEX = 1;
$page = 1;
$limit = 250;
$w_upd = null;

$client_id = @end(mysqli_fetch_all(mysqli_query($db, "select VALUE from yamarket_trading_settings where NAME='OAUTH_CLIENT_ID'"), MYSQLI_ASSOC))['VALUE'];
// $client_id = mysqli_fetch_all(mysqli_query($db, "select VALUE from yamarket_trading_settings where NAME='OAUTH_CLIENT_ID'"), MYSQLI_ASSOC); // ['VALUE']
// $client_id='1deb6169bf7e4cbe9dc434ea4b71265d';
$token = @end(mysqli_fetch_all(mysqli_query($db, "select ACCESS_TOKEN from yamarket_api_oauth2_token where CLIENT_ID='$client_id'")))[0];

$beruAPI = new BeruAPI($client_id, $token, $ym_campaign, 'business');

/////////////////////////////////////////////////////////////////////////////////////////////////// Статусы Б.РУ -> 1C BITRIX

$orderData = [
	'updated' => [ 'from' => date('d.m.Y H:i:s', strtotime('-5 minutes')) ],
	'partner_id' => '10366564' // Яндекс Маркет Rusmarta FBS
];

$orders = $class365api->request("get", "customerorders", $orderData);

foreach ($orders['result'] as $order) {
	
	$bx_order_id = $order['number']; //str_replace('RM', '', $order['number']);
	
	$ym_order = mysqli_fetch_all(mysqli_query($db, "select * from b_sale_order where ACCOUNT_NUMBER='$bx_order_id'"), MYSQLI_ASSOC)[0]; // Покупатель маркетплейса
	$ym_ord_id = str_replace('YAMARKET_2_', '', $ym_order['XML_ID']);
    $ym_ord_id = str_replace('_4', '', $ym_ord_id);
	
	if ($order['calc_order_ships'] > 0) {
		$ym_order = $beruAPI->get("/campaigns/" . $ym_campaign . "/orders/$ym_ord_id.json")['order'];
        packageOrder($beruAPI, $ym_ord_id, $ym_campaign, $ym_order['delivery']['shipments']);
        $resp = sendStatusToBeru($beruAPI, $ym_ord_id, $ym_campaign, "PROCESSING", "READY_TO_SHIP");
        d($resp);
    }
    
	if ($order['status_id'] == '567') { // Я.Маркет собран
        mysqli_query($db, "update b_sale_order set STATUS_ID='E' where ACCOUNT_NUMBER='$bx_order_id'");
        $resp = sendStatusToBeru($beruAPI, $ym_ord_id, $ym_campaign, "PROCESSING", "READY_TO_SHIP");
	    //d('change E');
	}
	elseif ($order['status_id'] == '565') { // Я.Маркет отправлен
		mysqli_query($db, "update b_sale_order set STATUS_ID='G' where ACCOUNT_NUMBER='$bx_order_id'");
        $resp = sendStatusToBeru($beruAPI, $ym_ord_id, $ym_campaign, "PROCESSING", "SHIPPED");
        //d('change G');
	}
	elseif ($order['status_id'] == '589') { // Я.Маркет Отменён
        mysqli_query($db, "update b_sale_order set STATUS_ID='C' where ACCOUNT_NUMBER='$bx_order_id'");
        $resp = sendStatusToBeru($beruAPI, $ym_ord_id, $ym_campaign, "CANCELLED", "SHOP_FAILED");
	    //d('change C');
	}
}
		
///////////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////////////////////////// Заказы 1C BITRIX|ЯМ -> Б.РУ

$date = date('Y-m-d 00:00:00');
$db_res = mysqli_fetch_all(mysqli_query($db, "select * from b_sale_order where USER_ID='79564' AND DATE_INSERT >= '$date'"), MYSQLI_ASSOC); // Покупатель маркетплейса
$db_orders=[];
foreach ($db_res as $ym_order) {
    $ym_ord_id = str_replace('YAMARKET_2_', '', $ym_order['XML_ID']);
    $ym_ord_id = str_replace('_4', '', $ym_ord_id);
    $db_orders[$ym_ord_id] = $ym_order;
}

$beruAPI = new BeruAPI($client_id, $token, $ym_campaign, 'business');
$response = $beruAPI->get("/campaigns/" . $ym_campaign . "/orders.json?fromDate=" . date('d-m-Y') . "&page=1");

foreach ($db_orders as $ym_ord_id => $db_order) {
	//d($db_order);
    $check = @mysqli_fetch_all(mysqli_query($db, "select id from ci_ym_orders where ym_id='$ym_ord_id'"), MYSQLI_ASSOC);

    if ($check == true) { echo 'check c'; continue; }

    $ym_orders=[];
    foreach ($response['orders'] as $order) {
        $ym_orders[$order['id']] = $order;
    }

    $order = array('db' => $db_orders[$ym_ord_id], 'ym' => @$ym_orders[$ym_ord_id]);

    if ($order['ym'] == false) { echo 'check 2 c ' . $ym_ord_id . '<br>'; continue; }
	
    $b_order = @$class365api->request("get", "customerorders", ['number' => 'RM' . $order['db']['ID'] ])['result'][0];
    d($b_order, $order['db']['ID']);
    //$printLabel = printLabel($beruAPI,$order['ym']['id'],$ym_campaign);

    if (isset($b_order['id'])) {
            switch (@end($order['ym']['items'])['partnerWarehouseId']) {
                case "df8be9eb-5f3c-412e-a8c2-9c56167f4db3": { $name_sklad ='Склад СПБ ПВЗ';  break; }
                case "707f02d5-9ccf-4cfe-a486-9c54c8f6c6b8": { $name_sklad ='Склад СПБ Экспресс';  break; }
                case "db2a3eaa-aca6-4a35-9f5f-e721dac2c703": { $name_sklad ='Склад МСК Экспресс';  break; }
                default: $name_sklad = @end($order['ym']['items'])['partnerWarehouseId'];
            }
            $orderData = array(
            'id' => $b_order['id'],
            // 'number' => $order['db']['ACCOUNT_NUMBER'],
            // 'author_employee_id' => '119692',
            // 'responsible_employee_id' => '75531', // Юрий Емельянов
            // 'organization_id' => '399730',
            // 'partner_id' => '9106237', // Яндекс.Маркет Rusmarta FBS
            // 'shipper_id' => '399730',
            // 'consignee_id' => '9106237', // Яндекс.Маркет Rusmarta FBS
            // 'status_id' => '567', // ЯП Обрабатывается
            //'comment' => 'Беру: ' . $order['ym']['id'],
            '384241' => @$order['ym']['shipments'][0]['shipmentDate'],
            '6762577' => $name_sklad,
            '6762576' => $order['ym']['id'],
            //'9115944' => 'https://rusmarta.ru/c_integr/1c_bitrix' . '/pdf/' .  $printLabel,
            '9115944' => 'https://rusmarta.ru/c_integr/1c_bitrix' . '/sticker.php?ord_id=' . $order['ym']['id'],
            'delivery_note' => $order['ym']['delivery']['serviceName'] . ' с ' . 
            @$order['ym']['delivery']['dates']['fromDate'] . ' ' . @$order['ym']['delivery']['dates']['fromTime'] .
            ' до ' . @$order['ym']['delivery']['dates']['toDate'] . ' ' . @$order['ym']['delivery']['dates']['toTime'],
            //'date' => date('Y-m-d H:i:s', strtotime($order['ym']['creationDate']))
        );

        $createOrderResponse = $class365api->request("put", "customerorders", $orderData);

        if ($createOrderResponse['result']['id'] && isset($order['ym']['shipments'][0]['shipmentDate'])) {
            mysqli_query($db, "insert into ci_ym_orders set ym_id='{$order['ym']['id']}'");
        }
    }

}

//RM96543
//art 1789
//code 1789
//id 8143893

//b_sale_order_props_value ORDER_ID = '96543';
// 

/* while ($pager < 50) {

    $response = $beruAPI->get("/campaigns/" . $config['YM_MARKETS'][$YM_INDEX]['API_BERU_CAMPAIGN_ID'] . "/orders.json?fromDate=" . date('d-m-Y', strtotime('-2 days')) . "&page=$pager");

    if ($response['orders']) foreach ($response['orders'] as $k=>$order) {

        if ($order['fake'] || $order['status'] == 'UNPAID') continue;

        $orderData        = array();
        $orderData['attributes'] = array();

        $orderObject = BeruOrder::getByBeruID($connect, $beruAPI, $order['id']);

        if ($orderObject->crm_id) { continue; }

        $orderObject->substatus     = isset($order['substatus']) ? $order['substatus'] : '';
        $orderObject->status        = $order['status'];
        $orderObject->ready_to_ship = true;

        echo "<p>Working on order ID: {$order['id']} status: $orderObject->status substatus: $orderObject->substatus </p>";

        $order_date = date('Y-m-d H:i:s', strtotime($order['creationDate']));
        $orderData = array(
            'author_employee_id' => '334336',
            'responsible_employee_id' => '75531', // Юрий Емельянов
            'organization_id' => '75535',
            'partner_id' => '2248357', // Яндекс.Маркет
            'shipper_id' => '75535',
            'consignee_id' => '2248357', // Яндекс.Маркет
            'status_id' => '329', // ЯП Обрабатывается
            'comment' => 'Беру: ' . $order['id'],
            'date' => $order_date
        );

        $createOrderResponse = $class365api->request("post", "realizations", $orderData);
        d($createOrderResponse, 'response');

        if ($createOrderResponse['result']['id']) {
            printLog('Новый заказ ' . $order['id']);
            $orderObject->crm_id = $createOrderResponse['result']['id'];

            foreach ($order['items'] as $i => $productOrderItem) {

                $productLink = BeruLink::getByShopSKU($connect, $beruAPI, $productOrderItem['offerId']);

                // if ($productLink->crm_id) {

                //     $params = [
                //         'realization_id' => $orderObject->crm_id,
                //         'good_id' => $productLink->crm_id,
                //         'amount' => $productOrderItem['count'],
                //         'price' => (float)$productOrderItem['price'] + (float)@$productOrderItem['subsidy']
                //     ];

                //     $jsonResponse = $class365api->request("post", "realizationgoods", $params);
                //     d($jsonResponse, 'params ' . $i);
                // }


                if ($productLink->crm_id) {
                    $params = [
                        'realization_id' => $orderObject->crm_id,
                        //'store_id' => $store_sync[$orderObject->store_id],
                        'amount' => $productOrderItem['count'],
                        'good_id' => $productLink->crm_id,
                        'price' =>(float)$productOrderItem['price'] + (float)@$productOrderItem['subsidy']
                    ];
                    if ($productLink->product_type == 'modification') {
                        $params['modification_id'] = $productLink->crm_modification_id;
                    }

                    $jsonResponse = $class365api->request("post", "realizationgoods", $params);
                    $orderObject->crm_position_id = $jsonResponse['result']['id'];
                    d($jsonResponse, 'params ' . $i);
                    if ($jsonResponse['status'] != 'ok') {
                        printLog('jsonResponse Error: ' . $jsonResponse);
                    }
                }
            }

            $orderObject->name = $order['id'];
            $orderObject->beru_id = $order['id'];
            $orderObject->app_client_id = $config['YM_MARKETS'][$YM_INDEX]['API_BERU_CAMPAIGN_ID'];
            $orderObject->updated = date('Y-m-d H:i:s');
            $orderObject->ready_to_ship = $orderObject->ready_to_ship ? '1' : '0';
            $orderObject->save();
        }
    }

    if ($response['pager']['currentPage'] < $response['pager']['pagesCount']) $pager++; else break;
} */


function printLabel($beruAPI,$order_id,$campaign_id) {
    $beruAPI->curlLoadLabel("/campaigns/$campaign_id/orders/$order_id/delivery/labels.json", __DIR__ . "/pdf/$order_id.pdf");
    return "$order_id.pdf";
}

function packageOrder($beruAPI, $order_id, $campaign_id, $shipments) {
    $shipment = $shipments[0];
    $boxes = array();
    $boxes[] = array(
        "fulfilmentId" => $order_id . '-1',
        "weight" => $shipment['weight'],
        "width" => $shipment['width'],
        "height" => $shipment['height'],
        "depth" => $shipment['depth'],
        "items" => $shipment['items']
    );
    return $beruAPI->put("/campaigns/$campaign_id/orders/$order_id/delivery/shipments/{$shipment['id']}/boxes.json", array('boxes' => $boxes));
}

function sendStatusToBeru($beruAPI, $order_id, $campaign_id, $status, $substatus) {
    return $beruAPI->put("/campaigns/$campaign_id/orders/$order_id/status.json", array('order' => array("status" => strtoupper($status), "substatus" => strtoupper($substatus))));
}

echo 'end.';