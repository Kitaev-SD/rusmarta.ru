<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "??????? ? ?????????? ? ?????????? ?????????? ????????-???????? ? Rusmarta.ru");
$APPLICATION->SetPageProperty("description", "????? ?????? ?????????? ??? ??????????? ????????. ?????? ? ??????????. ??????? ?????? ? ???????????? ? ????????? ?? ???? ?????? ? ????????-???????? Rusmarta.ru");
$APPLICATION->SetTitle("???????");
$APPLICATION->AddHeadString('<link rel="canonical" href="https://'.SITE_SERVER_NAME.str_replace(" ", "",$APPLICATION->GetCurPage()).'">');?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news", 
	".default", 
	array(
		"IBLOCK_TYPE" => "content",
		"IBLOCK_ID" => "20",
		"NEWS_COUNT" => "12",
		"USE_SEARCH" => "N",
		"USE_RSS" => "N",
		"NUM_NEWS" => "20",
		"NUM_DAYS" => "180",
		"YANDEX" => "N",
		"USE_RATING" => "N",
		"USE_CATEGORIES" => "N",
		"USE_REVIEW" => "Y",
		"USE_FILTER" => "N",
		"SORT_BY1" => "created_date",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "created",
		"SORT_ORDER2" => "desc",
		"CHECK_DATES" => "Y",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/news/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_SHADOW" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "N",
		"DISPLAY_PANEL" => "Y",
		"SET_TITLE" => "Y",
		"SET_STATUS_404" => "Y",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"USE_PERMISSIONS" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"LIST_ACTIVE_DATE_FORMAT" => "j F Y",
		"LIST_FIELD_CODE" => array(
			0 => "DATE_ACTIVE_FROM",
			1 => "DATE_CREATE",
			2 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "LINKED",
			1 => "",
		),
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"DISPLAY_NAME" => "Y",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"BROWSER_TITLE" => "-",
		"DETAIL_ACTIVE_DATE_FORMAT" => "j F Y",
		"DETAIL_FIELD_CODE" => array(
			0 => "DATE_CREATE",
			1 => "",
		),
		"DETAIL_PROPERTY_CODE" => array(
			0 => "LINKED",
			1 => "",
		),
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
		"DETAIL_PAGER_TITLE" => "????????",
		"DETAIL_PAGER_TEMPLATE" => "arrows",
		"DETAIL_PAGER_SHOW_ALL" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "???????",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "arrows",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "3600",
		"PAGER_SHOW_ALL" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"COMPONENT_TEMPLATE" => ".default",
		"SET_LAST_MODIFIED" => "N",
		"ADD_ELEMENT_CHAIN" => "Y",
		"DETAIL_SET_CANONICAL_URL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SHOW_404" => "Y",
		"FILE_404" => "",
		"STRICT_SECTION_CHECK" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"MESSAGES_PER_PAGE" => "10",
		"USE_CAPTCHA" => "Y",
		"REVIEW_AJAX_POST" => "Y",
		"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
		"FORUM_ID" => "",
		"URL_TEMPLATES_READ" => "",
		"SHOW_LINK_TO_FORUM" => "Y",
		"SEF_URL_TEMPLATES" => array(
			"news" => "",
			"section" => "",
			"detail" => "#ELEMENT_CODE#/",
		)
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>