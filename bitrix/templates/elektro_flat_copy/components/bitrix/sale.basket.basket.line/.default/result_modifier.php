<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader,
	Bitrix\Sale;

if(!Loader::includeModule("sale") || !Loader::includeModule("currency"))
	return;

$arResult = array(
	"QUANTITY" => 0,
	"SUM" => 0
);

$currentFuser = Sale\Fuser::getId(true);

$basket = Sale\Basket::loadItemsForFUser($currentFuser, Bitrix\Main\Context::getCurrent()->getSite())->getOrderableItems();
foreach($basket as $arBasketItem) {
	$arResult["QUANTITY"] += $arBasketItem->getQuantity();
}

$arResult["SUM"] = Sale\BasketComponentHelper::getFUserBasketPrice($currentFuser, SITE_ID);

$arCurFormat = CCurrencyLang::GetCurrencyFormat(CSaleLang::GetLangCurrency(SITE_ID), LANGUAGE_ID);

$arResult["DECIMALS"] = $arCurFormat["DECIMALS"];
if($arCurFormat["HIDE_ZERO"] == "Y")
	if(round($arResult["SUM"], $arCurFormat["DECIMALS"]) == round($arResult["SUM"], 0))
		$arResult["DECIMALS"] = 0;

$arResult["DEC_POINT"] = $arCurFormat["DEC_POINT"];

$arResult["THOUSANDS_SEP"] = $arCurFormat["THOUSANDS_SEP"];
if(empty($arResult["THOUSANDS_SEP"]))
	$arResult["THOUSANDS_SEP"] = " ";

$arResult["SUM_FORMATED"] = number_format($arResult["SUM"], $arResult["DECIMALS"], $arResult["DEC_POINT"], $arResult["THOUSANDS_SEP"]);

$arResult["CURRENCY"] = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);?>