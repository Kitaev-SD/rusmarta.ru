<?

namespace Bxmaker\SmsNotice\Service;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bxmaker\SmsNotice\Error;
use Bxmaker\SmsNotice\ManagerTable;
use Bxmaker\SmsNotice\Result;
use Bxmaker\SmsNotice\Service;

Loc::loadMessages(__FILE__);


Class mainsms
{

    const REQUEST_SUCCESS = 'success';
    const REQUEST_ERROR = 'error';

    private $module_id = 'bxmaker.smsnotice';
    private $oHttp = null;

    protected
        $project = null,
        $key = null,
        $from = '',
        $testMode = false,
        $url = 'mainsms.ru/api/mainsms',
        $useSSL = false,
        $response = null;



    /**
     * Конструктор
     *
     * @param array $arParams
     * - string $project
     * - string $key
     * - string $useSSL
     * - integer $testMode
     */
    public function __construct($arParams = array())
    {

        if(is_null($this->oHttp))
        {
            $this->oHttp =  new \Bitrix\Main\Web\HttpClient();
        }

        if (array_key_exists('PROJECT', $arParams))
            $this->project = $this->getPreparedStr($arParams['PROJECT']);

        if (array_key_exists('KEY', $arParams))
            $this->key = $this->getPreparedStr($arParams['KEY']);
        
        if (array_key_exists('FROM', $arParams))
            $this->from = $this->getPreparedStr($arParams['FROM']);

        if (array_key_exists('USE_SSL', $arParams))
            $this->useSSL = $this->getPreparedStr($arParams['USE_SSL']);

        if (array_key_exists('TEST_MODE', $arParams))
            $this->testMode = $this->getPreparedStr($arParams['TEST_MODE']);
    }


    public function send($phone, $text, $arParams = array())
    {
        $params = array(
            'recipients' => $phone,
            'message'    => $this->getPreparedStr($text)
        );

        if (array_key_exists('sender', $arParams) && !is_null($arParams['sender'])) {
            $params['sender'] = $arParams['sender'];
        }
        else
        {
            $params['sender'] = $this->from;
        }

        if (array_key_exists('run_at', $arParams)) {
            $params['run_at'] = $arParams['run_at'];
        }
        if (array_key_exists('test', $arParams)) {
            $params['test'] = (int)boolval($arParams['test']);
        } else if ($this->testMode) {
            $params['test'] = 1;
        }

        $response = $this->makeRequest('message/send', $params);


        $result = new Result();
        if ($response['status'] == self::REQUEST_SUCCESS) {

            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
            $result->setMore('params', array(
                'messageId' => $response['messages_id'][0],
                'price'     => $response['price']
            ));
            $result->setMore('response', $response);

        } elseif ($response['status'] == self::REQUEST_ERROR) {
            $result->setError(new Error($this->getErrorDescription($response['error']), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array_merge($params, array(
                'response' => $response
            ))));

        } else {
            $result->setError(new Error('', \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, $response));
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
        $response = $this->makeRequest('message/balance');
        if ($response['status'] == self::REQUEST_SUCCESS) {
            $result->setResult($response['balance'] . GetMessage($this->module_id . '.SERVICE.MAINSMS.CURRENCY'));
        } else {
            $result->setError(new Error($response['message'], \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, $response));
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


    public function agent($arSms)
    {

        $result = new Result(true);
        $arResult = array();
        $arError = array();

        foreach ($arSms as $smsId => $arSmsMore) {
            if (isset($arSmsMore['PARAMS']['messageId'])) {
                $res = $this->messageStatus($arSmsMore['PARAMS']['messageId']);
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


    private function preparePhone(&$phone)
    {
        if (is_array($phone)) {
            $newPhone = array();
            foreach ($phone as $item) {
                $item = preg_replace('/^8/', '7', preg_replace('/[^\d]/', '', $item));
                if ($item != '') {
                    $newPhone[] = $item;
                }
            }
        } else {
            $phone = preg_replace('/^8/', '7', preg_replace('/[^\d]/', '', $phone));
        }
    }

    private function getPreparedStr($str)
    {
        /** @global CMain $APPLICATION */
        global $APPLICATION;

        return (strtoupper(LANG_CHARSET) != 'UTF-8' ? $APPLICATION->ConvertCharset($str, LANG_CHARSET, "UTF-8") : $str);
    }

    public function getParams()
    {

        return array(
            'PROJECT'   => array(
                'NAME'      => GetMessage($this->module_id . '.SERVICE.MAINSMS.PARAMS.PROJECT'),
                'NAME_HINT' => GetMessage($this->module_id . '.SERVICE.MAINSMS.PARAMS.PROJECT_HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            ),
            'KEY'       => array(
                'NAME'      => GetMessage($this->module_id . '.SERVICE.MAINSMS.PARAMS.KEY'),
                'NAME_HINT' => GetMessage($this->module_id . '.SERVICE.MAINSMS.PARAMS.KEY_HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            ),
            'FROM'      => array(
                'NAME'      => GetMessage($this->module_id . '.SERVICE.MAINSMS.PARAMS.FROM'),
                'NAME_HINT' => GetMessage($this->module_id . '.SERVICE.MAINSMS.PARAMS.FROM_HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            ),
            'USE_SSL'   => array(
                'NAME'      => GetMessage($this->module_id . '.SERVICE.MAINSMS.PARAMS.USE_SSL'),
                'NAME_HINT' => GetMessage($this->module_id . '.SERVICE.MAINSMS.PARAMS.USE_SSL_HINT'),
                'TYPE'      => 'CHECKBOX',
                'VALUE'     => ''
            ),
            'TEST_MODE' => array(
                'NAME'      => GetMessage($this->module_id . '.SERVICE.MAINSMS.PARAMS.TEST_MODE'),
                'NAME_HINT' => GetMessage($this->module_id . '.SERVICE.MAINSMS.PARAMS.TEST_MODE_HINT'),
                'TYPE'      => 'CHECKBOX',
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
        return GetMessage($this->module_id . '.SERVICE.MAINSMS.DESCRIPTION');
    }


    /**
     * Отправить запрос
     *
     * @param string $function
     * @param array  $params
     *
     * @return stdClass
     */
    protected function makeRequest($function, array $params = array())
    {
        
        $params = $this->joinArrayValues($params);
        
        $sign = $this->generateSign($params);
        $params = array_merge(array('project' => $this->project), $params);

        $url = ($this->useSSL ? 'https://' : 'http://') . $this->url . '/' . $function;
        $post = http_build_query(array_merge($params, array('sign' => $sign)), '', '&');

//        if (function_exists('curl_init')) {
//            $ch = curl_init($url);
//            if ($this->useSSL) {
//                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//            }
//            curl_setopt($ch, CURLOPT_POST, 1);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//
//            $response = curl_exec($ch);
//            curl_close($ch);
//        } else {
//            $context = stream_context_create(array(
//                'http' => array(
//                    'method'  => 'POST',
//                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
//                    'content' => $post,
//                    'timeout' => 10,
//                ),
//            ));
//            $response = file_get_contents($url, false, $context);
//        }


        $response = $this->oHttp->post($url, $post);

        return $this->response = json_decode($response, true);
    }

    protected function joinArrayValues($params)
    {
        $result = array();
        foreach ($params as $name => $value) {
            if (is_array($value)) {
                $v = array_values($value);
                if (is_array($v[0])) {
                    $result[$name] = join(',', $this->joinArrayValues($value));
                } else {
                    $result[$name] = join(',', $value);
                }
            } else {
                $result[$name] = $value;
            }
        }
        return $result;
    }

    /**
     * Сгенерировать подпись
     *
     * @param array $params
     *
     * @return string
     */
    protected function generateSign(array $params)
    {
        $params['project'] = $this->project;
        ksort($params);
        return md5(sha1(join(';', array_merge($params, Array($this->key)))));
    }

    /**
     * Проверить статус доставки сообщений
     *
     * @param string|array $messagesId
     *
     * @return boolean|array
     */
    private function messageStatus($messagesId)
    {
        if (!is_array($messagesId)) {
            $messagesId = array($messagesId);
        }

        $response = $this->makeRequest('message/status', array(
            'messages_id' => join(',', $messagesId),
        ));

        $result = new Result();
        if ($response['status'] == self::REQUEST_SUCCESS) {


            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
            $result->setMore('response', $response);

            if (isset($response['messages'][$messagesId[0]])) {

                if ($response['messages'][$messagesId[0]] == 'delivered') {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                }
            } else {
                $result->setResult(new Error('Status not found', \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'response' => $response
                )));
            }

        } elseif ($response['status'] == self::REQUEST_ERROR) {
            $result->setError(new Error($this->getErrorDescription($response['error']), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                'response' => $response
            )));

        } else {
            $result->setError(new Error('', \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, $response));
        }

        return $result;
    }


    /**
     * Запрос стоимости сообщения
     *
     * @param string|array $recipients
     * @param string       $message
     *
     * @return boolean|decimal
     */
    private function messagePrice($recipients, $message)
    {
        $response = $this->makeRequest('message/price', array(
            'recipients' => $recipients,
            'message'    => $message,
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['price'] : false;
    }

    /**
     * Запрос информации о номерах
     *
     * @param string|array $recipients
     *
     * @return boolean|decimal
     */
    private function phoneInfo($phones)
    {
        //$this->preparePhone($phones);

        $response = $this->makeRequest('message/info', array(
            'phones' => $phones
        ));

        return $response['status'] == self::REQUEST_SUCCESS ? $response['info'] : false;
    }

    /**
     * Установить адрес шлюза
     *
     * @param string $url
     *
     * @return void
     */
    private function setUrl($url)
    {
        $this->url = $url;
    }


    /**
     * Получить адрес сервера
     *
     * @return string
     */
    private function getUrl()
    {
        return $this->url;
    }


    private function getErrorDescription($error)
    {
        switch ($error) {
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
            case 10: {
                return GetMessage($this->module_id . '.SERVICE.MAINSMS.ERROR_' . $error);
            }
                break;
            default: {
            return GetMessage($this->module_id . '.SERVICE.MAINSMS.ERROR_UNKNOWN');
            }
        }
    }


}
