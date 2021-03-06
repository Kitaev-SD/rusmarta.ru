<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\PluginManager,
	\Acrit\Core\Export\Plugin,
	\Acrit\Core\Export\Field\Field,
	\Acrit\Core\Export\Field\ValueBase,
	\Acrit\Core\Export\Filter,
	\Acrit\Core\Cli,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\DiscountRecalculation,
	\Acrit\Core\Teacher,
	\Acrit\Core\Export\Debug;

// Core (part 1)
$strCoreId = 'acrit.core';
$strModuleId = $ModuleID = preg_replace('#^.*?/([a-z0-9]+)_([a-z0-9]+).*?$#', '$1.$2', $_SERVER['REQUEST_URI']);
$strModuleCode = preg_replace('#^(.*?)\.(.*?)$#', '$2', $strModuleId);
$strModuleUnderscore = preg_replace('#^(.*?)\.(.*?)$#', '$1_$2', $strModuleId);
define('ADMIN_MODULE_NAME', $strModuleId);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strModuleId.'/prolog.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strCoreId.'/install/demo.php');
IncludeModuleLangFile(__FILE__);
\CJSCore::Init(['jquery', 'jquery2', 'fileinput']);
$strModuleCodeLower = toLower($strModuleCode);

// Check rights
$strRight = $APPLICATION->getGroupRight($strModuleId);
if($strRight < 'R'){
	$APPLICATION->authForm(Loc::getMessage('ACCESS_DENIED'));
}

// Input data
$obGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList();
$arGet = $obGet->toArray();
$obPost = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList();
$arPost = $obPost->toArray();

// Demo
acritShowDemoExpired($strModuleId);

// Core notice
if(!\Bitrix\Main\Loader::includeModule($strCoreId)){
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
	?><div id="acrit-exp-core-notifier"><?
		print '<div style="margin-top:15px;"></div>';
		print \CAdminMessage::ShowMessage(array(
			'MESSAGE' => Loc::getMessage('ACRIT_EXP_CORE_NOTICE', [
				'#CORE_ID#' => $strCoreId,
				'#LANG#' => LANGUAGE_ID,
			]),
			'HTML' => true,
		));
	?></div><?
	$APPLICATION->SetTitle(Loc::getMessage('ACRIT_EXP_PAGE_TITLE_DEFAULT'));
	die();
}

// Module
\Bitrix\Main\Loader::includeModule($strModuleId);

// Debug
Debug::setModuleId($strModuleId);

// Page title
$strPageTitle = Loc::getMessage('ACRIT_EXP_PAGE_TITLE_ADD');

// CSS
$APPLICATION->setAdditionalCss('/bitrix/js/'.ACRIT_CORE.'/jquery.select2/dist/css/select2.css');
$APPLICATION->setAdditionalCss('/bitrix/js/'.ACRIT_CORE.'/filter/style.css');
Teacher::addCss();

// Get helper data
$arSites = Helper::getSitesList();
$arPlugins = Exporter::getInstance($strModuleId)->findPlugins();
$arPluginsPlain = Exporter::getInstance($strModuleId)->findPlugins(false);
$arPluginTypes = array(
	Plugin::TYPE_NATIVE => Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_NATIVE'),
	Plugin::TYPE_CUSTOM => Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_CUSTOM'),
);

// Core (part 2, visual)
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

// Demo
acritShowDemoNotice($strModuleId);

// Get delay time
$fDelayTime = FloatVal(Helper::getOption($strModuleId, 'time_delay'));
if($fDelayTime<=0){
	$fDelayTime = 0.05;
}
$fDelayTime *= 1000;

// Text definitions for popup
ob_start();
?><script>
var acritExpExportTimeDelay = <?=$fDelayTime?>;
var acritExpModuleVersion = '<?=Helper::getModuleVersion($strModuleId);?>';
var acritExpCoreVersion = '<?=Helper::getModuleVersion($strCoreId);?>';
BX.message({
	// General
	ACRIT_EXP_POPUP_LOADING: '<?=Loc::getMessage('ACRIT_EXP_POPUP_LOADING');?>',
	ACRIT_EXP_POPUP_SAVE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_SAVE');?>',
	ACRIT_EXP_POPUP_CLOSE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CLOSE');?>',
	ACRIT_EXP_POPUP_CANCEL: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CANCEL');?>',
	ACRIT_EXP_POPUP_REFRESH: '<?=Loc::getMessage('ACRIT_EXP_POPUP_REFRESH');?>',
	//
	ACRIT_EXP_IBLOCK_SETTINGS_SAVE_PROGRESS: '<?=Loc::getMessage('ACRIT_EXP_IBLOCK_SETTINGS_SAVE_PROGRESS');?>',
	ACRIT_EXP_IBLOCK_SETTINGS_SAVE_SUCCESS: '<?=Loc::getMessage('ACRIT_EXP_IBLOCK_SETTINGS_SAVE_SUCCESS');?>',
	ACRIT_EXP_IBLOCK_SETTINGS_SAVE_ERROR: '<?=Loc::getMessage('ACRIT_EXP_IBLOCK_SETTINGS_SAVE_ERROR');?>',
	ACRIT_EXP_IBLOCK_SETTINGS_CLEAR_CONFIRM: '<?=Loc::getMessage('ACRIT_EXP_IBLOCK_SETTINGS_CLEAR_CONFIRM');?>',
	// Popup: SelectField
	ACRIT_EXP_POPUP_SELECT_FIELD_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_SELECT_FIELD_TITLE');?>',
	// Popup: ValueSettings
	ACRIT_EXP_POPUP_VALUE_SETTINGS_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_VALUE_SETTINGS_TITLE');?>',
	// Popup: FieldSettings
	ACRIT_EXP_POPUP_FIELD_SETTINGS_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_FIELD_SETTINGS_TITLE');?>',
	// Popup: AdditionalFields
	ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_TITLE');?>',
	// Popup: CategoriesRedefinition
	ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_TITLE');?>',
	ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_ALL: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_ALL');?>',
	ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_CONFIRM: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_CLEAR_CONFIRM');?>',
	// Popup: CategoriesRedefinitionSelect
	ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_SELECT_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORY_REDEFINITION_SELECT_TITLE');?>',
	// Popup: Execute
	ACRIT_EXP_POPUP_EXECUTE_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_TITLE');?>',
	ACRIT_EXP_POPUP_EXECUTE_BUTTON_START: '<?=Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_BUTTON_START');?>',
	ACRIT_EXP_POPUP_EXECUTE_BUTTON_STOP: '<?=Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_BUTTON_STOP');?>',
	ACRIT_EXP_POPUP_EXECUTE_STOPPED: '<?=Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_STOPPED');?>',
	ACRIT_EXP_POPUP_EXECUTE_ERROR: '<?=Loc::getMessage('ACRIT_EXP_POPUP_EXECUTE_ERROR');?>',
	// Popup: IBlocks preview
	ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_TITLE: '<?=Loc::getMessage('ACRIT_EXP_POPUP_IBLOCKS_PREVIEW_TITLE');?>',
	// 
	ACRIT_EXP_ADDITIONAL_FIELD_DELETE_CONFIRM: '<?=Loc::getMessage('ACRIT_EXP_ADDITIONAL_FIELD_DELETE_CONFIRM');?>',
	ACRIT_EXP_ADDITIONAL_FIELDS_DELETE_ALL_CONFIRM: '<?=Loc::getMessage('ACRIT_EXP_ADDITIONAL_FIELDS_DELETE_ALL_CONFIRM');?>',
	// 
	ACRIT_EXP_UPDATE_CATEGORIES_SUCCESS: '<?=Loc::getMessage('ACRIT_EXP_UPDATE_CATEGORIES_SUCCESS');?>',
	ACRIT_EXP_UPDATE_CATEGORIES_ERROR: '<?=Loc::getMessage('ACRIT_EXP_UPDATE_CATEGORIES_ERROR');?>',
	ACRIT_EXP_UPDATE_CATEGORIES_ERROR_NOTE: '<?=Loc::getMessage('ACRIT_EXP_UPDATE_CATEGORIES_ERROR_NOTE');?>',
	//
	ACRIT_EXP_POPUP_CRON_ERROR: '<?=Loc::getMessage('ACRIT_EXP_POPUP_CRON_ERROR');?>',
	ACRIT_EXP_AJAX_AUTH_REQUIRED: '<?=Loc::getMessage('ACRIT_EXP_AJAX_AUTH_REQUIRED');?>',
	ACRIT_EXP_AJAX_CONFIRM_CLEAR_EXPORT_DATA: '<?=Loc::getMessage('ACRIT_EXP_AJAX_CONFIRM_CLEAR_EXPORT_DATA');?>',
	// Get file URL
	ACRIT_EXP_GET_FILE_TITLE: '<?=Helper::getMessage('ACRIT_EXP_GET_FILE_TITLE');?>',
	ACRIT_EXP_GET_FILE_URL_NO_DOMAIN: '<?=Helper::getMessage('ACRIT_EXP_GET_FILE_URL_NO_DOMAIN');?>',
	ACRIT_EXP_GET_FILE_URL_NO_FILENAME: '<?=Helper::getMessage('ACRIT_EXP_GET_FILE_URL_NO_FILENAME');?>'
});
</script><?
\Bitrix\Main\Page\Asset::getInstance()->addString(ob_get_clean(), true, \Bitrix\Main\Page\AssetLocation::AFTER_CSS);

