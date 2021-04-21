<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arService = array(
	'YANDEX' => GetMessage("MODE_OPERATION_YANDEX"),
	'BITRIX' => GetMessage("MODE_OPERATION_BITRIX")
);

$arComponentParameters = array(
	"GROUPS" => array(
		"MODE_OPERATION_SETTINGS" => array(
			"NAME" => GetMessage("GEOLOCATION_MODE_OPERATION_SETTINGS"),
		),
		"CONFIRM_CITY_SETTINGS" => array(
			"NAME" => GetMessage("GEOLOCATION_CONFIRM_CITY_SETTINGS"),
		),
		"CHANGE_CITY_SETTINGS" => array(
			"NAME" => GetMessage("GEOLOCATION_CHANGE_CITY_SETTINGS"),
		),
		"COOKIE_SETTINGS" => array(
			"NAME" => GetMessage("GEOLOCATION_COOKIE_SETTINGS"),
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("GEOLOCATION_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,			
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("GEOLOCATION_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,			
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"MODE_OPERATION" => array(
			"PARENT" => "MODE_OPERATION_SETTINGS",
			"NAME" => GetMessage("MODE_OPERATION"),
			"TYPE" => "LIST",
			"VALUES" => $arService,			
			"DEFAULT" => "YANDEX",
		),
		"SHOW_CONFIRM" => array(
			"PARENT" => "CONFIRM_CITY_SETTINGS",
			"NAME" => GetMessage("GEOLOCATION_SHOW_CONFIRM"),
			"TYPE" => "CHECKBOX",			
			"DEFAULT" => "Y",
		),
		"SHOW_DEFAULT_LOCATIONS" => array(
			"PARENT" => "CHANGE_CITY_SETTINGS",
			"NAME" => GetMessage("GEOLOCATION_SHOW_DEFAULT_LOCATIONS"),
			"TYPE" => "CHECKBOX",			
			"DEFAULT" => "Y",
		),
		"SHOW_TEXT_BLOCK" => array(
			"PARENT" => "CHANGE_CITY_SETTINGS",
			"NAME" => GetMessage("GEOLOCATION_SHOW_TEXT_BLOCK"),
			"TYPE" => "CHECKBOX",			
			"DEFAULT" => "Y",
			"REFRESH" => "Y",
		)
	)
);

if($arCurrentValues["SHOW_TEXT_BLOCK"] == "Y") {
	$arComponentParameters["PARAMETERS"]["SHOW_TEXT_BLOCK_TITLE"] = array(
		"PARENT" => "CHANGE_CITY_SETTINGS",
		"NAME" => GetMessage("GEOLOCATION_SHOW_TEXT_BLOCK_TITLE"),
		"TYPE" => "CHECKBOX",			
		"DEFAULT" => "Y",
		"REFRESH" => "Y",
	);
	if($arCurrentValues["SHOW_TEXT_BLOCK_TITLE"] != "N") {
		$arComponentParameters["PARAMETERS"]["TEXT_BLOCK_TITLE"] = array(
			"PARENT" => "CHANGE_CITY_SETTINGS",
			"NAME" => GetMessage("GEOLOCATION_TEXT_BLOCK_TITLE"),
			"TYPE" => "STRING",		
			"DEFAULT" => GetMessage("GEOLOCATION_TEXT_BLOCK_TITLE_DEFAULT")
		);
	}
}

$arComponentParameters["PARAMETERS"]["COOKIE_TIME"] = array(
	"PARENT" => "COOKIE_SETTINGS",
	"NAME" => GetMessage("GEOLOCATION_COOKIE_TIME"),
	"TYPE" => "STRING",
	"DEFAULT" => 36000000
);

$arComponentParameters["PARAMETERS"]["CACHE_TIME"] = array(
	"DEFAULT" => 36000000
);?>