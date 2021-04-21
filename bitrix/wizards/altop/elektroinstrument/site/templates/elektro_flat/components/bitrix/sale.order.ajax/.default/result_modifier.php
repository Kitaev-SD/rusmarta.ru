<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $arSetting;

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);

//CURRENCIES//
$arResult["CURRENCIES"] = array();
$currencyIterator = Bitrix\Currency\CurrencyTable::getList(array(
	"select" => array("CURRENCY")
));
while($currency = $currencyIterator->fetch()) {
	$currencyFormat = CCurrencyLang::GetFormatDescription($currency["CURRENCY"]);
	$arResult["CURRENCIES"][] = array(
		"CURRENCY" => $currency["CURRENCY"],
		"FORMAT" => array(
			"FORMAT_STRING" => $currencyFormat["FORMAT_STRING"],
			"DEC_POINT" => $currencyFormat["DEC_POINT"],
			"THOUSANDS_SEP" => $currencyFormat["THOUSANDS_SEP"],
			"DECIMALS" => $currencyFormat["DECIMALS"],
			"THOUSANDS_VARIANT" => $currencyFormat["THOUSANDS_VARIANT"],
			"HIDE_ZERO" => $currencyFormat["HIDE_ZERO"]
		)
	);
}
unset($currencyFormat, $currency, $currencyIterator);

if(in_array("OFFERS_LINK_SHOW", $arSetting["GENERAL_SETTINGS"]["VALUE"])) {
	foreach($arResult["BASKET_ITEMS"] as $key => $item) {
		if(is_array(CCatalogSku::GetProductInfo($item["PRODUCT_ID"])))
			$arResult["BASKET_ITEMS"][$key]["DETAIL_PAGE_URL"] .= "?offer=".$item["PRODUCT_ID"];
	}
	if(isset($arResult["JS_DATA"]["GRID"]["ROWS"]) && is_array($arResult["JS_DATA"]["GRID"]["ROWS"])) foreach($arResult["JS_DATA"]["GRID"]["ROWS"] as $key => $item) {
		if(is_array(CCatalogSku::GetProductInfo($item["data"]["PRODUCT_ID"])))
			$arResult["JS_DATA"]["GRID"]["ROWS"][$key]["data"]["DETAIL_PAGE_URL"] .= "?offer=".$item["data"]["PRODUCT_ID"];
	}
}
?>