<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

global $APPLICATION;
IncludeModuleLangFile(__FILE__);

$moduleClass = "CNext";
$moduleID = "aspro.next";
\Bitrix\Main\Loader::includeModule($moduleID);

use \Bitrix\Main\Config\Option;
use CNextCache as Cache;

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
		$arItems = array();
		$arSite['DIR'] = str_replace('//', '/', '/'.$arSite['DIR']);
		if(!strlen($arSite['DOC_ROOT'])){
			$arSite['DOC_ROOT'] = $_SERVER['DOCUMENT_ROOT'];
		}
		$arSite['DOC_ROOT'] = str_replace('//', '/', $arSite['DOC_ROOT'].'/');
		$siteDir = str_replace('//', '/', $arSite['DOC_ROOT'].$arSite['DIR']);
		$optionsSiteID = $arSite["ID"];

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
			"HAS_REGIONS" => $bGenerate,
			"ITEMS" => $arItems,
			"OPTIONS" => array(
				"SITEMAP_URL" => array(
					"TITLE" => GetMessage("NEXT_MODULE_SITEMAP_URL_TITLE"),
					"DEFAULT" => ($arSite["SERVER_NAME"] ? $arSite["SERVER_NAME"] : $_SERVER["SERVER_NAME"]),
					"TYPE" => "text",
					"REQUIRED" => "Y",
				),
				"SITEMAP_NAME" => array(
					"TITLE" => GetMessage("NEXT_MODULE_SITEMAP_NAME_TITLE"),
					"DEFAULT" => "sitemap.xml",
					"TYPE" => "text",
					"REQUIRED" => "Y",
				),
				/*"SITEMAP_ADD_ROBOTS" => array(
					"TITLE" => GetMessage("NEXT_MODULE_SITEMAP_ADD_ROBOTS_TITLE"),
					"TYPE" => "checkbox",
					"DEFAULT" => "N",
				),*/
			),
		);
	}

	$tabControl = new CAdminTabControl("tabControl", $arTabs);

	$arErrors = $arOK = array();
	if($REQUEST_METHOD == "POST" && $RIGHT >= "W" && check_bitrix_sessid())
	{
		global $APPLICATION, $CACHE_MANAGER;

		if($_POST["Generate"])
		{
			foreach($arTabs as $key => $arTab)
			{
				$optionsSiteID = $arTab["SITE_ID"];
				if(isset($arTab["OPTIONS"]) && $arTab["OPTIONS"])
				{
					foreach($arTab["OPTIONS"] as $optionCode => $arOption)
					{
						if(isset($arOption["REQUIRED"]) && $arOption["REQUIRED"] == "Y")
						{
							if(!$_POST[$optionCode."_".$optionsSiteID])
								$arErrors[$optionsSiteID][] = GetMessage('NEXT_MODULE_FIELD_NO_VALUE', array('#FIELD#' => GetMessage('NEXT_MODULE_'.$optionCode.'_TITLE'), "#SITE_ID#" => $optionsSiteID));
						}
						if($_POST[$optionCode."_".$optionsSiteID])
						{
							Option::set("aspro.next", $optionCode, $_POST[$optionCode."_".$optionsSiteID], $optionsSiteID);
						}
					}
					$siteMapName =  Option::get("aspro.next", "SITEMAP_NAME", $arTab["OPTIONS"]["SITEMAP_NAME"]["DEFAULT"], $optionsSiteID);
					$siteMapNewName =  Option::get("aspro.next", "SITEMAP_NEW_NAME", $arTab["OPTIONS"]["SITEMAP_NEW_NAME"]["DEFAULT"], $optionsSiteID);
					$siteMapUrl =  Option::get("aspro.next", "SITEMAP_URL", $arTab["OPTIONS"]["SITEMAP_URL"]["DEFAULT"], $optionsSiteID);
					$bExistSiteMap = (file_exists($arTab["SITE_DIR_FORMAT"].$siteMapName));
					if($arTab["HAS_REGIONS"])
					{
						if(!$bExistSiteMap)
							$arErrors[$optionsSiteID][] = GetMessage("NEXT_MODULE_FILENAME_FULL", array("#FILE#" => $siteMapName, "#SITE_ID#" => $optionsSiteID));
					}

					if(!$arErrors[$optionsSiteID])
					{
						if($arTab["ITEMS"])
						{

							$arName = explode(".xml", $siteMapName);
							$siteMapNameTmp = reset($arName);
							$siteMapNameTmp2 = $siteMapNameTmp;

							// get iblock id of landings in search
							if($LANDINGS_SEARCH_IBLOCK_ID = Cache::$arIBlocks[$optionsSiteID]['aspro_next_catalog']['aspro_next_search'][0]){
								// get sitemap file for this iblock
								$sitemapLandingsSearch = $siteMapNameTmp.'_iblock_'.$LANDINGS_SEARCH_IBLOCK_ID.'.xml';
							}

							$arFiles = array();
							foreach(glob($arTab["SITE_DIR_FORMAT"].$siteMapNameTmp.'*.xml', 0) as $dir){
								$dir = str_replace($arTab["SITE_DIR_FORMAT"], '', basename($dir));
								$arFiles[] = $dir;

								if($LANDINGS_SEARCH_IBLOCK_ID && strpos($dir, $sitemapLandingsSearch) !== false){
									$sitemapLandingsSearch = $dir;
								}
							}


							// get content of sitemap of landings in search
							if($sitemapLandingsSearch && file_exists($arTab['SITE_DIR_FORMAT'].$sitemapLandingsSearch)){
								$content = @file_get_contents($arTab['SITE_DIR_FORMAT'].$sitemapLandingsSearch);

								// get landings items
								$arLandings = $arLandingsIDs = array();
								if($content && preg_match_all('/<url>\s*<loc>(([^<]*)=(\d*))<\/loc>\s*<lastmod>([^<]*)<\/lastmod>\s*<\/url>/i', $content, $arLandingsMatches)){
									$arLandingsIDs = $arLandingsMatches[3];
									$arLandings = Cache::CIBLockElement_GetList(
										array(
											'ID' => 'ASC',
											'CACHE' => array(
												'MULTI' => 'N',
												'TAG' => Cache::GetIBlockCacheTag($LANDINGS_SEARCH_IBLOCK_ID),
												'GROUP' => array('ID'),
											),
										),
										array(
											'ID' => $arLandingsIDs,
											'ACTIVE' => 'Y',
										),
										false,
										false,
										array(
											'ID',
											'IBLOCK_ID',
											'NAME',
											'DETAIL_PAGE_URL',
											'PROPERTY_IS_INDEX',
											'PROPERTY_URL_CONDITION',
											'PROPERTY_REDIRECT_URL',
											'PROPERTY_QUERY',
											'PROPERTY_LINK_REGION',
										)
									);

									// get enum id of property IS_INDEX with XML_ID = Y
									$arEnumID_IS_INDEX = \CIBlockPropertyEnum::GetList(
										array(),
										array(
											'IBLOCK_ID' => $LANDINGS_SEARCH_IBLOCK_ID,
											'CODE' => 'IS_INDEX',
											'XML_ID' => 'Y',
										)
									)->GetNext();
								}
							}

							$bInsertRobots = ($_POST["SITEMAP_ADD_ROBOTS_".$optionsSiteID] && $_POST["SITEMAP_ADD_ROBOTS_".$optionsSiteID]);
							if($bInsertRobots)
							{
								if(file_exists($arTab["SITE_DIR_FORMAT"]."robots.txt"))
								{
									copy($arTab["SITE_DIR_FORMAT"].'robots.txt', $arTab["SITE_DIR_FORMAT"].'robots.txt._back'.time());
									$arFile = file($arTab["SITE_DIR_FORMAT"].'robots.txt');
									$bFind = false;
									foreach($arFile as $key => $str)
									{
										if(strpos($str, "Sitemap") !== false)
										{
											$bFind = true;
											$arFile[$key] = "Sitemap: ".(CMain::isHTTPS() ? "https://" : "http://").$siteMapUrl."/".$siteMapName."\r\n";
										}
									}
									if(!$bFind)
										$arFile[] = "\r\nSitemap: ".(CMain::isHTTPS() ? "https://" : "http://").$siteMapUrl."/".$siteMapName."\r\n";
									$strr = implode("", $arFile);
									file_put_contents($arTab["SITE_DIR_FORMAT"].'robots.txt', $strr);
								}
								$el = new CIBlockElement;
								foreach($arTab["ITEMS"] as $arItem)
									$res = $el->Update($arItem["ID"], array("ACTIVE" => "Y", "SITE_MAP" => $siteMapName));
							}
							if($arFiles)
							{
								foreach($arFiles as $xmlfile)
								{
									$arName = array();
									$siteMapNameTmp = "";

									$arName = explode(".xml", $xmlfile);
									$siteMapNameTmp = reset($arName);
									if($siteMapNameTmp)
									{
										foreach($arTab["ITEMS"] as $arItem)
										{
											if(!file_exists($arTab["SITE_DIR_FORMAT"].$siteMapNameTmp.".php"))
											{
												// @copy($arTab["SITE_DIR_FORMAT"]."sitemap.php", $arTab["SITE_DIR_FORMAT"].$siteMapNameTmp.".php");
												CopyDirFiles($arTab["SITE_DIR_FORMAT"]."sitemap.php", $arTab["SITE_DIR_FORMAT"].$siteMapNameTmp.".php", true, true);
												$file = file_get_contents($arTab["SITE_DIR_FORMAT"].$siteMapNameTmp.".php");
												$file = str_replace(array("sitemap_", "sitemap."), array($siteMapNameTmp."_", $siteMapNameTmp."."), $file);
												file_put_contents($arTab["SITE_DIR_FORMAT"].$siteMapNameTmp.".php", $file);
											}

											//@copy($arTab["SITE_DIR_FORMAT"].$xmlfile, $arTab["SITE_DIR_FORMAT"].'aspro_regions/sitemap/'.$siteMapNameTmp.'_'.$arItem["PROPERTY_MAIN_DOMAIN_VALUE"].'.xml');
											CopyDirFiles($arTab["SITE_DIR_FORMAT"].$xmlfile, $arTab["SITE_DIR_FORMAT"].'aspro_regions/sitemap/'.$siteMapNameTmp.'_'.$arItem["PROPERTY_MAIN_DOMAIN_VALUE"].'.xml', true, true);

											// rewrite sitemap for landings in search iblock
											if($xmlfile === $sitemapLandingsSearch && $arLandings){
												$regionSitemapLandingSearch = $arTab["SITE_DIR_FORMAT"].'aspro_regions/sitemap/'.$siteMapNameTmp.'_'.$arItem["PROPERTY_MAIN_DOMAIN_VALUE"].'.xml';
												if($content = @file_get_contents($regionSitemapLandingSearch)){
													foreach($arLandingsMatches[0] as $i => $match){
														$LID = $arLandingsMatches[3][$i];
														if($arLandings[$LID]){
															$arLandings[$LID]['PROPERTY_LINK_REGION_VALUE'] = (array)$arLandings[$LID]['PROPERTY_LINK_REGION_VALUE'];
															if((!$arEnumID_IS_INDEX || ($arEnumID_IS_INDEX && $arLandings[$LID]['PROPERTY_IS_INDEX_ENUM_ID'] == $arEnumID_IS_INDEX['ID'])) && (!$arLandings[$LID]['PROPERTY_LINK_REGION_VALUE'] || in_array($arItem['ID'], $arLandings[$LID]['PROPERTY_LINK_REGION_VALUE']))){

																$catalogDir = preg_replace('/[\?].*/', '', $arLandings[$LID]['DETAIL_PAGE_URL']);
																$url = \Aspro\Next\SearchQuery::getLandingUrl(
																	$catalogDir,
																	$arLandings[$LID]['PROPERTY_URL_CONDITION_VALUE'],
																	$arLandings[$LID]['PROPERTY_REDIRECT_URL_VALUE'],
																	$arLandings[$LID]['PROPERTY_QUERY_VALUE']
																);

																$url = str_replace('&', '%26', $url);

																if(strpos($url, 'http') === false){
																	$url = (CMain::isHTTPS() ? 'https://' : 'http://').str_replace('//', '/', $siteMapUrl.$url);
																}
																$content = str_replace($arLandingsMatches[1][$i], $url, $content);

																continue;
															}
														}

														// delete if not IS_INDEX
														$content = str_replace($match, '', $content);
													}
													@file_put_contents($regionSitemapLandingSearch, $content);
												}
											}

											$file = file_get_contents($arTab["SITE_DIR_FORMAT"].'aspro_regions/sitemap/'.$siteMapNameTmp.'_'.$arItem["PROPERTY_MAIN_DOMAIN_VALUE"].'.xml');
											$file = str_replace($siteMapUrl, $arItem["PROPERTY_MAIN_DOMAIN_VALUE"], $file);
											file_put_contents($arTab["SITE_DIR_FORMAT"].'aspro_regions/sitemap/'.$siteMapNameTmp.'_'.$arItem["PROPERTY_MAIN_DOMAIN_VALUE"].'.xml', $file);

											$file_access = $arTab["SITE_DIR_FORMAT"].'.htaccess';
											$file = file_get_contents($file_access);

											if(strpos($file, "ASPRO_SITEMAP_".$siteMapNameTmp ) === false && strpos($file, "RewriteEngine On" ) !== false)
											{
												if(!file_exists($file_access.'_back'.time()))
													copy($file_access, $file_access.'_back'.time());

												$file = str_replace("RewriteEngine On", "RewriteEngine On
												\r\n\t# ASPRO_SITEMAP_".$siteMapNameTmp." Serve sitemap.xml with sitemap.php only if the latter exists
\tRewriteCond %{REQUEST_FILENAME} ".$siteMapNameTmp.".xml
\tRewriteCond %{DOCUMENT_ROOT}/".$siteMapNameTmp.".php -f
\tRewriteRule ^(.*)$ /".$siteMapNameTmp.".php [L]", $file);
												file_put_contents($file_access, $file);
											}
										}
									}
								}
								$arOK[$optionsSiteID][] = GetMessage("NEXT_MODULE_FILENAME_GENERATE", array("#FILE#" => $siteMapName, "#SITE_ID#" => $optionsSiteID));
							}
						}
					}
				}
			}
		}
		if(!$arErrors && !$arOK)
			$APPLICATION->RestartBuffer();
	}

	CJSCore::Init(array("jquery"));
	CAjax::Init();

	if(!empty($arErrors))
	{
		foreach($arErrors as $siteID => $arError)
			CAdminMessage::ShowMessage(join("\n", $arError));
	}

	if(!empty($arOK))
	{
		foreach($arOK as $siteID => $arError)
			CAdminMessage::ShowMessage(array("MESSAGE" => join("\n", $arError), "TYPE" => "OK"));
	}
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
			<div class="adm-info-message"><?=GetMessage("NEXT_MODULE_SITEMAP_INFO");?></div>
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
				<?if(isset($arTab["OPTIONS"]) && $arTab["OPTIONS"]):?>
					<tr class="heading"><td colspan="2"><?=GetMessage("NEXT_MODULE_MAIN_CONFIG")?></td></tr>
					<?foreach($arTab["OPTIONS"] as $optionCode => $arOption):?>
						<?$val = Option::get("aspro.next", $optionCode, $arOption["DEFAULT"], $optionsSiteID);?>
						<tr>
							<td>
								<?=$arOption["TITLE"];?>
							</td>
							<td style="width:50%;">
								<input type="<?=$arOption["TYPE"];?>" size="" maxlength="255" value="<?=$val;?>" name="<?=$optionCode;?>_<?=$optionsSiteID;?>">
							</td>
						</tr>
					<?endforeach;?>
				<?endif;?>
				<?
				$siteMapName =  Option::get("aspro.next", "SITEMAP_NAME", $arTab["OPTIONS"]["SITEMAP_NAME"]["DEFAULT"], $optionsSiteID);
				$bExistSiteMap = (file_exists($arTab["SITE_DIR_FORMAT"].$siteMapName));
				if(!$bExistSiteMap)
				{?>
					<td><?=GetMessage("NEXT_MODULE_FILENAME", array("#FILE#" => $siteMapName))?></td>
					<td><?=GetMessage("NEXT_MODULE_NOT_EXISTS")?></td>
				<?}?>
				<?
				if($arTab["HAS_REGIONS"])
				{
					if($arTab["ITEMS"])
					{
						foreach($arTab["ITEMS"] as $arItem):?>
							<tr>
							<td><?=GetMessage("NEXT_MODULE_SITEMAP_DOMAIN", array("#DOMAIN#" => $arItem["PROPERTY_MAIN_DOMAIN_VALUE"]))?></td>
							<?$href = (CMain::isHTTPS() ? "https://" : "http://").$arItem["PROPERTY_MAIN_DOMAIN_VALUE"]."/".$siteMapName;?>
							<td style="width:50%;"><a href="<?=$href;?>" target="_blank"><?=$href;?></a></td>
							</tr>
						<?endforeach;
					}
				}
				else
				{?>
					<tr>
						<td style="width:100%;text-align:center;" colspan="2">
							<div class="adm-info-message"><?=GetMessage("NEXT_MODULE_SITEMAP_ERROR");?></div>
						</td>
					</tr>
				<?}?>
			<?}
		}?>
		<?
		if($REQUEST_METHOD == "POST" && strlen($Generate.$Apply.$RestoreDefaults) && check_bitrix_sessid())
		{
			if(strlen($Update) && strlen($_REQUEST["back_url_settings"]))
				LocalRedirect($_REQUEST["back_url_settings"]);
			elseif(!$arErrors)
				LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
		}?>
			<?$tabControl->Buttons();?>
			<?if($bShowGenerate):?>
				<input <?if($RIGHT < "W") echo "disabled"?> type="submit" name="Generate" class="submit-btn adm-btn-save" value="<?=GetMessage("NEXT_MODULE_GENERATE_SITEMAP")?>" title="<?=GetMessage("NEXT_MODULE_GENERATE_SITEMAP")?>">
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