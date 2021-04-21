<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;

if(!Loader::includeModule("iblock"))
	return;

$arSortOffers = CIBlockParameters::GetElementSortFields(
	array('SHOWS', 'SORT', 'TIMESTAMP_X', 'NAME', 'ID', 'ACTIVE_FROM', 'ACTIVE_TO', 'catalog_PRICE_1'),
	array('KEY_LOWERCASE' => 'Y')
);

$arAscDescOffers = array(
	"asc" => GetMessage("IBLOCK_SORT_ASC"),
	"desc" => GetMessage("IBLOCK_SORT_DESC"),
);

$arSortOffers['PRICE'] = GetMessage("IBLOCK_SORT_OFFERS_PRICE");
$arSortOffers['PROPERTIES'] = GetMessage("IBLOCK_SORT_OFFERS_PROPERTIES");

$arTemplateParameters = array(
	"DISPLAY_IMG_WIDTH" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_IMG_WIDTH"),
		"TYPE" => "TEXT",
		"DEFAULT" => "150",
	),
	"DISPLAY_IMG_HEIGHT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_IMG_HEIGHT"),
		"TYPE" => "TEXT",
		"DEFAULT" => "150",
	),
	"SHARPEN" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_SHARPEN"),
		"TYPE" => "TEXT",
		"DEFAULT" => "30",
	),
	"DISPLAY_COMPARE" => Array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("T_IBLOCK_DESC_DISPLAY_COMPARE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SHOW_POPUP" => Array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("T_IBLOCK_DESC_SHOW_POPUP"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	'HIDE_NOT_AVAILABLE' => array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => GetMessage('T_HIDE_NOT_AVAILABLE_EXT2'),
		'TYPE' => 'LIST',
		'DEFAULT' => 'N',
		'VALUES' => array(
			'Y' => GetMessage('T_HIDE_NOT_AVAILABLE_HIDE'),
			'N' => GetMessage('T_HIDE_NOT_AVAILABLE_SHOW')
		),
		'ADDITIONAL_VALUES' => 'N'
	),
	'HIDE_NOT_AVAILABLE_OFFERS' => array(
		'PARENT' => 'DATA_SOURCE',
		'NAME' => GetMessage('T_HIDE_NOT_AVAILABLE_OFFERS'),
		'TYPE' => 'LIST',
		'DEFAULT' => 'N',
		'VALUES' => array(
			'Y' => GetMessage('T_HIDE_NOT_AVAILABLE_OFFERS_HIDE'),
			'L' => GetMessage('T_HIDE_NOT_AVAILABLE_OFFERS_SUBSCRIBE'),
			'N' => GetMessage('T_HIDE_NOT_AVAILABLE_OFFERS_SHOW')
		)
	),
	"OFFERS_SORT_FIELD" => array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("CP_BC_OFFERS_SORT_FIELD"),
		"TYPE" => "LIST",
		"VALUES" => $arSortOffers,
		"ADDITIONAL_VALUES" => "Y",
		"DEFAULT" => "sort",
	),
	"OFFERS_SORT_ORDER" => array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("CP_BC_OFFERS_SORT_ORDER"),
		"TYPE" => "LIST",
		"VALUES" => $arAscDescOffers,
		"DEFAULT" => "asc",
		"ADDITIONAL_VALUES" => "Y",
	)
);?>