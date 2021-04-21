<?
    
    /**
     * Class \Bxmaker\SmsNotice\Manager
     */
    class Bxmaker_SmsNotice_Manager_Demo
    {
        
        static private $instance = null;
        
        private $bDemo = null;
        private $bDemoExpired = null;
        public $phoneRegExpStr = null;
        
        
        private function __construct()
        {
        
        }
        
        private function __clone()
        {
        }
        
        
        /**
         * Проверка демо режима
         *
         * @return bool
         */
        final public function isDemo()
        {
            if (is_null($this->bDemo)) {
                $this->_checkDemo();
            }
            return $this->bDemo;
        }
        
        /**
         * Проверка не истекло ли время демо режима
         *
         * @return bool
         */
        final public function isExpired()
        {
            if (is_null($this->bDemoExpired)) {
                $this->_checkDemo();
            }
            return $this->bDemoExpired;
        }
        
        /**
         * Инициализация параметров работы модуля
         */
        final private function _checkDemo()
        {
            $module = new CModule();
            if ($module->IncludeModuleEx('bxmaker.smsnotice') == constant('M' . 'O' . 'D' . 'U' . 'L' . 'E' . '_' . 'N' . 'O' . 'T' . '_' . 'F' . 'O' . 'U' . 'N' . 'D')) {
                $this->bDemo = false;
                $this->bDemoExpired = false;
            } elseif ($module->IncludeModuleEx('bxmaker.smsnotice') == constant('M' . 'O' . 'D' . 'U' . 'LE' . '_' . 'D' . 'E' . 'M' . 'O')) {
                $this->bDemo = true;
                $this->bDemoExpired = false;
            } elseif ($module->IncludeModuleEx('bxmaker.smsnotice') == constant('M' . 'O' . 'D' . 'U' . 'L' . 'E' . '_' . 'D' . 'E' . 'M' . 'O' . '_' . 'E' . 'X' . 'P' . 'I' . 'R' . 'E' . 'D')) {
                $this->bDemo = true;
                $this->bDemoExpired = true;
            }
        }
        
        
        /** Сообщение о Демо режиме */
        public function showDemoMessage()
        {
            if ($this->isDemo()) {
                if ($this->isExpired()) {
                    echo '<div class="ap-bxmaker_smsnotice-notice-box expired" >' . $this->getMsg('DEMO_EXPIRED_NOTICE') . '</div>';
                } else {
                    echo '<div class="ap-bxmaker_smsnotice-notice-box" >' . $this->getMsg('DEMO_NOTICE') . '</div>';
                }
            }
        }
        
        
        /**
         * Проверка правильноси номера
         *
         * @param $phone
         *
         * @return bool
         */
        public function isValidPhone($phone)
        {
            $phone = $this->getPreparePhone($phone);
            
            $phoneNumber = \Bitrix\Main\PhoneNumber\Parser::getInstance()->parse('+' . $phone);
            if ($phoneNumber->isValid()) {
                return true;
            }
            return false;
        }
        
        /**
         * Подготовка номера телефона к единому формату, например 79991112233
         *
         * @param $phone
         *
         * @return mixed
         */
        public function getPreparePhone($phone)
        {
            $phone = preg_replace('/[^\d]+/', '', $phone);
            
            //нужно ли подставлять в начало номера 7, когда указывается номер например так - (926) 111 22 33
            if ($this->checkNeedRepirePhone()) {
                $phone = preg_replace('/^([0-9]{1}\d{9})$/', '7\1', $phone);
            }
            
            $phoneNumber = \Bitrix\Main\PhoneNumber\Parser::getInstance()->parse('+' . $phone);
            if (!$phoneNumber->isValid() || is_null($phoneNumber->getCountry())) {
                //замена 8 на +7 для росийских номеров
                $phone = preg_replace('/^8([0-9]{10})$/', '7\1', $phone);
            }
            
            $phoneNumber = \Bitrix\Main\PhoneNumber\Parser::getInstance()->parse('+' . $phone);
            
            $phone = $phoneNumber->format(\Bitrix\Main\PhoneNumber\Format::E164);
            
            return preg_replace('/^\+/', '', $phone);
        }
        
        
        /**
         * Единый формат ошибок для записи в базу, для быстрого анализа неполадок
         *
         * @param array $errors
         *
         * @return string
         */
        protected function getCommentFromErrors($errors = array())
        {
            $comment = '';
            
            foreach ($errors as $error) {
                $comment .= $error->getMessage() . (!empty($error->getMore()) ? "\r\n>>>>>>>>\r\n" . var_export($error->getMore(),
                            true) . "\r\n<<<<<<<<<\r\n\r\n" : '');
            }
            
            return $comment;
        }
        
        /**
         * Замена в шаблоне плэйсхолдеров на действительные значения
         *
         * @param       $arTemplate
         * @param array $arFields
         */
        protected function prepareTemplate(&$arTemplate, $arFields = array())
        {
            if ($arSiteData = $this->getSiteData()) {
                $arFields['SITE_NAME'] = $arSiteData['SITE_NAME'];
                $arFields['SERVER_NAME'] = $arSiteData['SERVER_NAME'];
            }
            foreach ($arFields as $find => $replacement) {
                $arTemplate['PHONE'] = trim(preg_replace('/#' . trim($find) . '#/', (string)$replacement, $arTemplate['PHONE']));
                $arTemplate['TEXT'] = trim(preg_replace('/#' . trim($find) . '#/', (string)$replacement, $arTemplate['TEXT']));
            }
        }
        
        /**
         * Используется агентом, для обновления статусов сообщений, для сервисов где есть задержки
         *
         * @return bool
         * @throws \Bitrix\Main\ArgumentException
         */
        public function agentCheckSmsStatus()
        {
            $arSmsCheck = array();
            
            $dateStart = new \Bitrix\Main\Type\DateTime();
            $dateStart->add('-18 hours');
            
            $dateStop = new \Bitrix\Main\Type\DateTime();
            $dateStop->add('-2 minutes');
            
            $oManagerTable = $this->getTable();
            $dbr = $oManagerTable->getList(array(
                'filter' => array(
                    'STATUS' => \Bxmaker\SmsNotice\SMS_STATUS_SENT,
                    '><CREATED' => array(
                        $dateStart,
                        $dateStop
                    )
                ),
                'order' => array(
                    'SITE_ID' => 'ASC',
                    'CREATED' => 'ASC'
                ),
                'limit' => '50'
            ));
            while ($ar = $dbr->fetch()) {
                $arSmsCheck[$ar['SITE_ID']][$ar['SERVICE_ID']][$ar['ID']] = $ar;
            }
            
            foreach ($arSmsCheck as $siteId => $arSmsList) {
                foreach ($arSmsList as $serviceId => $arSms) {
                    
                    $resService = $this->initService($serviceId, $siteId);
                    
                    if ($resService->isSuccess()) {
                        
                        $result = $this->getService($siteId, $serviceId)->agent($arSms);
                        $arResults = $result->getMore('results');
                        
                        if ($arResults) {
                            foreach ($arResults as $smsId => $smsResult) {
                                $this->updateItemStatus($smsId, $smsResult, $arSms[$smsId]);
                            }
                        }
                    }
                }
            }
            
            
            return true;
            
        }
        
        /**
         * Обновление состояния смс
         *
         * @param      $smsId
         * @param      $smsResult
         * @param null $arCurrentFields
         */
        private function updateItemStatus($smsId, $smsResult, $arCurrentFields = null)
        {
            
            $arParams = array();
            
            if(!is_null($arCurrentFields))
            {
                if(isset($arCurrentFields['PARAMS']))
                {
                    $arParams = $arCurrentFields['PARAMS'];
                }
            }
            
            //success
            if ($smsResult->isSuccess()) {
                
                if ($smsResult->getMore('params')) {
                    $arParams = array_merge($arParams,$smsResult->getMore('params'));
                }
    
                // если есть описание ошикби, то добавим
                if ($smsResult->getMore('error_description')) {
                    $arParams['error_description'] = $smsResult->getMore('error_description');
                }
                
                switch ($smsResult->getResult()) {
                    case \Bxmaker\SmsNotice\SMS_STATUS_DELIVERED:
                        {
                            unset($arParams['error_description']);
                            
                            $this->getTable()->update($smsId, array(
                                'STATUS' => \Bxmaker\SmsNotice\SMS_STATUS_DELIVERED,
                                'COMMENT' => '',
                                'PARAMS' => $arParams
                            ));
                            
                            break;
                        }
                    case \Bxmaker\SmsNotice\SMS_STATUS_SENT:
                        {
                            $this->getTable()->update($smsId, array(
                                'STATUS' => \Bxmaker\SmsNotice\SMS_STATUS_SENT,
                                'COMMENT' => '',
                                'PARAMS' => $arParams
                            ));
                            break;
                        }
                    case \Bxmaker\SmsNotice\SMS_STATUS_ERROR:
                        {
                            
                            $arUpdateFields = array(
                                'STATUS' => \Bxmaker\SmsNotice\SMS_STATUS_ERROR,
                                'COMMENT' => '',
                                'PARAMS' => $arParams
                            );
                            $this->getTable()->update($smsId, $arUpdateFields);
                            
                            break;
                        }
                }
            } //error
            else {
                
                $this->getTable()->update($smsId, array(
                    'STATUS' => \Bxmaker\SmsNotice\SMS_STATUS_ERROR,
                    'COMMENT' => $this->getCommentFromErrors($smsResult->getErrors())
                ));
            }
        }
        
        
        /**
         * Отложенная отправка смс на агентах
         *
         * @return bool
         * @throws \Bitrix\Main\ArgumentException
         * @throws \Exception
         */
        public
        function sendQueue()
        {
            $arSms = array();
            
            $dbr = $this->getTable()->getList(array(
                'filter' => array(
                    'STATUS' => \Bxmaker\SmsNotice\SMS_STATUS_WAIT
                ),
                'order' => array(
                    'ID' => 'ASC'
                ),
                'limit' => $this->getParam('HANDLER.QUEUE_LIMIT', '200')
            ));
            while ($ar = $dbr->fetch()) {
                $arSms[$ar['SITE_ID']][$ar['SERVICE_ID']][$ar['ID']] = $ar;
            }
            
            
            foreach ($arSms as $siteId => $service) {
                //готовим сообщения
                foreach ($service as $serviceId => $arItems) {
                    
                    $serviceInitResult = $this->initService($serviceId, $siteId);
                    if ($serviceInitResult->isSuccess()) {
                        $arFields = array(
                            'messages' => array()
                        );
                        
                        foreach ($arItems as $id => $arItem) {
                            $arFields['messages'][] = array(
                                "phone" => $arItem['PHONE'],
                                "clientId" => $id,
                                "text" => $arItem['TEXT'],
                                "sender" => $arItem['SENDER']
                            );
                        }
                        
                        $oService = $this->getService($siteId, $serviceId);
                        
                        if (is_null($oService)) {
                            continue;
                        }
                        
                        $result = $oService->sendPack($arFields);
                        
                        if ($result->isSuccess()) {
                            
                            $arSmsIdResult = $result->getResult();
                            
                            foreach ($arSmsIdResult['messages'] as $smsId => $smsResult) {
                                $this->updateItemStatus($smsId, $smsResult);
                            }
                        } else {
                        
                        }
                    }
                }
            }
        }
        
        
        /**
         * Отправка сообщения и добавление при успешном результате даных в базу
         *
         * @param      $phone
         * @param      $text
         * @param      $siteId
         * @param null $template
         * @param null $sender
         * @param null $event
         * @param bool $bTranslit
         *
         * @return \BXmaker\SmsNotice\Result
         */
        protected
        function sendSms(
            $phone,
            $text,
            $siteId,
            $template = null,
            $sender = null,
            $event = null,
            $bTranslit = false
        ) {
            global $USER;
            
            if ($this->bDemoExpired) {
                return new \Bxmaker\SmsNotice\Result(new \Bxmaker\SmsNotice\Error($this->getMsg('MANAGER.MODULE_DEMO_EXPIRED'),
                    \Bxmaker\SmsNotice\ERROR_SERVICE_INITIALIZATION));
            }
            
            
            // опдготовка номера телефона
            $phone = $this->getPreparePhone($phone);
            
            
            if (!$this->isValidPhone($phone)) {
                return new \Bxmaker\SmsNotice\Result(new \Bxmaker\SmsNotice\Error($this->getMsg('MANAGER.ERROR_INVALID_PHONE'),
                    \Bxmaker\SmsNotice\ERROR_INVALID_PHONE, array(
                        'METHOD' => 'sendSms',
                        'PHONE' => $phone,
                        'PHONE_PREPARED' => $this->getPreparePhone($phone),
                        'TEXT' => $text,
                        'SITE_ID' => $siteId,
                        'TEMPLATE' => $template
                    )));
            }
            
            
            // ограничнеие на отправку определнным группам польвзаотелей
            // когда менеджер оформляет заказ, он это делает за другого человека,
            // нужно оперделить исходного пользвоателя и проверить поля его
            // по номеру телефона например
            if ($this->isNeedStopSendSmsByUserPhone($phone, $siteId)) {
                return new \Bxmaker\SmsNotice\Result(new \Bxmaker\SmsNotice\Error($this->getMsg('MANAGER.ERROR_SKIP_GROUP'),
                    \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                        'METHOD' => 'sendSms',
                        'PHONE' => $phone,
                        'PHONE_PREPARED' => $this->getPreparePhone($phone),
                        'TEXT' => $text,
                        'SITE_ID' => $siteId,
                        'TEMPLATE' => $template
                    )));
            }
            
            
            // транслит елси необходим
            if ($bTranslit) {
                $text = $this->getTranslitText($text);
            }
            
            // инициализация смс сервиса
            $arCurrentSeviceData = $this->getServiceParam(null, $siteId);
            
            
            // В режиме отладки просто сохраняем в базу, фактиче кой отправки не происходит ------------
            if ($this->isDebug()) {
                $result = new \Bxmaker\SmsNotice\Result(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                $result->setMore('phone', $phone);
                $result->setMore('text', $text);
                $result->setMore('template', $template);
                
                
                $resSaveSms = $this->getTable()->add(array(
                    'PHONE' => $this->getPreparePhone($phone),
                    'TEXT' => $text,
                    'CREATED' => new \Bitrix\Main\Type\DateTime(),
                    'STATUS' => $result->getResult(),
                    'TYPE_ID' => $template,
                    'COMMENT' => $this->getMsg('MANAGER.SEND_SMS_DEBUG_MODE'),
                    'SERVICE_ID' => (isset($arCurrentSeviceData['ID']) ? $arCurrentSeviceData['ID'] : null),
                    'SENDER' => $sender,
                    'SITE_ID' => $siteId,
                    'EVENT' => $event
                ));
                if (!$resSaveSms->isSuccess()) {
                    $result->setMore('save_sms_error', $resSaveSms->getErrorMessages());
                }
                
                return $result;
            }
            
            
            // сохранение в базу ---------------------------------------------------------------------------------
            $arSms = array(
                'PHONE' => $phone,
                'TEXT' => $text,
                'CREATED' => new \Bitrix\Main\Type\DateTime(),
                'STATUS' => ($this->isWaitSending() ? \Bxmaker\SmsNotice\SMS_STATUS_WAIT : \Bxmaker\SmsNotice\SMS_STATUS_SENT),
                //значение поумолчанию
                'TYPE_ID' => $template,
                'SERVICE_ID' => (isset($arCurrentSeviceData['ID']) ? $arCurrentSeviceData['ID'] : null),
                'SITE_ID' => $siteId,
                'SENDER' => $sender,
                'EVENT' => $event
            );
            $resSaveSms = $this->getTable()->add($arSms);
            if (!$resSaveSms->isSuccess()) {
                //ошибка сохранения смс в базе
                return new \Bxmaker\SmsNotice\Result(new \Bxmaker\SmsNotice\Error($resSaveSms->getErrorMessages(), 'save_sms_error', $arSms));
            }
            $smsId = $resSaveSms->getId();
            
            
            /**
             * @var Result $result
             */
            
            //предварительный результат отправки
            $result = new \BXmaker\SmsNotice\Result(\Bxmaker\SmsNotice\SMS_STATUS_WAIT);
            $result->setMore('smsId', $smsId);
            
            // если не включена отложенная отправка, то отправляем сразу---------------------------------------
            if (!$this->isWaitSending()) {
                
                $result = $this->getService($siteId)->send($phone, $text, array(
                    'smsId' => $smsId,
                    'service' => $arCurrentSeviceData,
                    'sender' => $sender,
                    'event' => $event
                ));
                
                $this->updateItemStatus($smsId, $result);
                
            }
            
            return $result;
        }
        
        
        /**
         * Замена символов транслитом
         *
         * @param $text
         *
         * @return string
         */
        public
        function getTranslitText(
            $text
        ) {
            
            static $search = array();
            
            $lang = 'ru';
            
            if (!isset($search[$lang])) {
                $mess = IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/js_core_translit.php", $lang, true);
                $trans_from = explode(",", $mess["TRANS_FROM"]);
                $trans_to = explode(",", $mess["TRANS_TO"]);
                foreach ($trans_from as $i => $from) {
                    $search[$lang][$from] = $trans_to[$i];
                }
            }
            
            $len = strlen($text);
            $text_new = '';
            
            for ($i = 0; $i < $len; $i++) {
                $chr = substr($text, $i, 1);
                
                if (preg_match("/[a-zA-Z0-9]/" . BX_UTF_PCRE_MODIFIER, $chr)) {
                    $text_new .= $chr;
                } else {
                    if (array_key_exists($chr, $search[$lang])) {
                        $text_new .= $search[$lang][$chr];
                    } else {
                        $text_new .= $chr;
                    }
                }
            }
            
            $text_new = preg_replace('/\x20+/', ' ', $text_new);
            
            return trim($text_new);
            
        }
        
        /**
         * Вернет массив с описанием шаблонов для js
         *
         * @param null $siteId
         *
         * @return array
         * @throws \Bitrix\Main\ArgumentException
         * @throws \Bitrix\Main\ArgumentOutOfRangeException
         * @throws \Bitrix\Main\ObjectPropertyException
         * @throws \Bitrix\Main\SystemException
         */
        public
        function getTemplateForJs(
            $siteId = null
        ) {
            $arReturn = array();
            
            if (is_null($siteId)) {
                $siteId = $this->getCurrentSiteId();
            }
            
            $oTemplate = new \Bxmaker\SmsNotice\TemplateTable();
            
            $dbrTemplate = $oTemplate->getList(array(
                'select' => array(
                    '*',
                    'TYPE' => 'TYPE.*'
                ),
                'filter' => array(
                    'SITE.SID' => $siteId,
                    '!=TYPE_ID' => 0
                )
            ));
            while ($ar = $dbrTemplate->fetch()) {
                
                $arTempalte = array(
                    'ID' => $ar['ID'],
                    'NAME' => $ar['NAME'],
                    'LABEL' => $ar['NAME'] . ' (' . $ar['TYPECODE'] . ')',
                    'TEXT' => $ar['TEXT'],
                    'PARAMS' => array(),
                    //парамтеры из описания типа шаблона, ключ => название
                    'REQUEST' => array(),
                    // перечисление клчюей из текста шаблона
                    'TRANSLIT' => (bool)$ar['TRANSLIT']
                );
                
                //парамтеры которые расписаны в типе шаблона---
                $arParams = explode("\n", $ar['TYPEDESCR']);
                foreach ($arParams as $row) {
                    $arRow = explode('-', $row, 2);
                    $arTempalte['PARAMS'][trim($arRow[0])] = trim($arRow[1]);
                }
                
                // параметры из текста шаблона --------
                if (preg_match_all('/(#[\w\.]+#)/im', $ar['TEXT'], $match, PREG_PATTERN_ORDER)) {
                    $arTempalte['REQUEST'] = $match[1];
                }
                
                $arReturn[] = $arTempalte;
            }
            
            \Bitrix\Main\Type\Collection::sortByColumn($arReturn, array('LABEL' => SORT_ASC));
            
            
            return $arReturn;
        }
        
        
        /**
         * Вренет список отправленных смс на указанный номер, для использвоаняи в js
         *
         * @param      $phone
         * @param null $siteId
         *
         * @return array
         * @throws \Bitrix\Main\ArgumentException
         * @throws \Bitrix\Main\ArgumentOutOfRangeException
         * @throws \Bitrix\Main\ObjectPropertyException
         * @throws \Bitrix\Main\SystemException
         */
        public
        function getPhoneHistoryForJs(
            $phone,
            $siteId = null,
            $limit = 10,
            $page = 1
        ) {
            $dateFormat = $GLOBALS["DB"]->DateFormatToPHP(CSite::GetDateFormat("FULL"));
            
            $limit = intval($limit);
            $page = intval($page);
            
            $arReturn = array(
                'items' => array(),
                'count' => 0,
                'limit' => intval($limit),
                'page' => intval($page)
            );
            
            if (is_null($siteId)) {
                $siteId = $this->getCurrentSiteId();
            }
            
            //общее количество
            $arReturn['count'] = $this->getTable()->getCount(array(
                'PHONE' => $phone,
                'SITE_ID' => $siteId
            ));
            
            // сами смс---
            $dbrItem = $this->getTable()->getList(array(
                'order' => array(
                    'ID' => 'DESC'
                ),
                'select' => array(
                    '*',
                    'TYPE_NAME' => 'TYPE.NAME'
                ),
                'filter' => array(
                    'SITE_ID' => $siteId,
                    'PHONE' => $phone
                ),
                'limit' => $limit,
                'offset' => $limit * ($page - 1)
            ));
            while ($arItem = $dbrItem->fetch()) {
                $ar = array(
                    'ID' => $arItem['ID'],
                    'TEXT' => $arItem['TEXT'],
                    'COMMENT' => $arItem['COMMENT'],
                    'CREATED' => $arItem['CREATED']->format($dateFormat),
                    'EVENT' => $arItem['EVENT'],
                    'TYPE_ID' => $arItem['TYPE_ID'],
                    'TYPE_NAME' => $arItem['TYPE_NAME'],
                );
                
                switch ($arItem['STATUS']) {
                    case \Bxmaker\SmsNotice\SMS_STATUS_SENT:
                        {
                            $ar['STATUS_NAME'] = $this->getMsg('SMS_STATUS_SENT');
                            $ar['STATUS'] = 'sent';
                            break;
                        }
                    case \Bxmaker\SmsNotice\SMS_STATUS_DELIVERED:
                        {
                            $ar['STATUS_NAME'] = $this->getMsg('SMS_STATUS_DELIVERED');
                            $ar['STATUS'] = 'delivered';
                            break;
                        }
                    case \Bxmaker\SmsNotice\SMS_STATUS_ERROR:
                        {
                            $ar['STATUS_NAME'] = $this->getMsg('SMS_STATUS_ERROR');
                            $ar['STATUS'] = 'error';
                            break;
                        }
                    case \Bxmaker\SmsNotice\SMS_STATUS_WAIT:
                        {
                            $ar['STATUS_NAME'] = $this->getMsg('SMS_STATUS_WAIT');
                            $ar['STATUS'] = 'wait';
                            break;
                        }
                    default:
                        {
                            $ar['STATUS_NAME'] = $this->getMsg('SMS_STATUS_ERROR');
                            $ar['STATUS'] = 'error';
                            break;
                        }
                }
                
                $arReturn['items'][] = $ar;
            }
            
            
            return $arReturn;
        }
        
        
        /**
         * Обработчик ajax запросов для административных страниц
         *
         * @throws \Bitrix\Main\ArgumentException
         * @throws \Bitrix\Main\LoaderException
         * @throws \Bitrix\Main\SystemException
         */
        public
        function adminPageAjaxHandler()
        {
            $app = \Bitrix\Main\Application::getInstance();
            $req = $app->getContext()->getRequest();
            
            // так как этот методы вызывается в отдельном файле для ajax запросов,
            // в котором уже произвеена конвертация данных под текущую кодировку
            // здесь повторно это делать не нужно
            
            $arAnswer = array(
                'response' => array(),
                'error' => array(),
            );
            
            
            switch ($req->getPost('method')) {
                case 'getTemplates':
                    {
                        //вернет шаблоны и подготоведенные даныне
                        
                        //проверка прав доступа
                        if (!$this->getBase()->canActionRight('R')) {
                            $arAnswer['error'] = array(
                                'msg' => $this->getMsg('AJAX.ACCESS_DENIED'),
                                'code' => 'ACCESS_DENIED',
                            );
                            break;
                        }
                        
                        $arAnswer['response'] = array(
                            'templates' => array(),
                            'data' => false
                        );
                        
                        $siteId = $req->getPost('siteId');
                        $orderId = $req->getPost('orderId');
                        if (!empty($orderId)) {
                            // определяем текущий сайт по идентификатору заказа
                            
                            $arOrder = \Bxmaker\SmsNotice\Handler::getOrderDataD7($orderId);
                            if (!empty($arOrder)) {
                                $arAnswer['response']['data'] = $arOrder;
                                
                                $siteId = $arOrder['SITE_ID'];
                                $phone = $arOrder['PHONE'];
                            }
                            
                        }
                        
                        if (!is_null($siteId)) {
                            
                            $arAnswer['response']['templates'] = $this->getTemplateForJs($siteId);
                            
                            $arSiteData = $this->getSiteData();
                            $arAnswer['response']['data']['SITE_NAME'] = (isset($arSiteData['SITE_NAME']) ? $arSiteData['SITE_NAME'] : '');
                            $arAnswer['response']['data']['SERVER_NAME'] = (isset($arSiteData['SERVER_NAME']) ? $arSiteData['SERVER_NAME'] : '');
                        } else {
                            $arAnswer['error'] = array(
                                'msg' => $this->getMsg('AJAX.SITE_ID_NOT_FOUND'),
                                'code' => 'SITE_ID_NOT_FOUND',
                            );
                        }
                        
                        break;
                    }
                case 'getPhoneHistory':
                    {
                        //вернет историю по номеру телефона (определит номер по номеру заказа если номер не указан)
                        
                        //проверка прав доступа
                        if (!$this->getBase()->canActionRight('R')) {
                            $arAnswer['error'] = array(
                                'msg' => $this->getMsg('AJAX.ACCESS_DENIED'),
                                'code' => 'ACCESS_DENIED',
                            );
                            break;
                        }
                        
                        $siteId = $req->getPost('siteId');
                        $orderId = $req->getPost('orderId');
                        $phone = $req->getPost('phone');
                        
                        if (empty($siteId)) {
                            $siteId = $this->getCurrentSiteId();
                        }
                        
                        if (!empty($orderId) && empty($phone)) {
                            // определяем текущий сайт по идентификатору заказа
                            
                            $arOrder = \Bxmaker\SmsNotice\Handler::getOrderDataD7($orderId);
                            if (!empty($arOrder)) {
                                $phone = $arOrder['PHONE'];
                            }
                        }
                        
                        if (!is_null($siteId)) {
                            
                            $arAnswer['response'] = $this->getPhoneHistoryForJs($phone, $siteId);
                            
                        } else {
                            $arAnswer['error'] = array(
                                'msg' => $this->getMsg('AJAX.SITE_ID_NOT_FOUND'),
                                'code' => 'SITE_ID_NOT_FOUND',
                            );
                        }
                        
                        break;
                    }
                case 'sendPreparedSms':
                    {
                        
                        // отправка готовогшо смс с уже подставлеными значениям в шаблон(если используется)
                        
                        //проверка прав доступа
                        if (!$this->getBase()->canActionRight('R')) {
                            $arAnswer['error'] = array(
                                'msg' => $this->getMsg('AJAX.ACCESS_DENIED'),
                                'code' => 'ACCESS_DENIED',
                            );
                            break;
                        }
                        
                        $siteId = $req->getPost('siteId');
                        $orderId = $req->getPost('orderId');
                        $phone = $req->getPost('phone');
                        $text = $req->getPost('text');
                        $templateId = $req->getPost('templateId');
                        
                        if (strlen(trim($text)) <= 1) {
                            $arAnswer['error'] = array(
                                'msg' => $this->getMsg('AJAX.TEXT_SMS_EMPTY'),
                                'code' => 'TEXT_SMS_EMPTY',
                            );
                            break;
                        }
                        
                        if (!empty($orderId) && empty($siteId)) {
                            // определяем текущий сайт по идентификатору заказа
                            $arOrder = \Bxmaker\SmsNotice\Handler::getOrderDataD7($orderId);
                            if (!is_null($arOrder)) {
                                $siteId = $arOrder['SITE_ID'];
                            }
                        }
                        
                        if (!is_null($siteId)) {
                            
                            $resultSend = $this->send($phone, $text, $siteId, false, false, $templateId);
                            if ($resultSend->isSuccess()) {
                                $arAnswer ['response'] = array(
                                    'msg' => $this->getMsg('AJAX.SMS_SEND_SUCCESSFULL')
                                );
                            } else {
                                /**
                                 * @var Error $error
                                 */
                                $errors = $resultSend->getErrors();
                                $error = reset($errors);
                                
                                $arAnswer['error'] = array(
                                    'msg' => $error->getMessage(),
                                    'code' => $error->getCode(),
                                    'more' => $error->getMore(),
                                );
                            }
                        } else {
                            $arAnswer['error'] = array(
                                'msg' => $this->getMsg('AJAX.SITE_ID_NOT_FOUND'),
                                'code' => 'SITE_ID_NOT_FOUND',
                            );
                        }
                        
                        break;
                    }
                default:
                    {
                        $arAnswer['error'] = array(
                            'msg' => $this->getMsg('AJAX.METHOD_NOT_FOUND'),
                            'code' => 'METHOD_NOT_FOUND',
                        );
                        break;
                    }
            }
            
            $this->getBase()->showJson($arAnswer);
        }
        
    }
    
    
    \Bitrix\Main\Loader::registerAutoLoadClasses('bxmaker.smsnotice', array(
        '\Bxmaker\SmsNotice\Agent' => 'lib/agent.php',
        '\Bxmaker\SmsNotice\Base' => 'lib/base.php',
        '\Bxmaker\SmsNotice\Error' => 'lib/error.php',
        '\Bxmaker\SmsNotice\Handler' => 'lib/handler.php',
        '\Bxmaker\SmsNotice\LogTable' => 'lib/log.php',
        '\Bxmaker\SmsNotice\Manager' => 'lib/manager.php',
        '\Bxmaker\SmsNotice\Result' => 'lib/result.php',
        '\Bxmaker\SmsNotice\ServiceTable' => 'lib/service.php',
        '\Bxmaker\SmsNotice\Service' => 'lib/service.php',
        '\Bxmaker\SmsNotice\TemplateTable' => 'lib/template.php',
        '\Bxmaker\SmsNotice\Template\TypeTable' => 'lib/template/type.php',
        '\Bxmaker\SmsNotice\Template\SiteTable' => 'lib/template/site.php',
        '\Bxmaker\SmsNotice\Template\Condition' => 'lib/template/condition.php',
        '\Bxmaker\SmsNotice\Template\CondCtrl' => 'lib/template/condition.php',
    ));

?>