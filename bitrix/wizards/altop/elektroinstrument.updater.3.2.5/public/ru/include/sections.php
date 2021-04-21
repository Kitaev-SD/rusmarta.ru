<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "panel",
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "#ITEMS_IBLOCK_ID#",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",		
		"TOP_DEPTH" => 2,
		"SECTION_FIELDS" => array(),
		"SECTION_USER_FIELDS" => array(
			0 => "UF_ICON"
		),
		"SECTION_URL" => "",
		"ADD_SECTIONS_CHAIN" => "N",
		"DISPLAY_IMG_WIDTH" => 50,
		"DISPLAY_IMG_HEIGHT" => 50
	),
	false
);?>