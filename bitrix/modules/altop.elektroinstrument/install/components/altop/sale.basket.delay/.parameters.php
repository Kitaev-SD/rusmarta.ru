<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = Array(
	"PARAMETERS" => Array(
		"PATH_TO_DELAY" => Array(
			"NAME" => GetMessage("ALTOP_PATH_TO_DELAY"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "/personal/cart/",
			"COLS" => 25,
			"PARENT" => "ADDITIONAL_SETTINGS",
		),
	)
);
?>