<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = array(
	'NAME' => GetMessage('T_CD_NAME'),
	'DESCRIPTION' => GetMessage('T_CD_DESCRIPTION'),
	'ICON' => '/images/catalog_delivery.gif',
	'CACHE_PATH' => 'Y',
	'SORT' => 10,
	'PATH' => array(
		'ID' => 'aspro',
		'NAME' => GetMessage('T_CD_ASPRO'),
		'SORT' => 2,
	),
);
?>