<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
\Bitrix\Main\Loader::includeModule('iblock');
$arProperty_S = $arProperty_XL = array();
if (0 < intval($arCurrentValues['IBLOCK_ID']))
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y"));
	while ($arr=$rsProp->Fetch())
	{
		if($arr["PROPERTY_TYPE"]=="S")
			$arProperty_S[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
		elseif($arr["MULTIPLE"] == "Y" && $arr["PROPERTY_TYPE"] == "L")
			$arProperty_XL[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arTemplateParameters = array(
	"FILTER_NAME" => Array(
		"NAME" => GetMessage("FILTER_NAME"),
		"TYPE" => "STRING",
		"DEFAULT" => "arrTopFilter",
	),
	"SHOW_MEASURE" => Array(
		"NAME" => GetMessage("SHOW_MEASURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"SHOW_DISCOUNT_PERCENT" => Array(
		"NAME" => GetMessage("SHOW_DISCOUNT_PERCENT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"SHOW_OLD_PRICE" => Array(
		"NAME" => GetMessage("SHOW_OLD_PRICE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"STIKERS_PROP" => array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("STIKERS_PROP_TITLE"),
		"TYPE" => "LIST",
		"DEFAULT" => "-",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => array_merge(Array("-"=>" "), $arProperty_XL),
	),
	"SALE_STIKER" =>array(
		"PARENT" => "ADDITIONAL_SETTINGS",
		"NAME" => GetMessage("SALE_STIKER"),
		"TYPE" => "LIST",
		"DEFAULT" => "-",
		"ADDITIONAL_VALUES" => "Y",
		"VALUES" => array_merge(Array("-"=>" "), $arProperty_S),
	),
	"SHOW_MEASURE_WITH_RATIO" => Array(
		"NAME" => GetMessage("SHOW_MEASURE_WITH_RATIO"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
);
?>
