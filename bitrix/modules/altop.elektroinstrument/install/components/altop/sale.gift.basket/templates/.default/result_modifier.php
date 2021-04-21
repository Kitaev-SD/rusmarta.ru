<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $arSetting;

//USE_PRICE_RATIO//
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);

//ITEM_PRICES//
foreach($arResult["ITEMS"] as $key => $arElement) {
	if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
		foreach($arElement["OFFERS"] as $key_off => $arOffer) {			
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICE_MODE"] = Bitrix\Catalog\ProductTable::PRICE_MODE_SIMPLE;
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICE_SELECTED"] = 0;
			$itemPrice = $arOffer["MIN_PRICE"];			
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["CURRENCY"] = $itemPrice["CURRENCY"];
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["BASE_PRICE"] = $itemPrice["VALUE"];
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["PRICE"] = 0;
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["DISCOUNT"] = $itemPrice["VALUE"];
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["PERCENT"] = 100;
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["PRINT_BASE_PRICE"] = CCurrencyLang::CurrencyFormat($itemPrice["VALUE"], $itemPrice["CURRENCY"], true);
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["RATIO_BASE_PRICE"] = $itemPrice["VALUE"] * $arOffer["CATALOG_MEASURE_RATIO"];
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["PRINT_RATIO_BASE_PRICE"] = CCurrencyLang::CurrencyFormat($itemPrice["VALUE"] * $arOffer["CATALOG_MEASURE_RATIO"], $itemPrice["CURRENCY"], true);
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["PRINT_PRICE"] = CCurrencyLang::CurrencyFormat(0, $itemPrice["CURRENCY"], true);
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["RATIO_PRICE"] = 0;
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["PRINT_RATIO_PRICE"] = CCurrencyLang::CurrencyFormat(0, $itemPrice["CURRENCY"], true);
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["PRINT_DISCOUNT"] = CCurrencyLang::CurrencyFormat($itemPrice["VALUE"], $itemPrice["CURRENCY"], true);
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["RATIO_DISCOUNT"] = $itemPrice["VALUE"] * $arOffer["CATALOG_MEASURE_RATIO"];
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["PRINT_RATIO_DISCOUNT"] = CCurrencyLang::CurrencyFormat($itemPrice["VALUE"] * $arOffer["CATALOG_MEASURE_RATIO"], $itemPrice["CURRENCY"], true);
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][0]["MIN_QUANTITY"] = $arOffer["CATALOG_MEASURE_RATIO"];
			unset($itemPrice);
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_MEASURE_RATIOS"][$arOffer["CATALOG_MEASURE"]]["RATIO"] = $arOffer["CATALOG_MEASURE_RATIO"];
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_MEASURE"]["TITLE"] = $arOffer["CATALOG_MEASURE_NAME"];
			$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_MEASURE_RATIO_SELECTED"] = $arOffer["CATALOG_MEASURE"];
		}
		unset($key_off, $arOffer);
	} else {
		$arResult["ITEMS"][$key]["ITEM_PRICE_MODE"] = Bitrix\Catalog\ProductTable::PRICE_MODE_SIMPLE;
		$arResult["ITEMS"][$key]["ITEM_PRICE_SELECTED"] = 0;		
		$itemPrice = $arElement["MIN_PRICE"];
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["CURRENCY"] = $itemPrice["CURRENCY"];
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["BASE_PRICE"] = $itemPrice["VALUE"];
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["PRICE"] = 0;
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["DISCOUNT"] = $itemPrice["VALUE"];
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["PERCENT"] = 100;
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["PRINT_BASE_PRICE"] = CCurrencyLang::CurrencyFormat($itemPrice["VALUE"], $itemPrice["CURRENCY"], true);
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["RATIO_BASE_PRICE"] = $itemPrice["VALUE"] * $arElement["CATALOG_MEASURE_RATIO"];
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["PRINT_RATIO_BASE_PRICE"] = CCurrencyLang::CurrencyFormat($itemPrice["VALUE"] * $arElement["CATALOG_MEASURE_RATIO"], $itemPrice["CURRENCY"], true);
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["PRINT_PRICE"] = CCurrencyLang::CurrencyFormat(0, $itemPrice["CURRENCY"], true);
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["RATIO_PRICE"] = 0;
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["PRINT_RATIO_PRICE"] = CCurrencyLang::CurrencyFormat(0, $itemPrice["CURRENCY"], true);
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["PRINT_DISCOUNT"] = CCurrencyLang::CurrencyFormat($itemPrice["VALUE"], $itemPrice["CURRENCY"], true);
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["RATIO_DISCOUNT"] = $itemPrice["VALUE"] * $arElement["CATALOG_MEASURE_RATIO"];
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["PRINT_RATIO_DISCOUNT"] = CCurrencyLang::CurrencyFormat($itemPrice["VALUE"] * $arElement["CATALOG_MEASURE_RATIO"], $itemPrice["CURRENCY"], true);
		$arResult["ITEMS"][$key]["ITEM_PRICES"][0]["MIN_QUANTITY"] = $arElement["CATALOG_MEASURE_RATIO"];
		unset($itemPrice);
	}
}
unset($key, $arElement);

