<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

if(!Loader::includeModule('iblock'))
	return;

$boolCatalog = CModule::IncludeModule("catalog");

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arProperty = array();
$arProperty_N = array();
$arProperty_X = array();
if(0 < intval($arCurrentValues["IBLOCK_ID"])) {
	$rsProp = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"], "ACTIVE" => "Y"));
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

$arOffers = CIBlockPriceTools::GetOffersIBlock($arCurrentValues["IBLOCK_ID"]);
$OFFERS_IBLOCK_ID = is_array($arOffers)? $arOffers["OFFERS_IBLOCK_ID"]: 0;
$arProperty_Offers = array();
$arProperty_OffersWithoutFile = array();
if($OFFERS_IBLOCK_ID) {
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=> $OFFERS_IBLOCK_ID, "ACTIVE"=>"Y"));
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
}

$arAscDesc = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$arTemplateParameters = array(
	"IBLOCK_TYPE" => array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("IBLOCK_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlockType,
		"REFRESH" => "Y",
	),
	"IBLOCK_ID" => array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("IBLOCK_IBLOCK"),
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
		"DEFAULT" => "rand",
	),
	"ELEMENT_SORT_ORDER" => array(
		"PARENT" => "DATA_SOURCE",
		"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDesc,
		"DEFAULT" => "asc",
		"ADDITIONAL_VALUES" => "Y",
	),
	"DISPLAY_COMPARE" => Array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_DESC_DISPLAY_COMPARE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),	
	"PROPERTY_CODE_MOD" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("IBLOCK_PROPERTY_MOD"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty,
		"ADDITIONAL_VALUES" => "Y",
	),
	"OFFERS_FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("OFFERS_FIELD_CODE"), "VISUAL"),
	"OFFERS_PROPERTY_CODE" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("OFFERS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_Offers,
		"ADDITIONAL_VALUES" => "Y",
	),
	"OFFERS_SORT_FIELD" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("OFFERS_SORT_FIELD"),
		"TYPE" => "LIST",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "sort",
	),
	"OFFERS_SORT_ORDER" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("OFFERS_SORT_ORDER"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDesc,
		"DEFAULT" => "asc",
		"ADDITIONAL_VALUES" => "Y",
	),
	"OFFERS_SORT_FIELD2" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("OFFERS_SORT_FIELD2"),
		"TYPE" => "LIST",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "id",
	),
	"OFFERS_SORT_ORDER2" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage("OFFERS_SORT_ORDER2"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDesc,
		"DEFAULT" => "asc",
		"ADDITIONAL_VALUES" => "Y",
	),
	"OFFERS_LIMIT" => array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage('OFFERS_LIMIT'),
		"TYPE" => "STRING",
		"DEFAULT" => "",
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
		$arTemplateParameters["CONVERT_CURRENCY"] = array(
			'PARENT' => 'PRICES',
			'NAME' => GetMessage('CONVERT_CURRENCY'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
			'REFRESH' => 'Y',
		);

		if(isset($arCurrentValues['CONVERT_CURRENCY']) && 'Y' == $arCurrentValues['CONVERT_CURRENCY']) {
			$arCurrencyList = array();
			$rsCurrencies = CCurrency::GetList(($by = 'SORT'), ($order = 'ASC'));
			while($arCurrency = $rsCurrencies->Fetch()) {
				$arCurrencyList[$arCurrency['CURRENCY']] = $arCurrency['CURRENCY'];
			}
			$arTemplateParameters["CURRENCY_ID"] = array(
				'PARENT' => 'PRICES',
				'NAME' => GetMessage('CURRENCY_ID'),
				'TYPE' => 'LIST',
				'VALUES' => $arCurrencyList,
				'DEFAULT' => CCurrency::GetBaseCurrency(),
				"ADDITIONAL_VALUES" => "Y",
			);
		}
	}

	if($arCurrentValues["USE_GIFTS"] == "Y") {
		$arTemplateParameters["GIFTS_PLACE"] = array(
			"HIDDEN" => "Y"
		);		
		$arTemplateParameters["GIFTS_TEXT_LABEL_GIFT"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_PRODUCT_QUANTITY_VARIABLE"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_PRODUCT_PROPS_VARIABLE"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_SHOW_OLD_PRICE"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_SHOW_DISCOUNT_PERCENT"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_SHOW_NAME"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_SHOW_IMAGE"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_MESS_BTN_BUY"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_MESS_BTN_DETAIL"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_PAGE_ELEMENT_COUNT"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_CONVERT_CURRENCY"] = array(
			"HIDDEN" => "Y"
		);
		$arTemplateParameters["GIFTS_HIDE_NOT_AVAILABLE"] = array(
			"HIDDEN" => "Y"
		);
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
		'PARENT' => 'PRICES',
		"NAME" => GetMessage("OFFERS_CART_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_OffersWithoutFile,
	);
}