// JS lang
$strSelect2LangFile = Helper::isUtf() ? 'ru_utf8.js' : 'ru_cp1251.js';

// JS
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.cookie.min.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.textchange.min.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.insertatcaret.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.acrit.tabs.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.acrit.filter.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.acrit.popup.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.acrit.hotkey.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/jquery.acrit.togglelistitem.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/filter/script.js');
\Bitrix\Main\Page\Asset::GetInstance()->addJs('/bitrix/js/'.ACRIT_CORE.'/jquery.select2/dist/js/select2.js');
\Bitrix\Main\Page\Asset::GetInstance()->addJs('/bitrix/js/'.ACRIT_CORE.'/jquery.select2/'.$strSelect2LangFile);
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/highlightjs/highlight.pack.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/moment.min.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/cron.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/copy_to_clipboard.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/export/profile_edit.js');
\Bitrix\Main\Page\Asset::GetInstance()->AddJs('/bitrix/js/'.ACRIT_CORE.'/export/profile_edit.hotkeys.js');
Teacher::addJs();
Filter::addJs();

// Get current profile
$intProfileID = IntVal($arGet['ID']);
if($intProfileID>0) {
	$arQuery = [
		'filter' => [
			'ID' => $intProfileID,
		],
		'limit' => 1,
	];
	if(Helper::call($strModuleId, 'Profile', 'getList', [$arQuery])->getSelectedRowsCount() == 0){
		LocalRedirect('/bitrix/admin/acrit_'.$strModuleCodeLower.'_new_list.php?lang='.LANGUAGE_ID);
	}
	$strPageTitle = Loc::getMessage('ACRIT_EXP_PAGE_TITLE_EDIT', array('#ID#' => $intProfileID));
}
$strAdminFormName = 'AcritExpProfile';
$strTabParam = $strAdminFormName.'_active_tab';

// Backup
if($arGet['backup'] == 'Y' && $intProfileID > 0){
	$bBackupSuccess = false;
	#$strTmpFile = Backup::createBackupFile($intProfileID);
	$strTmpFile = Helper::call($strModuleId, 'Backup', 'createBackupFile', [$intProfileID]);
	if(strlen($strTmpFile) && is_file($strTmpFile)) {
		#$strZipFile = Backup::fileToZip($strTmpFile);
		$strZipFile = Helper::call($strModuleId, 'Backup', 'fileToZip', [$strTmpFile]);
		if(is_file($strZipFile)){
			Helper::obRestart();
			#$bBackupSuccess = Backup::downloadFile($strZipFile);
			$bBackupSuccess = Helper::call($strModuleId, 'Backup', 'downloadFile', [$strZipFile]);
			@unlink($strTmpFile);
			@unlink($strZipFile);
			if($bBackupSuccess){
				die();
			}
		}
	}
	if(!$bBackupSuccess) {
		LocalRedirect($APPLICATION->getCurPageParam('', array('backup')));
	}
}

// Copy mode?
$bCopy = $arGet['copy'] == 'Y';
if($bCopy){
	$strPageTitle = Loc::getMessage('ACRIT_EXP_PAGE_TITLE_COPY', array('#ID#' => $intProfileID));
}

// Set page title
$APPLICATION->SetTitle($strPageTitle);

// Deleting current profile?
if($arGet['delete'] == 'Y'){
	Helper::call($strModuleId, 'Profile', 'delete', [$intProfileID]);
	LocalRedirect('/bitrix/admin/acrit_'.$strModuleCodeLower.'_new_list.php?lang='.LANGUAGE_ID);
}

// Get current iblock
$intIBlockID = IntVal($arGet['iblock_id']);
if($intIBlockID==0){
	$intIBlockID = IntVal($arPost['iblock_id']);
}
$intIBlockOffersID = 0;
$intIBlockParentID = 0;
if($intIBlockID>0){
	$arCatalog = Helper::getCatalogArray($intIBlockID);
	if(is_array($arCatalog) && $arCatalog['OFFERS_IBLOCK_ID']>0){
		$intIBlockOffersID = $arCatalog['OFFERS_IBLOCK_ID'];
	}
	elseif(is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID']>0){
		$intIBlockParentID = $arCatalog['PRODUCT_IBLOCK_ID'];
	}
}

// Get helper data
/*
$arSites = Helper::getSitesList();
$arPlugins = Exporter::getInstance($strModuleId)->findPlugins();
$arPluginsPlain = Exporter::getInstance($strModuleId)->findPlugins(false);
$arPluginTypes = array(
	Plugin::TYPE_NATIVE => Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_NATIVE'),
	Plugin::TYPE_CUSTOM => Loc::getMessage('ACRIT_EXP_FIELD_PLUGIN_CUSTOM'),
);
*/

// Add plugins js-array to page content
$arPluginsPrint = $arPlugins;
foreach($arPluginsPrint as &$arPlugin){
	unset($arPlugin['DESCRIPTION'], $arPlugin['EXAMPLE'], $arPlugin['ICON'], $arPlugin['ICON_BASE64']);
	foreach($arPlugin['FORMATS'] as &$arFormat){
		unset($arFormat['DESCRIPTION'], $arFormat['EXAMPLE'], $arFormat['ICON'], $arFormat['ICON_BASE64']);
	}
}
unset($arPlugin, $arFormat);
ob_start();
?><script>
window.acritExpPlugins = <?=\CUtil::PhpToJSObject($arPluginsPrint);?>;
</script><?
\Bitrix\Main\Page\Asset::GetInstance()->AddString(ob_get_clean(), true, \Bitrix\Main\Page\AssetLocation::AFTER_CSS);
unset($arPluginsPrint);

// Current
$arProfilePlugin = false;
$strPluginClass = null;
$obPlugin = null; // plugin || format

// Get current data
$arProfile = array();
if($intProfileID) {
	// Get from db
	#$arProfile = Profile::getProfiles($intProfileID);
	$arProfile = Helper::call($strModuleId, 'Profile', 'getProfiles', [$intProfileID]);
	// Get plugin info
	if(strlen($arProfile['FORMAT'])/* && is_array($arPlugins[$arProfile['PLUGIN']])*/) {
		$arProfilePlugin = Exporter::getInstance($strModuleId)->getPluginInfo($arProfile['FORMAT']);
		if(is_array($arProfilePlugin)){
			$strPluginClass = $arProfilePlugin['CLASS'];
		}
		else {
			print Helper::showError(Loc::getMessage('ACRIT_EXP_ERROR_FORMAT_NOT_FOUND_TITLE'), 
				Loc::getMessage('ACRIT_EXP_ERROR_FORMAT_NOT_FOUND_DETAILS', array(
					'#FORMAT#' => $arProfile['FORMAT'],
				)));
		}
	}
	$APPLICATION->SetTitle($APPLICATION->GetTitle().' &laquo;'.$arProfile['NAME'].'&raquo;');
}
elseif(strlen($arGet['format'])){
	$arProfilePlugin = Exporter::getInstance($strModuleId)->getPluginInfo($arGet['format']);
	if(is_array($arProfilePlugin)){
		$strPluginClass = $arProfilePlugin['CLASS'];
	}
}
if(strlen($strPluginClass) && class_exists($strPluginClass)) {
	$obPlugin = new $strPluginClass($strModuleId);
}

// Check / get default data
$arProfile['ACTIVE'] = in_array($arProfile['ACTIVE'],array('Y','N')) ? $arProfile['ACTIVE'] : 'Y';
$arProfile['SORT'] = is_numeric($arProfile['SORT']) && $arProfile['SORT']>0 ? $arProfile['SORT'] : 100;
$arProfile['SITE_ID'] = isset($arPost['site_id']) ? $arPost['site_id'] : $arProfile['SITE_ID'];
if(!strlen($arProfile['SITE_ID']) || !array_key_exists($arProfile['SITE_ID'], $arSites)) {
	foreach($arSites as $arSite) {
		if($arSite['DEF']=='Y') {
			$arProfile['SITE_ID'] = $arSite['ID'];
			break;
		}
	}
}
if(strlen($arProfile['SITE_ID'])){
	Cli::setSiteId($arProfile['SITE_ID']);
}

