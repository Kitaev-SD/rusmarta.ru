<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?global $arTheme, $isShowCatalogSections;?>
<?if($isShowCatalogSections):?>
	<?$APPLICATION->IncludeComponent(
	"aspro:catalog.section.list.next",
	"front_sections_theme",
	array(
		"IBLOCK_TYPE" => "#IBLOCK_NEXT_CATALOG_TYPE#",
		"IBLOCK_ID" => "#IBLOCK_CATALOG_ID#",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "N",
		"COUNT_ELEMENTS" => "N",
		"FILTER_NAME" => "arrPopularSections",
		"TOP_DEPTH" => "",
		"SECTION_URL" => "",
		"VIEW_MODE" => "",
		"SHOW_PARENT_NAME" => "N",
		"HIDE_SECTION_NAME" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"SHOW_SECTIONS_LIST_PREVIEW" => "N",
		"SECTIONS_LIST_PREVIEW_PROPERTY" => "N",
		"SECTIONS_LIST_PREVIEW_DESCRIPTION" => "N",
		"SHOW_SECTION_LIST_PICTURES" => "N",
		"TEMPLATE" => $arTheme["FRONT_PAGE_SECTIONS"]["VALUE"],
		"DISPLAY_PANEL" => "N",
		"COMPONENT_TEMPLATE" => "front_sections_theme",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_CODE" => "",
		"SECTION_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"TITLE_BLOCK" => "Популярные категории",
		"TITLE_BLOCK_ALL" => "Весь каталог",
		"ALL_URL" => "catalog/"
	),
	false
);?>
<?endif;?>