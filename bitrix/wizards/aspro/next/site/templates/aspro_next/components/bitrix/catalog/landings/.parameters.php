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

	$arAscDesc = array(
		"asc" => GetMessage("IBLOCK_SORT_ASC"),
		"desc" => GetMessage("IBLOCK_SORT_DESC"),
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
		while($arr=$rsPrice->Fetch()) $arPrice[$arr["NAME"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
		if ((isset($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID']) > 0)
		{
			$arSKU = CCatalogSKU::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);
			$boolSKU = !empty($arSKU) && is_array($arSKU);
		}
	} else {$arPrice = $arProperty_N;}
	$arPrice  = array_merge(array("MINIMUM_PRICE"=>GetMessage("SORT_PRICES_MINIMUM_PRICE"), "MAXIMUM_PRICE"=>GetMessage("SORT_PRICES_MAXIMUM_PRICE")), $arPrice);

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
		CNext::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams); // add option value FROM_MODULE
	}

	$arTemplateParametersParts = array();

	$arTemplateParametersParts[] = array_merge($arPageBlocksParams, array(
		"INCLUDE_IBLOCK_INTO_CHAIN" => Array(
				"NAME" => GetMessage("T_INCLUDE_IBLOCK_INTO_CHAIN"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "Y",
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
		"ELEMENT_SORT_FIELD_LANDING" => array(
			"PARENT" => "LIST_SETTINGS",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_FIELD_LANDING"),
			"TYPE" => "LIST",
			"VALUES" => $arSort,
			"ADDITIONAL_VALUES" => "Y",
			"DEFAULT" => "sort",
		),
		"ELEMENT_SORT_ORDER_LANDING" => array(
			"PARENT" => "LIST_SETTINGS",
			"NAME" => GetMessage("IBLOCK_ELEMENT_SORT_ORDER_LANDING"),
			"TYPE" => "LIST",
			"VALUES" => $arAscDesc,
			"DEFAULT" => "asc",
			"ADDITIONAL_VALUES" => "Y",
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
		"SHOW_DISCOUNT_PERCENT" => array(
			'PARENT' => 'VISUAL',
			'NAME' => GetMessage('CP_BC_TPL_SHOW_DISCOUNT_PERCENT'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		),
		"SHOW_DISCOUNT_TIME" => Array(
			'PARENT' => 'VISUAL',
			"NAME" => GetMessage("SHOW_DISCOUNT_TIME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
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
		"LIST_FIELD_CODE" => CIBlockParameters::GetFieldCode(GetMessage("IBLOCK_FIELD"), "LIST_SETTINGS"),
		"SHOW_ITEMS" => Array(
			"NAME" => GetMessage("SHOW_ITEMS"),
			"TYPE" => "CHECKBOX",
			"PARENT" => "DETAIL_SETTINGS",
			"DEFAULT" => "Y",
		),
		"SHOW_LANDINGS" => array(
			'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('SHOW_LANDINGS_TITLE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'Y',
			'REFRESH' => 'Y',
		),
	);

	if($arCurrentValues["SHOW_LANDINGS"] !== 'N'){
		$arTemplateParametersParts[] = Array(
			"LANDING_POSITION" => Array(
				"NAME" => GetMessage("LANDING_POSITION_TITLE"),
				"TYPE" => "LIST",
				"DEFAULT" => "BEFORE_PRODUCTS",
				"PARENT" => "DETAIL_SETTINGS",
				"VALUES" => array(
					'BEFORE_PRODUCTS' => GetMessage('LANDING_POSITION_BEFORE_PRODUCTS'),
					'AFTER_PRODUCTS' => GetMessage('LANDING_POSITION_AFTER_PRODUCTS'),
					'AFTER_DETAIL_TEXT' => GetMessage('LANDING_POSITION_AFTER_DETAIL_TEXT'),
				),
			),
			"LANDING_TITLE" => Array(
				"NAME" => GetMessage("LANDING_TITLE_TITLE"),
				"TYPE" => "STRING",
				"DEFAULT" => "",
				"PARENT" => "DETAIL_SETTINGS",
			),
			"LANDING_SECTION_COUNT" => Array(
				"NAME" => GetMessage("LANDING_SECTION_COUNT_TITLE"),
				"TYPE" => "STRING",
				"DEFAULT" => "7",
				"PARENT" => "DETAIL_SETTINGS",
			),
		);
	}

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
	//merge parameters to one array
	$arTemplateParameters = array();
	foreach($arTemplateParametersParts as $i => $part) { $arTemplateParameters = array_merge($arTemplateParameters, $part); }
?>
