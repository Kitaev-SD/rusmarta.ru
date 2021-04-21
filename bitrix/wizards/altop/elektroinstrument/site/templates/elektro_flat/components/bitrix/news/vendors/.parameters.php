<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Web\Json;

if(!CModule::IncludeModule("iblock"))
	return;

CBitrixComponent::includeComponentClass("bitrix:catalog.section");

$boolCatalog = CModule::IncludeModule("catalog");

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE_CATALOG"], "ACTIVE" => "Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty = array();
$arProperty_N = array();
$arProperty_X = array();
if(0 < intval($arCurrentValues["IBLOCK_ID_CATALOG"])) {
	$rsProp = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("IBLOCK_ID" => $arCurrentValues["IBLOCK_ID_CATALOG"], "ACTIVE" => "Y"));
	while ($arr=$rsProp->Fetch()) {
		$code = $arr["CODE"];
		$label = "[".$arr["CODE"]."] ".$arr["NAME"];

		if($arr["PROPERTY_TYPE"] != "F")
			$arProperty[$code] = $label;

		if($arr["PROPERTY_TYPE"] == "N")
			$arProperty_N[$code] = $label;

		if($arr["PROPERTY_TYPE"] != "F") {
			if($arr["MULTIPLE"] == "Y")
				$arProperty_X[$code] = $label;
			elseif($arr["PROPERTY_TYPE"] == "L")
				$arProperty_X[$code] = $label;
			elseif($arr["PROPERTY_TYPE"] == "E" && $arr["LINK_IBLOCK_ID"] > 0)
				$arProperty_X[$code] = $label;
		}
	}
}

$arOffers = CIBlockPriceTools::GetOffersIBlock($arCurrentValues["IBLOCK_ID_CATALOG"]);
$OFFERS_IBLOCK_ID = is_array($arOffers)? $arOffers["OFFERS_IBLOCK_ID"]: 0;
$arProperty_Offers = array();
$arProperty_OffersWithoutFile = array();
if($OFFERS_IBLOCK_ID) {
	$rsProp = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("IBLOCK_ID" => $OFFERS_IBLOCK_ID, "ACTIVE" => "Y"));
	while($arr=$rsProp->Fetch()) {
		$arr['ID'] = intval($arr['ID']);
		if ($arOffers['OFFERS_PROPERTY_ID'] == $arr['ID'])
			continue;
		$strPropName = '['.$arr['ID'].']'.('' != $arr['CODE'] ? '['.$arr['CODE'].']' : '').' '.$arr['NAME'];
		if ('' == $arr['CODE'])
			$arr['CODE'] = $arr['ID'];
		$arProperty_Offers[$arr["CODE"]] = $strPropName;
		if ('F' != $arr['PROPERTY_TYPE'])
			$arProperty_OffersWithoutFile[$arr["CODE"]] = $strPropName;
	}
}

$arSort = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
	array('KEY_LOWERCASE' => 'Y')
);

$arPrice = array();
if($boolCatalog) {
	$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
	$rsPrice = CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
} else {
	$arPrice = $arProperty_N;
}

