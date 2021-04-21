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


Class sms48
{
    private $module_id = 'bxmaker.smsnotice';

    private $host = 'sms48.ru';
    private $proto = 'http://';

    private $login = '';
    private $pass = '';
    private $from = '';
    private $api_login = false;
    private $api_pass = false;

    private $notice_path = '';

    private $oHttp = null;


    public function __construct($arParams = array())
    {
        if(is_null($this->oHttp))
        {
            $this->oHttp =  new \Bitrix\Main\Web\HttpClient();
        }

        $this->login = $this->getPrepareStr($arParams['LOGIN']);
        $this->pass = $this->getPrepareStr($arParams['PASS']);
        $this->from = $this->getPrepareStr($arParams['FROM']);

        $this->api_login = $this->getPrepareStr($arParams['API_LOGIN']);
        $this->api_pass = $this->getPrepareStr($arParams['API_PASS']);

        $this->notice_path = 'http://' . Option::get('main', 'server_name', '') .  '/bitrix/tools/' . $this->module_id . '/notice.php';

    }

    public function send($phone, $text, $arParams = array())
    {
        //$this->preparePhone($phone);

        $check = $this->getSign($phone);
        $params = array(
            'to'      => $phone,
            'msg'     => $this->getPrepareStr($text),
            'from'    => $this->from,
            'dlr_url' => $this->getNoticeUrl($arParams)
        );

        // если доступ осуществл€етс€ через апи ключ
        if ($this->api_login) {
            $params['api_login'] = $this->api_login;
            $params['check3'] = $check;
        } // если стандартный доступ
        else {
            $params['login'] = $this->login;
            $params['check2'] = $check;
        }

        $res = $this->oHttp->get($this->proto . $this->host . '/send_sms.php?' . http_build_query($params));

        if ($res == 1) {
            $result = new Result(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
            $result->setMore('phone', $phone);
            $result->setMore('text', $text);
        } else {
            if ($res == 8) {
                $result = new Result(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                $result->setMore('phone', $phone);
                $result->setMore('text', $text);
            } else {
                $result = new Result(new Error($res, 'BXMAKER_SMSNOTICE_SERVICE_SEND_ERROR', array(
                    'phone'   => $phone,
                    'text'    => $text,
                    'params'  => $params,
                    'request' => $this->proto . $this->host . '/send_sms.php?' . http_build_query($params)
                )));
            }
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

        if ($this->api_login) {
            $params['api_login'] = $this->api_login;
            $params['check'] = $this->getSign();
        } // если стандартный доступ
        else {
            $params['login'] = $this->login;
            $params['check'] = $this->getSign();
        }

        //$res = $this->oHttp->get($this->proto . $this->host . '/get_balance.php?' . http_build_query($params));
//        if ($res !== false) {
//            $result = new Result( '~ ' .  (intval($res) * 0.32) . GetMessage($this->module_id . '.SERVICE.SMS48.CURRENCY'));
//        } else {
//            $result = new Result(new Error($res, 'BXMAKER_SMSNOTICE_SERVICE_GETBALANCE_ERROR'));
//        }

        $res =$this->oHttp->get($this->proto . $this->host . '/get_balance2.php?' . http_build_query($params));
        if ($res !== false) {
            $result = new Result($res . GetMessage($this->module_id . '.SERVICE.SMS48.CURRENCY'));
        } else {
            $result = new Result(new Error($res, 'BXMAKER_SMSNOTICE_SERVICE_GETBALANCE_ERROR'));
        }
        return $result;
    }

    /**
     * ѕровер€ет статус сообщени€ и возвращает объект с результотм = статусу сообщени€, в доп параметрах содержитс€ его идентификатор smsId
     * @return Result
     * @throws \Bitrix\Main\ArgumentException
     */
    public function notice()
    {
        $req = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $result = new Result();
        $oManagerTable = new ManagerTable();
        $dbr = $oManagerTable->getList(array(
            'filter' => array(
                'ID' => (int) $req->getQuery('smsId')
            )
        ));

        if ($ar = $dbr->fetch()) {
            // раз найдено то проверим статус
            switch ($req->getQuery('status')) {
                case 1: {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                    break;
                }
                case 8: {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    break;
                }
                default: {
                $result->setError(new Error( GetMessage($this->module_id . '.SERVICE.SMS48.NOTICE.BXMAKER_SMSNOTICE_SERVICE_NOTICE_ERROR_UNKNOWN_SMS_STATUS'), 'BXMAKER_SMSNOTICE_SERVICE_NOTICE_ERROR_UNKNOWN_SMS_STATUS', array(
                    'params' => $req->getQueryList()
                )));
                }
            }
        } else {
            $result->setError(new Error(GetMessage($this->module_id . '.SERVICE.SMS48.NOTICE.NOT_FOUND_SMS_BY_ID'), 'BXMAKER_SMSNOTICE_SERVICE_NOTICE_ERROR_NOT_FOUN_SMS_BY_ID'));
        }

        $result->setMore('smsId', (int)$req->getQuery('smsId'));
        return $result;
    }


    public function agent($arSms)
    {
        $result = new Result(true);
        $arResult = array();

        foreach($arSms  as $smsId => $arSmsMore)
        {
            // просто проставл€ем всем что они доставлены
            // всеравно в случае ошибки статус сменитс€ и добавитс€ информаци об ошибке, при оповещении сервисом о статусе
            // а смски в статусе ошибки не обрабатываеютс€ агентом в последствии
            $arResult[$smsId] = new Result(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
        }

        $result->setMore('results', $arResult);
        $result->setMore('errors', null); // ошибок нет

        return $result;
    }


    /**
     * ¬озвращает контрольную строку дл€ отправки сообщени€
     *
     * @param string $phone
     *
     * @return string
     */
    private function getSign($phone = null)
    {
        if ($this->api_login) {
            return md5($this->api_login . md5($this->api_pass) . $phone);
        } else {
            return md5($this->login . md5($this->pass) . $phone);
        }
    }

    private function getNoticeUrl($arParams = array())
    {

        return $this->getPrepareStr($this->notice_path . '?' . http_build_query(array(
                'smsId'     => $arParams['smsId'],
                'serviceId' => $arParams['service']['ID'],
                'status'    => '%d'
            )));
    }

    private function preparePhone(&$phone)
    {
        $phone = preg_replace('/[^\d]/', '', $phone);
    }

    private function getPrepareStr($str)
    {
        /** @global \CMain $APPLICATION */
        global $APPLICATION;

        return ( !Manager::isWin() ? $APPLICATION->ConvertCharset($str, LANG_CHARSET, "Windows-1251") : $str);
    }

    public function getParams()
    {

        return array(
            'LOGIN'     => array(
                'NAME'      => GetMessage($this->module_id . '.SERVICE.SMS48.PARAMS.LOGIN'),
                'NAME_HINT' => GetMessage($this->module_id . '.SERVICE.SMS48.PARAMS.LOGIN_HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            ),
            'PASS'      => array(
                'NAME'      => GetMessage($this->module_id . '.SERVICE.SMS48.PARAMS.PASS'),
                'NAME_HINT' => GetMessage($this->module_id . '.SERVICE.SMS48.PARAMS.PASS_HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            ),
            'FROM'      => array(
                'NAME'      => GetMessage($this->module_id . '.SERVICE.SMS48.PARAMS.FROM'),
                'NAME_HINT' => GetMessage($this->module_id . '.SERVICE.SMS48.PARAMS.FROM_HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            ),
            'API_LOGIN' => array(
                'NAME'      => GetMessage($this->module_id . '.SERVICE.SMS48.PARAMS.API_LOGIN'),
                'NAME_HINT' => GetMessage($this->module_id . '.SERVICE.SMS48.PARAMS.API_LOGIN_HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            ),
            'API_PASS'  => array(
                'NAME'      => GetMessage($this->module_id . '.SERVICE.SMS48.PARAMS.API_PASS'),
                'NAME_HINT' => GetMessage($this->module_id . '.SERVICE.SMS48.PARAMS.API_PASS_HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            )
        );
    }

    public function getDescription(){
        return GetMessage($this->module_id . '.SERVICE.SMS48.DESCRIPTION');
    }
}