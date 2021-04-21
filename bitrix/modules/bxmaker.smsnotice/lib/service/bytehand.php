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


Class bytehand
{

// bytehand.com
// 10 руб

    const REQUEST_SUCCESS = 'success';
    const REQUEST_ERROR = 'error';

    private $arParams = array(
        'user'      => '',
        'pwd'       => '',
        'sadr'      => '',
        'test_mode' => ''
    );

    private $oHttp = null;

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
        if(is_null($this->oHttp))
        {
            $this->oHttp =  new \Bitrix\Main\Web\HttpClient();
        }

        if (array_key_exists('ID', $arParams))
            $this->arParams['id'] = trim($this->_getPreparedStr($arParams['ID']));

        if (array_key_exists('KEY', $arParams))
            $this->arParams['key'] = trim($this->_getPreparedStr($arParams['KEY']));

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
        return GetMessage('bxmaker.smsnotice.bytehand.' . $code);
    }


    public function send($phone, $text, $arParams = array())
    {
        $result = new Result();
        $arParams['text'] = $this->_getPreparedStr($text);
        $arParams['to'] = $phone;

        $response = $this->_makeRequest('message/send', $arParams);

        if(isset($response['status']))
        {
            if($response['status'] === 0)
            {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                $result->setMore('params', array(
                    'messageId' => (isset($response['description']) ? trim($response['description']) : '')
                ));
            }
            else{
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getErrorDescription($response['status']));
            }
            $result->setMore('response', $response);
        }
        else
        {
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
    public function getBalance()
    {
        $result = new Result();
        $response = $this->_makeRequest('balance', Array());

        if (array_key_exists('status', $response)) {
            if ($response['status'] === 0) {
                $result->setResult(floatval((isset($response['description']) ? $response['description'] : 0)) . $this->_getMsg('SERVICE.CURRENCY'));
            } else {
                $result->setError(new Error($this->_getErrorDescription($response['status']), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array('response' => $response)));
            }

        } else {
            $result->setError(new Error($this->_getMsg('ERROR_BALANCE_EMPTY'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array('response' => $response)));
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


    // кодировка должна быть UTF-8
    private function _getPreparedStr($str)
    {
        /** @global \CMain $APPLICATION */
        global $APPLICATION;

        return (!Manager::isUTF() ? $APPLICATION->ConvertCharset($str, LANG_CHARSET, "UTF-8") : $str);
    }


    public function getParams()
    {

        return array(
            'ID'   => array(
                'NAME'      => $this->_getMsg('PARAMS.ID'),
                'NAME_HINT' => $this->_getMsg('PARAMS.ID.HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            ),
            'KEY'  => array(
                'NAME'      => $this->_getMsg('PARAMS.KEY'),
                'NAME_HINT' => $this->_getMsg('PARAMS.KEY.HINT'),
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
     * @return array
     */
    protected function _makeRequest($method, array $params = array())
    {


        $p = array(
            'id'  => $this->_getParam('id'),
            'key' => $this->_getParam('key'),
        );

        $url = 'http://bytehand.com:3800/balance?';

        switch ($method) {
            case 'message/status': {
                $url = 'http://bytehand.com:3800/status?';
                $p = array_merge(array(
                    'message' => $params['id']
                ), $p);
                break;
            }
            case 'message/send': {
                $url = 'http://bytehand.com:3800/send?';
                $p = array_merge(array(
                    'text' => $params['text'],
                    'to'   => $params['to'],
                    'from' => $this->_getParam('sadr'),
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
    private function _prepareAnswer($str)
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
    private function _messageStatus($messagesId)
    {
//        http://bxmaker.sms.ru/?panel=api&subpanel=method&show=sms/status
        $result = new Result();

        $response = $this->_makeRequest('message/status', array(
            'id' => $messagesId
        ));
        $result->setMore('response', $response);


        if(isset($response['status']))
        {
            if($response['status'] === 0)
            {
                switch($response['description'])
                {
                    case $this->_getMsg('MSG_STATUS_NEW'): {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        break;
                    }
                    case $this->_getMsg('MSG_STATUS_DELIVERED'): {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                        break;
                    }
                    case $this->_getMsg('MSG_STATUS_EXPIRED'): {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_EXPIRED_VALUE'));
                        break;
                    }
                    case $this->_getMsg('MSG_STATUS_DELETED'): {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_DELETED_VALUE'));
                        break;
                    }
                    case $this->_getMsg('MSG_STATUS_UNDELIVERABLE'): {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_UNDELIVERABLE_VALUE'));
                        break;
                    }
                    case $this->_getMsg('MSG_STATUS_ACCEPTED'): {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        break;
                    }
                    case $this->_getMsg('MSG_STATUS_REJECTED'): {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_REJECTED_VALUE'));
                        break;
                    }
                    default: {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_UNKNOWN_VALUE'));
                        break;
                    }
                }
            }
            else
            {
                $result->setError(new Error( $this->_getErrorDescription($response['status']), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'message_id' => $messagesId,
                    'response'   => $response
                )));
            }
        }
        else
        {
            $result->setError(new Error( $this->_getMsg('ERROR_STATUS_EMPTY'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                'message_id' => $messagesId,
                'response'   => $response
            )));
        }

        return $result;
    }

    /*
     * Варианты ошибок, который можно переиначить
     */
    private function _getErrorDescription($error_code)
    {
        switch ($error_code) {

            case $this->_getMsg('ERROR_DESCRIPTION_M1'): {
                return $this->_getMsg('ERROR_DESCRIPTION_M1_VALUE');
                break;
            }
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
             case $this->_getMsg('ERROR_DESCRIPTION_10'): {
                return $this->_getMsg('ERROR_DESCRIPTION_10_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_11'): {
                return $this->_getMsg('ERROR_DESCRIPTION_11_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_12'): {
                return $this->_getMsg('ERROR_DESCRIPTION_12_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_13'): {
                return $this->_getMsg('ERROR_DESCRIPTION_13_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_14'): {
                return $this->_getMsg('ERROR_DESCRIPTION_14_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_15'): {
                return $this->_getMsg('ERROR_DESCRIPTION_15_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_16'): {
                return $this->_getMsg('ERROR_DESCRIPTION_16_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_17'): {
                return $this->_getMsg('ERROR_DESCRIPTION_17_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_18'): {
                return $this->_getMsg('ERROR_DESCRIPTION_18_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_19'): {
                return $this->_getMsg('ERROR_DESCRIPTION_19_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_20'): {
                return $this->_getMsg('ERROR_DESCRIPTION_20_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_21'): {
                return $this->_getMsg('ERROR_DESCRIPTION_21_VALUE');
                break;
            }
             case $this->_getMsg('ERROR_DESCRIPTION_22'): {
                return $this->_getMsg('ERROR_DESCRIPTION_22_VALUE');
                break;
            }

            default: {
                return $error_code;
            }
        }
    }


}
