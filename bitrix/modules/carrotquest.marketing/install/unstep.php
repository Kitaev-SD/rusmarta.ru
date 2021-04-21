<?if(!check_bitrix_sessid()) return;?>
<?
$strPath2file = str_replace("\\", "/", __FILE__);
$strPath2file = substr($strPath2file, 0, strlen($strPath2file)-strlen("/unstep.php"));
include(GetLangFileName($strPath2file.'/lang/', '/unstep.php'));
echo CAdminMessage::ShowNote(GetMessage("CARROT_INTEGR_UNINSTALL_MESSAGE"));
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage('MOD_BACK')?>">	
<form>