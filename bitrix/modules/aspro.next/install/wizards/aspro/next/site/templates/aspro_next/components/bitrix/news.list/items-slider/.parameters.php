<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters = array(
	'SHOW_DETAIL_LINK' => array(
		'NAME' => GetMessage('SHOW_DETAIL_LINK'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'TITLE' => array(
		'NAME' => GetMessage('TITLE'),
		'TYPE' => 'STRING',
		'DEFAULT' => GetMessage('TITLE_DEFAULT'),
	),
	'NORMAL_BLOCK' => array(
		'NAME' => GetMessage('NORMAL_BLOCK'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => "Y",
	),
);
?>
