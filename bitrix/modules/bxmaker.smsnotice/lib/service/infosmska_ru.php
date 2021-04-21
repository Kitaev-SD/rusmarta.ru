<?

    namespace Bxmaker\SmsNotice\Service;

    use Bitrix\Main\Application;
    use Bitrix\Main\Config\Option;
    use Bitrix\Main\Loader;
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\Text\Encoding;
    use Bxmaker\SmsNotice\Error;
    use Bxmaker\SmsNotice\Manager;
    use Bxmaker\SmsNotice\ManagerTable;
    use Bxmaker\SmsNotice\Result;
    use Bxmaker\SmsNotice\Service;

    Loc::loadMessages(__FILE__);


    Class infosmska_ru
    {

//https://www.infosmska.ru/Pages/HttpApi.aspx

        const REQUEST_SUCCESS = 'success';
        const REQUEST_ERROR = 'error';

        private $arParams = array(
            'user' => '',
            'pwd' => '',
            'sender' => ''
        );

        private $oHttp = null;


        /**
         * Конструктор
         *
         * @param array $arParams
         * - string USER
         * - string PWD
         * - string SENDER
         * - integer TEST_MODE
         */
        public function __construct($arParams = array())
        {
            if (is_null($this->oHttp)) {
                $this->oHttp = new \Bitrix\Main\Web\HttpClient();
            }

            if (array_key_exists('USER', $arParams))
                $this->arParams['user'] = trim($this->encode($arParams['USER']));

            if (array_key_exists('PWD', $arParams))
                $this->arParams['pwd'] = trim($this->encode($arParams['PWD']));

            if (array_key_exists('SENDER', $arParams))
                $this->arParams['sender'] = trim($this->encode($arParams['SENDER']));
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
            return (isset($this->arParams[$name]) ? $this->arParams[$name] : $default_value);
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
            return GetMessage('bxmaker.smsnotice.infosmska_ru.' . $code);
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
                'SENDER' => array(
                    'NAME' => $this->_getMsg('PARAMS.SENDER'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.SENDER.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
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
         * Проверка баланса
         * @return Result
         */
        public function getBalance()
        {
            $result = new Result();
            $response = $this->_makeRequest('balance', Array());
            $arPrats = explode('.', $response);
            if (count($arPrats) == 2) {
                $result->setResult(floatval($response) . $this->_getMsg('SERVICE.CURRENCY'));
            } else {
                //приводим к строке
                $result->setError(new Error((string)$response, \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE));
            }
            return $result;
        }

        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();
            $arParams['message'] = $this->encode($text);
            $arParams['phone'] = $phone;

            $response = $this->_makeRequest('message/send', $arParams);
            $arPart = explode(':', $response);

            
            if ($arPart[0] == 'Error') {
                $result->setError(new Error($response, \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
                    'response' => $response
                ))));
            } elseif ($arPart[0] == 'Ok') {
                $arPartMessageId = explode(';', $arPart[1]);
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                $result->setMore('params', array(
                    'messageId' => $arPartMessageId[0]
                ));
                $result->setMore('response', $response);
            } else {
                $result->setError(new Error($this->_getMsg('ERROR_RESPONSE'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
                    'response' => $response
                ))));
            }

            return $result;
        }

        public function sendPack($arPack)
        {
            //		PackItem - array(
            //			"phone"    => $arItem['PHONE'],
            //			  "clientId" => $id,
            //			  "text"     => $arItem['TEXT'],
            //			  "sender"   => $arItem['SENDER']
            //		)

            $result = new Result();

            $arMsg = array();
            foreach ($arPack['messages'] as $msg) {
                $arMsg[$msg['clientId']] = $this->send($msg['phone'], $msg['text']);
            }
            $result->setResult(array('messages' => $arMsg));

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


            $arMessageId2smsId = array();
            foreach ($arSms as $smsId => $arSmsMore) {
                if (isset($arSmsMore['PARAMS']['messageId']) && !empty($arSmsMore['PARAMS']['messageId'])) {

                    $arMessageId2smsId[trim($arSmsMore['PARAMS']['messageId'], ';')] = $smsId;

                } else {
                    $arError[] = new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $arResult[$smsId] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
                }
            }

            if(count($arMessageId2smsId))
            {
                $arParts = array_chunk($arMessageId2smsId, 50, true);
                foreach($arParts as $arPart)
                {
                    $res = $this->_messageStatus(array_keys($arPart));
                    if($res->isSuccess())
                    {
                        $arStatusResult = $res->getResult();

                        foreach($arStatusResult as $msgId => $statusResult)
                        {
                            $arResult[$arMessageId2smsId[$msgId]] = $statusResult;
                        }
                    }
                    else{
                        $arError = array_merge($arError, $res->getErrors());
                    }
                }
            }


            $result->setMore('results', $arResult);
            $result->setMore('errors', (!empty($arError) ? $arError : null)); // ошибок нет

            return $result;
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
                'ids' => $messagesId
            ));
            
            $arResult = array();

            $arParts = explode(':', $response);
            if($arParts[0] == 'Ok')
            {
                $arMsgStatus = explode(';', $arParts[1]);

                foreach($messagesId as $msgIdIndex => $msgId)
                {
                    if(strlen(trim($msgId)) <= 0) continue;
                    
                    $statusResult = new Result();
                    $statusResult->setMore('response', $arMsgStatus[$msgIdIndex]);
                    $statusResult->setMore('messagesId', $msgId);
                    $statusResult->setMore('params', array(
                        'messageId' => $msgId
                    ));


                    switch($arMsgStatus[$msgIdIndex])
                    {
                        case '2':
                        case '3': {
                            //доставлено
                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                            break;
                        }
                        case '0': {
                            // отправлено
                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                            break;
                        }
                        case '-1':
                        case '-3':
                        case '-2':
                        case '-4':
                        case '-10':
                        case '-100': {
                            //errro
                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $result->setMore('error_description', $this->_getMsg('STATUS_ERROR_'.$arMsgStatus[$msgIdIndex]));
                            break;
                        }
                        default: {

                            $statusResult->setError(new Error($response, \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                                'message_id' => $msgId,
                                'response' => $arMsgStatus[$msgIdIndex]
                            )));
                        }
                    }

                    $arResult[$msgId] = $statusResult;
                }
            }
            elseif($arParts[0] == 'error')
            {
                foreach($messagesId as $msgIdIndex => $msgId)
                {
                    $statusResult = new Result();
                    $statusResult->setError(new Error($response, \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                        'response' => $response
                    )));
                    $statusResult->setMore('response', $response);
                    $statusResult->setMore('messagesId', $msgId);
                    $statusResult->setMore('params', array(
                        'messageId' => $msgId
                    ));

                    $arResult[$msgId] = $statusResult;
                }
            }
            else{
                $result->setError(new Error($response, \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'response' => $response
                )));
            }

            $result->setResult($arResult);

            return $result;
        }


        /**
         * Отправить запрос
         *
         * @param string $function
         * @param array $params
         *
         * @return stdClass
         */
        protected function _makeRequest($method, array $params = array())
        {
            $arData = array(
                'login' => $this->_getParam('user'),
                'pwd' => md5($this->_getParam('pwd')),
            );

            switch ($method) {
                case 'message/status':
                    {
                        $arData = array_merge(array(
                            'ids' => implode(',',$params['ids'])
                        ), $arData);

                        $url = 'http://api.infosmska.ru/interfaces/GetMessagesState.ashx';

                        $response = $this->oHttp->post($url, $arData);
                        break;
                    }
                case 'message/send':
                    {
                        $url = 'http://api.infosmska.ru/interfaces/SendMessages.ashx';
                        $arData = array_merge(array(
                            'message' => $params['message'],
                            'phones' => $params['phone']
                        ), $arData);

                        if (strlen($this->_getParam('sender')) > 0) {
                            $arData['sender'] = $this->_getParam('sender');
                        }

                        $response = $this->oHttp->post($url, $arData);

                        break;
                    }
                case 'balance':
                    {
                        $url = 'http://api.infosmska.ru/interfaces/getbalance.ashx';
                        $response = $this->oHttp->post($url, $arData);
                        break;
                    }
                default:
                    {
                        return false;
                    }
            }

            return $this->response = $this->decode($response);
        }

        private function encode($data)
        {
            if (!Application::getInstance()->isUtfMode()) {
                $data = Encoding::convertEncoding($data, SITE_CHARSET, 'UTF-8');
            }
            return $data;
        }

        private function decode($data)
        {
            if (!Application::getInstance()->isUtfMode()) {
                $data = $data = Encoding::convertEncoding($data, 'UTF-8', SITE_CHARSET);
            }
            return $data;
        }


    }
