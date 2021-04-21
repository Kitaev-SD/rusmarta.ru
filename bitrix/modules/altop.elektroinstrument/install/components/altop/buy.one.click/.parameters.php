<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(	
	"PARAMETERS" => array(		
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_ELEMENT_ID"),
			"TYPE" => "STRING",
			"HIDDEN" => "Y"
		),
		"ELEMENT_AREA_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_ELEMENT_AREA_ID"),
			"TYPE" => "STRING",
			"HIDDEN" => "Y"
		),
		"USE_FILE_FIELD" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_USE_FILE_FIELD"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y"
		)
	)
);

if($arCurrentValues["USE_FILE_FIELD"] == "Y") {
	$arComponentParameters["PARAMETERS"]["FILE_FIELD_MULTIPLE"] = array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("1CB_PARAMETER_FILE_FIELD_MULTIPLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "Y"
	);
	if($arCurrentValues["FILE_FIELD_MULTIPLE"] == "Y") {
		$arComponentParameters["PARAMETERS"]["FILE_FIELD_MAX_COUNT"] = array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("1CB_PARAMETER_FILE_FIELD_MAX_COUNT"),
			"TYPE" => "TEXT",
			"DEFAULT" => "5"
		);
	}
	$arComponentParameters["PARAMETERS"]["FILE_FIELD_NAME"] = array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("1CB_PARAMETER_FILE_FIELD_NAME"),
		"TYPE" => "TEXT",
		"DEFAULT" => GetMessage("1CB_PARAMETER_FILE_FIELD_NAME_DEFAULT")
	);	
	$arFileFieldTypes = array(
		"" => GetMessage("1CB_PARAMETER_FILE_FIELD_TYPE_ALL"),
		"jpg, gif, bmp, png, jpeg" => GetMessage("1CB_PARAMETER_FILE_FIELD_TYPE_IMAGES"),
		"mp3, wav, midi, snd, au, wma" => GetMessage("1CB_PARAMETER_FILE_FIELD_TYPE_SOUNDS"),
		"mpg, avi, wmv, mpeg, mpe, flv" => GetMessage("1CB_PARAMETER_FILE_FIELD_TYPE_VIDEO"),
		"doc, docx, txt, rtf" => GetMessage("1CB_PARAMETER_FILE_FIELD_TYPE_DOCS")
	);
	$arComponentParameters["PARAMETERS"]["FILE_FIELD_TYPE"] = array(
		"PARENT" => "BASE",
		"NAME" => GetMessage("1CB_PARAMETER_FILE_FIELD_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => $arFileFieldTypes,
		"DEFAULT" => "",
		"ADDITIONAL_VALUES" => "N",
		"REFRESH" => "N",
		"MULTIPLE" => "N"
	);	
}

//REQUIRED//
$arRequiredFields = array(
	"NAME" => GetMessage("1CB_PARAMETER_REQUIRED_NAME"),
    "PHONE" => GetMessage("1CB_PARAMETER_REQUIRED_PHONE"),
    "EMAIL" => GetMessage("1CB_PARAMETER_REQUIRED_EMAIL"),
	"MESSAGE" => GetMessage("1CB_PARAMETER_REQUIRED_MESSAGE"),
);
if($arCurrentValues["USE_FILE_FIELD"] == "Y") {
	$arRequiredFields["FILE"] = GetMessage("1CB_PARAMETER_REQUIRED_FILE");
}
$arComponentParameters["PARAMETERS"]["REQUIRED"] = array(
	"PARENT" => "BASE",
	"NAME" => GetMessage("1CB_PARAMETER_REQUIRED"),
	"TYPE" => "LIST",
	"VALUES" => $arRequiredFields,
	"DEFAULT" => array("NAME", "PHONE"),
	"ADDITIONAL_VALUES" => "N",
	"REFRESH" => "N",
	"MULTIPLE" => "Y"
);

//BUY_MODES//
$arBuyModes = array(
	"ONE" => GetMessage("1CB_PARAMETER_BUY_MODE_ONE"),
	"ALL" => GetMessage("1CB_PARAMETER_BUY_MODE_ALL"),
);
$arComponentParameters["PARAMETERS"]["BUY_MODE"] = array(
	"PARENT" => "BASE",
	"NAME" => GetMessage("1CB_PARAMETER_BUY_MODE"),
	"TYPE" => "LIST",
	"VALUES" => $arBuyModes,
	"ADDITIONAL_VALUES" => "N",
	"DEFAULT" => "ONE",
	"REFRESH" => "N",
	"MULTIPLE" => "N"
);

$arComponentParameters["PARAMETERS"]["CACHE_TIME"] = array(
	"DEFAULT" => 36000000
);?>