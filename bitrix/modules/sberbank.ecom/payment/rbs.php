<?php


require_once(realpath(dirname(dirname(__FILE__))) ."/config.php");

define('LOG_FILE', realpath(dirname(dirname(__FILE__))) . "/rbspayment.log");

class RBS
{

   
    const test_url = API_TEST_URL;
 
    const prod_url = API_PROD_URL;
 
    const log_file = LOG_FILE;

    /**
     * Массив с НДС
     *
     * @var integer
     * 0 = Без НДС
     * 2 = НДС чека по ставке 10%
     * 3 = НДС чека по ставке 18%
     * 6 = НДС чека по ставке 20%
     */
    private static $arr_tax = [
        0 => 0,
        2 => 10, 
        3 => 18,
        6 => 20,
    ];

    private $user_name;

    private $password;

    private $two_stage;
 
    private $test_mode;

    private $language = 'ru';

    private $logging;



    /**
     * КОНСТРУКТОР КЛАССА
     * Заполнение свойств объекта
     * @param $params
     * @return RBS
     * @internal param string $user_name логин мерчанта
     * @internal param string $password пароль мерчанта
     * @internal param bool $logging логирование
     * @internal param bool $two_stage двухстадийный платеж
     * @internal param bool $test_mode тестовый режим
     */

    public function RBS($params = array()) {
        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }
    }


    private function gateway($method, $data)
    {
        $data['userName'] = $this->user_name;
        $data['password'] = $this->password;
        $data['CMS'] = 'Bitrix ' . SM_VERSION;
        $data['jsonParams'] = json_encode( array('CMS' => $data['CMS'],'Module-Version' => RBS_VERSION) );
        if (SITE_CHARSET != 'UTF-8') {
            global $APPLICATION;
            $data = $APPLICATION->ConvertCharsetArray($data, 'windows-1251', 'UTF-8');
        }
        $dataEncoded = http_build_query($data);




        if ($this->test_mode) {
            $url = self::test_url;
        } else {
            $url = self::prod_url;
        }


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $dataEncoded,
            CURLOPT_HTTPHEADER => array('CMS: Bitrix', 'Module-Version: ' . RBS_VERSION),
            CURLOPT_SSLVERSION => 6
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        if (!$response) {
 
            $client = new \Bitrix\Main\Web\HttpClient(array(
                'waitResponse' => true
            ));
            $client->setHeader('CMS', 'Bitrix');
            $client->setHeader('Module-Version', RBS_VERSION);
            $response = $client->post($url . $method, $data);
        }

        if (!$response) {
            $response = array(
                'errorCode' => 999,
                'errorMessage' => 'The server does not have SSL/TLS encryption on port 443',
            );
        } else {
            if (SITE_CHARSET != 'UTF-8') {
                global $APPLICATION;
                $APPLICATION->ConvertCharset($response, 'UTF-8', 'windows-1251');
            }
            $response = \Bitrix\Main\Web\Json::decode($response);
 
            if ($this->logging) {
                if(isset($response['errorCode']) && $response['errorCode'] != 1) {
                    $this->logger($url, $method, $data, $dataEncoded, $response);
                } else if(isset($response['orderId'])) {
                    $this->logger($url, $method, $data, $dataEncoded, $response);
                }
            }
        }
        return $response;
    }


    private function logger($url, $method, $data, $dataEncoded, $response)
    {
        $objDateTime = new DateTime();
        $file = self::log_file;
        $logContent = '';
        
        if(file_exists($file)) {
            $logSize = filesize($file) / 1000;
            if($logSize < 4000) {
                $logContent = file_get_contents($file);
            }
        }

        $logContent .= "DATE: " . $objDateTime->format("Y-m-d H:i:s") . "\n";
        $logContent .= 'RBS PAYMENT ' . $url . $method . "\n";
        $logContent .= "DATA: \n" . print_r($data,true) . "\n";
        $logContent .= "REQUEST: \n" . print_r($dataEncoded,true) . "\n";
        $logContent .= "RESPONSE: \n" . print_r($response,true) . "\n";
        $logContent .= "\n\n";
        file_put_contents($file, $logContent);
    }


    function register_order($order_number, $amount, $return_url, $currency, $orderDescription = '', $arCheck = null)
    {
        $iso = COption::GetOptionString("sberbank.ecom", "iso", serialize(array()));
        $arCurrency = unserialize($iso);
        $arCurrency = array_filter($arCurrency);
        $arDefaultIso = unserialize(DEFAULT_ISO);
        if (is_array($arDefaultIso))
            $arCurrency = array_merge($arDefaultIso, $arCurrency);

        $data = array(
            'orderNumber' => $order_number,
            'language' => $this->language,
            'amount' => $amount,
            'returnUrl' => $return_url,
            'description' => $orderDescription,
        );
        if ($currency && isset($arCurrency[$currency]))
            $data['currency'] = $arCurrency[$currency];

        if ($arCheck) {
            $data = array_merge($data, $arCheck);
            $data['orderBundle'] = \Bitrix\Main\Web\Json::encode($data['orderBundle']);
        }

        if ($this->two_stage) {
            $method = 'registerPreAuth.do';
        } else {
            $method = 'register.do';
        }
        $response = $this->gateway($method, $data);
        return $response;
    }


    public function get_order_status_by_orderId($orderId)
    {
        $data = array('orderId' => $orderId);
        $response = $this->gateway('getOrderStatusExtended.do', $data);
        return $response;
    }

 
    public function get_order_status_by_orderNumber($order_number)
    {
        $data = array('orderNumber' => $order_number);
        $response = $this->gateway('getOrderStatusExtended.do', $data);
        return $response;
    }
    
    public function get_tax_list() {
        return self::$arr_tax;
    }
}