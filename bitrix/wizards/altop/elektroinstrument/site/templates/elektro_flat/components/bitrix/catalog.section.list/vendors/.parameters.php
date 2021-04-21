<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arTemplateParameters = array(
	"VENDOR_ID" => array(
		"NAME" => GetMessage("VENDOR_ID"),
		"TYPE" => "TEXT",
		"DEFAULT" => ""
	),
	"VENDOR_NAME" => array(
		"NAME" => GetMessage("VENDOR_NAME"),
		"TYPE" => "TEXT",
		"DEFAULT" => ""
	),
	"SEF_MODE" = array(
		"NAME" => GetMessage("SEF_MODE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y"
	),
	"HIDE_SECTION" => array(		
		"NAME" => GetMessage("HIDE_SECTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N"
	)
);?>