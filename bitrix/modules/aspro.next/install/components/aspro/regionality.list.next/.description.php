<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
 
$arComponentDescription = array(
	"NAME" => GetMessage("T_ASPRO_REGIONALITY_LIST_NAME"),
	"DESCRIPTION" => GetMessage("T_ASPRO_REGIONALITY_LIST_DESCRIPTION"),
	"ICON" => "/images/news-list.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "aspro",
		"NAME" => GetMessage("ASPRO")
	),
	"COMPLEX" => "N"
);
?>