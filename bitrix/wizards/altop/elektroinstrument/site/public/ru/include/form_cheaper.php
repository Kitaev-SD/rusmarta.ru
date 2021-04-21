<?global $arCheaperFilter;?>
<?$APPLICATION->IncludeComponent("altop:forms", "",
	array(
		"IBLOCK_TYPE" => "forms",
		"IBLOCK_ID" => "#CHEAPER_IBLOCK_ID#",
		"ELEMENT_ID" => $arCheaperFilter["ELEMENT_ID"],
		"ELEMENT_AREA_ID" => $arCheaperFilter["ELEMENT_AREA_ID"],
		"ELEMENT_NAME" => $arCheaperFilter["ELEMENT_NAME"],
		"ELEMENT_PRICE" => $arCheaperFilter["ELEMENT_PRICE"],		
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
	false
);?>