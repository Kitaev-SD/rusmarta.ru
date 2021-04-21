<?global $arCheaperFilter;?>
<?$APPLICATION->IncludeComponent("altop:forms", "",
	array(
		"IBLOCK_TYPE" => "forms",
		"IBLOCK_ID" => "4",
		"ELEMENT_ID" => $arCheaperFilter["ELEMENT_ID"],
		"ELEMENT_AREA_ID" => $arCheaperFilter["ELEMENT_AREA_ID"],
		"ELEMENT_NAME" => $arCheaperFilter["ELEMENT_NAME"],
		"ELEMENT_PRICE" => $arCheaperFilter["ELEMENT_PRICE"],
		"BUTTON_ID" => $arCheaperFilter["BUTTON_ID"],
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
	false,
	array("HIDE_ICONS" => isset($arCheaperFilter["HIDE_ICONS"]) && $arCheaperFilter["HIDE_ICONS"] == "Y" ? "Y" : "N")	
);?>