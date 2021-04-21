<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/* get component template pages & params array */
$arPageBlocksParams = array();
if(\Bitrix\Main\Loader::includeModule('aspro.next')){
	$arPageBlocks = CNext::GetComponentTemplatePageBlocks(__DIR__);
	$arPageBlocksParams = CNext::GetComponentTemplatePageBlocksParams($arPageBlocks);
	CNext::AddComponentTemplateModulePageBlocksParams(__DIR__, $arPageBlocksParams); // add option value FROM_MODULE
}

$arTemplateParameters = array_merge($arPageBlocksParams, array(
	'SHOW_DETAIL_LINK' => array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => GetMessage('SHOW_DETAIL_LINK'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'SHOW_SECTION_PREVIEW_DESCRIPTION' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 500,
		'NAME' => GetMessage('SHOW_SECTION_PREVIEW_DESCRIPTION'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'FORM' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 500,
		'NAME' => GetMessage('FORM'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'FORM_ID' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 500,
		'NAME' => GetMessage('FORM_ID'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	'FORM_BUTTON_TITLE' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 500,
		'NAME' => GetMessage('FORM_BUTTON_TITLE'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	),
	'USE_SHARE' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 600,
		'NAME' => GetMessage('USE_SHARE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
));

?>