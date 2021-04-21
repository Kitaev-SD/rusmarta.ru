<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arResult["PROP_ROWS"] = array();

global $arSetting;

//USE_PRICE_RATIO//
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);

foreach($arParams["PROPERTY_CODE"] as $key => $propCode) {
	if(empty($arResult["SHOW_PROPERTIES"][$propCode]["ID"]) && empty($arResult["DELETED_PROPERTIES"][$propCode]["ID"])) {
		unset($arParams["PROPERTY_CODE"][$key]);
		unset($arResult["SHOW_PROPERTIES"][$propCode]);
	}
}
unset($propCode, $key);

while(count($arParams["PROPERTY_CODE"]) > 0) {
	$arRow = array_splice($arParams["PROPERTY_CODE"], 0, 3);
	while(count($arRow) < 3)
		$arRow[] = false;
	$arResult["PROP_ROWS"][] = $arRow;
}

//ELEMENTS//
foreach($arResult["ITEMS"] as $key => $arElement) {
	//STR_MAIN_ID//
	$arResult["ITEMS"][$key]["STR_MAIN_ID"] = $this->GetEditAreaId($arElement["ID"]);	

	//PREVIEW_PICTURE//
	if(is_array($arElement["FIELDS"]["PREVIEW_PICTURE"])) {
		if($arElement["FIELDS"]["PREVIEW_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arElement["FIELDS"]["PREVIEW_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
			$arFileTmp = CFile::ResizeImageGet(
				$arElement["FIELDS"]["PREVIEW_PICTURE"],
				array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult["ITEMS"][$key]["FIELDS"]["PREVIEW_PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
				"ALT" => $arElement["FIELDS"]["PREVIEW_PICTURE"]["ALT"],
				"TITLE" => $arElement["FIELDS"]["PREVIEW_PICTURE"]["TITLE"]
			);
		}
	} elseif(is_array($arElement["FIELDS"]["DETAIL_PICTURE"])) {
		if($arElement["FIELDS"]["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arElement["FIELDS"]["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
			$arFileTmp = CFile::ResizeImageGet(
				$arElement["FIELDS"]["DETAIL_PICTURE"],
				array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult["ITEMS"][$key]["FIELDS"]["PREVIEW_PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
				"ALT" => $arElement["FIELDS"]["DETAIL_PICTURE"]["ALT"],
				"TITLE" => $arElement["FIELDS"]["DETAIL_PICTURE"]["TITLE"]
			);
		} else {
			$arResult["ITEMS"][$key]["FIELDS"]["PREVIEW_PICTURE"] = $arElement["FIELDS"]["DETAIL_PICTURE"];
		}
	}
	if(is_array($arElement["FIELDS"]["DETAIL_PICTURE"]))
		unset($arResult["ITEMS"][$key]["FIELDS"]["DETAIL_PICTURE"]);
	
	//MANUFACTURER//
	$vendorId = intval($arElement["PROPERTIES"]["MANUFACTURER"]["VALUE"]);
	if($vendorId > 0)
		$vendorIds[] = $vendorId;
	
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

	//MEASURE//
	if(!isset($arElement["CATALOG_MEASURE_RATIO"]))
		$arResult["ITEMS"][$key]["CATALOG_MEASURE_RATIO"] = 1;
	
	$rsRatios = CCatalogMeasureRatio::getList(
		array(),
		array("PRODUCT_ID" => $arElement["ID"]),
		false,
		false,
		array("PRODUCT_ID", "RATIO")
	);
	if($arRatio = $rsRatios->Fetch()) {
		$intRatio = intval($arRatio["RATIO"]);
		$dblRatio = doubleval($arRatio["RATIO"]);
		$mxRatio = ($dblRatio > $intRatio ? $dblRatio : $intRatio);
		if(CATALOG_VALUE_EPSILON > abs($mxRatio))
			$mxRatio = 1;
		elseif(0 > $mxRatio)
			$mxRatio = 1;
		$arResult["ITEMS"][$key]["CATALOG_MEASURE_RATIO"] = $mxRatio;
	}

	if(!isset($arElement["CATALOG_MEASURE"]))
		$arElement["CATALOG_MEASURE"] = 0;
	$arElement["CATALOG_MEASURE"] = intval($arElement["CATALOG_MEASURE"]);
	if(0 > $arElement["CATALOG_MEASURE"])
		$arElement["CATALOG_MEASURE"] = 0;
	if(!isset($arElement["CATALOG_MEASURE_NAME"]))
		$arElement["CATALOG_MEASURE_NAME"] = "";
		
	if(0 < $arElement["CATALOG_MEASURE"]) {
		$rsMeasures = CCatalogMeasure::getList(
			array(),
			array("ID" => $arElement["CATALOG_MEASURE"]),
			false,
			false,
			array("ID", "SYMBOL_RUS")
		);
		if($arMeasure = $rsMeasures->GetNext()) {
			$arResult["ITEMS"][$key]["CATALOG_MEASURE_NAME"] = $arMeasure["SYMBOL_RUS"];
		}
	}
	if("" == $arElement["CATALOG_MEASURE_NAME"]) {
		$arDefaultMeasure = CCatalogMeasure::getDefaultMeasure(true, true);
		$arResult["ITEMS"][$key]["CATALOG_MEASURE_NAME"] = $arDefaultMeasure["SYMBOL_RUS"];
	}
	
	//PRICE_MATRIX//
	$arPriceMatrix = false;
	$arPriceMatrix = $arElement["PRICE_MATRIX"]["MATRIX"];
	foreach($arPriceMatrix as $key_matrix => $item) {
		foreach($item as $key2 => $item2) {
			$arPriceMatrix[$key_matrix][$key2]["QUANTITY_FROM"] = $arElement["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_FROM"];
			$arPriceMatrix[$key_matrix][$key2]["QUANTITY_TO"] = ($arElement["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"] != 0 ? $arElement["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"]: INF);
			$arCurFormat = CCurrencyLang::GetCurrencyFormat($arPriceMatrix[$key_matrix][$key2]["CURRENCY"], LANGUAGE_ID);
			$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
			$arPriceMatrix[$key_matrix][$key2]["PRINT_CURRENCY"] = $currency;
			if($inPriceRatio) {
				$arPriceMatrix[$key_matrix][$key2]["DISCOUNT_PRICE"] = $arElement["CATALOG_MEASURE_RATIO"]*$arElement["PRICE_MATRIX"]["MATRIX"][$key_matrix][$key2]["DISCOUNT_PRICE"];
			}
		}
	}
	
	$price = array();
	$discountPrice = array();
	if(count($arElement["PRICE_MATRIX"]["COLS"]) > 1) {
		foreach($arElement["PRICE_MATRIX"]["CAN_BUY"] as $key_can => $canBuy) {
			if(is_array($arElement["PRICE_MATRIX"]["MATRIX"][$canBuy])) {
				$price[$key_can] = $arElement["PRICE_MATRIX"]["MATRIX"][$canBuy][0]["PRICE"];
				$discountPrice[$key_can] = $arElement["PRICE_MATRIX"]["MATRIX"][$canBuy][0]["DISCOUNT_PRICE"];
			}
		}
		
		//FORMAT_CURRENCY//
		$priceCurr = CCurrencyLang::GetCurrencyFormat($arElement["PRICE_MATRIX"]["MIN_PRICES"][0]["CURRENCY"], LANGUAGE_ID);
		if(empty($price["THOUSANDS_SEP"])):
			$priceCurr["THOUSANDS_SEP"] = " ";
		endif;					
		if($priceCurr["HIDE_ZERO"] == "Y"):						
			if(round(min($discountPrice), $priceCurr["DECIMALS"]) == round(min($discountPrice), 0)):
				$priceCurr["DECIMALS"] = 0;
			endif;
		endif;
		$currency = str_replace("# ", " ", $priceCurr["FORMAT_STRING"]);

		$arResult["ITEMS"][$key]["MIN_PRICE"]["DISCOUNT_VALUE"] = min($discountPrice);
		$arResult["ITEMS"][$key]["MIN_PRICE"]["VALUE"] = min($price);
		$arResult["ITEMS"][$key]["MIN_PRICE"]["PRINT_VALUE"] = min($price)." ".$currency;
		$arResult["ITEMS"][$key]["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"] = (min($price) - min($discountPrice))." ".$currency;
		
		if(!empty($arElement["PRICE_MATRIX"]["CAN_BUY"]))
			$arResult["ITEMS"][$key]["MIN_PRICE"]["CAN_ACCESS"] = true;
		
	}
}

//CONVERT_CURRENCY//
$arConvertParams = array();
if("Y" == $arParams["CONVERT_CURRENCY"]) {
	if(!CModule::IncludeModule("currency")) {
		$arParams["CONVERT_CURRENCY"] = "N";
		$arParams["CURRENCY_ID"] = "";
	} else {
		$arResultModules["currency"] = true;
		$arCurrencyInfo = CCurrency::GetByID($arParams["CURRENCY_ID"]);
		if(!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo))) {
			$arParams["CONVERT_CURRENCY"] = "N";
			$arParams["CURRENCY_ID"] = "";
		} else {
			$arParams["CURRENCY_ID"] = $arCurrencyInfo["CURRENCY"];
			$arConvertParams["CURRENCY_ID"] = $arCurrencyInfo["CURRENCY"];
		}
	}
}

//OFFERS//
$offersFilter = array(
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"]
);
if(!$arParams["USE_PRICE_COUNT"])
	$offersFilter["SHOW_PRICE_COUNT"] = $arParams["SHOW_PRICE_COUNT"];

$arOffers = CIBlockPriceTools::GetOffersArray(
	$offersFilter,
	$arResult["ITEMS"],
	array(
		$arParams["OFFERS_SORT_FIELD"] => $arParams["OFFERS_SORT_ORDER"],
		$arParams["OFFERS_SORT_FIELD2"] => $arParams["OFFERS_SORT_ORDER2"],
	),
	$arParams["OFFERS_FIELD_CODE"],
	$arParams["OFFERS_PROPERTY_CODE"],
	$arParams["OFFERS_LIMIT"],
	$arResult["PRICES"],
	$arParams["PRICE_VAT_INCLUDE"],
	$arConvertParams
);
if(!empty($arOffers)) {
	$arElementLink = array();
	foreach($arResult["ITEMS"] as $key => $arElement) {		
		$arElementLink[$arElement["ID"]] = &$arResult["ITEMS"][$key];
		$arElementLink[$arElement["ID"]]["OFFERS"] = array();
	}
	unset($arElement, $key);

	foreach($arOffers as $arOffer) {
		$linkElement = $arOffer["LINK_ELEMENT_ID"];
		if(!isset($arElementLink[$arOffer["LINK_ELEMENT_ID"]]))
			continue;
		$arElementLink[$linkElement]["OFFERS"][] = $arOffer;
		unset($linkElement);
	}
	unset($arOffer);
}
unset($arOffers);

//ELEMENTS//
foreach($arResult["ITEMS"] as $key => $arElement) {	
	//OFFERS//
	if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
		//TOTAL_OFFERS//	
		$totalQnt = false;
		$totalDiscount = array();
		
		$minId = false;
		$minPrice = false;	
		$minPrintPrice = false;
		$minDiscount = false;
		$minDiscountDiff = false;
		$minDiscountDiffPercent = false;
		$minCurr = false;
		$minMeasureRatio = false;
		$minMeasure = false;
		$minCheckQnt = false;
		$minQnt = false;
		$minCanByu = false;
		$minProperties = false;
		$minDisplayProperties = false;
		
		$arResult["ITEMS"][$key]["TOTAL_OFFERS"] = array();
		
		foreach($arElement["OFFERS"] as $key_off => $arOffer) {			
			$totalQnt += $arOffer["CATALOG_QUANTITY"];
			
			if($arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] == 0)
				continue;

			$totalDiscount[] = $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"];
			
			if($minDiscount === false || $minDiscount > $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"]) {			
				$minId = $arOffer["ID"];
				$minPrice = $arOffer["MIN_PRICE"]["VALUE"];			
				$minPrintPrice = $arOffer["MIN_PRICE"]["PRINT_VALUE"];
				$minDiscount = $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"];
				$minDiscountDiff = $arOffer["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"];
				$minDiscountDiffPercent = $arOffer["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"];
				$minCurr = $arOffer["MIN_PRICE"]["CURRENCY"];			
				$minMeasureRatio = $arOffer["CATALOG_MEASURE_RATIO"];
				$minMeasure = $arOffer["CATALOG_MEASURE_NAME"];
				$minCheckQnt = $arOffer["CHECK_QUANTITY"];				
				$minQnt = $arOffer["CATALOG_QUANTITY"];
				$minCanByu = $arOffer["CAN_BUY"];
				$minProperties = $arOffer["PROPERTIES"];
				$minDisplayProperties = $arOffer["DISPLAY_PROPERTIES"];
			}
		}
		
		if(count($totalDiscount) > 0) {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(		
				"ID" => $minId,
				"VALUE" => $minPrice,		
				"PRINT_VALUE" => $minPrintPrice,
				"DISCOUNT_VALUE" => $minDiscount,
				"PRINT_DISCOUNT_DIFF" => $minDiscountDiff,
				"DISCOUNT_DIFF_PERCENT" => $minDiscountDiffPercent,
				"CURRENCY" => $minCurr,		
				"CATALOG_MEASURE_RATIO" => $minMeasureRatio,
				"CATALOG_MEASURE_NAME" => $minMeasure,
				"CHECK_QUANTITY" => $minCheckQnt,			
				"CATALOG_QUANTITY" => $minQnt,
				"CAN_BUY" => $minCanByu,
				"PROPERTIES" => $minProperties,
				"DISPLAY_PROPERTIES" => $minDisplayProperties
			);
		} else {
			$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(
				"VALUE" => "0",
				"CURRENCY" => $arElement["OFFERS"][0]["MIN_PRICE"]["CURRENCY"],
				"CATALOG_MEASURE_RATIO" => $arElement["OFFERS"][0]["CATALOG_MEASURE_RATIO"],
				"CATALOG_MEASURE_NAME" => $arElement["OFFERS"][0]["CATALOG_MEASURE_NAME"]
			);
		}
		
		$arResult["ITEMS"][$key]["TOTAL_OFFERS"]["QUANTITY"] = $totalQnt;
		
		if(count(array_unique($totalDiscount)) > 1) {
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