<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$PARTNER_ID = "bxmaker";
$PARTNER_COMPONENT_ID = 'BXMAKER.GEOIP.MESSAGE';

$arComponentDescription = array(
    "NAME" => GetMessage($PARTNER_COMPONENT_ID."_COMPONENT_NAME"),
    "DESCRIPTION" => GetMessage($PARTNER_COMPONENT_ID."_COMPONENT_DESCRIPTION"),
    "ICON" => "",
    "PATH" => array(
        "ID" => $PARTNER_ID,
        "NAME" => GetMessage($PARTNER_COMPONENT_ID.'_DEVELOP_GROUP'),
        "CHILD" => array(
            "ID" => "location",
            "NAME" => GetMessage($PARTNER_COMPONENT_ID.'_LOCATION_COMPONENT_GROUP')
        )
    ),
);
unset($PARTNER_ID,$PARTNER_COMPONENT_ID);
