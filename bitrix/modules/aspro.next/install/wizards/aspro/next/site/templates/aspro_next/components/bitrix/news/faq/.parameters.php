<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/* get component template pages & params array */
$arPageBlocksParams = array();
if(\Bitrix\Main\Loader::includeModule('aspro.next')){
	$arPageBlocks = CNext::GetComponentTemplatePageBlocks(__DIR__);
	$arPageBlocksParams = CNext::GetComponentTemplatePageBlocksParams($arPageBlocks);
}

$arTemplateParameters = array_merge($arPageBlocksParams, array(
	'VIEW_TYPE' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 100,
		'NAME' => GetMessage('VIEW_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'list' => GetMessage('VIEW_TYPE_LIST'),
			'table' => GetMessage('VIEW_TYPE_TABLE'),
			'accordion' => GetMessage('VIEW_TYPE_ACCORDION'),
		),
		'DEFAULT' => 'list',
		'REFRESH' => 'Y'
	),
	'SHOW_DETAIL_LINK' => array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => GetMessage('SHOW_DETAIL_LINK'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'SHOW_TABS' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 100,
		'NAME' => GetMessage('SHOW_TABS'),
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
	'SHOW_SECTION_NAME' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 500,
		'NAME' => GetMessage('SHOW_SECTION_NAME'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'USE_SHARE' => array(
		'PARENT' => 'DETAIL_SETTINGS',
		'SORT' => 600,
		'NAME' => GetMessage('USE_SHARE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'SHOW_ASK_QUESTION_BLOCK' => array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 100,
		'NAME' => GetMessage('SHOW_ASK_QUESTION_BLOCK'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
));

if($arCurrentValues['SHOW_ASK_QUESTION_BLOCK'] !== 'N'){
	$arTemplateParameters['S_ASK_QUESTION'] = array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 700,
		'NAME' => GetMessage('S_ASK_QUESTION'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	);
}

if($arCurrentValues['VIEW_TYPE'] == 'list'){
	$arTemplateParameters['IMAGE_POSITION'] = array(
		'PARENT' => 'LIST_SETTINGS',
		'SORT' => 250,
		'NAME' => GetMessage('IMAGE_POSITION'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'left' => GetMessage('IMAGE_POSITION_LEFT'),
			'right' => GetMessage('IMAGE_POSITION_RIGHT'),
		),
		'DEFAULT' => 'left',
	);
}

if($arCurrentValues['VIEW_TYPE'] == 'table'){
	$arTemplateParameters['COUNT_IN_LINE'] = array(
		'PARENT' => 'LIST_SETTINGS',
		'NAME' => GetMessage('COUNT_IN_LINE'),
		'TYPE' => 'STRING',
		'DEFAULT' => '3',
	);
}
?>