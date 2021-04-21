<?
##########################################################################
# Name: kreattika.secyritycontent                                        #
# http://marketplace.1c-bitrix.ru/solutions/kreattika.secyritycontent/   #
# File: options.php                                                      #
# Version: 1.0.2                                                         #
# (c) 2011-2015 Kreattika, Sedov S.Y.                                    #
# Proprietary licensed                                                   #
# http://kreattika.ru/                                                   #
# mailto:info@kreattika.ru                                               #
##########################################################################
?><?
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

if(!$USER->IsAdmin()) return;

	$MODULE_ID = $module_id = "kreattika.secyritycontent";
	$strWarning = "";

	$SecurityType = array(
		"1"=>GetMessage("KREATTIKA_SECURITYCONTENT_SET_SECURITY_TYPE_1"),
		"2"=>GetMessage("KREATTIKA_SECURITYCONTENT_SET_SECURITY_TYPE_2"),
		"3"=>GetMessage("KREATTIKA_SECURITYCONTENT_SET_SECURITY_TYPE_3"),
		"4"=>GetMessage("KREATTIKA_SECURITYCONTENT_SET_SECURITY_TYPE_4"),
		"5"=>GetMessage("KREATTIKA_SECURITYCONTENT_SET_SECURITY_TYPE_5"),
	);

	$arSiteList = array();
	$obSites = CSite::GetList($by="sort", $order="asc");
	while ($arSite = $obSites->Fetch()):
		$arSiteList[$arSite["ID"]] = "[".$arSite["ID"]."] ".$arSite["NAME"];
	endwhile;

	$arGroupList = array();
	$obGroups = CGroup::GetList(($by="id"), ($order="asc"));
	while ($arGroups = $obGroups->Fetch()):
		$arGroupList[$arGroups["ID"]] = "[".$arGroups["ID"]."] ".$arGroups["NAME"];
	endwhile;

	$SectionTitleNum = 1;
	
	$SecurityModType = COption::GetOptionString($MODULE_ID, "secyritycontent_type", "1");

/////////////////////////////////////////////////////////////////////////////////////////
//* типы значений полей настроек: text, checkbox, selectbox, multiselectbox, textarea *//
/////////////////////////////////////////////////////////////////////////////////////////
	$arAllOptions = array(
	        "main" => Array(
			Array("secyritycontent_on", GetMessage("KREATTIKA_SECURITYCONTENT_SET_ON"), "", Array("checkbox")),
			Array("secyritycontent_jquery", GetMessage("KREATTIKA_SECURITYCONTENT_SET_JQUERY"), "", Array("checkbox")),
			Array("secyritycontent_site", GetMessage("KREATTIKA_SECURITYCONTENT_SET_SECURITY_SITE_TITLE"), "", Array("multiselectbox", $arSiteList)),
			Array("secyritycontent_type", GetMessage("KREATTIKA_SECURITYCONTENT_SET_SECURITY_TYPE_TITLE"), "", Array("selectbox", $SecurityType)),
			Array("secyritycontent_disable_user_group", GetMessage("KREATTIKA_SECURITYCONTENT_SET_SECURITY_GROUP_TITLE"), "", Array("multiselectbox", $arGroupList)),
	        ),
	);

	if( $SecurityModType == 2 ):
		$arAllOptions["main"][] = Array("secyritycontent_id", GetMessage("KREATTIKA_SECURITYCONTENT_SET_SECURITY_ID_TITLE"), "", Array("text"));
	elseif( $SecurityModType == 3 ):
		$arAllOptions["main"][] = Array("secyritycontent_class", GetMessage("KREATTIKA_SECURITYCONTENT_SET_SECURITY_CLASS_TITLE"), "", Array("text"));
	endif;

	if( $SecurityModType == 1 || $SecurityModType == 2 || $SecurityModType == 3 ):
		$SectionTitleNum = 1;
		$arAllOptions["more_options"] = Array(
				Array("secyritycontent_copy", GetMessage("KREATTIKA_SECURITYCONTENT_SET_COPY"), "", Array("checkbox")),
				Array("secyritycontent_select", GetMessage("KREATTIKA_SECURITYCONTENT_SET_SELECT"), "", Array("checkbox")),
				Array("secyritycontent_right_btn_mouse", GetMessage("KREATTIKA_SECURITYCONTENT_SET_RIGHT_BTN_MOUSE"), "", Array("checkbox")),
				Array("secyritycontent_drag_and_drop", GetMessage("KREATTIKA_SECURITYCONTENT_SET_DRAG_AND_DROP"), "", Array("checkbox")),
		        );
	elseif($SecurityModType == 4 || $SecurityModType == 5):
		$SectionTitleNum = 2;
		$arAllOptions["more_options"] = Array(
				Array("secyritycontent_include_message", GetMessage("KREATTIKA_SECURITYCONTENT_SET_INC_MESS_TITLE"), GetMessage("KREATTIKA_SECURITYCONTENT_SET_INC_MESS"), Array("text")),
		        );
	endif;


	$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
		array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "kreattika_comments_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
	);

