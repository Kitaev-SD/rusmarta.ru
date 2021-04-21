<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters = array(
	'DISPLAY_DATE' => Array(
		'NAME' => GetMessage('T_IBLOCK_DESC_NEWS_DATE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'DISPLAY_PICTURE' => Array(
		'NAME' => GetMessage('T_IBLOCK_DESC_NEWS_PICTURE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'DISPLAY_PREVIEW_TEXT' => Array(
		'NAME' => GetMessage('T_IBLOCK_DESC_NEWS_TEXT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'USE_SHARE' => array(
		'NAME' => GetMessage('USE_SHARE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
	'S_ASK_QUESTION' => array(
		'SORT' => 700,
		'NAME' => GetMessage('S_ASK_QUESTION'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'S_ORDER_SERVISE' => array(
		'SORT' => 701,
		'NAME' => GetMessage('S_ORDER_SERVISE'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_GALLERY' => array(
		'SORT' => 702,
		'NAME' => GetMessage('T_GALLERY'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_DOCS' => array(
		'SORT' => 703,
		'NAME' => GetMessage('T_DOCS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_GOODS' => array(
		'SORT' => 704,
		'NAME' => GetMessage('T_GOODS'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_STUDY' => array(
		'SORT' => 705,
		'NAME' => GetMessage('T_STUDY'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_SERVICES' => array(
		'SORT' => 706,
		'NAME' => GetMessage('T_SERVICES'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	),
	'T_VIDEO' => array(
		'SORT' => 707,
		'NAME' => GetMessage('T_VIDEO'),
		'TYPE' => 'TEXT',
		'DEFAULT' => '',
	)
);
?>