<?
IncludeModuleLangFile(__FILE__);

class acrit_googlemerchant extends CModule {
	const MODULE_ID = 'acrit.googlemerchant';
	var $MODULE_ID = 'acrit.googlemerchant';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	private $siteArray = [];
	private $code; // module
	private $Code; // Module
	private $CODE; // MODULE
	private $siteEncoding = [
		'utf-8' => 'utf8',
		'UTF-8' => 'utf8',
		'WINDOWS-1251' => 'cp1251',
		'windows-1251' => 'cp1251',
	];

	function __construct(){
		$this->code = toLower(preg_replace('#^acrit\.(.*?)$#i', '$1', $this->MODULE_ID));
		$this->Code = toUpper(substr($this->code, 0, 1)).substr($this->code, 1);
		$this->CODE = toUpper($this->code);
		#
		require(__DIR__.'/version.php');
		if(is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)){
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}
		$this->MODULE_NAME = GetMessage('ACRIT_GOOGLEMERCHANT_MODULE_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('ACRIT_GOOGLEMERCHANT_MODULE_DESC');
		$this->PARTNER_NAME = GetMessage('ACRIT_GOOGLEMERCHANT_PARTNER_NAME');
		$this->PARTNER_URI = GetMessage('ACRIT_GOOGLEMERCHANT_PARTNER_URI');
		#
		$app = \Bitrix\Main\Application::getInstance();
		$dbSite = \Bitrix\Main\SiteTable::getList();
		while($arSite = $dbSite->Fetch()){
			if(!$arSite['DOC_ROOT'])
				$this->siteArray[$arSite['LID']] = $app->getDocumentRoot().$arSite['DIR'];
			else {
				$this->siteArray[$arSite['LID']] = $arSite['DOC_ROOT'];
			}
			$this->siteArray[$arSite['LID']] = \Bitrix\Main\IO\Path::normalize($this->siteArray[$arSite['LID']]);
		}
	}

	function InstallEvents(){
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', $this->MODULE_ID, 'CAcrit'.$this->Code.'Menu', 'OnBuildGlobalMenu');
		RegisterModuleDependences($this->MODULE_ID, 'OnCondCatControlBuildList', $this->MODULE_ID, 'CAcritCatalogCondCtrlGroup', 'GetControlDescr', 100);
		RegisterModuleDependences($this->MODULE_ID, 'OnCondCatControlBuildList', $this->MODULE_ID, 'CAcritCatalogCondCtrlIBlockFields', 'GetControlDescr', 200);
		RegisterModuleDependences($this->MODULE_ID, 'OnCondCatControlBuildList', $this->MODULE_ID, 'CAcritCatalogCondCtrlIBlockProps', 'GetControlDescr', 300);
		return true;
	}

	function UnInstallEvents(){
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', $this->MODULE_ID, 'CAcrit'.$this->Code.'Menu', 'OnBuildGlobalMenu');
		UnRegisterModuleDependences($this->MODULE_ID, 'OnCondCatControlBuildList', $this->MODULE_ID, 'CAcritCatalogCondCtrlGroup', 'GetControlDescr');
		UnRegisterModuleDependences($this->MODULE_ID, 'OnCondCatControlBuildList', $this->MODULE_ID, 'CAcritCatalogCondCtrlIBlockFields', 'GetControlDescr');
		UnRegisterModuleDependences($this->MODULE_ID, 'OnCondCatControlBuildList', $this->MODULE_ID, 'CAcritCatalogCondCtrlIBlockProps', 'GetControlDescr');
		return true;
	}

	function InstallDB($arParams = []){
		global $APPLICATION, $DB, $DBType;
		if(\Bitrix\Main\Loader::includeModule('security')){
			$dbSecurityFilter = \CSecurityFilterMask::GetList();
			$arFilterMask = [];
			while($arSecurityFilter = $dbSecurityFilter->Fetch()){
				$arFilterMask[] = [
					'MASK' => $arSecurityFilter['FILTER_MASK'],
					'SITE_ID' => $arSecurityFilter['SITE_ID'],
				];
			}
			$arFilterMask[] = [
				'MASK' => '/bitrix/admin/acrit_'.$this->code.'_edit.php',
				'SITE_ID' => '',
			];
			$arFilterMask[] = [
				'MASK' => '/bitrix/admin/acrit_'.$this->code.'_new_edit.php*',
				'SITE_ID' => '',
			];
			\CSecurityFilterMask::Update($arFilterMask);
		}
		$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/db/install.sql');
		if($this->errors === false && \Bitrix\Main\Loader::includeModule('acrit.core')){
			$this->errors = \Acrit\Core\Helper::runCoreSqlBatch('export/install.sql', $this->MODULE_ID);
		}
		if(is_array($this->errors)){
			$APPLICATION->ThrowException(implode('<br/>', $this->errors));
			return false;
		}
		return true;
	}

