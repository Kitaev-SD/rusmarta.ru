<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();



$arPrice = array();
if (\Bitrix\Main\Loader::includeModule('catalog'))
{
	$arPrice = CCatalogIBlockParameters::getPriceTypesList();

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


	$arTemplateParameters = array(
		'PRICE_CODE' => array(
			// 'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('PRICE_CODE_TITLE'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES' => $arPrice,
		),
		'STORES' => array(
			// 'PARENT' => 'DETAIL_SETTINGS',
			'NAME' => GetMessage('STORES'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES' => $arStore,
			'ADDITIONAL_VALUES' => 'Y'
		),
		"STIKERS_PROP" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("STIKERS_PROP_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => "HIT",
		),
		"SALE_STIKER" =>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SALE_STIKER"),
			"TYPE" => "STRING",
			"DEFAULT" => "SALE_TEXT",
		),
		"SHOW_DISCOUNT_PERCENT_NUMBER" => Array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SHOW_DISCOUNT_PERCENT_NUMBER_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
	);
}
if (\Bitrix\Main\ModuleManager::isModuleInstalled("sale"))
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
	$arTemplateParameters['BIG_DATA_RCM_TYPE'] = array(
		// 'PARENT' => 'BIG_DATA_SETTINGS',
		'NAME' => GetMessage('CP_BC_TPL_BIG_DATA_RCM_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => $rcmTypeList
	);
	unset($rcmTypeList);
}?>