<?php
$file = file('roistat.order.php');
$ct   = count($file);

if ($ct > 2) {
    $app_id  = 230833;
    $project = 'https://rusmarta.class365.ru';
    $secret  = '574tY3Jq3OhG6zhNRVGuPFv2XNn0DJly';

    include 'class365.php';

    $api = new class365($app_id, "", $project);
    $api->setSecret($secret);
    $response = $api->repair();
    $token    = $response['token'];
    $api->setToken($token);

}

$fp = fopen('roistat.txt', 'a+');
fwrite($fp, '============ Update ORDER ============' . "\n");

for ($i = 2; $i < $ct; $i++) {

    $tmp = explode(';', $file[$i]);
    fwrite($fp, print_r($tmp, 1) . "\n");
    $order = $api->request('get', 'customerorders', [
        'number'                 => $tmp[0],
        'with_additional_fields' => 1
    ]);
    fwrite($fp, print_r($order, 1) . "\n");
    if (!empty($order['result']) && $order['result'][0]['id'] > 0) {
        $id = $order['result'][0]['id'];

        $order = $api->request('put', 'customerorders', [
            'id'     => $id,
            '398944' => $tmp[1]
        ]);
        fwrite($fp, print_r($order, 1) . "\n");
        if ($order['status'] == 'ok') {
            unset($file[$i]);
        }
    }
    fwrite($fp, "\n\n");
    fclose($fp);

    $fp = fopen('roistat.order.php', 'w');
    fwrite($fp, implode('', $file));
    fclose($fp);

    // if($order['result'][0])

    // fwrite($fp, print_r($order,1) . "\n");
    // fwrite($fp, "\n\n");
    // fclose($fp);

}



