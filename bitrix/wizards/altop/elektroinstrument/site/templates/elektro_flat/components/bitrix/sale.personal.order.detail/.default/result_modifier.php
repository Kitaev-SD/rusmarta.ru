<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Loader;
use \Bitrix\Sale\Delivery\ExtraServices\Manager;

global $arSetting;

if(!Loader::includeModule('iblock') || !Loader::includeModule('catalog') || !Loader::includeModule('sale'))
	return;

foreach($arResult['SHIPMENT'] as $keyS => $shipment) {
	$arListExtraService = Manager::getValuesForShipment($shipment['ID'], $shipment['DELIVERY']['ID']);
	$extraServiceManager = new Manager($shipment['DELIVERY']['ID']);
	$extraService = $extraServiceManager->getItems();
	foreach ($extraService as $itemId => $item) {
		if(array_key_exists($itemId, $arListExtraService)) {
			$arResult['SHIPMENT'][$keyS]['EXTRA_SERVICE'][$itemId] = array(
				'ID' => $itemId,
				'NAME' => $item->getName(),
				'DESCRIPTION' => $item->getDescription(),
				'PARAMS' => $item->getParams(),
				'VALUE' => $arListExtraService[$itemId]
			);
		}
	}
	unset($arListExtraService, $extraServiceManager, $extraService);
}

foreach($arResult["BASKET"] as $key => $arBasketItems) {
	$ar = CIBlockElement::GetList(
		array(), 
		array("ID" => $arBasketItems["PRODUCT_ID"]), 
		false, 
		false, 
		array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
	)->Fetch();		
	if($ar["DETAIL_PICTURE"] > 0) {
		$arResult["BASKET"][$key]["DETAIL_PICTURE"] = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array("width" => 30, "height" => 30), BX_RESIZE_IMAGE_PROPORTIONAL, true);
	} else {
		$mxResult = CCatalogSku::GetProductInfo($ar["ID"]);
		if(is_array($mxResult)) {
			$ar = CIBlockElement::GetList(
				array(), 
				array("ID" => $mxResult["ID"]), 
				false, 
				false, 
				array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
			)->Fetch();
			if($ar["DETAIL_PICTURE"] > 0) {
				$arResult["BASKET"][$key]["DETAIL_PICTURE"] = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array("width" => 30, "height" => 30), BX_RESIZE_IMAGE_PROPORTIONAL, true);
			}
		}
	}
	if(in_array("OFFERS_LINK_SHOW", $arSetting["GENERAL_SETTINGS"]["VALUE"]) && is_array(CCatalogSku::GetProductInfo($arBasketItems["PRODUCT_ID"]))) {
		$arResult["BASKET"][$key]["DETAIL_PAGE_URL"] .= "?offer=".$arBasketItems["PRODUCT_ID"];
	}
}?>