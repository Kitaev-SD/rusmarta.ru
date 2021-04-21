<?

    namespace Bxmaker\SmsNotice\Service;

    use Bitrix\Main\Localization\Loc;
    use Bxmaker\SmsNotice\Error;
    use Bxmaker\SmsNotice\Manager;
    use Bxmaker\SmsNotice\Result;
    use Bxmaker\SmsNotice\Service;

    Loc::loadMessages(__FILE__);


    Class rocketsms_by
    {

        const REQUEST_SUCCESS = 'success';
        const REQUEST_ERROR = 'error';

        private $arParams = array(
            'LOGIN' => '',
            'PASS' => '',
            'FROM' => '',
        );

        private $oHttp = null;

        /**
         * Конструктор
         *
         * @param array $arParams
         * - string LOGIN
         * - string PASS
         * - string FROM
         */
        public function __construct($arParams = array())
        {
            if (is_null($this->oHttp)) {
                $this->oHttp = new \Bitrix\Main\Web\HttpClient();
            }

            if (array_key_exists('LOGIN', $arParams))
                $this->arParams['LOGIN'] = trim($this->_getPreparedStr($arParams['LOGIN']));

            if (array_key_exists('PASS', $arParams))
                $this->arParams['PASS'] = trim($this->_getPreparedStr($arParams['PASS']));

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
            return GetMessage('bxmaker.smsnotice.rocketsms_by.' . $code, $code);
        }


        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();
            $arParams['text'] = $this->_getPreparedStr($text);
            $arParams['to'] = $phone;


            $response = $this->_makeRequest('message/send', $arParams);

            if (isset($response['status'])) {

                $result->setMore('params', array(
                    'messageId' => $response['id']
                ));

                switch ($response['status']) {
                    case 'QUEUED': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        break;
                    }
                    case 'SENT': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        break;
                    }
                    case 'DELIVERED': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                        break;
                    }
                    case 'FAILED': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_FAILED_VALUE'));
                        break;
                    }
                    default: {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_UNKNOWN_VALUE'));
                        break;
                    }
                }
            }
            elseif (isset($response['error'])) {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg($response['error']));
                $result->setMore('response', $response);

            } else {
                $result->setError(new Error($this->_getMsg('ERROR_SEND_EMPTY'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
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
        public
        function getBalance()
        {
            $result = new Result();
            $response = $this->_makeRequest('balance', Array());


            if (array_key_exists('credits', $response)) {
                $result->setResult(floatval((isset($response['credits']) ? $response['credits'] : 0)) . $this->_getMsg('SERVICE.CURRENCY'));
            } elseif (array_key_exists('error', $response)) {
                $result->setError(new Error($this->_getMsg($response['error']), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array('response' => $response)));
            } else {
                $result->setError(new Error($this->_getMsg('ERROR_BALANCE_EMPTY'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array('response' => $response)));
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


            foreach ($arSms as $smsId => $arSmsMore) {
                if (isset($arSmsMore['PARAMS']['messageId'])) {

                    $res = $this->_messageStatus($arSmsMore['PARAMS']['messageId']);

                    if ($res->isSuccess()) {
                        $arResult[$smsId] = $res;
                    } else {
                        $arError = array_merge($arError, $res->getErrors());
                        $arResult[$smsId] = $res;
                    }
                } else {
                    $arError[] = new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $arResult[$smsId] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
                }
            }

            $result->setMore('results', $arResult);
            $result->setMore('errors', (!empty($arError) ? $arError : null)); // ошибок нет

            return $result;
        }


        // кодировка должна быть UTF-8
        private
        function _getPreparedStr($str)
        {
            /** @global \CMain $APPLICATION */
            global $APPLICATION;

            return (!Manager::isUTF() ? $APPLICATION->ConvertCharset($str, LANG_CHARSET, "UTF-8") : $str);
        }


        public
        function getParams()
        {

            return array(
                'LOGIN' => array(
                    'NAME' => $this->_getMsg('PARAMS.LOGIN'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.LOGIN.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                ),
                'PASS' => array(
                    'NAME' => $this->_getMsg('PARAMS.PASS'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.PASS.HINT'),
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


            $p = array(
                'username' => $this->_getParam('LOGIN'),
                'password' => md5($this->_getParam('PASS'))
            );

            $url = 'http://api.rocketsms.by/simple/balance?';

            switch ($method) {
                case 'message/status': {
                    $url = 'http://api.rocketsms.by/simple/status?';
                    $p = array_merge(array(
                        'id' => $params['id']
                    ), $p);
                    break;
                }
                case 'message/send': {
                    $url = 'http://api.rocketsms.by/simple/send?';
                    $p = array_merge(array(
                        'text' => $params['text'],
                        'phone' => $params['to'],
                        'sender' => $this->_getParam('FROM'),
                    ), $p);
                    break;
                }
                case 'balance': {
                    break;
                }
                default: {
                    return false;
                }
            }

            $url .= http_build_query($p);

            $response = $this->_prepareAnswer($this->oHttp->get($url));

            return $this->response = (array)@json_decode($response, true);
        }

        // ответ в utf
        private
        function _prepareAnswer($str)
        {
            /** @global CMain $APPLICATION */
            global $APPLICATION;

            return (!Manager::isUTF() ? $APPLICATION->ConvertCharset($str, "UTF-8", LANG_CHARSET) : $str);
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

            $result = new Result();

            $response = $this->_makeRequest('message/status', array(
                'id' => $messagesId
            ));
            $result->setMore('response', $response);

            if (isset($response['status'])) {

                switch ($response['status']) {
                    case 'QUEUED': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        break;
                    }
                    case 'SENT': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        break;
                    }
                    case 'DELIVERED': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                        break;
                    }
                    case 'FAILED': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_FAILED_VALUE'));
                        break;
                    }
                    default: {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_UNKNOWN_VALUE'));
                        break;
                    }
                }
            }
            elseif (isset($response['error'])) {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg($response['error']));
                $result->setMore('response', $response);
            }
            else {
                $result->setError(new Error($this->_getMsg('ERROR_STATUS_EMPTY'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'messageId' => $messagesId,
                    'response' => $response
                )));
            }

            return $result;
        }


    }
