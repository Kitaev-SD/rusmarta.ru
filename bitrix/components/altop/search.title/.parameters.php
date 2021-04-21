<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("search"))
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
if (0 < intval($arCurrentValues["IBLOCK_ID"]))
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
	while ($arr=$rsProp->Fetch())
	{
		$code = $arr["CODE"];
		$label = "[".$arr["CODE"]."] ".$arr["NAME"];

		if($arr["PROPERTY_TYPE"] != "F")
			$arProperty[$code] = $label;

		if($arr["PROPERTY_TYPE"]=="N")
			$arProperty_N[$code] = $label;

		if($arr["PROPERTY_TYPE"]!="F")
		{
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
if($OFFERS_IBLOCK_ID)
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$OFFERS_IBLOCK_ID, "ACTIVE"=>"Y"));
	while($arr=$rsProp->Fetch())
	{
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

$arSort['PRICE'] = GetMessage("ALTOP_SORT_OFFERS_PRICE");
$arSort['PROPERTIES'] = GetMessage("ALTOP_SORT_OFFERS_PROPERTIES");

$arAscDesc = array(
	"asc" => GetMessage("ALTOP_IBLOCK_SORT_ASC"),
	"desc" => GetMessage("ALTOP_IBLOCK_SORT_DESC"),
);

$arComponentParameters = array(
	"GROUPS" => array(
		"PRICES" => array(
			"NAME" => GetMessage("ALTOP_IBLOCK_PRICES"),
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ALTOP_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ALTOP_IBLOCK_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"PAGE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("ALTOP_BST_FORM_PAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => "#SITE_DIR#catalog/",
		),
		"NUM_CATEGORIES" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ALTOP_BST_NUM_CATEGORIES"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
			"REFRESH" => "Y",
		),
		"TOP_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ALTOP_BST_TOP_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "7",
			"REFRESH" => "Y",
		),
		"ORDER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ALTOP_BST_ORDER"),
			"TYPE" => "LIST",
			"DEFAULT" => "rank",
			"VALUES" => array(
				"date" => GetMessage("ALTOP_BST_ORDER_BY_DATE"),
				"rank" => GetMessage("ALTOP_BST_ORDER_BY_RANK"),
				"title" => GetMessage("ALTOP_BST_ORDER_BY_TITLE"),
			),
		),
		"USE_LANGUAGE_GUESS" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ALTOP_BST_USE_LANGUAGE_GUESS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CHECK_DATES" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ALTOP_BST_CHECK_DATES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),		
		"PROPERTY_CODE_MOD" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("ALTOP_IBLOCK_PROPERTY_MOD"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty,
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("ALTOP_OFFERS_FIELD_CODE"), "VISUAL"),
		"OFFERS_PROPERTY_CODE" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("ALTOP_OFFERS_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arProperty_Offers,
			"ADDITIONAL_VALUES" => "Y",
		),
		"OFFERS_SORT_FIELD" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("ALTOP_OFFERS_SORT_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"OFFERS_SORT_ORDER" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("ALTOP_OFFERS_SORT_ORDER"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
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
		),
		"OFFERS_LIMIT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage('ALTOP_OFFERS_LIMIT'),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"SHOW_PRICE" => Array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("ALTOP_SHOW_PRICE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PRICE_CODE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("ALTOP_IBLOCK_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $arPrice,
		),
		"PRICE_VAT_INCLUDE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("ALTOP_IBLOCK_VAT_INCLUDE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_ADD_TO_CART" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ALTOP_SHOW_ADD_TO_CART"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_ALL_RESULTS" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ALTOP_SHOW_ALL_RESULTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SEARCH_SECTION_ACTIVE" => Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("ALTOP_SEARCH_SECTION_ACTIVE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		'HIDE_NOT_AVAILABLE' => array(
			'PARENT' => 'DATA_SOURCE',
			'NAME' => GetMessage('AST_HIDE_NOT_AVAILABLE_EXT2'),
			'TYPE' => 'LIST',
			'DEFAULT' => 'N',
			'VALUES' => array(
				'Y' => GetMessage('AST_HIDE_NOT_AVAILABLE_HIDE'),
				'N' => GetMessage('AST_HIDE_NOT_AVAILABLE_SHOW')
			),
			'ADDITIONAL_VALUES' => 'N'
		),
		'HIDE_NOT_AVAILABLE_OFFERS' => array(
			'PARENT' => 'DATA_SOURCE',
			'NAME' => GetMessage('AST_HIDE_NOT_AVAILABLE_OFFERS'),
			'TYPE' => 'LIST',
			'DEFAULT' => 'N',
			'VALUES' => array(
				'Y' => GetMessage('AST_HIDE_NOT_AVAILABLE_OFFERS_HIDE'),
				'L' => GetMessage('AST_HIDE_NOT_AVAILABLE_OFFERS_SUBSCRIBE'),
				'N' => GetMessage('AST_HIDE_NOT_AVAILABLE_OFFERS_SHOW')
			)
		)
	),
);

$NUM_CATEGORIES = intval($arCurrentValues["NUM_CATEGORIES"]);
if($NUM_CATEGORIES <= 0)
	$NUM_CATEGORIES = 1;

for($i = 0; $i < $NUM_CATEGORIES; $i++)
{
	$arComponentParameters["GROUPS"]["CATEGORY_".$i] = array(
		"NAME" => GetMessage("ALTOP_BST_NUM_CATEGORY", array("#NUM#" => $i+1))
	);
	$arComponentParameters["PARAMETERS"]["CATEGORY_".$i."_TITLE"] = array(
		"PARENT" => "CATEGORY_".$i,
		"NAME" => GetMessage("ALTOP_BST_CATEGORY_TITLE"),
		"TYPE" => "STRING",
	);

	CSearchParameters::AddFilterParams($arComponentParameters, $arCurrentValues, "CATEGORY_".$i, "CATEGORY_".$i);
}

if($boolCatalog) {
	if(CModule::IncludeModule('currency')) {
		$arComponentParameters["PARAMETERS"]['CONVERT_CURRENCY'] = array(
			'PARENT' => 'PRICES',
			'NAME' => GetMessage('ALTOP_CONVERT_CURRENCY'),
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
			$arComponentParameters['PARAMETERS']['CURRENCY_ID'] = array(
				'PARENT' => 'PRICES',
				'NAME' => GetMessage('ALTOP_CURRENCY_ID'),
				'TYPE' => 'LIST',
				'VALUES' => $arCurrencyList,
				'DEFAULT' => CCurrency::GetBaseCurrency(),
				"ADDITIONAL_VALUES" => "Y",
			);
		}
	}
}

if(!$OFFERS_IBLOCK_ID) {
	unset($arComponentParameters["PARAMETERS"]["OFFERS_FIELD_CODE"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_PROPERTY_CODE"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_FIELD"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_ORDER"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_FIELD2"]);
	unset($arComponentParameters["PARAMETERS"]["OFFERS_SORT_ORDER2"]);
} else {
	$arComponentParameters["PARAMETERS"]["OFFERS_CART_PROPERTIES"] = array(
		"PARENT" => "PRICES",
		"NAME" => GetMessage("ALTOP_OFFERS_CART_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_OffersWithoutFile,
	);
}?>