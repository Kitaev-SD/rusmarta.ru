<?$APPLICATION->IncludeComponent("altop:search.title", ".default",
	Array(
		"SHOW_INPUT" => "Y",
		"INPUT_ID" => "title-search-input",
		"CONTAINER_ID" => "altop_search",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "#ITEMS_IBLOCK_ID#",
		"PAGE" => "#SITE_DIR#catalog/",
		"NUM_CATEGORIES" => "1",
		"TOP_COUNT" => "7",
		"ORDER" => "rank",
		"USE_LANGUAGE_GUESS" => "N",
		"CHECK_DATES" => "N",		
		"PROPERTY_CODE_MOD" => array(
			0 => "GUARANTEE"
		),
		"OFFERS_FIELD_CODE" => array(),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "COLOR",
			1 => "PROP2",
			2 => "PROP3"
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "asc",
		"OFFERS_LIMIT" => "",
		"SHOW_PRICE" => "Y",
		"PRICE_CODE" => array(
			0 => "BASE"
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"SHOW_ADD_TO_CART" => "Y",
		"SHOW_ALL_RESULTS" => "Y",
		"CATEGORY_0_TITLE" => GetMessage("SEARCH_GOODS"),
		"CATEGORY_0" => array("iblock_catalog"),
		"CATEGORY_0_iblock_catalog" => array("all"),
		"CONVERT_CURRENCY" => "N",
		"CURRENCY_ID" => "",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "COLOR",
			1 => "PROP2",
			2 => "PROP3"
		)
	),
	false
);?> 