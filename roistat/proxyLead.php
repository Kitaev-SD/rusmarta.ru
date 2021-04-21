<?php

const ROISTAT_LOGIN    = 'minin@rusmarta.ru';
const ROISTAT_PASSWORD = 'rusmarta';

if(!isset($_REQUEST['token']) || md5(ROISTAT_LOGIN.ROISTAT_PASSWORD) != $_REQUEST['token']) {
    die('Invalid token');
}
if (empty($_REQUEST['phone']) && empty($_REQUEST['email'])) {
	die('Bad request');
}

require_once __DIR__ . '/ProxyLeadToBusinessRuHandler.php';

$authData = [
	'appId' => 230833,
	'apiPath' => 'https://rusmarta.class365.ru',
];
$handler = new ProxyLeadToBusinessRuHandler($authData, $_REQUEST);
$leadId = $handler->setOrder();

if (!empty($leadId)) {
	$response = json_encode(['status' => 'ok', 'order_id' => $leadId]);
} else {
	$response = json_encode(['status' => 'error', 'message' => 'Не удалось создать лид']);
}

echo $response;
