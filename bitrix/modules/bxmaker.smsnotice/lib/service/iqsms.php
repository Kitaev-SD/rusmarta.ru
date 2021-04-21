<?

    namespace Bxmaker\SmsNotice\Service;

    use Bitrix\Main\Config\Option;
    use Bitrix\Main\Loader;
    use Bitrix\Main\Localization\Loc;
    use Bxmaker\SmsNotice\Error;
    use Bxmaker\SmsNotice\Manager;
    use Bxmaker\SmsNotice\ManagerTable;
    use Bxmaker\SmsNotice\Result;
    use Bxmaker\SmsNotice\Service;

    Loc::loadMessages(__FILE__);


    Class Iqsms
    {

        const REQUEST_SUCCESS = 'success';
        const REQUEST_ERROR = 'error';

        private $oHttp = null;

        private $arParams
            = array(
                'user' => '',
                'pass' => '',
                'from' => '',
            );

        private $arSenders = null;


        public function __construct($arParams = array())
        {

            $this->oHttp = new \Bitrix\Main\Web\HttpClient();


            if (array_key_exists('USER', $arParams)) {
                $this->arParams['user'] = trim($arParams['USER']);
            }

            if (array_key_exists('PWD', $arParams)) {
                $this->arParams['pass'] = trim($arParams['PWD']);
            }

            if (array_key_exists('SADR', $arParams)) {
                $this->arParams['from'] = trim($arParams['SADR']);
            }
        }


        /**
         * ѕолучение значени€ параметры
         *
         * @param      $name
         * @param null $default_value
         *
         * @return null
         */
        private function _getParam($name, $default_value = null)
        {
            return (isset($this->arParams[$name]) ? $this->arParams[$name] : $default_value);
        }


        /**
         * —ообщени€
         *
         * @param $code
         *
         * @return mixed|string
         */
        private function _getMsg($code)
        {
            return GetMessage('bxmaker.smsnotice.iqsms.' . $code);
        }

        public function getLogin()
        {
            return $this->_getParam('user');
        }

        public function getDefaultSender()
        {
            return $this->_getParam('from');
        }


        public function getSenders()
        {
            if (is_null($this->arSenders)) {
                $this->arSenders = array();

                $p = array(
                    'login' => $this->_getParam('user'),
                    'password' => $this->_getParam('pass'),
                );

                $url = 'http://json.gate.iqsms.ru/senders/';
                $response = $this->oHttp->post($url, json_encode($p));
                $response = json_decode($this->_getFromUtf($response), true);

                if ($response['status'] == 'ok') {
                    foreach ($response['senders'] as $sender) {
                        $this->arSenders[] = $sender;
                    }
                }

            }
            return $this->arSenders;
        }


        /**
         * ѕроверка баланса
         * @return Result
         */
        public function getBalance()
        {
            $result = new Result();

            $p = array(
                'login' => $this->_getParam('user'),
                'password' => $this->_getParam('pass'),
            );

            $url = 'http://api.iqsms.ru/messages/v2/balance.json';
            $response = $this->oHttp->post($url, json_encode($p));
            $response = json_decode($this->_getFromUtf($response), true);


            if ($response['status'] == 'ok') {
                $result->setResult(floatval($response["balance"][0]['balance']) . $this->_getMsg('CURRENCY_' . $response["balance"][0]['type']));
            } elseif ($response['status'] == 'error') {
                $result->setResult($response['description']);
            } else {
                $result->setError(new Error($this->_getMsg('ERROR_DESCRIPTION_BALANCE_RESPONSE'), \BXmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $response)));
            }
            return $result;
        }

        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();

            $arPack = array(
                'messages' => array(
                    "phone" => $phone,
                    "text" => $text
                ),
                'login' => $this->_getParam('user'),
                'password' => $this->_getParam('pass'),
            );

            if (!$arPack['sender']) {
                $arPack['sender'] = $this->_getParam('from');
            }

            $arPack = $this->_getPrepared($arPack);

            $url = 'http://json.gate.iqsms.ru/send/';


            $response = $this->oHttp->post($url, json_encode($arPack));

            $response = json_decode($this->_getFromUtf($response), true);

            if ($response['status'] == 'ok') {
                $msg = $response['messages'][0];

                $r = new Result();
                switch ($msg['status']) {
                    case 'accepted':
                        {
                            $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                            $r->setMore('params', array(
                                'messageId' => $msg['smscId']
                            ));
                            break;
                        }
                    case 'queued':
                        {
                            $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                            break;
                        }
                    case 'delivered':
                        {
                            $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_DELIVERED);
                            break;
                        }
                    case 'delivery error':
                        {
                            $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                            $r->setMore('error_description', $msg['status']);
                            break;
                        }
                    case 'smsc submit':
                        {
                            $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                            break;
                        }
                    case 'smsc reject':
                        {
                            $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                            $r->setMore('error_description', $msg['status']);
                            break;
                        }
                    case 'incorrect id':
                        {
                            $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                            $r->setMore('error_description', $msg['status']);
                            break;
                        }
                    default:
                        {
                            $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                            $r->setMore('error_description', $msg['status']);
                            break;
                        }
                }
                $r->setMore('params', array(
                    'messageId' => $msg['smscId']
                ));

                return $r;


            } elseif ($response['status'] == 'error') {
                $result->setError(new Error($response['description'], \BXmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $response)));
            } else {
                $result->setError(new Error($this->_getMsg('ERROR_DESCRIPTION_BALANCE_RESPONSE'), \BXmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $response)));
            }


            return $result;
        }


        public function sendPack($arPack)
        {
            $result = new Result();

            $arPack = array_merge($arPack, array(
                'login' => $this->_getParam('user'),
                'password' => $this->_getParam('pass'),
            ));

            if (!$arPack['sender']) {
                $arPack['sender'] = $this->_getParam('from');
            }

            $arPack = $this->_getPrepared($arPack);

            $url = 'http://json.gate.iqsms.ru/send/';
            $arMsg = array();

            $arMessagesParts = array_chunk($arPack['messages'], 200);
            foreach ($arMessagesParts as $part) {
                $arPack['messages'] = $part;

                $response = $this->oHttp->post($url, json_encode($arPack));

                $response = json_decode($this->_getFromUtf($response), true);
                if ($response['status'] == 'ok') {

                    foreach ($response['messages'] as $msg) {
                        $r = new Result();
                        switch ($msg['status']) {
                            case 'accepted':
                                {
                                    $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                                    $r->setMore('params', array(
                                        'messageId' => $msg['smscId']
                                    ));
                                    break;
                                }
                            case 'queued':
                                {
                                    $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                                    $r->setMore('params', array(
                                        'messageId' => $msg['smscId']
                                    ));
                                    break;
                                }
                            case 'delivered':
                                {
                                    $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_DELIVERED);
                                    $r->setMore('params', array(
                                        'messageId' => $msg['smscId']
                                    ));
                                    break;
                                }
                            case 'delivery error':
                                {
                                    $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                    $r->setMore('params', array(
                                        'messageId' => $msg['smscId'],
                                        'error_description' => $msg['status']
                                    ));
                                    break;
                                }
                            case 'smsc submit':
                                {
                                    $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                                    $r->setMore('params', array(
                                        'messageId' => $msg['smscId']
                                    ));
                                    break;
                                }
                            case 'smsc reject':
                                {
                                    $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                    $r->setMore('params', array(
                                        'messageId' => $msg['smscId'],
                                        'error_description' => $msg['status']
                                    ));
                                    break;
                                }
                            case 'incorrect id':
                                {
                                    $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                    $r->setMore('params', array(
                                        'messageId' => $msg['smscId'],
                                        'error_description' => $msg['status']
                                    ));
                                    break;
                                }
                            default:
                                {
                                    $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                    $r->setMore('params', array(
                                        'messageId' => $msg['smscId'],
                                        'error_description' => $msg['status']
                                    ));
                                    break;
                                }
                        }

                        $arMsg[$msg['clientId']] = $r;
                    }

                } elseif ($response['status'] == 'error') {
                    $result->setError(new Error($response['description'], \BXmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $response)));
                } else {
                    $result->setError(new Error($this->_getMsg('ERROR_DESCRIPTION_BALANCE_RESPONSE'), \BXmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $response)));
                }
            }

            $result->setResult(array('messages' => $arMsg));


            return $result;
        }

        public function statusPack($arPack)
        {
            $result = new Result();

            $arPack = array_merge($arPack, array(
                'login' => $this->_getParam('user'),
                'password' => $this->_getParam('pass'),
            ));
            $arPack = $this->_getPrepared($arPack);

            $url = 'http://json.gate.iqsms.ru/messages/v2/status.json';
            $response = $this->oHttp->post($url, json_encode($arPack));


            $response = json_decode($this->_getFromUtf($response), true);
            if ($response['status'] == 'ok') {
                $arMsg = array();
                foreach ($response['messages'] as $msg) {
                    $r = new Result();
                    switch ($msg['status']) {
                        case 'accepted':
                            {
                                $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                                $r->setMore('params', array(
                                    'messageId' => $msg['smscId']
                                ));
                                break;
                            }
                        case 'queued':
                            {
                                $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                                $r->setMore('params', array(
                                    'messageId' => $msg['smscId']
                                ));
                                break;
                            }
                        case 'delivered':
                            {
                                $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_DELIVERED);
                                $r->setMore('params', array(
                                    'messageId' => $msg['smscId']
                                ));
                                break;
                            }
                        case 'delivery error':
                            {
                                $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                $r->setMore('params', array(
                                    'messageId' => $msg['smscId'],
                                    'error_description' => $msg['status']
                                ));
                                break;
                            }
                        case 'smsc submit':
                            {
                                $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                                $r->setMore('params', array(
                                    'messageId' => $msg['smscId']
                                ));
                                break;
                            }
                        case 'smsc reject':
                            {
                                $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                $r->setMore('params', array(
                                    'messageId' => $msg['smscId'],
                                    'error_description' => $msg['status']
                                ));
                                break;
                            }
                        case 'incorrect id':
                            {
                                $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                $r->setMore('params', array(
                                    'messageId' => $msg['smscId'],
                                    'error_description' => $msg['status']
                                ));
                                break;
                            }
                        default:
                            {
                                $r->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                $r->setMore('params', array(
                                    'messageId' => $msg['smscId'],
                                    'error_description' => $msg['status']
                                ));
                                break;
                            }
                    }

                    $arMsg[$msg['clientId']] = $r;
                }
                $result->setResult(array('messages' => $arMsg));
            } elseif ($response['status'] == 'error') {
                $result->setError(new Error($response['description'], \BXmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $response)));
            } else {
                $result->setError(new Error($this->_getMsg('ERROR_DESCRIPTION_BALANCE_RESPONSE'), \BXmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $response)));
            }
            return $result;
        }


        // кодировка должна быть UTF-8
        private function _getPrepared($str)
        {
            return (!Manager::isUTF() ? \Bitrix\Main\Text\Encoding::convertEncoding($str, LANG_CHARSET, "UTF-8") : $str);
        }

        // ответ в UTF-8 приходит, поэтому подготовим дл€ внутреннего пользовани€
        private function _getFromUtf($str)
        {
            return (!Manager::isUTF() ? \Bitrix\Main\Text\Encoding::convertEncoding($str, "UTF-8", LANG_CHARSET) : $str);
        }


        private function _strlen($str)
        {
            return (Manager::isUTF() ? mb_strlen($str) : strlen($str));
        }


        public function getParams()
        {

            return array(
                'USER' => array(
                    'NAME' => $this->_getMsg('PARAMS.USER'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.USER.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                ),
                'PWD' => array(
                    'NAME' => $this->_getMsg('PARAMS.PWD'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.PWD.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                ),
                'SADR' => array(
                    'NAME' => $this->_getMsg('PARAMS.SADR'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.SADR.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                )
            );
        }

        /**
         * ќписание сервиса что куда тывать, дл€ страницы параметров сервиса
         * @return mixed|string
         */
        public function getDescription()
        {
            return $this->_getMsg('DESCRIPTION');
        }


        /**
         * ѕериодически выполн€етс€ агент, провер€щий статусы сообщений данного типа если нужно
         *
         * @param $arSms
         *
         * @return Result
         */
        public function agent($arSms)
        {
            $result = new Result(true);
            $arResult = array();
            $arError = array();


            $arParams = array();
            $arSmsId = array();
            foreach ($arSms as $smsId => $arSmsMore) {
                $arParams['messages'][] = array(
                    "clientId" => $smsId,
                    "smscId" => $arSmsMore['PARAMS']['messageId']
                );
                $arSmsId[$arSmsMore['PARAMS']['messageId']] = $smsId;
            }


            $statusResult = $this->statusPack($arParams);
            if ($statusResult->isSuccess()) {
                $arSmsIdResult = $statusResult->getResult();

                foreach ($arSmsIdResult['messages'] as $id => $smsResult) {
                    $arResult[$id] = $smsResult;
                }
            }

            $result->setMore('results', $arResult);

            return $result;
        }


    }