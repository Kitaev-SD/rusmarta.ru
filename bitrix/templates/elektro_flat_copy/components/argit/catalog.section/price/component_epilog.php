<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["ITEMS"] as $key => $arElement):
	$strMainID = $this->GetEditAreaId($arElement["ID"]);
	if(!isset($arElement["OFFERS"]) || (isset($arElement["OFFERS"]) && empty($arElement["OFFERS"]))):
		if($arElement["CAN_BUY"] && $arElement["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0):
			//ASK_PRICE//
			global $arAskPriceFilter;
			$arAskPriceFilter = array(
				"ELEMENT_ID" => $arElement["ID"],
				"ELEMENT_AREA_ID" => $strMainID,
				"ELEMENT_NAME" => $arElement["NAME"],
				"BUTTON_ID" => "ask_price_anch_".$strMainID,
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_ask_price.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?elseif(!$arElement["CAN_BUY"]):
			//UNDER_ORDER//
			global $arUnderOrderFilter;
			$arUnderOrderFilter = array(
				"ELEMENT_ID" => $arElement["ID"],
				"ELEMENT_AREA_ID" => $strMainID,
				"ELEMENT_NAME" => $arElement["NAME"],
				"BUTTON_ID" => "under_order_anch_".$strMainID,
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_under_order.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?endif;
	endif;
endforeach;?>