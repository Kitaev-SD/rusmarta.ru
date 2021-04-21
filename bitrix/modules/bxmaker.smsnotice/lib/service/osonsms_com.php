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
    
    
    class osonsms_com
    {
        
        //https://osonsms.com/docs/sms-api-documentation.pdf
        
        const REQUEST_SUCCESS = 'success';
        const REQUEST_ERROR = 'error';
        
        private $login = null;
        private $hash = null;
        private $sender = null;
        private $server = 'https://api.osonsms.com/';
        
        private $oHttp = null;
        
        private $lastRequestParams = array();
        
        
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
            
            if (array_key_exists('HASH', $arParams)) {
                $this->hash = trim($this->encode($arParams['HASH']));
            }
            
            if (array_key_exists('SENDER', $arParams)) {
                $this->sender = trim($this->encode($arParams['SENDER']));
            }
            
            if (array_key_exists('SERVER', $arParams) && strlen(trim($this->encode($arParams['SERVER']))) > 0) {
                $arUrl = parse_url($arParams['SERVER']);
                //                $this->server = $arUrl['scheme'].'://'.$arUrl['host'];
                $this->server = 'https://' . $arUrl['host'];
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
            return GetMessage('bxmaker.smsnotice.osonsms_com.' . $code);
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
                'HASH' => array(
                    'NAME' => $this->getMessage('PARAMS.HASH'),
                    'NAME_HINT' => $this->getMessage('PARAMS.HASH.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                ),
                'SENDER' => array(
                    'NAME' => $this->getMessage('PARAMS.SENDER'),
                    'NAME_HINT' => $this->getMessage('PARAMS.SENDER.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                ),
                'SERVER' => array(
                    'NAME' => $this->getMessage('PARAMS.SERVER'),
                    'NAME_HINT' => $this->getMessage('PARAMS.SERVER.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                )
            );
        }
        
        /**
         * ќписание сервиса что куда тывать, дл€ страницы параметров сервиса
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
            $response = $this->_makeRequest('balance', array());
            
            if (isset($response['balance'])) {
                $result->setResult($response['balance']);
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
            $arParams['from'] = $this->sender;
            $arParams['phone_number'] = $phone;
            $arParams['msg'] = $this->encode($text);
            $arParams['login'] = $this->login;
            $arParams['txn_id'] = time().randString(10);
            
            $response = $this->_makeRequest('message/send', $arParams);
    
    
            if (is_array($response) && isset($response['error']['code']))
            {
                $result->addError($response['error']['msg'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'CODE' => $response['error']['code'],
                    'MSG' => $response['error']['msg']
                ));
            }
            elseif (is_array($response) && isset($response['status']))
            {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                $result->setMore('params', array(
                    'messageId' => $response['msg_id']
                ));
                $result->setMore('response', $response);
            }
            else {
                //приводим к строке
                $result->addError($this->getMessage('CHECK_PARAM_CONNECTION_ERROR'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array(
                    'HTTP_STATUS' => $this->oHttp->getStatus()
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
            foreach ($arPack['messages'] as $msg) {
                $arMsg[$msg['clientId']] = $this->send($msg['phone'], $msg['text']);
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
        
        
        /**
         * ѕроверить статус доставки сообщений
         *
         * @param string $messagesId
         *
         * @return boolean|array
         */
        private function _messageStatus($messagesId, $phone)
        {
            $result = new Result();
            
            $response = $this->_makeRequest('message/status', array(
                'msg_id' => $messagesId,
                'login' => $this->login,
                'txn_id' => time().randString(10)
            ));
            
            $result->setMore('response', $response);
            $result->setMore('messagesId', $messagesId);
            $result->setMore('params', array(
                'messageId' => $messagesId
            ));
    
            if (is_array($response) && isset($response['Code']))
            {
                $result->addError($response['Desc'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'CODE' => $response['Code'],
                    'MSG' => $response['Desc']
                ));
            }
            elseif (is_array($response) && isset($response['error']['code']))
            {
                $result->addError($response['error']['msg'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'CODE' => $response['error']['code'],
                    'MSG' => $response['error']['msg']
                ));
            }
            elseif (is_array($response) && isset($response['status'])) {
                switch ($response['State']) {
//                    case '0':
//                        {
//                            //доставлено
//                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
//
//                            break;
//                        }
//
//                    case '-255' :
//                        {
//                            // отправлено
//                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
//                            break;
//                        }
//
                    default:
                        {
                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                        }
                }
            }
            else {
                //приводим к строке
                $result->addError($this->getMessage('CHECK_PARAM_CONNECTION_ERROR'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array(
                    'HTTP_STATUS' => $this->oHttp->getStatus()
                ));
            }
            
            
            return $result;
        }
        
        
        /**
         * ќтправить запрос
         *
         * @param string $function
         * @param array  $params
         *
         * @return stdClass
         */
        protected function _makeRequest($method, array $params = array())
        {
            try {
                $p = array(
                
                );
                
                switch ($method) {
                    case 'message/status':
                        {
                            $url = $this->server . '/query_sm.php';
    
                            $params['str_hash'] = hash('sha256', implode(';', array(
                                $params['msg_id'],
                                $params['login'],
                                $this->hash
                            )), false);
    
                            $url .= '?' . http_build_query($params);
    
                            $response = $this->oHttp->get($url);
                            
                            break;
                        }
                    case 'message/send':
                        {
                            $url = $this->server . '/sendsms_v1.php';
    
                            $params['str_hash'] = hash('sha256', implode(';', array(
                                $params['txn_id'],
                                $params['login'],
                                $params['from'],
                                $params['phone_number'],
                                $this->hash
                            )), false);
                            
                            $url .= '?' . http_build_query($params);
    
                            $response = $this->oHttp->get($url);
                            
                            break;
                        }
                    case 'balance':
                        {
                            $url = $this->server . '/check_balance.php';
                            $ar = array(
                                'txn_id' => time().randString(10),
                                'login' => $this->login,
                                'hash' => $this->hash
                            );
                            $url .= '?' . http_build_query(array(
                                    'login' => $this->login,
                                    'str_hash' => hash('sha256', implode(';', $ar), false),
                                    'txn_id' => $ar['txn_id']
                                ));
                            
                            $response = $this->oHttp->get($url);
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
