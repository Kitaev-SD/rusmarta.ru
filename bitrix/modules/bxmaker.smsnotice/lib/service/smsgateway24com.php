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


    Class smsgateway24com extends Base
    {
        /**
         * @var string  токен дял отправки
         */
        private $token = null;
        /**
         * @var string Идентификатор устройства
         */
        private $deviceId = null;


        /**
         * Конструктор
         *
         * @param array $arParams
         * - string TOKEN
         */
        public function __construct($arParams = array())
        {
            $this->token = (isset($arParams['TOKEN']) ? $arParams['TOKEN'] : '');
            $this->deviceId = (isset($arParams['DEVICE_ID']) ? $arParams['DEVICE_ID'] : '');

            parent::__construct($arParams);
        }

        /**
         * @inheritdoc
         */
        public function getMessage($name, $arReplace = array())
        {
            return parent::getMessage('smsgateway24com.' . $name, $arReplace);
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
        public function send($phone, $text, $params = array())
        {
            $result = new Result();

            $arParams = array();
            $arParams['sendto'] = $phone;
            $arParams['body'] = $text;

            $requestResult = $this->request('send', $arParams);

            if (!$requestResult->isSuccess()) {
                return $requestResult;
            } else {
                $data = $requestResult->getResult();
                if ($data['error'] == 0) {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                    $result->setMore('params', array(
                        'messageId' => $data['sms_id']
                    ));
                } elseif ($data['error'] == 1) {
                    $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $result->setMore('error_description', $data['message']);
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

            $arMessagesStatus = array(); // clientId => status
            $arMessages = array();
            /*
                        // подготовим массив смс
                        foreach ($arPack['messages'] as $packItem) {
                            $arMessages[] = array(
                                'body' => $packItem['text'],
                                'sendto'  => $packItem['phone'],
                                'device_id' => $this->deviceId
                            );
                        }

                        // разобьем на части
                        $arParts = array_chunk($arMessages, 99, true);
                        foreach ($arParts as $arPart) {
                            $requestResult = $this->request('sendPack', array(
                                'smsdata' => $arPart
                            ));

                            echo '<pre class="debug">';
                            print_r($requestResult);
                            echo '</pre>';


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

            */

            // пока сделаем одиночную очередь
            foreach ($arPack['messages'] as $packItem) {
                $arMessagesStatus[ $packItem['clientId'] ] = $this->send($packItem['phone'], $packItem['text']);
            }

            $result = new \Bxmaker\SmsNotice\Result(array(
                'messages' => $arMessagesStatus
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
            //todo  обещали доработать api  фэтого сервиса проверить при возможности

            //подготовим массив идентфиикаторов смс           
            foreach ($arSms as $smsId => $arSmsMore) {
                if (isset($arSmsMore['PARAMS']['messageId'])) {

                    $requestResult = $this->request('status', array(
                        'sms_id' => $arSmsMore['PARAMS']['messageId']
                    ));
                    if ($requestResult->isSuccess()) {
                        $data = $requestResult->getResult();

                        $statusResult = new \Bxmaker\SmsNotice\Result();

                        if ($data['error'] == 0) {
                            switch ($data['status']) {
                                case 1:
                                case 2:
                                case 4:
                                case 6:
                                    {
                                        $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                                        break;
                                    }
                                case 5:
                                case 7:
                                    {
                                        $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                                        break;
                                    }
                                case 8:
                                case 9:
                                case 10:
                                case 11:
                                case 12:
                                case 100:
                                case 101:
                                case 3:{
                                    $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                    $statusResult->setMore('error_description', $data['status_description']);

                                    break;
                                }
                                default:
                                    {
                                        $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                        $statusResult->addError($data['error_description'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $data ));
                                        break;
                                    }
                            }

                        } elseif ($data['error'] == 1) {
                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $statusResult->setMore('error_description', $data['message']);
                        } else {
                            $statusResult->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $statusResult->addError($requestResult->getResult(), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $requestResult->getResult() ));
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
                'TOKEN'     => array(
                    'NAME'      => $this->getMessage('PARAMS.TOKEN'),
                    'NAME_HINT' => $this->getMessage('PARAMS.TOKEN.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
                'DEVICE_ID' => array(
                    'NAME'      => $this->getMessage('PARAMS.DEVICE_ID'),
                    'NAME_HINT' => $this->getMessage('PARAMS.DEVICE_ID.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                )
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

            $arParams['token'] = $this->token;

            $httpClient = $this->getHttpClient();
            $httpClient->setTimeout(2);

            switch ($method) {
                case 'send':
                    {
                        $url = '/getdata/addsms';

                        $arParams['device_id'] = $this->deviceId;

                        $arParams = $this->toUtf($arParams);
                        break;
                    }
                case 'sendPack':
                    {
                        $url = '/getdata/addalotofsms';

                        $arParams['datajson'] = json_encode($this->toUtf($arParams));
                        unset($arParams['token'], $arParams['smsdata']);

                        break;
                    }
                case 'status':
                    {
                        $url = '/getdata/getsmsstatus';
                        $arParams = $this->toUtf($arParams);
                        break;
                    }
                default:
                    {
                        return $result->addError('Unknown request method', 'UNKNOWN_REGUEST_METHOD');
                    }
            }

            $httpClient->post('https://smsgateway24.com' . $url, $arParams);


            if ($httpClient->getStatus() == '200') {
                return $result->setResult($this->fromUtf(json_decode($httpClient->getResult(), true)));
            } else {
                return $result->addError($this->getMessage('ERROR_SERVICE_STATUS'), \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array( 'response' => $httpClient->getResult() ));
            }
        }

    }
