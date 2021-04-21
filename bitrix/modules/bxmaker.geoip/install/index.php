<?
    use Bitrix\Main\Localization\Loc as Loc;

    Loc::loadLanguageFile(__FILE__);


    class bxmaker_geoip extends CModule
    {

        var $MODULE_ID = "bxmaker.geoip";
        var $PARTNER_NAME = "BXmaker";
        var $PARTNER_URI = "http://bxmaker.ru/";

        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $PARTNER_ID = "bxmaker";

        /**
         * Массив зависимостей, для обработки событий других модулей
         * @var array
         */
        private $arModuleDependences
            = array(
                array('main', 'OnBuildGlobalMenu', 'bxmaker.geoip', '\Bxmaker\GeoIP\Handler', 'main_onBuildGlobalMenu'),
                array('main', 'OnBeforeProlog', 'bxmaker.geoip', '\Bxmaker\GeoIP\Handler', 'main_OnBeforeProlog'),
                array('sale', 'OnSaleComponentOrderOneStepOrderProps', 'bxmaker.geoip', '\Bxmaker\GeoIP\Handler', 'sale_OnSaleComponentOrderOneStepOrderProps'),
                array('sale', 'OnSaleComponentOrderProperties', 'bxmaker.geoip', '\Bxmaker\GeoIP\Handler', 'sale_OnSaleComponentOrderProperties'),
            );


        public function __construct()
        {
            include(__DIR__ . '/version.php');

            $this->MODULE_DIR = \Bitrix\Main\Loader::getLocal('modules/' . $this->MODULE_ID);

            $this->isLocal = !!strpos($this->MODULE_DIR, '/local/modules/');

            $this->MODULE_NAME = Loc::getMessage($this->MODULE_ID . '_MODULE_NAME');
            $this->MODULE_DESCRIPTION = Loc::getMessage($this->MODULE_ID . '_MODULE_DESCRIPTION');
            $this->PARTNER_NAME = GetMessage('bxmaker.geoip_PARTNER_NAME');
            $this->PARTNER_URI = GetMessage('bxmaker.geoip_PARTNER_URI');
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
            $this->RegisterEventType();
            $this->RegisterEventMessage();


            return true;
        }

        function DoUninstall()
        {
            $this->UnInstallDB();
            $this->UnInstallFiles();
            $this->UnInstallAgents();
            $this->UnInstallDependences();
            $this->UnRegisterEventType();
            $this->UnRegisterEventMessage();

            COption::RemoveOption($this->MODULE_ID);
            UnRegisterModule($this->MODULE_ID);

            return true;
        }

        /**
         * Добавление в базу необходимых таблиц для работы модуля
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

            // данные
            if ($this->isLocal) {
                CopyDirFiles($this->MODULE_DIR . "/install/php_interface/", $_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/", true, true);
            } else {
                CopyDirFiles($this->MODULE_DIR . "/install/php_interface/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/", true, true);
            }

            //        if ($this->isLocal) {
            //            CopyDirFiles($this->MODULE_DIR . "/install/templates/", $_SERVER["DOCUMENT_ROOT"] . "/local/templates/", true, true);
            //        } else {
            //            CopyDirFiles($this->MODULE_DIR . "/install/templates/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/", true, true);
            //        }

            if (file_exists($path = $this->MODULE_DIR . '/admin')) {
                if ($dir = opendir($path)) {
                    while (false !== $item = readdir($dir)) {
                        if (in_array($item, array('.', '..', 'menu.php')) || is_dir($path . '/' . $item)) {
                            continue;
                        }

                        if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item)) {
                            file_put_contents($file, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."/' . ($this->isLocal ? 'local' : 'bitrix') . '/modules/' . $this->MODULE_ID . '/admin/' . $item . '");?' . '>');
                        }
                    }
                }
            }
            return true;
        }

        /**
         * Удаление файлов
         * @return bool|void
         */
        function UnInstallFiles()
        {

            if (is_dir($this->MODULE_DIR . "/install/components/" . $this->PARTNER_ID . "/")) {
                $d = dir($this->MODULE_DIR . "/install/components/" . $this->PARTNER_ID . "/");
                while ($entry = $d->read()) {
                    if ($entry == '.' || $entry == '..') continue;

                    DeleteDirFilesEx('/local/components/' . $this->PARTNER_ID . '/' . $entry . '/');
                    DeleteDirFilesEx('/bitrix/components/' . $this->PARTNER_ID . '/' . $entry . '/');
                }
                $d->close();
            }


            //данные
            if (is_dir($this->MODULE_DIR . "/install/php_interface/")) {
                $d = dir($this->MODULE_DIR . "/install/php_interface/");
                while ($entry = $d->read()) {
                    if ($entry == '.' || $entry == '..') continue;
                    //$entry = bxmaker.ipgeo

                    DeleteDirFilesEx('/local/php_interface/' . $entry . '/');
                    DeleteDirFilesEx('/bitrix/php_interface/' . $entry . '/');

                }
                $d->close();
            }

            //		if (is_dir($this->MODULE_DIR . "/install/templates/.default/components/bitrix/")) {
            //            $d = dir($this->MODULE_DIR . "/install/templates/.default/components/bitrix/");
            //            while ($entry = $d->read()) {
            //                if ($entry == '.' || $entry == '..') continue;
            //                //$entry = system.auth.authorize
            //
            //                // Удаление шаблонов компонентов в Шаблоне по умолчанию
            //                if (is_dir($this->MODULE_DIR . "/install/templates/.default/components/bitrix/" . $entry . '/')) {
            //                    $int = dir($this->MODULE_DIR . "/install/templates/.default/components/bitrix/" . $entry . '/');
            //                    while ($dir = $int->read()) {
            //                        if ($dir == '.' || $dir == '..') continue;
            //                        DeleteDirFilesEx('/local/templates/.default/components/bitrix/' . $entry . '/' . $dir . '/');
            //                        DeleteDirFilesEx('/bitrix/templates/.default/components/bitrix/' . $entry . '/' . $dir . '/');
            //                    }
            //                }
            //            }
            //            $d->close();
            //        }


            if (file_exists($path = $this->MODULE_DIR . '/admin')) {
                if ($dir = opendir($path)) {
                    while (false !== $item = readdir($dir)) {
                        if (in_array($item, array('.', '..', 'menu.php')) || is_dir($path . '/' . $item)) {
                            continue;
                        }

                        if (file_exists($file = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item)) {
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
            $oAgent->AddAgent('\Bxmaker\GeoIP\Agent::updateBase();', $this->MODULE_ID, 'N', 86400);
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
                if (count($item) < 5) continue;

                //array('main', 'OnEpilog', 'bxmaker.geoip', '\Bxmaker\GeoIP\Handler', 'min_OnEpilog'),
                RegisterModuleDependences($item[0], $item[1], $item[2], $item[3], $item[4], (isset($item[5]) ? intval($item[5]) : 500));
            }
        }

        /**
         * Удаление обработчиков событий
         */
        public function UnInstallDependences()
        {
            foreach ($this->arModuleDependences as $item) {
                if (count($item) < 5) continue;
                //array('main', 'OnEpilog', 'bxmaker.geoip', '\Bxmaker\GeoIP\Handler', 'main_OnEpilog'),
                UnRegisterModuleDependences($item[0], $item[1], $item[2], $item[3], $item[4]);
            }
        }


        //регистрация типов почтовых шаблонов
        public function RegisterEventType()
        {

        }

        //удалим типов почтовых шаблонов
        function UnRegisterEventType()
        {

        }

        // регистрация почтовых шаблонов
        function RegisterEventMessage()
        {

        }

        //удаление почтовых шаблонов
        function UnRegisterEventMessage()
        {

        }


    }