//OFFERS_PRICES_DEF//
foreach($arResult["ITEMS"] as $key => $arElement) {	
	if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {		
		$arResult["ITEMS"][$key]["OFFERS"] = $arResult["ITEMS"][$key]["OFFERS_DEF"] = array_values($arElement["OFFERS"]);
	} else {		
		$arResult["ITEMS"][$key]["ITEM_PRICES_DEF"] = $arElement["ITEM_PRICES"];
	}
}
unset($key, $arElement);

//USE_PRICE_RATIO//
if(!$inPriceRatio) {
	foreach($arResult["ITEMS"] as $key => $arElement) {
		if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
			foreach($arElement["OFFERS"] as $key_off => $arOffer) {
				foreach($arOffer["ITEM_PRICES"] as $keyPrice => $itemPrice) {					
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["RATIO_BASE_PRICE"] = $itemPrice["BASE_PRICE"];
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_BASE_PRICE"] = $itemPrice["PRINT_BASE_PRICE"];
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["RATIO_DISCOUNT"] = $itemPrice["DISCOUNT"];
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_DISCOUNT"] = $itemPrice["PRINT_DISCOUNT"];
				}
				unset($keyPrice, $itemPrice);
			}
			unset($key_off, $arOffer);
		} else {
			foreach($arElement["ITEM_PRICES"] as $keyPrice => $itemPrice) {						
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_BASE_PRICE"] = $itemPrice["BASE_PRICE"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_BASE_PRICE"] = $itemPrice["PRINT_BASE_PRICE"];		
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_DISCOUNT"] = $itemPrice["DISCOUNT"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_DISCOUNT"] = $itemPrice["PRINT_DISCOUNT"];
			}
			unset($keyPrice, $itemPrice);
		}
	}
	unset($key, $arElement);
}

