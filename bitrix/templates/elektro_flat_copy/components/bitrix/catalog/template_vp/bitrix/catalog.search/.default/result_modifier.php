<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(!CModule::IncludeModule("catalog"))
	return;

$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams["IBLOCK_ID"]);

if(is_array($arSKU)) {
	$arResult["OFFERS"]["SKU_IBLOCK_ID"] = $arSKU["IBLOCK_ID"];
}?>