<?
$strCoreId = 'acrit.core';
$strModuleId = pathinfo(realpath(__DIR__.'/..'), PATHINFO_BASENAME);
$strFile = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strCoreId.'/admin/export/menu.php';
if(is_file($strFile)){
	return include $strFile;
}
?>