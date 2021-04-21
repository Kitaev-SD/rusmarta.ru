<?

    namespace Bxmaker\SmsNotice\Service;

    use Bitrix\Main\Localization\Loc;
    use Bxmaker\SmsNotice\Error;
    use Bxmaker\SmsNotice\Manager;
    use Bxmaker\SmsNotice\Result;
    use Bxmaker\SmsNotice\Service;

    Loc::loadMessages(__FILE__);


    Class alphasms_ua
    {

        const REQUEST_SUCCESS = 'success';
        const REQUEST_ERROR = 'error';

        private $arParams = array(
            'APIKEY' => '',
            'FROM' => '',
        );

        private $oHttp = null;

        /**
         * Конструктор
         *
         * @param array $arParams
         * - string APIKEY
         * - string FROM
         */
        public function __construct($arParams = array())
        {
            if (is_null($this->oHttp)) {
                $this->oHttp = new \Bitrix\Main\Web\HttpClient();
            }

            if (array_key_exists('APIKEY', $arParams))
                $this->arParams['APIKEY'] = trim($this->_getPreparedStr($arParams['APIKEY']));

            if (array_key_exists('FROM', $arParams))
                $this->arParams['FROM'] = trim($this->_getPreparedStr($arParams['FROM']));
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
            return (isset($this->arParams[$name]) ? trim($this->arParams[$name]) : trim($default_value));
        }


        /**
         * Сообщения
         *
         * @param $code
         *
         * @return mixed|string
         */
        private function _getMsg($code, $arReplace = array())
        {
            return GetMessage('bxmaker.smsnotice.alphasms_ua.' . $code, $arReplace);
        }


        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();
            $arParams['text'] = $this->_getPreparedStr($text);
            $arParams['to'] = $phone;

            $response = $this->_makeRequest('message/send', $arParams);

            if (isset($response['package']['#']['message']['0']['#']['msg'][0]['#'])) {

                if (intval($response['package']['#']['message']['0']['#']['msg'][0]['#']) == 1) {
                    $result->setMore('params', array(
                        'messageId' => $response['package']['#']['message']['0']['#']['msg'][0]['@']['sms_id']
                    ));
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                } else {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $this->getError($response['package']['#']['message']['0']['#']['msg'][0]['#'], true, true));
                    $result->setMore('response', $response);
                }
            } elseif (isset($response['package']['#']['error']['0']['#'])) {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->getError($response['package']['#']['error']['0']['#'], false, true));
                $result->setMore('response', $response);
            } else {
                $result->setResult(\BXmaker\SmsNotice\SMS_STATUS_WAIT);
                $result->setMore('response', $response);
            }


            return $result;
        }

        public function sendPack($arPack)
        {
            //		PackItem - array(
            //			   "phone"    => $arItem['PHONE'],
            //			  "clientId" => $id,
            //			  "text"     => $arItem['TEXT'],
            //			  "sender"   => $arItem['SENDER']
            //		)
            $arMsg = array();
            $result = new Result();
            $response = $this->_makeRequest('message/sendPack', $arPack);

            if (isset($response['package']['#']['message']['0']['#']['msg'][0]['#'])) {

                foreach($response['package']['#']['message']['0']['#']['msg'] as $msgResponseIndex => $msgResponse)
                {
                    $result = new Result();
                    $clientId = $arPack['messages'][$msgResponseIndex];

                    if (intval($response['package']['#']['message']['0']['#']['msg'][$msgResponseIndex]['#']) == 1) {
                        $result->setMore('params', array(
                            'messageId' => $response['package']['#']['message']['0']['#']['msg'][$msgResponseIndex]['@']['sms_id']
                        ));
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    } else {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->getError($response['package']['#']['message']['0']['#']['msg'][$msgResponseIndex]['#'], true, true));
                        $result->setMore('response', $response);
                    }
                    $arMsg[$clientId] = $result;
                }

            } elseif (isset($response['package']['#']['error']['0']['#'])) {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->getError($response['package']['#']['error']['0']['#'], false, true));
                $result->setMore('response', $response);
            } else {
                $result->setResult(\BXmaker\SmsNotice\SMS_STATUS_WAIT);
                $result->setMore('response', $response);
            }


            $result->setResult(array('messages' => $arMsg));

            return $result;
        }


        /**
         * Проверка баланса
         * @return Result
         */
        public
        function getBalance()
        {
            $result = new Result();
            $response = $this->_makeRequest('balance', Array());


            if (isset($response['package']['#']['balance']['0']['#']['amount'][0]['#'])) {
                $result->setResult($this->_getMsg('SERVICE.BALANCE', array(
                    '#AMOUNT#' => floatval($response['package']['#']['balance']['0']['#']['amount'][0]['#']),
                    '#CURRENCY#' => $response['package']['#']['balance']['0']['#']['currency'][0]['#'],
                )));
            } else {
                $result->setError($this->getError($response['package']['#']['error']['0']['#']));
            }

            return $result;
        }

        /**
         * Проверяет статус сообщения поидее
         */
        public
        function notice()
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
        public
        function agent($arSms)
        {
            $result = new Result(true);
            $arResult = array();
            $arError = array();

            $arMsgId = array();

            foreach ($arSms as $smsId => $arSmsMore) {
                if (isset($arSmsMore['PARAMS']['messageId'])) {
                    $arMsgId[$arSmsMore['PARAMS']['messageId']] = $smsId;
                } else {
                    $arError[] = new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $arResult[$smsId] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
                }
            }

            if(count($arMsgId)) {
                $response = $this->_messageStatus(array_keys($arMsgId));
                foreach($response as $messageId => $msgResult) {
                    $arResult[$arMsgId[$messageId]] = $msgResult;
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
        private
        function _messageStatus($messagesId)
        {

            $arReturn = array();

            $response = $this->_makeRequest('message/status', array(
                'id' => $messagesId
            ));

            if (isset($response['package']['#']['status']['0']['#']['msg'][0]['#'])) {

                foreach($response['package']['#']['status']['0']['#']['msg'] as $msgResponseIndex => $msgResponse)
                {
                    $result = new Result();
                    $clientId = $messagesId[$msgResponseIndex];

                    if (intval($response['package']['#']['status']['0']['#']['msg'][$msgResponseIndex]['#']) > 0) {

                        $statusCode = $response['package']['#']['status']['0']['#']['msg'][$msgResponseIndex]['#'];

                        switch ($response['package']['#']['status']['0']['#']['msg'][$msgResponseIndex]['#']) {
                            case '100':
                                {
                                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                                    break;
                                }
                            case '102':
                                {
                                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                                    break;
                                }
                            case '101':
                            case '103':
                            case '104':
                            case '105':
                            case '106':
                            case '107':
                            case '108':
                            case '109':
                            case '110':
                            case '111':
                            case '112':
                            case '113':
                                {
                                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                    $result->setMore('error_description', $this->_getMsg('MSG_STATUS.'.$statusCode));
                                    break;
                                }
                            default:
                                {
                                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                    $result->setMore('error_description', $this->_getMsg('MSG_STATUS.0'));
                                    break;
                                }
                        }

                        $result->setMore('params', array(
                            'messageId' => $response['package']['#']['message']['0']['#']['msg'][$msgResponseIndex]['@']['sms_id']
                        ));
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    } else {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->getError($response['package']['#']['message']['0']['#']['msg'][$msgResponseIndex]['#'], true, true));
                        $result->setMore('response', $response);
                    }
                    $arReturn[$clientId] = $result;
                }

            } elseif (isset($response['package']['#']['error']['0']['#'])) {
                foreach($messagesId as $msgId) {
                    $result = new Result();
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $this->getError($response['package']['#']['error']['0']['#'], false, true));
                    $arReturn[$msgId] = $result;
                }
            } else {
                foreach($messagesId as $msgId) {
                    $result = new Result();
                    $result->setError(new Error($this->_getMsg('MSG_STATUS.0'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                        'messageId' => $messagesId
                    )));
                    $arReturn[$msgId] = $result;
                }
            }

            return $arReturn;
        }


        public
        function getParams()
        {

            return array(
                'APIKEY' => array(
                    'NAME' => $this->_getMsg('PARAMS.APIKEY'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.APIKEY.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                ),
                'FROM' => array(
                    'NAME' => $this->_getMsg('PARAMS.FROM'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.FROM.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                )
            );
        }

        /**
         * Описание сервиса что куда тывать, для страницы параметров сервиса
         * @return mixed|string
         */
        public
        function getDescription()
        {
            return $this->_getMsg('DESCRIPTION');
        }


        /**
         * Отправить запрос
         *
         * @param string $function
         * @param array $params
         *
         * @return array
         */
        protected
        function _makeRequest($method, array $params = array())
        {


            $str = '<?xml version="1.0" encoding="utf-8" ?><package key="' . $this->arParams['APIKEY'] . '">';

            $url = 'https://alphasms.ua/api/xml.php';

            switch ($method) {
                case 'message/status':
                    {
                        $str .= '<status>';
                        foreach ($params['id'] as $smsId) {
                            $str .= '<msg sms_id="' . $smsId . '"/>';
                        }
                        $str .= '</status>';
                        break;
                    }
                case 'message/send':
                    {
                        $str .= '<message>';
                        $str .= '<msg recipient="' . $params['to'] . '" sender="' . $this->_getParam('FROM') . '" type="0">' . $params['text'] . '</msg>';
                        $str .= '</message>';

                        break;
                    }
                case 'message/sendPack':
                    {
                        $str .= '<message>';
                        foreach ($params as $message) {
                            $str .= '<msg recipient="' . $message['phone'] . '" sender="' . $message['sender'] . '" type="0">' . $message['text'] . '</msg>';

                        }
                        $str .= '</message>';

                        break;
                    }
                case 'balance':
                    {

                        $str .= '<balance></balance>';
                        break;
                    }
                default:
                    {
                        return false;
                    }
            }

            $str .= '</package>';

            require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');

            $this->oHttp->setTimeout(3);
            $response = $this->_getPreparedAnswer($this->oHttp->post($url, $str));
            $response = str_replace(array('<![CDATA[', ']]>'), '', $response);

            $objXML = new \CDataXML();
            $objXML->LoadString($response);
            $arData = $objXML->GetArray();

            return $arData;
        }


        // ответ в UTF-8
        private
        function _getPreparedAnswer($str)
        {
            return (!Manager::isUTF() ? \Bitrix\Main\Text\Encoding::convertEncoding($str, "UTF-8", 'windows-1251') : $str);
        }


        // кодировка должна быть UTF-8
        private
        function _getPreparedStr($str)
        {
            return (!Manager::isUTF() ? \Bitrix\Main\Text\Encoding::convertEncoding($str, 'windows-1251', "UTF-8") : $str);
        }

        private function getError($errorCodeRaw, $bSmsSendError = false, $bText = false)
        {
            $errorCode = intval($errorCodeRaw);

            if ($bSmsSendError) {
                switch ($errorCode) {
                    case '200':
                    case '201':
                    case '202':
                    case '203':
                    case '204':
                    case '206':
                    case '207':
                    case '208':
                    case '211':
                        {
                            if ($bText) {
                                return $this->_getMsg('ERROR_1.' . $errorCode);
                            }
                            return new Error($this->_getMsg('ERROR_1.' . $errorCode), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $errorCodeRaw));
                            break;
                        }
                }
            } else {
                switch ($errorCode) {
                    case '200':
                    case '201':
                    case '202':
                    case '205':
                    case '209':
                    case '210':
                    case '212':
                        {
                            if ($bText) {
                                return $this->_getMsg('ERROR_' . $errorCode);
                            }
                            return new Error($this->_getMsg('ERROR_' . $errorCode), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $errorCodeRaw));
                            break;
                        }
                }
            }

            if ($bText) {
                return $this->_getMsg('ERROR_0');
            }

            return new Error($this->_getMsg('ERROR_0'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $errorCodeRaw));

        }

    }
