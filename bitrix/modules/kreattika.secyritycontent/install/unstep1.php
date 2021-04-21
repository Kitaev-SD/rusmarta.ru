<?
##########################################################################
# Name: kreattika.secyritycontent                                        #
# http://marketplace.1c-bitrix.ru/solutions/kreattika.secyritycontent/   #
# File: unstep1.php                                                      #
# Version: 1.0.2                                                         #
# (c) 2011-2015 Kreattika, Sedov S.Y.                                    #
# Proprietary licensed                                                   #
# http://kreattika.ru/                                                   #
# mailto:info@kreattika.ru                                               #
##########################################################################
?>
<?
IncludeModuleLangFile(__FILE__);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
        <?=bitrix_sessid_post()?>
        <input type="hidden" name="lang" value="<?echo LANG?>">
        <input type="hidden" name="id" value="kreattika.secyritycontent">
        <input type="hidden" name="uninstall" value="Y">
        <input type="hidden" name="step" value="2">
        <?echo CAdminMessage::ShowMessage(GetMessage("MOD_UNINST_WARN"))?>

        <p><b><?echo GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_TITLE")?></b></p>
        <p>
                <input type="radio" name="uq" value="<?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_1")?>"> <?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_1")?><br/>
                <input type="radio" name="uq" value="<?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_2")?>"> <?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_2")?><br/>
                <input type="radio" name="uq" value="<?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_3")?>"> <?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_3")?><br/>
                <input type="radio" name="uq" value="<?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_4")?>"> <?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_4")?><br/>
                <input type="radio" name="uq" value="<?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_5")?>"> <?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_5")?><br/>
                <input type="radio" name="uq" value="<?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_6")?>"> <?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_6")?><br/>
                <input type="radio" name="uq" value="<?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_7")?>"> <?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_7")?><br/>
        </p>
        <p><textarea name="utext" rows="5" cols="45" placeholder="<?=GetMessage("KREATTIKA_SECURITYCONTENT_UNINST_QUESTIONS_DETAIL_TITLE")?>"></textarea></p>

        <p><?echo GetMessage("MOD_UNINST_SAVE")?></p>
        <p><input type="checkbox" name="savedata" id="savedata" value="Y" checked><label for="savedata"><?echo GetMessage("MOD_UNINST_SAVE_TABLES")?></label></p>
        <input type="submit" name="inst" value="<?echo GetMessage("MOD_UNINST_DEL")?>">
</form>