$arAscDesc = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$arTemplateParameters = array(
	"IBLOCK_TYPE_CATALOG" => array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("IBLOCK_TYPE_CATALOG"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlockType,
		"REFRESH" => "Y",
	),
	"IBLOCK_ID_CATALOG" => array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("IBLOCK_ID_CATALOG"),
		"TYPE" => "LIST",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arIBlock,
		"REFRESH" => "Y",
	),
	"DISPLAY_IMG_WIDTH" => Array(
		"NAME" => GetMessage("IBLOCK_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "150",
	),
	"DISPLAY_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("IBLOCK_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "150",
	),
	"ELEMENT_SORT_FIELD" => array(
		"PARENT" => "DATA_SOURCE",
		"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD"),
		"TYPE" => "LIST",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "CATALOG_AVAILIBLE",
	),
	"ELEMENT_SORT_ORDER" => array(
		"PARENT" => "DATA_SOURCE",
		"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDesc,
		"DEFAULT" => "desc",
		"ADDITIONAL_VALUES" => "Y",
	),
	"ELEMENT_SORT_FIELD2" => array(
		"PARENT" => "DATA_SOURCE",
		"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD2"),
		"TYPE" => "LIST",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "CATALOG_AVAILIBLE",
	),
	"ELEMENT_SORT_ORDER2" => array(
		"PARENT" => "DATA_SOURCE",
		"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER2"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDesc,
		"DEFAULT" => "desc",
		"ADDITIONAL_VALUES" => "Y",
	),	
	"DISPLAY_COMPARE" => Array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("T_IBLOCK_DESC_DISPLAY_COMPARE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"PROPERTY_CODE" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("IBLOCK_PROPERTY"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty,
		"ADDITIONAL_VALUES" => "Y",
	),
	"PROPERTY_CODE_MOD" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("IBLOCK_PROPERTY_MOD"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty,
		"ADDITIONAL_VALUES" => "Y",
	),
	"OFFERS_FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("CP_BCT_OFFERS_FIELD_CODE"), "VISUAL"),
	"OFFERS_PROPERTY_CODE" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCT_OFFERS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_Offers,
		"ADDITIONAL_VALUES" => "Y",
	),
	"OFFERS_SORT_FIELD" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCT_OFFERS_SORT_FIELD"),
		"TYPE" => "LIST",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "sort",
	),
	"OFFERS_SORT_ORDER" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCT_OFFERS_SORT_ORDER"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDesc,
		"DEFAULT" => "asc",
		"ADDITIONAL_VALUES" => "Y",
	),
	"OFFERS_SORT_FIELD2" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCT_OFFERS_SORT_FIELD2"),
		"TYPE" => "LIST",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "id",
	),
	"OFFERS_SORT_ORDER2" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("CP_BCT_OFFERS_SORT_ORDER2"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDesc,
		"DEFAULT" => "asc",
		"ADDITIONAL_VALUES" => "Y",
	),
	"PRICE_CODE" => array(
		"PARENT" => "PRICES",
		"NAME" => GetMessage("IBLOCK_PRICE_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arPrice,
	),
	"PRICE_VAT_INCLUDE" => array(
		"PARENT" => "PRICES",
		"NAME" => GetMessage("IBLOCK_VAT_INCLUDE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"USE_PRICE_COUNT" => array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_USE_PRICE_COUNT"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => isset($templateProperties['USE_RATIO_IN_RANGES']) ? "Y" : "N",
		"DEFAULT" => "N",
	),
	"SHOW_PRICE_COUNT" => array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_SHOW_PRICE_COUNT"),
		"TYPE" => "STRING",
		"DEFAULT" => "1"
	),
	"HIDE_SECTION" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("HIDE_SECTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	)
);

if($boolCatalog) {
	$arTemplateParameters['HIDE_NOT_AVAILABLE'] = array(
	'PARENT' => 'DATA_SOURCE',	
	'NAME' => GetMessage('CP_BCT_HIDE_NOT_AVAILABLE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);

	if(CModule::IncludeModule('currency')) {
		$arTemplateParameters['CONVERT_CURRENCY'] = array(
			'PARENT' => 'PRICES',	
			'NAME' => GetMessage('CP_BCT_CONVERT_CURRENCY'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		);

		if(isset($arCurrentValues['CONVERT_CURRENCY']) && 'Y' == $arCurrentValues['CONVERT_CURRENCY']) {
			$arCurrencyList = array();
			$rsCurrencies = CCurrency::GetList(($by = 'SORT'), ($order = 'ASC'));
			while ($arCurrency = $rsCurrencies->Fetch()) {
				$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
			}
			$arTemplateParameters['CURRENCY_ID'] = array(
				'PARENT' => 'PRICES',
				'NAME' => GetMessage('CP_BCT_CURRENCY_ID'),
				'TYPE' => 'LIST',
				'VALUES' => $arCurrencyList,
				'DEFAULT' => CCurrency::GetBaseCurrency(),
				"ADDITIONAL_VALUES" => "Y",
			);
		}
	}
}

if(!$OFFERS_IBLOCK_ID) {
	unset($arTemplateParameters["OFFERS_FIELD_CODE"]);
	unset($arTemplateParameters["OFFERS_PROPERTY_CODE"]);
	unset($arTemplateParameters["OFFERS_SORT_FIELD"]);
	unset($arTemplateParameters["OFFERS_SORT_ORDER"]);
	unset($arTemplateParameters["OFFERS_SORT_FIELD2"]);
	unset($arTemplateParameters["OFFERS_SORT_ORDER2"]);
} else {
	$arTemplateParameters["OFFERS_CART_PROPERTIES"] = array(
		"PARENT" => "PRICES",
		"NAME" => GetMessage("CP_BCT_OFFERS_CART_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_OffersWithoutFile,
	);
}

if(ModuleManager::isModuleInstalled("sale")) {
	$arTemplateParameters['USE_BIG_DATA'] = array(
		'PARENT' => 'BIG_DATA_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_USE_BIG_DATA'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
		'REFRESH' => 'Y'
	);
	if(!isset($arCurrentValues['USE_BIG_DATA']) || $arCurrentValues['USE_BIG_DATA'] == 'Y') {
		$rcmTypeList = array(
			'bestsell' => GetMessage('CP_BC_TPL_RCM_BESTSELLERS'),
			'personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL'),
			'similar_sell' => GetMessage('CP_BC_TPL_RCM_SOLD_WITH'),
			'similar_view' => GetMessage('CP_BC_TPL_RCM_VIEWED_WITH'),
			'similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR'),
			'any_similar' => GetMessage('CP_BC_TPL_RCM_SIMILAR_ANY'),
			'any_personal' => GetMessage('CP_BC_TPL_RCM_PERSONAL_WBEST'),
			'any' => GetMessage('CP_BC_TPL_RCM_RAND')
		);
		$arTemplateParameters['BIG_DATA_RCM_TYPE'] = array(
			'PARENT' => 'BIG_DATA_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_BIG_DATA_RCM_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => $rcmTypeList
		);
		unset($rcmTypeList);
	}
}

$lineElementCount = (int)$arCurrentValues["LINE_ELEMENT_COUNT"] ?: 4;
$pageElementCount = (int)$arCurrentValues["PAGE_ELEMENT_COUNT"] ?: 12;

$arTemplateParameters["PRODUCT_ROW_VARIANTS"] = array(
	"PARENT" => "VISUAL",
	"NAME" => GetMessage("CP_BC_TPL_PRODUCT_ROW_VARIANTS"),
	"TYPE" => "CUSTOM",
	"BIG_DATA" => "N",
	"COUNT_PARAM_NAME" => "PAGE_ELEMENT_COUNT",
	"JS_FILE" => CatalogSectionComponent::getSettingsScript($templateFolder, "dragdrop_add"),
	"JS_EVENT" => "initDraggableAddControl",
	"JS_MESSAGES" => Json::encode(array(
		"variant" => GetMessage("CP_BC_TPL_SETTINGS_VARIANT"),
		"delete" => GetMessage("CP_BC_TPL_SETTINGS_DELETE"),
		"quantity" => GetMessage("CP_BC_TPL_SETTINGS_QUANTITY"),
		"quantityBigData" => GetMessage("CP_BC_TPL_SETTINGS_QUANTITY_BIG_DATA")
	)),
	"JS_DATA" => Json::encode(CatalogSectionComponent::getTemplateVariantsMap()),
	"DEFAULT" => Json::encode(CatalogSectionComponent::predictRowVariants($lineElementCount, $pageElementCount))
);

$arTemplateParameters["LAZY_LOAD"] = array(
	"PARENT" => "PAGER_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_LAZY_LOAD"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);

if(isset($arCurrentValues["LAZY_LOAD"]) && $arCurrentValues["LAZY_LOAD"] === "Y") {
	$arTemplateParameters["MESS_BTN_LAZY_LOAD"] = array(
		"PARENT" => "PAGER_SETTINGS",
		"NAME" => GetMessage("CP_BC_TPL_MESS_BTN_LAZY_LOAD"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("CP_BC_TPL_MESS_BTN_LAZY_LOAD_DEFAULT")
	);
}

$arTemplateParameters["LOAD_ON_SCROLL"] = array(
	"PARENT" => "PAGER_SETTINGS",
	"NAME" => GetMessage("CP_BC_TPL_LOAD_ON_SCROLL"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y"
);?>