// Default site params
if(!$intProfileID) {
	$arProfile['DOMAIN'] = Helper::getCurrentHost();
	$arProfile['IS_HTTPS'] = Helper::isHttps() ? 'Y' : 'N';
}

// Set array of profile
if(is_object($obPlugin)) {
	$obPlugin->setProfileArray($arProfile);
}

// Save form on POST
$bSave = !!strlen($arPost['save']);
$bApply = !!strlen($arPost['apply']);
$bCancel = !!strlen($arPost['cancel']);
if(($bSave || $bApply) && $strRight == 'W'){
	$arProfileFields = $arPost['PROFILE'];
	if(is_array($arPost['PROFILE_del'])){
		$arProfileFields['__delete'] = $arPost['PROFILE_del'];
	}
	if(is_object($obPlugin)) {
		$arProfileFields = array_merge(['ID' => $intProfileID], $arProfileFields);
		$obPlugin->setProfileArray($arProfileFields, true);
	}
	Helper::clearDomain($arProfileFields['DOMAIN']);
	$arProfileFields['PARAMS'] = serialize($arProfileFields['PARAMS']);
	$bCopySuccess = false;
	if($intProfileID && $bCopy) {
		$intNewProfileID = Helper::call($strModuleId, 'Profile', 'copyProfile', [$intProfileID]);
		if($intNewProfileID){
			$intProfileID = $intNewProfileID;
			unset($arProfileFields['ID']);
			$obResult = Helper::call($strModuleId, 'Profile', 'update', [$intProfileID, $arProfileFields]);
			$bCopySuccess = true;
			$strTabParam = $strAdminFormName.'_active_tab';
		}
	}
	elseif($intProfileID && !$bCopy) {
		$obResult = Helper::call($strModuleId, 'Profile', 'update', [$intProfileID, $arProfileFields]);
	}
	else {
		$arProfileFields['DATE_CREATED'] = new \Bitrix\Main\Type\DateTime();
		$obResult = Helper::call($strModuleId, 'Profile', 'add', [$arProfileFields]);
		$intProfileID = $obResult->getID();
	}
	if($bCopySuccess || $obResult->isSuccess()) {
		// Save current iblock data
		if(is_array($arPost['iblock_id'])){
			foreach($arPost['iblock_id'] as $intProfileIBlockID){
				$arIBlockData = array(
					'SECTIONS_ID' => $arPost['iblock_sections_id'][$intProfileIBlockID],
					'SECTIONS_MODE' => $arPost['iblock_sections_mode'][$intProfileIBlockID],
					'FILTER' => $arPost['iblockfilter'][$intProfileIBlockID],
					'PARAMS' => $arPost['iblockparams'][$intProfileIBlockID],
					'FIELDS' => $arPost[ValueBase::INPUTNAME_DEFAULT][$intProfileIBlockID],
				);
				#$bSuccess = Profile::updateIBlockSettings($intProfileID, $intProfileIBlockID, $strPluginClass, $arIBlockData);
				$bSuccess = Helper::call($strModuleId, 'Profile', 'updateIBlockSettings', 
					[$intProfileID, $intProfileIBlockID, $strPluginClass, $arIBlockData]);
				if($bSuccess) {
					// Remove old generated data
					#ExportData::deleteGeneratedData($intProfileID, $intProfileIBlockID);
					Helper::call($strModuleId, 'ExportData', 'deleteGeneratedData', [$intProfileID, $intProfileIBlockID]);
				}
			}
		}
		if(DiscountRecalculation::isEnabled()){
			DiscountRecalculation::checkProperties();
		}
		// Redirect
		if($bApply) {
			$arClearGetParams = array(
				'ID',
				'copy',
				$strTabParam,
			);
			$strTab = strlen($arPost[$strTabParam]) ? '&'.$strTabParam.'='.$arPost[$strTabParam] : '';
			$strUrl = $APPLICATION->getCurPageParam('ID='.$intProfileID.$strTab, $arClearGetParams);
		}
		else {
			$strUrl = '/bitrix/admin/acrit_'.$strModuleCodeLower.'_new_list.php?lang='.LANGUAGE_ID;
		}
		LocalRedirect($strUrl);
	}
	else {
		$arErrors = $obResult->getErrorMessages();
		print Helper::showError(is_array($arErrors) ? implode('<br/>', $arErrors) : $arErrors);
		$arProfile = $arPost['PROFILE'];
	}
}

