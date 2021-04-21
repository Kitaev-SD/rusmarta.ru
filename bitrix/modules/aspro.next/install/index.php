<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

class aspro_next extends CModule {
	const solutionName	= 'next';
	const partnerName = 'aspro';
	const moduleClass = 'CNext';
	const moduleClassEvents = 'CNextEvents';
	const moduleClassCache = 'CNextCache';

	var $MODULE_ID = "aspro.next";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function aspro_next(){
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("ASPRO_NEXT_SCOM_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("ASPRO_NEXT_SCOM_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("ASPRO_NEXT_SPER_PARTNER");
		$this->PARTNER_URI = GetMessage("ASPRO_NEXT_PARTNER_URI");
	}

	function checkValid(){
		return true;
	}

	function InstallDB($install_wizard = true){
		global $DB, $DBType, $APPLICATION;

		/*if(preg_match ( '/.bitrixlabs.ru/' , $_SERVER["HTTP_HOST"])){
			RegisterModuleDependences("main", "OnBeforeProlog", $this->MODULE_ID, self::moduleClassEvents, "correctInstall");
		}*/

		RegisterModule($this->MODULE_ID);
		// RegisterModuleDependences("main", "OnBeforeProlog", $this->MODULE_ID, self::moduleClassEvents, "ShowPanel");

		return true;
	}

	function UnInstallDB($arParams = array()){
		global $DB, $DBType, $APPLICATION;

		UnRegisterModule($this->MODULE_ID);

		return true;
	}

	function InstallEvents(){
		RegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID, self::moduleClassEvents, "OnPageStartHandler");
		RegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, self::moduleClassEvents, 'OnEndBufferContentHandler');
		RegisterModuleDependences('main', 'OnBeforeEventAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeEventAddHandler');
		RegisterModuleDependences("main", "OnBeforeUserRegister", $this->MODULE_ID, self::moduleClassEvents, "OnBeforeUserUpdateHandler");
		RegisterModuleDependences("main", "OnBeforeUserAdd", $this->MODULE_ID, self::moduleClassEvents, "OnBeforeUserUpdateHandler");
		RegisterModuleDependences("main", "OnBeforeUserUpdate", $this->MODULE_ID, self::moduleClassEvents,"OnBeforeUserUpdateHandler");
		RegisterModuleDependences("main", "OnAfterUserUpdate", $this->MODULE_ID, self::moduleClassCache, "ClearTagByUser");

