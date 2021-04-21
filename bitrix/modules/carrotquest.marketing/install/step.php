<?if(!check_bitrix_sessid()) return;?>
<?
$module_id = "carrotquest.marketing";
COption::SetOptionString($module_id,"basket_page","/personal/cart/");
$strPath2file = str_replace("\\", "/", __FILE__);
$strPath2file = substr($strPath2file, 0, strlen($strPath2file)-strlen("/step.php"));
include(GetLangFileName($strPath2file.'/lang/', '/step.php'));
echo CAdminMessage::ShowNote(GetMessage("CARROT_INTEGR_INSTALL_MESSAGE"));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
<form>