// Ajax actions
$strAjaxAction = $arGet['ajax_action'];
if(strlen($strAjaxAction)){
	header('Content-Type: application/json; charset='.(Helper::isUtf()?'utf-8':'windows-1251'));
	ini_set('display_errors',0);
	error_reporting(~E_ALL);
	$arJsonResult = array();
	$APPLICATION->RestartBuffer();
	switch($strAjaxAction){
		// get_plugin_info
		case 'get_plugin_info':
			$strAjaxPlugin = $arGet['plugin'];
			$strAjaxFormat = $arGet['format'];
			if(strlen($strAjaxPlugin)) {
				$arJsonResult['PLUGIN'] = false;
				if(is_array($arPlugins[$strAjaxPlugin])){
					$arJsonResult['PLUGIN'] = $arPlugins[$strAjaxPlugin];
					if(strlen($strAjaxFormat)) {
						$arJsonResult['FORMAT'] = false;
						if(is_array($arPlugins[$strAjaxPlugin]['FORMATS'][$strAjaxFormat])) {
							$arJsonResult['FORMAT'] = $arPlugins[$strAjaxPlugin]['FORMATS'][$strAjaxFormat];
						}
					}
				}
			}
			$arJsonResult['PLUGIN_CODE'] = $strAjaxPlugin;
			$arJsonResult['FORMAT_CODE'] = $strAjaxFormat;
			break;
		// load_structure_iblock
		case 'load_structure_iblock':
			ob_start();
			#Profile::update($intProfileID, array(
			#	'LAST_IBLOCK_ID' => $intIBlockID,
			#));
			if($intIBlockID){
				Helper::call($strModuleId, 'Profile', 'update', [$intProfileID, ['LAST_IBLOCK_ID' => $intIBlockID]]);
				Loc::loadMessages(__DIR__.'/include/tabs/structure.php');
				require __DIR__.'/include/tabs/_structure_iblock_all.php';
			}
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// reload profile iblocks
		case 'reload_iblocks':
			ob_start();
			Helper::call($strModuleId, 'Profile', 'setParam', [$intProfileID, ['SHOW_JUST_CATALOGS' => $arGet['show_just_catalogs'] == 'N' ? 'N' : 'Y']]);
			require __DIR__.'/include/tabs/_structure_iblock_select.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// save last settings subtab
		case 'save_last_settings_tab':
			#Profile::update($intProfileID, array(
			#	'LAST_SETTINGS_TAB' => $arGet['tab'],
			#));
			if($strRight == 'W'){
				Helper::call($strModuleId, 'Profile', 'update', [$intProfileID, ['LAST_SETTINGS_TAB' => $arGet['tab']]]);
			}
			break;
		// save iblock (fields, filter, ..)
		case 'iblock_save_data':
			$arJsonResult['SaveSuccess'] = false;
			//
			if(is_array($arPost['iblock_id']) && $strRight == 'W'){
				foreach($arPost['iblock_id'] as $intProfileIBlockID){
					$arIBlockData = array(
						'SECTIONS_ID' => $arPost['iblock_sections_id'][$intProfileIBlockID],
						'SECTIONS_MODE' => $arPost['iblock_sections_mode'][$intProfileIBlockID],
						'FILTER' => $arPost['iblockfilter'][$intProfileIBlockID],
						'PARAMS' => $arPost['iblockparams'][$intProfileIBlockID],
						'FIELDS' => $arPost[ValueBase::INPUTNAME_DEFAULT][$intProfileIBlockID],
					);
					$arJsonResult['X'][] = $arIBlockData;
					if(!Helper::isUtf()){
						$arIBlockData = Helper::convertEncoding($arIBlockData, 'UTF-8', 'CP1251');
					}
					$bSuccess = Helper::call($strModuleId, 'Profile', 'updateIBlockSettings', 
						[$intProfileID, $intProfileIBlockID, $strPluginClass, $arIBlockData]);
					if($bSuccess) {
						$arJsonResult['SaveSuccess'] = true;
						// Remove old generated data
						Helper::call($strModuleId, 'ExportData', 'deleteGeneratedData', [$intProfileID, $intProfileIBlockID]);
						// Save some additional profile params
						$arAllowedProfileParams = [
							'SORT_ORDER',
							'CATEGORIES_REDEFINITION_MODE',
							'CATEGORIES_REDEFINITION_SOURCE', 
							'CATEGORIES_EXPORT_PARENTS',
						];
						$arSaveProfileParams = [];
						if(is_array($arPost['PROFILE']['PARAMS'])){
							foreach($arPost['PROFILE']['PARAMS'] as $key => $value){
								if(in_array($key, $arAllowedProfileParams)){
									$arSaveProfileParams[$key] = $value;
								}
							}
						}
						if(!empty($arSaveProfileParams)){
							Helper::call($strModuleId, 'Profile', 'setParam', [$intProfileID, $arSaveProfileParams]);
						}
					}
				}
				if(DiscountRecalculation::isEnabled()){
					DiscountRecalculation::checkProperties();
				}
			}
			//
			ob_start();
			require __DIR__.'/include/tabs/_structure_iblock_select.php';
			$arJsonResult['IBlocks'] = ob_get_clean();
			$arJsonResult['IBlocksMultipleNotice'] = $strIBlocksMultipleNotice;
			//
			break;
		// remove iblock from settings
		case 'iblock_clear_data':
			if($intIBlockID>0 && $strRight == 'W'){
				Helper::call($strModuleId, 'Profile', 'removeIBlockSettings', [$intProfileID, $intIBlockID]);
			}
			//
			ob_start();
			require __DIR__.'/include/tabs/_structure_iblock_select.php';
			$arJsonResult['IBlocks'] = ob_get_clean();
			$arJsonResult['IBlocksMultipleNotice'] = $strIBlocksMultipleNotice;
			//
			break;
		// change field type of selected field
		case 'change_field_type':
			$strField = htmlspecialcharsbx($arGet['field']);
			$strType = htmlspecialcharsbx($arGet['type']);
			ob_start();
			if(is_object($obPlugin)) {
				$arFieldsAll = $obPlugin->getFields($intProfileID, $intIBlockID);
				foreach($arFieldsAll as $obField){
					if($obField->getCode() == $strField){
						$obField->setModuleId($strModuleId);
						#$arSavedField = ProfileField::loadSavedFields($intProfileID, $intIBlockID, $strField);
						$arSavedField = Helper::call($strModuleId, 'ProfileField', 'loadSavedFields', [$intProfileID, $intIBlockID, $strField]);
						$obField->setProfileID($intProfileID);
						$obField->setIBlockID($intIBlockID);
						$obField->setConditions($arSavedField['CONDITIONS']);
						if(is_array($arSavedField) && $arSavedField['TYPE']==$strType) { // load saved values only if same type
							#$arSavedValue = ProfileValueTable::loadFieldValuesAll($intProfileID, $intIBlockID, $strField);
							$arSavedValue = Helper::call($strModuleId, 'ProfileValueTable', 'loadFieldValuesAll', [$intProfileID, $intIBlockID, $strField]);
							$obField->setValue($arSavedValue);
						}
						//
						$obField->setType($strType);
						print $obField->displayField();
					}
				}
			}
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// load popup SelectField
		case 'load_popup_select_field':
			ob_start();
			require __DIR__.'/include/popups/select_field.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// load conditions popup (3-in-1: field, logic, value)
		case 'load_popup_conditions':
			$strPopupType = $arPost['entity'];
			ob_start();
			switch($strPopupType){
				case 'field':
					require __DIR__.'/include/popups/conditions_field.php';
					break;
				case 'logic':
					require __DIR__.'/include/popups/conditions_logic.php';
					break;
				case 'value':
					require __DIR__.'/include/popups/conditions_value.php';
					break;
			}
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// load popup ValueSettins
		case 'load_popup_value_settings':
			ob_start();
			require __DIR__.'/include/popups/value_settings.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// load popup FieldSettins
		case 'load_popup_field_settings':
			ob_start();
			require __DIR__.'/include/popups/field_settings.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// custom plugin actions
		case 'plugin_ajax_action':
			$strAction = $arGet['action'];
			if(is_object($obPlugin)) {
				$obPlugin->ajaxAction($strAction, array(
					'PROFILE_ID' => $intProfileID,
					'IBLOCK_ID' => $intIBlockID,
					'IBLOCK_OFFERS_ID' => $intIBlockOffersID,
					'GET' => $arGet,
					'POST' => $arPost,
					'PLUGINS' => $arPlugins,
				), $arJsonResult);
			}
			break;
		// Add additional field [SINGLE]
		case 'add_additional_field':
			$arJsonResult['Success'] = false;
			if($strRight == 'W'){
				#$mResult = AdditionalField::addNewAndGetHtml($intProfileID, $intIBlockID);
				$mResult = Helper::call($strModuleId, 'AdditionalField', 'addNewAndGetHtml', [$intProfileID, $intIBlockID]);
				if($mResult!==false && strlen($mResult)){
					$arJsonResult['HTML'] = $mResult;
					$arJsonResult['Success'] = true;
				}
			}
			break;
		// Delete additional field [SINGLE]
		case 'delete_additional_field':
			$arJsonResult['Success'] = false;
			if($strRight == 'W'){
				$intAdditionalFieldID = IntVal($arGet['field_id']);
				if($intAdditionalFieldID>0) {
					#AdditionalField::delete($intAdditionalFieldID);
					Helper::call($strModuleId, 'AdditionalField', 'delete', [$intAdditionalFieldID]);
					#$arJsonResult['Field'] = AdditionalField::getFieldCode($intAdditionalFieldID);
					$arJsonResult['Field'] = Helper::call($strModuleId, 'AdditionalField', 'getFieldCode', [$intAdditionalFieldID]);
					$arJsonResult['Success'] = true;
				}
			}
			break;
		// Show props for additional fields
		case 'show_props_for_additional_fields':
			ob_start();
			require __DIR__.'/include/popups/props_for_additional_fields.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Add additional fields
		case 'add_additional_fields':
			$arJsonResult['Success'] = false;
			if($strRight == 'W'){
				$arPropsID = explode(',', $arGet['props']);
				Helper::arrayRemoveEmptyValues($arPropsID);
				if(!empty($arPropsID)){
					$arRowsHtml = array();
					foreach($arPropsID as $intPropertyId){
						$mResult = Helper::call($strModuleId, 'AdditionalField', 'addNewAndGetHtml', [$intProfileID, $intIBlockID, $intPropertyId]);
						if($mResult!==false && strlen($mResult)){
							$arRowsHtml[] = $mResult;
							$arJsonResult['Success'] = true;
						}
					}
					if($arJsonResult['Success']){
						$arJsonResult['HTML'] = implode('', $arRowsHtml);
					}
				}
			}
			break;
		// Delete all additional fields [MULTIPLE]
		case 'delete_additional_fields_all':
			$arJsonResult['Success'] = false;
			if($strRight == 'W'){
				#AdditionalField::deleteAll($intProfileID, $intIBlockID);
				Helper::call($strModuleId, 'AdditionalField', 'deleteAll', [$intProfileID, $intIBlockID]);
				$arJsonResult['Success'] = true;
			}
			break;
		// Categories redefinition [SHOW]
		case 'categories_redefinition_show':
			ob_start();
			require __DIR__.'/include/popups/categories_redefinition.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Categories redefinition [SAVE]
		case 'categories_redefinition_save':
			$arJsonResult['Success'] = false;
			if($strRight == 'W'){
				$arJsonResult['Success'] = true;
				$bSaveRedefinition = true;
				require __DIR__.'/include/popups/categories_redefinition.php';
			}
			break;
		// Categories redefinition [SELECT]
		case 'categories_redefinition_select':
			$arJsonResult['Success'] = true;
			ob_start();
			require __DIR__.'/include/popups/categories_redefinition_select.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Load popup 'Execute'
		case 'load_popup_execute':
			$arJsonResult['Success'] = true;
			ob_start();
			require __DIR__.'/include/popups/execute.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Export execute
		case 'export_execute':
			$bExecute = true;
			ob_start();
			require __DIR__.'/include/popups/execute.php';
			$arJsonResult['HTML'] = ob_get_clean();
			#
			$arJsonResult['LockedHtml'] = '';
			#if(Profile::isLocked($intProfileID)){
			if(Helper::call($strModuleId, 'Profile', 'isLocked', [$intProfileID])){
				#$obDateLocked = Profile::getDateLocked($intProfileID);
				$obDateLocked = Helper::call($strModuleId, 'Profile', 'getDateLocked', [$intProfileID]);
				$arJsonResult['LockedHtml'] .= '<div style="margin-top:15px;"></div>';
				$arJsonResult['LockedHtml'] .= Helper::showNote(Loc::getMessage('ACRIT_EXP_LOCK_NOTICE', array(
					'#DATE#' => is_object($obDateLocked) ? $obDateLocked->toString() : '???',
				)), true, false, true).'<br/>';
			}
			break;
		// Load plugin settings
		case 'load_plugin_settings':
			$arJsonResult['HTML'] = '';
			$arJsonResult['DESCRIPTION'] = '';
			$arJsonResult['EXAMPLE'] = '';
			$arPlugin = Exporter::getInstance($strModuleId)->getPluginInfo($arGet['format']);
			if(is_array($arPlugin) && is_array($arPluginsPlain[$arPlugin['CODE']])){
				$obPlugin = new $arPluginsPlain[$arPlugin['CODE']]['CLASS']($strModuleId);
				# Auto set default filename
				$arSavedPlugin = Exporter::getInstance($strModuleId)->getPluginInfo($arProfile['FORMAT']);
				if($arPlugin!=$arSavedPlugin && method_exists($obPlugin, 'getDefaultExportFilename')){
					$strExportFilenameDefault = $obPlugin->getDefaultExportFilename();
					if(strlen($strExportFilenameDefault)){
						$strDirectory = $obPlugin->getDefaultDirectory();
					}
					$strExportFilename = $strDirectory.'/'.$strExportFilenameDefault;
					$intFilenameIndex = 0;
					while(true){
						$intFilenameIndex++;
						$strExportFilename = Helper::getFileNameWithIndex($strExportFilename, $intFilenameIndex);
						if(!is_file($_SERVER['DOCUMENT_ROOT'].$strExportFilename)){
							break;
						}
					}
					$arProfile['PARAMS']['EXPORT_FILE_NAME'] = $strExportFilename;
				}
				#
				$obPlugin->setProfileArray($arProfile);
				if(is_array($arPost['PROFILE'])){
					if(isset($arPost['PROFILE']['EXPORT_FILE_NAME']) && !strlen($arPost['PROFILE']['EXPORT_FILE_NAME'])) {
						unset($arPost['PROFILE']['EXPORT_FILE_NAME']);
					}
					$arProfileTmp = array_merge($arProfile, $arPost['PROFILE']);
					$obPlugin->setProfileArray($arProfileTmp);
				}
			}
			if(is_object($obPlugin)) {
				$obPlugin->setAjaxLoadSettings(true);
				$arJsonResult['HTML'] =
					$obPlugin->includeCss().
					$obPlugin->includeJs().
					$obPlugin->showSettings();
				$arJsonResult['DESCRIPTION'] = $obPlugin::getDescription();
				$arJsonResult['EXAMPLE'] = $obPlugin::getExample();
			}
			break;
		// Load popup 'Cron'
		case 'load_popup_cron':
			ob_start();
			require __DIR__.'/include/popups/cron.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Setup crontab task
		case 'cron_setup':
			$bAdd = $arGet['cron_action']=='add';
			$strSchedule = $arGet['schedule'];
			$strScriptName = 'export.php';
			#
			$arCli = Cli::getFullCommand($strModuleId, $strScriptName, $intProfileID, Log::getInstance($strModuleId)->getLogFilename($intProfileID));
			#
			if($strRight == 'W'){
				Cli::deleteCronTask($strModuleId, $arCli['COMMAND_CLEAR']);
				if($bAdd) {
					$bResult = Cli::addCronTask($strModuleId, $arCli['COMMAND'], $strSchedule);
					if($bResult){
						Log::getInstance($strModuleId)->add(Loc::getMessage('ACRIT_EXP_AJAX_CRON_SETUP_SUCCESS', array(
							'#COMMAND#' => $arCli['COMMAND'],
						)), $intProfileID);
					}
					else{
						Log::getInstance($strModuleId)->add(Loc::getMessage('ACRIT_EXP_AJAX_CRON_SETUP_ERROR', array(
							'#COMMAND#' => $arCli['COMMAND'],
						)), $intProfileID);
					}
				}
				else{
					Log::getInstance($strModuleId)->add(Loc::getMessage('ACRIT_EXP_AJAX_CRON_DELETE_SUCCESS'), $intProfileID);
				}
			}
			#$arJsonResult['IsConfigured'] = Cli::isCronTaskConfigured($strModuleId, $arCli['COMMAND_CLEAR'], $strSchedule);
			$arJsonResult['IsConfigured'] = Cli::isProfileOnCron($strModuleId, $intProfileID, $strScriptName, null, true);
			#
			ob_start();
			require __DIR__.'/include/tabs/_cron_tasks.php';
			$arJsonResult['CronTasksHtml'] = ob_get_clean();
			break;
		// Cron: set one-time
		case 'cron_set_one_time':
			#$obResult = Profile::update($intProfileID, array(
			#	'ONE_TIME' => $arPost['one_time'] == 'Y' ? 'Y' : 'N',
			#));
			$arJsonResult['Success'] = false;
			if($strRight == 'W'){
				$obResult = Helper::call($strModuleId, 'Profile', 'update', [$intProfileID, [
					'ONE_TIME' => $arPost['one_time'] == 'Y' ? 'Y' : 'N',
				]]);
				$arJsonResult['Success'] = $obResult->isSuccess();
				if($arJsonResult['Success']){
					$arJsonResult['SuccessMessage'] = '<span style="color:green;font-weight:bold;">'
						.Loc::getMessage('ACRIT_EXP_CHANGES_SAVED').'</span>';
				}
				else{
					$arJsonResult['SuccessMessage'] = '<span style="color:red;font-weight:bold;">Error!</span>';
				}
			}
			break;
		// Unlock profile
		case 'profile_unlock':
			$arJsonResult['HTML'] = '';
			$arJsonResult['Success'] = false;
			#$obResult = Profile::unlock($intProfileID);
			$obResult = Helper::call($strModuleId, 'Profile', 'unlock', [$intProfileID]);
			#Profile::clearSession($intProfileID);
			Helper::call($strModuleId, 'Profile', 'clearSession', [$intProfileID]);
			if($obResult->isSuccess()){
				$arJsonResult['Success'] = true;
				$arSession = unserialize($arProfile['SESSION']);
				$arSession = is_array($arSession) ? $arSession : array();
				if($arSession['HISTORY_ID'] > 0){
					#$obResult = History::update($arSession['HISTORY_ID'], array('STOPPED' => 'Y'));
					$obResult = Helper::call($strModuleId, 'History', 'update', [$arSession['HISTORY_ID'], array('STOPPED' => 'Y')]);
					$arJsonResult['Success'] = $obResult->isSuccess();
				}
			}
			break;
		// Update categories
		case 'categories_update':
			$arJsonResult['Success'] = false;
			$bContinue = \Bitrix\Main\Loader::includeSharewareModule($strModuleId) == \Bitrix\Main\Loader::MODULE_DEMO;
			if(!$bContinue){
				\Acrit\Core\Update::checkModuleUpdates($strModuleId, $intDateTo, $strLastVersion);
				if($intDateTo){
					$bContinue = $intDateTo > time();
				}
			}
			if($bContinue) {
				if(is_object($obPlugin)){
					$arAdditionalData = array(
						'MODE' => $arPost['iblock_sections_mode'],
						'ID' => $arPost['iblock_sections_id'],
						'PARAMS' => $arPost['PROFILE']['PARAMS'],
					);
					if($obPlugin->updateCategories($intProfileID, $arAdditionalData)) {
						$arJsonResult['Success'] = true;
						$arJsonResult['Date'] = Helper::formatUnixDatetime($obPlugin->getCategoriesDate());
					}
				}
			}
			else{
				$arJsonResult['Message'] = 	Helper::showNote(Helper::getMessage('ACRIT_EXP_UPDATE_CATEGORIES_UNAVAILABLE', array(
					'#DATE#' => date(\CDatabase::DateFormatToPHP(FORMAT_DATE), $intDateTo),
					'#LINK#' => Helper::getRenewUrl($strModuleId),
				)), true, false, true);
			}
			break;
		// Log: refresh
		case 'log_refresh':
			$arJsonResult['Success'] = false;
			// Profile log
			$strLogPreview = Log::getInstance($strModuleId)->getLogPreview($intProfileID);
			if(strlen($strLogPreview)){
				$arJsonResult['Success'] = true;
				$arJsonResult['Log'] = $strLogPreview;
			}
			$arJsonResult['LogSize'] = Log::getInstance($strModuleId)->getLogSize($intProfileID, true);
			if(is_object($obPlugin)){
				$arJsonResult['ExportFilename'] = $obPlugin->showFileOpenLink(false, true);
			}
			break;
		// Log: clear
		case 'log_clear':
			$arJsonResult['Success'] = true;
			Log::getInstance($strModuleId)->deleteLog($intProfileID);
			break;
		// Popup: iblocks preview
		case 'iblocks_preview':
			ob_start();
			require __DIR__.'/include/popups/iblocks_preview.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// History refresh
		case 'history_refresh':
			ob_start();
			require __DIR__.'/include/tabs/_log_history.php';
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Get profile name index
		case 'get_profile_name_index':
			$strName = $arPost['profile_name'];
			if(!Helper::isUtf()){
				$strName = Helper::convertEncoding($strName, 'UTF-8', 'CP1251');
			}
			$strName = preg_replace('#\s?\(\d+\)$#', '', $strName);
			$arExistNames = array();
			$arProfiles = Helper::call($strModuleId, 'Profile', 'getProfiles', [[], [], false, false, ['ID', 'NAME']]);
			foreach($arProfiles as $arProfile){
				$arExistNames[] = $arProfile['NAME'];
			}
			$intNameIndex = 0;
			while(true){
				$intNameIndex++;
				$strNameNew = $strName.($intNameIndex > 1 ? ' ('.$intNameIndex.')' : '');
				if(!in_array($strNameNew, $arExistNames)){
					break;
				}
			}
			$arJsonResult['NAME'] = $strNameNew;
			break;
		// Hide main notice
		case 'main_notice_hide':
			$arJsonResult['Success'] = true;
			\CUserOptions::SetOption($strModuleId, 'main_notice_hidden', 'Y');
			break;
		// Step export reset
		case 'step_export_reset':
			$obResult = Helper::call($strModuleId, 'Profile', 'update', [$intProfileID, [
				'LAST_EXPORTED_ITEM' => null,
				'_QUIET' => 'Y',
			]]);
			$arJsonResult['Success'] = $obResult->isSuccess();
			break;
		// Check lock
		case 'check_lock':
			ob_start();
			#if(Profile::isLocked($arProfile)){
			if(Helper::call($strModuleId, 'Profile', 'isLocked', [$arProfile])){
				print '<div style="margin-top:15px;"></div>';
				print Helper::showNote(Loc::getMessage('ACRIT_EXP_LOCK_NOTICE', array(
					'#DATE#' => is_object($arProfile['DATE_LOCKED']) ? $arProfile['DATE_LOCKED']->toString() : '???',
				)), true);
			}
			$arJsonResult['HTML'] = ob_get_clean();
			break;
		// Execute console command
		case 'console_execute':
			$strConsoleCommand = $arPost['command'];
			$strConsoleHeight = $arPost['height'];
			$strConsoleText = $arPost['text'];
			\CUserOptions::SetOption($strModuleId, 'console_command', base64_encode($strConsoleCommand));
			\CUserOptions::SetOption($strModuleId, 'console_height', $strConsoleHeight);
			\CUserOptions::SetOption($strModuleId, 'console_text', $strConsoleText);
			$arJsonResult['HTML'] = false;
			if($USER->isAdmin()){
				$fTime = microtime(true);
				$arClasses = array(
					'\Bitrix\Main\Localization\Loc',
					'\Bitrix\Main\EventManager',
					'\Bitrix\Main\Application',
					'\Bitrix\Main\Config\Option',
				);
				$arCoreAutoload = &$GLOBALS['ACRIT_CORE_AUTOLOAD_CLASSES'];
				#$arModuleAutoload = &$GLOBALS['ACRIT_'.toUpper($strModuleCode).'_AUTOLOAD_CLASSES'];
				foreach($arCoreAutoload as $strClass => $strClassDir){
					$arClass = explode('\\', $strClass);
					if(count($arClass) > 3 && toLower($arClass[2]) != 'export'){
						continue;
					}
					$strClassBasename = array_pop($arClass); 
					$strAs = '';
					if(substr($strClassBasename, -5) === 'Table'){
						$strAs = substr($strClassBasename, 0, -5);
					}
					elseif($strClassBasename === 'Base'){
						$strAs = end($arClass).$strClassBasename;
					}
					$arClass[] = $strClassBasename;
					$strClass = '\\'.implode('\\', $arClass);
					$arClasses[] = $strClass.(strlen($strAs) ? ' as '.$strAs : '');
				}
				$arModules = array('iblock', 'catalog', 'sale', 'currency');
				foreach($arModules as $strModule){
					\Bitrix\Main\Loader::includeModule($strModule);
				}
				$strCommand = 'use '.implode(', '.PHP_EOL."    ", $arClasses).';'.PHP_EOL;
				$arGlobals = array(
					'$DB',
					'$DBType',
					'$DBHost',
					'$DBLogin',
					'$DBPassword',
					'$DBName',
					'$DBDebug',
					'$DBDebugToFile',
					'$USER',
					'$APPLICATION',
					'$intProfileID',
					'$intIBlockID',
					'$intIBlockOffersID',
					'$intIBlockParentID',
					'$arCatalog',
					'$arSites',
					'$arPlugins',
					'$arPluginsPlain',
					'$arPluginTypes',
					'$arProfilePlugin',
					'$strPluginClass',
					'$arGet',
					'$arPost',
					'$arProfile',
				);
				$strCommand .= 'global '.implode(', ', $arGlobals).';'.PHP_EOL;
				$strCommand .= $strConsoleCommand;
				Debug::$arData['PROFILE_ID'] = $intProfileID;
				Debug::$arData['IBLOCK_ID'] = $intIBlockID;
				Debug::$arData['OFFERS_IBLOCK_ID'] = $intIBlockOffersID;
				Debug::$arData['PLUGIN'] = $obPlugin;
				Debug::$arData['CATALOG'] = $arCatalog;
				ini_set('display_errors',1);
				error_reporting(E_ALL^E_NOTICE^E_STRICT^E_DEPRECATED);
				$strContent = $obPlugin->executeConsole($strCommand);
				if($strConsoleText=='Y'){
					ob_start();
					Helper::P($strContent);
					$strContent = ob_get_clean();
				}
				if(trim($strContent) == ''){
					$strContent = '<br/>';
				}
				$strContent = '<hr/>'.$strContent.'<hr/>';
				$strContent .= Loc::getMessage('ACRIT_EXP_AJAX_CONSOLE_TIME', array(
					'#TIME#' => number_format(microtime(true)-$fTime, 6, '.', ''),
				));
				$arJsonResult['HTML'] = $strContent;
			}
			else{
				$arJsonResult['AccessDenied'] = Loc::getMessage('ACRIT_EXP_ERROR_CONSOLE_ACCESS_DENIED');
			}
			break;
		// Clear profile export data
		case 'clear_profile_export_data':
			#ExportData::deleteGeneratedData($intProfileID);
			Helper::call($strModuleId, 'ExportData', 'deleteGeneratedData', [$intProfileID]);
			$arJsonResult['Success'] = true;
			break;
		// Run in background
		case 'run_in_background':
			$arJsonResult['Success'] = false;
			$arJsonResult['LockedHtml'] = '';
			$strError = '';
			if(Cli::isProcOpen()){
				if($arProfile['ACTIVE'] == 'Y') {
					#if(!Profile::isLocked($arProfile)){
					if(!Helper::call($strModuleId, 'Profile', 'isLocked', [$arProfile])){
						$arJsonResult['Pid'] = Exporter::run($strModuleId, $intProfileID);
						if($arJsonResult['Pid'] > 0) {
							$arJsonResult['Success'] = true;
							$arJsonResult['SuccessMessage'] = Loc::getMessage('ACRIT_EXP_RUN_BACKGROUND_SUCCESS', array(
								'#PID#' => $arJsonResult['Pid'],
							));
							#if(Profile::isLocked($intProfileID)){
							if(Helper::call($strModuleId, 'Profile', 'isLocked', [$intProfileID])){
								#$obDateLocked = Profile::getDateLocked($intProfileID);
								$obDateLocked = Helper::call($strModuleId, 'Profile', 'getDateLocked', [$intProfileID]);
								$arJsonResult['LockedHtml'] .= '<div style="margin-top:15px;"></div>';
								$arJsonResult['LockedHtml'] .= Helper::showNote(Loc::getMessage('ACRIT_EXP_LOCK_NOTICE', array(
									'#DATE#' => is_object($obDateLocked) ? $obDateLocked->toString() : '???',
								)), true, false, true).'<br/>';
							}
						}
						else{
							$strError = Loc::getMessage('ACRIT_EXP_RUN_BACKGROUND_ERROR');
						}
					}
					else{
						$strError = Loc::getMessage('ACRIT_EXP_RUN_BACKGROUND_BLOCKED');
					}
				}
				else {
					$strError = Loc::getMessage('ACRIT_EXP_RUN_BACKGROUND_INACTIVE');
				}
			}
			else{
				$strError = Loc::getMessage('ACRIT_EXP_RUN_BACKGROUND_DISABLED');
			}
			if(!$arJsonResult['Success']){
				$arJsonResult['ErrorMessage'] = $strError;
			}
			break;
		//
		case 'add_multicondition_item':
			$arTypes = Field::getValueTypesStatic($strModuleId);
			$obValue = new $arTypes['MULTICONDITION']['CLASS'];
			$strField = htmlspecialcharsbx($arPost['field_code']);
			$obValue->setFieldCode($strField);
			$arFieldsAll = $obPlugin->getFields($intProfileID, $intIBlockID);
			foreach($arFieldsAll as $obField){
				if($obField->getCode() == $strField){
					$obField->setModuleId($strModuleId);
					$obValue->setFieldObject($obField);
				}
			}
			$strSuffix = call_user_func(array($arTypes['MULTICONDITION']['CLASS'], 'getRandomSuffix'));
			$arJsonResult['IBLOCK_ID'] = $intIBlockID;
			$obValue->setIBlockID($intIBlockID);
			$obValue->setValueSuffix($strSuffix);
			$obValue->setConditions($strConditions='');
			$obValue->setSiteID($arProfile['SITE_ID']);
			$arJsonResult['HTML'] = $obValue->showAddCondition($strSuffix, array());
			unset($obValue);
			break;
		//
		case 'find_first_element':
			$arFilter = Helper::call($strModuleId, 'Profile', 'getFilter', [$intProfileID, $arGet['iblock_id']]);
			$bFound = false;
			if(!empty($arFilter)){
				$arSelect = [
					'ID',
					'IBLOCK_ID',
					'IBLOCK_TYPE_ID',
					'IBLOCK_SECTION_ID',
				];
				$resItem = \CIBlockElement::getList(['ID' => 'ASC'], $arFilter, false, false, $arSelect);
				if($arItem = $resItem->getNext()){
					$bFound = true;
					$arItem['_URL'] = Exporter::getInstance($strModuleId)->getElementPreviewUrl($arItem['ID'], $intProfileID);
					$arJsonResult['FirstElement'] = $arItem;
				}
			}
			if(!$bFound){
				$arJsonResult['ErrorMessage'] = Loc::getMessage('ACRIT_EXP_ERROR_FIRST_ELEMENT_IS_NOT_FOUND');
			}
			break;
		// check_export_filename_unique
		case 'check_export_filename_unique':
			$arJsonResult['Unique'] = true;
			$arJsonResult['UniqueMessage'] = null;
			$arJsonResult['ProfileID'] = null;
			$strExportFilename = toLower($arGet['export_filename']);
			if(strlen($strExportFilename)){
				$arFilter = [
					'!ID' => $intProfileID,
				];
				$arProfiles = Helper::call($strModuleId, 'Profile', 'getProfiles', [$arFilter, [], false, false]);
				if(is_array($arProfiles)){
					foreach($arProfiles as $arProfile){
						$strExportFileName = $arProfile['PARAMS']['EXPORT_FILE_NAME'];
						if(is_string($strExportFileName) && strlen($strExportFileName)){
							if(toLower($strExportFileName) == $strExportFilename){
								$arJsonResult['Unique'] = false;
								$arJsonResult['UniqueMessage'] = Loc::getMessage('ACRIT_EXP_ERROR_FILENAME_IS_NOT_UNIQUE', [
									'#ID#' => $arProfile['ID'],
									'#FILENAME#' => $arGet['export_filename'],
								]);
							}
						}
					}
				}
			}
			break;
		// cron_get_command
		case 'cron_get_command':
			$arCli = Cli::getFullCommand($strModuleId, 'export.php', $intProfileID, Log::getInstance($strModuleId)->getLogFilename($intProfileID));
			$arJsonResult['Command'] = $arCli['COMMAND'];
			$arJsonResult['CommandNoOutput'] = $arCli['COMMAND_NO_OUTPUT'];
			break;
	}
	$arJsonResult['ServerTime'] = date('r');
	print Json::encode($arJsonResult);
	ini_set('display_errors', 0); // Against 'Warning:  A non-numeric value encountered in /home/bitrix/www/bitrix/modules/perfmon/classes/general/keeper.php on line 321'
	die();
}

