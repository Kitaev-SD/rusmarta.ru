<?
	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	/** @var array $arCurrentValues */
	/** @global CUserTypeManager $USER_FIELD_MANAGER */
	global $USER_FIELD_MANAGER;
	use Bitrix\Main\Loader;
	use Bitrix\Main\ModuleManager;
	Loader::includeModule('iblock');
	$arSKU = false;
	$boolSKU = false;


	$arSort = CIBlockParameters::GetElementSortFields(
		array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO'),
		array('KEY_LOWERCASE' => 'Y')
	);

	$arIBlocks=Array();
	$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_BANNERS_TYPE"]!="-"?$arCurrentValues["IBLOCK_BANNERS_TYPE"]:"")));
	while($arRes = $db_iblock->Fetch()) $arIBlocks[$arRes["ID"]] = $arRes["NAME"];

	$arTypes = array();
	if ($arCurrentValues["IBLOCK_BANNERS_TYPE_ID"])
	{
		$rsTypes=CIBlockElement::GetList(array(), array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_BANNERS_TYPE_ID"], "ACTIVE" =>"Y"), false, false, array("ID", "IBLOCK_ID", "NAME", "CODE"));
		while($arr=$rsTypes->Fetch()) $arTypes[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
	}
	$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));


	$arPrice = array();
	if (Loader::includeModule("catalog"))
	{
		$arSort = array_merge($arSort, CCatalogIBlockParameters::GetCatalogSortFields());
		$rsPrice=CCatalogGroup::GetList($v1="sort", $v2="asc");
		while($arr=$rsPrice->Fetch())
		{
			$arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
		}
		if ((isset($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID']) > 0)
		{
			$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);
			$boolSKU = !empty($arSKU) && is_array($arSKU);
		}
	}
	else
	{
		$arPrice = $arProperty_N;
	}
	$arRegionPrice = $arPrice;
	$arPrice  = array_merge(array("MINIMUM_PRICE"=>GetMessage("SORT_PRICES_MINIMUM_PRICE"), "MAXIMUM_PRICE"=>GetMessage("SORT_PRICES_MAXIMUM_PRICE"), "REGION_PRICE"=>GetMessage("SORT_PRICES_REGION_PRICE")), $arPrice);

	$arProperty_S = $arProperty_XL = array();
	if (0 < intval($arCurrentValues['IBLOCK_ID']))
	{
		$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
		while ($arr=$rsProp->Fetch())
		{
			if($arr["PROPERTY_TYPE"]=="S")
				$arProperty_S[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
			elseif($arr["MULTIPLE"] == "Y" && $arr["PROPERTY_TYPE"] == "L")
				$arProperty_XL[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		}
	}

	/* get component template pages & params array */
	$arPageBlocksParams = array();
	if(\Bitrix\Main\Loader::includeModule('aspro.next')){
		$arPageBlocks = CNext::GetComponentTemplatePageBlocks(__DIR__);

		$arPageBlocksParams = CNext::GetComponentTemplatePageBlocksParams($arPageBlocks);
		CNext::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams, array('SECTION' => 'CATALOG_PAGE', 'OPTION' => 'CATALOG')); // add option value FROM_MODULE
		if($arPageBlocks["BIGDATA"])
		{
			$arPageBlocksParams["BIGDATA_NORMAL"] = array(
				"SORT" => 100,
				"NAME" => GetMessage("BIGDATA_NORMAL_TITLE"),
				"TYPE" => "LIST",
				"VALUES" => $arPageBlocks["BIGDATA"],
				"DEFAULT" => "bigdata_1",
				"PARENT" => "BASE",
				"TYPE" => "LIST",
			);
			$arPageBlocksParams["BIGDATA_EXT"] = array(
				"SORT" => 100,
				"NAME" => GetMessage("BIGDATA_EXT_TITLE"),
				"TYPE" => "LIST",
				"VALUES" => $arPageBlocks["BIGDATA"],
				"DEFAULT" => "bigdata_2",
				"PARENT" => "BASE",
				"TYPE" => "LIST",
			);
		}
	}

	$arUserFields_S = array();
	$arUserFields_E = array();
	$arUserFields = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION");
	foreach($arUserFields as $FIELD_NAME=>$arUserField) {
		if($arUserField["USER_TYPE"]["BASE_TYPE"]=="enum")
			{ $arUserFields_E[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME; }
		if($arUserField["USER_TYPE"]["BASE_TYPE"]=="string")
			{ $arUserFields_S[$FIELD_NAME] = $arUserField["LIST_COLUMN_LABEL"]? $arUserField["LIST_COLUMN_LABEL"]: $FIELD_NAME; }
	}

	$arTemplateParametersParts = array();

	$arTemplateParametersParts[]["SHOW_HOW_BUY"] = array(
		"NAME" => GetMessage("SHOW_HOW_BUY"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	);
	if($arCurrentValues["SHOW_HOW_BUY"] != "N")
	{
		$arTemplateParametersParts[]["TITLE_HOW_BUY"] = array(
			"NAME" => GetMessage("TITLE_HOW_BUY"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("VALUE_HOW_BUY"),
		);
	}
	$arTemplateParametersParts[]["SHOW_DELIVERY"] = array(
		"NAME" => GetMessage("SHOW_DELIVERY"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	);
	if($arCurrentValues["SHOW_DELIVERY"] != "N")
	{
		$arTemplateParametersParts[]["TITLE_DELIVERY"] = array(
			"NAME" => GetMessage("TITLE_DELIVERY"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("VALUE_DELIVERY"),
		);
	}
	$arTemplateParametersParts[]["SHOW_PAYMENT"] = array(
		"NAME" => GetMessage("SHOW_PAYMENT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	);
	if($arCurrentValues["SHOW_PAYMENT"] != "N")
	{
		$arTemplateParametersParts[]["TITLE_PAYMENT"] = array(
			"NAME" => GetMessage("TITLE_PAYMENT"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("VALUE_PAYMENT"),
		);
	}
	$arTemplateParametersParts[]["SHOW_GARANTY"] = array(
		"NAME" => GetMessage("SHOW_GARANTY"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	);
	if($arCurrentValues["SHOW_GARANTY"] != "N")
	{
		$arTemplateParametersParts[]["TITLE_GARANTY"] = array(
			"NAME" => GetMessage("TITLE_GARANTY"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("VALUE_GARANTY"),
		);
	}

	$arTemplateParametersParts[] = array_merge($arPageBlocksParams,array(
		"IBLOCK_STOCK_ID" => Array(
			"NAME" => GetMessage("IBLOCK_STOCK_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"SHOW_MEASURE" => Array(
				"NAME" => GetMessage("SHOW_MEASURE"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
		),
		"SORT_BUTTONS" => Array(
			"SORT" => 100,
			"NAME" => GetMessage("SORT_BUTTONS"),
			"TYPE" => "LIST",
			"VALUES" => array("POPULARITY"=>GetMessage("SORT_BUTTONS_POPULARITY"), "NAME"=>GetMessage("SORT_BUTTONS_NAME"), "PRICE"=>GetMessage("SORT_BUTTONS_PRICE"), "QUANTITY"=>GetMessage("SORT_BUTTONS_QUANTITY")),
			"DEFAULT" => array("POPULARITY", "NAME", "PRICE"),
			"PARENT" => "LIST_SETTINGS",
			"TYPE" => "LIST",
			"REFRESH" => "Y",
			"MULTIPLE" => "Y",
		),
	));


	if(is_array($arCurrentValues["SORT_BUTTONS"])){
		if (in_array("PRICE", $arCurrentValues["SORT_BUTTONS"])){
			$arTemplateParametersParts[]["SORT_PRICES"] = Array(
				"SORT"=>200,
				"NAME" => GetMessage("SORT_PRICES"),
				"TYPE" => "LIST",
				"VALUES" => $arPrice,
				"DEFAULT" => array("MINIMUM_PRICE"),
				"PARENT" => "LIST_SETTINGS",
				"MULTIPLE" => "N",
			);
			$arTemplateParametersParts[]["SORT_REGION_PRICE"] = Array(
				"SORT"=>200,
				"NAME" => GetMessage("SORT_REGION_PRICE"),
				"TYPE" => "LIST",
				"VALUES" => $arRegionPrice,
				"DEFAULT" => array("BASE"),
				"PARENT" => "LIST_SETTINGS",
				"MULTIPLE" => "N",
			);
		}
	}

	$detailPictMode = array(
		'IMG' => GetMessage('DETAIL_PICTURE_MODE_IMG'),
		'POPUP' => GetMessage('DETAIL_PICTURE_MODE_POPUP'),
		'MAGNIFIER' => GetMessage('DETAIL_PICTURE_MODE_MAGNIFIER')
	);

	// get iblock properties and group by types
	$arAllPropList = array();
	$arFilePropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$arListPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$arHighloadPropList = array(
		'-' => GetMessage('CP_BC_TPL_PROP_EMPTY')
	);
	$rsProps = CIBlockProperty::GetList(
		array('SORT' => 'ASC', 'ID' => 'ASC'),
		array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'], 'ACTIVE' => 'Y')
	);
	while ($arProp = $rsProps->Fetch())
	{
		$strPropName = '['.$arProp['ID'].']'.('' != $arProp['CODE'] ? '['.$arProp['CODE'].']' : '').' '.$arProp['NAME'];
		if ('' == $arProp['CODE'])
			$arProp['CODE'] = $arProp['ID'];
		$arAllPropList[$arProp['CODE']] = $strPropName;
		if ('F' == $arProp['PROPERTY_TYPE'])
			$arFilePropList[$arProp['CODE']] = $strPropName;
		if ('L' == $arProp['PROPERTY_TYPE'])
			$arListPropList[$arProp['CODE']] = $strPropName;
		if ('S' == $arProp['PROPERTY_TYPE'] && 'directory' == $arProp['USER_TYPE'] && CIBlockPriceTools::checkPropDirectory($arProp))
			$arHighloadPropList[$arProp['CODE']] = $strPropName;
	}

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

	$arTemplateParametersParts[] = array(
		"DEFAULT_LIST_TEMPLATE" => Array(
				"NAME" => GetMessage("DEFAULT_LIST_TEMPLATE"),
				"TYPE" => "LIST",
				"VALUES" => array("block"=>GetMessage("DEFAULT_LIST_TEMPLATE_BLOCK"), "list"=>GetMessage("DEFAULT_LIST_TEMPLATE_LIST"), "table"=>GetMessage("DEFAULT_LIST_TEMPLATE_TABLE")),
				"DEFAULT" => "list",
				"PARENT" => "LIST_SETTINGS",
		),
		"SECTION_DISPLAY_PROPERTY" => Array(
				"NAME" => GetMessage("SECTION_DISPLAY_PROPERTY"),
				"TYPE" => "LIST",
				"VALUES" => $arUserFields_E,
				"DEFAULT" => "list",
				"MULTIPLE" => "N",
				"PARENT" => "LIST_SETTINGS",
		),
		"SECTION_TOP_BLOCK_TITLE" => Array(
				"NAME" => GetMessage("SECTION_TOP_BLOCK_TITLE"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("SECTION_TOP_BLOCK_TITLE_VALUE"),
				"PARENT" => "TOP_SETTINGS",
		),
		"USE_RATING" => array(
				"NAME" => GetMessage("USE_RATING"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
		),
		"SHOW_UNABLE_SKU_PROPS" => array(
				"NAME" => GetMessage("SHOW_UNABLE_SKU_PROPS"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
		),
		"SHOW_ARTICLE_SKU" => array(
			"NAME" => GetMessage("SHOW_ARTICLE_SKU"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"SHOW_MEASURE_WITH_RATIO" => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('SHOW_MEASURE_WITH_RATIO'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		"LIST_DISPLAY_POPUP_IMAGE" => array(
			"NAME" => GetMessage("LIST_DISPLAY_POPUP_IMAGE"),
			"PARENT" => "LIST_SETTINGS",
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT" => "Y",
		),
		"DISPLAY_WISH_BUTTONS" => array(
			"NAME" => GetMessage("DISPLAY_WISH_BUTTONS"),
			"TYPE" => "CHECKBOX",
			"MULTIPLE" => "N",
			"ADDITIONAL_VALUES" => "N",
			"DEFAULT" => "Y",
		),
		"DEFAULT_COUNT" => array(
			"NAME" => GetMessage("DEFAULT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
		),
		"DISPLAY_ELEMENT_SLIDER" => Array(
			"NAME" => GetMessage("DISPLAY_ELEMENT_SLIDER"),
			"TYPE" => "STRING",
			"DEFAULT" => "10",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"DISPLAY_ELEMENT_SLIDER" => Array(
			"NAME" => GetMessage("DISPLAY_ELEMENT_SLIDER"),
			"TYPE" => "STRING",
			"DEFAULT" => "10",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"TITLE_SLIDER" => Array(
			"NAME" => GetMessage("TITLE_SLIDER"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("TITLE_SLIDER_VALUE"),
			"PARENT" => "DETAIL_SETTINGS",
		),
		"VIEW_BLOCK_TYPE" => Array(
			"NAME" => GetMessage("VIEW_BLOCK_TYPE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"PROPERTIES_DISPLAY_LOCATION" => Array(
			"NAME" => GetMessage("PROPERTIES_DISPLAY_LOCATION"),
			"TYPE" => "LIST",
			"VALUES" => array("DESCRIPTION"=>GetMessage("PROPERTIES_DISPLAY_LOCATION_DESCRIPTION"), "TAB"=>GetMessage("PROPERTIES_DISPLAY_LOCATION_TAB")),
			"DEFAULT" => "DESCRIPTION",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"DETAIL_ADD_DETAIL_TO_SLIDER" => array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('CP_BC_TPL_DETAIL_ADD_DETAIL_TO_SLIDER'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N'
		),
		"SHOW_BRAND_PICTURE" => Array(
				"NAME" => GetMessage("SHOW_BRAND_PICTURE"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"SHOW_CHEAPER_FORM" => Array(
				"NAME" => GetMessage("SHOW_CHEAPER_FORM"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"SHOW_SEND_GIFT" => Array(
				"NAME" => GetMessage("SHOW_SEND_GIFT"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"SEND_GIFT_FORM_NAME" => Array(
				"NAME" => GetMessage("SEND_GIFT_FORM_NAME"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"CHEAPER_FORM_NAME" => Array(
				"NAME" => GetMessage("CHEAPER_FORM_NAME"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"SHOW_ASK_BLOCK" => Array(
				"NAME" => GetMessage("SHOW_ASK_BLOCK"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"ASK_FORM_ID" => Array(
				"NAME" => GetMessage("ASK_FORM_ID"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"DETAIL_OFFERS_LIMIT" => Array(
				"NAME" => GetMessage("DETAIL_OFFERS_LIMIT"),
				"TYPE" => "STRING",
				"DEFAULT" => "0",
				"PARENT" => "DETAIL_SETTINGS",
		),
		"DETAIL_EXPANDABLES_TITLE" => Array(
				"NAME" => GetMessage("DETAIL_EXPANDABLES_TITLE"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("DETAIL_EXPANDABLES_VALUE"),
				"PARENT" => "DETAIL_SETTINGS",
		),
		"DETAIL_ASSOCIATED_TITLE" => Array(
				"NAME" => GetMessage("DETAIL_ASSOCIATED_TITLE"),
				"TYPE" => "STRING",
				"DEFAULT" => GetMessage("DETAIL_ASSOCIATED_VALUE"),
				"PARENT" => "DETAIL_SETTINGS",
		),
		"STIKERS_PROP" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("STIKERS_PROP_TITLE"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => array_merge(Array("-"=>" "), $arProperty_XL),
		),
		"SALE_STIKER" =>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SALE_STIKER"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => array_merge(Array("-"=>" "), $arProperty_S),
		),
		"SHOW_ADDITIONAL_TAB" => Array(
			"NAME" => GetMessage("SHOW_ADDITIONAL_TAB"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"SHOW_HINTS" => Array(
			"NAME" => GetMessage("SHOW_HINTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"PROPERTIES_DISPLAY_TYPE" => Array(
			"NAME" => GetMessage("PROPERTIES_DISPLAY_TYPE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => array("BLOCK"=>GetMessage("PROPERTIES_DISPLAY_TYPE_BLOCK"), "TABLE"=>GetMessage("PROPERTIES_DISPLAY_TYPE_TABLE")),
			"DEFAULT" => "BLOCK",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"SHOW_DISCOUNT_PERCENT_NUMBER" => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT_NUMBER'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		"SHOW_DISCOUNT_PERCENT" => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		"ALT_TITLE_GET" => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('ALT_TITLE_GET_TITLE'),
			"VALUES" => array("SEO"=>GetMessage("ALT_TITLE_GET_SEO"), "NORMAL"=>GetMessage("ALT_TITLE_GET_NORMAL")),
			'TYPE' => 'LIST',
			'DEFAULT' => 'NORMAL',
		),
		/*"DETAIL_PICTURE_MODE" => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BCE_TPL_DETAIL_PICTURE_MODE'),
			'TYPE' => 'LIST',
			'DEFAULT' => 'POPUP',
			'VALUES' => $detailPictMode
		),*/
		"SHOW_DISCOUNT_TIME" => Array(
			'PARENT' => 'VISUAL',
			"NAME" => GetMessage("SHOW_DISCOUNT_TIME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_COUNTER_LIST" => Array(
			"NAME" => GetMessage("SHOW_COUNTER_LIST"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"SORT" => 100,
			"PARENT" => "VISUAL",
		),
		"SHOW_DISCOUNT_TIME_EACH_SKU" => Array(
			"NAME" => GetMessage("SHOW_DISCOUNT_TIME_EACH_SKU"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"SORT" => 100,
			"PARENT" => "VISUAL",
		),
		"SHOW_RATING" => Array(
			'PARENT' => 'VISUAL',
			"NAME" => GetMessage("SHOW_RATING"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"SHOW_OLD_PRICE" => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_SHOW_OLD_PRICE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		"RESTART" => array(
			'PARENT' => 'SEARCH_SETTINGS',
			'NAME' => GetMessage('RESTART'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		"USE_LANGUAGE_GUESS" => array(
			'PARENT' => 'SEARCH_SETTINGS',
			'NAME' => GetMessage('USE_LANGUAGE_GUESS'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		"NO_WORD_LOGIC" => array(
			'PARENT' => 'SEARCH_SETTINGS',
			'NAME' => GetMessage('NO_WORD_LOGIC'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
		),
		"SHOW_LANDINGS_SEARCH" => array(
			'PARENT' => 'SEARCH_SETTINGS',
			'NAME' => GetMessage('SHOW_LANDINGS_SEARCH_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y',
		),
	);

	if($arCurrentValues["SHOW_LANDINGS_SEARCH"] !== 'N'){
		$arTemplateParametersParts[] = Array(
			"LANDING_SEARCH_TITLE" => Array(
				"NAME" => GetMessage("LANDING_SEARCH_TITLE"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"PARENT" => "SEARCH_SETTINGS",
			),
			"LANDING_SEARCH_COUNT" => Array(
				"NAME" => GetMessage("LANDING_SEARCH_COUNT"),
				"TYPE" => "STRING",
				"DEFAULT" => "7",
				"PARENT" => "SEARCH_SETTINGS",
			),
		);
	}

	$arTemplateParametersParts[]["SECTIONS_LIST_PREVIEW_PROPERTY"] = Array(
		"NAME" => GetMessage("SHOW_SECTION_PREVIEW_PROPERTY"),
		"VALUES" => array_merge(array("DESCRIPTION"=>GetMessage("SHOW_SECTION_PREVIEW_PROPERTY_DESCRIPTION")), $arUserFields_S),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"DEFAULT" => "DESCRIPTION",
		"PARENT" => "SECTIONS_SETTINGS",
	);

	$arTemplateParametersParts[]["SECTION_PREVIEW_PROPERTY"] = Array(
		"NAME" => GetMessage("SHOW_SECTION_PREVIEW_PROPERTY"),
		"VALUES" => array_merge(array("DESCRIPTION"=>GetMessage("SHOW_SECTION_PREVIEW_PROPERTY_DESCRIPTION")), $arUserFields_S),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"DEFAULT" => "DESCRIPTION",
		"PARENT" => "LIST_SETTINGS");
	$arTemplateParametersParts[]["SECTIONS_LIST_PREVIEW_DESCRIPTION"] = Array(
		"NAME" => GetMessage("SHOW_SECTION_ROOT_PREVIEW"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"PARENT" => "SECTIONS_SETTINGS");


	$arTemplateParametersParts[] = Array(
		"SHOW_SECTION_LIST_PICTURES" => Array(
			"NAME" => GetMessage("SHOW_SECTION_PICTURES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "SECTIONS_SETTINGS",
		),
		"SHOW_SECTION_PICTURES" => Array(
			"NAME" => GetMessage("SHOW_SECTION_PICTURES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "LIST_SETTINGS",
		),
		"SHOW_SECTION_DESC" => Array(
			"NAME" => GetMessage("SHOW_SECTION_DESC"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "LIST_SETTINGS",
		),
		"SHOW_LANDINGS" => array(
			'PARENT' => 'LIST_SETTINGS',
			'NAME' => GetMessage('SHOW_LANDINGS_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y',
		),
		"SHOW_KIT_PARTS" => Array(
			"NAME" => GetMessage("SHOW_KIT_PARTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "N",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"SHOW_KIT_PARTS_PRICES" => Array(
			"NAME" => GetMessage("SHOW_KIT_PARTS_PRICES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "N",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"SHOW_ONE_CLICK_BUY" => Array(
			"NAME" => GetMessage("SHOW_ONE_CLICK_BUY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "N",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"USE_SHARE" => Array(
			"NAME" => GetMessage("USE_SHARE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"SKU_DETAIL_ID" => Array(
			"NAME" => GetMessage("SKU_DETAIL_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "oid",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"USE_ADDITIONAL_GALLERY" => Array(
			"NAME" => GetMessage("USE_ADDITIONAL_GALLERY"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y",
			"PARENT" => "DETAIL_SETTINGS",
		),
	);

	if($arCurrentValues["SHOW_LANDINGS"] !== 'N'){
		$arTemplateParametersParts[] = Array(
			"LANDING_POSITION" => Array(
				"NAME" => GetMessage("LANDING_POSITION_TITLE"),
				"TYPE" => "LIST",
				"DEFAULT" => "AFTER_PRODUCTS",
				"PARENT" => "LIST_SETTINGS",
				"VALUES" => array(
					'BEFORE_PRODUCTS' => GetMessage('LANDING_POSITION_BEFORE_PRODUCTS'),
					'AFTER_PRODUCTS' => GetMessage('LANDING_POSITION_AFTER_PRODUCTS'),
					//'AFTER_DETAIL_TEXT' => GetMessage('LANDING_POSITION_AFTER_DETAIL_TEXT'),
				),
			),
			"LANDING_TITLE" => Array(
				"NAME" => GetMessage("LANDING_TITLE"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"PARENT" => "LIST_SETTINGS",
			),
			"LANDING_SECTION_COUNT" => Array(
				"NAME" => GetMessage("LANDING_SECTION_COUNT"),
				"TYPE" => "STRING",
				"DEFAULT" => "7",
				"PARENT" => "LIST_SETTINGS",
			),
		);
	}

	if($arCurrentValues['USE_ADDITIONAL_GALLERY'] === 'Y'){
		$arTemplateParametersParts[] = Array(
			"ADDITIONAL_GALLERY_TYPE" => Array(
				"NAME" => GetMessage("ADDITIONAL_GALLERY_TYPE"),
				"TYPE" => "LIST",
				"DEFAULT" => "BIG",
				"PARENT" => "DETAIL_SETTINGS",
				"VALUES" => array(
					'BIG' => GetMessage("ADDITIONAL_GALLERY_TYPE_BIG"),
					'SMALL' => GetMessage("ADDITIONAL_GALLERY_TYPE_SMALL"),
				),
			),
			"ADDITIONAL_GALLERY_PROPERTY_CODE" => Array(
				"NAME" => GetMessage("ADDITIONAL_GALLERY_PROPERTY_CODE"),
				"TYPE" => "LIST",
				"DEFAULT" => "-",
				"VALUES" => $arFilePropList,
				"PARENT" => "DETAIL_SETTINGS",
			),
			"ADDITIONAL_GALLERY_OFFERS_PROPERTY_CODE" => Array(
				"NAME" => GetMessage("ADDITIONAL_GALLERY_OFFERS_PROPERTY_CODE"),
				"TYPE" => "LIST",
				"DEFAULT" => "-",
				"VALUES" => $arFileOfferPropList,
				"PARENT" => "DETAIL_SETTINGS",
			),
			"BLOCK_ADDITIONAL_GALLERY_NAME" => Array(
				"NAME" => GetMessage("BLOCK_ADDITIONAL_GALLERY_NAME"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"PARENT" => "DETAIL_SETTINGS",
			),
		);
	}

	$arTemplateParametersParts[] = Array(
		"TAB_OFFERS_NAME" => Array(
			"NAME" => GetMessage("TAB_OFFERS_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"TAB_DESCR_NAME" => Array(
			"NAME" => GetMessage("TAB_DESCR_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"TAB_CHAR_NAME" => Array(
			"NAME" => GetMessage("TAB_CHAR_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"TAB_VIDEO_NAME" => Array(
			"NAME" => GetMessage("TAB_VIDEO_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"TAB_REVIEW_NAME" => Array(
			"NAME" => GetMessage("TAB_REVIEW_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"TAB_FAQ_NAME" => Array(
			"NAME" => GetMessage("TAB_FAQ_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"TAB_STOCK_NAME" => Array(
			"NAME" => GetMessage("TAB_STOCK_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"TAB_DOPS_NAME" => Array(
			"NAME" => GetMessage("TAB_DOPS_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"BLOCK_SERVICES_NAME" => Array(
			"NAME" => GetMessage("BLOCK_SERVICES_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"BLOCK_LANDINGS_NAME" => Array(
			"NAME" => GetMessage("BLOCK_LANDINGS_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"BLOCK_DOCS_NAME" => Array(
			"NAME" => GetMessage("BLOCK_DOCS_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"BLOG_IBLOCK_ID" => Array(
			"NAME" => GetMessage("BLOG_IBLOCK_ID_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"BLOCK_BLOG_NAME" => Array(
			"NAME" => GetMessage("BLOCK_BLOG_NAME_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"RECOMEND_COUNT" => Array(
			"NAME" => GetMessage("RECOMEND_COUNT_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "5",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"VISIBLE_PROP_COUNT" => Array(
			"NAME" => GetMessage("VISIBLE_PROP_COUNT_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "4",
			"PARENT" => "DETAIL_SETTINGS",
		),
		"BUNDLE_ITEMS_COUNT" => Array(
			"NAME" => GetMessage("BUNDLE_ITEMS_COUNT_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "3",
			"PARENT" => "DETAIL_SETTINGS",
		),
		'USE_DETAIL_PREDICTION' => array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('USE_DETAIL_PREDICTION_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		"AJAX_FILTER_CATALOG" => Array(
			"NAME" => GetMessage("AJAX_FILTER_CATALOG_TITLE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "N",
			"PARENT" => "FILTER_SETTINGS",
		),
		"USE_FILTER_PRICE" => Array(
			"NAME" => GetMessage("USE_FILTER_PRICE_TITLE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"PARENT" => "FILTER_SETTINGS",
		),
		"DISPLAY_ELEMENT_COUNT" => Array(
			"NAME" => GetMessage("DISPLAY_ELEMENT_COUNT_TITLE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "N",
			"PARENT" => "FILTER_SETTINGS",
		),
		"STORES_FILTER" => Array(
			"NAME" => GetMessage("STORES_FILTER_TITLE"),
			"TYPE" => "LIST",
			"DEFAULT" => "TITLE",
			"VALUES" => array(
				"TITLE" => GetMessage("STORES_FILTER_NAME_TITLE"),
				"SORT" => GetMessage("STORES_FILTER_SORT_TITLE"),
				"AMOUNT" => GetMessage("STORES_FILTER_AMOUNT_TITLE"),
			),
			"PARENT" => "STORE_SETTINGS",
		),
		"STORES_FILTER_ORDER" => Array(
			"NAME" => GetMessage("STORES_FILTER_ORDER_TITLE"),
			"TYPE" => "LIST",
			"DEFAULT" => "SORT_ASC",
			"VALUES" => array(
				"SORT_ASC" => GetMessage("STORES_FILTER_ORDER_ASC_TITLE"),
				"SORT_DESC" => GetMessage("STORES_FILTER_ORDER_DESC_TITLE"),
			),
			"PARENT" => "STORE_SETTINGS",
		),
	);

	$arTemplateParametersParts[] = array(
		'ADD_PICT_PROP' => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_ADD_PICT_PROP'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arFilePropList
		)
	);
	$arTemplateParametersParts[] = array(
		'DETAIL_DOCS_PROP' => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('DETAIL_DOCS_PROP_TTILE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'N',
			'ADDITIONAL_VALUES' => 'N',
			'REFRESH' => 'N',
			'DEFAULT' => '-',
			'VALUES' => $arFilePropList
		)
	);

	if ($boolSKU)
	{
		$arTemplateParametersParts[] = array(
			'OFFER_ADD_PICT_PROP' => array(
				'PARENT' => 'VISUAL',
				'NAME' => GetMessage('CP_BC_TPL_OFFER_ADD_PICT_PROP'),
				'TYPE' => 'LIST',
				'MULTIPLE' => 'N',
				'ADDITIONAL_VALUES' => 'N',
				'REFRESH' => 'N',
				'DEFAULT' => '-',
				'VALUES' => $arFileOfferPropList
			)
		);
		$arTemplateParametersParts[]=array(
			'OFFER_TREE_PROPS' => array(
				'PARENT' => 'OFFERS_SETTINGS',
				'NAME' => GetMessage('OFFERS_SETTINGS'),
				'TYPE' => 'LIST',
				'MULTIPLE' => 'Y',
				'ADDITIONAL_VALUES' => 'N',
				'REFRESH' => 'N',
				'DEFAULT' => '-',
				'VALUES' => $arTreeOfferPropList
			)
		);
		$arTemplateParametersParts[]=array(
			'OFFER_HIDE_NAME_PROPS' => array(
				'PARENT' => 'OFFERS_SETTINGS',
				'NAME' => GetMessage('OFFER_HIDE_NAME_PROPS_TITLE'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'N',
			)
		);
		$arTemplateParametersParts[]=array(
			'OFFER_SHOW_PREVIEW_PICTURE_PROPS' => array(
				'PARENT' => 'OFFERS_SETTINGS',
				'NAME' => GetMessage('OFFER_SHOW_PREVIEW_PICTURE_PROPS_TITLE'),
				'TYPE' => 'LIST',
				'MULTIPLE' => 'Y',
				'ADDITIONAL_VALUES' => 'N',
				'REFRESH' => 'N',
				'DEFAULT' => '-',
				'VALUES' => $arShowPreviewPictuteTreeOfferPropList
			)
		);
	}
	if (ModuleManager::isModuleInstalled("sale"))
	{
		$arTemplateParametersParts[]=array(
			'USE_BIG_DATA' => array(
				'PARENT' => 'BIG_DATA_SETTINGS',
				'NAME' => GetMessage('CP_BC_TPL_USE_BIG_DATA'),
				'TYPE' => 'CHECKBOX',
				'DEFAULT' => 'Y',
				'REFRESH' => 'Y'
			)
		);
		if (!isset($arCurrentValues['USE_BIG_DATA']) || $arCurrentValues['USE_BIG_DATA'] == 'Y')
		{
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
			$arTemplateParametersParts[]=array(
				'BIG_DATA_RCM_TYPE' => array(
					'PARENT' => 'BIG_DATA_SETTINGS',
					'NAME' => GetMessage('CP_BC_TPL_BIG_DATA_RCM_TYPE'),
					'TYPE' => 'LIST',
					'VALUES' => $rcmTypeList
				)
			);
			unset($rcmTypeList);
		}
	}


	//merge parameters to one array
	$arTemplateParameters = array();
	foreach($arTemplateParametersParts as $i => $part) { $arTemplateParameters = array_merge($arTemplateParameters, $part); }
?>
