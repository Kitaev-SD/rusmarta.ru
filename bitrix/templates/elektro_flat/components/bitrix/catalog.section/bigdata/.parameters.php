<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader,
	Bitrix\Main\Web\Json;

if(!Loader::includeModule("iblock"))
	return;

CBitrixComponent::includeComponentClass($componentName);

$lineElementCount = (int)$arCurrentValues["LINE_ELEMENT_COUNT"] ?: 4;
$pageElementCount = (int)$arCurrentValues["PAGE_ELEMENT_COUNT"] ?: 8;
	
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

$rcmTypeList = array(
	"bestsell" => GetMessage("CP_BC_TPL_RCM_BESTSELLERS"),
	"personal" => GetMessage("CP_BC_TPL_RCM_PERSONAL"),
	"similar_sell" => GetMessage("CP_BC_TPL_RCM_SOLD_WITH"),
	"similar_view" => GetMessage("CP_BC_TPL_RCM_VIEWED_WITH"),
	"similar" => GetMessage("CP_BC_TPL_RCM_SIMILAR"),
	"any_similar" => GetMessage("CP_BC_TPL_RCM_SIMILAR_ANY"),
	"any_personal" => GetMessage("CP_BC_TPL_RCM_PERSONAL_WBEST"),
	"any" => GetMessage("CP_BC_TPL_RCM_RAND")
);

$arSortOffers = CIBlockParameters::GetElementSortFields(
	array("SHOWS", "SORT", "TIMESTAMP_X", "NAME", "ID", "ACTIVE_FROM", "ACTIVE_TO", "catalog_PRICE_1"),
	array("KEY_LOWERCASE" => "Y")
);

$arAscDescOffers = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$arSortOffers["PRICE"] = GetMessage("IBLOCK_SORT_OFFERS_PRICE");
$arSortOffers["PROPERTIES"] = GetMessage("IBLOCK_SORT_OFFERS_PROPERTIES");

$arTemplateParameters = array(
	"PRODUCT_ROW_VARIANTS" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCS_TPL_PRODUCT_ROW_VARIANTS"),
		"TYPE" => "CUSTOM",
		"BIG_DATA" => "Y",
		"COUNT_PARAM_NAME" => "PAGE_ELEMENT_COUNT",
		"JS_FILE" => CatalogSectionComponent::getSettingsScript($templateFolder, "dragdrop_add"),
		"JS_EVENT" => "initDraggableAddControl",
		"JS_MESSAGES" => Json::encode(array(
			"variant" => GetMessage("CP_BCS_TPL_SETTINGS_VARIANT"),
			"delete" => GetMessage("CP_BCS_TPL_SETTINGS_DELETE"),
			"quantity" => GetMessage("CP_BCS_TPL_SETTINGS_QUANTITY"),
			"quantityBigData" => GetMessage("CP_BCS_TPL_SETTINGS_QUANTITY_BIG_DATA")
		)),
		"JS_DATA" => Json::encode(CatalogSectionComponent::getTemplateVariantsMap()),
		"DEFAULT" => Json::encode(CatalogSectionComponent::predictRowVariants($lineElementCount, $pageElementCount))
	),
	"DISPLAY_IMG_WIDTH" => Array(
		"NAME" => GetMessage("DISPLAY_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "178",
	),
	"DISPLAY_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("DISPLAY_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "178",
	),
	"PROPERTY_CODE_MOD" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("PROPERTY_CODE_MOD"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty,
		"ADDITIONAL_VALUES" => "Y",
	),	
	"BIG_DATA_RCM_TYPE" => array(
		"PARENT" => "BIG_DATA_SETTINGS",
		"NAME" => GetMessage("BIG_DATA_RCM_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $rcmTypeList
	),
	"BIG_DATA_TITLE" => array(		
		"PARENT" => "BIG_DATA_SETTINGS",
		"NAME" => GetMessage("BIG_DATA_TITLE"),
		"TYPE" => "CHECKBOX",
		"MULTIPLE" => "N",
		"REFRESH" => "N",
		"DEFAULT" => "Y"
	),
	"ELEMENT_SORT_FIELD2" => array(
		"HIDDEN" => "Y"
	),
	"ELEMENT_SORT_ORDER2" => array(
		"HIDDEN" => "Y"
	),
	"OFFERS_SORT_FIELD" => array(
		"PARENT" => "SORT_SETTINGS",
		"NAME" => GetMessage("CP_BC_OFFERS_SORT_FIELD"),
		"TYPE" => "LIST",
		"VALUES" => $arSortOffers,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "sort",
	),
	"OFFERS_SORT_ORDER" => array(
		"PARENT" => "SORT_SETTINGS",
		"NAME" => GetMessage("CP_BC_OFFERS_SORT_ORDER"),
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
);

    $arTemplateParameters['SHOW_MAX_QUANTITY'] = array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY'),
		'TYPE' => 'LIST',
		'REFRESH' => 'Y',
		'MULTIPLE' => 'N',
		'VALUES' => array(
			'N' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY_N'), 
			'Y' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY_Y'),  
			'M' => GetMessage('CP_BC_TPL_SHOW_MAX_QUANTITY_M')  
		),
		'DEFAULT' => array('N')
	);
	if(isset($arCurrentValues['SHOW_MAX_QUANTITY'])) {
		if($arCurrentValues['SHOW_MAX_QUANTITY'] !== 'N') {
			$arTemplateParameters['MESS_SHOW_MAX_QUANTITY'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_MESS_SHOW_MAX_QUANTITY'),
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('CP_BC_TPL_MESS_SHOW_MAX_QUANTITY_DEFAULT')
			);
		}
		if($arCurrentValues['SHOW_MAX_QUANTITY'] === 'M') {
			$arTemplateParameters['RELATIVE_QUANTITY_FACTOR'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_RELATIVE_QUANTITY_FACTOR'),
				'TYPE' => 'STRING',
				'DEFAULT' => '5'
			);
			$arTemplateParameters['MESS_RELATIVE_QUANTITY_MANY'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_MANY'),
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_MANY_DEFAULT')
			);
			$arTemplateParameters['MESS_RELATIVE_QUANTITY_FEW'] = array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_FEW'),
				'TYPE' => 'STRING',
				'DEFAULT' => GetMessage('CP_BC_TPL_MESS_RELATIVE_QUANTITY_FEW_DEFAULT')
			);
		}
	}
?>