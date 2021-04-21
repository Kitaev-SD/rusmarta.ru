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

	$by = "id";
	$sort = "asc";

	$arSites = array();
	$db_res = CSite::GetList($by, $sort, array("ACTIVE"=>"Y"));
	while($res = $db_res->Fetch()){
		$arSites[] = $res;
	}

	$arTabs = array();
	foreach($arSites as $key => $arSite){
		$arBackParametrs = $moduleClass::GetBackParametrsValues($arSite["ID"], false);
		$arTabs[] = array(
			"DIV" => "edit".($key+1),
			"TAB" => GetMessage("MAIN_OPTIONS_SITE_TITLE", array("#SITE_NAME#" => $arSite["NAME"], "#SITE_ID#" => $arSite["ID"])),
			"ICON" => "settings",
			// "TITLE" => GetMessage("MAIN_OPTIONS_TITLE"),
			"PAGE_TYPE" => "site_settings",
			"SITE_ID" => $arSite["ID"],
			"SITE_DIR" => $arSite["DIR"],
			"TEMPLATE" => CNext::GetSiteTemplate($arSite["ID"]),
			"OPTIONS" => $arBackParametrs,
		);
	}

	$tabControl = new CAdminTabControl("tabControl", $arTabs);

	if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) > 0 && $RIGHT >= "W" && check_bitrix_sessid()){
		global $APPLICATION, $CACHE_MANAGER;

		if(strlen($RestoreDefaults) > 0){
			Option::delete(CNext::moduleID);
			Option::delete(CNext::moduleID, array("name" => "NeedGenerateCustomTheme"));
			Option::delete(CNext::moduleID, array("name" => "NeedGenerateCustomThemeBG"));
			$APPLICATION->DelGroupRight(CNext::moduleID);
		}
		else{
			Option::delete(CNext::moduleID, array("name" => "sid"));
			unset($_SESSION['THEME']);

			foreach($arTabs as $key => $arTab){
				$optionsSiteID = $arTab["SITE_ID"];
				foreach($moduleClass::$arParametrsList as $blockCode => $arBlock){
					foreach($arBlock["OPTIONS"] as $optionCode => $arOption){
						if($arOption['TYPE'] === 'array'){
							$arOptionsRequiredKeys = array();
							$arOptionsKeys = array_keys($arOption['OPTIONS']);
							$itemsKeysCount = Option::get($moduleID, $optionCode, '0', $optionsSiteID);
							$fullKeysCount = 0;

							if($arOption['OPTIONS'] && is_array($arOption['OPTIONS']))
							{
								foreach($arOption['OPTIONS'] as $_optionCode => $_arOption){
									if(strlen($_arOption['REQUIRED']) && $_arOption['REQUIRED'] === 'Y')
										$arOptionsRequiredKeys[] = $_optionCode;

								}
								for($itemKey = 0, $cnt = $itemsKeysCount + 50; $itemKey <= $cnt; ++$itemKey){
									$bFull = true;
									if($arOptionsRequiredKeys){
										foreach($arOptionsRequiredKeys as $_optionCode){
											if(!strlen($_REQUEST[$optionCode.'_array_'.$_optionCode.'_'.$itemKey.'_'.$optionsSiteID]))
											{
												$bFull = false;
												break;
											}
										}
									}
									if($bFull){
										foreach($arOptionsKeys as $_optionCode){
											$newOptionValue = $_REQUEST[$optionCode.'_array_'.$_optionCode.'_'.$itemKey.'_'.$optionsSiteID];
											Option::set($moduleID, $optionCode.'_array_'.$_optionCode.'_'.$fullKeysCount, $newOptionValue, $optionsSiteID);
											unset($_REQUEST[$optionCode.'_array_'.$_optionCode.'_'.$itemKey.'_'.$optionsSiteID]);
											unset($_FILES[$optionCode.'_array_'.$_optionCode.'_'.$itemKey.'_'.$optionsSiteID]);
										}

										++$fullKeysCount;
									}
								}
							}

							Option::set($moduleID, $optionCode, $fullKeysCount, $optionsSiteID);
						}
						else{
							if($optionCode == "BASE_COLOR_CUSTOM" || $optionCode == 'CUSTOM_BGCOLOR_THEME')
								$moduleClass::CheckColor($_REQUEST[$optionCode."_".$optionsSiteID]);

							if($optionCode == "BASE_COLOR" && $_REQUEST[$optionCode."_".$optionsSiteID] === 'CUSTOM')
								Option::set($moduleID, "NeedGenerateCustomTheme", 'Y', $optionsSiteID);

							if($optionCode == "BGCOLOR_THEME" && $_REQUEST[$optionCode."_".$optionsSiteID] === 'CUSTOM')
								Option::set($moduleID, "NeedGenerateCustomThemeBG", 'Y', $optionsSiteID);

							$newVal = $_REQUEST[$optionCode."_".$optionsSiteID];

							if($arOption["TYPE"] == "checkbox"){
								if(!strlen($newVal) || $newVal != "Y")
									$newVal = "N";

								if(isset($arOption['DEPENDENT_PARAMS']) && $arOption['DEPENDENT_PARAMS'])
								{
									foreach($arOption['DEPENDENT_PARAMS'] as $keyOption => $arOtionValue)
									{
										if(isset($arTab["OPTIONS"][$keyOption]))
										{
											$newDependentVal = $_REQUEST[$keyOption."_".$optionsSiteID];
											if((!strlen($newDependentVal) || $newDependentVal != "Y") && $arOtionValue["TYPE"] == "checkbox"){
												$newDependentVal = "N";
											}

											if($keyOption == "YA_COUNTER_ID" && strlen($newDependentVal))
												$newDependentVal = str_replace('yaCounter', '', $newDependentVal);

											Option::set($moduleID, $keyOption, $newDependentVal, $optionsSiteID);
										}
									}
								}
							}elseif($arOption["TYPE"] == "file"){
								$arValueDefault = serialize(array());
								$newVal = unserialize(COption::GetOptionString($moduleID, $optionCode, $arValueDefault, $optionsSiteID));
								if(isset($_REQUEST[$optionCode."_".$optionsSiteID.'_del']) || (isset($_FILES[$optionCode."_".$optionsSiteID]) && strlen($_FILES[$optionCode."_".$optionsSiteID]['tmp_name']['0']))){
									$arValues = $newVal;
									$arValues = (array)$arValues;
									foreach($arValues as $fileID){
										CFile::Delete($fileID);
									}
									$newVal = serialize(array());
								}

								if(isset($_FILES[$optionCode."_".$optionsSiteID]) && (strlen($_FILES[$optionCode."_".$optionsSiteID]['tmp_name']['n0']) || strlen($_FILES[$optionCode."_".$optionsSiteID]['tmp_name']['0']))){
									$arValues = array();
									$absFilePath = (strlen($_FILES[$optionCode."_".$optionsSiteID]['tmp_name']['n0']) ? $_FILES[$optionCode."_".$optionsSiteID]['tmp_name']['n0'] : $_FILES[$optionCode."_".$optionsSiteID]['tmp_name']['0']);
									$arOriginalName = (strlen($_FILES[$optionCode."_".$optionsSiteID]['name']['n0']) ? $_FILES[$optionCode."_".$optionsSiteID]['name']['n0'] : $_FILES[$optionCode."_".$optionsSiteID]['name']['0']);
									if(file_exists($absFilePath)){
										$arFile = CFile::MakeFileArray($absFilePath);
										$arFile['name'] = $arOriginalName; // for original file extension

										if($bIsIco = strpos($arOriginalName, '.ico') !== false){
											$script_files = COption::GetOptionString("fileman", "~script_files", "php,php3,php4,php5,php6,phtml,pl,asp,aspx,cgi,dll,exe,ico,shtm,shtml,fcg,fcgi,fpl,asmx,pht,py,psp,var");
											$arScriptFiles = explode(',', $script_files);
											if(($p = array_search('ico', $arScriptFiles)) !== false)
												unset($arScriptFiles[$p]);

											$tmp = implode(',', $arScriptFiles);
											Option::set("fileman", "~script_files", $tmp);
										}

										if($fileID = CFile::SaveFile($arFile, $moduleClass))
											$arValues[] = $fileID;

										if($bIsIco)
											Option::set("fileman", "~script_files", $script_files);
									}
									$newVal = serialize($arValues);
								}

								if(!isset($_FILES[$optionCode."_".$optionsSiteID]) || (!strlen($_FILES[$optionCode."_".$optionsSiteID]['tmp_name']['n0']) && !strlen($_FILES[$optionCode."_".$optionsSiteID]['tmp_name']['0']) && !isset($_REQUEST[$optionCode."_".$optionsSiteID.'_del']))){
									//return;
								}

								if($optionCode === 'FAVICON_IMAGE')
									$moduleClass::CopyFaviconToSiteDir($newVal, $optionsSiteID); //copy favicon for search bots

								if(is_array($newVal))
									$newVal = serialize($newVal);
								Option::set($moduleID, $optionCode, $newVal, $optionsSiteID);
								unset($arTab["OPTIONS"][$optionCode]);
							}elseif($arOption["TYPE"] == "selectbox" && (isset($arOption["SUB_PARAMS"]) && $arOption["SUB_PARAMS"])){
								if(isset($arOption["LIST"]) && $arOption["LIST"]){
									$arSubValues = array();
									foreach($arOption["LIST"] as $key2 => $value) {
										if($arOption["SUB_PARAMS"][$key2] && $key2 == $newVal){
											foreach($arOption["SUB_PARAMS"][$key2] as $key3 => $arSubValue){
												if($_REQUEST[$key2."_".$key3."_".$optionsSiteID])
												{
													$arSubValues[$key3] = $_REQUEST[$key2."_".$key3."_".$optionsSiteID];
													unset($arTab["OPTIONS"][$key2."_".$key3]);
												}
												elseif($arTab["OPTIONS"][$key2."_".$key3])
												{
													if($arSubValue["TYPE"] == "checkbox" && $key2 == $newVal && !isset($arSubValue["VISIBLE"]))
														$arSubValues[$key3] = "N";

													unset($arTab["OPTIONS"][$key2."_".$key3]);
												}

											}
										}
									}
									if($arSubValues)
									{
										Option::set($moduleID, "NESTED_OPTIONS_".$optionCode."_".$newVal, serialize($arSubValues), $optionsSiteID);
									}
								}
							}elseif($arOption["TYPE"] == "multiselectbox"){
								$newVal = @implode(",", $newVal);
							}

							if($arOption["TYPE"] != "file")
								$arTab["OPTIONS"][$optionCode] = $newVal;

							Option::set($moduleID, $optionCode, $newVal, $optionsSiteID);
						}
					}
				}

				CBitrixComponent::clearComponentCache('bitrix:catalog.element', $optionsSiteID);
				CBitrixComponent::clearComponentCache('bitrix:form.result.new', $optionsSiteID);
				CBitrixComponent::clearComponentCache('bitrix:catalog.section', $optionsSiteID);
				CBitrixComponent::clearComponentCache('bitrix:catalog.bigdata.products', $optionsSiteID);
				CBitrixComponent::clearComponentCache('bitrix:catalog.store.amount', $optionsSiteID);
				CBitrixComponent::clearComponentCache('bitrix:menu', $optionsSiteID);
				$arTabs[$key] = $arTab;
			}
		}

		// clear composite cache
		if($compositeMode = $moduleClass::IsCompositeEnabled()){
			$obCache = new CPHPCache();
			$obCache->CleanDir('', 'html_pages');
			$moduleClass::EnableComposite($compositeMode === 'AUTO_COMPOSITE');
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
		<?$tabControl->Begin();?>

		<form method="post" class="next_options" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post();?>
		<?CModule:: IncludeModule('sale');
		$arPersonTypes = $arDeliveryServices = $arPaySystems = $arCurrency = $arOrderPropertiesByPerson = $arS = $arC = $arN = array();
		$dbRes = CSalePersonType::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'), false, false, array());
		while($arItem = $dbRes->Fetch()){
			if($arItem['LIDS'] && is_array($arItem['LIDS'])){
				foreach($arItem['LIDS'] as $site_id){
					$arPersonTypes[$site_id][$arItem['ID']] = '['.$arItem['ID'].'] '.$arItem['NAME'].' ('.$site_id.')';
				}
			}
			$arS[$arItem['ID']] = array('FIO', 'PHONE', 'EMAIL');
			$arN[$arItem['ID']] = array(
				'FIO' => GetMessage('ONECLICKBUY_PROPERTIES_FIO'),
				'PHONE' => GetMessage('ONECLICKBUY_PROPERTIES_PHONE'),
				'EMAIL' => GetMessage('ONECLICKBUY_PROPERTIES_EMAIL'),
			);
		}

		foreach($arTabs as $key => $arTab){
			if($arTab["SITE_ID"]){
				$dbRes = CSaleDelivery::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y', 'LID' => $arTab["SITE_ID"]), false, false, array());
				while($arItem = $dbRes->Fetch()){
					$arDeliveryServices[$arTab["SITE_ID"]][$arItem['ID']] = '['.$arItem['ID'].'] '.$arItem['NAME'].' ('.$arTab["SITE_ID"].')';
				}
			}
		}

		$dbRes = CSalePaySystem::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'), false, false, array());
		while($arItem = $dbRes->Fetch()){
			$arPaySystems[$arItem['ID']] = '['.$arItem['ID'].'] '.$arItem['NAME'];
		}

		$dbRes = CCurrency::GetList(($by = "sort"), ($order = "asc"), LANGUAGE_ID);
		while($arItem = $dbRes->Fetch()){
			$arCurrency[$arItem['CURRENCY']] = $arItem['FULL_NAME'].' ('.$arItem['CURRENCY'].')';
		}

		$dbRes = CSaleOrderProps::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'), false, false, array('ID', 'CODE', 'NAME', 'PERSON_TYPE_ID', 'TYPE', 'IS_PHONE', 'IS_EMAIL', 'IS_PAYER'));
		while($arItem = $dbRes->Fetch()){
			if($arItem['TYPE'] === 'TEXT' || $arItem['TYPE'] === 'FILE' && strlen($arItem['CODE'])){
				$arN[$arItem['PERSON_TYPE_ID']][$arItem['CODE']] = $arItem['NAME'];
				if($arItem['IS_PAYER'] === 'Y'){
					$arS[$arItem['PERSON_TYPE_ID']][0] = $arItem['CODE'];
				}
				elseif($arItem['IS_PHONE'] === 'Y'){
					$arS[$arItem['PERSON_TYPE_ID']][1] = $arItem['CODE'];
				}
				elseif($arItem['IS_EMAIL'] === 'Y'){
					$arS[$arItem['PERSON_TYPE_ID']][2] = $arItem['CODE'];
				}
				else{
					$arS[$arItem['PERSON_TYPE_ID']][] = $arItem['CODE'];
				}
			}
		}
		if($arS && $arN){
			foreach($arS as $PERSON_TYPE_ID => $arCodes){
				if($arCodes){
					foreach($arCodes as $CODE){
						$arOrderPropertiesByPerson[$PERSON_TYPE_ID][$CODE] = $arN[$PERSON_TYPE_ID][$CODE];
					}
					$arOrderPropertiesByPerson[$PERSON_TYPE_ID]['COMMENT'] = GetMessage('ONECLICKBUY_PROPERTIES_COMMENT');
				}
			}
		}?>
		<?
		foreach($arTabs as $key => $arTab){
			$tabControl->BeginNextTab();
			if($arTab["SITE_ID"]){
				$optionsSiteID = $arTab["SITE_ID"];
				foreach($moduleClass::$arParametrsList as $blockCode => $arBlock)
				{?>
					<tr class="heading"><td colspan="2"><?=$arBlock["TITLE"]?></td></tr>
					<?
					foreach($arBlock["OPTIONS"] as $optionCode => $arOption)
					{
						if(array_key_exists($optionCode, $arTab["OPTIONS"]) || $arOption["TYPE"] == 'note' || $arOption["TYPE"] == 'includefile')
						{
							$arControllerOption = CControllerClient::GetInstalledOptions($module_id);
							if($optionCode === "ONECLICKBUY_PERSON_TYPE"){
								$arOption['LIST'] = $arPersonTypes[$arTab["SITE_ID"]];
							}
							elseif($optionCode === "ONECLICKBUY_DELIVERY"){
								$arOption['LIST'] = $arDeliveryServices[$arTab["SITE_ID"]];
							}
							elseif($optionCode === "ONECLICKBUY_PAYMENT"){
								$arOption['LIST'] = $arPaySystems;
							}
							elseif($optionCode === "ONECLICKBUY_CURRENCY"){
								$arOption['LIST'] = $arCurrency;
							}
							elseif($optionCode === "ONECLICKBUY_PROPERTIES" || $optionCode === "ONECLICKBUY_REQUIRED_PROPERTIES"){
								$arOption['LIST'] = $arOrderPropertiesByPerson[Option::get($moduleID, 'ONECLIKBUY_PERSON_TYPE', ($arPersonTypes ? key($arPersonTypes[$arTab["SITE_ID"]]) : ''), $arTab["SITE_ID"])];
							}
							if($arOption['TYPE'] === 'array'){
								$itemsKeysCount = Option::get($moduleID, $optionCode, 0, $optionsSiteID);
								if($arOption['OPTIONS'] && is_array($arOption['OPTIONS'])){
									$arOptionsKeys = array_keys($arOption['OPTIONS']);
									?>
									<tr>
										<td style="text-align:center;" colspan="2"><?=$arOption["TITLE"]?></td>
									</tr>
									<tr>
										<td colspan="2">
											<table class="aspro-admin-item-table">
												<tr style="text-align:center;">
													<?
													for($itemKey = 0, $cnt = $itemsKeysCount; $itemKey <= $cnt; ++$itemKey){
														$_arParameters = array();
														foreach($arOptionsKeys as $_optionKey){
															$_arParameters[$optionCode.'_array_'.$_optionKey.'_'.($itemKey != $cnt ? $itemKey : 'new')] = $arOption['OPTIONS'][$_optionKey];
															if(!$itemKey){
																?><th colspan="2"><?=$arOption['OPTIONS'][$_optionKey]['TITLE']?></th><?
															}
														}
														?>
												</tr>
												<tr class="aspro-admin-item<?=(!$itemKey ? ' first' : '')?><?=($itemKey == $itemsKeysCount - 1 ? ' last' : '')?><?=($itemKey == $cnt ? ' new' : '')?>" data-itemkey="<?=$itemKey?>" style="text-align:center;"><?
														foreach($_arParameters as $_optionCode => $_arOption){
															$moduleClass::ShowAdminRow($_optionCode, $_arOption, $arTab, $arControllerOption);
														}
														?><td class="rowcontrol"><span class="up"></span><span class="down"></span><span class="remove"></span></td></tr><?
													}
													?>
												<tr style="text-align:center;">
													<td><a href="javascript:;" class="adm-btn adm-btn-save adm-btn-add" title="<?=GetMessage('PRIME_OPTIONS_ADD_BUTTON_TITLE')?>"><?=GetMessage('OPTIONS_ADD_BUTTON_TITLE')?></a></td>
												</tr>
											</table>
										</td>
									</tr><?
								}
							}
							else{
								if($arOption["TYPE"] == 'note')
								{
									if($optionCode === 'CONTACTS_EDIT_LINK_NOTE'){
										$contactsHref = str_replace('//', '/', $arTab['SITE_DIR'].'/contacts/?bitrix_include_areas=Y');
										$arOption["TITLE"] = GetMessage('CONTACTS_OPTIONS_EDIT_LINK_NOTE', array('#CONTACTS_HREF#' => $contactsHref));
									}
									?>
									<tr data-option_code="<?=$optionCode;?>">
										<td colspan="2" align="center">
											<?=BeginNote('align="center" name="'.htmlspecialcharsbx($optionCode)."_".$optionsSiteID.'"');?>
											<?=$arOption["TITLE"]?>
											<?=EndNote();?>
										</td>
									</tr>
									<?
								}
								else{
									$optionName = $arOption["TITLE"];
									$optionType = $arOption["TYPE"];
									$optionList = $arOption["LIST"];
									$optionDefault = $arOption["DEFAULT"];
									$optionVal = $arTab["OPTIONS"][$optionCode];
									$optionSize = $arOption["SIZE"];
									$optionCols = $arOption["COLS"];
									$optionRows = $arOption["ROWS"];
									$optionChecked = $optionVal == "Y" ? "checked" : "";
									$optionDisabled = isset($arControllerOption[$optionCode]) || array_key_exists("DISABLED", $arOption) && $arOption["DISABLED"] == "Y" ? "disabled" : "";
									$optionSup_text = array_key_exists("SUP", $arOption) ? $arOption["SUP"] : "";
									$optionController = isset($arControllerOption[$optionCode]) ? "title='".GetMessage("MAIN_ADMIN_SET_CONTROLLER_ALT")."'" : "";
									$style = "";
									if(($optionCode == 'BGCOLOR_THEME' || $optionCode == 'CUSTOM_BGCOLOR_THEME') && $arTab["OPTIONS"]['SHOW_BG_BLOCK'] != 'Y')
									{
										$style = "style=display:none;";
									}
									?>
									<tr data-optioncode="<?=$optionCode;?>" <?=$style;?>>
										<?=$moduleClass::ShowAdminRow($optionCode, $arOption, $arTab, $arControllerOption);?>
									</tr>
									<?if(isset($arOption['SUB_PARAMS']) && $arOption['SUB_PARAMS'] && (isset($arOption['LIST']) && $arOption['LIST'])): //nested params?>
										<?foreach($arOption['LIST'] as $key => $value):?>
											<?foreach((array)$arOption['SUB_PARAMS'][$key] as $key2 => $arValue)
											{
												if(isset($arValue['VISIBLE']) && $arValue['VISIBLE'] == 'N')
													unset($arOption['SUB_PARAMS'][$key][$key2]);
											}
											if($arOption['SUB_PARAMS'][$key]):?>
												<tr data-parent='<?=$optionCode."_".$arTab["SITE_ID"]?>' class="block <?=$key?>" <?=($key == $arTab["OPTIONS"][$optionCode] ? "style='display:table-row'" : "style='display:none'");?>>
													<?if($arOption['SUB_PARAMS'][$key]):?><td style="text-align:center;" colspan="2"><?=GetMessage('SUB_PARAMS');?></td><?endif;?>
												</tr>
												<?foreach((array)$arOption['SUB_PARAMS'][$key] as $key2 => $arValue):
													if($arValue['VISIBLE'] != 'N'):?>
														<tr data-parent='<?=$optionCode."_".$arTab["SITE_ID"]?>' class="block <?=$key?>" <?=($key == $arTab["OPTIONS"][$optionCode] ? "style='display:table-row'" : "style='display:none'");?>><?=$moduleClass::ShowAdminRow($key.'_'.$key2, $arValue, $arTab, $arControllerOption);?></tr>
													<?endif;?>
												<?endforeach;?>
											<?endif;?>
										<?endforeach;?>
									<?endif;?>
									<?if(isset($arOption['DEPENDENT_PARAMS']) && $arOption['DEPENDENT_PARAMS']): //dependent params?>
										<?foreach($arOption['DEPENDENT_PARAMS'] as $key => $arValue):?>
											<?if(!isset($arValue['CONDITIONAL_VALUE']) || ($arValue['CONDITIONAL_VALUE'] && $arTab["OPTIONS"][$optionCode] == $arValue['CONDITIONAL_VALUE']))
												$style = "style='display:table-row'";
											else
												$style = "style='display:none'";
											?>
											<tr data-optioncode="<?=$key;?>" class="depend-block <?=$key?>" <?=((isset($arValue['CONDITIONAL_VALUE']) && $arValue['CONDITIONAL_VALUE']) ? "data-show='".$arValue['CONDITIONAL_VALUE']."'" : "");?> data-parent='<?=$optionCode."_".$arTab["SITE_ID"]?>' <?=$style;?>><?=$moduleClass::ShowAdminRow($key, $arValue, $arTab, $arControllerOption);?></tr>
										<?endforeach;?>
									<?endif;?>
									<?
								}
							}
						}
					}
				}
			}
		}
		?>
		<?
		if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults) && check_bitrix_sessid())
		{
			if(strlen($Update) && strlen($_REQUEST["back_url_settings"]))
				LocalRedirect($_REQUEST["back_url_settings"]);
			else
				LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
		}?>
			<?$tabControl->Buttons();?>
			<input <?if($RIGHT < "W") echo "disabled"?> type="submit" name="Apply" class="submit-btn" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
			<?if(strlen($_REQUEST["back_url_settings"]) > 0): ?>
				<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?=htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
				<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
			<?endif;?>
			<?if(CNext::IsCompositeEnabled()):?>
				<div class="adm-info-message"><?=GetMessage("WILL_CLEAR_HTML_CACHE_NOTE")?></div><div style="clear:both;"></div>
			<?endif;?>
			<script type="text/javascript">
				var arOrderPropertiesByPerson = <?=CUtil::PhpToJSObject($arOrderPropertiesByPerson, false)?>;
				$(document).ready(function() {
					$('input[name^="THEME_SWITCHER"]').change(function() {
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							if(!confirm("<?=GetMessage("NO_COMPOSITE_NOTE")?>")){
								$(this).removeAttr('checked');
							}
						}
					});

					$('select[name^="SCROLLTOTOP_TYPE"]').change(function() {
						var posSelect = $(this).parents('table').first().find('select[name^="SCROLLTOTOP_POSITION"]');
						if(posSelect){
							var posSelectTr = posSelect.parents('tr').first();
							var isNone = $(this).val().indexOf('NONE') != -1;
							if(isNone){
								if(posSelectTr.is(':visible')){
									posSelectTr.hide();
								}
							}
							else{
								if(!posSelectTr.is(':visible')){
									posSelectTr.show();
								}
								var isRound = $(this).val().indexOf('ROUND') != -1;
								var isTouch = posSelect.val().indexOf('TOUCH') != -1;
								if(isRound && !!posSelect){
									posSelect.find('option[value^="TOUCH"]').attr('disabled', 'disabled');
									if(isTouch){
										posSelect.val(posSelect.find('option[value^="PADDING"]').first().attr('value'));
									}
								}
								else{
									posSelect.find('option[value^="TOUCH"]').removeAttr('disabled');
								}
							}
						}
					});

					$('select[name^="PAGE_CONTACTS"]').change(function() {
						var value = $(this).val();
						if(value == 'custom'){
							$(this).parents('table').find('[name^="CONTACTS_EDIT_LINK_NOTE"]').closest('tr').hide();
							$(this).parents('table').find('[name^="CONTACTS_ADDRESS"]').closest('tr').hide();
							$(this).parents('table').find('[name^="CONTACTS_PHONE"]').closest('tr').hide();
							$(this).parents('table').find('[name^="CONTACTS_REGIONAL_PHONE"]').closest('tr').hide();
							$(this).parents('table').find('[name^="CONTACTS_EMAIL"]').closest('tr').hide();
							$(this).parents('table').find('[name^="CONTACTS_SCHEDULE"]').closest('tr').hide();
							$(this).parents('table').find('[name^="CONTACTS_DESCRIPTION"]').closest('tr').hide();
							$(this).parents('table').find('[name^="CONTACTS_REGIONAL_DESCRIPTION"]').closest('tr').hide();
							$(this).parents('table').find('[name^="CONTACTS_USE_FEEDBACK"]').closest('tr').hide();
							$(this).parents('table').find('[name^="CONTACTS_USE_MAP"]').first().closest('tr').hide();
							$(this).parents('table').find('[name^="CONTACTS_MAP"]').first().closest('tr').hide();
							$(this).parents('table').find('[name^="CONTACTS_MAP_NOTE"]').closest('tr').hide();
						}
						else{
							$(this).parents('table').find('[name^="CONTACTS_EDIT_LINK_NOTE"]').closest('tr').show();
							$(this).parents('table').find('[name^="CONTACTS_EMAIL"]').closest('tr').show();
							$(this).parents('table').find('[name^="CONTACTS_USE_MAP"]').first().closest('tr').show();

							if($(this).val() < 3){
								$(this).parents('table').find('[name^="CONTACTS_PHONE"]').closest('tr').show();
								$(this).parents('table').find('[name^="CONTACTS_REGIONAL_PHONE"]').closest('tr').hide();
								$(this).parents('table').find('[name^="CONTACTS_SCHEDULE"]').closest('tr').show();
								$(this).parents('table').find('[name^="CONTACTS_DESCRIPTION12"]').closest('tr').show();
								$(this).parents('table').find('[name^="CONTACTS_REGIONAL_DESCRIPTION34"]').closest('tr').hide();
								$(this).parents('table').find('[name^="CONTACTS_REGIONAL_DESCRIPTION5"]').closest('tr').hide();
								$(this).parents('table').find('[name^="CONTACTS_USE_FEEDBACK"]').closest('tr').show();
								$(this).parents('table').find('[name^="CONTACTS_MAP"]').first().closest('tr').show();
								$(this).parents('table').find('[name^="CONTACTS_MAP_NOTE"]').closest('tr').show();
							}
							else{
								$(this).parents('table').find('[name^="CONTACTS_PHONE"]').closest('tr').show();
								$(this).parents('table').find('[name^="CONTACTS_REGIONAL_PHONE"]').closest('tr').hide();
								$(this).parents('table').find('[name^="CONTACTS_SCHEDULE"]').closest('tr').hide();
								if(value < 5){
									$(this).parents('table').find('[name^="CONTACTS_ADDRESS"]').closest('tr').show();
									$(this).parents('table').find('[name^="CONTACTS_DESCRIPTION12"]').closest('tr').hide();
									$(this).parents('table').find('[name^="CONTACTS_REGIONAL_DESCRIPTION34"]').closest('tr').show();
									$(this).parents('table').find('[name^="CONTACTS_REGIONAL_DESCRIPTION5"]').closest('tr').hide();
									$(this).parents('table').find('[name^="CONTACTS_USE_FEEDBACK"]').closest('tr').show();
								}
								else{
									$(this).parents('table').find('[name^="CONTACTS_ADDRESS"]').closest('tr').hide();
									$(this).parents('table').find('[name^="CONTACTS_DESCRIPTION12"]').closest('tr').hide();
									$(this).parents('table').find('[name^="CONTACTS_REGIONAL_DESCRIPTION34"]').closest('tr').hide();
									$(this).parents('table').find('[name^="CONTACTS_REGIONAL_DESCRIPTION5"]').closest('tr').show();
									$(this).parents('table').find('[name^="CONTACTS_USE_FEEDBACK"]').closest('tr').hide();
								}
								$(this).parents('table').find('[name^="CONTACTS_MAP"]').first().closest('tr').hide();
								$(this).parents('table').find('[name^="CONTACTS_MAP_NOTE"]').closest('tr').hide();
							}
						}
					});

					$('.aspro-admin-item-table .adm-btn-add').click(function() {
						var $table = $(this).closest('.aspro-admin-item-table');
						var $newItem = $table.find('.aspro-admin-item.new');
						if($newItem.length){
							var lastItemKey = $table.find('.aspro-admin-item.last').length ? $table.find('.aspro-admin-item.last').attr('data-itemkey') * 1 : -1;
							var $clone = $newItem.clone().insertBefore($newItem).removeClass('new');
							$clone.attr('data-itemkey', lastItemKey + 1);
							$clone.find('td:not(.rowcontrol)').each(function(i) {
								var name = $(this).find('*[name]:first-of-type').attr('name');
								var newName = name.replace('_new_', '_' + (lastItemKey + 1) + '_');
								$(this).find('*[name]:first-of-type').attr('name', newName);
							});
						}
						$table.find('.aspro-admin-item').removeClass('first, last');
						$table.find('.aspro-admin-item:not(.new)').first().addClass('first');
						$table.find('.aspro-admin-item:not(.new)').last().addClass('last');
					});

					$(document).on('click', '.rowcontrol>span', function() {
						var action = ($(this).hasClass('up') ? 'up' : ($(this).hasClass('down') ? 'down' : 'remove'));
						var $table = $(this).closest('.aspro-admin-item-table');
						var $item = $(this).closest('.aspro-admin-item');
						var itemKey = $item.attr('data-itemkey');

						if(action === 'up'){
							var prevItemKey = $item.prev().attr('data-itemkey');
							$item.find('td:not(.rowcontrol)').each(function(i) {
								var name = $(this).find('*[name]:first-of-type').attr('name');
								if(typeof(name) !== 'undefined'){
									var newName = name.replace('_' + itemKey + '_', '_' + prevItemKey + '_');
									$(this).find('*[name]:first-of-type').attr('name', newName);
									var name = $item.prev().find('td:not(.rowcontrol)').eq(i).find('*[name]:first-of-type').attr('name');
									var newName = name.replace('_' + prevItemKey + '_', '_' + itemKey + '_');
									$item.prev().find('td:not(.rowcontrol)').eq(i).find('*[name]:first-of-type').attr('name', newName);
								}
							});
							$item.attr('data-itemkey', prevItemKey);
							$item.prev().attr('data-itemkey', itemKey);
							$item.clone().insertBefore($item.prev());
						}
						else if(action === 'down'){
							var nextItemKey = $item.next().attr('data-itemkey');
							$item.find('td:not(.rowcontrol)').each(function(i) {
								var name = $(this).find('*[name]:first-of-type').attr('name');
								if(typeof(name) !== 'undefined'){
									var newName = name.replace('_' + itemKey + '_', '_' + nextItemKey + '_');
									$(this).find('*[name]:first-of-type').attr('name', newName);
									var name = $item.next().find('td:not(.rowcontrol)').eq(i).find('*[name]:first-of-type').attr('name');
									var newName = name.replace('_' + nextItemKey + '_', '_' + itemKey + '_');
									$item.next().find('td:not(.rowcontrol)').eq(i).find('*[name]:first-of-type').attr('name', newName);
								}
							});
							$item.attr('data-itemkey', nextItemKey);
							$item.next().attr('data-itemkey', itemKey);
							$item.clone().insertAfter($item.next());
						}
						$item.detach();
						$table.find('.aspro-admin-item').removeClass('first').removeClass('last');
						$table.find('.aspro-admin-item:not(.new)').first().addClass('first');
						$table.find('.aspro-admin-item:not(.new)').last().addClass('last');
					});

					$('select[name^="SCROLLTOTOP_TYPE"]').change();
					$('select[name^="PAGE_CONTACTS"]').change();
				});
				function CheckActive(){
					$('input[name^="USE_WORD_EXPRESSION"]').each(function() {
						var input = this;
						var isActiveUseExpressions = $(input).attr('checked') == 'checked';
						var tab = $(input).parents('.adm-detail-content-item-block');
						if(!isActiveUseExpressions){
							tab.find('input[name^="MAX_AMOUNT"]').attr('disabled', 'disabled');
							tab.find('input[name^="MIN_AMOUNT"]').attr('disabled', 'disabled');
							tab.find('input[name^="EXPRESSION_FOR_MIN"]').attr('disabled', 'disabled');
							tab.find('input[name^="EXPRESSION_FOR_MAX"]').attr('disabled', 'disabled');
							tab.find('input[name^="EXPRESSION_FOR_MID"]').attr('disabled', 'disabled');
						}
						else{
							tab.find('input[name^="MAX_AMOUNT"]').removeAttr('disabled');
							tab.find('input[name^="MIN_AMOUNT"]').removeAttr('disabled');
							tab.find('input[name^="EXPRESSION_FOR_MIN"]').removeAttr('disabled');
							tab.find('input[name^="EXPRESSION_FOR_MAX"]').removeAttr('disabled');
							tab.find('input[name^="EXPRESSION_FOR_MID"]').removeAttr('disabled');
						}
					});

					$('select[name^="BUYMISSINGGOODS"]').each(function() {
						var select = this;
						var BuyMissingGoodsVal = $(select).val();
						var tab = $(select).parents('.adm-detail-content-item-block');
						tab.find('input[name^="EXPRESSION_SUBSCRIBE_BUTTON"]').attr('disabled', 'disabled');
						tab.find('input[name^="EXPRESSION_SUBSCRIBED_BUTTON"]').attr('disabled', 'disabled');
						tab.find('input[name^="EXPRESSION_ORDER_BUTTON"]').attr('disabled', 'disabled');
						if(BuyMissingGoodsVal == 'SUBSCRIBE'){
							tab.find('input[name^="EXPRESSION_SUBSCRIBE_BUTTON"]').removeAttr('disabled');
							tab.find('input[name^="EXPRESSION_SUBSCRIBED_BUTTON"]').removeAttr('disabled');
						}
						else if(BuyMissingGoodsVal == 'ORDER'){
							tab.find('input[name^="EXPRESSION_ORDER_BUTTON"]').removeAttr('disabled');
						}
					});
				}

				function checkGoalsNote(){
					var inUAC = $('.adm-detail-content-table:visible').first().find('tr input[id^=YA_GOALS]');
					var itrYACID = $('.adm-detail-content-table:visible').first().find('tr.YA_COUNTER_ID');
					var itrGNote = $('.adm-detail-content-table:visible').first().find('tr.GOALS_NOTE');
					var itrUFG = $('.adm-detail-content-table:visible').first().find('tr.USE_FORMS_GOALS');
					var itrUBG = $('.adm-detail-content-table:visible').first().find('tr.USE_BASKET_GOALS');
					var itrU1CG = $('.adm-detail-content-table:visible').first().find('tr.USE_1CLICK_GOALS');
					var itrUQOG = $('.adm-detail-content-table:visible').first().find('tr.USE_FASTORDER_GOALS');
					var itrUFOG = $('.adm-detail-content-table:visible').first().find('tr.USE_FULLORDER_GOALS');
					var itrUDG = $('.adm-detail-content-table:visible').first().find('tr.USE_DEBUG_GOALS');

					if(inUAC.length && inUAC.attr('checked')){
						var bShowNote = 6;

						if(itrUFG.find('select').val().indexOf('NONE') == -1){
							itrGNote.find('[data-goal=form]').show();
						}
						else{
							itrGNote.find('[data-goal=form]').hide();
							--bShowNote;
						}

						if(itrUBG.find('input').attr('checked')){
							itrGNote.find('[data-goal=basket]').show();
						}
						else{
							itrGNote.find('[data-goal=basket]').hide();
							--bShowNote;
						}

						if(itrU1CG.find('input').attr('checked')){
							itrGNote.find('[data-goal=1click]').show();
						}
						else{
							itrGNote.find('[data-goal=1click]').hide();
							--bShowNote;
						}

						if(itrUQOG.find('input').attr('checked')){
							itrGNote.find('[data-goal=fastorder]').show();
						}
						else{
							itrGNote.find('[data-goal=fastorder]').hide();
							--bShowNote;
						}

						if(itrUFOG.find('input').attr('checked')){
							itrGNote.find('[data-goal=fullorder]').show();
						}
						else{
							itrGNote.find('[data-goal=fullorder]').hide();
							--bShowNote;
						}

						if(itrUDG.find('input').attr('checked')){
							itrGNote.find('[data-goal=debug]').show();
						}
						else{
							itrGNote.find('[data-goal=debug]').hide();
							--bShowNote;
						}

						if(bShowNote){
							itrGNote.fadeIn();
						}
						else{
							itrGNote.fadeOut();
						}
					}
					else{
						itrGNote.fadeOut();
					}
				}
			</script>
			<script type="text/javascript">
				$(document).ready(function() {
					CheckActive();
					$('form.next_options').submit(function(e) {
						$(this).attr('id', 'next_options');
						jsAjaxUtil.ShowLocalWaitWindow('id', 'next_options', true);
						$(this).find('input').removeAttr('disabled');
					});
					$('select[name^="INDEX_TYPE"]').change(function() {
						var value = $(this).val()
							sub_block = $('tr.block[data-parent='+$(this).attr('name')+']');
						if(sub_block.length)
						{
							sub_block.css({'display':'none'});
							$('tr.block.'+value+'[data-parent='+$(this).attr('name')+']').css({'display':'table-row'});
						}
					});
					$('input.depend-check').change(function() {
						var ischecked = $(this).prop('checked'),
							depend_block = $('.depend-block[data-parent='+$(this).attr('id')+']');
						if(depend_block.length && $(this).attr('id').indexOf('YA_GOALS') < 0)
						{
							if(typeof(depend_block.data('show')) != 'undefined')
							{
								if(depend_block.data('show') == 'Y')
								{
									if(ischecked)
										depend_block.fadeIn();
									else
										depend_block.fadeOut();
								}
								else
								{
									if(ischecked)
										depend_block.fadeOut();
									else
										depend_block.fadeIn();
								}
							}
						}
					});

				})
				$('select[name^="USE_FORMS_GOALS"]').change(function() {
					var parent = $(this).closest('tr').data('parent');
					var inUAC = $(this).parents('table').first().find('input#'+parent);
					if(inUAC.length && inUAC.attr('checked')){
						var isNone = $(this).val().indexOf('NONE') != -1;
						var isCommon = $(this).val().indexOf('COMMON') != -1;
						var itrGNote = $(this).parents('table').first().find('tr.GOALS_NOTE');
						if(!isNone){
							if(isCommon){
								itrGNote.find('[data-value=common]').show();
								itrGNote.find('[data-value=single]').hide();
							}
							else{
								itrGNote.find('[data-value=common]').hide();
								itrGNote.find('[data-value=single]').show();
							}
							itrGNote.find('[data-goal=form]').show();
						}
						else{
							itrGNote.find('[data-goal=form]').hide();
						}
					}

					checkGoalsNote();
				});
				$('input[name^="USE_BASKET_GOALS"]').change(function() {
					var parent = $(this).closest('tr').data('parent');
					var inUAC = $(this).parents('table').first().find('input#'+parent);
					if(inUAC.length && inUAC.attr('checked')){
						var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							itrGNote.find('[data-goal=basket]').show();
						}
						else{
							itrGNote.find('[data-goal=basket]').hide();
						}
					}

					checkGoalsNote();
				});
				$('input[name^="USE_1CLICK_GOALS"]').change(function() {
					var parent = $(this).closest('tr').data('parent');
					var inUAC = $(this).parents('table').first().find('input#'+parent);
					if(inUAC.length && inUAC.attr('checked')){
						var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							itrGNote.find('[data-goal=1click]').show();
						}
						else{
							itrGNote.find('[data-goal=1click]').hide();
						}
					}

					checkGoalsNote();
				});
				$('input[name^="USE_FASTORDER_GOALS"]').change(function() {
					var parent = $(this).closest('tr').data('parent');
					var inUAC = $(this).parents('table').first().find('input#'+parent);
					if(inUAC.length && inUAC.attr('checked')){
						var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							itrGNote.find('[data-goal=fastorder]').show();
						}
						else{
							itrGNote.find('[data-goal=fastorder]').hide();
						}
					}

					checkGoalsNote();
				});
				$('input[name^="USE_FULLORDER_GOALS"]').change(function() {
					var parent = $(this).closest('tr').data('parent');
					var inUAC = $(this).parents('table').first().find('input#'+parent);
					if(inUAC.length && inUAC.attr('checked')){
						var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							itrGNote.find('[data-goal=fullorder]').show();
						}
						else{
							itrGNote.find('[data-goal=fullorder]').hide();
						}
					}

					checkGoalsNote();
				});
				$('input[name^="USE_DEBUG_GOALS"]').change(function() {
					var parent = $(this).closest('tr').data('parent');
					var inUAC = $(this).parents('table').first().find('input#'+parent);
					if(inUAC.length && inUAC.attr('checked')){
						var itrGNote = $(this).parents('table').first().find('tr[data-optioncode=GOALS_NOTE]');
						var ischecked = $(this).attr('checked');
						if(typeof(ischecked) != 'undefined'){
							itrGNote.find('[data-goal=debug]').show();
						}
						else{
							itrGNote.find('[data-goal=debug]').hide();
						}
					}

					checkGoalsNote();
				});
				$('input[name^="YA_GOALS"]').change(function(){
					var itrYACID = $(this).parents('table').first().find('tr.YA_COUNTER_ID');
					var itrUFG = $(this).parents('table').first().find('tr.USE_FORMS_GOALS');
					var itrUBG = $(this).parents('table').first().find('tr.USE_BASKET_GOALS');
					var itrU1CG = $(this).parents('table').first().find('tr.USE_1CLICK_GOALS');
					var itrUQOG = $(this).parents('table').first().find('tr.USE_FASTORDER_GOALS');
					var itrUFOG = $(this).parents('table').first().find('tr.USE_FULLORDER_GOALS');
					var itrUDG = $(this).parents('table').first().find('tr.USE_DEBUG_GOALS');
					var itrGNote = $(this).parents('table').first().find('tr.GOALS_NOTE');
					var ischecked = $(this).attr('checked');
					if(typeof(ischecked) != 'undefined'){
						itrYACID.fadeIn();
						itrUFG.fadeIn();
						var valUFG = itrUFG.find('select').val();

						if(valUFG.indexOf('NONE') == -1){
							var isCommon = valUFG.indexOf('COMMON') != -1;
							if(isCommon){
								itrGNote.find('[data-value=common]').show();
								itrGNote.find('[data-value=single]').hide();
							}
							else{
								itrGNote.find('[data-value=common]').hide();
								itrGNote.find('[data-value=single]').show();
							}
						}
						itrUBG.fadeIn();
						itrU1CG.fadeIn();
						itrUQOG.fadeIn();
						itrUFOG.fadeIn();
						itrUDG.fadeIn();
					}
					else{
						itrYACID.fadeOut();
						itrUFG.fadeOut();
						itrUBG.fadeOut();
						itrU1CG.fadeOut();
						itrUQOG.fadeOut();
						itrUFOG.fadeOut();
						itrUDG.fadeOut();
						itrGNote.fadeOut();
					}
					checkGoalsNote();
				});

				$('input[name^="USE_WORD_EXPRESSION"], select[name^="BUYMISSINGGOODS"]').change(function() {
					CheckActive();
				});

				$('select[name^="SHOW_SECTION_DESCRIPTION"]').change(function(){
					if($(this).val() != 'BOTH')
						$('select[name*="SECTION_DESCRIPTION_POSITION"]').closest('tr').css('display','none');
					else
						$('select[name*="SECTION_DESCRIPTION_POSITION"]').closest('tr').css('display','');
				});

				$('select[name^="SHOW_QUANTITY_FOR_GROUPS"]').change(function() {
					var val = $(this).val();
					var tab = $(this).parents('.adm-detail-content-item-block');
					var sqcg = tab.find('select[name^="SHOW_QUANTITY_COUNT_FOR_GROUPS"]');

					var isAll = false;
					if(val){
						isAll = val.indexOf('2') !== -1;
					}

					if(!isAll){
						$(this).find('option').each(function() {
							if($(this).attr('selected') != 'selected'){
								sqcg.find('option[value="' + $(this).attr('value') + '"]').removeAttr('selected');
							}
						});
					}
				});

				$('select[name^="SHOW_QUANTITY_COUNT_FOR_GROUPS"]').change(function(e) {
					e.stopPropagation();
					var val = $(this).val();
					var tab = $(this).parents('.adm-detail-content-item-block');
					var sqg_val = tab.find('select[name^="SHOW_QUANTITY_FOR_GROUPS"]').val();

					if(!sqg_val){
						$(this).find('option').removeAttr('selected');
						return;
					}

					var isAll = false;
					if(sqg_val){
						isAll = sqg_val.indexOf('2') !== -1;
					}

					if(!isAll && val){
						for(i in val){
							var g = val[i];
							if(sqg_val.indexOf(g) === -1){
								$(this).find('option[value="' + g + '"]').removeAttr('selected');
							}
						}
					}
				});

				$('select[name^="ONECLICKBUY_PERSON_TYPE"]').change(function() {
					if(typeof arOrderPropertiesByPerson !== 'undefined'){
						var table = $(this).parents('table').first();
						var value = $(this).val();
						if(typeof value !== 'undefined' && typeof arOrderPropertiesByPerson[value] !== 'undefined'){
							var arSelects = [table.find('select[name^=ONECLICKBUY_PROPERTIES]'), table.find('select[name^=ONECLICKBUY_REQUIRED_PROPERTIES]')];
							for(var i in arSelects){
								var $fields = arSelects[i];
								if($fields.length){
									var fields = $fields.val();
									$fields.find('option').remove();
									for(var j in arOrderPropertiesByPerson[value]){
										var selected = '';
										if(fields)
											selected = (fields.indexOf(j) !== -1 ? ' selected="selected"' : '');
										$fields.append('<option value="' + j + '"' + selected + '>' + arOrderPropertiesByPerson[value][j] + '</option>');
									}
									$fields.find('option').eq(0).attr('selected', 'selected');
									$fields.find('option').eq(1).attr('selected', 'selected');
								}
							}
						}
					}
				});

				$('select[name^="ONECLICKBUY_PROPERTIES"]').change(function() {
					var table = $(this).parents('table').first();
					$(this).find('option').eq(0).attr('selected', 'selected');
					$(this).find('option').eq(1).attr('selected', 'selected');
					var fiedsValue = $(this).val();
					var $requiredFields = table.find('select[name^=ONECLICKBUY_REQUIRED_PROPERTIES]');
					var requiredFieldsValue = $requiredFields.val();
					for(var i in requiredFieldsValue){
						if(fiedsValue === null || fiedsValue.indexOf(requiredFieldsValue[i]) === -1){
							$requiredFields.find('option[value=' + requiredFieldsValue[i] + ']').removeAttr('selected');
						}
					}
				});

				$('select[name^="ONECLICKBUY_REQUIRED_PROPERTIES"]').change(function() {
					var table = $(this).parents('table').first();
					$(this).find('option').eq(0).attr('selected', 'selected');
					$(this).find('option').eq(1).attr('selected', 'selected');
					var requiredFieldsValue = $(this).val();
					var $fieds = table.find('select[name^=ONECLICKBUY_PROPERTIES]');
					var fiedsValue = $fieds.val();
					var $FIO = $(this).find('option[value^=FIO]');
					var $PHONE = $(this).find('option[value^=PHONE]');
					for(var i in requiredFieldsValue){
						if(fiedsValue === null || fiedsValue.indexOf(requiredFieldsValue[i]) === -1){
							$(this).find('option[value=' + requiredFieldsValue[i] + ']').removeAttr('selected');
						}
					}
				});

				$('input[name^="USE_GOOGLE_RECAPTCHA"]').change(function(){
					if($(this).attr('checked') != 'checked')
						$(this).closest('.adm-detail-content-table').find('tr[data-optioncode^="GOOGLE_RECAPTCHA"]').each(function(){
							$(this).css('display','none');
						});
					else
						$(this).closest('.adm-detail-content-table').find('tr[data-optioncode^="GOOGLE_RECAPTCHA"]').each(function(){
							$(this).css('display','');
						});
					$('select[name^="GOOGLE_RECAPTCHA_SIZE"]').change();
				});

				$('select[name^="GOOGLE_RECAPTCHA_SIZE"]').change(function() {
					var val = $(this).val();
					var tab = $(this).parents('.adm-detail-content-item-block');
					if(tab.find('input[name^="USE_GOOGLE_RECAPTCHA"]').attr('checked') == 'checked')
					{
						if(val != 'INVISIBLE')
						{
							tab.find('tr[data-optioncode^="GOOGLE_RECAPTCHA_SHOW_LOGO"]').css('display','none');
							tab.find('tr[data-optioncode^="GOOGLE_RECAPTCHA_BADGE"]').css('display','none');
						}
						else
						{
							tab.find('tr[data-optioncode^="GOOGLE_RECAPTCHA_SHOW_LOGO"]').css('display','');
							tab.find('tr[data-optioncode^="GOOGLE_RECAPTCHA_BADGE"]').css('display','');
						}
					}
					else
					{
						tab.find('tr[data-optioncode^="GOOGLE_RECAPTCHA_SHOW_LOGO"]').css('display','none');
						tab.find('tr[data-optioncode^="GOOGLE_RECAPTCHA_BADGE"]').css('display','none');
					}
				})

				$('select[name^="ONECLICKBUY_PERSON_TYPE"]').change();
				$('input[name^="YA_GOALS"]').change();
				$('select[name^="USE_FORMS_GOALS"]').change();
				$('input[name^="USE_BASKET_GOALS"]').change();
				$('input[name^="USE_1CLICK_GOALS"]').change();
				$('input[name^="USE_FASTORDER_GOALS"]').change();
				$('input[name^="USE_FULLORDER_GOALS"]').change();
				$('input[name^="USE_DEBUG_GOALS"]').change();

				$('input[name^="USE_GOOGLE_RECAPTCHA"]').change();
				$('select[name^="GOOGLE_RECAPTCHA_SIZE"]').change();
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