<?
namespace Acrit\Core;

use
	\Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

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
\CJSCore::Init(array('jquery','jquery2'));

// Check rights
if($APPLICATION->GetGroupRight($strModuleId) == 'D'){
	$APPLICATION->authForm(Loc::getMessage('ACCESS_DENIED'));
}

// Input data
$obGet = \Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList();
$arGet = $obGet->toArray();
$obPost = \Bitrix\Main\Context::getCurrent()->getRequest()->getPostList();
$arPost = $obPost->toArray();

// Demo
acritShowDemoExpired($strModuleId);

// Page title
$strPageTitle = Loc::getMessage('ACRIT_CORE_PAGE_TITLE');

// Core notice
if(!\Bitrix\Main\Loader::includeModule($strCoreId)){
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
	?><div id="acrit-core-notifier"><?
		print '<div style="margin-top:15px;"></div>';
		print \CAdminMessage::ShowMessage(array(
			'MESSAGE' => \Bitrix\Main\Localization\Loc::getMessage('ACRIT_CORE_NOTICE', [
				'#CORE_ID#' => $strCoreId,
				'#LANG#' => LANGUAGE_ID,
			]),
			'HTML' => true,
		));
	?></div><?
	$APPLICATION->SetTitle($strPageTitle);
	die();
}

// Module
\Bitrix\Main\Loader::includeModule($strModuleId);
$strModuleName = Helper::getModuleName($strModuleId);

// Core (part 2, visual)
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

// Demo
acritShowDemoNotice($strModuleId);

# Update notifier
\Acrit\Core\Update::display();

// Set page title
$APPLICATION->SetTitle($strPageTitle);

# Prepare tabs
$arTabs = [];
$arTabs['courses'] = [
	'TAB' => Helper::getMessage('ACRIT_CORE_TAB_COURSES_NAME'),
	'TITLE' => Helper::getMessage('ACRIT_CORE_TAB_COURSES_DESC'),
	'FILE' => realpath(__DIR__.'/../../include/export/course/courses.php'),
];
# Move array associative key to value 'DIV' and remove this associative key (array must be non-associtive)
foreach($arTabs as $strTab => &$arTab){
	$arTab['DIV'] = $strTab;
}
unset($arTab);
$arTabs = array_values($arTabs);
# Replace #MODULE_NAME# in title and description
foreach($arTabs as $strTab => &$arTab){
	$arTab['TAB'] = str_replace('#MODULE_NAME#', $strModuleName, $arTab['TAB']);
	$arTab['TITLE'] = str_replace('#MODULE_NAME#', $strModuleName, $arTab['TITLE']);
}
unset($arTab);

?><div id="acrit_core_support"><?

// Start TabControl (via CAdminForm, not CAdminTabControl)
$obTabControl = new \CAdminForm('AcritExpSupport', $arTabs);
$obTabControl->Begin(array(
	'FORM_ACTION' => $APPLICATION->getCurPageParam('', array()),
));

# Display tabs
foreach($arTabs as $arTab){
	if(strlen($arTab['FILE']) && is_file($arTab['FILE'])){
		$obTabControl->beginNextFormTab();
		require_once($arTab['FILE']);
	}
}

$obTabControl->Show();

?></div><?

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>