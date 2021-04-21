<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

global $APPLICATION;
IncludeModuleLangFile(__FILE__);

$moduleClass = "CNext";
$moduleID = "aspro.next";
\Bitrix\Main\Loader::includeModule($moduleID);

use \Bitrix\Main\Config\Option,
	\Bitrix\Main\Localization\Loc;

$RIGHT = $APPLICATION->GetGroupRight($moduleID);
if($RIGHT >= "R")
{
	$GLOBALS['APPLICATION']->SetAdditionalCss("/bitrix/css/".$moduleID."/style.css");
	$GLOBALS['APPLICATION']->SetTitle(Loc::getMessage("ASPRO_NEXT_PAGE_TITLE"));

	$by = "id";
	$sort = "asc";

	$arSites = array();
	$db_res = CSite::GetList($by, $sort, array("ACTIVE"=>"Y"));
	while($res = $db_res->Fetch())
	{
		$arSites[] = $res;
	}

	$arTabs = array();
	$bShowGenerate = false;
	foreach($arSites as $key => $arSite)
	{
		$arSite['DIR'] = str_replace('//', '/', '/'.$arSite['DIR']);
		if(!strlen($arSite['DOC_ROOT']))
			$arSite['DOC_ROOT'] = $_SERVER['DOCUMENT_ROOT'];
		
		$arSite['DOC_ROOT'] = str_replace('//', '/', $arSite['DOC_ROOT'].'/');
		$siteDir = str_replace('//', '/', $arSite['DOC_ROOT'].$arSite['DIR']);
		$optionsSiteID = $arSite["ID"];

		//get web forms
		$arItems = array();
		if(\Bitrix\Main\Loader::includeModule("form"))
		{
			$rsForms = CForm::GetList($by = "s_id", $order = "ASC", array('ACTIVE' => 'Y', 'SITE' => array($optionsSiteID)), $is_filtered);
			while($arForm = $rsForms->Fetch())
			{
				$arItems[$arForm['ID']] = $arForm;
			}
		}

		$arTabs[] = array(
			"DIV" => "edit".($key+1),
			"TAB" => Loc::getMessage("MAIN_OPTIONS_SITE_ASPRO_TITLE", array("#SITE_NAME#" => $arSite["NAME"], "#SITE_ID#" => $arSite["ID"])),
			"ICON" => "settings",
			"PAGE_TYPE" => "site_settings",
			"SITE_ID" => $optionsSiteID,
			"SITE_DIR" => $arSite["DIR"],
			"SITE_DIR_FORMAT" => $siteDir,
			"FORMS" => $arItems,
			"ITEMS" => array(
				"CONFIG" => array(
					"TITLE" => Loc::getMessage("ASPRO_NEXT_MODULE_CONFIG_AMO_CRM"),
					"ITEMS" => array(
						"DOMAIN_AMO_CRM" => array(
							"TYPE" => "text",
							"VALUE" => Option::get($moduleID, "DOMAIN_AMO_CRM", "", $optionsSiteID),
							"HINT" => Loc::getMessage("ASPRO_NEXT_MODULE_DOMAIN_HINT")
						),
						"LOGIN_AMO_CRM" => array(
							"TYPE" => "text",
							"VALUE" => Option::get($moduleID, "LOGIN_AMO_CRM", "", $optionsSiteID)
						),
						"TOKEN_AMO_CRM" => array(
							"TYPE" => "text",
							"VALUE" => Option::get($moduleID, "TOKEN_AMO_CRM", "", $optionsSiteID),
							"HINT" => Loc::getMessage("ASPRO_NEXT_MODULE_TOKEN_HINT")
						),
					)
				),
				"LINK" => array(
					"TITLE" => Loc::getMessage("ASPRO_NEXT_MODULE_LINK_AMO_CRM"),
					"ITEMS" => array(
						"ACTIVE_LINK_AMO_CRM" => array(
							"TYPE" => "hidden",
							"VALUE" => Option::get($moduleID, "ACTIVE_LINK_AMO_CRM", "", $optionsSiteID),
						),
						"ACTIVE_AMO_CRM" => array(
							"TYPE" => "checkbox",
							"VALUE" => Option::get($moduleID, "ACTIVE_AMO_CRM", "N", $optionsSiteID),
						),
						"AUTOMATE_SEND_AMO_CRM" => array(
							"TYPE" => "checkbox",
							"VALUE" => Option::get($moduleID, "AUTOMATE_SEND_AMO_CRM", "Y", $optionsSiteID),
							"HINT" => Loc::getMessage("ASPRO_NEXT_MODULE_AUTOMATE_SEND_AMO_CRM_HINT")
						),
						"USE_LOG_AMO_CRM" => array(
							"TYPE" => "checkbox",
							"VALUE" => Option::get($moduleID, "USE_LOG_AMO_CRM", "N", $optionsSiteID),
							"HINT" => Loc::getMessage("ASPRO_NEXT_MODULE_USE_LOG_AMO_CRM_HINT")
						),
						"LEAD_NAME_AMO_CRM_TITLE" => array(
							"TYPE" => "text",
							"VALUE" => Option::get($moduleID, "LEAD_NAME_AMO_CRM_TITLE", Loc::getMessage("ASPRO_NEXT_MODULE_LEAD_NAME_AMO_CRM"), $optionsSiteID),
						),
						"TAGS_AMO_CRM_TITLE" => array(
							"TYPE" => "text",
							"VALUE" => Option::get($moduleID, "TAGS_AMO_CRM_TITLE", Loc::getMessage("ASPRO_NEXT_MODULE_TAGS_AMO_CRM"), $optionsSiteID),
						),
						"WEB_FORM_AMO_CRM" => array(
							"TYPE" => "select",
							"VALUE" => Option::get($moduleID, "WEB_FORM_AMO_CRM", "", $optionsSiteID),
							"VALUES" => $arItems,
							"DINAMIC_FORMS" => "Y",
						),
					)
				)
			),
		);
	}

	$tabControl = new CAdminTabControl("tabControl", $arTabs);

	if($REQUEST_METHOD == "POST" && strlen($Update.$Apply.$RestoreDefaults.$Auth.$SendCrm) && $RIGHT >= "W" && check_bitrix_sessid())
	{
		global $APPLICATION, $CACHE_MANAGER;
		$APPLICATION->RestartBuffer();
		if($Auth)
		{
			if($_POST["DOMAIN"] && $_POST["TOKEN"] && $_POST["LOGIN"])
			{
				$url = str_replace("#DOMAIN#", $_POST["DOMAIN"], \Aspro\Functions\CAsproNextCRM::AMO_CRM_PATH);

				$arPostFields = array(
					"USER_HASH" => $_POST["TOKEN"],
					"USER_LOGIN" => $_POST["LOGIN"],
				);
				echo \Aspro\Functions\CAsproNextCRM::query($url, \Aspro\Functions\CAsproNextCRM::$arCrmMethods['AMO_CRM']['AUTH'], $arPostFields, true, true);
			}
			else
			{
				$arError = array(
					"error" => array(
						"error_code" => 00,
						"error_msg" => "empty fields"
					)
				);
				echo json_encode($arError);
			}
			die();
		}
		elseif($SendCrm)
		{
			if($_POST["SITE_ID"] && $_POST["FORM_ID"] && $_POST["RESULT_ID"])
			{
				$url = str_replace('#DOMAIN#', Option::get($moduleID, 'DOMAIN_AMO_CRM', '', $_POST["SITE_ID"]), \Aspro\Functions\CAsproNextCRM::AMO_CRM_PATH);
				$dataDeal = array(
					'name' => iconv(LANG_CHARSET, 'UTF-8', 'Текст')
				);
				$data['request']['leads']['add'] = array($dataDeal);
				/*$arFields = array(
					'request' => array(
						'leads' => array(
							'add' => array(
								'name' => 'test2'
							)
						)
					)
				);*/
				// $result_text = \Aspro\Functions\CAsproNextCRM::query($url, "/private/api/v2/json/accounts/current/", $data, true, true);
				// echo $result_text;
				// \Aspro\Functions\CAsproNext::set_log('crm', 'test_create_lead_response', json_decode($result_text, true));

				echo \Aspro\Functions\CAsproNext::sendLeadCrmFromForm($_POST["FORM_ID"], $_POST["RESULT_ID"], 'AMO_CRM', $_POST["SITE_ID"], true, true);
			}
			else
			{
				$arError = array(
					"error" => array(
						"error_code" => 00,
						"error_msg" => "empty fields"
					)
				);
				echo json_encode($arError);
			}
			die();
		}
		else
		{
			foreach($arTabs as $key => $arTab)
			{
				$optionsSiteID = $arSite["ID"];
				foreach($arTab["ITEMS"] as $groupCode => $arOptions)
				{
					foreach($arOptions["ITEMS"] as $optionCode => $arOption)
					{
						if(strlen($RestoreDefaults))
						{
							Option::delete($moduleID, array("name" => $optionCode));
						}
						else
						{
							if($arOption["TYPE"] == "checkbox")
							{
								if(!isset($_POST[$optionCode."_".$optionsSiteID]))
									$_POST[$optionCode."_".$optionsSiteID] = "N";
							}
							if(isset($_POST[$optionCode."_".$optionsSiteID]))
								Option::set($moduleID, $optionCode, $_POST[$optionCode."_".$optionsSiteID], $optionsSiteID);
						}
					}
				}

				//set integration with crm
				$domain = Option::get($moduleID, "DOMAIN_AMO_CRM", "", $optionsSiteID);
				$token = Option::get($moduleID, "TOKEN_AMO_CRM", "", $optionsSiteID);
				$login = Option::get($moduleID, "LOGIN_AMO_CRM", "", $optionsSiteID);
				$val = "";
				if($domain && $token && $login)
				{
					$url = str_replace("#DOMAIN#", $domain, \Aspro\Functions\CAsproNextCRM::AMO_CRM_PATH);
					$arPostFields = array(
						"USER_HASH" => $token,
						"USER_LOGIN" => $login,
					);
					$result = json_decode(\Aspro\Functions\CAsproNextCRM::query($url, "/private/api/auth.php?type=json", $arPostFields, true), true);

					if(isset($result["response"]) && ($result["response"] && isset($result["response"]["auth"]) && $result["response"]["auth"]))
						$val = "Y";
				}

				Option::set($moduleID, "ACTIVE_LINK_AMO_CRM", $val, $optionsSiteID);

				//set field matching
				if($_POST['CRM_FIELD_'.$optionsSiteID] && $_POST['CRM_FORM_FIELD_'.$optionsSiteID])
				{
					foreach($_POST['CRM_FIELD_'.$optionsSiteID] as $formID => $arFields)
					{
						$arPostFields = array();
						foreach($arFields as $keyProp => $value)
						{
							if($_POST['CRM_FORM_FIELD_'.$optionsSiteID][$formID][$keyProp])
							{
								$arPostFields[$value] = $_POST['CRM_FORM_FIELD_'.$optionsSiteID][$formID][$keyProp];
							}
						}
						Option::set($moduleID, 'AMO_CRM_FIELDS_MATCH_'.$formID, serialize($arPostFields), $optionsSiteID);
					}
				}

				//set fields array from amo crm
				if(isset($_POST['CUSTOM_FIELD_AMO_CRM_'.$optionsSiteID]))
				{
					Option::set($moduleID, 'CUSTOM_FIELD_AMO_CRM', urldecode($_POST['CUSTOM_FIELD_AMO_CRM_'.$optionsSiteID]), $optionsSiteID);
				}
			}
		}
	}
	
	CJSCore::Init(array("jquery"));
	CAjax::Init();?>
	<?if(!count($arTabs)):?>
		<div class="adm-info-message-wrap adm-info-message-red">
			<div class="adm-info-message">
				<div class="adm-info-message-title"><?=Loc::getMessage("ASPRO_NEXT_NO_SITE_INSTALLED", array("#SESSION_ID#"=>bitrix_sessid_get()))?></div>
				<div class="adm-info-message-icon"></div>
			</div>
		</div>
	<?else:?>
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
				$optionsSiteID = $arTab["SITE_ID"];
				$value_count = 0;?>
				<?if(!$arTab["FORMS"]):?>
					<tr>
						<td colspan="2" style="width:100%;text-align:center;">
							<div class="adm-info-message"><?=Loc::getMessage("ASPRO_NEXT_MODULE_NO_FORMS");?></div>
						</td>
					</tr>
					<?continue;?>
				<?endif;?>
				<?foreach($arTab["ITEMS"] as $groupCode => $arOptions):?>
					<?if($groupCode == "LINK" && !$arOptions["ITEMS"]["ACTIVE_LINK_AMO_CRM"]["VALUE"]):?>
						<tr>
							<td colspan="2" style="width:100%;text-align:center;">
								<div class="adm-info-message"><?=Loc::getMessage("ASPRO_NEXT_MODULE_INTEGRATION_AMO_CRM");?></div>
							</td>
						</tr>
						<?continue;?>
					<?endif;?>

					<tr class="heading"><td colspan="2"><?=Loc::getMessage("ASPRO_NEXT_MODULE_".$groupCode."_AMO_CRM");?></td></tr>

					<?foreach($arOptions["ITEMS"] as $optionCode => $arOption):?>
						<?$value = ($arOption["TYPE"] == "checkbox" ? "Y" : $arOption["VALUE"]);?>
						<?if($arOption["VALUE"] && $groupCode == "CONFIG")
						{
							++$value_count;
						}
						if($arOption["TYPE"] == "hidden"):?>
							<input type="<?=$arOption["TYPE"];?>" size="50" maxlength="255" value="" name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>">
						<?else:?>
							<tr>
								<td class="adm-detail-content-cell-l" style="width:50%;<?=($arOption["HINT"] ? "vertical-align: top;padding-top: 7px;" : "");?>">
									<?=Loc::getMessage("ASPRO_NEXT_MODULE_".$optionCode);?>
								</td>
								<td style="width:50%;">
									<?if($arOption["TYPE"] == "select"):?>
										<select name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>">
											<?if($arOption["VALUES"])
											{
												foreach($arOption["VALUES"] as $key => $arForm):?>
													<option <?=($key == $value ? "selected" : "");?> value="<?=$key?>">[<?=$arForm['ID'];?>] <?=$arForm['NAME'];?></option>
												<?endforeach;
											}?>
										</select>
									<?else:?>
										<input type="<?=$arOption["TYPE"];?>" <?=($arOption["TYPE"] == "checkbox" ? ($arOption["VALUE"] == "Y" ? "checked" : "") : "");?> size="60" maxlength="255" value="<?=htmlspecialcharsbx($value)?>" name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>" <?=($optionCode == "password" ? "autocomplete='off'" : "")?>>
									<?endif;?>
									<?if($arOption["HINT"]):?>
										<br/><small style="color: #777;"><?=$arOption["HINT"];?></small>
									<?endif;?>
								</td>
							</tr>
							<?if(isset($arOption["DINAMIC_FORMS"]) && $arOption["DINAMIC_FORMS"]):?>
								<?if($arOption["VALUES"]):?>
									<tr>
										<td colspan="2">
											<?
											$aSiteTabs = array(
												array(
													"DIV" => "edit_forms_field",
													"TAB" => Loc::getMessage("ASPRO_NEXT_MODULE_FIELDS_AMO_CRM"),
													"TITLE" => Loc::getMessage("ASPRO_NEXT_MODULE_ALL_FIELDS_AMO_CRM"),
													"ICON" => "settings",
													"PAGE_TYPE" => "site_settings",
												),
												array(
													"DIV" => "edit_forms_result",
													"TAB" => Loc::getMessage("ASPRO_NEXT_MODULE_RESULTS_AMO_CRM"),
													"TITLE" => Loc::getMessage("ASPRO_NEXT_MODULE_ALL_RESULTS_AMO_CRM"),
													"ICON" => "settings",
													"PAGE_TYPE" => "site_settings",
												),
											);
											$bIntegrationAmoCrm = (Option::get($moduleID, 'ACTIVE_LINK_AMO_CRM', $optionsSiteID) && (Option::get($moduleID, 'ACTIVE_AMO_CRM', 'N', $optionsSiteID) == 'Y'));
											if($bIntegrationAmoCrm)
											{

												//auth
												$result = \Aspro\Functions\CAsproNextCRM::authAmoCrm($optionsSiteID);

												if(isset($result["response"]) && ($result["response"] && isset($result["response"]["auth"]) && $result["response"]["auth"]))
												{
													$url = str_replace('#DOMAIN#', Option::get($moduleID, 'DOMAIN_AMO_CRM', '', $optionsSiteID), \Aspro\Functions\CAsproNextCRM::AMO_CRM_PATH);
													$result_text = \Aspro\Functions\CAsproNextCRM::query($url, "/private/api/v2/json/accounts/current/", array(), true, true);
													$arResponse = json_decode($result_text, true);
													if($arResponse)
													{
														if(isset($arResponse["response"]["account"]) && (isset($arResponse["response"]["account"]["custom_fields"]) && $arResponse["response"]["account"]["custom_fields"]))
														{
															$arCrmOptions = array();
															foreach($arResponse["response"]["account"]["custom_fields"] as $codeGroup => $arGroup)
															{
																if($codeGroup != "customers")
																{
																	foreach($arGroup as $arProp)
																	{
																		$arCrmOptions[$codeGroup][$arProp["id"]]["CODE"] = $arProp["code"];
																		if(isset($arProp["enums"]))
																		{
																			$arCrmOptions[$codeGroup][$arProp["id"]]["ENUMS"] = $arProp["enums"];
																			foreach($arProp["enums"] as $keyEnum => $enum)
																			{
																				\Aspro\Functions\CAsproNextCRM::$arCrmFileds["AMO_CRM"][$codeGroup]["PROPS"][$arProp["id"]."_".$keyEnum."_".$codeGroup] = (Loc::getMessage("AMO_CRM_FIELD_".$arProp["code"]."_".$enum) ? Loc::getMessage("AMO_CRM_FIELD_".$arProp["code"]."_".$enum) : iconv("UTF-8", LANG_CHARSET, $arProp["name"]."_".$enum));
																			}
																		}
																		else
																		{
																			\Aspro\Functions\CAsproNextCRM::$arCrmFileds["AMO_CRM"][$codeGroup]["PROPS"][$arProp["id"]."_".$codeGroup] = iconv("UTF-8", LANG_CHARSET, $arProp["name"]);
																		}
																	}
																}
															}
														}
														if($arCrmOptions):?>
															<input type="hidden" value="<?=urlencode(serialize($arCrmOptions));?>" name="CUSTOM_FIELD_AMO_CRM_<?=$optionsSiteID?>">
														<?endif;
													}
												}
											}
											?>

											<?$siteTabControl = new CAdminViewTabControl("siteTabControl", $aSiteTabs);
											$siteTabControl->Begin();?>

											<?$siteTabControl->BeginNextTab();?>
												<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit-table">
													<?foreach($arOption["VALUES"] as $key => $arForm):?>
														<?
														$arFields = array();
														$rsQuestions = CFormField::GetList($key, "ALL", $by = "id", $order = "desc", array("ACTIVE" => "Y"), $is_filtered);
														while($arQuestion = $rsQuestions->Fetch())
														{
															$arFields[$arQuestion["ID"]] = $arQuestion;
														}
														$arValueForm = unserialize(Option::get($moduleID, "AMO_CRM_FIELDS_MATCH_".$key, "", $optionsSiteID));
														?>
														<tr class="form_<?=$key;?>" <?=($key != $value ? "style='display: none;'" : '');?>>
															<td colspan="2">
																<table class="internal" style="width:100%;">
																	<thead>
																		<tr class="heading">
																			<td width="50%"><?=Loc::getMessage("CRM_FIELD_TABLE")?></td>
																			<td width="50%"><?=Loc::getMessage("FORM_FIELD_TABLE")?></td>
																			<td width="17"></td>
																		</tr>
																	</thead>
																	<tbody>
																		<?if($arValueForm)
																		{
																			foreach($arValueForm as $crm_field_id => $form_field_id):?>
																				<tr>
																					<td class="adm-detail-content-cell-l" style="width:50%;">
																						<select name="CRM_FIELD_<?=$optionsSiteID?>[<?=$key;?>][]" class="field_crm" style="width:300px;">
																							<option value=""><?=Loc::getMessage('FORM_FIELD_CRM_FIELDS_FORM_NAME_NO')?></option>
																							<?foreach(\Aspro\Functions\CAsproNextCRM::$arCrmFileds["AMO_CRM"] as $groupCode => $arGroup):?>
																								<optgroup label="<?=Loc::getMessage($groupCode."_FIELD_CODE_AMO_CRM");?>">
																									<?foreach($arGroup["PROPS"] as $key2 => $text):?>
																										<option <?=($key2 == $crm_field_id ? "selected" : "");?> value="<?=$key2?>">
																											<?=($text ? $text : Loc::getMessage($key2."_AMO_CRM"));?>
																										</option>
																									<?endforeach;?>
																								</optgroup>
																							<?endforeach;?>
																						</select>
																					</td>
																					<td style="width:50%;">
																						<select name="CRM_FORM_FIELD_<?=$optionsSiteID?>[<?=$key;?>][]" class="field_form" style="width:300px;">
																							<option value=""><?=Loc::getMessage('FORM_FIELD_CRM_FIELDS_FORM_NAME_NO')?></option>
																							<optgroup label="...">
																								<?foreach(\Aspro\Functions\CAsproNextCRM::$arCrmFileds["MAIN"] as $key2 => $text):?>
																									<option <?=($key2 == $form_field_id ? "selected" : "");?> value="<?=$key2?>"><?=Loc::getMessage('FORM_FIELD_CRM_FIELDS_'.$key2)?></option>
																								<?endforeach;?>
																							</optgroup>
																							<optgroup label="...">
																								<?foreach($arFields as $key2 => $arQuestion):?>
																									<option <?=($key2 == $form_field_id ? "selected" : "");?> value="<?=$key2?>"><?=$arQuestion["TITLE"];?> (<?=$arQuestion["SID"];?>)</option>
																								<?endforeach;?>
																							</optgroup>
																						</select>
																					</td>
																					<td><a href="javascript:void(0)" title="<?=Loc::getMessage("DELETE_NODE")?>" class="form-action-button action-delete"></a></td>
																				</tr>
																			<?endforeach;
																		}?>
																		<tr>
																			<td class="adm-detail-content-cell-l" style="width:50%;">
																				<select name="CRM_FIELD_<?=$optionsSiteID?>[<?=$key;?>][]" class="field_crm" style="width:300px;">
																					<option value=""><?=Loc::getMessage('FORM_FIELD_CRM_FIELDS_FORM_NAME_NO')?></option>
																					<?foreach(\Aspro\Functions\CAsproNextCRM::$arCrmFileds["AMO_CRM"] as $groupCode => $arGroup):?>
																						<optgroup label="<?=Loc::getMessage($groupCode."_FIELD_CODE_AMO_CRM");?>">
																							<?foreach($arGroup["PROPS"] as $key2 => $text):?>
																								<option value="<?=$key2?>">
																									<?=($text ? $text : Loc::getMessage($key2."_AMO_CRM"));?>
																								</option>
																							<?endforeach;?>
																						</optgroup>
																					<?endforeach;?>
																				</select>
																			</td>
																			<td style="width:50%;">
																				<select name="CRM_FORM_FIELD_<?=$optionsSiteID?>[<?=$key;?>][]" class="field_form" style="width:300px;">
																					<option value=""><?=GetMessage('FORM_FIELD_CRM_FIELDS_FORM_NAME_NO')?></option>
																					<optgroup label="...">
																						<?foreach(\Aspro\Functions\CAsproNextCRM::$arCrmFileds["MAIN"] as $key2 => $text):?>
																							<option value="<?=$key2?>"><?=Loc::getMessage('FORM_FIELD_CRM_FIELDS_'.$key2)?></option>
																						<?endforeach;?>
																					</optgroup>
																					<optgroup label="...">
																						<?foreach($arFields as $key2 => $arQuestion):?>
																							<option value="<?=$key2?>"><?=$arQuestion["TITLE"];?> (<?=$arQuestion["SID"];?>)</option>
																						<?endforeach;?>
																					</optgroup>
																				</select>
																			</td>
																			<td></td>
																		</tr>
																	</tbody>
																	<tfoot>
																		<tr>
																			<td colspan="3"><input type="button" class="addbtn" value="<?=htmlspecialcharsbx(Loc::getMessage('FORM_CRM_ADD'))?>"></td>
																		</tr>
																	</tfoot>
																</table>
															</td>
														</tr>
													<?endforeach;?>
												</table>
											<?$siteTabControl->BeginNextTab();?>
												<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit-table">
													<?foreach($arOption["VALUES"] as $key => $arForm):?>
														<?$arFormResults = array();
														$rsFormResults = CFormResult::GetList($key, $by = 's_id', $order = 'asc', array(), $is_filtered, 'N', false);
														while($arFormResult = $rsFormResults->Fetch())
														{
															$arFormResults[] = $arFormResult;
														}
														$arValueForm = unserialize(Option::get($moduleID, "AMO_CRM_FIELDS_MATCH_".$key, "", $optionsSiteID));?>
														<tr class="form_<?=$key;?>" <?=($key != $value ? "style='display: none;'" : '');?>>
															<td colspan="2">
																<?if(!$arValueForm || !$bIntegrationAmoCrm):?>
																	<div class="adm-info-message"><?=Loc::getMessage("ASPRO_NEXT_MODULE_NO_FORM_FIELD_MATCHING");?></div>
																<?elseif($arFormResults):?>
																	<table class="internal" style="width:100%;">
																		<thead>
																			<tr class="heading">
																				<td width="100%"><?=Loc::getMessage("FORM_RESULT_FIELD_TABLE")?></td>
																				<td width="17"></td>
																			</tr>
																		</thead>
																		<tbody>
																			<?foreach($arFormResults as $arFormResult):?>
																				<?$arStatus = unserialize(Option::get($moduleID, 'CRM_SEND_FORM_'.$arFormResult["ID"], '', $optionsSiteID));
																				$bSend = (isset($arStatus["AMO_CRM"]) && $arStatus["AMO_CRM"]);?>
																				<tr>
																					<td>
																						<a href="/bitrix/admin/form_result_edit.php?lang=<?=LANGUAGE_ID;?>&WEB_FORM_ID=<?=$arForm["ID"];?>&RESULT_ID=<?=$arFormResult["ID"];?>&WEB_FORM_NAME=<?=$arForm["SID"];?>">
																							<?=$arFormResult["ID"];?>
																						</a>
																						<?=Loc::getMessage('FORM_RESULT_INFO', array("DATE_CREATE" => $arFormResult["DATE_CREATE"], "TIMESTAMP_X" => $arFormResult["TIMESTAMP_X"]))?> <span class="status_send <?=($bSend ? "success" : "error");?>">(<span><?=($bSend ? Loc::getMessage('FORM_RESULT_SEND') : Loc::getMessage('FORM_RESULT_NO_SEND'))?></span>)</span></td>
																					<td>
																						<?if(!$bSend):?>
																							<a href="javascript:void(0)" title="<?=Loc::getMessage("SEND_CRM")?>" data-form_id="<?=$key;?>" data-result_id="<?=$arFormResult["ID"]?>" data-site_id="<?=$optionsSiteID;?>" class="form-action-button action-send"></a>
																						<?endif;?>
																					</td>
																				</tr>
																			<?endforeach;?>
																		</tbody>
																	</table>
																<?else:?>
																	<div class="adm-info-message"><?=Loc::getMessage("ASPRO_NEXT_MODULE_NO_FORM_RESULTS");?></div>
																<?endif;?>
															</td>
														</tr>
													<?endforeach;?>
												</table>
											<?$siteTabControl->End();?>
										</td>
									</tr>
								<?endif;?>
							<?endif;?>
						<?endif;?>
					<?endforeach;?>

					<?if($value_count == 3 && $groupCode == "CONFIG"):?>
						<tr>
							<td colspan="2" style="width:100%;text-align:center;">
								<input type="submit" class="check_auth" value="<?=Loc::getMessage("ASPRO_NEXT_MODULE_CHECK_AUTH");?>"/>
								<div><span class="response"></span></div>
							</td>
						</tr>
					<?endif;?>
				<?endforeach;?>
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

			<input <?if($RIGHT < "W") echo "disabled"?> type="submit" name="Apply" class="submit-btn adm-btn-save" value="<?=Loc::getMessage("ASPRO_NEXT_MODULE_SAVE_OPTION")?>" title="<?=Loc::getMessage("ASPRO_NEXT_MODULE_SAVE_OPTION")?>">
			<input type="submit" name="RestoreDefaults" title="<?=Loc::getMessage("ASPRO_NEXT_MODULE_DELETE_OPTION")?>" onclick="confirm('<?=Loc::getMessage("ASPRO_NEXT_MODULE_DELETE_OPTION_TITLE")?>')" value="<?=Loc::getMessage("ASPRO_NEXT_MODULE_DELETE_OPTION_TEXT")?>">

			<script type="text/javascript">
				BX.message({
					"CRM_SEND": "<?=Loc::getMessage("FORM_RESULT_SEND")?>"
				})
				$(document).ready(function(){
					$('input.addbtn').on('click', function(){
						var _table = $(this).closest('.internal');
						$(_table.find('tbody tr:last').clone()).insertAfter(_table.find('tbody tr:last'));
					})

					$('.action-delete').on('click', function(){
						var _tr = $(this).closest('tr');
						_tr.remove();
					})

					$('.action-send').on('click', function(){
						var _this = $(this),
							tr = _this.closest('tr');
						if(_this.data('disabled') != 'disabled')
						{
							_this.attr('data-disabled', 'disabled');
							$.ajax({
								type: 'POST',
								dataType: 'json',
								data: {'sessid': $('input[name=sessid]').val(), 'SendCrm': true, 'SITE_ID': _this.data('site_id'), 'FORM_ID': _this.data('form_id'), 'RESULT_ID': _this.data('result_id')},
								success: function(data){
									_this.removeAttr('data-disabled');
									console.log(data);
									if('error' in data)
									{
										tr.find('.status_send').removeClass('success').addClass('error').find('span').text(data.error.error_msg);
									}
									else if('response' in data)
									{
										if('error' in data.response)
										{
											tr.find('.status_send').removeClass('success').addClass('error').find('span').text(data.response.error);
										}
										else
										{
											tr.find('.status_send').removeClass('error').addClass('success').find('span').text(BX.message("CRM_SEND"));
											_this.remove();
										}
									}
								},
								error: function(data){
									window.console&&console.log(data);
									_this.removeAttr('data-disabled');
									tr.find('.status_send').removeClass('success').addClass('error').find('span').text(data.responseText);
								}
							});
						}
					})

					$('input.check_auth').on('click', function(){
						var _this = $(this),
							form = _this.closest('form');
						_this.attr('disabled', 'disabled');
						$.ajax({
							type: 'POST',
							dataType: 'json',
							data: {'sessid': $('input[name=sessid]').val(), 'Auth': true, 'DOMAIN': form.find('input[name^=DOMAIN_AMO_CRM]').val(), 'LOGIN': form.find('input[name^=LOGIN_AMO_CRM]').val(), 'TOKEN': form.find('input[name^=TOKEN_AMO_CRM]').val()},
							success: function(data){
								_this.removeAttr('disabled');
								console.log(data);
								if('error' in data)
								{
									$('.response').removeClass('success').addClass('error').text(data.error.error_msg);
								}
								else if('response' in data)
								{
									if('error' in data.response)
										$('.response').removeClass('success').addClass('error').text(data.response.error);
									else
										$('.response').removeClass('error').addClass('success').text('ok');
								}
							},
							error: function(data){
								window.console&&console.log(data);
								_this.removeAttr('disabled');
								$('.response').removeClass('success').addClass('error').text('error');
							}
						});
					})
					$('select[name^="WEB_FORM_AMO_CRM"]').on('change', function(){
						$('tr[class^="form_"]').hide();
						$('tr.form_'+$(this).val()).css('display','');
					})
					$('select[name^="WEB_FORM_AMO_CRM"]').change();
				});

			</script>
		</form>
		<?$tabControl->End();?>
	<?endif;?>
<?
}
else
{
	echo CAdminMessage::ShowMessage(Loc::getMessage('NO_RIGHTS_FOR_VIEWING'));
}?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>