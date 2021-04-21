<?
    
    namespace Bxmaker\SmsNotice;
    
    const SMS_STATUS_SENT = 1; //отправлено
    const SMS_STATUS_DELIVERED = 2; //доставлено
    const SMS_STATUS_ERROR = 3; //ошибка
    const SMS_STATUS_WAIT = 4; //ожидание отправки
    
    const ERROR_SERVICE_INITIALIZATION = 1001; // не удалось проинициализировать, подключить, не найдне
    const ERROR_SERVICE_RESPONSE = 1002; // неизвестный ответ сревиса
    const ERROR_SERVICE_CUSTOM = 1003; // произвольная ошибка иная
    
    const ERROR_TEMPLATE_NOT_FOUND = 2002; //не наден шаблон
    const ERROR_INVALID_PHONE = 2003; // не верно указан номер телефона
    
    const ERROR_EVENT = 3001; // ошики связанные с событиями
    
    use Bitrix\Main\Application;
    use \Bitrix\Main\Entity;
    use Bitrix\Main\Localization\Loc;
    use Bitrix\Main\Type\DateTime;
    use Bitrix\Main\Loader;
    use Bxmaker\SmsNotice\Template\Condition;
    
    Loc::loadMessages(__FILE__);
    
    //include_once(dirname(__FILE__).'/../include.php');
    
    
    class ManagerTable extends Entity\DataManager
    {
        
        public static function getFilePath()
        {
            return __FILE__;
        }
        
        public static function getTableName()
        {
            return 'bxmaker_smsnotice_list';
        }
        
        public static function getMap()
        {
            return array(
                new Entity\IntegerField('ID', array(
                    'primary' => true,
                    'autocomplete' => true
                )),
                new Entity\StringField('PHONE', array(
                    'required' => true
                )),
                new Entity\TextField('TEXT', array(
                    'required' => true
                )),
                new Entity\TextField('COMMENT'),
                //  напрмиер режим отладки, содержимое ошибки
                new Entity\TextField('SENDER', array(
                    'default' => ''
                )),
                new Entity\IntegerField('STATUS', array(
                    'required' => true,
                    'validator' => function () {
                        return array(
                            new Entity\Validator\Range(0, 99)
                        );
                    }
                )),
                new Entity\DatetimeField('CREATED', array(
                    'required' => true
                )),
                new Entity\IntegerField('TYPE_ID'),
                new Entity\StringField('EVENT'),
                
                new Entity\IntegerField('SERVICE_ID', array(
                    'required' => true
                )),
                new Entity\StringField('SITE_ID', array(
                    'required' => true,
                    'validator' => function () {
                        return array(
                            new Entity\Validator\Range(2, 2)
                        );
                    }
                )),
                new Entity\TextField('PARAMS',
                    array( // параметры для конкретных сервисов, чтобы хранить ккие то данные например для mainsms.ru - messageId
                        'required' => false,
                        'save_data_modification' => function () {
                            return array(
                                function ($value) {
                                    return serialize($value);
                                }
                            );
                        },
                        'fetch_data_modification' => function () {
                            return array(
                                function ($value) {
                                    return unserialize($value);
                                }
                            );
                        }
                    )),
                new Entity\ReferenceField('TYPE', '\Bxmaker\SmsNotice\Template\Type', array('=ref.ID' => 'this.TYPE_ID'),
                    array('type_join' => 'left')),
                new Entity\ExpressionField('CNT', 'COUNT(ID)')
            );
        }
        
        /**
         * Удаление всех записей
         */
        public static function deleteAll()
        {
            \Bitrix\Main\Application::getConnection()->query('DELETE FROM ' . self::getTableName());
        }
    }
    
    final class Manager extends \Bxmaker_SmsNotice_Manager_Demo
    {
        
        static private $instance = null;
        
        protected $module_id = 'bxmaker.smsnotice';
        protected $bDebug = false; // режим отладки
        protected $bWaitSending = false; // откладывать отправку смс
        protected $bRepirePhone = false; // исправлять номер
        protected $bLogError = false; // запись в лог ошибки
        protected $bEmailToPhone = false; // восстанавливать номер телефона из email
        protected $arSmsTemplate = array(); // массивы шаблонов, чтобы не запрашивать повторно
        protected $arSmsTemplateEmail = array(); // массивы шаблонов автоматически отправляемых при почтовом событии
        
        protected $arServices = array(); // массив проинициалиированных объектов сервисов
        protected $arServiceCurrent = array(); // данные по текущему подклчюенному сревису для отправки
        
        protected $siteID = null;
        protected $arSiteData = array(); // данные по текущему сайту
        protected $arSMSId = array(); // очередь для отправки смс в текущей
        
        
        protected $oOption = null; //параметры
        protected $oLog = null; //параметры
        protected $oBase = null; //базовый класс
        /**
         * @var \CUser
         */
        protected $oOldUser = null;
        /**
         * @var Service
         */
        protected $oManagerTable = null; // объект для работы с таблицей, в окторой хранится история отправленных смс
        protected $oTemplate = null; // объект для работы с шаблонами
        protected $bDemoExpired = false;
        
        protected $arEmail2Phone = array(); // сохраняем расчетные данные
        
        
        private function __construct()
        {
            $this->oManagerTable = new ManagerTable();
            $this->oOption = new \Bitrix\Main\Config\Option();
            $this->oOption = new \Bitrix\Main\Config\Option();
            
            // отладка
            $this->bDebug = ($this->getParam('HANDLER.DEBUG', 'N') == 'Y');
            //ожидать ли отложенную отправку пакетами
            $this->bWaitSending = ($this->getParam('HANDLER.WAIT_SENDING', 'N') == 'Y');
            //исправление номера
            $this->bRepirePhone = ($this->getParam('HANDLER.REPIRE_PHONE', 'Y') == 'Y');
            //логировать ошибки
            $this->bLogError = ($this->getParam('HANDLER.LOG', 'N') == 'Y');
            // восстанвоелнеи номера телефона из email
            $this->bEmailToPhone = ($this->getParam('HANDLER.EMAIL_TO_PHONE', 'Y') == 'Y');
            
            if ($this->isLogError()) {
                $this->oLog = new \Bxmaker\SmsNotice\LogTable();
            }
            
        }
        
        private function __clone()
        {
        
        }
        
        /**
         * @return \Bxmaker\SmsNotice\Manager
         */
        static public function getInstance()
        {
            if (is_null(self::$instance)) {
                $c = __CLASS__;
                self::$instance = new $c();
            }
            return self::$instance;
        }
        
        /**
         * Вернет идентификатор текущего модуля
         *
         * @return string
         */
        public function getModuleId()
        {
            return $this->module_id;
        }
        
        /**
         * Вернет путь до текущего модуля от корня сайта
         *
         * @param bool $bReturnAbsPath -  вернуть абсалютный путь
         *
         * @return string
         */
        public function getModulePath($bReturnAbsPath = false)
        {
            return ($bReturnAbsPath ? \Bitrix\Main\Application::getDocumentRoot() : '') . getLocalPath('modules/' . $this->getModuleId());
        }
        
        
        /**
         * Вернет объект базового класса
         *
         * @return Base
         */
        public function getBase()
        {
            if (is_null($this->oBase)) {
                $this->oBase = new Base($this->getModuleId());
            }
            
            return $this->oBase;
        }
        
        /**
         * Вренет объект класса для работы с таблицей шаблонов
         *
         * @return \Bxmaker\SmsNotice\TemplateTable
         */
        public function getTemplateTable()
        {
            if (is_null($this->oTemplate)) {
                $this->oTemplate = new TemplateTable();
            }
            return $this->oTemplate;
        }
        
        /**
         * ПРоверка включенна ли отложенная отправка
         *
         * @return bool
         */
        public function isWaitSending()
        {
            return $this->bWaitSending;
        }
        
        /**
         * Если включен режим отладки
         *
         * @return bool
         */
        public function isDebug()
        {
            return $this->bDebug;
        }
        
        
        public function checkNeedRepirePhone()
        {
            return $this->bRepirePhone;
        }
        
        public function isLogError()
        {
            return $this->bLogError;
        }
        
        /**
         * Запись ошибок в лог
         *
         * @param        $data - данные для записи
         * @param string $type - тип ошибки
         *
         * @return boolean
         */
        public function log($data, $type = 'DEFAULT', $siteId = null)
        {
            if (!$this->isLogError()) {
                return false;
            }
            if (is_null($siteId)) {
                $siteId = $this->getCurrentSiteId();
            }
            
            $text = '';
            if ($data instanceof \Bxmaker\SmsNotice\Error) {
                /**
                 * @var $data \Bxmaker\SmsNotice\Error
                 */
                $text = $data->getMessage() . ' ' . var_export($data->getMore(), true);
                
                switch ($data->getCode()) {
                    case \Bxmaker\SmsNotice\SMS_STATUS_SENT:
                        {
                            $type = 'SMS_STATUS_SENT';
                            break;
                        }
                    case \Bxmaker\SmsNotice\SMS_STATUS_DELIVERED:
                        {
                            $type = 'SMS_STATUS_DELIVERED';
                            break;
                        }
                    case \Bxmaker\SmsNotice\SMS_STATUS_ERROR:
                        {
                            $type = 'SMS_STATUS_ERROR';
                            break;
                        }
                    case \Bxmaker\SmsNotice\SMS_STATUS_WAIT:
                        {
                            $type = 'SMS_STATUS_WAIT';
                            break;
                        }
                    case \Bxmaker\SmsNotice\ERROR_SERVICE_INITIALIZATION:
                        {
                            $type = 'ERROR_SERVICE_INITIALIZATION';
                            break;
                        }
                    case \Bxmaker\SmsNotice\ERROR_SERVICE_RESPONSE:
                        {
                            $type = 'ERROR_SERVICE_RESPONSE';
                            break;
                        }
                    case \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM:
                        {
                            $type = 'ERROR_SERVICE_CUSTOM';
                            break;
                        }
                    case \Bxmaker\SmsNotice\ERROR_TEMPLATE_NOT_FOUND:
                        {
                            $type = 'ERROR_TEMPLATE_NOT_FOUND';
                            break;
                        }
                    case \Bxmaker\SmsNotice\ERROR_INVALID_PHONE:
                        {
                            $type = 'ERROR_INVALID_PHONE';
                            break;
                        }
                    case \Bxmaker\SmsNotice\ERROR_EVENT:
                        {
                            $type = 'ERROR_EVENT';
                            break;
                        }
                    default:
                        {
                            $type = $data->getCode();
                            break;
                        }
                }
            } elseif (is_string($data) || is_numeric($data)) {
                $text = $data;
            }
            
            $this->oLog->add(array(
                'TEXT' => $text,
                'TYPE' => $type,
                'SITE_ID' => $siteId
            ));
            
        }
        
        public function isEnableEmailToPhone()
        {
            return $this->bEmailToPhone;
        }
        
        public function getPhoneByUserEmail($email)
        {
            $email = strtolower($email);
            
            if (check_email($email)) {
                if (!isset($this->arEmail2Phone[$email])) {
                    $this->arEmail2Phone[$email] = '';
                    
                    $phoneField = $this->getParam('HANDLER.USER_PHONE_FIELD', '');
                    
                    if ($phoneField != '') {
                        $dbrUser = \CUser::GetList($by = '', $order = '', array(
                            'EMAIL' => $email
                        ), array(
                            'SELECT' => array($phoneField)
                        ));
                        while ($arUser = $dbrUser->Fetch()) {
                            if (strlen(trim($arUser[$phoneField])) > 0 && $this->isValidPhone($arUser[$phoneField])) {
                                $this->arEmail2Phone[$email] = $arUser[$phoneField];
                                break;
                            }
                        }
                    }
                }
                
                return $this->arEmail2Phone[$email];
            }
            
            return '';
        }
        
        
        /*  Добавление скриптов и стилей на страницы модуля */
        public function addAdminPageCssJs()
        {
            \CUtil::InitJSCore(array('jquery'));
            \Bitrix\Main\UI\Extension::load("ui.vue");
            
            $messages = \Bitrix\Main\Localization\Loc::loadLanguageFile($this->getModulePath(true) . '/admin.js.php');
            
            //стили
            echo '<style type="text/css" data-module="' . $this->getModuleId() . '" >' . file_get_contents($this->getModulePath(true) . '/dest/app.css') . '</style>';
            
            //языковые сообщения
            echo '<script type="text/javascript" data-module="' . $this->getModuleId() . '" >BX.message(' . \Bitrix\Main\Web\Json::encode($messages) . ');</script>';
            
            // js
            echo '<script type="text/javascript" data-module="' . $this->getModuleId() . '" >' . file_get_contents($this->getModulePath(true) . '/dest/app.js') . '</script>';
            
            
        }
        
        
        /**
         * @return ManagerTable
         */
        public function getTable()
        {
            return $this->oManagerTable;
        }
        
        /**
         * Возвращает параметры используемого сервиса для отправки
         *
         * @param null $serviceId
         *
         * @return array|null
         */
        public function getServiceParam($serviceId = null, $site_id = null)
        {
            if (is_null($site_id)) {
                $site_id = $this->getCurrentSiteId();
            }
            
            $resInitService = $this->initService($serviceId, $site_id);
            
            if (is_null($serviceId)) {
                return $this->arServiceCurrent;
            } else {
                return (isset($this->arServices[$site_id][$serviceId]) ? $this->arServices[$site_id][$serviceId] : null);
            }
        }
        
        /**
         * Возвращает объект текущего сервиса для отправки смс
         *
         * @param null $siteId
         *
         * @return mixed
         * @throws \Bitrix\Main\ArgumentException
         */
        public function getService($siteId = null, $serviceId = null)
        {
            if (is_null($siteId)) {
                $siteId = $this->getCurrentSiteId();
            }
            
            if (empty($serviceId)) {
                $serviceId = 0;
            }
            
            if (!isset($this->arServices[$siteId][$serviceId])) {
                $this->initService($serviceId, $siteId);
            }
            
            if ($this->arServices[$siteId][$serviceId]) {
                return $this->arServices[$siteId][$serviceId]['OBJ'];
            } else {
                return null;
            }
        }
        
        
        /**
         * Инициализация конкретного сервиса
         *
         * @param null|true|int $serviceId ( true - инициализация активного сервиса, int - конкретного сервиса, null - использование последнего )
         * @param null          $site_id   идентификатор сайта
         *
         * @return Result
         * @throws \Bitrix\Main\ArgumentException
         */
        public function initService($serviceId = null, $site_id = null)
        {
            if (is_null($site_id)) {
                $site_id = $this->getCurrentSiteId();
            }
            
            if (is_null($serviceId)) // подключение активногос сервиса
            {
                $serviceId = 0;
            }
            
            $result = new Result();
            
            if ($this->bDemoExpired) {
                $result->setError(new Error($this->getMsg('MANAGER.MODULE_DEMO_EXPIRED'), \Bxmaker\SmsNotice\ERROR_SERVICE_INITIALIZATION));
                return $result;
            }
            
            // если сервис был ранее проинициализирвоан
            if (isset($this->arServices[$site_id][$serviceId])) {
                
                if ($this->arServices[$site_id][$serviceId] === false) {
                    $result->setError(new Error($this->getMsg('MANAGER.ERROR_SERVICE_INITIALIZATION'),
                        \Bxmaker\SmsNotice\ERROR_SERVICE_INITIALIZATION, array(
                            'SERVICE_ID' => $serviceId,
                            'SITE_ID' => $site_id
                        )));
                }
                $this->arServiceCurrent = $this->arServices[$site_id][$serviceId]['DATA'];
                
                return $result;
            } else {
                // инициализируем
                $arFilter = array(
                    'SITE_ID' => $site_id
                );
                if (!$serviceId) // подключение активногос сервиса
                {
                    $arFilter['ACTIVE'] = true;
                } else {
                    $arFilter['ID'] = intval($serviceId);
                }
                
                
                $serviceTable = new \Bxmaker\SmsNotice\ServiceTable();
                
                $dbr = $serviceTable->getList(array(
                    'filter' => $arFilter,
                    'limit' => 1
                ));
                if ($arServiceParams = $dbr->fetch()) {
                    
                    // парамтеры получены
                    // подключаем файл
                    $oService = new Service();
                    $arService = $oService->getArray();
                    if (isset($arService[$arServiceParams['CODE']]) && file_exists($arService[$arServiceParams['CODE']]['FILE'])) {
                        
                        //подключение класса сервиса
                        include_once $arService[$arServiceParams['CODE']]['FILE'];
                        
                        $class_name = '\Bxmaker\SmsNotice\Service\\' . $arServiceParams['CODE'];
                        if (class_exists($class_name)) {
                            
                            $this->arServiceCurrent = $arServiceParams;
                            
                            // сохраняем временно
                            $this->arServices[$arServiceParams['SITE_ID']][$serviceId]['DATA'] = $this->arServiceCurrent;
                            $this->arServices[$arServiceParams['SITE_ID']][$serviceId]['OBJ'] = new $class_name($arServiceParams['PARAMS']);
                            
                            
                            return $result;
                        }
                    }
                }
                
            }
            
            //сброс
            $this->arServices[$site_id][$serviceId] = false;
            $this->arServiceCurrent = array();
            
            $result->setError(new Error($this->getMsg('MANAGER.ERROR_SERVICE_INITIALIZATION'), \Bxmaker\SmsNotice\ERROR_SERVICE_INITIALIZATION));
            return $result;
        }
        
        
        /**
         * Отправка одиночного смс сообщения
         *
         * @param      $phone
         * @param      $text
         * @param null $site_id
         * @param null $sender
         * @param bool $bTranslit
         * @param null $templateId
         *
         * @return Result
         *
         * @throws \Bitrix\Main\ArgumentException
         */
        public function send($phone, $text, $site_id = null, $sender = null, $bTranslit = false, $templateId = null)
        {
            // подклчюение сервиса активного
            $resInitService = $this->initService(null, $site_id);
            if (!$resInitService->isSuccess()) {
                return $resInitService;
            }
            
            if (is_null($site_id)) {
                $site_id = $this->getCurrentSiteId();
            }
            
            $arFields = array(
                'phone' => $phone,
                'text' => $text,
                'site_id' => $site_id,
                'sender' => $sender,
                'bTranslit' => $bTranslit,
                'templateId' => $templateId
            );
            
            $event = new \Bitrix\Main\Event($this->getModuleId(), "OnBeforeSend", array($arFields));
            $event->send();
            
            foreach ($event->getResults() as $eventResult) {
                $arParameters = $eventResult->getParameters();
                
                
                if (isset($arParameters[0]['phone'])) {
                    $phone = $arParameters[0]['phone'];
                }
                if (isset($arParameters[0]['text'])) {
                    $text = $arParameters[0]['text'];
                }
                if (isset($arParameters[0]['sender'])) {
                    $sender = $arParameters[0]['sender'];
                }
                if (isset($arParameters[0]['bTranslit'])) {
                    $bTranslit = $arParameters[0]['bTranslit'];
                }
                
                switch ($eventResult->getType()) {
                    case \Bitrix\Main\EventResult::ERROR:
                        {
                            
                            $msg = (isset($arParameters['error_msg']) ? $arParameters['error_msg'] : $this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_ERROR_EVENTRESULT'));
                            return new Result(new Error($msg, \Bxmaker\SmsNotice\ERROR_EVENT, array(
                                'METHOD' => 'send',
                                'PARAMS' => $arParameters,
                            )));
                            
                            break;
                        }
                    case \Bitrix\Main\EventResult::SUCCESS:
                        {
                            // успешно
                            break;
                        }
                    case \Bitrix\Main\EventResult::UNDEFINED:
                        {
                            /* обработчик вернул неизвестно что вместо объекта класса \Bitrix\Main\EventResult
                           его результат по прежнему доступен через getParameters
                           */
                            break;
                        }
                }
            }
            
            
            return $this->sendSms($phone, $text, $site_id, $templateId, $sender, null, $bTranslit);
        }
        
        
        /**
         * Отправка смс по шаблону
         *
         * @param       $templateCode
         * @param array $arFields
         * @param null  $site_id
         * @param null  $sender
         *
         * @return \Bxmaker\SmsNotice\Result
         * @throws \Bitrix\Main\ArgumentException
         * @throws \Bitrix\Main\ObjectPropertyException
         * @throws \Bitrix\Main\SystemException
         */
        public function sendTemplate($templateCode, $arFields = array(), $site_id = null, $sender = null)
        {
            
            // подклчюение сервиса активного
            $resInitService = $this->initService(null, $site_id);
            if (!$resInitService->isSuccess()) {
                return $resInitService;
            }
            
            if (is_null($site_id)) {
                $site_id = $this->getCurrentSiteId();
            }
            
            // получаем шаблоны если не сделали эо ранее
            if (!isset($this->arSmsTemplate[$site_id][$templateCode])) {
                
                $dbrTemplate = $this->getTemplateTable()->getList(array(
                    'filter' => array(
                        'ACTIVE' => true,
                        'TYPE.CODE' => $templateCode,
                        'SITE.SID' => $site_id
                    )
                ));
                while ($arTemplate = $dbrTemplate->fetch()) {
                    $this->arSmsTemplate[$site_id][$templateCode][] = $arTemplate;
                }
            }
            
            //раширяем поля
            $this->extendTemplateFieldsForConditions($arFields, $templateCode, $site_id);
            
            
            $event = new \Bitrix\Main\Event($this->getModuleId(), "OnBeforeSendTemplate", array(
                $templateCode,
                $arFields
            ));
            $event->send();
            
            foreach ($event->getResults() as $eventResult) {
                $arParameters = $eventResult->getParameters();
                
                if (!isset($arParameters[1])) {
                    new Result(new Error($this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_EMPTY_PARAMS'), \Bxmaker\SmsNotice\ERROR_EVENT, array(
                        'METHOD' => 'sendTemplate',
                        'PARAMS' => $arParameters
                    )));
                }
                
                $arFields = $arParameters[1];
                
                switch ($eventResult->getType()) {
                    case \Bitrix\Main\EventResult::ERROR:
                        {
                            
                            $msg = (isset($arParameters['error_msg']) ? $arParameters['error_msg'] : $this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_ERROR_EVENTRESULT'));
                            return new Result(new Error($msg, \Bxmaker\SmsNotice\ERROR_EVENT, array(
                                'METHOD' => 'sendTemplate'
                            )));
                            
                            break;
                        }
                    case \Bitrix\Main\EventResult::SUCCESS:
                        {
                            // успешно
                            break;
                        }
                    case \Bitrix\Main\EventResult::UNDEFINED:
                        {
                            /* обработчик вернул неизвестно что вместо объекта класса \Bitrix\Main\EventResult
                           его результат по прежнему доступен через getParameters
                           */
                            break;
                        }
                }
            }
            
            // обходим шаблоны и отправляем
            $arSentResult = array(
                'count' => 0,
                'errors' => null,
                'results' => array()
            );
            
            foreach ($this->arSmsTemplate[$site_id][$templateCode] as $arTemplate) {
                
                if (!$this->checkCondition($arTemplate, array_merge(array('SITE_ID' => $site_id), $arFields))) {
                    
                    $this->log($this->getMsg('MANAGER.CHECK_CONDITION_FAIL') . ' ' . var_export(array(
                            'METHOD' => 'sendTemplate',
                            'TYPE_ID' => $arTemplate['TYPE_ID'],
                            'TEMPLATE' => $templateCode,
                            'FIELDS' => $arFields,
                            'SITE_ID' => $site_id,
                            'SENDER' => $sender
                        ), true), 'CHECK_CONDITION_FAIL', $site_id);
                    
                    continue;
                }
                
                $arSentResult['count']++;
                
                $this->prepareTemplate($arTemplate, $arFields, $site_id);
                
                $res = $this->sendSms($arTemplate['PHONE'], $arTemplate['TEXT'], $site_id, $arTemplate['TYPE_ID'], $sender, null,
                    !!$arTemplate['TRANSLIT']);
                
                //копия
                if (!is_null($arTemplate['PHONE_COPY']) && $arPhoneCopy = explode(',', $arTemplate['PHONE_COPY'])) {
                    foreach ($arPhoneCopy as $phoneCopy) {
                        if ($this->isValidPhone($phoneCopy)) {
                            $this->sendSms($this->getPreparePhone($phoneCopy), $arTemplate['TEXT'], $site_id, $arTemplate['TYPE_ID'], $sender, null,
                                !!$arTemplate['TRANSLIT']);
                        }
                    }
                }
                
                
                if ($res->isSuccess()) {
                    $arSentResult['results'][] = $res;
                } else {
                    $arSentResult['errors'] = (array)$arSentResult['errors'];
                    $errors = $res->getErrors();
                    if (isset($errors[0])) {
                        $arSentResult['errors'][] = $errors[0];
                    }
                }
            }
            
            $result = new Result();
            $result->setMore('count', $arSentResult['count']);
            $result->setMore('errors', $arSentResult['errors']);
            $result->setMore('results', $arSentResult['results']);
            
            return $result;
        }
        
        
        public function sendTemplateEmail($template, $arFields = array(), $site_id = null, $sender = null)
        {
            
            if (is_null($site_id)) {
                $site_id = $this->getCurrentSiteId();
            }
            
            // получаем шаблоны если не сделали эо ранее
            if (!isset($this->arSmsTemplateEmail[$site_id][$template])) {
                
                
                $dbrTemplate = $this->getTemplateTable()->getList(array(
                    'filter' => array(
                        'ACTIVE' => true,
                        'TYPE_ID' => 0,
                        'SITE.SID' => $site_id,
                        'EVENT' => $template
                    )
                ));
                while ($arTemplate = $dbrTemplate->fetch()) {
                    $this->arSmsTemplateEmail[$site_id][$template][] = $arTemplate;
                }
            }
            
            $event = new \Bitrix\Main\Event($this->getModuleId(), "OnBeforeSendTemplateEmail", array(
                $template,
                $arFields
            ));
            $event->send();
            
            foreach ($event->getResults() as $eventResult) {
                $arParameters = $eventResult->getParameters();
                
                if (!isset($arParameters[1])) {
                    new Result(new Error($this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_EMAIL_EMPTY_PARAMS'), \Bxmaker\SmsNotice\ERROR_EVENT,
                        array(
                            'METHOD' => 'sendTemplateEmail',
                            'PARAMS' => $arParameters
                        )));
                }
                
                $arFields = $arParameters[1];
                
                switch ($eventResult->getType()) {
                    case \Bitrix\Main\EventResult::ERROR:
                        {
                            
                            $msg = (isset($arParameters['error_msg']) ? $arParameters['error_msg'] : $this->getMsg('MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_EMAIL_ERROR_EVENTRESULT'));
                            return new Result(new Error($msg, \Bxmaker\SmsNotice\ERROR_EVENT, array(
                                'METHOD' => 'sendTemplateEmail',
                            )));
                            
                            break;
                        }
                    case \Bitrix\Main\EventResult::SUCCESS:
                        {
                            // успешно
                            break;
                        }
                    case \Bitrix\Main\EventResult::UNDEFINED:
                        {
                            /* обработчик вернул неизвестно что вместо объекта класса \Bitrix\Main\EventResult
                           его результат по прежнему доступен через getParameters
                           */
                            break;
                        }
                }
            }
            
            
            // обходим шаблоны и отправляем
            $arSentResult = array(
                'count' => 0,
                'errors' => null,
                'results' => array()
            );
            
            
            //если нету номера телефона, то берем из полей пользователя
            if (!isset($arFields['PHONE']) && isset($arFields['EMAIL'])) {
                //ищем
                if ($this->isEnableEmailToPhone()) {
                    $arFields['PHONE'] = $this->getPhoneByUserEmail($arFields['EMAIL']);
                } else {
                    $this->log($this->getMsg('MANAGER.EMAIL_PHONE_IS_NOT_DEFINED') . ' ' . var_export(array(
                            'METHOD' => 'sendTemplateEmail',
                            'TEMPLATE' => $template,
                            'FIELDS' => $arFields,
                            'SITE_ID' => $site_id,
                            'SENDER' => $sender
                        ), true), 'ERROR_INVALID_PHONE_FOR_EMAIL_EVENT', $site_id);
                    
                    $result = new Result();
                    $result->setMore('count', 0);
                    $result->setMore('errors', array(
                        new Error($this->getMsg('MANAGER.EMAIL_PHONE_IS_NOT_DEFINED'), \BXmaker\SmsNotice\ERROR_INVALID_PHONE)
                    ));
                    $result->setMore('results', array());
                    
                    return $result;
                }
            }
            
            if (is_array($this->arSmsTemplateEmail[$site_id][$template])) {
                foreach ($this->arSmsTemplateEmail[$site_id][$template] as $arTemplate) {
                    
                    if (!$this->checkCondition($arTemplate, array_merge(array('SITE_ID' => $site_id), $arFields))) {
                        
                        $this->log($this->getMsg('MANAGER.CHECK_CONDITION_FAIL') . ' ' . var_export(array(
                                'METHOD' => 'sendTemplateEmail',
                                'TYPE_ID' => $arTemplate['TYPE_ID'],
                                'TEMPLATE' => $template,
                                'FIELDS' => $arFields,
                                'SITE_ID' => $site_id,
                                'SENDER' => $sender
                            ), true), 'CHECK_CONDITION_FAIL', $site_id);
                        continue;
                    }
                    
                    $arSentResult['count']++;
                    
                    $this->prepareTemplate($arTemplate, $arFields, $site_id);
                    
                    $res = $this->sendSms($arTemplate['PHONE'], $arTemplate['TEXT'], $site_id, null, $sender, $template, !!$arTemplate['TRANSLIT']);
                    
                    
                    //копия
                    if (!is_null($arTemplate['PHONE_COPY']) && $arPhoneCopy = explode(',', $arTemplate['PHONE_COPY'])) {
                        foreach ($arPhoneCopy as $phoneCopy) {
                            if ($this->isValidPhone($phoneCopy)) {
                                $this->sendSms($this->getPreparePhone($phoneCopy), $arTemplate['TEXT'], $site_id, null, $sender, $template,
                                    !!$arTemplate['TRANSLIT']);
                            }
                        }
                    }
                    
                    if ($res->isSuccess()) {
                        $arSentResult['results'][] = $res;
                    } else {
                        $arSentResult['errors'] = (array)$arSentResult['errors'];
                        $errors = $res->getErrors();
                        if (isset($errors[0])) {
                            $arSentResult['errors'][] = $errors[0];
                        }
                    }
                }
            }
            
            $result = new Result();
            $result->setMore('count', $arSentResult['count']);
            $result->setMore('errors', $arSentResult['errors']);
            $result->setMore('results', $arSentResult['results']);
            
            return $result;
        }
        
        
        /**
         * ДОбавляет в поля сообщений необходимые для фильтрации дополнительные поля, такие как url страницы и тп
         *
         * @param      $arFields
         * @param null $template
         * @param null $siteId
         *
         * @throws \Bitrix\Main\SystemException
         */
        public function extendTemplateFieldsForConditions(&$arFields, $template = null, $siteId = null)
        {
            $context = \Bitrix\Main\Application::getInstance()->getContext();
            $server = \Bitrix\Main\Application::getInstance()->getContext()->getServer();
            
            
            $arFields['CURRENT_PAGE_URL'] = '';
            $arFields['CURRENT_PAGE_URL_FULL'] = '';
            
            if ($server->get('REQUEST_SCHEME')) {
                $arFields['CURRENT_PAGE_URL_FULL'] = $server->get('REQUEST_SCHEME') . '://';
            }
            if ($server->get('SERVER_NAME')) {
                $arFields['CURRENT_PAGE_URL_FULL'] = $server->get('SERVER_NAME');
            }
            
            if ($context->getRequest()->isAjaxRequest() && $context->getServer()->get("HTTP_REFERER")) {
                $uri = new \Bitrix\Main\Web\Uri($context->getServer()->get("HTTP_REFERER"));
                $arFields['CURRENT_PAGE_URL'] = \Bitrix\Main\Text\BinaryString::changeCaseToLower($uri->getPathQuery());
                $arFields['CURRENT_PAGE_URL_FULL'] = \Bitrix\Main\Text\BinaryString::changeCaseToLower($arFields['CURRENT_PAGE_URL_FULL'] . $uri->getPathQuery());
                
            } elseif (!$context->getRequest()->isAjaxRequest()) {
                $arFields['CURRENT_PAGE_URL'] = \Bitrix\Main\Text\BinaryString::changeCaseToLower($context->getRequest()->getRequestUri());
                $arFields['CURRENT_PAGE_URL_FULL'] = \Bitrix\Main\Text\BinaryString::changeCaseToLower($arFields['CURRENT_PAGE_URL_FULL'] . $context->getRequest()->getRequestUri());
            }
        }
        
        
        public function checkCondition($arTemplate, $arFields)
        {
            $oCondition = new Condition();
            
            $result = $oCondition->checkItemConditions($arTemplate['PARAMS']['CONDITIONS'], $arFields);
            return $result;
        }
        
        public function getBalance($site_id = null, $serviceId = null)
        {
            // подклчюение сервиса активного
            $resInitService = $this->initService($serviceId, $site_id);
            if (!$resInitService->isSuccess()) {
                return $resInitService;
            }
            return $this->getService($site_id, $serviceId)->getBalance();
        }
        
        
        static public function isWin()
        {
            return !self::isUTF();
        }
        
        /**
         * Проверка, кодировка сайта UTF-8 или нет
         *
         * @return bool
         */
        final static public function isUTF()
        {
            return (defined('BX_UTF') && BX_UTF === true);
        }
        
        final static public function isAdminSection()
        {
            return (defined('ADMIN_SECTION') && defined('ADMIN_SECTION') === true);
        }
        
        final function restoreEncoding($data)
        {
            if (self::isUTF()) {
                return $data;
            }
            return \Bitrix\Main\Text\Encoding::convertEncoding($data, 'UTF-8', 'WINDOWS-1251');
        }
        
        final function prepareEncoding($data)
        {
            if (self::isUTF()) {
                return $data;
            }
            return \Bitrix\Main\Text\Encoding::convertEncoding($data, 'WINDOWS-1251', 'UTF-8');
        }
        
        
        /**
         * Возвращает текущий идентификатор сайта
         *
         * @return string
         */
        public function getCurrentSiteId()
        {
            if (!$this->siteID) {
                $this->defineSiteId();
            }
            
            return $this->siteID;
        }
        
        public function defineSiteId()
        {
            // если админка то определяем сайт поумолчанию или  по текущему домену
            if ($this->isAdminSection() || SITE_ID == LANGUAGE_ID) {
                
                $host = Application::getInstance()->getContext()->getRequest()->getHttpHost();
                $host = preg_replace('/(:[\d]+)/', '', $host);
                
                $by = 'sort';
                $order = 'asc';
                
                //ищем по домену
                $oSite = new \CSite();
                $dbr = $oSite->GetList($by, $order, array(
                    'ACTIVE' => 'Y',
                    'DOMAIN' => $host
                ));
                if ($ar = $dbr->Fetch()) {
                    $this->siteID = $ar['LID'];
                } else {
                    // сайт поумолчанию
                    $dbr = $oSite->GetList($by, $order, array(
                        'DEFAULT' => 'Y'
                    ));
                    if ($ar = $dbr->Fetch()) {
                        $this->siteID = $ar['LID'];
                    } else {
                        $dbr = $oSite->GetList($by, $order, array());
                        if ($ar = $dbr->Fetch()) {
                            $this->siteID = $ar['LID'];
                        }
                    }
                }
            } else {
                $this->siteID = SITE_ID;
            }
        }
        
        
        /**
         * Возвращает парамтеры сайта, для подставновки этих значений в шаблоны сообщений - SERVER_NAME напримре
         *
         * @param null $site_id
         *
         * @return mixed
         */
        public function getSiteData($site_id = null)
        {
            if (is_null($site_id)) {
                $site_id = $this->getCurrentSiteId();
            }
            if (!isset($this->arSiteData[$site_id])) {
                $oSite = new \CSite();
                $dbr = $oSite->GetByID($site_id);
                if ($ar = $dbr->Fetch()) {
                    $this->arSiteData[$site_id] = $ar;
                } else {
                    $this->arSiteData[$site_id] = false;
                }
            }
            return $this->arSiteData[$site_id];
        }
        
        /**
         * Разбирает список телефонов
         *
         * @param $phone
         *
         * @return array|bool
         */
        public function getMultyPhone($phone)
        {
            $phone = preg_replace("/\x09/", '', $phone); //пробелы убирает
            $phone = preg_replace("/\n/", ',', $phone); //переносы меняет на запятые
            $arPhone = explode(',', $phone); // разбивает на телеофны
            
            $arPhone = array_diff($arPhone, array('')); // убирает пустые значения
            
            if (count($arPhone)) {
                return $arPhone;
            } else {
                return false;
            }
        }
        
        /**
         * Возвращает валидные номера телефонов из списка
         *
         * @param $phone
         *
         * @return array|bool
         */
        public function getPreparedMultyPhone($phone)
        {
            
            $arPhoneTmp = $this->getMultyPhone($phone);
            $arPhone = array();
            
            foreach ($arPhoneTmp as $phone) {
                if ($this->isValidPhone($phone)) {
                    $arPhone[] = $this->getPreparePhone($phone);
                }
            }
            
            if (count($arPhone)) {
                return $arPhone;
            } else {
                return false;
            }
        }
        
        
        /**
         * Используется для смены статуса извне системы,  некоторые сервисы сами дополнительно оповещают ресурсы о статусе доставки смс
         *
         * @return Result
         * @throws \Bitrix\Main\ArgumentException
         */
        public function notice($siteId = null)
        {
            
            $req = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            $oManagerTable = new ManagerTable();
            $result = new Result();
            
            if (is_null($siteId)) {
                $siteId = $this->getCurrentSiteId();
            }
            
            $dbr = $oManagerTable->getList(array(
                'filter' => array(
                    'ID' => (int)$req->getQuery('smsId')
                )
            ));
            if ($ar = $dbr->fetch()) {
                
                // подклчюение сервиса активного
                $resInitService = $this->initService($ar['SERVICE_ID'], $siteId);
                if (!$resInitService->isSuccess()) {
                    return $resInitService;
                }
                
                
                // получаем результат понятный для работы
                /**
                 * @var Result
                 */
                $resNoticeCheck = $this->getService($siteId, $ar['SERVICE_ID'])->notice();
                
                // елси все успешно
                if ($resNoticeCheck->isSuccess()) {
                    
                    switch ($resNoticeCheck->getResult()) {
                        case \Bxmaker\SmsNotice\SMS_STATUS_DELIVERED:
                            {
                                // обновляем статус у сообщения
                                $oManagerTable->update($ar['ID'], array(
                                    'STATUS' => \Bxmaker\SmsNotice\SMS_STATUS_DELIVERED,
                                    'COMMENT' => ''
                                ));
                                break;
                            }
                        case \Bxmaker\SmsNotice\SMS_STATUS_SENT:
                            {
                                // обновляем статус у сообщения
                                $oManagerTable->update($ar['ID'], array(
                                    'STATUS' => \Bxmaker\SmsNotice\SMS_STATUS_SENT,
                                    'COMMENT' => ''
                                ));
                            }
                    }
                    $result->setResult(true);
                    
                } else {
                    
                    
                    foreach ($resNoticeCheck->getErrors() as $error) {
                        $result->setError($error);
                    }
                    
                    $oManagerTable->update($ar['ID'], array(
                        'STATUS' => \Bxmaker\SmsNotice\SMS_STATUS_ERROR,
                        'COMMENT' => $this->getCommentFromErrors($resNoticeCheck->getErrors())
                    ));
                }
                
            }
            
            return $result;
        }
        
        
        /**
         * Получение значения одного из параметров модуля
         *
         * @param string      $name
         * @param mixed       $default_value
         * @param null|string $siteId - адрес сайта паарметры которого необходимо получить, по умолчанию текущий
         *
         * @return string
         * @throws \Bitrix\Main\ArgumentNullException
         */
        public function getParam($name, $default_value = null, $siteId = null)
        {
            if (is_null($siteId)) {
                $siteId = $this->getCurrentSiteId();
            }
            return $this->oOption->get($this->getModuleId(), $name, $default_value, $siteId);
        }
        
        /**
         * Возврат языкозависимое сообщение об ошибке или успешности
         *
         * @param      $name
         * @param null $arReplace
         *
         * @return mixed|string
         */
        public function getMsg($name, $arReplace = null)
        {
            return GetMessage($this->getModuleId() . '.' . $name, $arReplace);
        }
        
        
        /**
         * Веренет поле в котором хранится номер телефона пользователя
         *
         * @param null $siteId
         *
         * @return string
         * @throws \Bitrix\Main\ArgumentNullException
         */
        public function getUserPhoneField($siteId = null)
        {
            return trim($this->getParam('HANDLER.USER_PHONE_FIELD', null, $siteId));
        }
        
        /**
         * Вернет массив групп пользователя, по умолчанию текущего
         *
         * @param null $userId
         */
        public function getUserGroups($userId = null)
        {
            global $USER;
            if (is_null($userId)) {
                $userId = $USER->GetID();
            }
            
            return \CUser::GetUserGroup(intval($userId));
        }
        
        /**
         * Вернет идентификтаор пользователя по его номеру телефона
         *
         * @param $phone
         *
         * @return int
         */
        public function getUserIdByPhone($phone, $siteId = null)
        {
            $phone = $this->getPreparePhone($phone);
            $phoneField = $this->getUserPhoneField($siteId);
            if (strlen(trim($phoneField)) > 0 && $phone) {
                $by = 'ID';
                $order = 'DESC';
                $dbr = $this->oldUser()->GetList($by, $order, array(
                    'ACTIVE' => 'Y',
                    array(
                        'LOGIC' => 'OR',
                        array(
                            $phoneField => $phone
                        ),
                        array(
                            $phoneField => '+' . $phone
                        )
                    )
                ), array(
                    'NAV_PARAMS' => array(
                        'nPageSize' => 1
                    )
                ));
                if ($ar = $dbr->Fetch()) {
                    return $ar['ID'];
                }
            }
            
            return null;
        }
        
        /**
         * Проверяет подходит ли номер телефона под стоп лист групп пользвоателей
         *
         * @param $phone
         *
         * @return bool
         */
        public function isNeedStopSendSmsByUserPhone($phone, $siteId = null)
        {
            // проверяем по номеру телефона
            $userIdByPhone = $this->getUserIdByPhone($phone, $siteId);
            if (!is_null($userIdByPhone) && !array_intersect(explode(',', $this->getParam('HANDLER.SKIP_GROUP', '')),
                    $this->getUserGroups($userIdByPhone))) {
                return false;
            }
            
            // проверяем по текущему пользователю
            if (array_intersect(explode(',', $this->getParam('HANDLER.SKIP_GROUP', '')), $this->getUserGroups(null))) {
                return true;
            }
            return false;
        }
        
        /**
         * @return \CUser
         */
        public function oldUser()
        {
            if (is_null($this->oOldUser)) {
                $this->oOldUser = new \CUser();
            }
            return $this->oOldUser;
        }
        
    }
