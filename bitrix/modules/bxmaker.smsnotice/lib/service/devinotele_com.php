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


    Class devinotele_com
    {

        //http://docs.devinotele.com/httpapiv2.html

        const REQUEST_SUCCESS = 'success';
        const REQUEST_ERROR = 'error';

        private $arParams = array(
            'user'   => '',
            'pwd'    => '',
            'sender' => ''
        );

        private $oHttp = null;

        private $lastRequestParams = array();


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
            return GetMessage('bxmaker.smsnotice.devinotele_com.' . $code);
        }

        public function getParams()
        {

            return array(
                'USER'   => array(
                    'NAME'      => $this->_getMsg('PARAMS.USER'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.USER.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
                'PWD'    => array(
                    'NAME'      => $this->_getMsg('PARAMS.PWD'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.PWD.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
                'SENDER' => array(
                    'NAME'      => $this->_getMsg('PARAMS.SENDER'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.SENDER.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
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

            if (is_array($response)) {
                $result->setError(new Error($response['Code'] . ' ' . $response['Desc'], \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array( 'response' => $response )));
            } elseif (is_scalar($response)) {
                $response = number_format($response, 2, '.', ' ');
                $result->setResult($response . ' ' . $this->_getMsg('SERVICE.CURRENCY'));
            } else {
                //приводим к строке
                $result->setError(new Error((string)$response, \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE));
            }
            return $result;
        }

        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();
            $arParams['Data'] = $this->encode($text);
            $arParams['DestinationAddress'] = $phone;

            $response = $this->_makeRequest('message/send', $arParams);


            if (is_array($response) && isset($response['Code'])) {
                $result->setError(new Error($response['Code'] . ' ' . $response['Desc'], \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
                    'response' => $response,
                    'requestParams' => $this->lastRequestParams
                ))));
            } elseif (is_array($response) && isset($response['0'])) {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                $result->setMore('params', array(
                    'messageId' => $response['0']
                ));
                $result->setMore('response', $response);
            } else {
                //попробуем отложить отправку в случае неизвестнго ответа
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_WAIT);

//                $result->setError(new Error($this->_getMsg('ERROR_RESPONSE'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
//                    'response' => $response
//                ))));
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
                $arMsg[ $msg['clientId'] ] = $this->send($msg['phone'], $msg['text']);
            }
            $result->setResult(array( 'messages' => $arMsg ));

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

                    $res = $this->_messageStatus($arSmsMore['PARAMS']['messageId'], $arSmsMore['PHONE']);

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


        /**
         * Проверить статус доставки сообщений
         *
         * @param string $messagesId
         *
         * @return boolean|array
         */
        private function _messageStatus($messagesId, $phone)
        {

            $result = new Result();

            $response = $this->_makeRequest('message/status', array(
                'id'    => $messagesId,
                'phone' => $phone
            ));


            $result->setMore('response', $response);
            $result->setMore('messagesId', $messagesId);
            $result->setMore('params', array(
                'messageId' => $messagesId
            ));

            if (is_array($response) && isset($response['Code'])) {
                $result->setError(new Error($response['Code'] . ' ' . $response['Desc'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'message_id' => $messagesId,
                    'response'   => $response
                )));
            } elseif (is_array($response) && isset($response['State'])) {
                switch ($response['State']) {
                    case '0':
                        {
                            //доставлено
                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);

                            break;
                        }
                    case '-1':
                    case '-2' :
                    case '-255' :
                        {
                            // отправлено
                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                            break;
                        }
                    case '47':
                    case '-98':
                    case '10':
                    case '11':
                    case '41':
                    case '42':
                    case '46':
                    case '48':
                    case '69':
                    case '99':
                        {
                            //errro
                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $result->setMore('error_description', $this->_getMsg('STATUS_ERROR_' . $response['State']));
                            break;
                        }
                    default:
                        {
                            $result->setError(new Error($this->_getMsg('ERROR_RESPONSE'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                                'message_id' => $messagesId,
                                'response'   => $response
                            )));
                        }
                }
            } else {
                $result->setError(new Error($this->_getMsg('ERROR_RESPONSE'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'message_id' => $messagesId,
                    'response'   => $response
                )));
            }


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
            $p = array(
                'Login'    => $this->_getParam('user'),
                'Password' => $this->_getParam('pwd'),
            );

            switch ($method) {
                case 'message/status':
                    {

                        $p = array_merge(array(
                            'messageId' => $params['id']
                        ), $p);

                        $url = 'https://integrationapi.net/rest/v2/Sms/State';
                        $url .= '?' . http_build_query($p);

                        $this->lastRequestParams = array(
                            'url' => $url,
                            'params' => $p
                        );

                        $response = $this->oHttp->get($url);
                        break;
                    }
                case 'message/send':
                    {
                        $url = 'https://integrationapi.net/rest/v2/Sms/Send';
                        $p = array_merge(array(
                            'Data'               => $params['Data'],
                            'DestinationAddress' => $params['DestinationAddress']
                        ), $p);

                        if (strlen($this->_getParam('sender')) > 0) {
                            $p['SourceAddress'] = $this->_getParam('sender');
                            $p['sourceAddress'] = $this->_getParam('sender');
                        }

                        $this->lastRequestParams = array(
                            'url' => $url,
                            'params' => $p
                        );

                        $response = $this->oHttp->post($url, $p);

                        break;
                    }
                case 'balance':
                    {
                        $url = 'https://integrationapi.net/rest/v2/User/Balance';
                        $url .= '?' . http_build_query($p);

                        $this->lastRequestParams = array(
                            'url' => $url,
                            'params' => $p
                        );

                        $response = $this->oHttp->get($url);
                        break;
                    }
                default:
                    {
                        return false;
                    }
            }

            return $this->response = $this->decode(json_decode($response, true));
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
                $data = Encoding::convertEncoding($data, 'UTF-8', SITE_CHARSET);
            }
            return $data;
        }


    }
