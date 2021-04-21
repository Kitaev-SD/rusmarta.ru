<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props_format.php");?>

<h2><?=GetMessage("SOA_TEMPL_PROP_INFO")?></h2>
<div class="order-info">
	<div class="order-info_in">
		<?if(!empty($arResult["ORDER_PROP"]["USER_PROFILES"])) {?>
			<div class="user_profile">
				<div class="label">
					<?if($arParams["ALLOW_NEW_PROFILE"] == "Y"):
						echo GetMessage("SOA_TEMPL_PROP_CHOOSE");
					else:
						echo GetMessage("SOA_TEMPL_EXISTING_PROFILE");
					endif;?>
				</div>
				<div class="block">
					<?if($arParams["ALLOW_NEW_PROFILE"] == "Y"):?>
						<select name="PROFILE_ID" id="ID_PROFILE_ID" class="selectbox" onChange="SetContact(this.value)">
							<option value="0"><?=GetMessage("SOA_TEMPL_PROP_NEW_PROFILE")?></option>
							<?foreach($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles) {?>
								<option value="<?= $arUserProfiles["ID"] ?>"<?if ($arUserProfiles["CHECKED"]=="Y") echo " selected";?>><?=$arUserProfiles["NAME"]?></option>
							<?}?>
						</select>
					<?else:
						if(count($arResult["ORDER_PROP"]["USER_PROFILES"]) == 1) {
							foreach($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles) {
								echo "<b>".$arUserProfiles["NAME"]."</b>";?>
								<input type="hidden" name="PROFILE_ID" id="ID_PROFILE_ID" value="<?=$arUserProfiles["ID"]?>" />
							<?}
						} else {?>
							<select name="PROFILE_ID" id="ID_PROFILE_ID" class="selectbox" onChange="SetContact(this.value)">
								<?foreach($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles) {?>
									<option value="<?= $arUserProfiles["ID"] ?>"<?if ($arUserProfiles["CHECKED"]=="Y") echo " selected";?>><?=$arUserProfiles["NAME"]?></option>
								<?}?>
							</select>
						<?}
					endif;?>
				</div>
				<div class="clr"></div>
			</div>
		<?}
		PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_N"], $arParams["TEMPLATE_LOCATION"]);
		PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_Y"], $arParams["TEMPLATE_LOCATION"]);
		PrintPropsForm($arResult["ORDER_PROP"]["RELATED"], $arParams["TEMPLATE_LOCATION"]);?>
	</div>
</div>

<?if(!CSaleLocation::isLocationProEnabled()):?>
	<div style="display:none;">
		<?$APPLICATION->IncludeComponent("bitrix:sale.ajax.locations", $arParams["TEMPLATE_LOCATION"],
			array(
				"AJAX_CALL" => "N",
				"COUNTRY_INPUT_NAME" => "COUNTRY_tmp",
				"REGION_INPUT_NAME" => "REGION_tmp",
				"CITY_INPUT_NAME" => "tmp",
				"CITY_OUT_LOCATION" => "Y",
				"LOCATION_VALUE" => "",
				"ONCITYCHANGE" => "submitForm()",
			),
			null,
			array('HIDE_ICONS' => 'Y')
		);?>
	</div>
<?endif?>