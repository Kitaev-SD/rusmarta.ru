<div class="visible_mobile_filter">
	<?$APPLICATION->IncludeComponent(
		"aspro:catalog.smart.filter",
		($arParams["AJAX_FILTER_CATALOG"]=="Y" ? "main_ajax" : "main"),
		Array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $catalogIBlockID,
			"AJAX_FILTER_FLAG" => $isAjaxFilter,
			"SECTION_ID" => '',
			"FILTER_NAME" => $arParams["FILTER_NAME"],
			"PRICE_CODE" => ($arParams["USE_FILTER_PRICE"] == 'Y' ? $arParams["FILTER_PRICE_CODE"] : $arParams["PRICE_CODE"]),
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_NOTES" => "",
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SECTION_IDS" => ($setionIDRequest ? array($setionIDRequest) : $arSectionsID),
			"ELEMENT_IDS" => ($setionIDRequest ? $arAllSections[$setionIDRequest]["ITEMS"] : $arItemsID),
			"SAVE_IN_SESSION" => "N",
			"XML_EXPORT" => "Y",
			"SECTION_TITLE" => "NAME",
			"SECTION_DESCRIPTION" => "DESCRIPTION",
			"SHOW_HINTS" => $arParams["SHOW_HINTS"],
			'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
			'CURRENCY_ID' => $arParams['CURRENCY_ID'],
			'DISPLAY_ELEMENT_COUNT' => $arParams['DISPLAY_ELEMENT_COUNT'],
			"INSTANT_RELOAD" => "Y",
			"VIEW_MODE" => strtolower($arTheme["FILTER_VIEW"]["VALUE"]),
			"SEF_MODE" => (strlen($arResult["URL_TEMPLATES"]["smart_filter"]) ? "Y" : "N"),
			"SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
			"SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"SORT_BUTTONS" => $arParams["SORT_BUTTONS"],
			"SORT_PRICES" => $arParams["SORT_PRICES"],
			"AVAILABLE_SORT" => $arAvailableSort,
			"SORT" => $sort,
			"SORT_ORDER" => $sort_order,
		),
		$component);
	?>
</div>
<?
if(isset($GLOBALS[$arParams["FILTER_NAME"]]["FACET_OPTIONS"]))
	unset($GLOBALS[$arParams["FILTER_NAME"]]["FACET_OPTIONS"]);
if(isset($GLOBALS[$arParams["FILTER_NAME"]]["OFFERS"]))
{
	$GLOBALS[$arParams["FILTER_NAME"]][] = array(
		"=ID" => $GLOBALS[$arParams["FILTER_NAME"]]["=ID"]
	);
}
?>