<?
IncludeModuleLangFile(__FILE__);

Class cackle_reviews extends CModule
{
    var $MODULE_ID = "cackle.reviews";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "Y";

    function __construct()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION =  $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
		$this->PARTNER_NAME = "Cackle";
		$this->PARTNER_URI = "http://cackle.me";
        $this->MODULE_NAME = GetMessage('APP_PLATFORM_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('APP_PLATFORM_MODULE_DESCRIPTION');
    }
//
    function InstallDB()
    {

        RegisterModule($this->MODULE_ID);
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID . "/install/db/".strtolower($DB->type)."/install.sql");
        return true;
    }

    function UnInstallDB($arParams = array())
    {
        //UnRegisterModule($this->MODULE_ID);
        global $DB, $DBType, $APPLICATION;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID . "/install/db/".strtolower($DB->type)."/uninstall.sql");
        return true;
    }

    function InstallFiles()
    {

        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/",
            true, true
        );

        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/",
            true, true
        );

        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFilesEx("/bitrix/components/cackle.reviews/");
        return true;
    }

    function DoInstall()
    {
        global $USER, $APPLICATION;
        if(!$USER->IsAdmin())
            return;


        //RegisterModuleDependences("sale", "OnOrderAdd", "cackle.reviews", "", "",1,"/modules/cackle.reviews/classes/general/cackle_reviews_orders_realtime.php");
        //RegisterModuleDependences("sale", "OnOrderUpdate", "cackle.reviews", "", "",1,"/modules/cackle.reviews/classes/general/cackle_reviews_orders_realtime.php");
        $this->InstallDB();
        $this->InstallFiles();
        //$obCache = new CPHPCache();
        $objSites = CSite::GetList($by="sort", $order="desc");
        while ($arrSite = $objSites->Fetch())
            BXClearCache(true, "/".$arrSite["ID"]."/bitrix/catalog.element/");
        RegisterModuleDependences("sale", "OnOrderAdd", "cackle.reviews", "cackle_reviews_orders_realtime", "OnOrderAdd");
        RegisterModuleDependences("sale", "OnOrderUpdate", "cackle.reviews", "cackle_reviews_orders_realtime", "OnOrderUpdate");
        //RegisterModuleDependences("sale", "OnSaleComponentOrderComplete", "cackle.reviews", "cackle_reviews_orders_realtime", "OnSaleComponentOrderComplete");
        RegisterModuleDependences("main", "OnBeforeProlog", "main", "", "",1,"/modules/cackle.reviews/prolog.php");
        RegisterModuleDependences("main", "OnAfterEpilog", "main", "", "",1,"/modules/cackle.reviews/epilog.php");
        $APPLICATION->IncludeAdminFile(GetMessage("APP_PLATFORM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID . "/install/step.php");

return true;
    }

    function DoUninstall(){
        global $USER, $DB, $APPLICATION, $step;
        if($USER->IsAdmin()){
        $this->UnInstallDB();
        $this->UnInstallFiles();
        $GLOBALS["errors"] = $this->errors;
            $obCache = new CPHPCache;
            $arrSites = array();
            $objSites = CSite::GetList($by="sort", $order="desc");
            while ($arrSite = $objSites->Fetch())
                    BXClearCache(true, "/".$arrSite["ID"]."/bitrix/catalog.element/");

            if(CModule::IncludeModule("main")){
                //COption::RemoveOption("cackle.reviews", "last_review_". CMainPage::GetSiteByHost());
                //COption::RemoveOption("cackle.reviews", "last_modified_". CMainPage::GetSiteByHost());
            }
            UnRegisterModuleDependences("sale", "OnOrderAdd", "cackle.reviews", "cackle_reviews_orders_realtime", "OnOrderAdd");

            UnRegisterModuleDependences("sale", "OnOrderUpdate", "cackle.reviews", "cackle_reviews_orders_realtime", "OnOrderUpdate");
            UnRegisterModuleDependences("sale", "OnSaleComponentOrderComplete", "cackle.reviews", "cackle_reviews_orders_realtime", "OnSaleComponentOrderComplete");
            UnRegisterModuleDependences("sale", "OnOrderAdd", "cackle.reviews", "", "",1,"/modules/cackle.reviews/classes/general/cackle_reviews_orders_realtime.php");
            UnRegisterModuleDependences("sale", "OnOrderUpdate", "cackle.reviews", "", "",1,"/modules/cackle.reviews/classes/general/cackle_reviews_orders_realtime.php");
            UnRegisterModule("$this->MODULE_ID");
            UnRegisterModuleDependences("main", "OnBeforeProlog", "main", "", "","/modules/cackle.reviews/prolog.php");
            UnRegisterModuleDependences("main", "OnAfterEpilog", "main", "", "","/modules/cackle.reviews/epilog.php");
         //       $APPLICATION->IncludeAdminFile(GetMessage("APP_PLATFORM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/cackle/install/unstep.php");

        return true;
        }
    }
}
?>