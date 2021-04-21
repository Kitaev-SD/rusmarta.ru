<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $arSetting;

//USE_PRICE_RATIO//
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);
$inMinPrice = in_array("MIN_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);

//COLLECTION
$arSectionAll = array("0" => GetMessage("ALL_COLLECTION"));
foreach($arResult["ITEMS"] as $key => $arElement) {	
	$arCollectionSection = CIBlockElement::GetProperty($arParams["IBLOCK_ID"],$arElement["ID"],array("sort" => "asc"),array("CODE" => "COLLECTION_SECTION"))->Fetch();
	if(!empty($arCollectionSection["VALUE"])) {
		$arValue = CIBlockPropertyEnum::GetByID($arCollectionSection["VALUE"]);
		$arSections[$arValue["XML_ID"]] = $arValue["VALUE"];
	}
}
$arResult["SECTION_COLLECTION"] = array_merge($arSectionAll,array_unique($arSections));

//USE_PRICE_RATIO//
if(!$inPriceRatio) {
	foreach($arResult["ITEMS"] as $key => $arElement) {	
		if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
			foreach($arElement["OFFERS"] as $key_off => $arOffer) {
				foreach($arOffer["ITEM_PRICES"] as $keyPrice => $itemPrice) {
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["RATIO_BASE_PRICE"] = $itemPrice["BASE_PRICE"];
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_BASE_PRICE"] = $itemPrice["PRINT_BASE_PRICE"];
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["RATIO_PRICE"] = $itemPrice["PRICE"];
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_PRICE"] = $itemPrice["PRINT_PRICE"];
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_DISCOUNT"] = $itemPrice["PRINT_DISCOUNT"];	
				}
			}
		} else {
			foreach($arElement["ITEM_PRICES"] as $keyPrice => $itemPrice) {
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_BASE_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["BASE_PRICE"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_BASE_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["PRINT_BASE_PRICE"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["PRICE"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["PRINT_PRICE"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_DISCOUNT"] = $arElement["ITEM_PRICES"][$keyPrice]["DISCOUNT"];
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_DISCOUNT"] = $arElement["ITEM_PRICES"][$keyPrice]["PRINT_DISCOUNT"];
			}
		}
	}
} else {
	foreach($arResult["ITEMS"] as $key => $arElement) {	
		if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
			foreach($arElement["OFFERS"] as $key_off => $arOffer) {
				foreach($arOffer["ITEM_PRICES"] as $keyPrice => $itemPrice) {
					$arResult["ITEMS"][$key]["OFFERS"][$key_off]["ITEM_PRICES"][$keyPrice]["PRICE"] = $arOffer["CATALOG_MEASURE_RATIO"]*$arOffer["ITEM_PRICES"][$keyPrice]["PRICE"];
				}
			}
		} else {
			foreach($arElement["ITEM_PRICES"] as $keyPrice => $itemPrice) {
				$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRICE"] =  $arElement["CATALOG_MEASURE_RATIO"]*$arElement["ITEM_PRICES"][$keyPrice]["PRICE"];
			}
		}
	}
}
//END_USE_PRICE_RATIO//

//MIN_QUANTITY//
foreach($arResult["ITEMS"] as $key => $arElement) {	
	foreach($arElement["ITEM_PRICES"] as $keyPrice => $itemPrice) {
		$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["MIN_QUANTITY"] =  $arElement["CATALOG_MEASURE_RATIO"];
	}
}

//ELEMENTS//
foreach($arResult["ITEMS"] as $key => $arElement) {
	//STR_MAIN_ID//
	$arResult["ITEMS"][$key]["STR_MAIN_ID"] = $this->GetEditAreaId($arElement["ID"]);
	
	//PRICE_MATRIX//
	$arPriceMatrix = false;
	$arPriceMatrix = $arElement["PRICE_MATRIX"]["MATRIX"];
	if(isset($arPriceMatrix) && is_array($arPriceMatrix)) foreach($arPriceMatrix as $key_matrix => $item) {
		foreach($item as $key2 => $item2) {
			$arPriceMatrix[$key_matrix][$key2]["QUANTITY_FROM"] = $arElement["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_FROM"];
			$arPriceMatrix[$key_matrix][$key2]["QUANTITY_TO"] = ($arElement["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"] != 0? $arElement["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"]: INF);
			$arCurFormat = CCurrencyLang::GetCurrencyFormat($arPriceMatrix[$key_matrix][$key2]["CURRENCY"], LANGUAGE_ID);
			$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
			$arPriceMatrix[$key_matrix][$key2]["PRINT_CURRENCY"] = $currency;
			if($inPriceRatio) {
				$arPriceMatrix[$key_matrix][$key2]["DISCOUNT_PRICE"] = $arElement["CATALOG_MEASURE_RATIO"]*$arElement["PRICE_MATRIX"]["MATRIX"][$key_matrix][$key2]["DISCOUNT_PRICE"];
			}
		}
	}
	$arResult["ITEMS"][$key]["PRICE_MATRIX_SHOW"]["COLS"] = $arResult["ITEMS"][$key]["PRICE_MATRIX"]["COLS"];
	$arResult["ITEMS"][$key]["PRICE_MATRIX_SHOW"]["MATRIX"] = $arPriceMatrix;

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
								$arPriceMatrix[$key_matrix][$key2]["QUANTITY_FROM"] = $arOffer["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_FROM"];
								$arPriceMatrix[$key_matrix][$key2]["QUANTITY_TO"] = ($arOffer["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"] != 0? $arOffer["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"]: INF);
								$arCurFormat = CCurrencyLang::GetCurrencyFormat($arPriceMatrix[$key_matrix][$key2]["CURRENCY"], LANGUAGE_ID);
								$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
								$arPriceMatrix[$key_matrix][$key2]["PRINT_CURRENCY"] = $currency;
								if($inPriceRatio) {
									$arPriceMatrix[$key_matrix][$key2]["DISCOUNT_PRICE"] = $arOffer["CATALOG_MEASURE_RATIO"]*$arOffer["PRICE_MATRIX"]["MATRIX"][$key_matrix][$key2]["DISCOUNT_PRICE"];
								}
							}
						}
					}
					$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["PRICE_MATRIX_SHOW"]["COLS"] = $arOffer["PRICE_MATRIX"]["COLS"];
					$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["PRICE_MATRIX_SHOW"]["MATRIX"] = $arPriceMatrix;
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