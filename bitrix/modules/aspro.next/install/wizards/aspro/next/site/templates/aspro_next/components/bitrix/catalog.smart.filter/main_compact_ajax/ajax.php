<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->RestartBuffer();
unset($arResult["COMBO"]);
echo CUtil::PHPToJSObject($arResult, true);

// some fixes from component.php, becouse there are some included components before filter, which will add their scripts in edit mode
$json = ob_get_contents();
$APPLICATION->RestartBuffer();
while(ob_end_clean());
header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
echo $json;
define("PUBLIC_AJAX_MODE", true);
//require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_after.php");
die();
?>