$arTemplateParameters["1CB_USE_FILE_FIELD"] = array(	
	"NAME" => GetMessage("1CB_USE_FILE_FIELD"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
	"REFRESH" => "Y"
);
if($arCurrentValues["1CB_USE_FILE_FIELD"] == "Y") {
	$arTemplateParameters["1CB_FILE_FIELD_MULTIPLE"] = array(		
		"NAME" => GetMessage("1CB_FILE_FIELD_MULTIPLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y"
	);
	if($arCurrentValues["1CB_FILE_FIELD_MULTIPLE"] == "Y") {
		$arTemplateParameters["1CB_FILE_FIELD_MAX_COUNT"] = array(			
			"NAME" => GetMessage("1CB_FILE_FIELD_MAX_COUNT"),
			"TYPE" => "TEXT",
			"DEFAULT" => "5"
		);
	}
	$arTemplateParameters["1CB_FILE_FIELD_NAME"] = array(		
		"NAME" => GetMessage("1CB_FILE_FIELD_NAME"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("1CB_FILE_FIELD_NAME_DEFAULT")
	);	
	$ar1CbFileFieldTypes = array(
		"" => GetMessage("1CB_FILE_FIELD_TYPE_ALL"),
		"jpg, gif, bmp, png, jpeg" => GetMessage("1CB_FILE_FIELD_TYPE_IMAGES"),
		"mp3, wav, midi, snd, au, wma" => GetMessage("1CB_FILE_FIELD_TYPE_SOUNDS"),
		"mpg, avi, wmv, mpeg, mpe, flv" => GetMessage("1CB_FILE_FIELD_TYPE_VIDEO"),
		"doc, docx, txt, rtf" => GetMessage("1CB_FILE_FIELD_TYPE_DOCS")
	);
	$arTemplateParameters["1CB_FILE_FIELD_TYPE"] = array(		
		"NAME" => GetMessage("1CB_FILE_FIELD_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $ar1CbFileFieldTypes,
		"DEFAULT" => "",
		"ADDITIONAL_VALUES" => "N",
		"REFRESH" => "N",
		"MULTIPLE" => "N"
	);	
}

$ar1CbRequiredFields = array(
	"NAME" => GetMessage("1CB_REQUIRED_FIELDS_NAME"),
    "PHONE" => GetMessage("1CB_REQUIRED_FIELDS_PHONE"),
    "EMAIL" => GetMessage("1CB_REQUIRED_FIELDS_EMAIL"),
	"MESSAGE" => GetMessage("1CB_REQUIRED_FIELDS_MESSAGE")
);
if($arCurrentValues["1CB_USE_FILE_FIELD"] == "Y") {
	$ar1CbRequiredFields["FILE"] = GetMessage("1CB_REQUIRED_FIELDS_FILE");
}

$arTemplateParameters["1CB_REQUIRED_FIELDS"] = array(	
	"NAME" => GetMessage("1CB_REQUIRED_FIELDS"),
	"TYPE" => "LIST",
	"VALUES" => $ar1CbRequiredFields,
	"DEFAULT" => array("NAME", "PHONE"),
	"ADDITIONAL_VALUES" => "N",
	"REFRESH" => "N",
	"MULTIPLE" => "Y"
);

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
}?>