	function UninstallDB($arParams = []){
		global $APPLICATION, $DB, $DBType;
		$this->errors = false;
		if(\Bitrix\Main\Loader::includeModule('security')){
			$dbSecurityFilter = \CSecurityFilterMask::GetList();
			$arAcritMask = ['/bitrix/admin/acrit_'.$this->code.'_edit.php', '/bitrix/admin/acrit_'.$this->code.'_new_edit.php*'];
			$arFilterMask = [];
			while($arSecurityFilter = $dbSecurityFilter->Fetch()){
				if(!in_array($arSecurityFilter['FILTER_MASK'], $arAcritMask)){
					$arFilterMask[] = [
						'MASK' => $arSecurityFilter['FILTER_MASK'],
						'SITE_ID' => $arSecurityFilter['SITE_ID'],
					];
				}
			}
			\CSecurityFilterMask::Update($arFilterMask);
		}
		if(\Bitrix\Main\Loader::includeModule('acrit.core')){
			$this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/db/uninstall.sql');
			\Acrit\Core\Helper::runCoreSqlBatch('export/uninstall.sql', $this->MODULE_ID);
		}
		return true;
	}

	function DoInstall(){
		global $APPLICATION, $DB, $DBType, $step, $install;
		$GLOBALS['ACRIT_MODULE_ID'] = $this->MODULE_ID;
		$GLOBALS['ACRIT_MODULE_NAME'] = $this->MODULE_NAME;
		if($APPLICATION->GetGroupRight('main') < 'W'){
			return;
		}
		if(!\Bitrix\Main\Loader::includeModule('acrit.core')){
			$APPLICATION->ThrowException(getMessage('ACRIT_'.$this->CODE.'_NO_CORE'));
			return false;
		}
		if(!CheckVersion(PHP_VERSION, '5.4.0')){
			$APPLICATION->ThrowException(GetMessage('ACRIT_'.$this->CODE.'_PHP_REQUIRE'));
			return false;
		}
		$this->reset();
		CJSCore::Init('jquery');
		if(!isset($step) || ($step < 1)){
			$APPLICATION->IncludeAdminFile(GetMessage('ACRIT_'.$this->CODE.'_RECOMMENDED'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/step.php');
		}
		elseif(($step == 3) && ($install == 'Y')){
			RegisterModule($this->MODULE_ID);
			$this->InstallFiles();
			$this->InstallDB();
			$this->InstallEvents();
			$this->RegisterGadget();
			require __DIR__.'/_agents.php';
			if(\Bitrix\Main\Loader::includeModule('acrit.core') && class_exists('\Acrit\Core\Helper')){
				\Acrit\Core\Helper::startBitrixCloudMonitoring('admin@acrit.ru');
				#
				$strPhpPath = \Acrit\Core\Cli::getPhpPath();
				if(strlen($strPhpPath)){
					\Bitrix\Main\Config\Option::set($this->MODULE_ID, 'php_path', $strPhpPath);
				}
			}
			$urlRewriter = new \CUrlRewriter();
			foreach($this->siteArray as $siteID => $siteDir){
				$urlRewriter->add([
					'SITE_ID' => $siteID,
					'CONDITION' => '#^/acrit.'.$this->code.'/(.*)#',
					'PATH' => '/acrit.'.$this->code.'/index.php',
					'RULE' => 'path=$1',
				]);
			}
			$APPLICATION->IncludeAdminFile(GetMessage('MOD_INST_OK'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/step3.php');
		}
		elseif($step == 2){
			CheckDirPath(__DIR__.'/db/category');
			CopyDirFiles(__DIR__.'/db/', __DIR__.'/db/category');
			$APPLICATION->IncludeAdminFile(GetMessage('MOD_INST_OK'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/step2.php');
		}
	}

	function DoUninstall(){
		global $APPLICATION, $DB, $DBType;
		if($APPLICATION->GetGroupRight('main') < 'W'){
			return;
		}
		if(!\Bitrix\Main\Loader::includeModule('acrit.core')){
			$APPLICATION->ThrowException(getMessage('ACRIT_'.$this->CODE.'_NO_CORE'));
			return false;
		}
		$GLOBALS['ACRIT_MODULE_ID'] = $this->MODULE_ID;
		$GLOBALS['ACRIT_MODULE_NAME'] = $this->MODULE_NAME;
		if($_REQUEST['step'] < 2){
			$APPLICATION->IncludeAdminFile(GetMessage('acrit.'.$this->code.'_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/uninst_form.php');
		}
		elseif($_REQUEST['step'] == 2){
			global $APPLICATION;
			$this->UnRegisterGadget();
			$this->UnInstallEvents();
			$this->UnInstallFiles();
			if($_REQUEST['savedata'] != 'Y'){
				$this->UnInstallDB();
			}
			\CAdminNotify::DeleteByModule($this->MODULE_ID);
			UnRegisterModule($this->MODULE_ID);
			$this->reset();
			$APPLICATION->IncludeAdminFile(GetMessage('acrit.'.$this->code.'_UNINSTALL_TITLE'), $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/uninst_mail.php');
		}
	}

	function InstallFiles($arParams = []){
		global $DB, $DBType, $APPLICATION;
		if(is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/admin')){
			if($dir = opendir($p)){
				while(false !== ($item = readdir($dir))){
					if(in_array($item, ['.', '..', 'menu.php', 'tabs', 'new'])){
						continue;
					}
					file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/acrit_'.$this->code.'_'.$item,
						'<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.$this->MODULE_ID.'/admin/'.$item.'");?>');
				}
				closedir($dir);
			}
		}
		if($_ENV['COMPUTERNAME'] != 'BX'){
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/', true, true);
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/', true, true);
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/js', $_SERVER['DOCUMENT_ROOT'].'/bitrix/js', true, true);
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/themes', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes', true, true);
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/gadgets', $_SERVER['DOCUMENT_ROOT'].'/bitrix/gadgets', true, true);
			CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/tools', $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools', true, true);
			foreach($this->siteArray as $siteDir){
				CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/public', $siteDir, true, true);
			}
		}
		return true;
	}

	function UnInstallFiles(){
		global $DB, $DBType, $APPLICATION;
		if(is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/admin')){
			$strCode = str_replace('.', '_', $this->MODULE_ID);
			if($dir = opendir($p)){
				while(false !== ($item = readdir($dir))){
					if(($item == '..') || ($item == '.')){
						continue;
					}
					if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$strCode.'_'.$item)){
						unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$strCode.'_'.$item);
					}
				}
				closedir($dir);
			}
		}
		if(is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components')){
			if($dir = opendir($p)){
				while(false !== ($item = readdir($dir))){
					if(($item == '..') || ($item == '.') || !is_dir($p0 = $p.'/'.$item)){
						continue;
					}
					$dir0 = opendir($p0);
					while(false !== ($item0 = readdir($dir0))){
						if(($item0 == '..') || ($item0 == '.')){
							continue;
						}
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}
		if($_ENV['COMPUTERNAME'] != 'BX'){
			DeleteDirFilesEx('/bitrix/js/acrit.'.$this->code.'/');
			DeleteDirFilesEx('/bitrix/gadgets/acrit/'.$this->code.'/');
			DeleteDirFilesEx('/bitrix/tools/acrit.'.$this->code.'/');
			DeleteDirFilesEx('/bitrix/themes/.default/acrit/'.$this->code.'/');
			DeleteDirFilesEx('/bitrix/themes/.default/acrit/'.$this->code.'/');
			DeleteDirFilesEx('/bitrix/themes/.default/acrit.'.$this->code.'.css');
			DeleteDirFilesEx('/upload/acrit_'.$this->code.'/');
			DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/components', $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/');
			foreach($this->siteArray as $siteDir){
				DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/public', $siteDir);
			}
			DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/admin', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/');
		}
		DeleteDirFilesEx('/upload/'.$this->code.'_log');
		DeleteDirFilesEx('/'.$this->MODULE_ID);
		return true;
	}

	function RegisterGadget(){
		$arModuleNameParts = explode('.', $this->MODULE_ID);
		$arAcritGadget = [
			toUpper($arModuleNameParts[1]).'@'.time() => [
				'COLUMN' => 0,
				'ROW' => 0,
				'HIDE' => 'N',
			],
		];
		$arOptions = \CUserOptions::getOption('intranet', '~gadgets_admin_index');
		if(is_array($arOptions[0]['GADGETS']) && !empty($arOptions[0]['GADGETS'])){
			$arOptions[0]['GADGETS'] = array_merge($arAcritGadget, $arOptions[0]['GADGETS']);
		}
		else{
			$arOptions[0]['GADGETS'] = $arAcritGadget;
		}
		\CUserOptions::SetOption('intranet', '~gadgets_admin_index', $arOptions, false, false);
	}


	function UnRegisterGadget(){
		$arModuleNameParts = explode('.', $this->MODULE_ID);
		$moduleUpperId = ToUpper($arModuleNameParts[1]);
		$arOptions = \CUserOptions::getOption('intranet', '~gadgets_admin_index');
		foreach($arOptions[0]['GADGETS'] as $gadgetIndex => $arGadgetData){
			if(stripos($gadgetIndex, $moduleUpperId.'@') !== false){
				unset($arOptions[0]['GADGETS'][$gadgetIndex]);
			}
		}
		\CUserOptions::SetOption('intranet', '~gadgets_admin_index', $arOptions, false, false);
	}
	
	function reset(){
		global $DB;
		$DB->Query('DELETE FROM b_option WHERE `MODULE_ID`=\''.$this->MODULE_ID.'\' AND `NAME`=\'~bsm_stop_date\';');
	}
	
}