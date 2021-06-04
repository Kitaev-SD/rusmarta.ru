 <?$APPLICATION->IncludeComponent(
	"bxmaker:geoip.city", 
	".default", 
	array(
		"BTN_EDIT" => "Изменить город",
		"CACHE_TIME" => "8600000",
		"CACHE_TYPE" => "Y",
		"CITY_COUNT" => "30",
		"CITY_LABEL" => "Ваш город:",
		"CITY_SHOW" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"FAVORITE_SHOW" => "Y",
		"FID" => "0",
		"INFO_SHOW" => "Y",
		"INFO_TEXT" => "<a href=\"#\" rel=\"nofollow\" target=\"_blank\">Подробнее о доставке</a>",
		"INPUT_LABEL" => "Начните вводить название города...",
		"MSG_EMPTY_RESULT" => "Ничего не найдено",
		"POPUP_LABEL" => "МЫ ДОСТАВЛЯЕМ ПО ВСЕЙ РОССИИ",
		"QUESTION_SHOW" => "Y",
		"QUESTION_TEXT" => "Ваш город<br/>#CITY#?",
		"RELOAD_PAGE" => "N",
		"SEARCH_SHOW" => "Y",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?>
<?/*
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
		"COOKIE_TIME" => "36000000",
		"MODE_OPERATION" => "YANDEX"
	),
	false
);?>
*/?>