<?
    
    use Bitrix\Main\Localization\Loc as Loc;
    
    Loc::loadMessages(__FILE__);
    
    
    class bxmaker_smsnotice extends CModule
    {
        
        var $MODULE_ID = "bxmaker.smsnotice";
        var $PARTNER_NAME = "BXmaker";
        var $PARTNER_URI = "http://bxmaker.ru/";
        
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $PARTNER_ID = "bxmaker";
        
        /**
         * Массив зависимостей, для обработки событий других модулей
         *
         * @var array
         */
        private $arModuleDependences = array(
            
            array('main', 'OnBuildGlobalMenu', '\Bxmaker\SmsNotice\Handler', 'main_onBuildGlobalMenu'),
            
            // Пользователи
            //регистрация
            array('main', 'OnBeforeUserAdd', '\Bxmaker\SmsNotice\Handler', 'main_OnBeforeUserAdd', 100000),
            array('main', 'OnAfterUserAdd', '\Bxmaker\SmsNotice\Handler', 'main_OnAfterUserAdd', 100000),
            
            //изменение пароля
            array('main', 'OnBeforeUserUpdate', '\Bxmaker\SmsNotice\Handler', 'main_OnBeforeUserUpdate', 100000),
            array('main', 'OnAfterUserUpdate', '\Bxmaker\SmsNotice\Handler', 'main_OnAfterUserUpdate', 100000),
            
            // заказы
            array('main', 'OnBeforeEventAdd', '\Bxmaker\SmsNotice\Handler', 'main_OnBeforeEventAdd', 100000),
            
            //D7
            array('sale', 'OnShipmentTrackingNumberChange', '\Bxmaker\SmsNotice\Handler', 'sale_OnShipmentTrackingNumberChange', 100000),
            array('sale', 'OnSaleStatusOrderChange', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleStatusOrderChange', 100000),
            array('sale', 'OnSaleOrderCanceled', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleOrderCanceled', 100000),
            array('sale', 'OnSaleOrderPaid', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleOrderPaid', 100000),
            array('sale', 'OnSaleOrderSaved', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleOrderSaved', 100000),
            array('sale', 'OnSaleStatusShipmentChange', '\BXmaker\SmsNotice\Handler', 'sale_OnSaleStatusShipmentChange', 100000),
            
            //old
            array('sale', 'OnBeforeOrderAdd', '\Bxmaker\SmsNotice\Handler', 'sale_OnBeforeOrderAdd', 100000),
            array('sale', 'OnOrderAdd', '\Bxmaker\SmsNotice\Handler', 'sale_OnOrderAdd', 100000),
            array('sale', 'OnBeforeOrderUpdate', '\Bxmaker\SmsNotice\Handler', 'sale_OnBeforeOrderUpdate', 100000),
            array('sale', 'OnOrderUpdate', '\Bxmaker\SmsNotice\Handler', 'sale_OnOrderUpdate', 100000),
            array('sale', 'OnSalePayOrder', '\Bxmaker\SmsNotice\Handler', 'sale_OnSalePayOrder', 100000),
            array('sale', 'OnSaleCancelOrder', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleCancelOrder', 100000),
            array('sale', 'OnSaleStatusOrder', '\Bxmaker\SmsNotice\Handler', 'sale_OnSaleStatusOrder', 100000),
            
            //история смс на странице заказа
            array('main', 'OnAdminTabControlBegin', '\Bxmaker\SmsNotice\Handler', 'OnAdminTabControlBegin', 100000),
            
            //для модуля авторизации по номеру телефона
            array('bxmaker.authuserphone', 'onSendCode', '\Bxmaker\SmsNotice\Handler', 'bxmaker_authuserphone_onSendCode', 500),
            array('bxmaker.authuserphone', 'onUserAdd', '\Bxmaker\SmsNotice\Handler', 'bxmaker_authuserphone_onUserAdd', 500),
            array('bxmaker.authuserphone', 'onUserChangePassword', '\Bxmaker\SmsNotice\Handler', 'bxmaker_authuserphone_onUserChangePassword', 500),
            
             array('messageservice', 'onGetSmsSenders', '\Bxmaker\SmsNotice\Connector', 'onGetSmsSenders', 500),
        );
        
        
        public function __construct()
        {
            include(__DIR__ . '/version.php');
            
            $this->MODULE_DIR = \Bitrix\Main\Loader::getLocal('modules/bxmaker.smsnotice');
            
            $this->isLocal = !!strpos($this->MODULE_DIR, '/local/modules/');
            
            $this->MODULE_NAME = Loc::getMessage($this->MODULE_ID . '_MODULE_NAME');
            $this->MODULE_DESCRIPTION = Loc::getMessage($this->MODULE_ID . '_MODULE_DESCRIPTION');
            $this->PARTNER_NAME = GetMessage('bxmaker.smsnotice_PARTNER_NAME');
            $this->PARTNER_URI = GetMessage('bxmaker.smsnotice_PARTNER_URI');
            $this->MODULE_VERSION = empty($arModuleVersion['VERSION']) ? '' : $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = empty($arModuleVersion['VERSION_DATE']) ? '' : $arModuleVersion['VERSION_DATE'];
        }
        
        function DoInstall()
        {
            RegisterModule($this->MODULE_ID);
            $this->InstallDB();
            $this->InstallFiles();
            $this->InstallAgents();
            $this->InstallDependences();
            $this->InstallTemplates();
            
            return true;
        }
        
        function DoUninstall()
        {
            $this->UnInstallDB();
            $this->UnInstallFiles();
            $this->UnInstallAgents();
            $this->UnInstallDependences();
            $this->UnInstallTemplates();
            
            COption::RemoveOption($this->MODULE_ID);
            UnRegisterModule($this->MODULE_ID);
            
            return true;
        }
        
        /**
         * Добавление в базу необходимых таблиц для работы модуля
         *
         * @return bool
         */
        function InstallDB()
        {
            global $DB, $DBType, $APPLICATION;
            
            //         Database tables creation
            $DB->RunSQLBatch(dirname(__FILE__) . "/db/mysql/install.sql");
            
            return true;
        }
        
        
        /**
         * Удаление таблиц модуля
         *
         * @return bool|void
         */
        function UnInstallDB()
        {
            global $DB, $DBType, $APPLICATION;
            
            $DB->RunSQLBatch(dirname(__FILE__) . "/db/mysql/uninstall.sql");
            
            return true;
        }
        
        
        /**
         * Копирование файлов
         *
         * @return bool|void
         */
        function InstallFiles($arParams = array())
        {
            // копируем рядом
            if ($this->isLocal) {
                CopyDirFiles($this->MODULE_DIR . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/local/components/", true, true);
            } else {
                CopyDirFiles($this->MODULE_DIR . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/", true, true);
            }
            
            if (file_exists($path = $this->MODULE_DIR . '/admin')) {
                if ($dir = opendir($path)) {
                    while (false !== $item = readdir($dir)) {
                        if (in_array($item, array('.', '..', 'menu.php'))) {
                            continue;
                        }
                        
                        if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item)) {
                            file_put_contents($file,
                                '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/' . ($this->isLocal ? 'local' : 'bitrix') . '/modules/' . $this->MODULE_ID . '/admin/' . $item . '");?' . '>');
                        }
                    }
                }
            }
            
            
            if (file_exists($path = $this->MODULE_DIR . '/tools')) {
                if ($dir = opendir($path)) {
                    
                    CheckDirPath($_SERVER["DOCUMENT_ROOT"] . "/bitrix/tools/" . $this->MODULE_ID . '/');
                    
                    while (false !== $item = readdir($dir)) {
                        if (in_array($item, array('.', '..'))) {
                            continue;
                        }
                        
                        $file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . $this->MODULE_ID . '/' . $item;
                        
                        if (!file_exists($file)) {
                            file_put_contents($file, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/'
                                . ($this->isLocal ? 'local' : 'bitrix') . '/modules/' . $this->MODULE_ID . '/tools/' . $item . '");?' . '>');
                        }
                    }
                }
            }
            
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/bitrix/components/bitrix/messageservice.config.sender.sms/templates/.default/')) {
                CopyDirFiles($this->MODULE_DIR . "/install/messageservice/template/",
                    $_SERVER["DOCUMENT_ROOT"] . "bitrix/components/bitrix/messageservice.config.sender.sms/templates/.default/", true, true);
            }
            
            
            return true;
        }
        
        /**
         * Удаление файлов
         *
         * @return bool|void
         */
        function UnInstallFiles()
        {
            
            if (is_dir($this->MODULE_DIR . "/install/components/" . $this->PARTNER_ID . "/")) {
                $d = dir($this->MODULE_DIR . "/install/components/" . $this->PARTNER_ID . "/");
                while ($entry = $d->read()) {
                    if ($entry == '.' || $entry == '..') {
                        continue;
                    }
                    
                    DeleteDirFilesEx('/local/components/' . $this->PARTNER_ID . '/' . $entry . '/');
                    DeleteDirFilesEx('/local/components/' . $this->PARTNER_ID . '/' . $entry . '/');
                }
                $d->close();
            }
            
            if (file_exists($path = $this->MODULE_DIR . '/admin')) {
                if ($dir = opendir($path)) {
                    while (false !== $item = readdir($dir)) {
                        if (in_array($item, array('.', '..', 'menu.php'))) {
                            continue;
                        }
                        
                        if (file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item)) {
                            unlink($file);
                        }
                    }
                }
            }
            
            if (file_exists($path = $this->MODULE_DIR . '/tools')) {
                if ($dir = opendir($path)) {
                    while (false !== $item = readdir($dir)) {
                        if (in_array($item, array('.', '..'))) {
                            continue;
                        }
                        
                        if (file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/' . $this->MODULE_ID . '/' . $item)) {
                            unlink($file);
                        }
                    }
                }
            }
            
            
            return true;
        }
        
        /**
         * Установка агентов
         */
        public function InstallAgents()
        {
            $oAgent = new CAgent();
            $oAgent->AddAgent('\Bxmaker\SmsNotice\Agent::cleanSmsHistory();', $this->MODULE_ID, 'N', 3600);
            $oAgent->AddAgent('\Bxmaker\SmsNotice\Agent::checkSmsStatus();', $this->MODULE_ID, 'N', 50);
            $oAgent->AddAgent('\Bxmaker\SmsNotice\Agent::agentSendSmsQueue();', $this->MODULE_ID, 'N', 50);
        }
        
        /**
         * Удаление агентов
         */
        public function UnInstallAgents()
        {
            $oAgent = new CAgent();
            $oAgent->RemoveModuleAgents($this->MODULE_ID);
        }
        
        
        /**
         * Установка обработчиков событий
         */
        public function InstallDependences()
        {
            foreach ($this->arModuleDependences as $item) {
                if (count($item) < 4) {
                    continue;
                }
                
                RegisterModuleDependences($item[0], $item[1], $this->MODULE_ID, $item[2], $item[3], (isset($item[4]) ? intval($item[4]) : 100));
            }
        }
        
        /**
         * Удаление обработчиков событий
         */
        public function UnInstallDependences()
        {
            foreach ($this->arModuleDependences as $item) {
                if (count($item) < 4) {
                    continue;
                }
                
                UnRegisterModuleDependences($item[0], $item[1], $this->MODULE_ID, $item[2], $item[3]);
            }
        }
        
        
        /**
         * Добавление типов шаблонов смс и самих шаблонов
         */
        public function InstallTemplates()
        {
            \Bitrix\Main\Loader::includeModule($this->MODULE_ID);
            
            $oTemplateType = new \Bxmaker\SmsNotice\Template\TypeTable();
            $oTemplateSite = new \Bxmaker\SmsNotice\Template\SiteTable();
            $oTemplate = new \Bxmaker\SmsNotice\TemplateTable();
            
            
            $arSite = array();
            $dbr = \CSite::GetList($by = 'sort', $order = 'asc');
            while ($ar = $dbr->Fetch()) {
                $arSite[] = $ar['ID'];
            }
            
            
            $arTypes = array(
                
                // Пользователи
                'MAIN_USER_ADD' => array('main'), // регистрация пользователя
                'MAIN_USER_CHANGEPASSWORD' => array('main'), //изменение пароля
                
                // Интернет-магазин
                'ORDER_NEW' => array('main'), //новый зазказ
                'TRACKING_NUMBER' => array('main'), //идентификтаор отправления
                'ORDER_CANCELED' => array('main'), // отмена
                'ORDER_PAYED' => array('main'), // оплачен
                'ORDER_STATUS' => array('main'), // статус
                'ORDER_CHECK' => array('main'), // чек
                'ORDER_SHIPMENT_STATUS' => array('main'), // смена статуса отгрузки
            );
            
            if (IsModuleInstalled('bxmaker.authuserphone')) {
                $arTypes = array_merge($arTypes, array(
                    // Модуль bxmaker.authuserphone
                    'BXMAKER_AUTHUSERPHONE_SENDCODE' => array('main'), // main - индификатор варианта шаблона, для разных значений яыкозависымых
                    'BXMAKER_AUTHUSERPHONE_USERADD' => array('main'),
                    'BXMAKER_AUTHUSERPHONE_USERCHANGEPASSWORD' => array('main'),
                ));
            }
            
            foreach ($arTypes as $code => $arTemplates) {
                if (!is_array($arTemplates)) {
                    continue;
                }
                
                // Типы
                $res = $oTemplateType->add(array(
                    'NAME' => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE_TYPE.NAME'),
                    'CODE' => $code,
                    'DESCR' => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE_TYPE.DESCR')
                ));
                
                if (!$res->isSuccess()) {
                    continue;
                }
                $templateTypeId = $res->getId();
                
                
                // Шаблоны
                foreach ($arTemplates as $template) {
                    
                    $resTemplate = $oTemplate->add(array(
                        'TYPE_ID' => $templateTypeId,
                        'NAME' => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE.NAME.' . $template),
                        'ACTIVE' => true,
                        'PHONE' => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE.PHONE.' . $template),
                        'TEXT' => GetMessage($this->MODULE_ID . '.' . $code . '.TEMPLATE.TEXT.' . $template),
                    ));
                    
                    //добавляем
                    foreach ($arSite as $sid) {
                        $oTemplateSite->add(array(
                            'TID' => intval($resTemplate->getId()),
                            'SID' => $sid
                        ));
                    }
                }
            }
        }
        
        /**
         * Добавление типов шаблонов смс и самих шаблонов
         */
        public function UnInstallTemplates()
        {
            //        \Bitrix\Main\Loader::includeModule($this->MODULE_ID);
            //        $oTemplateType = new \Bxmaker\SmsNotice\Template\TypeTable();
            //        $oTemplate = new \Bxmaker\SmsNotice\TemplateTable();
            
            // это не нужно, так как эти данные хранятся в базе, а во время удаления таблицы из базы стираются
            
        }
        
        
        public function GetModuleRightList()
        {
            
            return array(
                "reference_id" => array("D", "S", "W"),
                "reference" => array(
                    GetMessage($this->MODULE_ID . '.RIGHTS.D'),
                    GetMessage($this->MODULE_ID . '.RIGHTS.S'),
                    GetMessage($this->MODULE_ID . '.RIGHTS.W')
                )
            );
        }
        
    }

