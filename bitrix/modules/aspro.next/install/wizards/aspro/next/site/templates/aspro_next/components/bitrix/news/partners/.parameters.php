<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if(\Bitrix\Main\Loader::includeModule('iblock'))
{
	$arProperty = $arPropertyF = array();
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
	while ($arr=$rsProp->Fetch())
	{
		if($arr["PROPERTY_TYPE"] != "F")
			$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		else
			$arPropertyF[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}

	$arProperty_S = $arProperty_XL = $arProperty_ALL = array();
	if (0 < intval($arCurrentValues['IBLOCK_CATALOG_ID']))
	{
		$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_CATALOG_ID"], "ACTIVE"=>"Y"));
		while ($arr=$rsProp->Fetch())
		{
			if($arr["PROPERTY_TYPE"]=="S")
				$arProperty_S[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			elseif($arr["MULTIPLE"] == "Y" && $arr["PROPERTY_TYPE"] == "L")
				$arProperty_XL[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

			$arProperty_ALL[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}
	}

	$arIBlocks=Array();
	$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_CATALOG_TYPE"]!="-"?$arCurrentValues["IBLOCK_CATALOG_TYPE"]:"")));
	while($arRes = $db_iblock->Fetch()) $arIBlocks[$arRes["ID"]] = $arRes["NAME"]." [".$arRes["CODE"]."]";

	$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));
}

$arPrice = array();
if (\Bitrix\Main\Loader::includeModule("catalog"))
{
	$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
	$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
	while($arr=$rsPrice->Fetch())
	{
		$arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
	}
	if ((isset($arCurrentValues['IBLOCK_CATALOG_ID']) && (int)$arCurrentValues['IBLOCK_CATALOG_ID']) > 0)
	{
		$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['IBLOCK_CATALOG_ID']);
		$boolSKU = !empty($arSKU) && is_array($arSKU);
	}
}
else
{
	$arPrice = $arProperty_N;
}
$arRegionPrice = $arPrice;
$arPrice  = array_merge(array("MINIMUM_PRICE"=>GetMessage("SORT_PRICES_MINIMUM_PRICE"), "MAXIMUM_PRICE"=>GetMessage("SORT_PRICES_MAXIMUM_PRICE"), "REGION_PRICE"=>GetMessage("SORT_PRICES_REGION_PRICE")), $arPrice);

// get offers iblock properties and group by types
if ($boolSKU)
{
	$arAllOfferPropList = array();
	$arFileOfferPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$arTreeOfferPropList = $arShowPreviewPictuteTreeOfferPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$rsProps = CIBlockProperty::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('IBLOCK_ID' => $arSKU['IBLOCK_ID'], 'ACTIVE' => 'Y')
	);
	while ($arProp = $rsProps->Fetch())
	{
		if ($arProp['ID'] == $arSKU['SKU_PROPERTY_ID'])
			continue;
		$arProp['USER_TYPE'] = (string)$arProp['USER_TYPE'];
		$strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
		if ('' == $arProp['CODE'])
			$arProp['CODE'] = $arProp['ID'];

		$arProperty_Offers[$arProp['CODE']] = $strPropName;

		if ('F' == $arProp['PROPERTY_TYPE'])
			$arFileOfferPropList[$arProp['CODE']] = $strPropName;
		if ('N' != $arProp['MULTIPLE'])
			continue;
		if (
			'L' == $arProp['PROPERTY_TYPE']
			|| 'E' == $arProp['PROPERTY_TYPE']
			|| ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
		)
			$arTreeOfferPropList[$arProp['CODE']] = $strPropName;

		if ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp) && strlen($arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])){
			$arShowPreviewPictuteTreeOfferPropList[$arProp['CODE']] = $strPropName;
		}
	}
}

/* get component template pages & params array */
$arPageBlocksParams = array();
if(\Bitrix\Main\Loader::includeModule('aspro.next')){
	$arPageBlocks = CNext::GetComponentTemplatePageBlocks(__DIR__);
	$arPageBlocksParams = CNext::GetComponentTemplatePageBlocksParams($arPageBlocks);
	CNext::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams); // add option value FROM_MODULE
}

$arListView = array(
	'slider' => GetMessage("SLIDER_VIEW"),
	'block' => GetMessage("BLOCK_VIEW"),
);

