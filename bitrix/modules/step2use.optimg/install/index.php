<?
IncludeModuleLangFile(__FILE__);
Class step2use_optimg extends CModule
{
	const MODULE_ID = 'step2use.optimg';
	const API_URL = "https://optipic.ru/api/";
	const TEST_API_URL = "http://optipic.dev.atlant2010.ru/api/";
	var $MODULE_ID = 'step2use.optimg'; 
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("step2use.optimg_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("step2use.optimg_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("step2use.optimg_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("step2use.optimg_PARTNER_URI");
	}

	function InstallDB($arParams = array()) {
	    global $DBType, $APPLICATION;
        
        $node_id = strlen($arParams["DATABASE"]) > 0? intval($arParams["DATABASE"]): false;

		if($node_id !== false) {
			$DB = $GLOBALS["DB"]->GetDBNodeConnection($node_id);
	    }
		else {
			$DB = $GLOBALS["DB"];
		}
		
		// ??????? ???????
        $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/".strtolower($DBType)."/install.sql");
        if($this->errors !== false) {
			$APPLICATION->ThrowException(implode("<br>", $this->errors));
			return false;
		}
		
		
		
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CStepUseOptimg', 'OnBuildGlobalMenu');
		
		return true;
	}

	function UnInstallDB($arParams = array()) {
	    $DB = CDatabase::GetModuleConnection(self::MODULE_ID);
	    
	    // ??????? ???????
	    //$DB->Query("DROP TABLE IF EXISTS atl_optimg_files");
	    
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CStepUseOptimg', 'OnBuildGlobalMenu');
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}

		CopyDirFiles(
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/step2use.optimg/install/images/',
		 	$_SERVER['DOCUMENT_ROOT'].'/bitrix/images/step2use.optimg/', true, true
		);

		 CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/step2use.optimg/install/themes", 
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true
       	 );

		return true;
	}

	function UnInstallFiles()
	{
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0))
					{
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}

		DeleteDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/step2use.optimg/install/themes/.default/",
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default"
            );

        DeleteDirFilesEx("/bitrix/themes/.default/icons/step2use.optimg/");
        DeleteDirFilesEx("/bitrix/images/step2use.optimg/");

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION, $USER, $step;
		
		if(!function_exists("curl_init")) {
    		$this->errors []= GetMessage("step2use.optimg_ERROR_CURL");
		    $APPLICATION->ThrowException(implode("<br>", $this->errors));
		    return false;
		}
		//var_dump($_POST);exit;
		//$step = IntVal($_POST["step"]);
		
		
		$newInstall = false;
        if(!COption::GetOptionString("step2use.optimg", "LOGIN") || !COption::GetOptionString("step2use.optimg", "PASSWORD")) {
            $newInstall = true;
        }
		
		$step = IntVal($step);
        if($step < 2 && $newInstall) {
            
		    //if(CModule::IncludeModule(self::MODULE_ID)) {
		        $APPLICATION->IncludeAdminFile(GetMessage('ATL_OPTIPIC_INSTALL_STEP1_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/step2use.optimg/install/step1.php');
		        return false;
		    //}
        }
        else { //if($step == 2) {
            //$APPLICATION->IncludeAdminFile(GetMessage('ATL_OPTIPIC_INSTALL_STEP1_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/step2use.optimg/install/step2.php');
            //var_dump($_POST["atl_optipic_user_email"]);exit;
		
		    
		//if(true) {
		    //CStepUseOptimg::ReindexFileBase(true);

            if(isset($_POST["atl_optipic_account_exists"])) {
                $newInstall = false;
                COption::SetOptionString("step2use.optimg", "LOGIN", $_POST["atl_optipic_user_email"]);
                COption::SetOptionString("step2use.optimg", "PASSWORD", $_POST["atl_optipic_user_pass"]);
                COption::SetOptionString("step2use.optimg", "API_ERROR", "N"); // ??????? ? API ???
            }

            
            
            if($newInstall) {
                COption::SetOptionString("step2use.optimg", "LOGIN", $_POST["atl_optipic_user_email"]);

		        // ???????????? ?????? ????? ? OptiPic.ru ??? ?????????????? ??????
    		    $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, self::getApiUrl()."register?email=".urlencode($_POST["atl_optipic_user_email"])."&username=".urlencode($_POST["atl_optipic_user_email"])."&from=bitrix&site=". urlencode($_SERVER['HTTP_HOST']));
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $apiResult = curl_exec($ch);
                $apiResult = json_decode($apiResult, true);
                //var_dump($apiResult); exit;
        
                global $atlOptipicInstallErrors;
                $atlOptipicInstallErrors = false;
        
                if($apiResult['result']===true) {
                
                    COption::SetOptionString("step2use.optimg", "PASSWORD", $apiResult['password']);
                    COption::SetOptionString("step2use.optimg", "API_ERROR", "N"); // ??????? ? API ???
                }
                else {
                    //LocalRedirect("/bitrix/admin/partner_modules.php?id=step2use.optimg&install=Y");
                
                    global $atlOptipicInstallErrors;
                    $atlOptipicInstallErrors = array(GetMessage("ATL_OPTIPIC_INSTALL_EMAIL_IS_BUSY"));
                    $APPLICATION->IncludeAdminFile(GetMessage('ATL_OPTIPIC_INSTALL_STEP1_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/step2use.optimg/install/step1.php');
		            return false;
                
                    //COption::SetOptionString("step2use.optimg", "API_ERROR", "Y"); // ???? ???????? ? API
                    //COption::SetOptionString("step2use.optimg", "LAST_API_ERROR", GetMessage("ATL_INSTALL_API_ERROR")); //  ????????? ????? ??????
                }
            }
		    
		    $this->InstallFiles();
		    $this->InstallDB();
		    RegisterModule(self::MODULE_ID);
		
		    CModule::IncludeModule(self::MODULE_ID);
		    
		    // ??? ????? ??? ???????? ??????
		    CAgent::AddAgent(
                "CStepUseOptimg::OptimizeAllImgs();",     // ??? ???????
                "step2use.optimg",                         // ????????????? ??????
                "N",                                  // ????? ???????? ? ???-?? ????????
                300,                          // ???????? ???????
                "",                             // ????? ????????? ?????? ??????? (??????)
                "Y"                          // ????? ???????
            );
            
            $tomorrow = date("d.m.Y", time() + 86400);
            
            // ??? ????? ??? ?????????? ???????
            CAgent::AddAgent(
                "CStepUseOptimg::RefreshIndexBase();", // ??? ???????
                "step2use.optimg",                          // ????????????? ??????
                "N",                                  // ????? ?? ???????? ? ???-?? ????????
                86400,                                // ???????? ??????? - 1 ?????
                $tomorrow." 23:00:00",                // ???? ?????? ???????? ?? ??????
                "Y",                                  // ????? ???????
                $tomorrow." 23:00:00"                // ???? ??????? ???????
            );
            
            // ???? ??? ?? ????-?????? ? ?? ????????? ?????????, ?? ????????? 2 ?? ?? ???? ???????
            /*if($newInstall && CModule::IncludeModuleEx(self::MODULE_ID)===MODULE_INSTALLED) {
		        $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://optipic.ru/api/installbitrix");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
                curl_setopt($ch, CURLOPT_USERPWD, COption::GetOptionString("step2use.optimg", "LOGIN").":".COption::GetOptionString("step2use.optimg", "PASSWORD"));
                $apiResult = curl_exec($ch);
                //var_dump($apiResult);exit;
                $info = curl_getinfo($ch);
                //var_dump($info);exit;
                //$apiResult = json_decode($apiResult, true);
            }*/
            
            if($newInstall) {
                LocalRedirect("/bitrix/admin/settings.php?lang=ru&mid=step2use.optimg&installed=Y#reindex");
            }
            else {
                LocalRedirect("/bitrix/admin/settings.php?lang=ru&mid=step2use.optimg&reinstalled=Y#reindex");
            }
		//}
		
		}
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallFiles();
		
		CAgent::RemoveModuleAgents(self::MODULE_ID);
	}
	
	public static function getApiUrl() {
        return (COption::GetOptionString("step2use.optimg", "USE_TEST_API")=="Y")? self::TEST_API_URL: self::API_URL;
    }
}
?>
