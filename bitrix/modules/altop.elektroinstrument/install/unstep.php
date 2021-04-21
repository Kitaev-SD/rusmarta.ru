<?if(!check_bitrix_sessid())
	return;?>

<?=CAdminMessage::ShowNote(GetMessage("ELEKTROINSTRUMENT_MOD_UNINST_OK"));?>

<form action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="submit" name="" value="<?=GetMessage('ELEKTROINSTRUMENT_MOD_BACK')?>">
<form>