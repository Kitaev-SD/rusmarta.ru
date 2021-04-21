<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters['TITLE'] = array(
	'NAME' => GetMessage('SALE_BASKET_TITLE'),
	'TYPE' => 'STRING',
	'REFRESH' => 'N',
	'DEFAULT' => GetMessage('SALE_BASKET_TEXT'),
);
$arTemplateParameters['SITE_ID'] = array(
	'NAME' => GetMessage('SITE_ID_TITLE'),
	'TYPE' => 'STRING',
	'REFRESH' => 'N',
	'DEFAULT' => "s1",
);
$arTemplateParameters['SITE_ADDRESS'] = array(
	'NAME' => GetMessage('SITE_ADDRESS_TITLE'),
	'TYPE' => 'STRING',
	'REFRESH' => 'N',
	'DEFAULT' => "",
);
$arTemplateParameters['THEME_COLOR'] = array(
	'NAME' => GetMessage('THEME_COLOR_TITLE'),
	'TYPE' => 'STRING',
	'REFRESH' => 'N',
	'DEFAULT' => "",
);
$arTemplateParameters['CATALOG_PAGE'] = array(
	'NAME' => GetMessage('CATALOG_PAGE_TITLE'),
	'TYPE' => 'STRING',
	'REFRESH' => 'N',
	'DEFAULT' => "/catalog/",
);
$arTemplateParameters['SHOW_CATALOG'] = array(
	'NAME' => GetMessage('SHOW_CATALOG_TITLE'),
	'TYPE' => 'CHECKBOX',
	'REFRESH' => 'N',
	'DEFAULT' => "Y",
);