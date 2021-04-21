<?
$arFilterPath = $dbFilterPath = array();
$arFilterPath = explode('/', $arElement['PROPERTY_FILTER_URL_VALUE']);
if($arFilterPath)
{
	for($i=0;$i<count($arFilterPath);++$i){
		if(strlen($arFilterPath[$i])){
			if($next === true)
				$dbFilterPath[] = urldecode($arFilterPath[$i]);			
			
			if($arFilterPath[$i] == 'filter')
			{
				$dbFilterPath[] = $arFilterPath[$i];
				$next = true;
			}
			
		}
	}
}

if($dbFilterPath)
	$filterPath = implode('/', $dbFilterPath);

?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.smart.filter",
	"landing",
	Array(
		"IBLOCK_TYPE" => "aspro_next_catalog",
		"IBLOCK_ID" => $arTheme["CATALOG_IBLOCK_ID"]["VALUE"],
		"SECTION_ID" => '',
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"PRICE_CODE" => $arParams["FILTER_PRICE_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_NOTES" => "",
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SAVE_IN_SESSION" => "N",
		"XML_EXPORT" => "Y",
		"SECTION_TITLE" => "NAME",
		"SECTION_DESCRIPTION" => "DESCRIPTION",
		"SHOW_HINTS" => $arParams["SHOW_HINTS"],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		"INSTANT_RELOAD" => "Y",
		"VIEW_MODE" => strtolower($arTheme["FILTER_VIEW"]["VALUE"]),
		"SEF_MODE" => "Y",
		"SEF_RULE" => $arResult["FOLDER"].$arResult['VARIABLES']['ELEMENT_CODE'].'/'.$filterPath,
		"SMART_FILTER_PATH" => $filterPath,
		"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
		"SORT_BUTTONS" => $arParams["SORT_BUTTONS"],
		"SORT_PRICES" => $arParams["SORT_PRICES"],
	),
	$component);
?>