<?$APPLICATION->IncludeComponent("altop:geolocation", "",
	array(									
		"IBLOCK_TYPE" => "content",
		"IBLOCK_ID" => "#CONTACTS_IBLOCK_ID#",
		"SHOW_CONFIRM" => "Y",
		"SHOW_DEFAULT_LOCATIONS" => "Y",
		"SHOW_TEXT_BLOCK" => "Y",
		"SHOW_TEXT_BLOCK_TITLE" => "Y",
		"TEXT_BLOCK_TITLE" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"COOKIE_TIME" => "36000000"
	),
	false
);?>