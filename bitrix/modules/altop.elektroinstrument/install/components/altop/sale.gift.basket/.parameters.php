<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

if(!Loader::includeModule("catalog"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch()) {
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

//Prices
$catalogGroups = array();
$catalogGroupIterator = CCatalogGroup::GetListEx(
	array("NAME" => "ASC", "SORT" => "ASC"),
	array(),
	false,
	false,
	array("ID", "NAME", "NAME_LANG")
);
while($catalogGroup = $catalogGroupIterator->Fetch()) {
	$catalogGroups[$catalogGroup["NAME"]] = "[{$catalogGroup['NAME']}] {$catalogGroup['NAME_LANG']}";
}

$arAscDesc = array(
	"asc" => GetMessage("SGB_SORT_ASC"),
	"desc" => GetMessage("SGB_SORT_DESC"),
);

$showFromSection = isset($arCurrentValues["SHOW_FROM_SECTION"]) && $arCurrentValues["SHOW_FROM_SECTION"] == "Y";

$arComponentParameters = array(
	"GROUPS" => array(
		"PRICES" => array(
			"NAME" => GetMessage("SGB_PRICES"),
		),
		"BASKET" => array(
			"NAME" => GetMessage("SGB_BASKET"),
		),
	),
	"PARAMETERS" => array(
		"BLOCK_TITLE" => array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("SGB_PARAMS_BLOCK_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("SGB_PARAMS_BLOCK_TITLE_DEFAULT"),
		),
		"HIDE_BLOCK_TITLE" => array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("SGB_PARAMS_HIDE_BLOCK_TITLE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "",
		),		
		"DETAIL_URL" => CIBlockParameters::GetPathTemplateParam(
			"DETAIL",
			"DETAIL_URL",
			GetMessage("SGB_DETAIL_URL"),
			"",
			"URL_TEMPLATES"
		),
		"BASKET_URL" => array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("SGB_BASKET_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => "/personal/cart/",
		),
		"ACTION_VARIABLE" => array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("SGB_ACTION_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "action",
		),
		"PRODUCT_ID_VARIABLE" => array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("SGB_PRODUCT_ID_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "id",
		),
		"PRODUCT_QUANTITY_VARIABLE" => array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("SGB_PRODUCT_QUANTITY_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "quantity",
			"HIDDEN" => (isset($arCurrentValues["USE_PRODUCT_QUANTITY"]) && $arCurrentValues["USE_PRODUCT_QUANTITY"] == "Y" ? "N" : "Y")
		),		
		"PRODUCT_PROPS_VARIABLE" => array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("SGB_PRODUCT_PROPS_VARIABLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "prop",
			"HIDDEN" => (isset($arCurrentValues["ADD_PROPERTIES_TO_BASKET"]) && $arCurrentValues["ADD_PROPERTIES_TO_BASKET"] == "N" ? "Y" : "N")
		),		
		"PRICE_CODE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("SGB_PRICE_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "Y",
			"VALUES" => $catalogGroups,
		),
		"SHOW_PRICE_COUNT" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("SGB_SHOW_PRICE_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
		),		
		"PRICE_VAT_INCLUDE" => array(
			"PARENT" => "PRICES",
			"NAME" => GetMessage("SGB_VAT_INCLUDE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"USE_PRODUCT_QUANTITY" => array(
			"PARENT" => "BASKET",
			"NAME" => GetMessage("SGB_USE_PRODUCT_QUANTITY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"PAGE_ELEMENT_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage("SGB_PAGE_ELEMENT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "5",
		),
		"SHOW_FROM_SECTION" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("SGB_SHOW_FROM_SECTION"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
		),
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SGB_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("SGB_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"SECTION_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("SGB_SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "={$GLOBALS['CATALOG_CURRENT_SECTION_ID']}",
			"HIDDEN" => ($showFromSection ? "N" : "Y")
		),
		"SECTION_CODE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("SGB_SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"HIDDEN" => ($showFromSection ? "N" : "Y")
		),
		"SECTION_ELEMENT_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("SGB_SECTION_ELEMENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "={$GLOBALS['CATALOG_CURRENT_ELEMENT_ID']}",
			"HIDDEN" => ($showFromSection ? "N" : "Y")
		),
		"SECTION_ELEMENT_CODE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("SGB_SECTION_ELEMENT_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"HIDDEN" => ($showFromSection ? "N" : "Y")
		),
		"DEPTH" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("SGB_DEPTH"),
			"TYPE" => "STRING",
			"DEFAULT" => "2",
			"HIDDEN" => ($showFromSection ? "N" : "Y")
		),
		"CACHE_TIME" => array("DEFAULT"=>36000000),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("SGB_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		)
	)
);

//Params groups
$iblockMap = array();
$iblockIterator = CIBlock::GetList(array("SORT" => "ASC"), array("ACTIVE" => "Y"));
while($iblock = $iblockIterator->fetch()) {
	$iblockMap[$iblock["ID"]] = $iblock;
}

$catalogs = array();
$productsCatalogs = array();
$skuCatalogs = array();
$catalogIterator = CCatalog::GetList(
	array("IBLOCK_ID" => "ASC"),
	array("@IBLOCK_ID" => array_keys($iblockMap)),
	false,
	false,
	array("IBLOCK_ID", "PRODUCT_IBLOCK_ID", "SKU_PROPERTY_ID")
);
while($catalog = $catalogIterator->fetch()) {
	$isOffersCatalog = (int)$catalog["PRODUCT_IBLOCK_ID"] > 0;
	if($isOffersCatalog) {
		$skuCatalogs[$catalog["PRODUCT_IBLOCK_ID"]] = $catalog;
		if(!isset($productsCatalogs[$catalog["PRODUCT_IBLOCK_ID"]]))
			$productsCatalogs[$catalog["PRODUCT_IBLOCK_ID"]] = $catalog;
	} else {
		$productsCatalogs[$catalog["IBLOCK_ID"]] = $catalog;
	}
}

foreach($productsCatalogs as $catalog) {
	$catalog["VISIBLE"] = isset($arCurrentValues["SHOW_PRODUCTS_".$catalog["IBLOCK_ID"]]) && $arCurrentValues["SHOW_PRODUCTS_".$catalog["IBLOCK_ID"]] == "Y";
	$catalogs[] = $catalog;

	if(isset($skuCatalogs[$catalog["IBLOCK_ID"]])) {
		$skuCatalogs[$catalog["IBLOCK_ID"]]["VISIBLE"] = $catalog["VISIBLE"];
		$catalogs[] = $skuCatalogs[$catalog["IBLOCK_ID"]];
	}
}

$defaultListValues = array("-" => getMessage("SGB_UNDEFINED"));
foreach($catalogs as $catalog) {
	$catalogs[$catalog["IBLOCK_ID"]] = $catalog;
	$iblock = $iblockMap[$catalog["IBLOCK_ID"]];
	if((int)$catalog["SKU_PROPERTY_ID"] > 0) // sku
		$groupName = sprintf(getMessage("SGB_GROUP_OFFERS_CATALOG_PARAMS"), $iblock["NAME"]);
	else
		$groupName = sprintf(getMessage("SGB_GROUP_PRODUCT_CATALOG_PARAMS"), $iblock["NAME"]);

	$groupId = "CATALOG_PPARAMS_".$iblock["ID"];
	$arComponentParameters["GROUPS"][$groupId] = array(
		"NAME" => $groupName
	);

	//Params in group
	//1.Display Properties	
	$allProperties = array();	
	$treeProperties = array();

	$propertyIterator = CIBlockProperty::getList(array("SORT" => "ASC", "NAME" => "ASC"), array("IBLOCK_ID" => $iblock["ID"], "ACTIVE" => "Y"));
	while($property = $propertyIterator->fetch()) {
		$property["ID"] = (int)$property["ID"];
		$propertyName = "[".$property["ID"]."]".("" != $property["CODE"] ? "[".$property["CODE"]."]" : "")." ".$property["NAME"];
		if("" == $property["CODE"])
			$property["CODE"] = $property["ID"];

		$allProperties[$property["CODE"]] = $propertyName;
		
		//skip property id
		if($property["ID"] == $catalog["SKU_PROPERTY_ID"])
			continue;

		if("L" == $property["PROPERTY_TYPE"] || "E" == $property["PROPERTY_TYPE"] || ("S" == $property["PROPERTY_TYPE"] && "directory" == $property["USER_TYPE"]))
			$treeProperties[$property["CODE"]] = $propertyName;
	}

	//Properties
	//Common Catalog options
	if((int)$catalog["SKU_PROPERTY_ID"] <= 0) {
		$arComponentParameters["PARAMETERS"]["SHOW_PRODUCTS_".$iblock["ID"]] = array(
			"PARENT" => $groupId,
			"NAME" => GetMessage("SGB_SHOW_PRODUCTS"),
			"TYPE" => "CHECKBOX",
			"REFRESH" => "Y",
			"DEFAULT" => "N"
		);
	}

	$arComponentParameters["PARAMETERS"]["PROPERTY_CODE_".$iblock["ID"]] = array(
		"PARENT" => $groupId,
		"NAME" => GetMessage("SGB_PROPERTY_DISPLAY"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $allProperties,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "",
		"HIDDEN" => (!$catalog["VISIBLE"] ? "Y" : "N")
	);

	//3.Cart properties
	$arComponentParameters["PARAMETERS"]["CART_PROPERTIES_".$iblock["ID"]] = array(
		"PARENT" => $groupId,
		"NAME" => GetMessage("SGB_PROPERTY_ADD_TO_BASKET"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $treeProperties,
		"ADDITIONAL_VALUES" => "Y",
		"HIDDEN" => ((isset($arCurrentValues["ADD_PROPERTIES_TO_BASKET"]) && $arCurrentValues["ADD_PROPERTIES_TO_BASKET"] == "N") ||
			!$catalog["VISIBLE"] ? "Y" : "N")
	);
}

$arComponentParameters["PARAMETERS"]["HIDE_NOT_AVAILABLE"] = array(
	"PARENT" => "DATA_SOURCE",
	"NAME" => GetMessage("SGB_HIDE_NOT_AVAILABLE"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
);

$arComponentParameters["PARAMETERS"]["CONVERT_CURRENCY"] = array(
	"PARENT" => "PRICES",
	"NAME" => GetMessage("SGB_CONVERT_CURRENCY"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
	"REFRESH" => "Y",
);

if(isset($arCurrentValues["CONVERT_CURRENCY"]) && "Y" == $arCurrentValues["CONVERT_CURRENCY"]) {
	$arCurrencyList = array();
	$by = "SORT";
	$order = "ASC";
	$rsCurrencies = CCurrency::GetList($by, $order);
	while($arCurrency = $rsCurrencies->Fetch()) {
		$arCurrencyList[$arCurrency["CURRENCY"]] = $arCurrency["CURRENCY"];
	}
	$arComponentParameters["PARAMETERS"]["CURRENCY_ID"] = array(
		"PARENT" => "PRICES",
		"NAME" => GetMessage("SGB_CURRENCY_ID"),
		"TYPE" => "LIST",
		"VALUES" => $arCurrencyList,
		"DEFAULT" => CCurrency::GetBaseCurrency(),
		"ADDITIONAL_VALUES" => "Y",
	);
}?>