<?$APPLICATION->IncludeComponent(
	"altop:geolocation", 
	".default", 
	array(
		"IBLOCK_TYPE" => "content",
		"IBLOCK_ID" => "7",
		"SHOW_CONFIRM" => "Y",
		"SHOW_DEFAULT_LOCATIONS" => "Y",
		"SHOW_TEXT_BLOCK" => "Y",
		"SHOW_TEXT_BLOCK_TITLE" => "Y",
		"TEXT_BLOCK_TITLE" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"COMPONENT_TEMPLATE" => ".default",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"COOKIE_TIME" => "36000000"
	),
	false
);?>