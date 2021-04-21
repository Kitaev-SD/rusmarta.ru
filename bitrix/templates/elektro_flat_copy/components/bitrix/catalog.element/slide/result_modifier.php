<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $arSetting;

//USE_PRICE_RATIO//
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);
$inMinPrice = in_array("MIN_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);

//USE_PRICE_RATIO//
if(!$inPriceRatio) {
	if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
		foreach($arResult["OFFERS"] as $key_off => $arOffer) {
			foreach($arOffer["ITEM_PRICES"] as $keyPrice => $itemPrice) {
				$arResult["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["RATIO_BASE_PRICE"] = $itemPrice["BASE_PRICE"];
				$arResult["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_BASE_PRICE"] = $itemPrice["PRINT_BASE_PRICE"];
				$arResult["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["RATIO_PRICE"] = $itemPrice["PRICE"];
				$arResult["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_PRICE"] = $itemPrice["PRINT_PRICE"];
				$arResult["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_DISCOUNT"] = $itemPrice["PRINT_DISCOUNT"];	
				$arResult["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["CATALOG_MEASURE_RATIO"] = "1";
				$arResult["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["MIN_QUANTITY"] = "1";
			}
			$arResult["OFFERS"][$key_off]["CATALOG_MEASURE_RATIO"] = "1";
		}
	} else {
		$arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["RATIO_BASE_PRICE"] = $arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["BASE_PRICE"];
		$arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["PRINT_RATIO_BASE_PRICE"] = $arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["PRINT_BASE_PRICE"];
		$arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["RATIO_PRICE"] = $arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["PRICE"];
		$arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["PRINT_RATIO_PRICE"] = $arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["PRINT_PRICE"];
		$arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["RATIO_DISCOUNT"] = $arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["DISCOUNT"];
		$arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["PRINT_RATIO_DISCOUNT"] = $arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["PRINT_DISCOUNT"];
		$arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["MIN_QUANTITY"] = "1";
		$arResult["CATALOG_MEASURE_RATIO"] = "1";
	}
}

//PREVIEW_PICTURE//	
if(is_array($arResult["PREVIEW_PICTURE"])) {
	if($arResult["PREVIEW_PICTURE"]["WIDTH"] > 178 || $arResult["PREVIEW_PICTURE"]["HEIGHT"] > 178) {
		$arFileTmp = CFile::ResizeImageGet(
			$arResult["PREVIEW_PICTURE"],
			array("width" => 178, "height" => 178),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["PREVIEW_PICTURE"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"]
		);
	}
} elseif(is_array($arResult["DETAIL_PICTURE"])) {
	if($arResult["DETAIL_PICTURE"]["WIDTH"] > 178 || $arResult["DETAIL_PICTURE"]["HEIGHT"] > 178) {
		$arFileTmp = CFile::ResizeImageGet(
			$arResult["DETAIL_PICTURE"],
			array("width" => 178, "height" => 178),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["PREVIEW_PICTURE"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"]
		);
	} else {
		$arResult["PREVIEW_PICTURE"] = $arResult["DETAIL_PICTURE"];
	}
}

//MANUFACTURER//
if(!empty($arResult["PROPERTIES"]["MANUFACTURER"]["VALUE"])) {
	$obElement = CIBlockElement::GetByID($arResult["PROPERTIES"]["MANUFACTURER"]["VALUE"]);
	if($arEl = $obElement->GetNext()) {
		$arResult["PROPERTIES"]["MANUFACTURER"]["NAME"] = $arEl["NAME"];
		
		//PREVIEW_PICTURE//
		if($arEl["PREVIEW_PICTURE"] > 0) {
			$arFile = CFile::GetFileArray($arEl["PREVIEW_PICTURE"]);		
			if($arFile["WIDTH"] > 69 || $arFile["HEIGHT"] > 24) {
				$arFileTmp = CFile::ResizeImageGet(
					$arFile,
					array("width" => 69, "height" => 24),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arResult["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				$arResult["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"] = $arFile;
			}
		}
	}
}

//MIN_PRICE//
if(count($arResult["ITEM_QUANTITY_RANGES"]) > 1 && $inMinPrice) {
	$minPrice = false;
	foreach($arResult["ITEM_PRICES"] as $itemPrice) {
		if($itemPrice["RATIO_PRICE"] == 0)
			continue;
		if($minPrice === false || $minPrice > $itemPrice["RATIO_PRICE"]) {								
			$minPrice = $itemPrice["RATIO_PRICE"];					
			$arResult["MIN_PRICE"] = array(		
				"RATIO_BASE_PRICE" => $itemPrice["RATIO_BASE_PRICE"],
				"PRINT_RATIO_BASE_PRICE" => $itemPrice["PRINT_RATIO_BASE_PRICE"],
				"RATIO_PRICE" => $minPrice,											
				"PERCENT" => $itemPrice["PERCENT"],
				"CURRENCY" => $itemPrice["CURRENCY"]
			);
		}
	}
	if($minPrice === false) {
		$arResult["ITEMS"][$key]["MIN_PRICE"] = array(
			"RATIO_PRICE" => "0",
			"CURRENCY" => $arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]]["CURRENCY"]
		);
	}
} else {
	$arResult["MIN_PRICE"] = $arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]];
}

//OFFERS//
if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
	//TOTAL_OFFERS//		
	$minPrice = false;
	$totalPrices = false;
	foreach($arResult["OFFERS"] as $key_off => $arOffer) {
		foreach($arOffer["ITEM_PRICES"] as $itemPrice) {
			if($itemPrice["RATIO_PRICE"] == 0)
				continue;						
			if($minPrice === false || $minPrice > $itemPrice["RATIO_PRICE"]) {							
				$minPrice = $itemPrice["RATIO_PRICE"];
				$arResult["TOTAL_OFFERS"]["MIN_PRICE"] = array(								
					"RATIO_BASE_PRICE" => $itemPrice["RATIO_BASE_PRICE"],
					"PRINT_RATIO_BASE_PRICE" => $itemPrice["PRINT_RATIO_BASE_PRICE"],
					"RATIO_PRICE" => $minPrice,
					"PERCENT" => $itemPrice["PERCENT"],
					"CURRENCY" => $itemPrice["CURRENCY"]
				);
			}			
			$totalPrices[] = $itemPrice["RATIO_PRICE"];
		}
	}
	if($minPrice === false) {
		$arResult["TOTAL_OFFERS"]["MIN_PRICE"] = array(
			"RATIO_PRICE" => "0",
			"CURRENCY" => $arResult["OFFERS"][0]["ITEM_PRICES"][$arResult["OFFERS"][0]["ITEM_PRICE_SELECTED"]]["CURRENCY"]
		);
	}
	if(count(array_unique($totalPrices)) > 1) {
		$arResult["TOTAL_OFFERS"]["FROM"] = "Y";
	} else {
		$arResult["TOTAL_OFFERS"]["FROM"] = "N";
	}
}