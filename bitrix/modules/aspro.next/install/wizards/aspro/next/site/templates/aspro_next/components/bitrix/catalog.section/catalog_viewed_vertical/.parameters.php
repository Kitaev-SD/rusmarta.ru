<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"TITLE_BLOCK" => Array(
		"NAME" => GetMessage("TITLE_BLOCK_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage("TITLE_BLOCK_VALUE"),
	),
	'SHOW_MEASURE' => array(
		'NAME' => GetMessage('T_SHOW_MEASURE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
);
?>