// Check database
Helper::checkDatabase($strModuleId);

// Teachers
$arTeachers = [
	Plugin::TEACHER_DEFAULT => Plugin::getDefaultTeacher(),
];
if(is_object($obPlugin)){
	$obPlugin->modifyDefaultTeacher($arTeachers[Plugin::TEACHER_DEFAULT]);
}
if(is_object($obPlugin)){
	$obPlugin->addTeachers($arTeachers);
}
foreach($arTeachers as $strKey => $arTeacher){
	Teacher::getTeacherJs($arTeachers[$strKey]);
}

// Context menu
$arMenu = array();
$arMenu[] = array(
	'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_LIST'),
	'LINK' => 'acrit_'.$strModuleCodeLower.'_new_list.php?lang='.LANGUAGE_ID,
	'ICON' => 'btn_list',
);
if(!empty($arTeachers)){
	$arJs = ['window.acritTeachers = {};'];
	$arHelpMenu = [];
	foreach($arTeachers as $arTeacher){
		$strId = randString(16);
		$arJs[] = sprintf('window.acritTeachers["%s"] = %s;', $strId, $arTeacher['JS']);
		$arHelpMenu[] = array(
			'TEXT'	=> $arTeacher['NAME'],
			'ONCLICK' => sprintf('$("#adm-workarea").acritTeacher(window.acritTeachers["%s"])', $strId),
		);
	}
	$arHelpMenu[] = [
		'SEPARATOR' => true,
	];
	// $arHelpMenu[] = [
	// 	'TEXT' => Loc::getMessage('ACRIT_EXP_MENU_COURSES'),
	// 	'ONCLICK' => 'window.open("acrit_'.$strModuleCodeLower.'_new_course.php?lang='.LANGUAGE_ID.'");',
	// ];
	// $arHelpMenu[] = [
	// 	'SEPARATOR' => true,
	// ];
	// $arHelpMenu[] = [
	// 	'TEXT' => Loc::getMessage('ACRIT_EXP_MENU_COURSE'),
	// 	'ONCLICK' => 'window.open("https://sale.maed.ru/marketplace-manager/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=marketplace_web_--acrit&utm_content=text_ad");',
	// ];
	$arMenu[] = array(
		'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_HELP'),
		'MENU' => $arHelpMenu,
	);
	$strJsReady = sprintf('$(document).ready(function(){%s%s%s});', PHP_EOL, implode("\n", $arJs), PHP_EOL);
	$strJs = sprintf('<script>%s</script>', $strJsReady);
	\Bitrix\Main\Page\Asset::getInstance()->addString($strJs, false, \Bitrix\Main\Page\AssetLocation::AFTER_JS);
}
if($intProfileID) {
	if(!$bCopy){
		$arMenu[] = array(
			'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_EXPORT_PREVIEW'),
			'ONCLICK' => '$("input[data-role=\"preview-iblocks\"]").first().trigger("click");',
			'ICON' => 'acrit-exp-export-preview',
		);
		if(is_array($arProfile['PARAMS']) && array_key_exists('EXPORT_FILE_NAME', $arProfile['PARAMS'])){
			$arMenu[] = array(
				'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_GET_FILE_URL'),
				'ONCLICK' => 'acritExpGetFileUrl();',
				'ICON' => 'acrit-exp-get-file-url',
			);
		}
	}
	#
	$arActionsMenu = array();
	$arActionsMenu[] = array(
		'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_ADD'),
		'LINK' => 'acrit_'.$strModuleCodeLower.'_new_edit.php?lang='.LANGUAGE_ID,
		'ICON' => 'edit',
	);
	if(!$bCopy){
		$arActionsMenu[] = array(
			'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_COPY'),
			'LINK' => 'acrit_'.$strModuleCodeLower.'_new_edit.php?ID='.$intProfileID.'&copy=Y&lang='.LANGUAGE_ID,
			'ICON' => 'copy',
		);
		$strDeleteUrl = 'acrit_'.$strModuleCodeLower.'_new_edit.php?ID='.$intProfileID.'&delete=Y&lang='.LANGUAGE_ID;
		$arActionsMenu[] = array(
			'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_DELETE'),
			'ICON' => 'delete',
			'ACTION' => "if(confirm('".Loc::GetMessage("ACRIT_EXP_MENU_DELETE_CONFIRM")."')){window.location='".$strDeleteUrl."';}",
		);
		$arActionsMenu[] = array(
			'SEPARATOR' => true,
		);
		$strBackupUrl = 'acrit_'.$strModuleCodeLower.'_new_edit.php?ID='.$intProfileID.'&backup=Y&lang='.LANGUAGE_ID;
		$APPLICATION->addHeadString('<script>var acritExpProfileBackupUrl = "'.$strBackupUrl.'";</script>');
		$arActionsMenu[] = array(
			'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_BACKUP'),
			'ICON' => 'pack',
			'LINK' => $strBackupUrl,
		);
	}
	$arMenu[] = array(
		'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_ACTIONS'),
		'ICON' => 'btn_new',
		'MENU' => $arActionsMenu,
	);
	if(!$bCopy){
		$arMenu[] = array(
			'TEXT'	=> Loc::getMessage('ACRIT_EXP_MENU_RUN'),
			'ICON' => 'acrit-exp-button-run',
			'ONCLICK' => 'AcritExpPopupExecute.Open();',
		);
	}
}
$context = new \CAdminContextMenu($arMenu);
$context->Show();

