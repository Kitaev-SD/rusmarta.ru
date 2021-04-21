<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array();

$arTemplateParameters["COMPACT"] = array(
	"NAME" => GetMessage("T_COMPACT"),
	"TYPE" => "LIST",
	'VALUES' => GetMessage("T_COMPACT_VALUES"),
	"DEFAULT" => "S",
	"REFRESH" => "Y",
	"PARENT" => "VISUAL"
);

if (!empty($arCurrentValues['COMPACT']) && $arCurrentValues['COMPACT'] != 'off') {
	$arTemplateParameters["PRIORITY"] = array(
		"NAME" => GetMessage("T_PRIORITY"),
		"TYPE" => "LIST",
		'VALUES' => GetMessage("T_PRIORITY_VALUES"),
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

	if (!empty($arCurrentValues['PRIORITY']) && $arCurrentValues['PRIORITY'] == 'B') {
		$arTemplateParameters["COD_LIGHT"] = array(
			"NAME" => GetMessage("T_COD_LIGHT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "N",
			"PARENT" => "VISUAL"
		);
	}
}

$arTemplateParameters += array(
	"ORDER_FORMAT" => array(
		"NAME" => GetMessage("T_ORDER_FORMAT"),
		"TYPE" => "LIST",
		'VALUES' => GetMessage("T_ORDER_FORMAT_VALUES"),
		"DEFAULT" => "progress_compact",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
	"STYLE" => array(
		"NAME" => GetMessage("T_STYLE"),
		"TYPE" => "LIST",
		'VALUES' => GetMessage("T_STYLE_VALUES"),
		"DEFAULT" => "bright",
		"REFRESH" => "N",
		"PARENT" => "VISUAL"
	),
	"COLOR" => array(
		"NAME" => GetMessage("T_COLOR"),
		"TYPE" => "LIST",
		'VALUES' => GetMessage("T_COLOR_VALUES"),
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
		'VALUES' => GetMessage("T_CART_VALUES"),
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
	'VALUES' => GetMessage("T_FAST_VALUES"),
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
		'VALUES' => $status,
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
	'VALUES' => GetMessage("T_POLICY_VALUES"),
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
	"DEFAULT" => "bitrix",
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



?>
