<?php

header('Content-Type: application/json; charset=utf-8');
//header('Content-Type: text/html; charset=utf-8');

const ROISTAT_LOGIN    = 'minin@rusmarta.ru';
const ROISTAT_PASSWORD = 'rusmarta';

if(!isset($_REQUEST['token']) || md5(ROISTAT_LOGIN.ROISTAT_PASSWORD) != $_REQUEST['token']) {
   die('Invalid token');
}

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

function proxylead($orders)
{
    $firstOrder = array_shift($orders['result']);
    $lastOrder  = array_pop($orders['result']);

    $dateEnd   = new DateTime($firstOrder['updated']); // date('Y-m-d', strtotime($firstOrder['updated']));
    $dateEnd   = $dateEnd->modify('-60 days')->format('Y-m-d');
    $dateStart = date('Y-m-d', strtotime($lastOrder['updated']));

    $url  = 'https://cloud.roistat.com/api/v1/project/proxy-leads?project=67124'.
        '&key=e65eb76963f904b8e4997aba9d3e422f'.'&period='.$dateEnd.'-'.$dateStart;

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Cache-Control: no-cache",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    return json_decode($response)->ProxyLeads;
}

// ...

switch ($_GET['action']) {
    case 'import_scheme':
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

        // ...

        $json = [
            'statuses' => $statuses,
            'fields'   => [],
            'managers' => [],
        ];
        break;
    case 'export':
        $orders = [];
        $limit = 200;
        $page = ceil($_GET['offset'] / $limit) + 1;

        $tmp = $api->request('get', 'customerorders', [
            'with_additional_fields' => 1,
            'updated' => [
                'from' => date('d.m.Y H:m', $date)
            ],
            'page' => $page,
            'limit' => $limit,
            'order_by' => [
                'updated' => 'ASC'
            ]
        ]);

        $count = $api->request('get', 'customerorders', [
            'with_additional_fields' => 1,
            'updated' => [
                'from' => date('d.m.Y H:m', $date)
            ],
            'count_only' => 1,
            'page' => $page,
            'limit' => $limit
        ]);

        $proxyleads = proxylead($tmp);

        $clients = $api->request('get','partners',[
            'extend' => 'partnercontactinfo',
            'id'     => array_map(function ($item) {
                return $item['partner_id'];
            }, $tmp['result']),
        ])['result'];

        $clients = array_map(function ($item) use ($tmp) {
            $phone = array_filter($item['partnercontactinfo'], function ($info) {
                return $info['contact_info_type_id'] == 1;
            });
            $phone = !empty($phone) ? array_shift($phone)['contact_info'] : null;
            // $phone = '7' . substr(preg_replace('/\D/', '', $phone), 1);
            $phone = preg_replace('/\D/', '', $phone);

            $email = array_filter($item['partnercontactinfo'], function ($info) {
                return $info['contact_info_type_id'] == 4;
            });
            $email = !empty($email) ? array_shift($email)['contact_info'] : null;
            $email = mb_strtolower($email);
            $order_filter = array_filter($tmp['result'], function ($order) use ($item) {
                return $item['id'] == $order['partner_id'];
            });
            $order = array_shift($order_filter);

            return array(
                'order_id'      => $order['id'],
                'order_number'  => $order['number'],
                'partner_id'    => $item['id'],
                'partner_phone' => $phone,
                'partner_email' => $email,
            );
        }, $clients);

        $search = array_map(function ($item) use ($proxyleads) {
            if(!empty($item['order_number'])) {
                $proxylead = array_filter($proxyleads, function ($proxy) use ($item) {
                    if (isset($proxy->order_fields)) {
                        if (isset($proxy->order_fields->order)) {
                            return $item['order_number'] == $proxy->order_fields->order;
                        }
                    }
                });
            } elseif (!empty($item['partner_email'])) {
                $proxylead = array_filter($proxyleads, function ($proxy) use ($item) {
                    return $item['partner_email'] == $proxy->email;
                });
            } else {
                $proxylead = array_filter($proxyleads, function ($proxy) use ($item) {
                    return $item['partner_phone'] == $proxy->phone;
                });
            }

            $proxylead = array_shift($proxylead);
            $proxylead_roistat = $proxylead->roistat ? (isset($proxylead->roistat)) : null;
            return [
                'client_id' => $item['partner_id'],
                'visit'     => $proxylead_roistat,
            ];
        }, $clients);


        foreach($tmp['result'] as $row) {
            $client_id_filtered = array_filter($search, function ($item) use ($row) {
                return $item['client_id'] == $row['partner_id'];
            });
            $client = array_shift($client_id_filtered);

            $visit = !empty($client['visit']) ? $client['visit'] : '';

            $orders[] = [
                'id' => $row['id'],
                'name' => 'Заказ № ' . $row['number'],
                'date_create' => $row['date'], // strtotime($row['date']),
                'date_update' => $row['updated'], // strtotime($row['updated']),
                'status' => $row['status_id'],
                'price' => $row['sum'],
                'client_id' => $row['partner_id'],
                'roistat' => !empty($row['398944']) ? $row['398944'] : $visit,
            ];
        }

        /*
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
        */

//        echo '<pre>';
//        print_r( $orders );
//        die('</pre>');

        // ...

        $json = [
            'pagination' => [
                'total_count' => !empty($count['result']['count']) ? $count['result']['count'] : 99999,
                'limit' => $limit,
                'date' => date('d.m.Y H:m', $date),
            ],
            'orders' => $orders,
        ];

        writeToLog($_GET, 'Запрос');
        writeToLog($tmp, 'Ответ API');
        writeToLog($json, 'Ответ');

        break;
    case 'export_clients':
        $clients = [];
        $limit = 250;
        $page = ceil($_GET['offset'] / $limit) + 1;
        /*
        $counter = 10;

        $getApi = true;
        while (($getApi && $counter >= 0)) {
        */
            $tmp = $api->request('get','partners',[
                'extend' => 'partnercontactinfo',
                'page'  => $page,
                'limit' => $limit,

//                'id' => '8354077',

                'updated' => [
                    'from' => date('d.m.Y H:m', $date),
//                    'to' => '26.06.2019 12:00'
                ],
            ]);
        /*
            // ...

            if ($tmp['status'] == 'ok') {
                $getApi = false;
            } else {
                // Ожидание отключения ограничния по API
                sleep(10);

                $counter--;
            }
        }
        */

//        echo '<pre>';
//        print_r([
//            'extend' => 'partnercontactinfo',
//            'page'  => $page,
//            'limit' => $limit,
//
////                'id' => '8354077',
//
//            'updated' => [
//                'from' => date('d.m.Y H:m', $date),
//                'to' => '26.06.2019 12:00'
//            ],
//        ]);
//        print_r($tmp);
//        echo '<pre>';
//        die();

        foreach ($tmp['result'] as $index => $row) {
            $clients[$index] = [
                'id'    => $row['id'],
                'name'  => $row['name'],
                'phone' => '',
                'email' => '',
            ];

            if(!empty($row['partnercontactinfo'])) {
                $info = array_filter($row['partnercontactinfo'], function ($item) {
                    return in_array($item['contact_info_type_id'], array(1, 4));
                });
                
                $clients_phone_filtered = array_filter($info, function ($item) {
                    return $item['contact_info_type_id'] == 1;
                });
                $clients[$index]['phone'] = preg_replace('/\D/', '', array_shift($clients_phone_filtered)['contact_info']);
                
                $clients_email_filtered = array_filter($info, function ($item) {
                    return $item['contact_info_type_id'] == 4;
                });
                $clients[$index]['email'] = array_shift($clients_email_filtered)['contact_info'];
            }
        }

        // ...

        $json = [
            'clients' => $clients,
            'pagination' => [
                'total_count' => '999999',
                'limit' => $limit
            ]
        ];

        break;
    default:
        $json = [
            'status'  => 'error',
            'message' => 'No action',
        ];
        break;
}

echo json_encode($json,JSON_UNESCAPED_UNICODE);

function writeToLog($data, $title = '') {
    $log = "\n------------------------\n";
    $log .= date("d.m.Y G:i:s") . "\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n";
    file_put_contents(getcwd() . '/RS_TEST_log_data_export.log', $log, FILE_APPEND);
    return true;
}