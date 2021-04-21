<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $arSetting;

//USE_PRICE_RATIO//
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);
$inMinPrice = in_array("MIN_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);

//USE_PRICE_RATIO//
foreach($arResult["ITEMS"] as $key => $arElement) {	
	if(!$inPriceRatio) {
		if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
			foreach($arElement["OFFERS"] as $key_off => $arOffer) {
				foreach($arOffer["ITEM_PRICES"] as $keyPrice => $itemPrice) {
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["RATIO_BASE_PRICE"] = $itemPrice["BASE_PRICE"];
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_BASE_PRICE"] = $itemPrice["PRINT_BASE_PRICE"];
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["RATIO_PRICE"] = $itemPrice["PRICE"];
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_PRICE"] = $itemPrice["PRINT_PRICE"];
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_DISCOUNT"] = $itemPrice["PRINT_DISCOUNT"];	
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["CATALOG_MEASURE_RATIO"] = "1";
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["MIN_QUANTITY"] = "1";
				}
				$arResult["ITEMS"][$key]["OFFERS"][$key_off]["CATALOG_MEASURE_RATIO"] = "1";
			}
		} else {
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["RATIO_BASE_PRICE"] = $arElement["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["BASE_PRICE"];
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["PRINT_RATIO_BASE_PRICE"] = $arElement["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["PRINT_BASE_PRICE"];
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["RATIO_PRICE"] = $arElement["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["PRICE"];
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["PRINT_RATIO_PRICE"] = $arElement["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["PRINT_PRICE"];
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["RATIO_DISCOUNT"] = $arElement["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["DISCOUNT"];
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["PRINT_RATIO_DISCOUNT"] = $arElement["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["PRINT_DISCOUNT"];
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["MIN_QUANTITY"] = "1";
			$arResult["ITEMS"][$key]["CATALOG_MEASURE_RATIO"] = "1";
		}
	}
}
//END_USE_PRICE_RATIO//

//ELEMENTS//
foreach($arResult["ITEMS"] as $key => $arElement) {
	//PREVIEW_PICTURE//	
	if(is_array($arElement["PREVIEW_PICTURE"])) {
		if($arElement["PREVIEW_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arElement["PREVIEW_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
			$arFileTmp = CFile::ResizeImageGet(
				$arElement["PREVIEW_PICTURE"],
				array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"]
			);
		}
	} elseif(is_array($arElement["DETAIL_PICTURE"])) {
		if($arElement["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arElement["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
			$arFileTmp = CFile::ResizeImageGet(
				$arElement["DETAIL_PICTURE"],
				array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"]
			);
		} else {
			$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = $arElement["DETAIL_PICTURE"];
		}
	}

	//MANUFACTURER//
	$vendorId = intval($arElement["PROPERTIES"]["MANUFACTURER"]["VALUE"]);
	if($vendorId > 0)
		$vendorIds[] = $vendorId;

	//MIN_PRICE//
	if(count($arElement["ITEM_QUANTITY_RANGES"]) > 1 && $inMinPrice) {
		$minPrice = false;
		foreach($arElement["ITEM_PRICES"] as $itemPrice) {
			if($itemPrice["RATIO_PRICE"] == 0)
				continue;
			if($minPrice === false || $minPrice > $itemPrice["RATIO_PRICE"]) {								
				$minPrice = $itemPrice["RATIO_PRICE"];					
				$arResult["ITEMS"][$key]["MIN_PRICE"] = array(		
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
				"CURRENCY" => $arElement["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["CURRENCY"]
			);
		}
	} else {
		$arResult["ITEMS"][$key]["MIN_PRICE"] = $arElement["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]];
	}
	
	//OFFERS//
	if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
		//TOTAL_OFFERS//		
		$minPrice = false;
		$totalPrices = false;
		foreach($arElement["OFFERS"] as $key_off => $arOffer) {
			foreach($arOffer["ITEM_PRICES"] as $itemPrice) {
				if($itemPrice["RATIO_PRICE"] == 0)
					continue;						
				if($minPrice === false || $minPrice > $itemPrice["RATIO_PRICE"]) {							
					$minPrice = $itemPrice["RATIO_PRICE"];
					$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(								
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
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(
				"RATIO_PRICE" => "0",
				"CURRENCY" => $arElement["OFFERS"][0]["ITEM_PRICES"][$arElement["OFFERS"][0]["ITEM_PRICE_SELECTED"]]["CURRENCY"]
			);
		}
		if(count(array_unique($totalPrices)) > 1) {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["FROM"] = "Y";
		} else {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["FROM"] = "N";
		}
		//END_TOTAL_OFFERS//
	}
	//END_OFFERS//
}
//END_ELEMENTS//

//MANUFACTURER//
if(count($vendorIds) > 0) {	
	$arVendor = array();
	$rsElements = CIBlockElement::GetList(
		array(),
		array(
			"ID" => array_unique($vendorIds)
		),
		false,
		false,
		array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE")
	);
	while($arElement = $rsElements->GetNext()) {
		$arVendor[$arElement["ID"]]["NAME"] = $arElement["NAME"];
		if($arElement["PREVIEW_PICTURE"] > 0) {
			$arFile = CFile::GetFileArray($arElement["PREVIEW_PICTURE"]);		
			if($arFile["WIDTH"] > 69 || $arFile["HEIGHT"] > 24) {
				$arFileTmp = CFile::ResizeImageGet(
					$arFile,
					array("width" => 69, "height" => 24),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arVendor[$arElement["ID"]]["PREVIEW_PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				 $arVendor[$arElement["ID"]]["PREVIEW_PICTURE"] = $arFile;
			}
		}
	}
	
	//ELEMENTS//
	foreach($arResult["ITEMS"] as $key => $arElement) {
		//MANUFACTURER//
		$vendorId = intval($arElement["PROPERTIES"]["MANUFACTURER"]["VALUE"]);
		if($vendorId > 0 && isset($arVendor[$vendorId])) {
			$arResult["ITEMS"][$key]["PROPERTIES"]["MANUFACTURER"]["NAME"] = $arVendor[$vendorId]["NAME"];
			$arResult["ITEMS"][$key]["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"] = $arVendor[$vendorId]["PREVIEW_PICTURE"];
		}
	}
}?>