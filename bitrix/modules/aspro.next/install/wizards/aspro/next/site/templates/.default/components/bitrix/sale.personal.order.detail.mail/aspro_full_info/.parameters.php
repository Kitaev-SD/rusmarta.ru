<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters['SITE_ID'] = array(
	'NAME' => GetMessage('SITE_ID_TITLE'),
	'TYPE' => 'STRING',
	'REFRESH' => 'N',
	'DEFAULT' => "s1",
);
$arTemplateParameters['THEME_COLOR'] = array(
	'NAME' => GetMessage('THEME_COLOR_TITLE'),
	'TYPE' => 'STRING',
	'REFRESH' => 'N',
	'DEFAULT' => "",
);
$arTemplateParameters['PERSONAL_PAGE'] = array(
	'NAME' => GetMessage('PERSONAL_PAGE_TITLE'),
	'TYPE' => 'STRING',
	'REFRESH' => 'N',
	'DEFAULT' => "/personal/",
);
$arTemplateParameters['SHOW_PERSONAL'] = array(
	'NAME' => GetMessage('SHOW_PERSONAL_TITLE'),
	'TYPE' => 'CHECKBOX',
	'REFRESH' => 'N',
	'DEFAULT' => "Y",
);