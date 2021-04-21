<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(	
	"PARAMETERS" => array(		
		"ELEMENT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("GEOLOCATION_DELIVERY_ELEMENT_ID"),
			"TYPE" => "STRING"
		),
		"ELEMENT_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("GEOLOCATION_DELIVERY_ELEMENT_COUNT"),
			"TYPE" => "STRING"
		),
		"CART_PRODUCTS" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("GEOLOCATION_DELIVERY_CART_PRODUCTS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N"
		),
		"AJAX_CALL" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("GEOLOCATION_DELIVERY_AJAX_CALL"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N"
		),
		"CACHE_TIME"  => array(
			"DEFAULT" => 36000000
		)
	)
);?>