//ELEMENTS//
foreach($arResult["ITEMS"] as $key => $arElement) {
	//STR_MAIN_ID//
	$arResult["ITEMS"][$key]["STR_MAIN_ID"] = $this->GetEditAreaId($arElement["ID"]);

	//IDS//
	$ids[] = $arElement["ID"];
	
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
			unset($arFileTmp);
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
			unset($arFileTmp);
		} else {
			$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = $arElement["DETAIL_PICTURE"];
		}
	}
	
	//MANUFACTURER//
	$vendorId = intval($arElement["PROPERTIES"]["MANUFACTURER"]["VALUE"]);
	if($vendorId > 0)
		$vendorIds[] = $vendorId;

	//MIN_PRICE//
	$arResult["ITEMS"][$key]["MIN_PRICE"] = $arElement["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]];
	
	//CHECK_QUANTITY//
	$arResult["ITEMS"][$key]["CHECK_QUANTITY"] = $arElement["CATALOG_QUANTITY_TRACE"] == "Y" && $arElement["CATALOG_CAN_BUY_ZERO"] == "N";
	
	//SELECT_PROPS//
	if(is_array($arParams["PROPERTY_CODE_MOD"]) && !empty($arParams["PROPERTY_CODE_MOD"])) {
		$arResult["ITEMS"][$key]["SELECT_PROPS"] = array();
		foreach($arParams["PROPERTY_CODE_MOD"] as $pid) {
			if(!isset($arElement["PROPERTIES"][$pid]))
				continue;
			$prop = &$arElement["PROPERTIES"][$pid];
			$boolArr = is_array($prop["VALUE"]);
			if($prop["MULTIPLE"] == "Y" && $boolArr && !empty($prop["VALUE"])) {
				$arResult["ITEMS"][$key]["SELECT_PROPS"][$pid] = CIBlockFormatProperties::GetDisplayValue($arElement, $prop, "catalog_out");
				if(!is_array($arResult["ITEMS"][$key]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"]) && !empty($arResult["ITEMS"][$key]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"])) {
					$arTmp = $arResult["ITEMS"][$key]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"];
					unset($arResult["ITEMS"][$key]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"]);
					$arResult["ITEMS"][$key]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"][0] = $arTmp;
				}
			} elseif($prop["MULTIPLE"] == "N" && !$boolArr) {
				if($prop["PROPERTY_TYPE"] == "L") {
					$arResult["ITEMS"][$key]["SELECT_PROPS"][$pid] = $prop;
					$property_enums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "CODE" => $pid));
					while($enum_fields = $property_enums->GetNext()) {
						$arResult["ITEMS"][$key]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"][] = $enum_fields["VALUE"];
					}
				}
			}
		}
		unset($pid);
	}
	
	//OFFERS//
	if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
		//TOTAL_OFFERS//	
		$totalQnt = false;
		$minPrice = false;
		$totalPrices = false;
		foreach($arElement["OFFERS"] as $key_off => $arOffer) {		
			$totalQnt += $arOffer["CATALOG_QUANTITY"];
			$itemPrice = $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]];
			if($itemPrice["RATIO_BASE_PRICE"] == 0)
				continue;			
			if($minPrice === false || $minPrice > $itemPrice["RATIO_BASE_PRICE"]) {							
				$minPrice = $itemPrice["RATIO_BASE_PRICE"];
				$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(		
					"ID" => $arOffer["ID"],						
					"RATIO_BASE_PRICE" => $minPrice,
					"PRINT_RATIO_BASE_PRICE" => $itemPrice["PRINT_RATIO_BASE_PRICE"],
					"RATIO_PRICE" => $itemPrice["RATIO_PRICE"],						
					"PRINT_RATIO_DISCOUNT" => $itemPrice["PRINT_RATIO_DISCOUNT"],
					"PERCENT" => $itemPrice["PERCENT"],
					"CURRENCY" => $itemPrice["CURRENCY"],
					"CATALOG_MEASURE_RATIO" => $arOffer["CATALOG_MEASURE_RATIO"],
					"CATALOG_MEASURE_NAME" => $arOffer["CATALOG_MEASURE_NAME"],
					"ITEM_PRICE_MODE" => $arOffer["ITEM_PRICE_MODE"],
					"ITEM_PRICES" => $arOffer["ITEM_PRICES"],
					"ITEM_PRICE_SELECTED" => $arOffer["ITEM_PRICE_SELECTED"],					
					"MIN_QUANTITY" => $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]]["MIN_QUANTITY"],
					"CHECK_QUANTITY" => $arOffer["CHECK_QUANTITY"],
					"CATALOG_QUANTITY" => $arOffer["CATALOG_QUANTITY"],
					"CAN_BUY" => $arOffer["CAN_BUY"],
					"PROPERTIES" => $arOffer["PROPERTIES"],
					"DISPLAY_PROPERTIES" => $arOffer["DISPLAY_PROPERTIES"]
				);				
			}
			$totalPrices[] = $itemPrice["RATIO_BASE_PRICE"];
		}
		unset($key_off, $arOffer);
		if($minPrice === false) {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(
				"RATIO_BASE_PRICE" => "0",
				"CURRENCY" => $arElement["OFFERS"][0]["ITEM_PRICES"][$arElement["OFFERS"][0]["ITEM_PRICE_SELECTED"]]["CURRENCY"],	
				"CATALOG_MEASURE_RATIO" => $arElement["OFFERS"][0]["CATALOG_MEASURE_RATIO"],
				"CATALOG_MEASURE_NAME" => $arElement["OFFERS"][0]["CATALOG_MEASURE_NAME"],
				"MIN_QUANTITY" => $arElement["OFFERS"][0]["ITEM_PRICES"][$arElement["OFFERS"][0]["ITEM_PRICE_SELECTED"]]["MIN_QUANTITY"],
				"CHECK_QUANTITY" => $arElement["OFFERS"][0]["CHECK_QUANTITY"]
			);
		}		
		$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["QUANTITY"] = $totalQnt;
		if(count(array_unique($totalPrices)) > 1) {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["FROM"] = "Y";
		} else {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["FROM"] = "N";
		}
		unset($totalQnt, $minPrice, $totalPrices);
	}
}
unset($key, $arElement);

//PREVIEW_TEXT//
if(count($ids) > 0) {
	$arItems = array();
	$rsElements = CIBlockElement::GetList(array(), array("ID" => $ids), false, false, array("ID", "IBLOCK_ID", "PREVIEW_TEXT"));
	while($arElement = $rsElements->GetNext()) {
		$arItems[$arElement["ID"]] = $arElement["PREVIEW_TEXT"];
	}
	unset($arElement, $rsElements);

	foreach($arResult["ITEMS"] as $key => $arElement) {
		if(isset($arItems[$arElement["ID"]]))
			$arResult["ITEMS"][$key]["PREVIEW_TEXT"] = $arItems[$arElement["ID"]];
	}
	unset($arElement, $arItems);
}
unset($ids);

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
	unset($key, $arElement, $arVendor);
}
unset($vendorIds);?>