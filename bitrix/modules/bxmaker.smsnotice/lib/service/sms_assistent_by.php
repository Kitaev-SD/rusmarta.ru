<?

    namespace Bxmaker\SmsNotice\Service;

    use Bitrix\Main\Config\Option;
    use Bitrix\Main\Loader;
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\Text\Encoding;
    use Bitrix\Main\Web\HttpClient;
    use Bxmaker\SmsNotice\Error;
    use Bxmaker\SmsNotice\Manager;
    use Bxmaker\SmsNotice\ManagerTable;
    use Bxmaker\SmsNotice\Result;
    use Bxmaker\SmsNotice\Service;

    Loc::loadMessages(__FILE__);


    Class sms_assistent_by
    {

//http://http://sms-assistent.by/


        private $arParams = array(
            'user'   => '',
            'pwd'    => '',
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
                $this->arParams['user'] = trim($arParams['USER']);

            if (array_key_exists('PWD', $arParams))
                $this->arParams['pwd'] = trim($arParams['PWD']);

            if (array_key_exists('SENDER', $arParams))
                $this->arParams['sender'] = trim($arParams['SENDER']);
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
            return GetMessage('bxmaker.smsnotice.sms_assistent_by.' . $code);
        }


        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();

            $arParams['text'] = trim($text);
            $arParams['phone'] = $phone;

            $response = $this->_makeRequest('message/send', $arParams);

            if (isset($response['message'])) {

                if ($response['message']['msg'][0]['error_code'] < 0) {
                    $result->setError(new Error($this->_getErrorDescription($response['message']['msg'][0]['error_code']), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
                        'response' => $response
                    ))));
                } else {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    $result->setMore('params', array(
                        'messageId' => $response['message']['msg'][0]['sms_id']
                    ));
                    $result->setMore('response', $response);
                }


            } elseif (isset($response['error'])) {
                $result->setError(new Error($this->_getErrorDescription($response['error']), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
                    'response' => $response
                ))));
            } else {
                $result->setError(new Error($this->_getErrorDescription((string)$response), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
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
         * Проверка баланса
         * @return Result
         */
        public function getBalance()
        {
            $result = new Result();
            $response = $this->_makeRequest('balance', Array());

            if (intval($response) >= 0) {
                $result->setResult(number_format(intval($response), 0, '.', ' ') . $this->_getMsg('SERVICE.CURRENCY'));
            } elseif (intval($response) < 0) {
                $result->setError(new Error($this->_getErrorDescription(intval($response)), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array('response' => $response)));
            } else {
                //приводим к строке
                $result->setError(new Error((string)$response, \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE));
            }
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


            $arMessageId = array();
            foreach ($arSms as $smsId => $arSmsMore) {
                if (!!$arSmsMore['PARAMS']['messageId']) {
                    $arMessageId[$arSmsMore['PARAMS']['messageId']] = $arSmsMore['ID'];
                } else {
                    $arError[] = new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $arResult[$smsId] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
                }
            }


            if (!empty($arMessageId)) {

                $response = $this->_makeRequest('message/status', array(
                    'id' => array_keys($arMessageId)
                ));

                if ($response['status']) {

                    foreach ($response['status']['msg'] as $msgStatus) {
                        $statusResult = new Result();
                        $statusResult->setMore('message_id', $msgStatus['sms_id']);

                        switch (\Bitrix\Main\Text\BinaryString::changeCaseToLower($msgStatus['sms_status'])) {
                            case 'queued':
                            case 'sent': {
                                $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                                break;
                            }
                            case 'delivered': {
                                $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                                break;
                            }
                            case 'failed':
                            case 'unknown':
                            case 'expired':
                            case 'rejected': {
                                $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_ERROR.' . \Bitrix\Main\Text\BinaryString::changeCaseToLower($msgStatus['sms_status'])));
                                break;
                            }
                        }

                        $arResult[$arMessageId[$msgStatus['sms_id']]] = $statusResult;
                    }

                } elseif ($response['error']) {
                    $arError[] = new Error($this->_getErrorDescription(intval($response['error'])), \Bxmaker\SmsNotice\SMS_STATUS_ERROR);

                    foreach ($arMessageId as $smsId) {
                        $arResult[$smsId] = new Result(new Error($this->_getErrorDescription(intval($response['error'])), \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
                    }
                } else {
                    foreach ($arMessageId as $smsId) {
                        $arResult[$smsId] = new Result(new Error((string)$response, \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE));
                    }
                }
            }


            $result->setMore('results', $arResult);
            $result->setMore('errors', (!empty($arError) ? $arError : null)); // ошибок нет

            return $result;
        }

        private function getPreparedRequestData($data)
        {
            return (Manager::isUTF() ? $data : Encoding::convertEncoding($data, LANG_CHARSET, 'UTF-8'));
        }

        private function getPreparedResponseData($data)
        {
            return (Manager::isUTF() ? $data : Encoding::convertEncoding($data, 'UTF-8', LANG_CHARSET));
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
         * Отправить запрос
         *
         * @param string $function
         * @param array $params
         *
         */
        protected function _makeRequest($method, array $params = array())
        {
            $generalParams = array(
                'login'    => $this->_getParam('user'),
                'user'     => $this->_getParam('user'),
                'password' => $this->_getParam('pwd'),
            );

            switch ($method) {
                case 'message/status': {
                    $url = 'https://userarea.sms-assistent.by/api/v1/json';

                    $arMsg = array();
                    foreach ($params['id'] as $id) {
                        $arMsg[] = array(
                            'sms_id' => $id
                        );
                    }

                    $generalParams = array_merge(array(
                        'command' => 'statuses',
                        'status'  => array(
                            'msg' => $arMsg
                        )
                    ), $generalParams);

                    $response = $this->oHttp->post($url, json_encode($this->getPreparedRequestData($generalParams)));
                    return $this->response = json_decode($this->getPreparedResponseData($response), true);


                    break;
                }
                case 'message/send': {
                    $url = 'https://userarea.sms-assistent.by/api/v1/json';
                    $generalParams = array_merge(array(
                        'command' => 'sms_send',
                        'message' => array(
                            'msg' => array(
                                array(
                                    'sender'    => $this->_getParam('sender'),
                                    'recipient' => $params['phone'],
                                    'sms_text'  => $params['text']
                                )
                            )
                        )
                    ), $generalParams);

                    $response = $this->oHttp->post($url, json_encode($this->getPreparedRequestData($generalParams)));
                    return $this->response = json_decode($this->getPreparedResponseData($response), true);

                    break;
                }
                case 'balance': {

                    $url = 'https://userarea.sms-assistent.by/api/v1/credits/plan';

                    $response = $this->oHttp->post($url, $this->getPreparedRequestData($generalParams));
                    return $this->response = json_decode($this->getPreparedResponseData($response), true);

                    break;
                }
                default: {
                    return false;
                }
            }
        }


        /*
         * Варианты ошибок, который можно переиначить
         */
        private function _getErrorDescription($error_code)
        {

            if (intval($error_code) < 0 && intval($error_code) > -16) {
                return $this->_getMsg('ERROR_DESCRIPTION.' . abs($error_code));
            } else {
                return $error_code;
            }


        }


    }