// Tab control
$arTabs = array();
$strTabsDir = '/include/tabs';
$arTabs[] = array(
	'DIV' => 'general',
	'TAB' => Loc::getMessage('ACRIT_EXP_TAB_GENERAL_NAME'),
	'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_GENERAL_DESC'),
	'SORT' => 1,
	'FILE' => __DIR__.$strTabsDir.'/general.php',
);
if($intProfileID && is_object($obPlugin)){
	$arTabs[] = array(
		'DIV' => 'structure',
		'TAB' => Loc::getMessage('ACRIT_EXP_TAB_STRUCTURE_NAME'),
		'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_STRUCTURE_DESC'),
		'SORT' => 10,
		'FILE' => __DIR__.$strTabsDir.'/structure.php',
	);
	if(\Bitrix\Main\Loader::includeModule('currency')) {
		$arTabs[] = array(
			'DIV' => 'currency',
			'TAB' => Loc::getMessage('ACRIT_EXP_TAB_CURRENCY_NAME'),
			'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_CURRENCY_DESC'),
			'SORT' => 20,
			'FILE' => __DIR__.$strTabsDir.'/currency.php',
		);
	}
	if(!$bCopy){
		$arTabs[] = array(
			'DIV' => 'cron',
			'TAB' => Loc::getMessage('ACRIT_EXP_TAB_CRON_NAME'),
			'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_CRON_DESC'),
			'SORT' => 30,
			'FILE' => __DIR__.$strTabsDir.'/cron.php',
		);
		$arTabs[] = array(
			'DIV' => 'log',
			'TAB' => Loc::getMessage('ACRIT_EXP_TAB_LOG_NAME'),
			'TITLE' => Loc::getMessage('ACRIT_EXP_TAB_LOG_DESC'),
			'SORT' => 40,
			'FILE' => __DIR__.$strTabsDir.'/log.php',
		);
	}
}