//Restore defaults
if ($USER->IsAdmin() && $_SERVER["REQUEST_METHOD"]=="GET" && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
        COption::RemoveOption($module_id);
}
$tabControl = new CAdminTabControl("tabControl", $aTabs);

function ShowParamsHTMLByArray($module_id, $arParams)
{
        foreach($arParams as $Option)
        {
                 __AdmSettingsDrawRow($module_id, $Option);
        }
}

//Save options
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid())
{
        if(strlen($RestoreDefaults)>0)
        {
                COption::RemoveOption($module_id);
        }
        else
        {
                foreach($arAllOptions as $aOptGroup)
                {
                        foreach($aOptGroup as $option)
                        {
                                __AdmSettingsSaveOption($module_id, $option);
                        }
                }
        }
        if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
                LocalRedirect($_REQUEST["back_url_settings"]);
        else
                LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?echo LANG?>">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
        ?><?$ModuleInstallStatus = CModule::IncludeModuleEx($MODULE_ID);
	if($ModuleInstallStatus == 2 || $ModuleInstallStatus == 3):?>
		<tr>
			<td colspan="2">
				<div style="background-color: #fff; padding: 10px; margin-bottom: 15px;">
					<div style="padding: 7px; text-align: center;">
						<?if($ModuleInstallStatus == 2):?>
							<?=GetMessage("KREATTIKA_IS_DEMO")?>
						<?elseif($ModuleInstallStatus == 3):?>
							<span style="color: #cc0000"><?=GetMessage("KREATTIKA_IS_DEMO_EXPIRED")?></span>
						<?endif;?>
						<a href="http://marketplace.1c-bitrix.ru/solutions/<?=$MODULE_ID?>/" target="_blank" ><?=GetMessage("KREATTIKA_FOOL_VERSION_BUY")?></a>
					</div>
				</div>
			</td>
		</tr>
	<?endif;?>
	<tr><td colspan="2">
		<div style="padding: 0; border-top: 1px solid #8E8E8E; border-bottom: 1px solid #8E8E8E;  margin-bottom: 15px;">
			<div style="background-color: #fff; opacity: 0.9; height: 30px; padding: 7px; border: 1px solid #fff">
			        <!--<a href="http://kreattika.ru/sale/?solution=secyritycontent" target="_blank"><img src="/bitrix/modules/kreattika.secyritycontent/images/kreattika-logo.png" style="float: left; margin-right: 15px;" border="0" /></a>//-->
			        <div style="margin: 5px 0px 0px 0px">
			                <a href="http://kreattika.ru/sale/?solution=secyritycontent" target="_blank" style="color: #ff6600; font-size: 18px; text-decoration: none"><?=GetMessage("KREATTIKA_AGENSY")?></a>
			        </div>
			</div>
		</div>
	</td></tr>
	<?ShowParamsHTMLByArray($module_id, $arAllOptions["main"]);?>
	<tr class="heading">
		<td colspan="2"><?=GetMessage("KREATTIKA_SECURITYCONTENT_SET_OPT_TITLE_".$SectionTitleNum)?></td>
	</tr>
	<?ShowParamsHTMLByArray($module_id, $arAllOptions["more_options"]);?>
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?$tabControl->Buttons();?>
<script language="JavaScript">
function RestoreDefaults()
{
        if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
                window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
}
</script>
<div align="left">
        <input type="hidden" name="Update" value="Y">
        <input type="submit" <?if(!$USER->IsAdmin())echo " disabled ";?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
        <input type="reset" <?if(!$USER->IsAdmin())echo " disabled ";?> name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
        <input type="button" <?if(!$USER->IsAdmin())echo " disabled ";?> title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
</div>
<?$tabControl->End();?>
<?=bitrix_sessid_post();?>
</form>
