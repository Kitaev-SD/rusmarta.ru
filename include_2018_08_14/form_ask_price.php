<?global $arAskPriceFilter;?>
<?$APPLICATION->IncludeComponent("altop:forms", "",
	array(
		"IBLOCK_TYPE" => "forms",
		"IBLOCK_ID" => "3",
		"ELEMENT_ID" => $arAskPriceFilter["ELEMENT_ID"],
		"ELEMENT_AREA_ID" => $arAskPriceFilter["ELEMENT_AREA_ID"],
		"ELEMENT_NAME" => $arAskPriceFilter["ELEMENT_NAME"],
		"ELEMENT_PRICE" => "",
		"BUTTON_ID" => $arAskPriceFilter["BUTTON_ID"],
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
	false,
	array("HIDE_ICONS" => isset($arAskPriceFilter["HIDE_ICONS"]) && $arAskPriceFilter["HIDE_ICONS"] == "Y" ? "Y" : "N")	
);?>