<?php

$app_id = 230833;
$project = 'https://rusmarta.class365.ru';
$secret = '574tY3Jq3OhG6zhNRVGuPFv2XNn0DJly';
//$date = strtotime('2018-02-27');
$date = $_GET['date'] ?: strtotime('2018-01-27');

include 'class365.php';

$api = new class365($app_id, "", $project);
$api->setSecret($secret);
$response = $api->repair();
$token = $response['token'];
$api->setToken($token);




$statuses = [];

$tmp = $api->request('get','customerorderstatus',[]);
foreach($tmp['result'] as $row) {
	$statuses[] = [
		'id' => $row['id'],
		'name' => $row['name']
	];
}

$tmp = $api->request('get','dealstatus',[]);
foreach($tmp['result'] as $row) {
	$statuses[] = [
		'id' => $row['id'],
		'name' => $row['name']
	];
}



$orders = [];

$limit = 249;
$page = ceil($_GET['offset'] / $limit) ?: 1;

$tmp = $api->request('get','customerorders',[
	'with_additional_fields' => 1,
	'updated' => [
		'from' => date('d.m.Y',$date)
	],
	'page' => $page,
	'limit' => $limit
]);


foreach($tmp['result'] as $row) {


	$orders[] = [
		'id' => $row['id'],
		'name' => 'Заказ № ' . $row['number'],
		'date_create' => strtotime($row['date']),
		'status' => $row['status_id'],
		'price' => $row['sum'], 
		'client_id' => $row['partner_id'],
		'roistat' => $row['398944']
	];
}

//$fp = fopen('rsexport.txt','a+');
//fwrite($fp,'============ ' . date('Y-m-d H:i:s') . ' ============' . "\n");
//fwrite($fp,'GET:' . "\n");
//fwrite($fp, http_build_query($_GET) . "\n");
//fwrite($fp,'date: ' . $date . "\n");
//fwrite($fp,'page: ' . $page . "\n");
//fwrite($fp,'orders: ' . count($orders) . "\n");
//fwrite($fp, "\n\n");
//fclose($fp);

// if(!empty($_GET['offset'])) {
// 	sleep(40);
// }
$new_limit = $limit - count($tmp['result']);
$new_page = 1;

if($new_limit > 0) {
	$tmp = $api->request('get','deals',[
		'with_additional_fields' => 1,
		'updated' => [
			'from' => date('d.m.Y',$date)
		],
		'limit' => $new_limit,
		'page' => $new_page
	]);
	foreach($tmp['result'] as $row) {

		$orders[] = [
			'id' => $row['id'],
			'name' => $row['name'],
			'date_create' => strtotime($row['time_create']),
			'status' => $row['status_id'],
			'price' => $row['sum'],
			'client_id' => $row['partner_id'],
			'roistat' => $row['410996'],
			'fields' => [
				'marker' => $row['410998'],
				'form' => $row['411000']
			]
		];
	}
}




// exit;


$json = [
	'statuses' => $statuses,
	'orders' => $orders,
	'pagination' => [
		'total_count' => '99999',
		'limit' => $limit
	]
];



echo json_encode($json,JSON_UNESCAPED_UNICODE);
