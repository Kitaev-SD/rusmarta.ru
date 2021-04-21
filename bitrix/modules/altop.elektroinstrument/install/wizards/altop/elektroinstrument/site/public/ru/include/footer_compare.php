<?$APPLICATION->IncludeComponent("bitrix:catalog.compare.list", ".default", 
	Array(
		"AJAX_MODE" => "N",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "#ITEMS_IBLOCK_ID#",
		"DETAIL_URL" => "",
		"COMPARE_URL" => SITE_DIR."catalog/compare/",
		"NAME" => "CATALOG_COMPARE_LIST",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>