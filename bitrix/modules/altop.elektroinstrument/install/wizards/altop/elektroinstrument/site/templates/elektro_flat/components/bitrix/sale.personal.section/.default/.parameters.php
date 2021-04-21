<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $APPLICATION;

$arTemplateParameters = array(
	"SHOW_EMAIL_SUBSCRIBE_PAGE" => array(
		"NAME" => GetMessage("SPS_SHOW_EMAIL_SUBSCRIBE_PAGE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"PARENT" => "BASE",
		"REFRESH" => "Y"
	),
	"SHOW_EXIT_PAGE" => array(
		"NAME" => GetMessage("SPS_SHOW_EXIT_PAGE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
		"PARENT" => "BASE",
		"REFRESH" => "Y"
	),
	"PATH_TO_EMAIL_SUBSCRIBE" => array(
		"NAME" => GetMessage("SPS_PATH_TO_EMAIL_SUBSCRIBE"),
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"DEFAULT" => "/personal/mailings/",
		"COLS" => 25,
		"PARENT" => "URL_TEMPLATES",
	)
);?>