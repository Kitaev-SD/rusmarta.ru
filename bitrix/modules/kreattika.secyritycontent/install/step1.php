<?
##########################################################################
# Name: kreattika.secyritycontent                                        #
# http://marketplace.1c-bitrix.ru/solutions/kreattika.secyritycontent/   #
# File: step1.php                                                        #
# Version: 1.0.2                                                         #
# (c) 2011-2015 Kreattika, Sedov S.Y.                                    #
# Proprietary licensed                                                   #
# http://kreattika.ru/                                                   #
# mailto:info@kreattika.ru                                               #
##########################################################################
?>

<?
IncludeModuleLangFile(__FILE__);

if ($GLOBALS["install_step"] == 2):

	if(!check_bitrix_sessid()) return;

	if($ex = $APPLICATION->GetException())
		echo CAdminMessage::ShowMessage(Array(
			"TYPE" => "ERROR",
			"MESSAGE" => GetMessage("MOD_INST_ERR"),
			"DETAILS" => $ex->GetString(),
			"HTML" => true,
		));
	else
		echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));

?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
        <input type="hidden" name="lang" value="<?echo LANG?>">
        <input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">
<form>
<?
	return;
endif;

?>
<form action="<?=$APPLICATION->GetCurPage()?>" name="form1">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>" />
<input type="hidden" name="id" value="kreattika.secyritycontent" />
<input type="hidden" name="install" value="Y" />
<input type="hidden" name="step" value="2" />

<br />
<input type="submit" name="inst" value="<?= GetMessage("MOD_INSTALL")?>" />
</form>
<??>