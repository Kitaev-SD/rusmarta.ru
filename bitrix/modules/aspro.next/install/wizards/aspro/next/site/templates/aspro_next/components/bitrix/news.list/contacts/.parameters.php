<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters = array(
	'VIEW_TYPE' => array(
		'SORT' => 100,
		'NAME' => GetMessage('VIEW_TYPE'),
		'TYPE' => 'LIST',
		'VALUES' => array(
			'list' => GetMessage('VIEW_TYPE_LIST'),
			'table' => GetMessage('VIEW_TYPE_TABLE'),
		),
		'DEFAULT' => 'table',
		'REFRESH' => 'Y'
	),
	'SHOW_DETAIL_LINK' => array(
		'NAME' => GetMessage('SHOW_DETAIL_LINK'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
);

if($arCurrentValues['VIEW_TYPE'] == 'list'){
	$arTemplateParameters['IMAGE_POSITION'] = array(
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
		'NAME' => GetMessage('COUNT_IN_LINE'),
		'TYPE' => 'STRING',
		'DEFAULT' => '3',
	);
}
?>