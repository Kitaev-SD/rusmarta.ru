<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array();

$arTemplateParameters["COMPACT"] = array(
	"NAME" => GetMessage("T_COMPACT"),
	"TYPE" => "LIST",
	"VALUES" => GetMessage("T_COMPACT_VALUES"),
	"DEFAULT" => "S",
	"REFRESH" => "Y",
	"PARENT" => "VISUAL"
);

if (!empty($arCurrentValues['COMPACT']) && $arCurrentValues['COMPACT'] != 'off') {
	$arTemplateParameters["PRIORITY"] = array(
		"NAME" => GetMessage("T_PRIORITY"),
		"TYPE" => "LIST",
		"VALUES" => GetMessage("T_PRIORITY_VALUES"),
		"DEFAULT" => "B",
		"REFRESH" => "Y",
		"PARENT" => "VISUAL"
	);

	if (!empty($arCurrentValues['PRIORITY']) && $arCurrentValues['PRIORITY'] == 'C') {
		$arTemplateParameters["DELIVERY_BONUS"] = array(
			"NAME" => GetMessage("T_DELIVERY_BONUS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "N",
			"PARENT" => "VISUAL"
		);
		$arTemplateParameters["COD_FILTER_ZERO_TARIFF"] = array(
			"NAME" => GetMessage("T_COD_FILTER_ZERO_TARIFF"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "N",
			"PARENT" => "VISUAL"
		);
	}

	if (!empty($arCurrentValues['PRIORITY']) && $arCurrentValues['PRIORITY'] != 'C') {
		$arTemplateParameters["COD_POST_NAME"] = array(
			"NAME" => GetMessage("T_COD_POST_NAME"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "N",
			"PARENT" => "VISUAL"
		);
	}
}

if (empty($arCurrentValues['COMPACT']) || $arCurrentValues['COMPACT'] == 'off' || !empty($arCurrentValues['PRIORITY']) && $arCurrentValues['PRIORITY'] == 'B') {
	$arTemplateParameters["COD_LIGHT"] = array(
		"NAME" => GetMessage("T_COD_LIGHT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
}

if (!empty($arCurrentValues['COMPACT']) && $arCurrentValues['COMPACT'] != 'off') {
	$arTemplateParameters["SHOP_MAIN"] = array(
		"NAME" => GetMessage("T_SHOP_MAIN"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
}

$arTemplateParameters += array(
	"NO_POST_MAIN" => array(
		"NAME" => GetMessage("T_NO_POST_MAIN"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
	"POST_SMALL" => array(
		"NAME" => GetMessage("T_POST_SMALL"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),

	"USE_PRELOAD_DELIVERY" => array(
		"NAME" => GetMessage("T_USE_PRELOAD_DELIVERY"),
		"TYPE" => "LIST",
		"VALUES" => GetMessage("T_USE_PRELOAD_DELIVERY_VALUES"),
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
	"USE_PRELOAD_PROP" => array(
		"NAME" => GetMessage("T_USE_PRELOAD_PROP"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),

	"ORDER_FORMAT" => array(
		"NAME" => GetMessage("T_ORDER_FORMAT"),
		"TYPE" => "LIST",
		"VALUES" => GetMessage("T_ORDER_FORMAT_VALUES"),
		"DEFAULT" => "progress_compact",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
	"STYLE" => array(
		"NAME" => GetMessage("T_STYLE"),
		"TYPE" => "LIST",
		"VALUES" => GetMessage("T_STYLE_VALUES"),
		"DEFAULT" => "bright",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
	"COLOR" => array(
		"NAME" => GetMessage("T_COLOR"),
		"TYPE" => "LIST",
		"VALUES" => GetMessage("T_COLOR_VALUES"),
		"DEFAULT" => "blue",
		"REFRESH" => "Y",
		"PARENT" => "VISUAL"
	),
);

if (!empty($arCurrentValues['COLOR']) && $arCurrentValues['COLOR'] == 'manual') {
	$arTemplateParameters["COLOR_MANUAL"] = array(
		"NAME" => GetMessage("T_COLOR_MANUAL"),
		"TYPE" => "COLORPICKER",
		"DEFAULT" => "888888",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
}

if (!empty($arCurrentValues['COMPACT']) && in_array($arCurrentValues['COMPACT'], array('Y', 'S'))) {
	$arTemplateParameters["COMPACT_PREPAY_JOIN"] = array(
		"NAME" => GetMessage("T_COMPACT_PREPAY_JOIN"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
}

$arTemplateParameters += array(
	"BORDER_RADIUS" => array(
		"NAME" => GetMessage("T_BORDER_RADIUS"),
		"TYPE" => "STRING",
		"DEFAULT" => "0",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),

	"BORDER_COLOR" => array(
		"NAME" => GetMessage("T_BORDER_COLOR"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),

	"FONT_BIG" => array(
		"NAME" => GetMessage("T_FONT_BIG"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),

	"ACTIVE_LIGHT" => array(
		"NAME" => GetMessage("T_ACTIVE_LIGHT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
	"ACTIVE_LIGHT2" => array(
		"NAME" => GetMessage("T_ACTIVE_LIGHT2"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),

	"PAYMENT_BONUS" => array(
		"NAME" => GetMessage("T_PAYMENT_BONUS"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),

	"NO_INSURANCE" => array(
		"NAME" => GetMessage("T_NO_INSURANCE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),

	"PASSPORT_SMALL" => array(
		"NAME" => GetMessage("T_PASSPORT_SMALL"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
);

$arTemplateParameters += array(
	"COMPACT_CART_SHOW_IMG" => array(
		"NAME" => GetMessage("T_COMPACT_CART_SHOW_IMG"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
	"CART_SHOW_PROPS" => array(
		"NAME" => GetMessage("T_CART_SHOW_PROPS"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
	"CART" => array(
		"NAME" => GetMessage("T_CART"),
		"TYPE" => "LIST",
		"VALUES" => GetMessage("T_CART_VALUES"),
		"DEFAULT" => "compact",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
	"DISCOUNT_SAVING" => array(
		"NAME" => GetMessage("T_DISCOUNT_SAVING"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
	"YANDEX_API_KEY" => array(
		"NAME" => GetMessage("T_YANDEX_API_KEY"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
);




$arTemplateParameters["FAST"] = array(
	"NAME" => GetMessage("T_FAST"),
	"TYPE" => "LIST",
	"VALUES" => GetMessage("T_FAST_VALUES"),
	"DEFAULT" => "full",
	"REFRESH" => "Y",
	"PARENT" => "VISUAL"
);

if (!empty($arCurrentValues['FAST']) && $arCurrentValues['FAST'] != 'none') {
	\Bitrix\Main\Loader::includeModule('sale');
	$status = array('' => GetMessage("T_FAST_STATUS_NONE"));
	$ar = \Bitrix\Sale\Internals\StatusTable::getList(array(
		'select' => array('ID', 'NAME' => 'Bitrix\Sale\Internals\StatusLangTable:STATUS.NAME', 'TYPE'),
		'filter' => array('=Bitrix\Sale\Internals\StatusLangTable:STATUS.LID' => LANGUAGE_ID, '=TYPE' => 'O'),
		'order'  => array('SORT'),
	));
	while ($v = $ar->fetch()) $status[$v['ID']] = '['.$v['ID'].'] '.$v['NAME'];
	$arTemplateParameters["FAST_STATUS"] = array(
		"NAME" => GetMessage("T_FAST_STATUS"),
		"TYPE" => "LIST",
		"VALUES" => $status,
		"DEFAULT" => "full",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
	$arTemplateParameters["FAST_INFO"] = array(
		"NAME" => GetMessage("T_FAST_INFO"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
}


$arTemplateParameters["POLICY"] = array(
	"NAME" => GetMessage("T_POLICY"),
	"TYPE" => "LIST",
	"VALUES" => GetMessage("T_POLICY_VALUES"),
	"DEFAULT" => "bitrix",
	"REFRESH" => "Y",
	"PARENT" => "VISUAL"
);
if (!empty($arCurrentValues['POLICY']) && $arCurrentValues['POLICY'] == 'text') {
	$arTemplateParameters["POLICY_TEXT"] = array(
		"NAME" => GetMessage("T_POLICY_TEXT"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("T_POLICY_TEXT_DEFAULT"),
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
}
if (!empty($arCurrentValues['POLICY']) && $arCurrentValues['POLICY'] == 'checkbox') {
	$arTemplateParameters["POLICY_CHECKBOX_CHECKED"] = array(
		"NAME" => GetMessage("T_POLICY_CHECKBOX_CHECKED"), // Галочка по умолчанию поставлена
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
	$arTemplateParameters["POLICY_CHECKBOX_LABEL"] = array(
		"NAME" => GetMessage("T_POLICY_CHECKBOX_LABEL"), // Текст у галочки
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("T_POLICY_CHECKBOX_DEFAULT"),
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
}


if (CModule::IncludeModule('edost.locations')) {
	$arTemplateParameters["LOCATION_AREA"] = array(
		"NAME" => GetMessage("T_LOCATION_AREA"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
}


$arTemplateParameters["MENU"] = array(
	"NAME" => GetMessage("T_MENU"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
	"REFRESH" => "Y",
	"PARENT" => "VISUAL"
);
if (!empty($arCurrentValues['MENU']) && $arCurrentValues['MENU'] == 'Y') {
	$arTemplateParameters["MENU_QUERY"] = array(
		"NAME" => GetMessage("T_MENU_QUERY"),
		"TYPE" => "STRING",
		"DEFAULT" => '',
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
	$arTemplateParameters["MENU_HEIGHT"] = array(
		"NAME" => GetMessage("T_MENU_HEIGHT"),
		"TYPE" => "STRING",
		"DEFAULT" => '',
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
	$arTemplateParameters["MENU_WIDTH"] = array(
		"NAME" => GetMessage("T_MENU_WIDTH"),
		"TYPE" => "STRING",
		"DEFAULT" => '',
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
}

/*
$arTemplateParameters["MODULE_VBCHEREPANOV_BONUS"] = array(
	"NAME" => GetMessage("T_MODULE_VBCHEREPANOV_BONUS"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "N",
	"REFRESH" => "N",
	"PARENT" => "VISUAL"
);
*/

$arTemplateParameters["MESS_PAY_SYSTEM_PAYABLE_ERROR"] = array(
	"NAME" => GetMessage("T_MESS_PAY_SYSTEM_PAYABLE_ERROR"),
	"TYPE" => "STRING",
	"DEFAULT" => GetMessage("T_MESS_PAY_SYSTEM_PAYABLE_ERROR_DEFAULT"),
	"REFRESH" => "N",
	"PARENT" => "VISUAL"
);


$arTemplateParameters["TEL_MASK"] = array(
	"NAME" => GetMessage("T_TEL_MASK"),
	"TYPE" => "CHECKBOX",
	"DEFAULT" => "Y",
	"REFRESH" => "Y",
	"PARENT" => "VISUAL"
);
if (empty($arCurrentValues['TEL_MASK']) || $arCurrentValues['TEL_MASK'] == 'Y') {
	$arTemplateParameters["TEL_FORMAT"] = array(
		"NAME" => GetMessage("T_TEL_FORMAT"),
		"TYPE" => "LIST",
		"VALUES" => GetMessage("T_TEL_FORMAT_VALUES"),
		"DEFAULT" => "0",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
	$arTemplateParameters["TEL_COUNTRY"] = array(
		"NAME" => GetMessage("T_TEL_COUNTRY"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => GetMessage("T_TEL_COUNTRY_VALUES"),
		"DEFAULT" => "0",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
	$arTemplateParameters["TEL_CLEAR"] = array(
		"NAME" => GetMessage("T_TEL_CLEAR"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
/*
	$arTemplateParameters["TEL_RU8"] = array(
		"NAME" => GetMessage("T_TEL_RU8"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	);
*/
}


$arTemplateParameters += array(
	"ALLOW_USER_PROFILES" => array(
		"NAME" => GetMessage("T_ALLOW_USER_PROFILES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y",
		"PARENT" => "BASE"
	),
	"ALLOW_NEW_PROFILE" => Array(
		"NAME"=>GetMessage("T_ALLOW_NEW_PROFILE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT"=>"Y",
		"HIDDEN" => $arCurrentValues['ALLOW_USER_PROFILES'] !== 'Y' ? 'Y' : 'N',
		"PARENT" => "BASE",
	),
	"SHOW_PAYMENT_SERVICES_NAMES" => Array(
		"NAME" => GetMessage("T_PAYMENT_SERVICES_NAMES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"Y",
		"PARENT" => "BASE",
	),
	"SHOW_STORES_IMAGES" => Array(
		"NAME" => GetMessage("T_SHOW_STORES_IMAGES"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"N",
		"PARENT" => "BASE",
	),
);


$arTemplateParameters['USE_ENHANCED_ECOMMERCE'] = array(
	"PARENT" => "ANALYTICS_SETTINGS",
	"NAME" => GetMessage("T_USE_ENHANCED_ECOMMERCE"),
	"TYPE" => "CHECKBOX",
	"REFRESH" => "Y",
	"DEFAULT" => "N"
);

if (!empty($arCurrentValues['USE_ENHANCED_ECOMMERCE']) && $arCurrentValues['USE_ENHANCED_ECOMMERCE'] == 'Y') {
	if (\Bitrix\Main\Loader::includeModule('catalog')) {
		$arIblockIDs = array();
		$arIblockNames = array();
		$catalogIterator = \Bitrix\Catalog\CatalogIblockTable::getList(array('select' => array('IBLOCK_ID', 'NAME' => 'IBLOCK.NAME'), 'order' => array('IBLOCK_ID' => 'ASC')));
		while ($catalog = $catalogIterator->fetch()) {
			$catalog['IBLOCK_ID'] = (int)$catalog['IBLOCK_ID'];
			$arIblockIDs[] = $catalog['IBLOCK_ID'];
			$arIblockNames[$catalog['IBLOCK_ID']] = $catalog['NAME'];
		}
		unset($catalog, $catalogIterator);

		if (!empty($arIblockIDs)) {
			$arProps = array();
			$propertyIterator = \Bitrix\Iblock\PropertyTable::getList(array(
				'select' => array('ID', 'CODE', 'NAME', 'IBLOCK_ID'),
				'filter' => array('@IBLOCK_ID' => $arIblockIDs, '=ACTIVE' => 'Y', '!=XML_ID' => CIBlockPropertyTools::XML_SKU_LINK),
				'order' => array('IBLOCK_ID' => 'ASC', 'SORT' => 'ASC', 'ID' => 'ASC')
			));
			while ($property = $propertyIterator->fetch()) {
				$property['ID'] = (int)$property['ID'];
				$property['IBLOCK_ID'] = (int)$property['IBLOCK_ID'];
				$property['CODE'] = (string)$property['CODE'];

				if ($property['CODE'] == '') $property['CODE'] = $property['ID'];

				if (!isset($arProps[$property['CODE']])) {
					$arProps[$property['CODE']] = array(
						'CODE' => $property['CODE'],
						'TITLE' => $property['NAME'].' ['.$property['CODE'].']',
						'ID' => array($property['ID']),
						'IBLOCK_ID' => array($property['IBLOCK_ID'] => $property['IBLOCK_ID']),
						'IBLOCK_TITLE' => array($property['IBLOCK_ID'] => $arIblockNames[$property['IBLOCK_ID']]),
						'COUNT' => 1
					);
				}
				else {
					$arProps[$property['CODE']]['ID'][] = $property['ID'];
					$arProps[$property['CODE']]['IBLOCK_ID'][$property['IBLOCK_ID']] = $property['IBLOCK_ID'];
					if ($arProps[$property['CODE']]['COUNT'] < 2) $arProps[$property['CODE']]['IBLOCK_TITLE'][$property['IBLOCK_ID']] = $arIblockNames[$property['IBLOCK_ID']];
					$arProps[$property['CODE']]['COUNT']++;
				}
			}
			unset($property, $propertyIterator, $arIblockNames, $arIblockIDs);

			$propList = array();
			foreach ($arProps as $property) {
				$iblockList = '';
				if ($property['COUNT'] > 1) $iblockList = ($property['COUNT'] > 2 ? ' ( ... )' : ' ('.implode(', ', $property['IBLOCK_TITLE']).')');
				$propList['PROPERTY_'.$property['CODE']] = $property['TITLE'].$iblockList;
			}
			unset($property, $arProps);
		}
	}

	$arTemplateParameters['DATA_LAYER_NAME'] = array(
		"PARENT" => "ANALYTICS_SETTINGS",
		"NAME" => GetMessage("T_DATA_LAYER_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "dataLayer"
	);

	if (!empty($propList)) $arTemplateParameters['BRAND_PROPERTY'] = array(
		"PARENT" => "ANALYTICS_SETTINGS",
		"NAME" => GetMessage("T_BRAND_PROPERTY"),
		"TYPE" => "LIST",
		"MULTIPLE" => "N",
		"DEFAULT" => "",
		"VALUES" => array('' => '') + $propList
	);
}

?>
