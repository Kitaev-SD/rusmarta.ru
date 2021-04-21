<?
    
    namespace Bxmaker\SmsNotice\Service;
    
    use Bitrix\Main\Application;
    use Bitrix\Main\Config\Option;
    use Bitrix\Main\Loader;
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\Text\Encoding;
    use Bxmaker\SmsNotice\Error;
    use Bxmaker\SmsNotice\Manager;
    use Bxmaker\SmsNotice\ManagerTable;
    use Bxmaker\SmsNotice\Result;
    use Bxmaker\SmsNotice\Service;
    
    Loc::loadMessages(__FILE__);
    
    
    class targetsms_ru
    {
        
        //https://targetsms.ru/images/JSON.pdf
        
        private $login = null;
        private $password = null;
        private $sender = null;
        
        private $oHttp = null;
        
        
        /**
         *  онструктор
         *
         * @param array $arParams
         * - string LOGIN
         * - string PASSWORD
         * - string SENDER
         */
        public function __construct($arParams = array())
        {
            if (is_null($this->oHttp)) {
                $this->oHttp = new \Bitrix\Main\Web\HttpClient();
            }
            
            if (array_key_exists('LOGIN', $arParams)) {
                $this->login = trim($this->encode($arParams['LOGIN']));
            }
            
            if (array_key_exists('PASSWORD', $arParams)) {
                $this->password = trim($this->encode($arParams['PASSWORD']));
            }
            if (array_key_exists('SENDER', $arParams)) {
                $this->sender = trim($this->encode($arParams['SENDER']));
            }
            
        }
        
        
        /**
         * —ообщени€
         *
         * @param $code
         *
         * @return mixed|string
         */
        private function getMessage($code)
        {
            return GetMessage('bxmaker.smsnotice.targetsms_ru.' . $code);
        }
        
        public function getParams()
        {
            
            return array(
                'LOGIN' => array(
                    'NAME' => $this->getMessage('PARAMS.LOGIN'),
                    'NAME_HINT' => $this->getMessage('PARAMS.LOGIN.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                ),
                'PASSWORD' => array(
                    'NAME' => $this->getMessage('PARAMS.PASSWORD'),
                    'NAME_HINT' => $this->getMessage('PARAMS.PASSWORD.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                ),
                'SENDER' => array(
                    'NAME' => $this->getMessage('PARAMS.SENDER'),
                    'NAME_HINT' => $this->getMessage('PARAMS.SENDER.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                ),
            );
        }
        
        /**
         * ќписание сервиса что куда тыкать, дл€ страницы параметров сервиса
         *
         * @return mixed|string
         */
        public function getDescription()
        {
            return $this->getMessage('DESCRIPTION');
        }
        
        /**
         * ѕроверка баланса
         *
         * @return Result
         */
        public function getBalance()
        {
            $result = new Result();
            
            $response = $this->makeRequest('balance', array());
            if (is_array($response)) {
                if (isset($response['error'])) {
                    $result->addError($response['error'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                        'response' => $response
                    ));
                }
                elseif(isset($response['money']))
                {
                    $result->setResult($response['money']['value'] .' '. $response['money']['currency']);
                }
                else {
                    $result->addError($this->getMessage('UNKNOWN_RESPONSE_ERROR'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array(
                        'response' => $response
                    ));
                }
                
            } else {
                //приводим к строке
                $result->addError($this->getMessage('CHECK_PARAM_CONNECTION_ERROR'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array(
                    'HTTP_STATUS' => $this->oHttp->getStatus()
                ));
            }
            
            return $result;
        }
        
        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();
            
            $queryParams = array();
            $queryParams[] = array(
                    'type' => 'sms',
                    'sender' => (!empty($arparams['sender']) ? $arparams['sender'] : $this->sender),
                    'text' => $this->encode($text),
                    'abonent' => array(
                        array(
                            'phone' => $phone,
                            'number_sms' => 1
                        )
                    )
            );
            
            if(intval($arParams['smsId']))
            {
                $queryParams[0]['abonent'][0]['client_id_sms'] = $arParams['smsId'];
            }
            
            $response = $this->makeRequest('message/send', $queryParams);
            
            if (is_array($response)) {
    
                if (isset($response['error'])) {
                    $result->addError($response['error'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                        'response' => $response
                    ));
                }
                elseif(isset($response['sms']))
                {
                    $message = reset($response['sms']);
                    if(isset($message['error']))
                    {
                        $result->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description' , $message['error']);
                    }
                    elseif($message['action'] == 'send')
                    {
                        $result->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                        $result->setMore('params', array(
                            'messageId' => $message['id_sms']
                        ));
                    }
                    else
                    {
                        $result->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setMore('error_description' , $message['action']);
                    }
                }
                else {
                    $result->addError($this->getMessage('UNKNOWN_RESPONSE_ERROR'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array(
                        'response' => $response
                    ));
                }
            }
            else {
                //приводим к строке
                $result->addError($this->getMessage('CHECK_PARAM_CONNECTION_ERROR'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array(
                    'response' => $response
                ));
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
            $queryParams = array();
            $queryParams['messages'] = array();
            
            foreach ($arPack['messages'] as $msg) {
                $queryParams['messages'][] = array(
                    'recipient' => $msg['phone'],
                    'text' => $this->encode($msg['text']),
                    'source' => (!!$msg['sender'] ? $msg['sender'] : $this->sender),
                    'id' => $msg['clientId']
                );
            }
            
            if (count($queryParams['messages'])) {
                $response = $this->makeRequest('message/send', $queryParams);
                
                if (is_array($response) && isset($response['success'])) {
                    
                    if (isset($response['result']['messages']) && !empty($response['result']['messages'])) {
                       
                        foreach($response['result']['messages'] as $message)
                        {
                            $resultStatus = new Result();
                            $resultStatus->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                            $resultStatus->setMore('params', array(
                                'messageId' => $message['id']
                            ));
                            $arMsg[$msg['clientId']] = $resultStatus;
                        }
                        
                    } else {
                        foreach ($arPack['messages'] as $msg) {
                            $resultStatus = new Result();
                            $resultStatus->addError($response['error']['descr'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                                'response' => $response
                            ));
                            $arMsg[$msg['clientId']] = $resultStatus;
                        }
                    }
                    
                } else {
                    foreach ($arPack['messages'] as $msg) {
                        $resultStatus = new Result();
                        $resultStatus->addError($this->getMessage('CHECK_PARAM_CONNECTION_ERROR'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array(
                            'response' => $response
                        ));
                        $arMsg[$msg['clientId']] = $resultStatus;
                    }
                }
            }
            
            $result->setResult(array('messages' => $arMsg));
            
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
            $result = new Result(true);
            $arResult = array();
            $arError = array();
            
            $arMessageId = array();
            
            foreach ($arSms as $smsId => $arSmsMore) {
                if (isset($arSmsMore['PARAMS']['messageId'])) {
                    
                    $arMessageId[$arSmsMore['PARAMS']['messageId']] = $smsId;
                } else {
                    $arError[] = new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $arResult[$smsId] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
                }
            }
            
            if (count($arMessageId)) {
                $response = $this->makeRequest('message/status', array_keys($arMessageId));
                if (is_array($response)) {
                    
                    if (isset($response['error'])) {
                        foreach ($arMessageId as $id => $smsId) {
                            $arError[] = new Error($response['error'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM);
                            $arResult[$smsId] = new Result(new Error($response['error'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                                'response' => $response
                            )));
                        }
                    }
                    elseif(isset($response['state']))
                    {
                        foreach($response['state'] as $state)
                        {
                            $smsId = $arMessageId[$state['id_sms']];
    
                            $resultStatus = new Result(true);
                            $resultStatus->setMore('params', array(
                                'messageId' => $state['id_sms']
                            ));
    
                            if(isset($state['error']))
                            {
                                $resultStatus->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                $resultStatus->setMore('error_description' , $state['error']);
                            }
                            else {
                                switch($state['state'])
                                {
                                    case 'deliver':{
                                        $resultStatus->setResult(\BXmaker\SmsNotice\SMS_STATUS_DELIVERED);
                                        break;
                                    }
                                    case 'send':{
                                        $resultStatus->setResult(\BXmaker\SmsNotice\SMS_STATUS_SENT);
                                        break;
                                    }
                                    case 'not_deliver':
                                    case 'expired':
                                    case 'partly_deliver':{
                                        $resultStatus->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                        $resultStatus->setMore('error_description' , $this->getMessage('STATUS'.$state['state']));
                                        break;
                                    }
                                    default:{
                                        $resultStatus->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                        $resultStatus->setMore('error_description' , $this->getMessage('STATUS.UNKNOWN'));
                                        break;
                                    }
                                }
                            }
    
                            $arResult[$smsId] = $resultStatus;
                        }
                    }
                    else {
                        foreach ($arMessageId as $id => $smsId) {
                            $arError[] = new Error($this->getMessage('UNKNOWN_RESPONSE_ERROR'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM);
                            $arResult[$smsId] = new Result(new Error($this->getMessage('UNKNOWN_RESPONSE_ERROR'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                                'response' => $response
                            )));
                        }
                    }
                    
                } else {
                    foreach ($arMessageId as $id => $smsId) {
                        $arError[] = new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE);
                        $arResult[$smsId] = new Result(new Error($this->getMessage('CHECK_PARAM_CONNECTION_ERROR'),
                            \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE));
                    }
                    
                }
            }
            
            $result->setMore('results', $arResult);
            $result->setMore('errors', (!empty($arError) ? $arError : null)); // ошибок нет
            
            return $result;
        }
        
        
      
        
        /**
         * ќтправить запрос
         *
         * @param string $function
         * @param array  $params
         *
         * @return array|false
         */
        protected function makeRequest($method, array $params = array())
        {
            try {
                
                switch ($method) {
                    
                    case 'balance':
                        {
                            $url = "https://sms.targetsms.ru/sendsmsjson.php";
                            
                            $this->oHttp->setHeader('Content-Type','application/json; charset=utf-8');
                            
                            $response = $this->oHttp->post($url, json_encode(array(
                                'security' => array(
                                    'login' => $this->login,
                                    'password' => $this->password,
                                ),
                                'type'=> 'balance'
                            ), true));
                            
                            break;
                        }
                    case 'message/send':
                        {
                            $url = "https://sms.targetsms.ru/sendsmsjson.php";
    
                            $this->oHttp->setHeader('Content-Type','application/json; charset=utf-8');
    
                            $response = $this->oHttp->post($url, json_encode(array(
                                'security' => array(
                                    'login' => $this->login,
                                    'password' => $this->password,
                                ),
                                'type'=> 'sms',
                                'message' => $params
                            ), true));
                            
                            break;
                        }
                    
                    case 'message/status':
                        {
                            $url = "https://sms.targetsms.ru/sendsmsjson.php";
    
                            $this->oHttp->setHeader('Content-Type','application/json; charset=utf-8');
    
                            $response = $this->oHttp->post($url, json_encode(array(
                                'security' => array(
                                    'login' => $this->login,
                                    'password' => $this->password,
                                ),
                                'type'=> 'state',
                                'get_state' => $params
                            ), true));
    
                            break;
                        }
                    
                    default:
                        {
                            return false;
                        }
                }
            } catch (\Exception $e) {
                return false;
                
            }
            
            return $this->response = $this->decode(json_decode($response, true));
        }
        
        
        private function encode($data)
        {
            if (!Application::getInstance()->isUtfMode()) {
                $data = Encoding::convertEncoding($data, SITE_CHARSET, 'UTF-8');
            }
            return $data;
        }
        
        private function decode($data)
        {
            if (!Application::getInstance()->isUtfMode()) {
                $data = Encoding::convertEncoding($data, 'UTF-8', SITE_CHARSET);
            }
            return $data;
        }
        
        
    }
