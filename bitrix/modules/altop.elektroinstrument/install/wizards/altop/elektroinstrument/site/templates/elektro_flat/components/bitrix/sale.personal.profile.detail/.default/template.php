<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

if(strlen($arResult["ID"]) > 0) {
	ShowError($arResult["ERROR_MESSAGE"]);?>
	<form method="post"  class="sale-profile-detail-form" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="ID" value="<?=$arResult["ID"]?>">		
		<h2><?=Loc::getMessage("SPPD_GENERAL_INFORMATION")?></h2>
		<div class="sale-profile-detail-block-wrap">
			<div class="sale-profile-detail-block">
				<div class="sale-profile-detail-form-group">
					<label><?=Loc::getMessage("SPPD_PERS_TYPE")?></label>
					<div class="sale-profile-detail-form-property"><?=$arResult["PERSON_TYPE"]["NAME"]?></div>
				</div>
				<div class="sale-profile-detail-form-group">				
					<label><?=Loc::getMessage("SPPD_PNAME")?> <span class="req">*</span></label>
					<div class="sale-profile-detail-form-property">
						<input type="text" name="NAME" maxlength="50" value="<?=$arResult['NAME']?>" />
					</div>
				</div>
			</div>
		</div>
		<?foreach($arResult["ORDER_PROPS"] as $block) {
			if(!empty($block["PROPS"])) {?>
				<h2><?=$block["NAME"]?></h2>
				<div class="sale-profile-detail-block-wrap">
					<div class="sale-profile-detail-block">
						<?foreach($block["PROPS"] as $key => $property) {
							$name = "ORDER_PROP_".$property["ID"];
							$currentValue = $arResult["ORDER_PROPS_VALUES"][$name];?>
							<div class="sale-profile-detail-form-group sale-profile-detail-form-property-<?=strtolower($property['TYPE'])?>">
								<label><?=$property["NAME"].($property["REQUIED"] == "Y" ? " <span class='req'>*</span>" : "");?></label>
								<div class="sale-profile-detail-form-property">
									<?if($property["TYPE"] == "CHECKBOX") {?>
										<input type="checkbox" name="<?=$name?>" value="Y"<?=($currentValue == "Y" || !isset($currentValue) && $property["DEFAULT_VALUE"] == "Y" ? " checked" : "");?> />
									<?} elseif($property["TYPE"] == "TEXT") {?>
										<input type="text" name="<?=$name?>" maxlength="50" value="<?=$currentValue?>" />
									<?} elseif($property["TYPE"] == "SELECT") {?>
										<select name="<?=$name?>" size="<?=(intval($property["SIZE1"]) > 0 ? $property["SIZE1"] : 1);?>">
											<?foreach($property["VALUES"] as $value) {?>
												<option value="<?=$value["VALUE"]?>"<?=($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"] == $property["DEFAULT_VALUE"] ? " selected" : "");?>><?=$value["NAME"]?></option>
											<?}?>
										</select>
									<?} elseif($property["TYPE"] == "MULTISELECT") {?>
										<select multiple name="<?=$name?>[]" size="<?=(intval($property["SIZE1"]) > 0 ? $property["SIZE1"] : 5);?>">
											<?$arCurVal = array();
											$arCurVal = explode(",", $currentValue);
											for($i = 0, $cnt = count($arCurVal); $i < $cnt; $i++)
												$arCurVal[$i] = trim($arCurVal[$i]);
											$arDefVal = explode(",", $property["DEFAULT_VALUE"]);
											for($i = 0, $cnt = count($arDefVal); $i < $cnt; $i++)
												$arDefVal[$i] = trim($arDefVal[$i]);
											foreach($property["VALUES"] as $value) {?>
												<option value="<?= $value["VALUE"]?>"<?=(in_array($value["VALUE"], $arCurVal) || !isset($currentValue) && in_array($value["VALUE"], $arDefVal) ? " selected" : "");?>><?=$value["NAME"]?></option>
											<?}?>
										</select>
									<?} elseif($property["TYPE"] == "TEXTAREA") {?>
										<textarea rows="<?=((int)($property["SIZE2"]) > 0 ? $property["SIZE2"] : 4);?>" cols="<?=((int)($property["SIZE1"]) > 0 ? $property["SIZE1"] : 40);?>" name="<?=$name?>"><?=(isset($currentValue) ? $currentValue : $property["DEFAULT_VALUE"]);?></textarea>
									<?} elseif($property["TYPE"] == "LOCATION") {
										$locationTemplate = $arParams["USE_AJAX_LOCATIONS"] !== "Y" ? "popup" : "";
										$locationValue = intval($currentValue) ? $currentValue : $property["DEFAULT_VALUE"];
										CSaleLocation::proxySaleAjaxLocationsComponent(
											array(
												"AJAX_CALL" => "N",
												"CITY_OUT_LOCATION" => "Y",
												"COUNTRY_INPUT_NAME" => $name."_COUNTRY",
												"CITY_INPUT_NAME" => $name,
												"LOCATION_VALUE" => $locationValue,
											),
											array(
											),
											$locationTemplate,
											true,
											"location-block-wrapper"
										);
									} elseif($property["TYPE"] == "RADIO") {
										foreach($property["VALUES"] as $value) {?>
											<input type="radio" name="<?=$name?>" value="<?=$value["VALUE"]?>"<?=($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"] == $property["DEFAULT_VALUE"] ? " checked" : "");?> />
											<?=$value["NAME"]?>
											<br />
										<?}
									} elseif($property["TYPE"] == "FILE") {
										$multiple = $property["MULTIPLE"] === "Y" ? "multiple" : "";
										echo CFile::InputFile($name."[]", 20, null, false, 0, "IMAGE", "class='sale-profile-detail-form-input-file' ".$multiple);
										if(count($currentValue) > 0) {?>
											<input type="hidden" name="<?=$name?>_del" class="sale-profile-detail-form-input-delete-file">
											<?$profileFiles = unserialize(htmlspecialchars_decode($currentValue));
											foreach($profileFiles as $file) {?>											
												<div class="sale-profile-detail-form-check-file">
													<input type="checkbox" value="<?=$file?>" class="sale-profile-detail-form-check-file-checkbox" id="sale-profile-detail-form-check-file-checkbox-<?=$file?>">
													<label for="sale-profile-detail-form-check-file-checkbox-<?=$file?>"><?=Loc::getMessage("SPPD_DELETE_FILE")?></label>
												</div>
												<div class="sale-profile-detail-form-file">
													<?$fileInfo = CFile::GetByID($file);
													$fileInfoArray = $fileInfo->Fetch();
													if(CFile::IsImage($fileInfoArray["FILE_NAME"])) {
														echo CFile::ShowImage($file, 150, 150, "border=0", "", true);
													} else {?>
														<a download="<?=$fileInfoArray["ORIGINAL_NAME"]?>" href="<?=CFile::GetFileSRC($fileInfoArray)?>">
															<?=Loc::getMessage("SPPD_DOWNLOAD_FILE", array("#FILE_NAME#" => $fileInfoArray["ORIGINAL_NAME"]))?>
														</a>
													<?}?>
												</div>
											<?}
										}
									}?>
								</div>
								<?if(strlen($property["DESCRIPTION"]) > 0) {?>
									<div class="sale-profile-detail-form-description"><?=$property["DESCRIPTION"]?></div>
								<?}?>
							</div>
						<?}?>
					</div>
				</div>
			<?}
		}?>
		<div class="sale-profile-detail-form-btn">
			<button type="submit" name="apply" class="btn_buy ppp" value="<?=GetMessage('SPPD_APPLY')?>"><?=GetMessage("SPPD_APPLY")?></button>
			<button type="submit" name="save" class="btn_buy popdef" value="<?=GetMessage('SPPD_SAVE')?>"><?=GetMessage("SPPD_SAVE")?></button>
		</div>
	</form>
	<script>
		BX.Sale.PersonalProfileComponent.PersonalProfileDetail.init();
	</script>
<?} else {
	ShowError($arResult["ERROR_MESSAGE"]);
}?>