// Get system tabs codes
$arSystemTabs = array();
foreach($arTabs as $key => $arTab) {
	$arSystemTabs[] = $arTab['DIV'];
}

// Custom tabs
$arProfileTabs = array();
$intTabSortMinimal = 2;
$intTabSortDefault = 100;
if(is_object($obPlugin)) {
	$arProfileTabs = $obPlugin->getAdditionalTabs($intProfileID);
	if(!is_array($arProfileTabs)){
		$arProfileTabs = array();
	}
	foreach($arProfileTabs as $key => $arTab){
		if(!strlen($arTab['DIV']) || !strlen($arTab['TAB']) || !strlen($arTab['FILE']) || !is_file($arTab['FILE'])){
			unset($arProfileTabs[$key]);
		}
	}
	// Set tab sort by default
	foreach($arProfileTabs as $key => $arProfileTab) {
		$arProfileTab['SORT'] = IntVal($arProfileTab['SORT']);
		$arProfileTab['SORT'] = $arProfileTab['SORT']>=$intTabSortMinimal ? $arProfileTab['SORT'] : $intTabSortDefault;
		$arProfileTabs[$key] = $arProfileTab;
	}
	// Search files for additional tabs
	foreach($arProfileTabs as $key => $arProfileTab) {
		$bTabFileFound = false;
		if(strlen($arProfileTab['FILE'])) {
			if(is_file($arProfileTab['FILE']) && filesize($arProfileTab['FILE'])) {
				$bTabFileFound = true;
				$arProfileTabs[$key]['FILE'] = $arProfileTab['FILE'];
			}
		}
		if(!$bTabFileFound) {
			unset($arProfileTabs[$key]);
		}
	}
	if(is_array($arProfileTabs)) {
		$arTabs = array_merge($arTabs,$arProfileTabs);
	}
}
// Get custom tabs (ToDo: check this work properly)
foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers($strModuleId, 'OnGetAdditionalTabs') as $arHandler) {
	ExecuteModuleEventEx($arHandler, array(&$arTabs));
}
# Set tab sort by default (DEFAULT=100)
foreach($arTabs as $key => $arTab) {
	if(!in_array($arTab['DIV'], $arSystemTabs)){
		$arTab['SORT'] = IntVal($arTab['SORT']);
		$arTab['SORT'] = $arTab['SORT']>=$intTabSortMinimal ? $arTab['SORT'] : $intTabSortDefault;
		$arTabs[$key] = $arTab;
	}
}
usort($arTabs, '\Acrit\Core\Helper::sortBySort');

