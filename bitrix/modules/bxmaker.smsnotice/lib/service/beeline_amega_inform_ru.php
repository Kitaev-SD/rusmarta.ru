<?

    namespace Bxmaker\SmsNotice\Service;

    use Bitrix\Main\Localization\Loc;
    use Bxmaker\SmsNotice\Error;
    use Bxmaker\SmsNotice\Manager;
    use Bxmaker\SmsNotice\Result;
    use Bxmaker\SmsNotice\Service;

    Loc::loadMessages(__FILE__);


    Class beeline_amega_inform_ru
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
            return (isset($this->arParams[$name]) ? trim($this->arParams[$name]) : trim($default_value));
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
            return GetMessage('bxmaker.smsnotice.beeline_amega_inform_ru.' . $code, $code);
        }


        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();
            $arParams['text'] = $this->_getPreparedStr($text);
            $arParams['to'] = $phone;

            $response = $this->_makeRequest('message/send', $arParams);

            if (isset($response['output']['#']['result'][0]['#']['sms'][0]['@']['id'])) {
                $result->setMore('params', array(
                    'messageId' => $response['output']['#']['result'][0]['#']['sms'][0]['@']['id']
                ));
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);

            } elseif (isset($response['output']['#']['errors'][0]['#']['error'][0]['#'])) {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $response['output']['#']['errors'][0]['#']['error'][0]['#']);
                $result->setMore('response', $response);

            } else {
                $result->setResult(\BXmaker\SmsNotice\SMS_STATUS_WAIT);
                $result->setMore('response', $response);
//                $result->setError(new Error($this->_getMsg('ERROR_SEND_EMPTY'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
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
            //$response = $this->_makeRequest('balance', Array());

            $result->setResult($this->_getMsg('SERVICE.CURRENCY'));
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

            if (isset($response['output']['#']['errors'][0]['#']['error'][0]['#'])) {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $response['output']['#']['errors'][0]['#']['error'][0]['#']);
                $result->setMore('response', $response);
            } elseif (isset($response['output']['#']['MESSAGES'][0]['#']['MESSAGE'][0]['#']['SMSSTC_CODE'][0]['#'])) {

                switch ($response['output']['#']['MESSAGES'][0]['#']['MESSAGE'][0]['#']['SMSSTC_CODE'][0]['#']) {
                    case 'queued': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        break;
                    }
                    case 'wait': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        break;
                    }
                    case 'accepted': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        break;
                    }
                    case 'delivered': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                        break;
                    }
                    case 'not_delivered': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_NOT_DELIVERED_VALUE'));
                        break;
                    }
                    case 'failed': {
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
            } elseif (isset($response['output']['#']['MESSAGES'][0]['#']['MESSAGE'][0]['#'])) {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
            } else {
                $result->setError(new Error($this->_getMsg('ERROR_STATUS_EMPTY'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'messageId' => $messagesId,
                    'response' => $response
                )));
            }

            return $result;
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
            /*
            Адрес обращения : http://beeline.amega-inform.ru/sendsms/ или https://beeline.amega-inform.ru/sendsms/
            Тип авторизации : PLAIN (открытым текстом)
            Формат входных данных : Content-Type: application/x-www-form-urlencoded (windows-1251)
            Результат : Content-Type: text/xml (UTF-8); content-encoding: gzip
            Login/password : совпадают с login/password на web-сайт http://beeline.amega-inform.ru
            Максимальная длина сообщений : 480 символов.
                    */

            $p = array(
                'user' => $this->_getParam('LOGIN'),
                'pass' => $this->_getParam('PASS'),
                'gzip' => 'none'
            );

            $url = 'http://beeline.amega-inform.ru/sendsms/';

            switch ($method) {
                case 'message/status': {
                    $p = array_merge(array(
                        'action' => 'status',
                        'sms_id' => $params['id'],
                        'smstype' => 'SENDSMS'
                    ), $p);
                    break;
                }
                case 'message/send': {
                    $p = array_merge(array(
                        'action' => 'post_sms',
                        'message' => $params['text'],
                        'target' =>  $params['to'],
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


            require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');


            $this->oHttp->setTimeout(3);
            $response = $this->_getPreparedAnswer($this->oHttp->post($url, $p));
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


        // кодировка должна быть windows-1251
        private
        function _getPreparedStr($str)
        {
            return (!Manager::isUTF() ? $str : \Bitrix\Main\Text\Encoding::convertEncoding($str, "UTF-8", 'windows-1251'));
        }


    }
