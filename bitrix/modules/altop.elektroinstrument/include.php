<?
$moduleClass = "CElektroinstrument";
$moduleID = "altop.elektroinstrument";

CModule::AddAutoloadClasses(
	$moduleID,
	array(
		"elektroinstrument" => "install/index.php",
		$moduleClass => "classes/general/".$moduleClass.".php"		
	)
);

//EVENTS//
AddEventHandler("iblock", "OnAfterIBlockUpdate", array($moduleClass, "DeleteEventTypeEventMessage"));
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", array($moduleClass, "DoIBlockAfterSave"));
AddEventHandler("iblock", "OnAfterIBlockElementAdd", array($moduleClass, "DoIBlockAfterSave"));
AddEventHandler("catalog", "OnPriceAdd", array($moduleClass, "DoIBlockAfterSave"));
AddEventHandler("catalog", "OnPriceUpdate", array($moduleClass, "DoIBlockAfterSave"));
AddEventHandler("sale", "OnSaleComponentOrderProperties", array($moduleClass, "SetOrderPropertiesLocation"));?>