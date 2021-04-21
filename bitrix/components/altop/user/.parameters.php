<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = array(
	"PARAMETERS" => array(		
		"PATH_TO_PERSONAL" => array(
			"NAME" => GetMessage("USER_PATH_TO_PERSONAL"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "/personal/",
			"COLS" => 25,
			"PARENT" => "BASE",
		),
		"CACHE_TIME"  => array(
			"DEFAULT" => "36000000"
		)
	)
);?>