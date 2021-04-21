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


Class sms_fly_com
{

// http://sms-fly.com/


    const REQUEST_SUCCESS = 'success';
    const REQUEST_ERROR = 'error';

    private $arParams = array(
        'user' => '',
        'pwd'  => '',
        'sadr' => '',
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
        // подключим необходимую библиотеку
        require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/classes/general/xml.php');

        if (is_null($this->oHttp)) {
            $this->oHttp = new \Bitrix\Main\Web\HttpClient();
        }

        if (array_key_exists('ID', $arParams))
            $this->arParams['id'] = trim($arParams['ID']);

        if (array_key_exists('KEY', $arParams))
            $this->arParams['key'] = trim($arParams['KEY']);

        if (array_key_exists('SADR', $arParams))
            $this->arParams['sadr'] = trim($arParams['SADR']);
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
        return GetMessage('bxmaker.smsnotice.sms_fly_com.' . $code);
    }


    public function send($phone, $text, $arParams = array())
    {
        $result = new Result();
        $arParams['text'] = $text;
        $arParams['to'] = $phone;

        $response = $this->_makeRequest('message/send', $arParams);

        // ошибка
        if (is_array($response) && isset($response['message']['#']['state'][0]['@']['code'])) {
            $result->setMore('response', $response);

            switch ($response['message']['#']['state'][0]['@']['code']) {
                case 'ACCEPT': {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    $result->setMore('params', array(
                        'campaignID' => (isset($response['message']['#']['state'][0]['@']['campaignID']) ? trim($response['message']['#']['state'][0]['@']['campaignID']) : ''),
                        'date' => (isset($response['message']['#']['state'][0]['@']['date']) ? trim($response['message']['#']['state'][0]['@']['date']) : '')
                    ));

                    break;
                }
                default: {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getErrorDescription($response['message']['#']['state'][0]['@']['code'], $response['message']['#']['state'][0]['#']));
                break;
                }
            }

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
    public function getBalance()
    {
        $result = new Result();
        $response = $this->_makeRequest('balance', Array());

        if (is_array($response) && isset($response['message']['#']['balance'][0]['#'])) {
            $result->setResult(floatval($response['message']['#']['balance'][0]['#']) . $this->_getMsg('SERVICE.CURRENCY'));
        } else {
            $result->setError(new Error((string)$response, \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array('response' => $response)));
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
            if (isset($arSmsMore['PARAMS']['campaignID'])) {

                $res = $this->_messageStatus($arSmsMore['PARAMS']['campaignID'], $arSmsMore['PHONE']);

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
        global $APPLICATION;

        switch ($method) {
            case 'message/status': {

                $xml = '<' . '?xml version="1.0" encoding="utf-8"?' . '>' . "\n\t";
                $xml .= "<request>" . "\n\t\t";
                $xml .= "<operation>GETMESSAGESTATUS</operation>" . "\n\t";
                $xml .= "<message campaignID='".$params['id']."' recipient='".$params['phone']."' />" . "\n\t";
                $xml .= "</request>" . "\n";

                break;
            }
            case 'message/send': {

                $text = htmlspecialchars($params['text']);
                $description = $this->_getMsg('SEND_MESSAGE_DESCRIPTION');
                $start_time = 'AUTO'; // отправить немедленно или ставим дату и время  в формате YYYY-MM-DD HH:MM:SS
                $end_time = 'AUTO'; // автоматически рассчитать системой или ставим дату и время  в формате YYYY-MM-DD HH:MM:SS
                $rate = 1; // скорость отправки сообщений (1 = 1 смс минута). Одиночные СМС сообщения отправляются всегда с максимальной скоростью.
                $lifetime = 4; // срок жизни сообщения 4 часа
                $source = $this->_getParam('sadr'); // Alfaname
                $recipient = $params['to'];

                $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
                $xml .= "<request>";
                $xml .= "<operation>SENDSMS</operation>";
                $xml .= '		<message start_time="' . $start_time . '" end_time="' . $end_time . '" lifetime="' . $lifetime . '" rate="' . $rate . '" desc="' . $description . '" source="' . $source . '">' . "\n";
                $xml .= "		<body>" . $text . "</body>";
                $xml .= "		<recipient>" . $recipient . "</recipient>";
                $xml .= "</message>";


                break;
            }
            case 'balance': {
                $xml = '<' . '?xml version="1.0" encoding="utf-8"?' . '>' . "\n\t";
                $xml .= "<request>" . "\n\t\t";
                $xml .= "<operation>GETBALANCE</operation>" . "\n\t";
                $xml .= "</request>" . "\n";
                break;
            }
            default: {
            return false;
            }
        }

        $this->oHttp->setAuthorization($this->_getPreparedStr($this->_getParam('id')), $this->_getPreparedStr($this->_getParam('key')));
        $this->oHttp->setHeader('Content-Type', 'text/xml');
        $this->oHttp->setHeader('Accept', 'text/xml');

        $res = $this->oHttp->post('http://sms-fly.com/api/api.php', $this->_getPreparedStr($xml));
        if (!!$res) {
            $this->response = $this->_prepareAnswer($res);
        } else {
            $this->response = false;
        }


        return $this->response;
    }

    // ответ в utf
    private function _prepareAnswer($str)
    {
        /** @global CMain $APPLICATION */
        global $APPLICATION;

        if ($str) {
            $oXml = new \CDataXML();
            $oXml->LoadString($str);
            $arRes = $oXml->GetArray();

            if (is_array($arRes) && !empty($arRes)) {
                return (!Manager::isUTF() ? $APPLICATION->ConvertCharsetArray($arRes, "UTF-8", LANG_CHARSET) : $arRes);
            }
        }

        return (!Manager::isUTF() ? $APPLICATION->ConvertCharset($str, "UTF-8", LANG_CHARSET) : $str);
    }


    /**
     * Проверить статус доставки сообщений
     *
     * @param string $campaignID
     *
     * @return boolean|array
     */
    private function _messageStatus($campaignID, $phone)
    {
//        http://bxmaker.sms.ru/?panel=api&subpanel=method&show=sms/status
        $result = new Result();

        $response = $this->_makeRequest('message/status', array(
            'id' => $campaignID,
            'phone' => $phone
        ));
        $result->setMore('response', $response);

        if(is_array($response))
        {
            //ошибка
            if(isset($response['message']['#']['state'][0]['@']['code']))
            {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description',$response['message']['#']['state'][0]['#']);
            }
            //статус
            elseif(isset($response['message']['#']['state'][0]['@']['status']))
            {
                switch ($response['message']['#']['state'][0]['@']['status']) {
                    case 'PENDING': {
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
                    case 'EXPIRED': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_EXPIRED'));
                        break;
                    }
                    case 'UNDELIV': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_UNDELIV'));
                        break;
                    }
                    case 'STOPED': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_STOPED'));
                        break;
                    }
                    case 'ERROR': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_ERROR'));
                        break;
                    }
                    case 'USERSTOPED': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_USERSTOPED'));
                        break;
                    }
                    case 'ALFANAMELIMITED': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description', $this->_getMsg('MSG_STATUS_ALFANAMELIMITED'));
                        break;
                    }
                    case 'STOPFLAG': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        break;
                    }
                    case 'NEW': {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        break;
                    }
                    default: {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $this->_getMsg('MSG_STATUS_UNKNOWN_VALUE'));
                    break;
                    }
                }
            }
        }
        else
        {
            $result->setError(new Error($this->_getMsg('ERROR_STATUS_EMPTY'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                'campaignID' => $campaignID,
                'phone' => $phone,
                'response'   => $response
            )));
        }
        return $result;
    }

    /*
     * Варианты ошибок, который можно переиначить
     */
    private function _getErrorDescription($error_code, $defaultDescr = '')
    {
        switch ($error_code) {

            case 'XMLERROR': {
                return $this->_getMsg('XMLERROR');
                break;
            }
            case 'ERRPHONES': {
                return $this->_getMsg('ERRPHONES');
                break;
            }
            case 'ERRSTARTTIME': {
                return $this->_getMsg('ERRSTARTTIME');
                break;
            }
            case 'ERRENDTIME': {
                return $this->_getMsg('ERRENDTIME');
                break;
            }
            case 'ERRLIFETIME': {
                return $this->_getMsg('ERRLIFETIME');
                break;
            }
            case 'ERRSPEED': {
                return $this->_getMsg('ERRSPEED');
                break;
            }
            case 'ERRALFANAME': {
                return $this->_getMsg('ERRALFANAME');
                break;
            }
            case 'ERRTEXT': {
                return $this->_getMsg('ERRTEXT');
                break;
            }
            case 'INSUFFICIENTFUNDS': {
                return $this->_getMsg('INSUFFICIENTFUNDS');
                break;
            }
            default: {
            return trim($error_code .' ' . $defaultDescr);
            }
        }
    }


}
