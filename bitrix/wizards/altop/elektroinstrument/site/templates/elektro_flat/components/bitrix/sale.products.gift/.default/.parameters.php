<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader,
	Bitrix\Main\Web\Json;

if(!Loader::includeModule("iblock"))
	return;

CBitrixComponent::includeComponentClass($componentName);

$pageElementCount = (int)$arCurrentValues["DEFERRED_PAGE_ELEMENT_COUNT"] ?: 4;
	
$arProperty = array();
if(0 < intval($arCurrentValues["IBLOCK_ID"])) {
	$rsProp = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"], "ACTIVE" => "Y"));
	while($arr = $rsProp->Fetch()) {
		$code = $arr["CODE"];
		$label = "[".$arr["CODE"]."] ".$arr["NAME"];

		if($arr["PROPERTY_TYPE"] != "F")
			$arProperty[$code] = $label;
	}
}

$arSortOffers = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO', 'catalog_PRICE_1'),
	array('KEY_LOWERCASE' => 'Y')
);

$arAscDescOffers = array(
	"asc" => GetMessage("CP_SPG_TPL_IBLOCK_SORT_ASC"),
	"desc" => GetMessage("CP_SPG_TPL_IBLOCK_SORT_DESC"),
);

$arSortOffers['PRICE'] = GetMessage("CP_SPG_TPL_IBLOCK_SORT_OFFERS_PRICE");
$arSortOffers['PROPERTIES'] = GetMessage("CP_SPG_TPL_IBLOCK_SORT_OFFERS_PROPERTIES");

$arTemplateParameters = array(
	"DEFERRED_PRODUCT_ROW_VARIANTS" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_SPG_TPL_DEFERRED_PRODUCT_ROW_VARIANTS"),
		"TYPE" => "CUSTOM",
		"BIG_DATA" => "N",
		"COUNT_PARAM_NAME" => "DEFERRED_PAGE_ELEMENT_COUNT",
		"JS_FILE" => SaleProductsGiftComponent::getSettingsScript($templateFolder, "dragdrop_add"),
		"JS_EVENT" => "initDraggableAddControl",
		"JS_MESSAGES" => Json::encode(array(
			"variant" => GetMessage("CP_SPG_TPL_SETTINGS_VARIANT"),
			"delete" => GetMessage("CP_SPG_TPL_SETTINGS_DELETE"),
			"quantity" => GetMessage("CP_SPG_TPL_SETTINGS_QUANTITY"),
			"quantityBigData" => GetMessage("CP_SPG_TPL_SETTINGS_QUANTITY_BIG_DATA")
		)),
		"JS_DATA" => Json::encode(SaleProductsGiftComponent::getTemplateVariantsMap()),
		"DEFAULT" => Json::encode(SaleProductsGiftComponent::predictRowVariants($pageElementCount, $pageElementCount))
	),
	"DISPLAY_IMG_WIDTH" => array(
		"NAME" => GetMessage("CP_SPG_TPL_DISPLAY_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "178",
	),
	"DISPLAY_IMG_HEIGHT" => array(
		"NAME" => GetMessage("CP_SPG_TPL_DISPLAY_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "178",
	),
	"PROPERTY_CODE_MOD" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_SPG_TPL_PROPERTY_CODE_MOD"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty,
		"ADDITIONAL_VALUES" => "Y",
	),
	"ELEMENT_SORT_FIELD2" => array(
		"HIDDEN" => "Y"
	),
	"ELEMENT_SORT_ORDER2" => array(
		"HIDDEN" => "Y"
	),
	"OFFERS_SORT_FIELD" => array(
		"PARENT" => "SORT_SETTINGS",
		"NAME" => GetMessage("CP_SPG_TPL_OFFERS_SORT_FIELD"),
		"TYPE" => "LIST",
		"VALUES" => $arSortOffers,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "sort",
	),
	"OFFERS_SORT_ORDER" => array(
		"PARENT" => "SORT_SETTINGS",
		"NAME" => GetMessage("CP_SPG_TPL_OFFERS_SORT_ORDER"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDescOffers,
		"DEFAULT" => "asc",
		"ADDITIONAL_VALUES" => "Y",
	),
	"OFFERS_SORT_FIELD2" => array(
		"DEFAULT" => "sort",
		"HIDDEN" => "Y"
	),
	"OFFERS_SORT_ORDER2" => array(
		"DEFAULT" => "asc",
		"HIDDEN" => "Y"
	)
);?>