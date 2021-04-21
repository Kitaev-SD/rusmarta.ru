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
    
    
    class twilio_com
    {
        
        //https://www.twilio.com/docs/sms
       
        
        const REQUEST_SUCCESS = 'success';
        const REQUEST_ERROR = 'error';
        
        private $sid = null;
        private $token = null;
        private $sender = null;
        
        private $oHttp = null;
        
        
        /**
         * Конструктор
         *
         * @param array $arParams
         * - string SID
         * - string TOKEN
         * - string SENDER
         */
        public function __construct($arParams = array())
        {
            if (is_null($this->oHttp)) {
                $this->oHttp = new \Bitrix\Main\Web\HttpClient();
            }
            
            if (array_key_exists('SID', $arParams)) {
                $this->sid = trim($this->encode($arParams['SID']));
            }
            
            if (array_key_exists('TOKEN', $arParams)) {
                $this->token = trim($this->encode($arParams['TOKEN']));
            }
            if (array_key_exists('SENDER', $arParams)) {
                $this->sender = trim($this->encode($arParams['SENDER']));
            }
            
        }
        
        
        /**
         * Сообщения
         *
         * @param $code
         *
         * @return mixed|string
         */
        private function getMessage($code)
        {
            return GetMessage('bxmaker.smsnotice.twilio_com.' . $code);
        }
        
        public function getParams()
        {
            
            return array(
                'SID' => array(
                    'NAME' => $this->getMessage('PARAMS.SID'),
                    'NAME_HINT' => $this->getMessage('PARAMS.SID.HINT'),
                    'TYPE' => 'STRING',
                    'VALUE' => ''
                ),
                'TOKEN' => array(
                    'NAME' => $this->getMessage('PARAMS.TOKEN'),
                    'NAME_HINT' => $this->getMessage('PARAMS.TOKEN.HINT'),
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
         * Описание сервиса что куда тывать, для страницы параметров сервиса
         *
         * @return mixed|string
         */
        public function getDescription()
        {
            return $this->getMessage('DESCRIPTION');
        }
        
        /**
         * Проверка баланса
         *
         * @return Result
         */
        public function getBalance()
        {
            $result = new Result();
            
            //приводим к строке
            $result->addError($this->getMessage('BALANSE_UNAVAILABLE'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array(
                'HTTP_STATUS' => $this->oHttp->getStatus()
            ));
            
            return $result;
        }
        
        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();
    
            $arParams = array();
            $arParams['From'] = $this->sender;
            $arParams['To'] = '+'.$phone;
            $arParams['Body'] = $this->encode($text);
            
            $response = $this->_makeRequest('message/send', $arParams);
            
            
            if (is_array($response) && isset($response['status']) && $response['status'] == 400) {
                $result->addError($response['message'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                    'response' => $response,
                ));
            } elseif (is_array($response) && isset($response['sid'])) {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                $result->setMore('params', array(
                    'messageId' => $response['sid'],
                    'response' => $response
                ));
                $result->setMore('response', $response);
            } else {
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
                'id'    => $messagesId,
            ));
    
            $result->setMore('messagesId', $messagesId);
            $result->setMore('params', array(
                'messageId' => $messagesId
            ));
    
            if (is_array($response) && isset($response['status']) && $response['status'] == 400) {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $response['message']);
            }
            elseif (is_array($response) && isset($response['error_message']) && !empty($response['error_message'])) {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $response['error_message']);
            }
            elseif (is_array($response) && isset($response['status'])) {
                switch ($response['status']) {
                    case 'sent':
                        {
                            //отправлено
                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    
                            break;
                        }
                        case 'delivered':
                        {
                            //доставлено
                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                    
                            break;
                        }
                    default:
                        {
                            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                        }
                }
            } else {
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->getMessage('CHECK_PARAM_CONNECTION_ERROR'));
            }
    
    
            return $result;
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
            try {
               
                
                switch ($method) {
                    
                    case 'message/send':
                        {
                            
                            $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $this->sid . '/Messages.json';
                            
                            $this->oHttp->setAuthorization($this->sid, $this->token);
                            
                            $response = $this->oHttp->post($url, $params);
                            
                            break;
                        }
                    case 'message/status':
                        {
    
                            $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $this->sid . '/Messages/'.$params['id'].'.json';
    
                            $this->oHttp->setAuthorization($this->sid, $this->token);
                            
                            $response = $this->oHttp->post($url, array(
                                'Body' => ''
                            ));
                            
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
