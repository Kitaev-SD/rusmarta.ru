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


    Class terasmsru extends Base
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
         * - string TOKEN
         * - string SENDER
         */
        public function __construct($arParams = array())
        {
            $this->login = (isset($arParams['LOGIN']) ? $arParams['LOGIN'] : '');
            $this->password = (isset($arParams['PASSWORD']) ? $arParams['PASSWORD'] : '');
            $this->token = (isset($arParams['TOKEN']) ? $arParams['TOKEN'] : '');
            $this->sender = (isset($arParams['SENDER']) ? $arParams['SENDER'] : '');

            parent::__construct($arParams);
        }

        /**
         * @inheritdoc
         */
        public function getMessage($name, $arReplace = array())
        {
            return parent::getMessage('terasmsru.' . $name, $arReplace);
        }

        /**
         * @inheritdoc
         */
        public function getBalance()
        {
            $resultRequest = $this->request('balance');

            if ($resultRequest->isSuccess()) {
                $data = $resultRequest->getResult();
                if (intval($data['status']) < 0) {
                    return new Result(new Error($this->getErrorDescription($data['status']), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                        'response' => $data
                    )));
                } else {
                    return new Result($data['balance'] . ' ' . $this->getMessage('BALANCE_CURRENCY'));
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
            $arParams['target'] = $phone;

            if (empty($arParams['sender'])) {
                $arParams['sender'] = $this->sender;
            }

            $requestResult = $this->request('send', $arParams);

            if (!$requestResult->isSuccess()) {
                return $requestResult;
            } else {
                $data = $requestResult->getResult();
                if (intval($data['status']) < 0) {
                    return $this->getErrorDescription($data);
                } else {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    $result->setMore('params', array(
                        'messageId' => $data['message_infos'][0]['id']
                    ));
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

            $arMessagesStatus = array(); // clientId => status
            $arMessages = array();

            // подготовим массив смс
            foreach ($arPack['messages'] as $packItem) {
                $arMessages[] = array(
                    'message' => $packItem['text'],
                    'target'  => $packItem['phone'],
                    'sender'  => (!!$packItem['sender'] ? $packItem['sender'] : $this->sender),
                    'sms_id'  => $packItem['clientId']
                );
            }

            // разобьем на части
            $arParts = array_chunk($arMessages, 99, true);
            foreach ($arParts as $arPart) {
                $requestResult = $this->request('sendPack', array(
                    'smsPackage' => $arPart
                ));

                if ($requestResult->isSuccess()) {
                    $data = $requestResult->getResult();
                    if (isset($data['status']) && intval($data['status']) < 0) {
                        return $this->getErrorDescription($data);
                    } else {
                        foreach ($data as $item) {
                            if (intval($item['message_id']) < 0) {
                                $arMessagesStatus[ $item['sms_id'] ] = new Result(new Error($this->getErrorDescription($item['message_id']), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                                    'response' => $data
                                )));
                            } else {
                                $arMessagesStatus[ $item['sms_id'] ] = new Result(\Bxmaker\SmsNotice\SMS_STATUS_SENT, array(
                                    'params' => array(
                                        'messageId' => $item['message_id']
                                    )
                                ));
                            }
                        }
                    }
                } else {
                    //ничего не делаем, так как похоже ошибка на стороне сервиса
                    return $requestResult;
                }
            }


            $result = new \Bxmaker\SmsNotice\Result(array(
                'messages' => $arMessagesStatus
            ));

            return $result;
        }


        /**
         * @inheritdoc
         */
        public function agent($arSmsList)
        {
            $result = new Result(true);
            $arResult = array();

            $arMessageId2SmsId = array();
            foreach ($arSmsList as $smsId => $arSms) {
                if (empty($arSms['PARAMS']['messageId'])) {
                    $arResult[ $smsId ] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
                    continue;
                }
                $arMessageId2SmsId[ $arSms['PARAMS']['messageId'] ] = $smsId;
            }

            $arMessageId2SmsIdParts = array_chunk($arMessageId2SmsId, 200, true);
            foreach ($arMessageId2SmsIdParts as $arMessageId2SmsIdPart) {

                $requestResult = $this->request('status', array(
                    'id' => implode(',', array_keys($arMessageId2SmsIdPart))
                ));
                if ($requestResult->isSuccess()) {
                    $data = $requestResult->getResult();
                    if (intval($data['status']) < 0) {
                        // какая то ошибка
                    } else {
                        foreach ($data['message_infos'] as $item) {
                            $statusResult = new \Bxmaker\SmsNotice\Result();

                            if (intval($item['status']) < 0) {
                                $statusResult->setResult(\BXmaker\SmsNotice\SMS_STATUS_ERROR);
                                $statusResult->setMore('error_description', $this->getErrorDescription($item['status']));
                            } else {
                                switch ($item['status']) {
                                    case 0:
                                    case 1:
                                        {
                                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                                            break;
                                        }
                                    case 20:
                                    case 12:
                                        {
                                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                                            break;
                                            break;
                                        }
                                    case 13:
                                        {
                                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                            $statusResult->setMore('error_description', $this->getMessage('STATUS.expired'));
                                            break;
                                        }
                                    case 15:
                                        {
                                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                            $statusResult->setMore('error_description', $this->getMessage('STATUS.not_delivered'));
                                            break;
                                        }
                                    case 17:
                                        {
                                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                            $statusResult->setMore('error_description', $this->getMessage('STATUS.unknown'));
                                            break;
                                        }
                                    case 18:
                                        {
                                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                            $statusResult->setMore('error_description', $this->getMessage('STATUS.rejected'));
                                            break;
                                        }
                                    case 255:
                                        {
                                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                            $statusResult->setMore('error_description', $this->getMessage('STATUS.error'));
                                            break;
                                        }
                                    default:
                                        {
                                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                            $statusResult->addError($requestResult->getResult(), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $item ));
                                            break;
                                        }
                                }
                            }

                            $arResult[ $arMessageId2SmsId[ $item['id'] ] ] = $statusResult;
                        }
                    }

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
                'TOKEN'    => array(
                    'NAME'      => $this->getMessage('PARAMS.TOKEN'),
                    'NAME_HINT' => $this->getMessage('PARAMS.TOKEN.HINT'),
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

            if (!$this->token) {
                $arParams['password'] = $this->password;
            }


            $arParams = array_intersect_key($arParams, array_flip(array(
                'sender',
                'message',
                'target',
                'login',
                'password',
                'id',
                'smsPackage'
            )));

            switch ($method) {
                case 'balance':
                    {
                        $url = '/outbox/balance/json';
                        break;
                    }

                case 'send':
                    {
                        $url = '/outbox/send/json';
                        break;
                    }
                case 'sendPack':
                    {
                        $url = '/outbox/msend_json';
                        break;
                    }
                case 'status':
                    {
                        $url = '/outbox/status/json/';
                        break;
                    }
                default:
                    {
                        return $result->addError('Unknown request method', 'UNKNOWN_REGUEST_METHOD');
                    }
            }

            if ($this->token) {
                $arParams['sign'] = $this->getSign($arParams);
            }

            $httpClient = $this->getHttpClient();
            $httpClient->setTimeout(2);
            $httpClient->post('https://auth.terasms.ru' . $url, json_encode($this->toUtf($arParams)));


            if ($httpClient->getStatus() == '200') {
                return $result->setResult($this->fromUtf(json_decode($httpClient->getResult(), true)));
            } else {
                return $result->addError($this->getMessage('ERROR_SERVICE_STATUS'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array( 'response' => $httpClient->getResult() ));
            }
        }

        /**
         * Вренет подпись данных для запроса
         *
         * @param $arParams
         *
         * @return string
         */
        protected function getSign($arParams)
        {
            return md5($this->getSignChilds($this->toUtf($arParams)) . $this->token);
        }

        /**
         * Разбирает массив парамтеров и приводит к строке, для последующей подписи
         *
         * @param $arParams
         *
         * @return string
         */
        protected function getSignChilds($arParams)
        {
            ksort($arParams);

            $str = '';
            foreach ($arParams as $key => $value) {
                if (is_array($value)) {
                    $str .= $this->getSignChilds($value);
                } else {
                    $str .= $key . '=' . $value;
                }
            }


            return $str;
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
                case -20:
                case -30:
                case -40:
                case -45:
                case -50:
                case -60:
                case -70:
                case -80:
                case -90:
                case -100:
                case -110:
                case -120:
                case -130:
                case -140:
                case -160:
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