		RegisterModuleDependences("iblock", "OnAfterIBlockAdd", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlock");
		RegisterModuleDependences("iblock", "OnAfterIBlockUpdate", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlock");
		RegisterModuleDependences("iblock", "OnBeforeIBlockDelete", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockBeforeDelete");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockElement");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockElement");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, self::moduleClassEvents, "OnRegionUpdateHandler");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockElement");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, self::moduleClassEvents, "DoIBlockAfterSave");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", $this->MODULE_ID, self::moduleClassEvents, "DoIBlockAfterSave");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", $this->MODULE_ID, self::moduleClassCache, "DoIBlockElementAfterDelete");
		RegisterModuleDependences("iblock", "OnAfterIBlockSectionAdd", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockSection");
		RegisterModuleDependences("iblock", "OnAfterIBlockSectionUpdate", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockSection");
		RegisterModuleDependences("iblock", "OnBeforeIBlockSectionDelete", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockSectionBeforeDelete");
		RegisterModuleDependences("iblock", "OnAfterIBlockPropertyUpdate", $this->MODULE_ID, self::moduleClassCache, "ClearTagByProperty");
		RegisterModuleDependences("iblock", "OnAfterIBlockPropertyAdd", $this->MODULE_ID, self::moduleClassCache, "ClearTagByProperty");
		RegisterModuleDependences("iblock", "OnAfterIBlockPropertyDelete", $this->MODULE_ID, self::moduleClassCache, "ClearTagByProperty");
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\ListStores', "OnIBlockPropertyBuildList");
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\ListPrices', "OnIBlockPropertyBuildList");
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\ListLocations', "OnIBlockPropertyBuildList");
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\CustomFilter', "OnIBlockPropertyBuildList");
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\Service', "OnIBlockPropertyBuildList");
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\YaDirectQuery', "OnIBlockPropertyBuildList");
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\IBInherited', "OnIBlockPropertyBuildList");
		RegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\ListUsersGroups', "OnIBlockPropertyBuildList");

		RegisterModuleDependences("sale", "OnBasketAdd", $this->MODULE_ID, self::moduleClassEvents, "clearBasketCacheHandler");
		RegisterModuleDependences("sale", "OnBeforeBasketUpdate", $this->MODULE_ID, self::moduleClassEvents, "OnBeforeBasketUpdateHandler");
		RegisterModuleDependences("sale", "OnSaleComponentOrderProperties", $this->MODULE_ID, self::moduleClassEvents, "OnSaleComponentOrderPropertiesHandler");
		RegisterModuleDependences("sale", "OnSaleComponentOrderProperties", $this->MODULE_ID, self::moduleClassEvents, "OnSaleComponentOrderProperties");
		RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", $this->MODULE_ID, self::moduleClassEvents, "OnSaleComponentOrderOneStepComplete");
		RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", $this->MODULE_ID, self::moduleClassEvents, "clearBasketCacheHandler");
		if(class_exists('\Bitrix\Main\EventManager')){
			$eventManager = \Bitrix\Main\EventManager::getInstance();
			$eventManager->registerEventHandler('sale', 'OnSaleOrderSaved', $this->MODULE_ID, self::moduleClassEvents, 'BeforeSendEvent', 10);
		}

		RegisterModuleDependences("catalog", "OnPriceAdd", $this->MODULE_ID, self::moduleClassEvents, "DoIBlockAfterSave");
		RegisterModuleDependences("catalog", "OnPriceUpdate", $this->MODULE_ID, self::moduleClassEvents, "DoIBlockAfterSave");
		RegisterModuleDependences("catalog", "OnProductUpdate", $this->MODULE_ID, self::moduleClassEvents, "setStockProduct");
		RegisterModuleDependences("catalog", "OnProductAdd", $this->MODULE_ID, self::moduleClassEvents, "setStockProduct");
		RegisterModuleDependences("catalog", "OnProductAdd", $this->MODULE_ID, self::moduleClassEvents, "setStockProduct");
		RegisterModuleDependences("catalog", "OnStoreProductAdd", $this->MODULE_ID, self::moduleClassEvents, "setStoreProductHandler");
		RegisterModuleDependences("catalog", "OnStoreProductUpdate", $this->MODULE_ID, self::moduleClassEvents, "setStoreProductHandler");
		RegisterModuleDependences("catalog", "OnGetOptimalPrice", $this->MODULE_ID, self::moduleClassEvents, "OnGetOptimalPriceHandler");

		RegisterModuleDependences("form", "onAfterResultAdd", $this->MODULE_ID, self::moduleClassEvents, "onAfterResultAddHandler");
		RegisterModuleDependences('form', 'onBeforeResultAdd', $this->MODULE_ID, self::moduleClassEvents, 'onBeforeResultAddHandler');

		RegisterModuleDependences("socialservices", "OnAfterSocServUserAdd", $this->MODULE_ID, self::moduleClassEvents, "OnAfterSocServUserAddHandler");
		RegisterModuleDependences('socialservices', 'OnFindSocialservicesUser', $this->MODULE_ID, self::moduleClassEvents, "OnFindSocialservicesUserHandler");

		RegisterModuleDependences('search', 'OnSearchGetURL', $this->MODULE_ID, self::moduleClassEvents, 'OnSearchGetURL');

		RegisterModuleDependences("sender", "onPresetTemplateList", $this->MODULE_ID, "\Aspro\Solution\CAsproMarketing", "senderTemplateList");

		RegisterModuleDependences('subscribe', 'OnBeforeSubscriptionAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeSubscriptionAddHandler');

		RegisterModuleDependences("seo", "\Bitrix\Seo\Sitemap::OnAfterUpdate", $this->MODULE_ID, self::moduleClassEvents, 'OnAfterUpdateSitemapHandler');

		RegisterModuleDependences($this->MODULE_ID, "OnCatalogDeliveryComponentInitUserResult", $this->MODULE_ID, self::moduleClassEvents, "OnCatalogDeliveryComponentInitUserResult");

		return true;
	}

	function UnInstallEvents(){
		UnRegisterModuleDependences("main", "OnPageStart", $this->MODULE_ID, self::moduleClassEvents, "OnPageStartHandler");
		UnRegisterModuleDependences('main', 'OnEndBufferContent', $this->MODULE_ID, self::moduleClassEvents, 'OnEndBufferContentHandler');
		UnRegisterModuleDependences('main', 'OnBeforeEventAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeEventAddHandler');
		UnRegisterModuleDependences("main", "OnBeforeUserRegister", $this->MODULE_ID, self::moduleClassEvents, "OnBeforeUserUpdateHandler");
		UnRegisterModuleDependences("main", "OnBeforeUserAdd", $this->MODULE_ID, self::moduleClassEvents, "OnBeforeUserUpdateHandler");
		UnRegisterModuleDependences("main", "OnBeforeUserUpdate", $this->MODULE_ID, self::moduleClassEvents,"OnBeforeUserUpdateHandler");
		UnRegisterModuleDependences("main", "OnAfterUserUpdate", $this->MODULE_ID, self::moduleClassCache, "ClearTagByUser");

		UnRegisterModuleDependences("iblock", "OnAfterIBlockAdd", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlock");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockUpdate", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlock");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockDelete", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockBeforeDelete");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockElement");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockElement");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, self::moduleClassEvents, "OnRegionUpdateHandler");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockElement");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", $this->MODULE_ID, self::moduleClassEvents, "DoIBlockAfterSave");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", $this->MODULE_ID, self::moduleClassEvents, "DoIBlockAfterSave");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", $this->MODULE_ID, self::moduleClassCache, "DoIBlockElementAfterDelete");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockSectionAdd", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockSection");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockSectionUpdate", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockSection");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockSectionDelete", $this->MODULE_ID, self::moduleClassCache, "ClearTagIBlockSectionBeforeDelete");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockPropertyUpdate", $this->MODULE_ID, self::moduleClassCache, "ClearTagByProperty");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockPropertyAdd", $this->MODULE_ID, self::moduleClassCache, "ClearTagByProperty");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockPropertyDelete", $this->MODULE_ID, self::moduleClassCache, "ClearTagByProperty");
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\ListStores', "OnIBlockPropertyBuildList");
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\ListPrices', "OnIBlockPropertyBuildList");
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\ListLocations', "OnIBlockPropertyBuildList");
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\CustomFilter', "OnIBlockPropertyBuildList");
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\Service', "OnIBlockPropertyBuildList");
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\YaDirectQuery', "OnIBlockPropertyBuildList");
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\IBInherited', "OnIBlockPropertyBuildList");
		UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, 'Aspro\Next\Property\ListUsersGroups', "OnIBlockPropertyBuildList");

		UnRegisterModuleDependences("sale", "OnBasketAdd", $this->MODULE_ID, self::moduleClassEvents, "clearBasketCacheHandler");
		UnRegisterModuleDependences("sale", "OnBeforeBasketUpdate", $this->MODULE_ID, self::moduleClassEvents, "OnBeforeBasketUpdateHandler");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderProperties", $this->MODULE_ID, self::moduleClassEvents, "OnSaleComponentOrderPropertiesHandler");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderProperties", $this->MODULE_ID, self::moduleClassEvents, "OnSaleComponentOrderProperties");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", $this->MODULE_ID, self::moduleClassEvents, "OnSaleComponentOrderOneStepComplete");
		UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", $this->MODULE_ID, self::moduleClassEvents, "clearBasketCacheHandler");
		if(class_exists('\Bitrix\Main\EventManager')){
			$eventManager = \Bitrix\Main\EventManager::getInstance();
			$eventManager->unregisterEventHandler('sale', 'OnSaleOrderSaved', $this->MODULE_ID, self::moduleClassEvents, 'BeforeSendEvent', 10);
		}

		UnRegisterModuleDependences("catalog", "OnPriceAdd", $this->MODULE_ID, self::moduleClassEvents, "DoIBlockAfterSave");
		UnRegisterModuleDependences("catalog", "OnPriceUpdate", $this->MODULE_ID, self::moduleClassEvents, "DoIBlockAfterSave");
		UnRegisterModuleDependences("catalog", "OnProductUpdate", $this->MODULE_ID, self::moduleClassEvents, "setStockProduct");
		UnRegisterModuleDependences("catalog", "OnProductAdd", $this->MODULE_ID, self::moduleClassEvents, "setStockProduct");
		UnRegisterModuleDependences("catalog", "OnProductAdd", $this->MODULE_ID, self::moduleClassEvents, "setStockProduct");
		UnRegisterModuleDependences("catalog", "OnStoreProductAdd", $this->MODULE_ID, self::moduleClassEvents, "setStoreProductHandler");
		UnRegisterModuleDependences("catalog", "OnStoreProductUpdate", $this->MODULE_ID, self::moduleClassEvents, "setStoreProductHandler");
		UnRegisterModuleDependences("catalog", "OnGetOptimalPrice", $this->MODULE_ID, self::moduleClassEvents, "OnGetOptimalPriceHandler");

		UnRegisterModuleDependences("form", "onAfterResultAdd", $this->MODULE_ID, self::moduleClassEvents, "onAfterResultAddHandler");
		UnRegisterModuleDependences('form', 'onBeforeResultAdd', $this->MODULE_ID, self::moduleClassEvents, 'onBeforeResultAddHandler');

		UnRegisterModuleDependences("socialservices", "OnAfterSocServUserAdd", $this->MODULE_ID, self::moduleClassEvents, "OnAfterSocServUserAddHandler");
		UnRegisterModuleDependences('socialservices', 'OnFindSocialservicesUser', $this->MODULE_ID, self::moduleClassEvents, "OnFindSocialservicesUserHandler");

		UnRegisterModuleDependences('search', 'OnSearchGetURL', $this->MODULE_ID, self::moduleClassEvents, 'OnSearchGetURL');

		UnRegisterModuleDependences("sender", "onPresetTemplateList", $this->MODULE_ID, "\Aspro\Solution\CAsproMarketing", "senderTemplateList");

		UnRegisterModuleDependences('subscribe', 'OnBeforeSubscriptionAdd', $this->MODULE_ID, self::moduleClassEvents, 'OnBeforeSubscriptionAddHandler');

		UnRegisterModuleDependences("seo", "\Bitrix\Seo\Sitemap::OnAfterUpdate", $this->MODULE_ID, self::moduleClassEvents, 'OnAfterUpdateSitemapHandler');

		UnRegisterModuleDependences($this->MODULE_ID, "OnCatalogDeliveryComponentInitUserResult", $this->MODULE_ID, self::moduleClassEvents, "OnCatalogDeliveryComponentInitUserResult");

		return true;
	}

	function removeDirectory($dir){
		if($objs = glob($dir."/*")){
			foreach($objs as $obj){
				if(is_dir($obj)){
					CNext::removeDirectory($obj);
				}
				else{
					if(!unlink($obj)){
						if(chmod($obj, 0777)){
							unlink($obj);
						}
					}
				}
			}
		}
		if(!rmdir($dir)){
			if(chmod($dir, 0777)){
				rmdir($dir);
			}
		}
	}

	function InstallFiles(){
		CopyDirFiles(__DIR__.'/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin', true);
		CopyDirFiles(__DIR__.'/css/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/css/'.self::partnerName.'.'.self::solutionName, true, true);
		CopyDirFiles(__DIR__.'/js/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.self::partnerName.'.'.self::solutionName, true, true);
		CopyDirFiles(__DIR__.'/tools/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/'.self::partnerName.'.'.self::solutionName, true, true);
		CopyDirFiles(__DIR__.'/images/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/images/'.self::partnerName.'.'.self::solutionName, true, true);
		CopyDirFiles(__DIR__.'/components/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components', true, true);
		CopyDirFiles(__DIR__.'/wizards/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/wizards', true, true);

		$this->InstallGadget();

		if(preg_match('/.bitrixlabs.ru/', $_SERVER["HTTP_HOST"])){
			@set_time_limit(0);
			require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/include.php");
			CFileMan::DeleteEx(array('s1', '/bitrix/modules/'.$this->MODULE_ID.'/install/wizards'));
			CFileMan::DeleteEx(array('s1', '/bitrix/modules/'.$this->MODULE_ID.'/install/gadgets'));
		}

		return true;
	}

	function InstallPublic(){
	}

	function UnInstallFiles(){
		DeleteDirFiles(__DIR__.'/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
		DeleteDirFilesEx('/bitrix/css/'.self::partnerName.'.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/js/'.self::partnerName.'.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/tools/'.self::partnerName.'.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/images/'.self::partnerName.'.'.self::solutionName.'/');
		DeleteDirFilesEx('/bitrix/wizards/'.self::partnerName.'/'.self::solutionName.'/');

		$this->UnInstallGadget();

		return true;
	}

	function InstallGadget(){
		CopyDirFiles(__DIR__.'/gadgets/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/gadgets/', true, true);

		$gadget_id = strtoupper(self::solutionName);
		$gdid = $gadget_id."@".rand();
		if(class_exists('CUserOptions')){
			$arUserOptions = CUserOptions::GetOption('intranet', '~gadgets_admin_index', false, false);
			if(is_array($arUserOptions) && isset($arUserOptions[0])){
				foreach($arUserOptions[0]['GADGETS'] as $tempid => $tempgadget){
					$p = strpos($tempid, '@');
					$gadget_id_tmp = ($p === false ? $tempid : substr($tempid, 0, $p));

					if($gadget_id_tmp == $gadget_id){
						return false;
					}
					if($tempgadget['COLUMN'] == 0){
						++$arUserOptions[0]['GADGETS'][$tempid]['ROW'];
					}
				}
				$arUserOptions[0]['GADGETS'][$gdid] = array('COLUMN' => 0, 'ROW' => 0);
				CUserOptions::SetOption('intranet', '~gadgets_admin_index', $arUserOptions, false, false);
			}
		}

		return true;
	}

	function UnInstallGadget(){
		$gadget_id = strtoupper(self::solutionName);
		if(class_exists('CUserOptions')){
			$arUserOptions = CUserOptions::GetOption('intranet', '~gadgets_admin_index', false, false);
			if(is_array($arUserOptions) && isset($arUserOptions[0])){
				foreach($arUserOptions[0]['GADGETS'] as $tempid => $tempgadget){
					$p = strpos($tempid, '@');
					$gadget_id_tmp = ($p === false ? $tempid : substr($tempid, 0, $p));

					if($gadget_id_tmp == $gadget_id){
						unset($arUserOptions[0]['GADGETS'][$tempid]);
					}
				}
				CUserOptions::SetOption('intranet', '~gadgets_admin_index', $arUserOptions, false, false);
			}
		}

		DeleteDirFilesEx('/bitrix/gadgets/'.self::partnerName.'/'.self::solutionName.'/');

		return true;
	}

	function DoInstall(){
		global $APPLICATION, $step;

		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallEvents();
		$this->InstallPublic();

		$APPLICATION->IncludeAdminFile(GetMessage("ASPRO_NEXT_SCOM_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/aspro.next/install/step.php");
	}

	function DoUninstall(){
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		$APPLICATION->IncludeAdminFile(GetMessage("ASPRO_NEXT_SCOM_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/aspro.next/install/unstep.php");
	}
}
?>