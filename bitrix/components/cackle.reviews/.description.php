<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => 'Cackle reviews',
	"DESCRIPTION" => GetMessage('CACKLE_REVIEWS_REVIEWS_COMPONENT_DESC'),
	"ICON" => "/images/icon.jpg",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "Cackle", // for example "my_project"
        "NAME" => "Cackle",
		"CHILD" => array(
			"ID" => "cackle:reviews", // for example "my_project:services"
			"NAME" => GetMessage('CACKLE_REVIEWS_REVIEWS_MODULE_NAME'),  // for example "Services"
            "CHILD" => array(
                "ID" => "cackle.reviews",

            ),
		),
	),
	"COMPLEX" => "N",
);

?>