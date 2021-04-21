<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var CBitrixComponentTemplate $this */
/** @var array $arResult */

\Bitrix\Main\Mail\EventMessageThemeCompiler::includeComponent(
	"bitrix:catalog.show.products.mail",
	"aspro_goods",
	array(
		"LIST_ITEM_ID" => $arResult['ITEMS'],
		"SITE_ID" => $arParams['SITE_ID'],
		"SITE_ADDRESS" => $arParams['SITE_ADDRESS'],
		"THEME_COLOR" => $arParams['THEME_COLOR'],
		"TITLE" => $arParams['TITLE'],
		"CATALOG_PAGE" => $arParams['CATALOG_PAGE'],
		"SHOW_CATALOG" => $arParams['SHOW_CATALOG'],
	)
);