$arTemplateParameters = array_merge($arPageBlocksParams, array(
	'T_GOODS' => array(
		'SORT' => 704,
		'NAME' => GetMessage('T_GOODS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_GOODS_SECTION' => array(
		'SORT' => 704,
		'NAME' => GetMessage('T_GOODS_SECTION'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_GALLERY' => array(
		'SORT' => 704,
		'NAME' => GetMessage('T_GALLERY'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'LINKED_PRODUCTS_PROPERTY' => array(
		'NAME' => GetMessage('LINKED_PRODUCTS_PROPERTY'),
		'TYPE' => 'LIST',
		'PARENT' => 'DETAIL_SETTINGS',
		'VALUES' => $arProperty,
		'ADDITIONAL_VALUES' => 'Y',
		'DEFAULT' => 'BRAND'
	),
	'SHOW_LINKED_PRODUCTS' => array(
		'NAME' => GetMessage('SHOW_LINKED_PRODUCTS'),
		'TYPE' => 'CHECKBOX',
		'PARENT' => 'DETAIL_SETTINGS',
		'DEFAULT' => 'N',
	),
	"LIST_VIEW" => array(
		"NAME" => GetMessage("LIST_VIEW"),
		"TYPE" => "LIST",
		"PARENT" => "DETAIL_SETTINGS",
		"VALUES" => $arListView,
		"ADDITIONAL_VALUES" => "N",
		"DEFAULT" => "slider"
	),
	'LINKED_ELEMENST_PAGE_COUNT' => array(
		'SORT' => 704,
		'NAME' => GetMessage('LINKED_ELEMENST_PAGE_COUNT'),
		'TYPE' => 'TEXT',
		"PARENT" => "DETAIL_SETTINGS",
		'DEFAULT' => '20',
	),
	"SHOW_MEASURE" => Array(
		"NAME" => GetMessage("SHOW_MEASURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"PARENT" => "DETAIL_SETTINGS",
	),
	/*"AJAX_FILTER_CATALOG" => Array(
		"NAME" => GetMessage("AJAX_FILTER_CATALOG_TITLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"PARENT" => "DETAIL_SETTINGS",
	),*/
	"DEFAULT_LIST_TEMPLATE" => Array(
		"NAME" => GetMessage("DEFAULT_LIST_TEMPLATE"),
		"TYPE" => "LIST",
		"VALUES" => array("block"=>GetMessage("DEFAULT_LIST_TEMPLATE_BLOCK"), "list"=>GetMessage("DEFAULT_LIST_TEMPLATE_LIST"), "table"=>GetMessage("DEFAULT_LIST_TEMPLATE_TABLE")),
		"DEFAULT" => "block",
		"PARENT" => "DETAIL_SETTINGS",
	),
	"SHOW_UNABLE_SKU_PROPS" => array(
		"NAME" => GetMessage("SHOW_UNABLE_SKU_PROPS"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"PARENT" => "DETAIL_SETTINGS",
	),
	"SHOW_ARTICLE_SKU" => array(
		"NAME" => GetMessage("SHOW_ARTICLE_SKU"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"PARENT" => "DETAIL_SETTINGS",
	),
	"SHOW_MEASURE_WITH_RATIO" => array(
		'PARENT' => 'VISUAL',
		'NAME' => GetMessage('SHOW_MEASURE_WITH_RATIO'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		"PARENT" => "DETAIL_SETTINGS",
	),
	"SHOW_DISCOUNT_PERCENT" => array(
		"PARENT" => "DETAIL_SETTINGS",
		'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	"SHOW_DISCOUNT_PERCENT_NUMBER" => array(
		"PARENT" => "DETAIL_SETTINGS",
		'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT_NUMBER'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	"ALT_TITLE_GET" => array(
		"PARENT" => "DETAIL_SETTINGS",
		'NAME' => GetMessage('ALT_TITLE_GET_TITLE'),
		"VALUES" => array("SEO"=>GetMessage("ALT_TITLE_GET_SEO"), "NORMAL"=>GetMessage("ALT_TITLE_GET_NORMAL")),
		'TYPE' => 'LIST',
		'DEFAULT' => 'NORMAL',
	),
	/*"DETAIL_PICTURE_MODE" => array(
		"PARENT" => "DETAIL_SETTINGS",
		'NAME' => GetMessage('CP_BCE_TPL_DETAIL_PICTURE_MODE'),
		'TYPE' => 'LIST',
		'DEFAULT' => 'POPUP',
		'VALUES' => $detailPictMode
	),*/
	"SHOW_DISCOUNT_TIME" => Array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("SHOW_DISCOUNT_TIME"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SHOW_DISCOUNT_TIME_EACH_SKU" => Array(
		"NAME" => GetMessage("SHOW_DISCOUNT_TIME_EACH_SKU"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"SORT" => 100,
		"PARENT" => "DETAIL_SETTINGS",
	),
	"SHOW_RATING" => Array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("SHOW_RATING"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_COMPARE" => Array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("DISPLAY_COMPARE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_WISH_BUTTONS" => Array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("DISPLAY_WISH_BUTTONS"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SHOW_OLD_PRICE" => array(
		"PARENT" => "DETAIL_SETTINGS",
		'NAME' => GetMessage('CP_BC_TPL_SHOW_OLD_PRICE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	"ADD_PROPERTIES_TO_BASKET" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_ADD_PROPERTIES_TO_BASKET"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y"
	),
	"PRODUCT_PROPS_VARIABLE" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_PRODUCT_PROPS_VARIABLE"),
		"TYPE" => "STRING",
		"DEFAULT" => "prop",
		"HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N')
	),
	"PARTIAL_PRODUCT_PROPERTIES" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_PARTIAL_PRODUCT_PROPERTIES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N')
	),
	"PRODUCT_PROPERTIES" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_PRODUCT_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_ALL,
		"HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N')
	),
	"LIST_PROPERTY_CATALOG_CODE" => array(
		"PARENT" => "DETAIL_SETTINGS",
		"SORT" => 100,
		"NAME" => GetMessage("CP_BC_LIST_PRODUCT_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => $arProperty_ALL,
	),
	"SORT_BUTTONS" => Array(
		"SORT" => 100,
		"NAME" => GetMessage("SORT_BUTTONS"),
		"VALUES" => array("POPULARITY"=>GetMessage("SORT_BUTTONS_POPULARITY"), "NAME"=>GetMessage("SORT_BUTTONS_NAME"), "PRICE"=>GetMessage("SORT_BUTTONS_PRICE"), "QUANTITY"=>GetMessage("SORT_BUTTONS_QUANTITY")),
		"DEFAULT" => array("POPULARITY", "NAME", "PRICE"),
		"PARENT" => "DETAIL_SETTINGS",
		"TYPE" => "LIST",
		"REFRESH" => "Y",
		"MULTIPLE" => "Y",
	),
));


if(is_array($arCurrentValues["SORT_BUTTONS"])){
	if (in_array("PRICE", $arCurrentValues["SORT_BUTTONS"])){
		$arTemplateParameters["SORT_PRICES"] = Array(
			"SORT"=>200,
			"NAME" => GetMessage("SORT_PRICES"),
			"TYPE" => "LIST",
			"VALUES" => $arPrice,
			"DEFAULT" => array("MINIMUM_PRICE"),
			"PARENT" => "DETAIL_SETTINGS",
			"MULTIPLE" => "N",
		);
		$arTemplateParameters["SORT_REGION_PRICE"] = Array(
			"SORT"=>200,
			"NAME" => GetMessage("SORT_REGION_PRICE"),
			"TYPE" => "LIST",
			"VALUES" => $arRegionPrice,
			"DEFAULT" => array("BASE"),
			"PARENT" => "DETAIL_SETTINGS",
			"MULTIPLE" => "N",
		);
	}
}

$arTemplateParameters["IBLOCK_CATALOG_TYPE"] = Array(
	"SORT"=>200,
	"NAME" => GetMessage("IBLOCK_CATALOG_TYPE"),
	"TYPE" => "LIST",
	"VALUES" => $arTypesEx,
	"PARENT" => "DETAIL_SETTINGS",
	"MULTIPLE" => "N",
	"REFRESH" => "Y",
);
// if($arCurrentValues["IBLOCK_CATALOG_TYPE"]){
	$arTemplateParameters["IBLOCK_CATALOG_ID"] = Array(
		"SORT"=>200,
		"NAME" => GetMessage("IBLOCK_CATALOG_ID"),
		"TYPE" => "LIST",
		"VALUES" => $arIBlocks,
		"PARENT" => "DETAIL_SETTINGS",
		"MULTIPLE" => "N",
		"REFRESH" => "Y",
	);
// }
if($arCurrentValues["IBLOCK_CATALOG_ID"]){
	$arTemplateParameters["SALE_STIKER"] = Array(
		"NAME" => GetMessage("SALE_STIKER"),
		"TYPE" => "LIST",
		"DEFAULT" => "-",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => array_merge(Array("-"=>" "), $arProperty_S),
		"PARENT" => "DETAIL_SETTINGS",
	);
	$arTemplateParameters["STIKERS_PROP"] = Array(
		"NAME" => GetMessage("STIKERS_PROP_TITLE"),
		"TYPE" => "LIST",
		"DEFAULT" => "-",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => array_merge(Array("-"=>" "), $arProperty_XL),
		"PARENT" => "DETAIL_SETTINGS",
	);
}
if ($boolSKU)
{
	$arTemplateParameters["OFFER_ADD_PICT_PROP"] = Array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_OFFER_ADD_PICT_PROP'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'N',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'N',
		'DEFAULT' => '-',
		'VALUES' => $arFileOfferPropList
	);
	$arTemplateParameters["OFFER_TREE_PROPS"] = Array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('OFFERS_SETTINGS'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'N',
		'DEFAULT' => '-',
		'VALUES' => $arTreeOfferPropList
	);
	$arTemplateParameters["OFFER_HIDE_NAME_PROPS"] = Array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('OFFER_HIDE_NAME_PROPS_TITLE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
	$arTemplateParameters['OFFER_SHOW_PREVIEW_PICTURE_PROPS']=array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('OFFER_SHOW_PREVIEW_PICTURE_PROPS_TITLE'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'ADDITIONAL_VALUES' => 'N',
		'REFRESH' => 'N',
		'DEFAULT' => '-',
		'VALUES' => $arShowPreviewPictuteTreeOfferPropList
	);
	$arTemplateParameters["LIST_OFFERS_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("CP_BC_LIST_OFFERS_FIELD_CODE"), "DETAIL_SETTINGS");
	$arTemplateParameters["LIST_OFFERS_PROPERTY_CODE"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_LIST_OFFERS_PROPERTY_CODE"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_Offers,
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["LIST_OFFERS_LIMIT"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_LIST_OFFERS_LIMIT"),
		"TYPE" => "STRING",
		"DEFAULT" => 5,
	);
	$arTemplateParameters["OFFERS_CART_PROPERTIES"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_OFFERS_PROPERTIES"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arProperty_Offers,
		"HIDDEN" => (isset($arCurrentValues['ADD_PROPERTIES_TO_BASKET']) && $arCurrentValues['ADD_PROPERTIES_TO_BASKET'] == 'N' ? 'Y' : 'N')
	);

	$arTemplateParameters["OFFERS_SORT_FIELD"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_OFFERS_SORT_FIELD"),
		"TYPE" => "LIST",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "sort",
	);
	$arTemplateParameters["OFFERS_SORT_ORDER"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_OFFERS_SORT_ORDER"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDesc,
		"DEFAULT" => "asc",
		"ADDITIONAL_VALUES" => "Y",
	);
	$arTemplateParameters["OFFERS_SORT_FIELD2"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_OFFERS_SORT_FIELD2"),
		"TYPE" => "LIST",
		"VALUES" => $arSort,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "id",
	);
	$arTemplateParameters["OFFERS_SORT_ORDER2"] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("CP_BC_OFFERS_SORT_ORDER2"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDesc,
		"DEFAULT" => "desc",
		"ADDITIONAL_VALUES" => "Y",
	);
}

$arTemplateParameters['SHOW_GALLERY'] = array(
	'NAME' => GetMessage('SHOW_GALLERY'),
	'TYPE' => 'CHECKBOX',
	'SORT' => 707,
	'PARENT' => 'DETAIL_SETTINGS',
	'DEFAULT' => 'Y',
);
$arTemplateParameters['GALLERY_PRODUCTS_PROPERTY'] = array(
	'NAME' => GetMessage('GALLERY_PRODUCTS_PROPERTY'),
	'TYPE' => 'LIST',
	'SORT' => 708,
	'PARENT' => 'DETAIL_SETTINGS',
	'VALUES' => $arPropertyF,
	'ADDITIONAL_VALUES' => 'Y',
	'DEFAULT' => 'PHOTOS'
);
$arTemplateParameters['DEPTH_LEVEL_BRAND'] = array(
	'NAME' => GetMessage('DEPTH_LEVEL_BRAND'),
	'SORT' => 709,
	'TYPE' => 'TEXT',
	'PARENT' => 'DETAIL_SETTINGS',
	'DEFAULT' => '2'
);
$arTemplateParameters['SECTIONS_DETAIL_COUNT'] = array(
	'NAME' => GetMessage('SECTIONS_DETAIL_COUNT_TITLE'),
	'SORT' => 7010,
	'TYPE' => 'STRING',
	'PARENT' => 'DETAIL_SETTINGS',
	'DEFAULT' => '10'
);

$arTemplateParameters['IMAGE_POSITION'] = array(
	'PARENT' => 'LIST_SETTINGS',
	'SORT' => 250,
	'NAME' => GetMessage('IMAGE_POSITION'),
	'TYPE' => 'LIST',
	'VALUES' => array(
		'left' => GetMessage('IMAGE_POSITION_LEFT'),
		'right' => GetMessage('IMAGE_POSITION_RIGHT'),
	),
	'DEFAULT' => 'left',
);

$arTemplateParameters['COUNT_IN_LINE'] = array(
	'PARENT' => 'LIST_SETTINGS',
	'NAME' => GetMessage('COUNT_IN_LINE'),
	'TYPE' => 'STRING',
	'DEFAULT' => '3',
);

$arPrice = array();
if (\Bitrix\Main\Loader::includeModule('catalog'))
{
	$arPrice = CCatalogIBlockParameters::getPriceTypesList();
	$arTemplateParameters['PRICE_CODE'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('PRICE_CODE_TITLE'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'VALUES' => $arPrice,
		'ADDITIONAL_VALUES' => 'Y'
	);

	$arTemplateParameters['USE_PRICE_COUNT'] = array(
		"PARENT" => "DETAIL_SETTINGS",
		"NAME" => GetMessage("IBLOCK_USE_PRICE_COUNT"),
		"TYPE" => "CHECKBOX",
		"REFRESH" => "N",
		"DEFAULT" => "N",
	);

	$arTemplateParameters['CONVERT_CURRENCY'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('CP_BC_CONVERT_CURRENCY'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
		'REFRESH' => 'Y',
	);

	if (isset($arCurrentValues['CONVERT_CURRENCY']) && $arCurrentValues['CONVERT_CURRENCY'] == 'Y')
	{
		$arTemplateParameters['CURRENCY_ID'] = array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('CP_BC_CURRENCY_ID'),
			'TYPE' => 'LIST',
			'VALUES' => Bitrix\Currency\CurrencyManager::getCurrencyList(),
			'DEFAULT' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
			"ADDITIONAL_VALUES" => "Y",
		);
	}

	$arStore = array();
	global $USER_FIELD_MANAGER;
	$storeIterator = CCatalogStore::GetList(
		array(),
		array('ISSUING_CENTER' => 'Y'),
		false,
		false,
		array('ID', 'TITLE')
	);
	while ($store = $storeIterator->GetNext())
		$arStore[$store['ID']] = "[".$store['ID']."] ".$store['TITLE'];

	$userFields = $USER_FIELD_MANAGER->GetUserFields("CAT_STORE", 0, LANGUAGE_ID);
	$propertyUF = array();

	foreach($userFields as $fieldName => $userField)
		$propertyUF[$fieldName] = $userField["LIST_COLUMN_LABEL"] ? $userField["LIST_COLUMN_LABEL"] : $fieldName;

	$arTemplateParameters['STORES'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('STORES'),
		'TYPE' => 'LIST',
		'MULTIPLE' => 'Y',
		'VALUES' => $arStore,
		'ADDITIONAL_VALUES' => 'Y'
	);
	$arTemplateParameters['HIDE_NOT_AVAILABLE'] = array(
		'PARENT' => 'DETAIL_SETTINGS',
		'NAME' => GetMessage('T_HIDE_NOT_AVAILABLE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	);
}
?>