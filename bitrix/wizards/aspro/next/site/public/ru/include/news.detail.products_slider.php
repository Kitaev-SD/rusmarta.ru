<?global $arRegion;
if($arRegion)
{
	if($arRegion['LIST_PRICES'])
	{
		if(reset($arRegion['LIST_PRICES']) != 'component')
			$arParams['PRICE_CODE'] = array_keys($arRegion['LIST_PRICES']);
	}
	if($arRegion['LIST_STORES'])
	{
		if(reset($arRegion['LIST_STORES']) != 'component')
			$arParams['STORES'] = $arRegion['LIST_STORES'];
	}

	$GLOBALS['arrProductsFilter']['IBLOCK_ID'] = \Bitrix\Main\Config\Option::get("aspro.next", "CATALOG_IBLOCK_ID", "#IBLOCK_CATALOG_ID#");
	CNext::makeElementFilterInRegion($GLOBALS['arrProductsFilter']);
}
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.top",
	"products_slider",
	array(
		"IBLOCK_TYPE" => "#IBLOCK_NEXT_CATALOG_TYPE#",
		"IBLOCK_ID" => \Bitrix\Main\Config\Option::get("aspro.next", "CATALOG_IBLOCK_ID", "#IBLOCK_CATALOG_ID#"),
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_COUNT" => ($arParams["LINKED_ELEMENST_PAGE_COUNT"] ? $arParams["LINKED_ELEMENST_PAGE_COUNT"] : 20),
		"LINE_ELEMENT_COUNT" => "",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_LIMIT" => "10",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"BASKET_URL" => SITE_DIR."basket/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"CACHE_GROUPS" => "N",
		"CACHE_FILTER" => "Y",
		"DISPLAY_COMPARE" => "Y",
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"STORES" => $arParams["STORES"],
		"USE_REGION" => ($arRegion ? "Y" : "N"),
		"USE_PRICE_COUNT" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"CUSTOM_FILTER" => $arParams["~CUSTOM_FILTER"],
		"PRODUCT_PROPERTIES" => array(
		),
		"CONVERT_CURRENCY" => "N",
		"FILTER_NAME" => "arrProductsFilter",
		"SHOW_BUY_BUTTONS" => $arParams["SHOW_BUY_BUTTONS"],
		"USE_PRODUCT_QUANTITY" => "N",
		"INIT_SLIDER" => "Y",
		"COMPONENT_TEMPLATE" => "products_slider",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
		"OFFERS_FIELD_CODE" => array(
			0 => "ID",
			1 => "NAME",
			2 => "",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "desc",
		"SEF_MODE" => "N",
		"CACHE_FILTER" => "Y",
		"SHOW_MEASURE" => "Y",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"OFFERS_CART_PROPERTIES" => array(
		),
		"COMPARE_PATH" => "",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_RATING" => "Y",
		"STIKERS_PROP" => $arParams["STIKERS_PROP"],
		"SALE_STIKER" => $arParams["SALE_STIKER"],
		"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
		"TITLE" => $arParams["TITLE"],
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false, array("HIDE_ICONS" => "Y")
);?>