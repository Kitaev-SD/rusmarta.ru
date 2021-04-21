<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

global $APPLICATION;
IncludeModuleLangFile(__FILE__);

$moduleClass = "CNext";
$moduleID = "aspro.next";
\Bitrix\Main\Loader::includeModule($moduleID);

use \Bitrix\Main\Config\Option;

$RIGHT = $APPLICATION->GetGroupRight($moduleID);
if($RIGHT >= "R"){
	$GLOBALS['APPLICATION']->SetAdditionalCss("/bitrix/css/".$moduleID."/style.css");
	$GLOBALS['APPLICATION']->SetTitle(GetMessage("NEXT_PAGE_TITLE"));

	$by = "id";
	$sort = "asc";

	$arSites = array();
	$db_res = CSite::GetList($by, $sort, array("ACTIVE"=>"Y"));
	while($res = $db_res->Fetch()){
		$arSites[] = $res;
	}

	$arTabs = array();
	$bShowGenerate = false;
	foreach($arSites as $key => $arSite){
		$arSite['DIR'] = str_replace('//', '/', '/'.$arSite['DIR']);
		if(!strlen($arSite['DOC_ROOT'])){
			$arSite['DOC_ROOT'] = $_SERVER['DOCUMENT_ROOT'];
		}
		$arSite['DOC_ROOT'] = str_replace('//', '/', $arSite['DOC_ROOT'].'/');
		$siteDir = str_replace('//', '/', $arSite['DOC_ROOT'].$arSite['DIR']);
		$optionsSiteID = $arSite["ID"];

		$arItems = array();
		$rsItems = CIBlockElement::GetList(array("NAME" => "ASC"), array("ACTIVE" => "Y", "LID" => $optionsSiteID, "IBLOCK_CODE" => "aspro_next_regions"), false, false, array("ID", "NAME", "IBLOCK_ID", "PROPERTY_MAIN_DOMAIN"));
		while($arItem = $rsItems->Fetch())
		{
			$arItems[] = $arItem;
		}
		$bGenerate = ((Option::get("aspro.next", "REGIONALITY_TYPE", "ONE_DOMAIN", $optionsSiteID) == "SUBDOMAIN") && Option::get("aspro.next", "USE_REGIONALITY", "N", $optionsSiteID) == "Y");
		if($bGenerate)
			$bShowGenerate = true;

		$arTabs[] = array(
			"DIV" => "edit".($key+1),
			"TAB" => GetMessage("MAIN_OPTIONS_SITE_TITLE", array("#SITE_NAME#" => $arSite["NAME"], "#SITE_ID#" => $arSite["ID"])),
			"ICON" => "settings",
			"PAGE_TYPE" => "site_settings",
			"SITE_ID" => $arSite["ID"],
			"SITE_DIR" => $arSite["DIR"],
			"SITE_DIR_FORMAT" => $siteDir,
			"ITEMS" => $arItems,
			"HAS_REGIONS" => $bGenerate,
		);
	}

	$tabControl = new CAdminTabControl("tabControl", $arTabs);

	if($REQUEST_METHOD == "POST" && $RIGHT >= "W" && check_bitrix_sessid())
	{
		global $APPLICATION, $CACHE_MANAGER;
		if($_POST["Apply"] || $_POST["ID"])
		{
			foreach($arTabs as $key => $arTab)
			{
				if($arTab["ITEMS"])
				{
					$file_access = $arTab["SITE_DIR_FORMAT"].'.htaccess';
					$file = file_get_contents($file_access);

					if(strpos($file, "ASPRO_ROBOTS") === false && strpos($file, "RewriteEngine On" ) !== false)
					{
						if(!file_exists($file_access.'_back'.time()))
							copy($file_access, $file_access.'_back'.time());

						$file = str_replace("RewriteEngine On", "RewriteEngine On
						\r\n\t# ASPRO_ROBOTS Serve sitemap.xml with sitemap.php only if the latter exists
\tRewriteCond %{REQUEST_FILENAME} robots.txt
\tRewriteCond %{DOCUMENT_ROOT}/robots.php -f
\tRewriteRule ^(.*)$ /robots.php [L]", $file);
						file_put_contents($file_access, $file);	
					}
				}
			}
		}

		if($_POST["Apply"])
		{
			$el = new CIBlockElement;
			foreach($arTabs as $key => $arTab)
			{
				if($arTab["ITEMS"])
				{
					foreach($arTab["ITEMS"] as $arItem)
						$res = $el->Update($arItem["ID"], array("ACTIVE" => "Y"));					
				}
			}
		}
		if($_POST["ID"])
		{
			$el = new CIBlockElement;
			$res = $el->Update($_POST["ID"], array("ACTIVE" => "Y"));
		}
		$APPLICATION->RestartBuffer();
	}
	
	CJSCore::Init(array("jquery"));
	CAjax::Init();
	?>
	<?if(!count($arTabs)):?>
		<div class="adm-info-message-wrap adm-info-message-red">
			<div class="adm-info-message">
				<div class="adm-info-message-title"><?=GetMessage("ASPRO_NEXT_NO_SITE_INSTALLED", array("#SESSION_ID#"=>bitrix_sessid_get()))?></div>
				<div class="adm-info-message-icon"></div>
			</div>
		</div>
	<?else:?>
		<?if($bShowGenerate):?>
			<div class="adm-info-message"><?=GetMessage("NEXT_MODULE_ROBOTS_INFO");?></div>
			<br>
			<br>
		<?endif;?>
		<?$tabControl->Begin();?>
		<?$bShowBtn = true;?>
		<form method="post" class="next_options" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post();?>		
		<?
		foreach($arTabs as $key => $arTab)
		{
			$tabControl->BeginNextTab();
			if($arTab["SITE_ID"])
			{
				$optionsSiteID = $arTab["SITE_ID"];?>
				<tr>
					<td>
						<?=GetMessage("NEXT_MODULE_ROBOTS_MAIN");?>							
					</td>
					<td style="width:50%;">
						<?$template = 'include_area.php';
						$href = "javascript: new BX.CAdminDialog({'content_url':'/bitrix/admin/public_file_edit.php?site=".$optionsSiteID."&bxpublic=Y&from=includefile&templateID=".TEMPLATE_NAME."&path=".$arTab["SITE_DIR"]."robots.txt&lang=".LANGUAGE_ID."&noeditor=Y&template=include_area.php&subdialog=Y&siteTemplateId=".TEMPLATE_NAME."','width':'1009','height':'503'}).Show();";
						?><a class="adm-btn" href="<?=$href?>" name="edit" title="<?=GetMessage('NEXT_MODULE_EDIT_ROBOTS')?>"><?=GetMessage('NEXT_MODULE_EDIT_ROBOTS')?></a>
						
					</td>
				</tr>
				<?if($arTab["HAS_REGIONS"]):?>
					<tr class="heading"><td colspan="2"><?=GetMessage("NEXT_MODULE_DOMAINS")?></td></tr>

					<?if($arTab["ITEMS"])
					{
						foreach($arTab["ITEMS"] as $arItem):?>
							<tr>
								<td>
									<?=$arItem["NAME"]." (".$arItem["PROPERTY_MAIN_DOMAIN_VALUE"].")";?>							
								</td>
								<td style="width:50%;">
									<?$template = 'include_area.php';
									$href = "javascript: new BX.CAdminDialog({'content_url':'/bitrix/admin/public_file_edit.php?site=".$optionsSiteID."&bxpublic=Y&from=includefile&templateID=".TEMPLATE_NAME."&path=".$arTab["SITE_DIR"]."aspro_regions/robots/robots_".$arItem["PROPERTY_MAIN_DOMAIN_VALUE"].".txt&lang=".LANGUAGE_ID."&noeditor=Y&template=include_area.php&subdialog=Y&siteTemplateId=".TEMPLATE_NAME."','width':'1009','height':'503'}).Show();";
									?><a class="adm-btn" href="<?=$href?>" name="edit" title="<?=GetMessage('NEXT_MODULE_EDIT_ROBOTS')?>"><?=GetMessage('NEXT_MODULE_EDIT_ROBOTS')?></a>
									<input type="button" name="generate" data-element_id="<?=$arItem["ID"];?>" class="submit-btn adm-btn-save" value="<?=GetMessage("NEXT_MODULE_GENERATE_ROBOTS_SHORT")?>" title="<?=GetMessage("NEXT_MODULE_GENERATE_ROBOTS_SHORT")?>">
									<?$href = (CMain::isHTTPS() ? "https://" : "http://").$arItem["PROPERTY_MAIN_DOMAIN_VALUE"]."/robots.txt";?>
									<a href="<?=$href;?>" target="_blank"><?=$href;?></a>
								</td>
							</tr>
						<?endforeach;?>
					<?}?>
				<?else:?>
					<tr>
						<td style="width:100%;text-align:center;" colspan="2">
							<div class="adm-info-message"><?=GetMessage("NEXT_MODULE_ROBOTS_ERROR");?></div>
						</td>
					</tr>
				<?endif;?>	
			<?}
		}?>
		<?
		if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) && check_bitrix_sessid())
		{
			if(strlen($Update) && strlen($_REQUEST["back_url_settings"]))
				LocalRedirect($_REQUEST["back_url_settings"]);
			else
				LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
		}?>
			<?$tabControl->Buttons();?>
			<?if($bShowGenerate):?>
				<input <?if($RIGHT < "W") echo "disabled"?> type="submit" name="Apply" class="submit-btn adm-btn-save" value="<?=GetMessage("NEXT_MODULE_GENERATE_ROBOTS")?>" title="<?=GetMessage("NEXT_MODULE_GENERATE_ROBOTS")?>">
			<?endif;?>
			<script type="text/javascript">
				$(document).ready(function(){
					$('input[name=generate]').on('click', function(){
						var _this = $(this);
						_this.attr('disabled', 'disabled');
						$.ajax({
							type: 'POST',
							dataType: 'html',
							data: {'sessid': $('input[name=sessid]').val(), 'ID': _this.data('element_id')},
							success: function(html){
								_this.removeAttr('disabled');
							},
							error: function(data){
								window.console&&console.log(data);
							}
						});
					})
				});			
			</script>
		</form>
		<?$tabControl->End();?>
	<?endif;?>
<?}
else
{
	echo CAdminMessage::ShowMessage(GetMessage('NO_RIGHTS_FOR_VIEWING'));
}?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>