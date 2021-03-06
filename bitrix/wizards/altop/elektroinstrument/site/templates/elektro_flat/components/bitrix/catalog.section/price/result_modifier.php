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
			foreach($arElement["ITEM_PRICES"] as $keyPrice => $itemPrice) {
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_BASE_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["BASE_PRICE"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_BASE_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["PRINT_BASE_PRICE"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["PRICE"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["PRINT_PRICE"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_DISCOUNT"] = $arElement["ITEM_PRICES"][$keyPrice]["DISCOUNT"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_DISCOUNT"] = $arElement["ITEM_PRICES"][$keyPrice]["PRINT_DISCOUNT"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["MIN_QUANTITY"] = "1";
			}
			$arResult["ITEMS"][$key]["CATALOG_MEASURE_RATIO"] = "1";
		}
	}
}
//END_USE_PRICE_RATIO//

//ELEMENTS//
foreach($arResult["ITEMS"] as $key => $arElement) {
	//STR_MAIN_ID//
	$arResult["ITEMS"][$key]["STR_MAIN_ID"] = $this->GetEditAreaId($arElement["ID"]);
	
	//PRICE_MATRIX//
	$arPriceMatrix = $arElement["PRICE_MATRIX"]["MATRIX"];
	if(isset($arPriceMatrix) && is_array($arPriceMatrix)) foreach($arPriceMatrix as $key_matrix => $item) {
		foreach($item as $key2 => $item2) {
			$arElement["PRICE_MATRIX"]["MATRIX"][$key_matrix][$key2]["QUANTITY_FROM"] = $arElement["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_FROM"];
			$arElement["PRICE_MATRIX"]["MATRIX"][$key_matrix][$key2]["QUANTITY_TO"] = ($arElement["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"] != 0? $arElement["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"]: INF);
			$arCurFormat = CCurrencyLang::GetCurrencyFormat($arPriceMatrix[$key_matrix][$key2]["CURRENCY"], LANGUAGE_ID);
			$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
			$arElement["PRICE_MATRIX"]["MATRIX"][$key_matrix][$key2]["PRINT_CURRENCY"] = $currency;
		}
	}
	$arResult["ITEMS"][$key]["MATRIX"] = $arElement["PRICE_MATRIX"];

	//CURRENT_DISCOUNT//
	$arPrice = array();
	$arResult["ITEMS"][$key]["CURRENT_DISCOUNT"] = array();	

	if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
		$minId = false;
		$minRatioPrice = false;
		foreach($arElement["OFFERS"] as $key_off => $arOffer) {
			$arOffer["MIN_PRICE"] = $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]];
			if($arOffer["MIN_PRICE"]["RATIO_PRICE"] == 0)
				continue;
			if($minRatioPrice === false || $minRatioPrice > $arOffer["MIN_PRICE"]["RATIO_PRICE"]) {			
				$minId = $arOffer["ID"];
				$minRatioPrice = $arOffer["MIN_PRICE"]["RATIO_PRICE"];
			}
		}
		if($minId > 0) {
			$arDiscounts = CCatalogDiscount::GetDiscountByProduct($minId, $USER->GetUserGroupArray(), "N", array(), SITE_ID);
			$arResult["ITEMS"][$key]["CURRENT_DISCOUNT"] = current($arDiscounts);
		}
	} else {
		$arDiscounts = CCatalogDiscount::GetDiscountByProduct($arElement["ID"], $USER->GetUserGroupArray(), "N", array(), SITE_ID);
		$arResult["ITEMS"][$key]["CURRENT_DISCOUNT"] = current($arDiscounts);
	}

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
					"PRINT_RATIO_DISCOUNT" => $itemPrice["PRINT_RATIO_DISCOUNT"],
					"PERCENT" => $itemPrice["PERCENT"],
					"CURRENCY" => $itemPrice["CURRENCY"],					
					"MIN_QUANTITY" => $arElement["ITEM_PRICES"][$arElement["ITEM_PRICE_SELECTED"]]["MIN_QUANTITY"]
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
	}
	
	//OFFERS//
	if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
		//TOTAL_OFFERS//	
		$totalQnt = false;		
		$minPrice = false;
		$totalPrices = false;
		foreach($arElement["OFFERS"] as $key_off => $arOffer) {		
			$totalQnt += $arOffer["CATALOG_QUANTITY"];
			foreach($arOffer["ITEM_PRICES"] as $itemPrice) {
				if($itemPrice["RATIO_PRICE"] == 0)
					continue;
				if($minPrice === false || $minPrice > $itemPrice["RATIO_PRICE"]) {							
					$minPrice = $itemPrice["RATIO_PRICE"];
					$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(		
						"ID" => $arOffer["ID"],						
						"RATIO_BASE_PRICE" => $itemPrice["RATIO_BASE_PRICE"],
						"PRINT_RATIO_BASE_PRICE" => $itemPrice["PRINT_RATIO_BASE_PRICE"],
						"RATIO_PRICE" => $minPrice,						
						"PRINT_RATIO_DISCOUNT" => $itemPrice["PRINT_RATIO_DISCOUNT"],
						"PERCENT" => $itemPrice["PERCENT"],
						"CURRENCY" => $itemPrice["CURRENCY"],
						"CATALOG_MEASURE_RATIO" => $arOffer["CATALOG_MEASURE_RATIO"],
						"CATALOG_MEASURE_NAME" => $arOffer["CATALOG_MEASURE_NAME"],
						"ITEM_PRICE_MODE" => $arOffer["ITEM_PRICE_MODE"],
						"ITEM_PRICES" => $arOffer["ITEM_PRICES"],
						"ITEM_PRICE_SELECTED" => $arOffer["ITEM_PRICE_SELECTED"],
						"ITEM_QUANTITY_RANGES" => $arOffer["ITEM_QUANTITY_RANGES"],
						"MIN_QUANTITY" => $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]]["MIN_QUANTITY"],
						"CHECK_QUANTITY" => $arOffer["CHECK_QUANTITY"],
						"CATALOG_QUANTITY" => $arOffer["CATALOG_QUANTITY"],
						"CAN_BUY" => $arOffer["CAN_BUY"],
						"PROPERTIES" => $arOffer["PROPERTIES"],
						"DISPLAY_PROPERTIES" => $arOffer["DISPLAY_PROPERTIES"]
					);
					//PRICE_MATRIX//
					$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams['IBLOCK_ID'], $arParams['PRICE_CODE']);
					foreach ($arResultPrices as $value) {
						$arPriceTypeID[] = $value['ID'];
					}
					if (isset($value))
						unset($value);
				  
					$arOffer['PRICE_MATRIX'] = CatalogGetPriceTableEx($arOffer['ID'], 0, $arPriceTypeID, 'Y');
				
					$arMatrix;
					$arPriceMatrix = false;
					if(true) {
						$arPriceMatrix = $arOffer["PRICE_MATRIX"]["MATRIX"];
						foreach($arPriceMatrix as $key_matrix => $item) {
							foreach($item as $key2 => $item2) {
								$arOffer["PRICE_MATRIX"]["MATRIX"][$key_matrix][$key2]["QUANTITY_FROM"] = $arOffer["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_FROM"];
								$arOffer["PRICE_MATRIX"]["MATRIX"][$key_matrix][$key2]["QUANTITY_TO"] = ($arOffer["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"] != 0? $arOffer["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"]: INF);
								$arCurFormat = CCurrencyLang::GetCurrencyFormat($arPriceMatrix[$key_matrix][$key2]["CURRENCY"], LANGUAGE_ID);
								$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
								$arOffer["PRICE_MATRIX"]["MATRIX"][$key_matrix][$key2]["PRINT_CURRENCY"] = $currency;
							}
						}
					}
					$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["MATRIX"] = $arOffer["PRICE_MATRIX"];
				}
				$totalPrices[] = $itemPrice["RATIO_PRICE"];
			}
		}
		if($minPrice === false) {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(
				"RATIO_PRICE" => "0",
				"CURRENCY" => $arElement["OFFERS"][0]["ITEM_PRICES"][$arElement["OFFERS"][0]["ITEM_PRICE_SELECTED"]]["CURRENCY"],	
				"CATALOG_MEASURE_RATIO" => $arElement["OFFERS"][0]["CATALOG_MEASURE_RATIO"],
				"CATALOG_MEASURE_NAME" => $arElement["OFFERS"][0]["CATALOG_MEASURE_NAME"]
			);
		}
		$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["QUANTITY"] = $totalQnt;
		if(count(array_unique($totalPrices)) > 1) {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["FROM"] = "Y";
		} else {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["FROM"] = "N";
		}
		//END_TOTAL_OFFERS//
	}
	//END_OFFERS//
}
//END_ELEMENTS//?>