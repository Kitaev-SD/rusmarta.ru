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


    Class sms
    {

        //http://sms.ru/
        //5 ��� �� ���� �����

        const REQUEST_SUCCESS = 'success';
        const REQUEST_ERROR = 'error';

        private $oHttp = null;

        private $arParams = array(
            'user'      => '',
            'pwd'       => '',
            'sadr'      => '',
            'api_id'    => '',
            'test_mode' => ''
        );


        /**
         * �����������
         *
         * @param array $arParams
         * - string USER
         * - string PWD
         * - string SADR
         * - integer TEST_MODE
         */
        public function __construct($arParams = array())
        {
            if (is_null($this->oHttp)) {
                $this->oHttp = new \Bitrix\Main\Web\HttpClient();
            }

            if (array_key_exists('USER', $arParams)) {
                $this->arParams['user'] = trim($this->_getPreparedStr($arParams['USER']));
            }

            if (array_key_exists('PWD', $arParams)) {
                $this->arParams['pwd'] = trim($this->_getPreparedStr($arParams['PWD']));
            }

            if (array_key_exists('SADR', $arParams)) {
                $this->arParams['sadr'] = trim($this->_getPreparedStr($arParams['SADR']));
            }

            if (array_key_exists('API_ID', $arParams)) {
                $this->arParams['api_id'] = trim($this->_getPreparedStr($arParams['API_ID']));
            }

        }

        /**
         * ��������� �������� ���������
         *
         * @param      $name
         * @param null $default_value
         *
         * @return null
         */
        private function _getParam($name, $default_value = null)
        {
            return (isset($this->arParams[ $name ]) ? $this->arParams[ $name ] : $default_value);
        }


        /**
         * ���������
         *
         * @param $code
         *
         * @return mixed|string
         */
        private function _getMsg($code)
        {
            return GetMessage('bxmaker.smsnotice.sms.' . $code);
        }


        /**
         * �������� 1�� ��� ���������
         * @param $phone
         * @param $text
         * @param array $arParams
         * @return Result
         */
        public function send($phone, $text, $arParams = array())
        {
            $result = new Result();
            $arParams['text'] = $this->_getPreparedStr($text);
            $arParams['to'] = $phone;

            $response = $this->_makeRequest('message/send', $arParams);

            if (!empty($response)) {
                if ($response['status'] == 'OK') {

                    $arSmsResponse = reset($response['sms']);

                    if ($arSmsResponse['status'] == 'OK') {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                        $result->setMore('params', array(
                            'messageId' => $arSmsResponse['sms_id']
                        ));
                    } elseif ($arSmsResponse['status'] == 'ERROR') {
                        $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                        $result->setError(new Error($arSmsResponse['status_text'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $response )));
                    }

                    $result->setMore('response', $response);
                    return $result;

                } elseif ($response['status'] == 'ERROR') {
                    $result->setError(new Error($response['status_text'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $response )));
                    return $result;
                }
            }

            $result->setError(new Error('ERROR_SERVICE_RESPONSE', \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array( 'response' => $response )));
            return $result;

        }

        /**
         * �������� ������ ��� ���������
         * @param $arPack
         * @return Result
         */
        public function sendPack($arPack)
        {

            //		PackItem - array(
            //			"phone"    => $arItem['PHONE'],
            //			  "clientId" => $id,
            //			  "text"     => $arItem['TEXT'],
            //			  "sender"   => $arItem['SENDER']
            //		)

            $result = new Result();

            $arMsg = array(); // clientId => status

            //��������� �� ������������, ����� �� ������� ���������
            while (count($arPack['messages'])) {
                //����� �� ���������� ���������� ������ � ������ �������
                $arTmpPack = $arPack;

                $arSenderSms = array();
                $arPack['messages'] = array();

                $arPhoneToClientId = array(); //����� ����� �������� - id ������ � ����., ��� ��������� �������

                // ��������� �� ������������
                foreach ($arTmpPack['messages'] as $msg) {
                    if (!$msg['sender']) {
                        $msg['sender'] = $this->_getParam('sadr');
                    }
                    if (isset($arSenderSms[ $msg['sender'] ][ $msg['phone'] ])) {
                        //��� ���������� �������
                        $arPack['messages'][] = $msg;
                    } else {
                        // �������� ������
                        $arSenderSms[ $msg['sender'] ][ $msg['phone'] ] = $this->_getPreparedStr($msg['text']);
                        $arPhoneToClientId[ $msg['phone'] ] = $msg['clientId'];
                    }

                }

                foreach ($arSenderSms as $sender => $arMulti) {
                    // �������� �� 100 ���� ��������
                    $arMultiParts = array_chunk($arMulti, 99, true);
                    // ������ �� ����� ������
                    foreach ($arMultiParts as $arMultiPart) {

                        //�������
                        $response = $this->_makeRequest('message/sendPack', array(
                            'sender' => $sender,
                            'multi'  => $arMultiPart
                            // phone->text
                        ));

                        if (!empty($response)) {
                            if ($response['status'] == 'OK') {

                                foreach($response['sms'] as $phone => $smsStatus)
                                {
                                    if($smsStatus['status'] == 'OK')
                                    {
                                        $smsStatusResult = new Result(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                                        $smsStatusResult->setMore('params', array(
                                            'messageId' => $smsStatus['sms_id']
                                        ));

                                        $arMsg[$arPhoneToClientId[$phone]] = $smsStatusResult;
                                    }
                                    elseif ($smsStatus['status'] == 'ERROR') {
                                        $smsStatusResult = new Result(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                                        $smsStatusResult->setError(new Error($smsStatus['status_text'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'status' => $smsStatus )));

                                        $arMsg[$arPhoneToClientId[$phone]] = $smsStatusResult;
                                    }
                                }

                            } //��� ���� �����
                            elseif ($response['status'] == 'ERROR') {
                                foreach ($arMultiPart as $phone => $phoneText) {
                                    $arMsg[$arPhoneToClientId[$phone]] = new Result(new Error($response['status_text'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                                        'response' => $response
                                    )));
                                }
                            }
                        }

                    }
                }
            }

            $result->setResult(array(
                'messages' => $arMsg
            ));

            return $result;
        }


        /**
         * �������� �������
         * @return Result
         */
        public function getBalance()
        {
            $result = new Result();
            $response = $this->_makeRequest('balance', Array());

            if (!empty($response)) {
                if ($response['status'] == 'OK') {
                    $result->setResult(floatval($response['balance']) . $this->_getMsg('SERVICE.CURRENCY'));
                    return $result;

                } elseif ($response['status'] == 'ERROR') {
                    $result->setError(new Error($response['status_text'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $response )));
                    return $result;
                }
            }

            $result->setError(new Error('ERROR_SERVICE_RESPONSE', \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array( 'response' => $response )));

            return $result;
        }

        /**
         * ��������� ������ ��������� ������
         */
        public function notice()
        {
            return new Result();
        }


        /**
         * ������������ ����������� �����, ���������� ������� ��������� ������� ���� ���� �����
         *
         * @param $arSms
         *
         * @return Result
         */
        public function agent($arSms)
        {
            $result = new Result(true);
            $arResult = array();

            //���������� ������ ��������������� ���
            $arMessageId2SmsId = array();
            foreach ($arSms as $smsId => $arSmsMore) {
                if (isset($arSmsMore['PARAMS']['messageId'])) {
                    $arMessageId2SmsId[ $arSmsMore['PARAMS']['messageId'] ] = $smsId;

                } else {
                    $arError[] = new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                    $arResult[ $smsId ] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
                }
            }
                        

            //�������� �� ����� �� 100 ��
            $arMessageId2SmsIdParts = array_chunk($arMessageId2SmsId, 99, true);
            foreach ($arMessageId2SmsIdParts as $arMessageId2SmsIdPart) {
               
                $smsStatusesResult = $this->checkSmsListStatus($arMessageId2SmsIdPart);                
                if ($smsStatusesResult->isSuccess()) {
                    foreach ($smsStatusesResult->getMore('STATUS_LIST') as $smsId => $smsStatusResult) {
                        $arResult[ $smsId ] = $smsStatusResult;
                    }
                }
            }

            $result->setMore('results', $arResult);

            return $result;
        }


        // ��������� ������ ���� UTF-8
        private function _getPreparedStr($str)
        {
            /** @global CMain $APPLICATION */
            global $APPLICATION;

            return (!Manager::isUTF() ? $APPLICATION->ConvertCharsetArray($str, LANG_CHARSET, "UTF-8") : $str);
        }

        // ����� � UTF-8 ��������, ������� ���������� ��� ����������� �����������
        private function _getFromUtf($str)
        {
            /** @global CMain $APPLICATION */
            global $APPLICATION;

            return (!Manager::isUTF() ? $APPLICATION->ConvertCharsetArray($str, "UTF-8", LANG_CHARSET) : $str);
        }

        public function getParams()
        {

            return array(
                'USER'   => array(
                    'NAME'      => $this->_getMsg('PARAMS.USER'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.USER.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
                'PWD'    => array(
                    'NAME'      => $this->_getMsg('PARAMS.PWD'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.PWD.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
                'SADR'   => array(
                    'NAME'      => $this->_getMsg('PARAMS.SADR'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.SADR.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                ),
                'API_ID' => array(
                    'NAME'      => $this->_getMsg('PARAMS.API_ID'),
                    'NAME_HINT' => $this->_getMsg('PARAMS.API_ID.HINT'),
                    'TYPE'      => 'STRING',
                    'VALUE'     => ''
                )
            );
        }

        /**
         * �������� ������� ��� ���� ������, ��� �������� ���������� �������
         * @return mixed|string
         */
        public function getDescription()
        {
            return $this->_getMsg('DESCRIPTION');
        }


        /**
         * ��������� ������
         *
         * @param string $function
         * @param array $params
         *
         * @return array
         */
        protected function _makeRequest($method, array $params = array())
        {
            $p = array(
                'partner_id' => '129315',
                'json'       => '1'
            );

            if (!!$this->_getParam('api_id')) {
                $p = array_merge(array(
                    'api_id' => $this->_getParam('api_id')
                ), $p);
            } else {
                $p = array_merge(array(
                    'login'    => $this->_getParam('user'),
                    'password' => $this->_getParam('pwd')
                ), $p);
            }

            $url = 'http://sms.ru/my/balance?';

            switch ($method) {
                case 'message/status':
                    {
                        $url = 'http://sms.ru/sms/status?';
                        $p = array_merge(array(
                            'id' => $params['id']
                        ), $p);
                        break;
                    }
                case 'message/send':
                    {
                        $url = 'http://sms.ru/sms/send?';
                        $p = array_merge(array(
                            'text' => $params['text'],
                            'to'   => $params['to']
                        ), $p);
                        if ($this->_strlen($this->_getParam('sadr'))) {
                            $p['from'] = $this->_getParam('sadr');
                        }
                        break;
                    }
                case 'message/sendPack':
                    {
                        $url = 'http://sms.ru/sms/send?';
                        $p = array_merge(array(
                            'multi' => $params['multi'],
                            'from'  => (isset($params['sender']) ? $params['sender'] : $this->_getParam('sadr'))
                        ), $p);
                        break;
                    }
                case 'balance':
                    {
                        break;
                    }
                default:
                    {
                        return false;
                    }
            }

            $url .= http_build_query($p);
            $response = $this->oHttp->get($url);

            // ���������� ������
            $response = json_decode($response, true);

            // �������������� ���������
            $this->response = $this->_getFromUtf($response);

            return $this->response;
        }

        private function _strlen($str)
        {
            return (Manager::isUTF() ? mb_strlen($str) : strlen($str));
        }


        /**
         * ��������� ������ �������� ���������
         *
         * @param array[] $messageId2SmsId - ������ ������������ [[messageId => smsId], ...]
         *
         * @return Result
         */
        private function checkSmsListStatus($messageId2SmsId)
        {
            //        http://bxmaker.sms.ru/?panel=api&subpanel=method&show=sms/status
            $result = new Result();
            $arStatuses = array();

            $response = $this->_makeRequest('message/status', array(
                'id' => implode(',', array_keys($messageId2SmsId))
            ));
            $result->setMore('response', $response);

            //����  �������� ����������� �����
            if (empty($response)) {
                $result->setError(new Error('ERROR_SERVICE_RESPONSE', \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE, array(
                    'response' => $response
                )));
                return $result;
            }

            //���� ������
            if ($response['status'] == 'ERROR') {
                $result->setError(new Error($response['status_text'], \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $response )));
                return $result;
            }
            
            //��� ��� ����� ��������� ��������� �����, �� ������ �� ���������
            if(is_array($response['sms']))
            {
                $arSmsStatusNew = array();
                foreach($response['sms'] as $smsPath => $smsPathStatus)
                {
                    foreach(explode(',', $smsPath) as $smsId)
                    {
                        $arSmsStatusNew[$smsId] = $smsPathStatus;
                    }
                }
                $response['sms'] = $arSmsStatusNew;
            }
            else
            {
                $result->setError(new Error('Empty response by sms status', \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array( 'response' => $response )));
                return $result;
            }


            // ������������� ������� --
            foreach ($messageId2SmsId as $messageId => $smsId) {
                if (!isset($response['sms'][ $messageId ]['status_code'])) {
                    continue;
                }

                $resultStatus = new Result();

                switch ($response['sms'][ $messageId ]['status_code']) {
                    case $this->_getMsg('MSG_STATUS_M1'):
                        {
                            //�� �������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_M1_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_100'):
                        {
                            //��������� ��������� � ����� �������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_101'):
                        {
                            //��������� ���������� ���������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_102'):
                        {
                            //��������� ���������� (� ����)
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_103'):
                        {
                            //��������� ����������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_104'):
                        {
                            //����������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_104_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_105'):
                        {
                            //�� ����� ���� ����������: ������� ����������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_105_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_106'):
                        {
                            //�� ����� ���� ����������: ���� � ��������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_106_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_107'):
                        {
                            //�� ����� ���� ����������: ����������� �������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_107_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_108'):
                        {
                            //�� ����� ���� ����������: ���������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_108_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_200'):
                        {
                            //������������ api_id
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_200_VALUE'));
                            break;
                        }

                    case $this->_getMsg('MSG_STATUS_201'):
                        {
                            //�� ������� ������� �� ������� �����
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_201_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_202'):
                        {
                            //����������� ������ ����������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_202_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_203'):
                        {
                            //��� ������ ���������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_203_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_204'):
                        {
                            //��� ����������� �� ����������� � ��������������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_204_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_205'):
                        {
                            //��������� ������� ������� (��������� 8 ���)
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_205_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_206'):
                        {
                            //����� �������� ��� ��� �������� ������� ����� �� �������� ���������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_206_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_207'):
                        {
                            //�� ���� ����� (��� ���� �� �������) ������ ���������� ���������, ���� ������� ����� 100 ������� � ������ �����������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_207_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_208'):
                        {
                            //�������� time ������ �����������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_208_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_209'):
                        {
                            //�� �������� ���� ����� (��� ���� �� �������) � ����-����
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_209_VALUE'));
                            break;
                        }


                    case $this->_getMsg('MSG_STATUS_210'):
                        {
                            //������������ GET, ��� ���������� ������������ POST
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_210_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_211'):
                        {
                            //����� �� ������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_211_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_212'):
                        {
                            //����� ��������� ���������� �������� � ��������� UTF-8 (�� �������� � ������ ���������)
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_212_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_220'):
                        {
                            //������ �������� ����������, ���������� ���� �����.
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_220_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_230'):
                        {
                            //�������� ����� ����� ���������� ��������� �� ���� ����� � ����.
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_230_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_231'):
                        {
                            //�������� ����� ���������� ��������� �� ���� ����� � ������.
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_231_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_232'):
                        {
                            //�������� ����� ���������� ��������� �� ���� ����� � ����.
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_232_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_300'):
                        {
                            //������������ token (�������� ����� ���� ��������, ���� ��� IP ���������)
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_300_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_301'):
                        {
                            //������������ ������, ���� ������������ �� ������
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_301_VALUE'));
                            break;
                        }
                    case $this->_getMsg('MSG_STATUS_302'):
                        {
                            //������������ �����������, �� ������� �� ����������� (������������ �� ���� ���, ���������� � ��������������� ���)
                            $resultStatus->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                            $resultStatus->setMore('error_description', $this->_getMsg('MSG_STATUS_302_VALUE'));
                            break;
                        }
                    default:
                        {
                            $resultStatus->setError(new Error($response, \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                                'message_id' => $messageId2SmsId,
                                'response'   => $response
                            )));
                        }
                }

                $arStatuses[ $smsId ] = $resultStatus;
            }

            $result->setMore('STATUS_LIST', $arStatuses);

            return $result;
        }

        /*
         * �������� ������, ������� ����� �����������
         */
        private function _getErrorDescription($error_msg, $error_type = '')
        {
            if ($error_type == 'balance') {
                switch ($error_msg) {
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_100'):
                        {
                            return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_100_VALUE');
                            break;
                        }
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_200'):
                        {
                            return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_200_VALUE');
                            break;
                        }
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_210'):
                        {
                            return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_210_VALUE');
                            break;
                        }
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_211'):
                        {
                            return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_211_VALUE');
                            break;
                        }
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_220'):
                        {
                            return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_220_VALUE');
                            break;
                        }
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_300'):
                        {
                            return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_300_VALUE');
                            break;
                        }
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_301'):
                        {
                            return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_301_VALUE');
                            break;
                        }
                    case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_302'):
                        {
                            return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_302_VALUE');
                            break;
                        }
                    default:
                        {
                            return $error_msg;
                        }
                }
            } else {

                switch ($error_msg) {
                    case $this->_getMsg('MSG_SEND_100'):
                        {
                            return $this->_getMsg('MSG_SEND_100_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_200'):
                        {
                            return $this->_getMsg('MSG_SEND_200_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_201'):
                        {
                            return $this->_getMsg('MSG_SEND_201_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_202'):
                        {
                            return $this->_getMsg('MSG_SEND_202_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_203'):
                        {
                            return $this->_getMsg('MSG_SEND_203_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_204'):
                        {
                            return $this->_getMsg('MSG_SEND_204_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_205'):
                        {
                            return $this->_getMsg('MSG_SEND_205_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_206'):
                        {
                            return $this->_getMsg('MSG_SEND_206_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_207'):
                        {
                            return $this->_getMsg('MSG_SEND_207_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_208'):
                        {
                            return $this->_getMsg('MSG_SEND_208_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_209'):
                        {
                            return $this->_getMsg('MSG_SEND_209_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_210'):
                        {
                            return $this->_getMsg('MSG_SEND_210_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_211'):
                        {
                            return $this->_getMsg('MSG_SEND_211_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_212'):
                        {
                            return $this->_getMsg('MSG_SEND_212_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_220'):
                        {
                            return $this->_getMsg('MSG_SEND_220_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_230'):
                        {
                            return $this->_getMsg('MSG_SEND_230_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_300'):
                        {
                            return $this->_getMsg('MSG_SEND_300_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_301'):
                        {
                            return $this->_getMsg('MSG_SEND_301_VALUE');
                            break;
                        }
                    case $this->_getMsg('MSG_SEND_302'):
                        {
                            return $this->_getMsg('MSG_SEND_302_VALUE');
                            break;
                        }

                    default:
                        {
                            return $error_msg;
                        }
                }
            }

        }


    }
