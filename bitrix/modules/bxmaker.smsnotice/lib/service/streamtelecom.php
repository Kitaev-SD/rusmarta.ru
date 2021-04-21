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


	Class streamtelecom
	{

		//http://www.stream-telecom.ru/
		//10 рублей после регистрации

		const REQUEST_SUCCESS = 'success';
		const REQUEST_ERROR = 'error';

		private $arParams
			= array(
				'user'      => '',
				'pwd'       => '',
				'sadr'      => '',
				'test_mode' => ''
			);

		private $oHttp = null;


		/**
		 *  онструктор
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

			if (array_key_exists('USER', $arParams)) {
				$this->arParams['user'] = $this->getPreparedStr($arParams['USER']);
			}

			if (array_key_exists('PWD', $arParams)) {
				$this->arParams['pwd'] = $this->getPreparedStr($arParams['PWD']);
			}

			if (array_key_exists('SADR', $arParams)) {
				$this->arParams['sadr'] = $this->getPreparedStr($arParams['SADR']);
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
			return GetMessage('bxmaker.smsnotice.streamtelecom.' . $code);
		}


		public function send($phone, $text, $arParams = array())
		{

			$arParams['text'] = $this->getPreparedStr($text);
			$arParams['dadr'] = $phone;

			$response = $this->makeRequest('message/send', $arParams);

			$result = new Result();
			if (intval($response)) {

				$result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
				$result->setMore('params', array(
					'messageId' => intval($response)
				));
				$result->setMore('response', $response);

			}
			else {
				$result->setError(new Error($this->_getErrorDescription($response), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
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
		 * ѕроверка баланса
		 * @return Result
		 */
		public function getBalance()
		{
			$result   = new Result();
			$response = $this->makeRequest('balance', Array());

			if (floatval($response)) {
				$result->setResult(floatval($response) . $this->_getMsg('SERVICE.CURRENCY'));
			}
			else {
				$result->setError(new Error($this->_getErrorDescription($response), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE));
			}
			return $result;
		}

		/**
		 * ѕровер€ет статус сообщени€ поидее
		 */
		public function notice()
		{
			return new Result();
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
			$result   = new Result(true);
			$arResult = array();
			$arError  = array();


			foreach ($arSms as $smsId => $arSmsMore) {
				if (isset($arSmsMore['PARAMS']['messageId'])) {

					$res = $this->messageStatus($arSmsMore['PARAMS']['messageId']);

					if ($res->isSuccess()) {
						$arResult[$smsId] = $res;
					}
					else {
						$arError          = array_merge($arError, $res->getErrors());
						$arResult[$smsId] = $res;
					}
				}
				else {
					$arError[]        = new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR);
					$arResult[$smsId] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
				}
			}

			$result->setMore('results', $arResult);
			$result->setMore('errors', (!empty($arError) ? $arError : null)); // ошибок нет

			return $result;
		}


		// кодировка должна быть UTF-8
		private function getPreparedStr($str)
		{
			/** @global CMain $APPLICATION */
			global $APPLICATION;

			return (!Manager::isUTF() ? $APPLICATION->ConvertCharset($str, LANG_CHARSET, "UTF-8") : $str);
		}

		// ответ в UTF-8 приодит, поэтому подготовим дл€ внутреннего пользовани€
		private function _getFromUtf($str)
		{
			/** @global CMain $APPLICATION */
			global $APPLICATION;

			return (!Manager::isUTF() ? $APPLICATION->ConvertCharset($str, "UTF-8", LANG_CHARSET) : $str);
		}

		public function getParams()
		{

			return array(
				'USER' => array(
					'NAME'      => $this->_getMsg('PARAMS.USER'),
					'NAME_HINT' => $this->_getMsg('PARAMS.USER.HINT'),
					'TYPE'      => 'STRING',
					'VALUE'     => ''
				),
				'PWD'  => array(
					'NAME'      => $this->_getMsg('PARAMS.PWD'),
					'NAME_HINT' => $this->_getMsg('PARAMS.PWD.HINT'),
					'TYPE'      => 'STRING',
					'VALUE'     => ''
				),
				'SADR' => array(
					'NAME'      => $this->_getMsg('PARAMS.SADR'),
					'NAME_HINT' => $this->_getMsg('PARAMS.SADR.HINT'),
					'TYPE'      => 'STRING',
					'VALUE'     => ''
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
		 * ќтправить запрос
		 *
		 * @param string $function
		 * @param array  $params
		 *
		 * @return stdClass
		 */
		protected function makeRequest($method, array $params = array())
		{
			$p = array(
				'user' => $this->_getParam('user'),
				'pwd'  => $this->_getParam('pwd')
			);


			switch ($method) {
				case 'message/status': {
					$p = array_merge(array(
						'smsid' => $params['smsid']
					), $p);
					break;
				}
				case 'message/send': {
					$p = array_merge(array(
						'sadr' => $this->_getParam('sadr'),
						'text' => $params['text'],
						'dadr' => $params['dadr']
					), $p);
					break;
				}
				case 'balance': {
					$p = array_merge(array(
						'balance' => 1
					), $p);
					break;
				}
				default: {
					return false;
				}
			}

			$url      = 'http://gateway.api.sc/get/?' . http_build_query($p);
			$response = $this->oHttp->get($url);

			return $this->response = $this->_getFromUtf($response);
		}


		/**
		 * ѕроверить статус доставки сообщений
		 *
		 * @param string $messagesId
		 *
		 * @return boolean|array
		 */
		private function messageStatus($messagesId)
		{

			$result = new Result();

			$response = $this->makeRequest('message/status', array(
				'smsid' => $messagesId
			));

			$result->setMore('response', $response);

			switch ($response) {
				case $this->_getMsg('MSG_STATUS1'): {
					//не доставлено
					$result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
					$result->setMore('error_description', $this->_getMsg('MSG_STATUS1_VALUE'));

					break;
				}
				case $this->_getMsg('MSG_STATUS2'): {
					//доставлено
					$result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
					break;
				}
				case $this->_getMsg('MSG_STATUS3'): {
					//просрочено
					$result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
					$result->setMore('error_description', $this->_getMsg('MSG_STATUS3_VALUE'));
					break;
				}
				case $this->_getMsg('MSG_STATUS4'): {
					//отправлено
					$result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
					break;
				}
				case $this->_getMsg('MSG_STATUS5'): {
					// msg_id не верное
					//                $result->setError(new Error($response, \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
					//                    'message_id' => $messagesId,
					//                    'response'   => $response
					//                )));

					$result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
					$result->setMore('error_description', $this->_getMsg('MSG_STATUS3_VALUE'));

					break;
				}
				default: {
					$result->setError(new Error($response, \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
						'message_id' => $messagesId,
						'response'   => $response
					)));
				}
			}

			return $result;
		}

		/*
		 * ¬арианты ошибок, который можно переиначить
		 */
		private function _getErrorDescription($error_msg)
		{
			switch ($error_msg) {
				case $this->_getMsg('ERROR_DESCRIPTION1'): {
					return $this->_getMsg('ERROR_DESCRIPTION1_VALUE');
					break;
				}
				case $this->_getMsg('ERROR_DESCRIPTION2'): {
					return $this->_getMsg('ERROR_DESCRIPTION2_VALUE');
					break;
				}
				case $this->_getMsg('ERROR_DESCRIPTION3'): {
					return $this->_getMsg('ERROR_DESCRIPTION3_VALUE');
					break;
				}
				case $this->_getMsg('ERROR_DESCRIPTION4'): {
					return $this->_getMsg('ERROR_DESCRIPTION4_VALUE');
					break;
				}
				case $this->_getMsg('ERROR_DESCRIPTION5'): {
					return $this->_getMsg('ERROR_DESCRIPTION5_VALUE');
					break;
				}
				case $this->_getMsg('ERROR_DESCRIPTION6'): {
					return $this->_getMsg('ERROR_DESCRIPTION6_VALUE');
					break;
				}
				case $this->_getMsg('ERROR_DESCRIPTION7'): {
					return $this->_getMsg('ERROR_DESCRIPTION7_VALUE');
					break;
				}
				default: {
					return $error_msg;
				}
			}
		}


	}
