<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$PARTNER_ID = "bxmaker";
$BXMAKER_COMPONENT_NAME = 'BXMAKER.GEOIP.CITY';

$arComponentDescription = array(
    "NAME" => GetMessage($BXMAKER_COMPONENT_NAME."_COMPONENT_NAME"),
    "DESCRIPTION" => GetMessage($BXMAKER_COMPONENT_NAME."_COMPONENT_DESCRIPTION"),
    "ICON" => "",
    "PATH" => array(
        "ID" => $PARTNER_ID,
        "NAME" => GetMessage($BXMAKER_COMPONENT_NAME.'_DEVELOP_GROUP'),
        "CHILD" => array(
            "ID" => "location",
            "NAME" => GetMessage($BXMAKER_COMPONENT_NAME.'_LOCATION_COMPONENT_GROUP')
        )
    ),
);
unset($PARTNER_ID,$BXMAKER_COMPONENT_NAME);
