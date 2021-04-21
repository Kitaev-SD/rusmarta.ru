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


    Class smsprofi
    {

        const REQUEST_SUCCESS = 'success';
        const REQUEST_ERROR = 'error';

        private $oHttp = null;

        private $arParams = array(
            'usr'     => '',
            'pwd'     => '',
            'from'    => '',
            'channel' => '0',
        );


        /**
         * Конструктор
         *
         * @param array $arParams
         * - string USER
         * - string PWD
         */
        public function __construct($arParams = array())
        {
            // подключим необходимую библиотеку
            require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');

            if (is_null($this->oHttp)) {
                $this->oHttp = new \Bitrix\Main\Web\HttpClient();
            }

            if (array_key_exists('USER', $arParams)) {
                $this->arParams['usr'] = trim($arParams['USER']);
            }
            if (array_key_exists('PWD', $arParams)) {
                $this->arParams['pwd'] = trim($arParams['PWD']);
            }
            if (array_key_exists('FROM', $arParams)) {
                $this->arParams['from'] = trim($arParams['FROM']);
            }
            if (array_key_exists('CHANNEL', $arParams)) {
                $this->arParams['channel'] = trim($arParams['CHANNEL']);
            }

        }

        /**
         * Получение значения параметры
         *
         * @param      $name
         * @param null $default_value
         *
         * @return null
         */
        private function _getParam($name, $default_value = null)
        {
            return (isset($this->arParams[ $name ]) ? $this->arParams[ $name ] : $default_value);
        }


        /**
         * Сообщения
         *
         * @param $code
         *
         * @return mixed|string
         */
        private function _getMsg($code)
        {
            return GetMessage('bxmaker.smsnotice.smsprofi.' . $code);
        }

        /**
         * Проверка баланса
         * @return Result
         */
        public function getBalance()
        {
            $result = new Result();
            $response = $this->_makeRequest('balance', Array());

            if (isset($response['data']['#']['account'][0]['#'])) {
                $result->setResult(number_format($response['data']['#']['account'][0]['#'], 2, '.', ' ') . $this->_getMsg('SERVICE.CURRENCY'));
            } elseif (isset($response['data']['#']['descr'][0]['#'])) {
                $result->setError(new Error($response['data']['#']['descr'][0]['#'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $response )));
            } else {
                $result->setError(new Error($this->_getMsg('ERROR.UNKNOWN_RESPONSE'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $response )));
            }

            return $result;
        }


        /**
         * Отправка смс
         * @param $phone
         * @param $text
         * @param array $arParams
         * @return Result
         */
        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();

            $arParams["to"] = $phone;
            $arParams["text"] = $text;

            if (!isset($arParams['sender']) || strlen($arParams['sender']) <= 0) {
                $arParams["sender"] = $this->_getParam('from');
            }

            $response = $this->_makeRequest('message/send', $arParams);

            if (isset($response['data']['#']['smsid'][0]['#'])) {

                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                $result->setMore('params', array(
                    'messageId' => $response['data']['#']['smsid'][0]['#']
                ));
                $result->setMore('response', $response);

            } elseif (isset($response['data']['#']['descr'][0]['#'])) {
                $result->setError(new Error($response['data']['#']['descr'][0]['#'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $response )));
            } else {
                $result->setError(new Error($this->_getMsg('ERROR.UNKNOWN_RESPONSE'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'response' => $response,
                    'params'   => $arParams
                )));
            }

            return $result;
        }


        /**
         * Отправка пакета смс сообщений, около 200 за раз обычно
         * @param $arPack
         * @return Result
         */
        public function sendPack($arPack)
        {
            //		PackItem - array(
            //			"phone"    => $arItem['PHONE'],
            //			  "clientId" => $id,
            //			  "text"     => $arItem['TEXT'],
            //			  "sender"   => $arItem['SENDER']
            //		)

            $result = new Result();

            $arMsg = array(); // clientId => status

            //разбиение по отправителям, затем по номерам телефонов
            while (count($arPack['messages'])) {
                //чтобы не пропустить одинаковые номера с разным текстом
                $arTmpPack = $arPack;

                $arSenderSms = array();
                $arPack['messages'] = array();

                $arPhoneToClientId = array(); //связи номер телефона - id записи в базе., для установки статуса

                // разбиваем на отправителей
                foreach ($arTmpPack['messages'] as $msg) {
                    if (!$msg['sender']) {
                        $msg['sender'] = $this->_getParam('sadr');
                    }
                    if (isset($arSenderSms[ $msg['sender'] ][ $msg['phone'] ])) {
                        //для повторного прохода
                        $arPack['messages'][] = $msg;
                    } else {
                        // собираем массив
                        $arSenderSms[ $msg['sender'] ][ $msg['phone'] ] = $msg['text'];
                        $arPhoneToClientId[ $msg['phone'] ] = $msg['clientId'];
                    }
                }

                foreach ($arSenderSms as $sender => $arMulti) {
                    // партиями по 100 штук максимум
                    $arMultiParts = array_chunk($arMulti, 100, true);
                    // проход по одной партии
                    foreach ($arMultiParts as $arMultiPart) {

                        //отпрака
                        $response = $this->_makeRequest('message/sendPack', array(
                            'sender' => $sender,
                            'multi'  => $arMultiPart
                            // phone->text
                        ));
                        if (isset($response['data']['#']['smsid'][0]['#'])) {

                            foreach ($arMultiPart as $phone => $text) {
                                $smsResult = new Result(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                                $smsResult->setMore('params', array(
                                    'messageId' => $response['data']['#']['smsid'][0]['#']
                                ));
                                $arMsg[ $arPhoneToClientId[ $phone ] ] = $smsResult;
                            }

                        } elseif (isset($response['data']['#']['descr'][0]['#'])) {
                            foreach ($arMultiPart as $phone => $text) {
                                $smsResult = new Result();
                                $smsResult->setError(new Error($response['data']['#']['descr'][0]['#'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $response )));
                                $arMsg[ $arPhoneToClientId[ $phone ] ] = $smsResult;
                            }

                        } else {
                            foreach ($arMultiPart as $phone => $text) {
                                $smsResult = new Result();
                                $smsResult->setError(new Error($this->_getMsg('ERROR.UNKNOWN_RESPONSE'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                                    'response' => $response,
                                    'params'   => array(
                                        'sender'  => $sender,
                                        'isMulti' => 'Y'
                                    )
                                )));
                                $arMsg[ $arPhoneToClientId[ $phone ] ] = $smsResult;
                            }
                        }
                    }
                }
            }

            $result->setResult(array(
                'messages' => $arMsg
            ));

            return $result;
        }


        /**
         * Проверяет статус сообщения поидее
         */
        public function notice()
        {
            return new Result();
        }


        /**
         * Периодически выполняется агент, проверящий статусы сообщений данного типа если нужно
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


            foreach ($arSms as $smsId => $arSmsMore) {
                if (isset($arSmsMore['PARAMS']['messageId'])) {

                    $res = $this->_messageStatus($arSmsMore['PARAMS']['messageId']);

                    if ($res->isSuccess()) {
                        $arResult[ $smsId ] = $res;
                    } else {
                        $arError = array_merge($arError, $res->getErrors());
                        $arResult[ $smsId ] = $res;
                    }
                } else {
                    $arError[] = new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $arResult[ $smsId ] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
                }
            }

            $result->setMore('results', $arResult);
            $result->setMore('errors', (!empty($arError) ? $arError : null)); // ошибок нет

            return $result;
        }


        // кодировка должна быть UTF-8
        private function _getPreparedStr($str)
        {
            /** @global CMain $APPLICATION */
            global $APPLICATION;

            return (!Manager::isUTF() ? $APPLICATION->ConvertCharset($str, LANG_CHARSET, "UTF-8") : $str);
        }

        // ответ в UTF-8 приходит, поэтому подготовим для внутреннего пользования
        private function _getFromUtf($str)
        {
            /** @global CMain $APPLICATION */
            global $APPLICATION;

            return (!Manager::isUTF() ? $APPLICATION->ConvertCharset($str, "UTF-8", LANG_CHARSET) : $str);
        }

        /**
         * Вернет массив описывающий парамтеры подклчюения к сервису на странице настройки сервиса
         * @return array
         */
        public function getParams()
        {
            return array(
                'USER'    => array(
                    'NAME'      => $this->_getMsg('PARAMS.USER'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.USER.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
                'PWD'     => array(
                    'NAME'      => $this->_getMsg('PARAMS.PWD'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.PWD.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
                'FROM'    => array(
                    'NAME'      => $this->_getMsg('PARAMS.FROM'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.FROM.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
                'CHANNEL' => array(
                    'NAME'      => $this->_getMsg('PARAMS.CHANNEL'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.CHANNEL.HINT'),
                    'TYPE'      => 'LIST',
                    'VALUES'    => array(
                        array(
                            'id'   => '0',
                            'name' => $this->_getMsg('PARAMS.CHANNEL0')
                        ),
                        array(
                            'id'   => '1',
                            'name' => $this->_getMsg('PARAMS.CHANNEL1')
                        ),
                        array(
                            'id'   => '2',
                            'name' => $this->_getMsg('PARAMS.CHANNEL2')
                        )
                    ),
                    'VALUE'     => '0'
                )
            );
        }

        /**
         * Описание сервиса что куда тывать, для страницы параметров сервиса
         * @return mixed|string
         */
        public function getDescription()
        {
            return $this->_getMsg('DESCRIPTION');
        }


        /**
         * Вормирует xml  для запроса к внешнему сервису
         * @param $params
         * @param string $someXML
         * @return string
         */
        protected function makeXML($params, $someXML = "")
        {
            $xml = "<?xml version='1.0' encoding='UTF-8'?>\n<data>\n\t<login>" . $this->_getParam('usr') . "</login>\n\t<password>" . $this->_getParam('pwd') . "</password>\n";
            foreach ($params as $key => $value) {
                $xml .= "\t<$key>$value</$key>\n";
            }
            if ($someXML) {
                $xml .= $someXML . "\n";
            }
            $xml .= "</data>";

            return $this->_getPreparedStr($xml);
        }


        /**
         * Отправить запрос
         *
         * @param string $function
         * @param array $params
         *
         * @return array
         */
        protected function _makeRequest($method, array $params = array())
        {
            $url = "https://lcab.smsprofi.ru/API/XML/account.php";
            $someXml = '';

            switch ($method) {
                case 'message/status':
                    {
                        $url = "https://lcab.smsprofi.ru/API/XML/report.php";
                        $params["smsid"] = $params['id'];
                        unset($params['id']);
                        break;
                    }
                case 'message/send':
                    {
                        $url = "https://lcab.smsprofi.ru/API/XML/send.php";

                        $someXml .= "<to number='" . $params['to'] . "'></to>";


                        $params["source"] = $params["sender"];
                        $params["action"] = "send";
                        $params["channel"] = $this->_getParam('channel');

                        unset($params['to'], $params['service'], $params['sender']);

                        break;
                    }
                case 'message/sendPack':
                    {
                        $url = "https://lcab.smsprofi.ru/API/XML/send.php";

                        $params["action"] = "send";
                        $params["channel"] = $this->_getParam('channel');
                        $params["source"] = $params["sender"];

                        foreach ($params['multi'] as $phone => $text) {
                            $someXml .= "\t<to number='" . $phone . "'>" . $text . "</to>\n";
                        }

                        unset($params['to'], $params['service'], $params['sender'], $params['multi']);

                        break;
                    }
                case 'balance':
                    {
                        $url = "http://lcab.smsprofi.ru/API/XML/balance.php";
                        break;
                    }
                default:
                    {
                        return false;
                    }
            }

            $this->oHttp->setHeader('Content-Type', 'text/xml');
            $this->oHttp->setHeader('Accept', 'text/xml');
            $this->oHttp->setTimeout(10);
            $this->oHttp->disableSslVerification();
            $res = $this->oHttp->post($url, $this->makeXML($params, $someXml));


            if (!!$res) {
                $this->response = $this->_prepareAnswer($res);
            } else {
                $this->response = false;
            }

            return $this->response;
        }

        // ответ в utf
        private function _prepareAnswer($str)
        {
            /** @global \CMain $APPLICATION */
            global $APPLICATION;

            if ($str) {
                $oXml = new \CDataXML();
                $oXml->LoadString($str);
                $arRes = $oXml->GetArray();

                if (is_array($arRes) && !empty($arRes)) {
                    return (!Manager::isUTF() ? $APPLICATION->ConvertCharsetArray($arRes, "UTF-8", LANG_CHARSET) : $arRes);
                }
            }

            return (!Manager::isUTF() ? $APPLICATION->ConvertCharset($str, "UTF-8", LANG_CHARSET) : $str);
        }

        /**
         * Проверить статус доставки сообщений
         *
         * @param string $messagesId
         *
         * @return boolean|array
         */
        private function _messageStatus($messagesId)
        {
            $result = new Result();

            $response = $this->_makeRequest('message/status', array(
                'id' => $messagesId
            ));
            $result->setMore('response', $response);

            if (isset($response['data']['#']['code'][0]['#']) && $response['data']['#']['code'][0]['#'] != 1) {
                $result->setError(new Error($response['data']['#']['descr'][0]['#'], \Bxmaker\SmsNotice\SMS_STATUS_ERROR, array( 'response' => $response )));
            } elseif (isset($response['data']['#']['code'][0]['#']) && $response['data']['#']['code'][0]['#'] == 1) {
                $status = null;
                foreach ($response['data']['#']['detail'][0]['#'] as $statusKey => $statusInfo) {
                    if (isset($statusInfo[0]['#']['number']['0']['#'])) {
                        $status = $statusKey;
                    }
                }

                switch ($status) {
                    case 'delivered':
                        {
                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                            break;
                        }
                    case 'notDelivered':
                        {
                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $result->setMore('error_description', $this->_getMsg('STATUS.NO_DELIVERED'));
                            break;
                        }
                    case 'cancel':
                        {
                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $result->setMore('error_description', $this->_getMsg('STATUS.CANCEL'));
                            break;
                        }
                    case 'enqueued':
                    case 'waiting':
                    case 'onModer':
                    case 'process':
                        {
                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                            break;
                        }
                    default:
                        {
                            $result->setError(new Error($this->_getMsg('ERROR.UNKNOWN_RESPONSE'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                                'response' => $response
                            )));
                            break;
                        }

                }

            } else {
                $result->setError(new Error($this->_getMsg('ERROR.UNKNOWN_RESPONSE'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'response' => $response
                )));
            }

            return $result;
        }


    }
