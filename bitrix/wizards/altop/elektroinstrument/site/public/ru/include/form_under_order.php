<?global $arUnderOrderFilter;?>
<?$APPLICATION->IncludeComponent("altop:forms", "",
	array(
		"IBLOCK_TYPE" => "forms",
		"IBLOCK_ID" => "#UNDER_ORDER_IBLOCK_ID#",
		"ELEMENT_ID" => $arUnderOrderFilter["ELEMENT_ID"],
		"ELEMENT_AREA_ID" => $arUnderOrderFilter["ELEMENT_AREA_ID"],
		"ELEMENT_NAME" => $arUnderOrderFilter["ELEMENT_NAME"],
		"ELEMENT_PRICE" => "",		
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
	false
);?>