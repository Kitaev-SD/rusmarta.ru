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


    /**
     * Класс sms2ru используемый как сервис, для отправки смс через sms2.ru
     * Кодировка запросов cp1251
     *
     * @package Bxmaker\SmsNotice\Service
     */
    Class sms2ru extends Base
    {

        /**
         * @var string  Логин пользователя
         */
        private $login = null;
        /**
         * @var string пароль
         */
        private $password = null;
        /**
         * @var string ТОкен авторизации api
         */
        private $token = null;
        /**
         * @var string Имя отправителя
         */
        private $sender = null;

        /**
         * Конструктор
         *
         * @param array $arParams
         * - string LOGIN
         * - string PASSWORD
         * - string SENDER
         */
        public function __construct($arParams = array())
        {
            $this->login = (isset($arParams['LOGIN']) ? $arParams['LOGIN'] : '');
            $this->password = (isset($arParams['PASSWORD']) ? $arParams['PASSWORD'] : '');
            $this->sender = (isset($arParams['SENDER']) ? $arParams['SENDER'] : '');

            parent::__construct($arParams);
        }

        /**
         * @inheritdoc
         */
        public function getMessage($name, $arReplace = array())
        {
            return parent::getMessage('sms2ru.' . $name, $arReplace);
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
         * @inheritdoc
         */
        public function getParams()
        {
            return array(
                'LOGIN'    => array(
                    'NAME'      => $this->getMessage('PARAMS.LOGIN'),
                    'NAME_HINT' => $this->getMessage('PARAMS.LOGIN.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
                'PASSWORD' => array(
                    'NAME'      => $this->getMessage('PARAMS.PASSWORD'),
                    'NAME_HINT' => $this->getMessage('PARAMS.PASSWORD.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
                'SENDER'   => array(
                    'NAME'      => $this->getMessage('PARAMS.SENDER'),
                    'NAME_HINT' => $this->getMessage('PARAMS.SENDER.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
            );
        }

        /**
         * @inheritdoc
         */
        public function getBalance()
        {
            $resultRequest = $this->request('balance');
            
            if ($resultRequest->isSuccess()) {
                $data = $resultRequest->getResult();
                if (preg_match('/Balance: ([^;]+)/im', $data, $match)) {
                    return new Result(floatval($match[1]) .' '.$this->getMessage('BALANCE_CURRENCY'));
                } else {
                    return new Result(htmlspecialchars($data));
                }
            } else {
                return $resultRequest;
            }
        }

        /**
         * @inheritdoc
         */
        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();

            $arParams['message'] = $text;
            $arParams['phone'] = $phone;

            if (empty($arParams['sender'])) {
                $arParams['sender'] = $this->sender;
            }

            $requestResult = $this->request('send', $arParams);

            if (!$requestResult->isSuccess()) {
                return $requestResult;
            } else {
                $data = $requestResult->getResult();
                if (preg_match('/Message_ID=([\d\w]+)/mi', $data, $match)) {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    $result->setMore('params', array(
                        'messageId' => $match[1]
                    ));
                } else {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->addError($this->getMessage('ERROR.UNKNOWN'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array( 'response' => $data ));
                }
            }
            return $result;
        }
    
        /**
         * @inheritdoc
         */
        public function sendPack($arPack)
        {
            //		PackItem - array(
            //			"phone"    => $arItem['PHONE'],
            //			  "clientId" => $id,
            //			  "text"     => $arItem['TEXT'],
            //			  "sender"   => $arItem['SENDER']
            //		)
        
            $arMsg = array(); // clientId => status
        
            //так как тут возможна только одиночная отправка смс сообщений
            // то покетной обрботки по факту нет, есть очередь из одиночной отправки
            foreach($arPack['messages'] as $packItem)
            {
                $arMsg[$packItem['clientId']] = $this->send($packItem['phone'], $packItem['text'], array(
                    'sender' => $packItem['sender']
                ));
            }
        
            $result = new \Bxmaker\SmsNotice\Result(array(
                'messages' => $arMsg
            ));
        
            return $result;
        }
    
    
        /**
         * @inheritdoc
         */
        public function agent($arSms)
        {
            $result = new Result(true);
            $arResult = array();
        
            // так как нет пакетной проверки статусов смс сообщений, то делаем очередь запросов
        
            //подготовим массив идентфиикаторов смс
            foreach ($arSms as $smsId => $arSmsMore) {
                if (isset($arSmsMore['PARAMS']['messageId'])) {
                    
                    $statusResult = new \Bxmaker\SmsNotice\Result();
                    
                    $requestResult = $this->request('status', array(
                        'messageId' => $arSmsMore['PARAMS']['messageId']
                    ));
                    if (!$requestResult->isSuccess()) {
                        $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $statusResult->setError(reset($requestResult->getErrors()));
                    } else {
                        
                        if(preg_match('/Status: ([\w\d]+)/mi', $requestResult->getResult(), $match))
                        {
                            switch(\Bitrix\Main\Text\BinaryString::changeCaseToLower(trim($match[1])))
                            {
                                case 'delivered':{
                                    $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                                    break;
                                }
                                case 'not_delivered':{
                                    $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                    $statusResult->setMore('error_description', $this->getMessage('STATUS.not_delivered'));
                                    break;
                                }
                                // unknown
                                default :{
                                    $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                                    break;
                                }
                            }
                        }
                        else
                        {
                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $statusResult->addError($requestResult->getResult(), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $requestResult->getResult() ));
                        }
                    }
    
                    $arResult[ $smsId ] = $statusResult;
                
                } else {
                    $arResult[ $smsId ] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
                }
            }
        
            $result->setMore('results', $arResult);
        
            return $result;
        }


        /**
         * Отправить запрос
         *
         * @param       $method
         * @param array $arParams
         *
         * @return \Bxmaker\SmsNotice\Result
         */
        protected function request($method, array $arParams = array())
        {
            $result = new \Bxmaker\SmsNotice\Result();

            $arParams['user'] = $this->login;
            $arParams['pass'] = $this->password;
    
            if(isset($arParams['sender']) && !isset($arParams['from']))
            {
                $arParams['from'] = $arParams['sender'];
            }
    
            if(isset($arParams['messageId']) && !isset($arParams['mess_id']))
            {
                $arParams['mess_id'] = $arParams['messageId'];
            }
           
            $arParams = array_intersect_key($arParams, array_flip(array(
                'user',
                'pass',
                'phone',
                'message',
                'from',
                'flash',
                'send_at',
                'mess_id',
            )));

            switch ($method) {
                case 'balance':
                    {
                        $url = 'https://my.sms2.ru/get_credit_info.cgi';
                        break;
                    }

                case 'send':
                    {
                        $url = 'https://my.sms2.ru/sms.cgi';
                        break;
                    }
                case 'status':
                    {
                        $url = 'https://my.sms2.ru/sms_status2.cgi';
                        break;
                    }
                default:
                    {
                        return $result->addError('Unknown request method', 'UNKNOWN_REGUEST_METHOD');
                    }
            }

            $httpClient = $this->getHttpClient();
            $httpClient->setTimeout(2);
            $httpClient->post( $url, $this->toWindows1251($arParams));

            if ($httpClient->getStatus() == '200') {
                $responseText = $this->fromWindows1251($httpClient->getResult());
                if(preg_match('/^Error: (.*)$/mi', $responseText, $match))
                {
                    return $result->addError($match['1'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $responseText ));
                }
                
                return $result->setResult($responseText);
            } else {
                return $result->addError($this->getMessage('ERROR_SERVICE_STATUS'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array( 'response' => $httpClient->getResult()));
            }
        }

        


        /**
         * Вренет объект результата с описанием ошибки, полученнйо от сервиса
         *
         * @param int $statusCode
         *
         * @return string
         */
        protected function getErrorDescription($statusCode)
        {

            switch ($statusCode) {
                case -1:
                    {
                        return $this->getMessage('ERROR.' . $statusCode);
                        break;
                    }
                default:
                    {
                        return $this->getMessage('ERROR.UNKNOWN');
                        break;
                    }
            }
        }

    }
