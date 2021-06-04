<?php
header('Access-Control-Allow-Origin: *');
// /rusmarta.ru/bitrix/components/altop/forms/script.php
// /rusmarta.ru/bitrix/components/bitrix/suscribe.form/component.php

// $app_id = 230833;
// $project = 'https://rusmarta.class365.ru';
// $secret = '574tY3Jq3OhG6zhNRVGuPFv2XNn0DJly';

// include 'class365.php';

// $api = new class365($app_id, "", $project);
// $api->setSecret($secret);
// $response = $api->repair();
// $token = $response['token'];
// $api->setToken($token);
// $tmp = $api->request('get','organizations',[
// 'with_additional_fields' => 1
// ]);
// print_r($tmp);exit;

if ($_GET['key'] == 'ODkzODM6NjcxMjQ6MThlOThkOGE0YTFlMDY3ZWRjNDFhMDgzOTlkN2IzZGU=') {
    $fp = fopen('roistat.txt', 'a+');
    fwrite($fp, '============ ' . date('Y-m-d H:i:s') . ' ============' . "\n");
    fwrite($fp, 'POST:' . "\n");
    fwrite($fp, print_r($_POST, 1) . "\n");
    fwrite($fp, 'GET:' . "\n");
    fwrite($fp, print_r($_GET, 1) . "\n");
    fwrite($fp, 'Body:' . "\n");
    // fwrite($fp, print_r($body, 1) . "\n"); не работает
    fwrite($fp, "\n\n");
    fclose($fp);

    $app_id  = 230833;
    $project = 'https://rusmarta.class365.ru';
    $secret  = '574tY3Jq3OhG6zhNRVGuPFv2XNn0DJly';

    include 'class365.php';

    $api = new class365($app_id, "", $project);
    $api->setSecret($secret);
    $response = $api->repair();
    $token    = $response['token'];
    $api->setToken($token);

    $_GET['phone'] = strtr($_GET['phone'], array(
        '(' => '',
        ')' => '',
        ' ' => '',
        '+' => '',
        '-' => ''
    ));

    // 1 - phone
    // 3 - email
    if (!empty($_GET['phone'])) {
        $tmp = $api->request('get', 'partnercontactinfo', [
            'contact_info_type_id' => 1,
            'contact_info'         => $_GET['phone']
        ]);
    }
    if (!empty($_GET['email']) && empty($tmp['result'])) {
        $tmp = $api->request('get', 'partnercontactinfo', [
            // 'contact_info_type_id' => 3,
            'contact_info_type_id' => 4,
            'contact_info'         => $_GET['email']
        ]);
    }

    $fp = fopen('roistat.txt', 'a+');
    fwrite($fp, '============ Search Agent ============' . "\n");
    fwrite($fp, print_r($tmp, 1) . "\n");
    fwrite($fp, "\n\n");
    fclose($fp);

    $agent = $tmp['result'] ? $tmp['result'][0]['partner_id'] : false;
    //8833709 - Не назначен

    if (empty($_GET['fields']['order'])) {
        if (!$agent) {
            $name = (isset($_GET['name']) && !empty($_GET['name'])) ? $_GET['name'] : 'Неизвестный контакт';

            /*
            $agent = $api->request('post', 'partners', [
                'name' => $name
            ]);
            */

            $FIX_getApi  = true;
            $FIX_counter = 10;
            while (($FIX_getApi && $FIX_counter >= 0)) {
                $agent = $api->request('post', 'partners', [
                    'name' => $name,
                ]);

                // ...

                if ($agent['status'] == 'ok') {
                    $FIX_getApi = false;
                } else {
                    // Ожидание отключения ограничния по API
                    sleep(10);
                    $FIX_counter--;

                    // New token
                    $newToken = $api->repair()['token'];
                    $api->setToken($newToken);
                }
            }

            $agent = $agent['result']['id'];

            if ($agent['status'] != 'error' && !empty($_GET['phone'])) {
                $api->request('post', 'partnercontactinfo', [
                    'partner_id'           => $agent,
                    'contact_info_type_id' => 1,
                    'contact_info'         => $_GET['phone']
                ]);
            }

            if ($agent['status'] != 'error' && !empty($_GET['email'])) {
                $api->request('post', 'partnercontactinfo', [
                    'partner_id'           => $agent,
                    // 'contact_info_type_id' => 3,
                    'contact_info_type_id' => 4,
                    'contact_info'         => $_GET['email']
                ]);
            }
        }

        $fp = fopen('roistat.txt', 'a+');
        fwrite($fp, '============ Agent ============' . "\n");
        fwrite($fp, print_r($agent, 1) . "\n");
        fwrite($fp, "\n\n");
        fclose($fp);

        if (empty($_GET['livetex'])) {
            $fp = fopen('roistat.txt', 'a+');

            if ($_GET['fields']['form'] != 'Заказать товар') {
                fwrite($fp, '============ Lead ============' . "\n");

                $leadFields = [
                    'name'                    => 'Заявка с "' . $_GET['fields']['form'] . '"',
                    'description'             => $_GET['comment'],
                    'author_employee_id'      => 8833709,
                    'responsible_employee_id' => 8833709,
                    'partner_id'              => $agent,
                    'organization_id'         => 8738191,
                    '410996'                  => $_GET['roistat'],
                    '410998'                  => $_GET['fields']['marker'],
                    '411000'                  => $_GET['fields']['form']
                ];

                if ($_GET['fields']['form'] == 'Подписаться') {
                    $leadFields['status_id'] = 381;
                }

                $lead = $api->request('post', 'deals', $leadFields);
                fwrite($fp, print_r($lead, 1) . "\n");
            } else {
                fwrite($fp, '============ Order ============' . "\n");

                $statusId = 315;
                if (isset($_GET['fields']['title']) && $_GET['fields']['title'] == 'Купить в один клик') {
                    $statusId = 351;
                } elseif (isset($_GET['fields']['site']) && $_GET['fields']['site'] == 'VK') {
                    $statusId = (
                        isset($_GET['fields']['title']) &&
                        ($_GET['fields']['title'] == 'Заявка на консультацию' || $_GET['fields']['title'] == 'Подбор комплекта')
                    ) ? 389 : 385;
                }

                $leadData = [
                    'number'                  => $_GET['orderid'],
                    'name'                    => 'Заявка с "' . $_GET['fields']['form'] . '"',
                    'author_employee_id'      => 8833709,
                    'responsible_employee_id' => 8833709,
                    'partner_id'              => $agent,
                    'status_id'               => $statusId, //291
                    'delivery_address'        => isset($_GET['fields']['address']) ? $_GET['fields']['address'] : null,
                    'organization_id'         => 8738191,
                    'comment'                 => $_GET['comment'],
                    '398944'                  => $_GET['roistat'],
                ];

                $lead = $api->request('post', 'customerorders', $leadData);

                if($lead['status'] == 'error') {
                    switch ($lead['error_code']) {
                        case 'http:503':
                            sleep(20);
                            break;
                    }

                    //
                    $lead = $api->request('post', 'customerorders', $leadData);
                }

                fwrite($fp, print_r($lead, 1) . "\n");
            }
            fwrite($fp, "\n\n");
            fclose($fp);

            //Сделка
        } else {

            $fp = fopen('roistat.txt', 'a+');
            fwrite($fp, '============ Request ============' . "\n");

            // Проверка для заявок с виджетов сайта video.camerakit
            if (isset($_GET['fields']['from']) && $_GET['fields']['from'] == 'VK') {
                $closeStatuses = [110, 349, 350];

                $leads = $api->request('get', 'customerorders', [
                    'partner_id' => $agent
                ]);

                $send = true;
                if (!empty($leads['result'])) {
                    foreach ($leads['result'] as $lead) {
                        if (!in_array($lead['status_id'], $closeStatuses)) {
                            $send = false;
                            break;
                        }
                    }
                }

                if ($send) {
                    $lead = $api->request('post', 'customerorders', [
                        'name'                    => 'Заявка с "' . $_GET['fields']['form'] . '"',
                        'author_employee_id'      => 8833709,
                        'responsible_employee_id' => 8833709,
                        'partner_id'              => $agent,
                        'status_id'               => 387,
                        'organization_id'         => 8738191,
                        'comment'                 => $_GET['comment'],
                        '398944'                  => $_GET['roistat'],
                    ]);
                }
            // Конец проверки
            } else {
                $lead = $api->request('post', 'deals', [
                    'name'                    => $_GET['fields']['form'],
                    'author_employee_id'      => 8833709,
                    'responsible_employee_id' => 8833709,
                    'partner_id'              => $agent,
                    'status_id'               => 372,
                    'state'                   => 2,
                    'organization_id'         => 8738191,
                    '410996'                  => $_GET['roistat'],
                    '410998'                  => $_GET['fields']['marker'],
                    '411000'                  => $_GET['fields']['form']
                ]);
            }

            fwrite($fp, print_r($lead, 1) . "\n");
            fwrite($fp, "\n\n");
            fclose($fp);
            //Обращение
        }

    } else {
        //        if (is_numeric($_GET['fields']['order'])) {
        $fp = fopen('roistat.order.php', 'a+');
        fwrite($fp, $_GET['fields']['order'] . ';' . $_GET['roistat'] . ';' . $_GET['fields']['marker'] . "\n");
        fclose($fp);
        //        }

        //Корзина + 1 клик
    }

    /*

    [410996] => roistat
    [410998] => marker
    [411000] => form

    */

}
// else {
/*
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
*/
// }




