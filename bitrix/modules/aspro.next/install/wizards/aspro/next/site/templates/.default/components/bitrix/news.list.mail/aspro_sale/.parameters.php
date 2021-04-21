<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters['THEME_COLOR'] = array(
	'NAME' => GetMessage('THEME_COLOR_TITLE'),
	'TYPE' => 'STRING',
	'REFRESH' => 'N',
	'DEFAULT' => "",
);
$arTemplateParameters['SALE_PAGE'] = array(
	'NAME' => GetMessage('SALE_PAGE_TITLE'),
	'TYPE' => 'STRING',
	'REFRESH' => 'N',
	'DEFAULT' => "/catalog/",
);
$arTemplateParameters['SHOW_SALE'] = array(
	'NAME' => GetMessage('SHOW_SALE_TITLE'),
	'TYPE' => 'CHECKBOX',
	'REFRESH' => 'N',
	'DEFAULT' => "Y",
);