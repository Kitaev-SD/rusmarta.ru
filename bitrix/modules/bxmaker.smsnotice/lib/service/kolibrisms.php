<?

    namespace Bxmaker\SmsNotice\Service;

    use Bitrix\Main\Localization\Loc;
    use Bxmaker\SmsNotice\Error;
    use Bxmaker\SmsNotice\Manager;
    use Bxmaker\SmsNotice\Result;
    use Bxmaker\SmsNotice\Service;

    Loc::loadMessages(__FILE__);


    Class kolibrisms
    {

//http://kolibrisms.ru/
//5 смс на свой номер

        const REQUEST_SUCCESS = 'success';
        const REQUEST_ERROR = 'error';

        private $oHttp = null;

        private $arParams = array(
            'user' => '',
            'pwd' => '',
            'sadr' => ''
        );


        /**
         * Конструктор
         *
         * @param array $arParams
         * - string USER
         * - string PWD
         * - string SADR
         * - integer TEST_MODE
         */
        public function __construct($arParams = array())
        {
            if (is_null($this->oHttp)) {
                $this->oHttp = new \Bitrix\Main\Web\HttpClient();
            }

            if (array_key_exists('USER', $arParams))
                $this->arParams['user'] = trim($this->_getPreparedStr($arParams['USER']));

            if (array_key_exists('PWD', $arParams))
                $this->arParams['pwd'] = trim($this->_getPreparedStr($arParams['PWD']));

            if (array_key_exists('SADR', $arParams))
                $this->arParams['sadr'] = trim($this->_getPreparedStr($arParams['SADR']));
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
            return GetMessage('bxmaker.smsnotice.kolibrisms.' . $code);
        }


        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();
            $arParams['text'] = $this->_getPreparedStr($text);
            $arParams['to'] = $phone;

            $response = $this->_makeRequest('message/send', $arParams);

            if (preg_match('/Message_ID=([\d]+)/', trim($response), $match)) {

                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);

                $result->setMore('params', array(
                    'messageId' => (isset($match[1]) ? trim($match[1]) : '')
                ));
                $result->setMore('response', $response);
            } else {
                $result->setError(new Error($response, \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array_merge($arParams, array(
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

            if (preg_match('/^[\w:]+\s+([\d\.]+)/', $response, $match)) {
                $result->setResult(floatval($match[1]) . $this->_getMsg('SERVICE.CURRENCY'));
            } else {
                $result->setError(new Error($response, \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $response)));
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


        // кодировка должна быть WINDOWS-1251
        private function _getPreparedStr($str)
        {
            /** @global CMain $APPLICATION */
            global $APPLICATION;

            return (!Manager::isWin() ? $APPLICATION->ConvertCharset($str, LANG_CHARSET, 'Windows-1251') : $str);
        }

        // ответ в WINDOWS-1251 приходит, поэтому подготовим для внутреннего пользования
        private function _getFromUtf($str)
        {
            /** @global CMain $APPLICATION */
            global $APPLICATION;

            return (!Manager::isWin() ? $APPLICATION->ConvertCharset($str, 'Windows-1251', LANG_CHARSET) : $str);
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
         * @return array
         */
        protected function _makeRequest($method, array $params = array())
        {
            $p = array(
                'user' => $this->_getParam('user'),
                'pass' => $this->_getParam('pwd'),
                // 'partner_id' => '129315'
            );

            $url = '';

            switch ($method) {
                case 'message/status': {
                    $url = 'https://customer2.kolibrisms.ru/sms_status2.cgi?';
                    $p = array_merge(array(
                        'mess_id' => $params['id']
                    ), $p);
                    break;
                }
                case 'message/send': {
                    $url = 'https://customer2.kolibrisms.ru/sms.cgi?';
                    $p = array_merge(array(
                        'message' => $params['text'],
                        'phone' => $params['to']
                    ), $p);
                    if ($this->_strlen($this->_getParam('sadr'))) {
                        $p['from'] = $this->_getParam('sadr');
                    }
                    break;
                }
                case 'balance': {
                    $url = 'https://customer2.kolibrisms.ru/get_credit_info.cgi?';
                    break;
                }
                default: {
                return false;
                }
            }

            $url .= http_build_query($p);
            $response = $this->oHttp->get($url);

            return $this->response = $this->_getFromUtf($response);
        }

        private function _strlen($str)
        {
            return (Manager::isUTF() ? mb_strlen($str) : strlen($str));
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
//        http://bxmaker.sms.ru/?panel=api&subpanel=method&show=sms/status
            $result = new Result();

            $response = $this->_makeRequest('message/status', array(
                'id' => $messagesId
            ));
            $result->setMore('response', $response);


            if (preg_match('/Status:\s+([\w\s]+)$/', $response, $match)) {
                switch (trim($match[1])) {
                    case 'unknown': {
                        //Сообщение находится в нашей очереди
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        $result->setMore('error_description', '');
                        break;
                    }
                    case 'delivered': {
                        //Сообщение доставлено
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                        $result->setMore('error_description', '');
                        break;
                    }
                    case 'not delivered': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $response);
                        break;
                    }
                    default : {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $response);
                    }
                }

            } else {
                //Пользователь авторизован, но аккаунт не подтвержден (пользователь не ввел код, присланный в регистрационной смс)
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $response);
            }

            return $result;
        }

        /*
         * Варианты ошибок, который можно переиначить
         */
        private
        function _getErrorDescription($error_msg, $error_type = '')
        {
            if ($error_type == 'balance') {
                switch ($error_msg) {
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_100'): {
                        return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_100_VALUE');
                        break;
                    }

                    default: {
                    return $error_msg;
                    }
                }
            } else {

                switch ($error_msg) {
                    case $this->_getMsg('MSG_SEND_100'): {
                        return $this->_getMsg('MSG_SEND_100_VALUE');
                        break;
                    }

                    default: {
                    return $error_msg;
                    }
                }
            }

        }


    }
