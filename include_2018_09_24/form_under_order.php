<?global $arUnderOrderFilter;?>
<?$APPLICATION->IncludeComponent("altop:forms", "",
	array(
		"IBLOCK_TYPE" => "forms",
		"IBLOCK_ID" => "2",
		"ELEMENT_ID" => $arUnderOrderFilter["ELEMENT_ID"],
		"ELEMENT_AREA_ID" => $arUnderOrderFilter["ELEMENT_AREA_ID"],
		"ELEMENT_NAME" => $arUnderOrderFilter["ELEMENT_NAME"],
		"ELEMENT_PRICE" => "",
		"BUTTON_ID" => $arUnderOrderFilter["BUTTON_ID"],
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
	false,
	array("HIDE_ICONS" => isset($arUnderOrderFilter["HIDE_ICONS"]) && $arUnderOrderFilter["HIDE_ICONS"] == "Y" ? "Y" : "N")	
);?>