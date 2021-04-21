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


Class smsc
{

//http://www.smsc.ru/
//15 рублей после регистрации

    const REQUEST_SUCCESS = 'success';
    const REQUEST_ERROR = 'error';

    private $arParams = array(
        'user'      => '',
        'pwd'       => '',
        'sender'    => '',
        'test_mode' => ''
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
        if(is_null($this->oHttp))
        {
            $this->oHttp =  new \Bitrix\Main\Web\HttpClient();
        }

        if (array_key_exists('USER', $arParams))
            $this->arParams['user'] = trim($this->_getPreparedStr($arParams['USER']));

        if (array_key_exists('PWD', $arParams))
            $this->arParams['pwd']  = trim( $this->_getPreparedStr($arParams['PWD']));

        if (array_key_exists('SENDER', $arParams))
            $this->arParams['sender'] = trim($this->_getPreparedStr($arParams['SENDER']));
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
        return GetMessage('bxmaker.smsnotice.smsc.' . $code);
    }


    public function send($phone, $text, $arParams = array())
    {
        $result = new Result();
        $arParams['mes'] = $this->_getPreparedStr($text);
        $arParams['phones'] = $phone;

        $response = $this->_makeRequest('message/send', $arParams);

        if (isset($response['id'])) {

            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
            $result->setMore('params', array(
                'messageId' => $response['id']
            ));
            $result->setMore('response', $response);

        }
        elseif(isset($response['error_code']))
        {
            $result->setError(new Error($this->_getErrorDescription($response['error_code']), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
                'response' => $response
            ))));
        }
        else {
            $result->setError(new Error($this->_getErrorDescription((string) $response), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array_merge($arParams, array(
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

        if (isset($response['balance'])) {
            $result->setResult(floatval($response['balance']) . $this->_getMsg('SERVICE.CURRENCY'));
        } elseif (isset($response['error_code'])) {
            $result->setError(new Error($this->_getErrorDescription($response['error_code'], 'balance'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array('response' => $response)));
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



        foreach ($arSms as $smsId => $arSmsMore) {
            if (isset($arSmsMore['PARAMS']['messageId'])) {

                $res = $this->_messageStatus($arSmsMore['PARAMS']['messageId'], $arSmsMore['PHONE']);

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


    // кодировка должна быть WIN
    private function _getPreparedStr($str)
    {
        /** @global CMain $APPLICATION */
        global $APPLICATION;

        return (Manager::isWin() ? $str : $APPLICATION->ConvertCharset($str, LANG_CHARSET, 'Windows-1251'));
    }

    // ответ в WIN приодит, поэтому подготовим для внутреннего пользования
    private function _getFromWin($str)
    {
        /** @global CMain $APPLICATION */
        global $APPLICATION;

        return (Manager::isWin() ? $str : $APPLICATION->ConvertCharset($str, 'Windows-1251', LANG_CHARSET));
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
     * @param array  $params
     *
     * @return stdClass
     */
    protected function _makeRequest($method, array $params = array())
    {
        $p = array(
            'login' => $this->_getParam('user'),
            'psw'   => $this->_getParam('pwd'),
            'pp'    => '413493',
            'fmt'   => 3
        );

        $url = 'http://smsc.ru/sys/balance.php?';

        switch ($method) {
            case 'message/status': {
                $url = 'http://smsc.ru/sys/status.php?';
                $p = array_merge(array(
                    'id' => $params['id'],
                    'phone' => $params['phone']
                ), $p);
                break;
            }
            case 'message/send': {
                $url = 'http://smsc.ru/sys/send.php?';
                $p = array_merge(array(
                    'mes'    => $params['mes'],
                    'phones' => $params['phones']
                ), $p);

                if ($this->strlen($this->_getParam('sender')) > 0) {
                    $p['sender'] = $this->_getParam('sender');
                }

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

        $response = $this->_getFromWin($this->oHttp->get($url));

        return $this->response = json_decode($response, true);
    }

    private function strlen($str)
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
    private function _messageStatus($messagesId, $phone)
    {

        $result = new Result();

        $response = $this->_makeRequest('message/status', array(
            'id' => $messagesId,
            'phone' => $phone
        ));
        $result->setMore('response', $response);

        if(isset($response['status']))
        {
            // успешно
            switch ($response['status']) {
                case $this->_getMsg('MSG_STATUS_M3'): {
                    //не найдено
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $this->_getMsg('MSG_STATUS_M3_VALUE'));

                    break;
                }
                case $this->_getMsg('MSG_STATUS_M1'): {
                    //Ожидает отправки
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    break;
                }
                case $this->_getMsg('MSG_STATUS_0'): {
                    //Передано оператору
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    break;
                }
                case $this->_getMsg('MSG_STATUS_1'): {
                    //Доставлено
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                    break;
                }
                case $this->_getMsg('MSG_STATUS_3'): {
                    //Просрочено
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $this->_getMsg('MSG_STATUS_3_VALUE'));

                    break;
                }
                case $this->_getMsg('MSG_STATUS_20'): {
                    //Невозможно доставить
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $this->_getMsg('MSG_STATUS_20_VALUE'));

                    break;
                }
                case $this->_getMsg('MSG_STATUS_22'): {
                    //Неверный номер
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $this->_getMsg('MSG_STATUS_22_VALUE'));

                    break;
                }
                case $this->_getMsg('MSG_STATUS_23'): {
                    //Запрещено
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $this->_getMsg('MSG_STATUS_22_VALUE'));

                    break;
                }
                case $this->_getMsg('MSG_STATUS_24'): {
                    //Недостаточно средств
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $this->_getMsg('MSG_STATUS_24_VALUE'));

                    break;
                }
                case $this->_getMsg('MSG_STATUS_25'): {
                    //Недоступный номер
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $this->_getMsg('MSG_STATUS_25_VALUE'));

                    break;
                }
                default: {
                    //не найдено
                    $result->setError(new Error( $this->_getErrorDescription($response['error_code'], 'status'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                        'message_id' => $messagesId,
                        'response'   => $response
                    )));
                    break;
                }
            }

        }
        elseif(isset($response['error_code']))
        {
            $result->setError(new Error( $this->_getErrorDescription($response['error_code'], 'status'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                'message_id' => $messagesId,
                'response'   => $response
            )));
        }
        else
        {
            $result->setError(new Error( $this->_getErrorDescription((string) $response, 'status'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                'message_id' => $messagesId,
                'response'   => $response
            )));
        }

        return $result;
    }

    /*
     * Варианты ошибок, который можно переиначить
     */
    private function _getErrorDescription($error_code, $type = '')
    {
        switch ($type) {
            case 'balance': {
                switch ($error_code) {
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_1'): {
                        return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_1_VALUE');
                        break;
                    }
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_2'): {
                        return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_2_VALUE');
                        break;
                    }
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_4'): {
                        return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_4_VALUE');
                        break;
                    }
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_9'): {
                        return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_9_VALUE');
                        break;
                    }
                    default: {
                    return $error_code;
                    }
                }

                break;
            }
            case 'status': {
                switch ($error_code) {
                    case $this->_getMsg('ERROR_DESCRIPTION_STATUS_1'): {
                        return $this->_getMsg('ERROR_DESCRIPTION_STATUS_1_VALUE');
                        break;
                    }
                    case $this->_getMsg('ERROR_DESCRIPTION_STATUS_2'): {
                        return $this->_getMsg('ERROR_DESCRIPTION_STATUS_2_VALUE');
                        break;
                    }
                    case $this->_getMsg('ERROR_DESCRIPTION_STATUS_4'): {
                        return $this->_getMsg('ERROR_DESCRIPTION_STATUS_4_VALUE');
                        break;
                    }
                    case $this->_getMsg('ERROR_DESCRIPTION_STATUS_5'): {
                        return $this->_getMsg('ERROR_DESCRIPTION_STATUS_5_VALUE');
                        break;
                    }
                    case $this->_getMsg('ERROR_DESCRIPTION_STATUS_9'): {
                        return $this->_getMsg('ERROR_DESCRIPTION_STATUS_9_VALUE');
                        break;
                    }
                    default: {
                    return $error_code;
                    }
                }

                break;
            }
            default: {
            switch ($error_code) {
                case $this->_getMsg('ERROR_DESCRIPTION_1'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_1_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_2'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_2_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_3'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_3_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_4'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_4_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_5'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_5_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_6'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_6_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_7'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_7_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_8'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_8_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_9'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_9_VALUE');
                    break;
                }
                default: {
                return $error_code;
                }
            }
            }
        }
    }


}
