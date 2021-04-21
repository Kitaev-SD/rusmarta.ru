<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

if(!CModule::IncludeModule("aspro.next"))
	return;

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"TOKEN" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TOKEN"),
			"TYPE" => "STRING",
			"DEFAULT" => "1056017790.9b6cbfe.81d864a1f0d94689821f63b1867624c7",
		),
		"TITLE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("TITLE_VALUE"),
		),
	),
);
