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


    Class tele2 extends Base
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
            return parent::getMessage('tele2.' . $name, $arReplace);
        }

        /**
         * @inheritdoc
         */
        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();

            $arParams['text'] = $text;
            $arParams['msisdn'] = $phone;

            if (empty($arParams['sender'])) {
                $arParams['shortcode'] = $this->sender;
            } else {
                $arParams['shortcode'] = $arParams['sender'];
            }
            unset($arParams['sender']);

            $requestResult = $this->request('send', $arParams);

            if (!$requestResult->isSuccess()) {
                return $requestResult;
            } else {
                if (preg_match('/^([\w]{8}-[\w]{4}-[\w]{4}-[\w]{4}-[\w]{12})$/', trim($requestResult->getResult()), $match)) {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    $result->setMore('params', array(
                        'messageId' => trim($requestResult->getResult())
                    ));
                } else {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->addError($requestResult->getResult(), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $requestResult->getResult() ));
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
        public function getBalance()
        {
            return new \Bxmaker\SmsNotice\Result($this->getMessage('BALANCE_IS_NOT_DEFINED'));
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


                    $requestResult = $this->request('status', array(
                        'id' => $arSmsMore['PARAMS']['messageId']
                    ));
                    if ($requestResult->isSuccess()) {

                        $statusResult = new \Bxmaker\SmsNotice\Result();

                        switch(trim($requestResult->getResult()))
                        {
                            case 'delivered': {
                                $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                                break;
                            }
                            case 'billed':
                            case 'unknown':
                            case 'accepted': {
                                $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                                break;
                            }
                            case 'expired':{
                                $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                $statusResult->setMore('error_description', $this->getMessage('STATUS.expired'));
                                break;
                            }
                            case 'undeliverable':{
                                $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                $statusResult->setMore('error_description', $this->getMessage('STATUS.undeliverable'));
                                break;
                            }
                            case 'rejected':{
                                $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                $statusResult->setMore('error_description', $this->getMessage('STATUS.rejected'));
                                break;
                            }
                            default: {
                                $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                $statusResult->addError($requestResult->getResult(), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $requestResult->getResult() ));
                            }
                        }

                        $arResult[ $smsId ] = $statusResult;
                    }

                } else {
                    $arResult[ $smsId ] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
                }
            }

            $result->setMore('results', $arResult);

            return $result;
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
         * Описание сервиса что куда тывать, для страницы параметров сервиса
         *
         * @return mixed|string
         */
        public function getDescription()
        {
            return $this->getMessage('DESCRIPTION');
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

            $arParams['login'] = $this->login;
            $arParams['password'] = $this->password;

            $arParams = array_intersect_key($arParams, array_flip(array(
                'operation',
                'shortcode',
                'text',
                'msisdn',
                'login',
                'password',
                'id'
            )));

            switch ($method) {
                case 'send':
                    {
                        $arParams['operation'] = 'send';
                        break;
                    }
                case 'status':
                    {
                        $arParams['operation'] = 'status';
                        break;
                    }
                default:
                    {
                        return $result->addError('Unknown request method', 'UNKNOWN_REGUEST_METHOD');
                    }
            }



            $httpClient = $this->getHttpClient();
            $httpClient->setTimeout(2);
            $httpClient->post('http://newbsms.tele2.ru/api/', $this->toUtf($arParams));

            if ($httpClient->getStatus() == '200') {
                return $result->setResult($this->fromUtf($httpClient->getResult()));
            } else {
                return $result->addError($this->getMessage('ERROR_SERVICE_STATUS'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array( 'response' => $httpClient->getResult() ));
            }
        }

    }