# Lock notifier
if(Helper::getOption($strModuleId, 'check_lock') == 'Y'){
	?><div id="acrit-exp-lock-notifier"><?
		#if(Profile::isLocked($arProfile)){
		if(Helper::call($strModuleId, 'Profile', 'isLocked', [$arProfile])){
			print '<div style="margin-top:15px;"></div>';
			print Helper::showNote(Loc::getMessage('ACRIT_EXP_LOCK_NOTICE', array(
				'#DATE#' => is_object($arProfile['DATE_LOCKED']) ? $arProfile['DATE_LOCKED']->toString() : '???',
			)), true).'<br/>';
		}
	?></div><?
}

# Update notifier
\Acrit\Core\Update::display();

# XDebug notifier
Helper::call($strModuleId, 'Profile', 'checkXDebug');

# Check export file name unique
if(!$bCopy && is_array($arProfile) && is_object($obPlugin)){
	$arCheckResults = $obPlugin->checkData();
	$arPluginErrors = [];
	if(is_array($arCheckResults)){
		foreach($arCheckResults as $arCheckResult){
			if(!is_array($arCheckResult)){
				$arCheckResult = ['MESSAGE' => $arCheckResult];
			}
			$bError = !!$arCheckResult['IS_ERROR'];
			$strErrorTitle = $arCheckResult['TITLE'];
			$strErrorMessage = $arCheckResult['MESSAGE'];
			if($bError){
				$arPluginErrors[] = Helper::showError($strErrorTitle, $strErrorMessage);
			}
			else{
				$arPluginErrors[] = Helper::showNote((strlen($strErrorTitle)?'<b>'.$strErrorTitle.'</b><br/>':'').$strErrorMessage, true, false, true);
			}
		}
	}
	print implode('', $arPluginErrors);
}

?><div id="acrit_exp_form"><?

// Show plugin messages
if(is_object($obPlugin)){
	ob_start();
	$obPlugin->showMessages();
	$strHtml = trim(ob_get_clean());
	if(strlen($strHtml)){
		print '<div id="acrit-exp-plugin-messages">'.$strHtml.'</div>';
	}
}

$bShowMainNotice = \CUserOptions::GetOption($strModuleId, 'main_notice_hidden') != 'Y';

// Start TabControl (via CAdminForm, not CAdminTabControl)
$obTabControl = new \CAdminForm($strAdminFormName, $arTabs, true, true);
$obTabControl->Begin(array(
	'FORM_ACTION' => $APPLICATION->GetCurPageParam('', array()),
));
$obTabControl->BeginPrologContent();
// Begin form parameters for JS
?>
<?if($bShowMainNotice):?>
	<div data-role="main-notice"><?=Helper::showNote(Loc::getMessage('ACRIT_EXP_MAIN_NOTICE_FOR_HINTS'), true);?></div>
<?endif?>
<input type="hidden" id="param__profile_id" value="<?=$intProfileID;?>" />
<input type="hidden" id="param__form_name" value="<?=$strAdminFormName;?>" />
<input type="hidden" id="param__plugin" value="<?=$arProfile['PLUGIN'];?>" />
<input type="hidden" id="param__format" value="<?=$arProfile['FORMAT'];?>" />
<input type="hidden" id="param__copy" value="<?=($bCopy?'Y':'N');?>" />
<input type="hidden" id="param__page_title" value="<?=($bCopy?'':$strPageTitle);?>" />
<?
// End form parameters for JS
$obTabControl->EndPrologContent();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// All tabs
foreach($arTabs as $arTab){
	$obTabControl->BeginNextFormTab();
	require $arTab['FILE'];
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$obTabControl->Buttons(array(
	'disabled' => false,
	'back_url' => 'acrit_'.$strModuleCodeLower.'_new_list.php?lang='.LANGUAGE_ID,
));
$obTabControl->Show();

?></div><?

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>