<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $arSetting;

//USE_PRICE_RATIO//
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);

$arResult["SEARCH"] = array();

$arSections = array();
$arItems = array();
foreach($arResult["CATEGORIES"] as $category_id => $arCategory) {
	foreach($arCategory["ITEMS"] as $i => $arItem) {
		if(isset($arItem["ITEM_ID"]) && $arItem["MODULE_ID"] == "iblock") {
			if(substr($arItem["ITEM_ID"], 0, 1) === "S") {
				$arSections[] = substr($arItem["ITEM_ID"], 1);				
			}			
			if(substr($arItem["ITEM_ID"], 0, 1) !== "S") {
				$arItems[] = $arItem["ITEM_ID"];
			}			
		}
	}
}

//ACTIVE_SECTIONS//
if(!empty($arSections) && CModule::IncludeModule("iblock")) {	
	$arActiveSections = array();
	
	$rsSection = CIBlockSection::GetList(
		array(), 
		array(
			"GLOBAL_ACTIVE" => "Y",
			"ID" => $arSections,
			"IBLOCK_ID" => $arParams["IBLOCK_ID"]
		),
		false,
		array("ID", "IBLOCK_ID", "PICTURE")
	);
	while($arSection = $rsSection->GetNext()) {
		//PICTURE//
		if($arSection["PICTURE"] > 0) {
			$arFile = CFile::GetFileArray($arSection["PICTURE"]);
			if($arFile["WIDTH"] > 62 || $arFile["HEIGHT"] > 62) {
				$arFileTmp = CFile::ResizeImageGet(
					$arFile,
					array("width" => 62, "height" => 62),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arSection["PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				$arSection["PICTURE"] = $arFile;
			}
		}
		$arActiveSections[$arSection["ID"]] = $arSection;
	}
	
	if(!empty($arActiveSections)) {
		foreach($arResult["CATEGORIES"] as $category_id => $arCategory) {
			foreach($arCategory["ITEMS"] as $i => $arItem) {
				if(isset($arItem["ITEM_ID"]) && $arItem["MODULE_ID"] == "iblock") {
					if(substr($arItem["ITEM_ID"], 0, 1) === "S") {
						$arItem["ITEM_ID"] = substr($arItem["ITEM_ID"], 1);
						if($arActiveSections[$arItem["ITEM_ID"]]) {
							if($arActiveSections[$arItem["ITEM_ID"]]["PICTURE"])
								$arResult["CATEGORIES"][$category_id]["ITEMS"][$i]["PREVIEW_PICTURE"] = $arActiveSections[$arItem["ITEM_ID"]]["PICTURE"];
							$arResult["CATEGORIES"][$category_id]["ITEMS"][$i]["ICON"] = true;
						} else {
							unset($arResult["CATEGORIES"][$category_id]["ITEMS"][$i]);
						}
					}
				}
			}
		}
	}
}

//ACTIVE_ELEMENTS//
if(!empty($arItems) && CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")) {	
	$ids = array();
	$arNewItems = array();
	$arActiveItems = array();
	$arNewActiveItems = array();
	
	foreach($arItems as $i => $arItemId) {
		$mxResult = CCatalogSku::GetProductInfo($arItemId);
		if(is_array($mxResult)) {
			if($arParams["HIDE_NOT_AVAILABLE_OFFERS"] == "Y") {
				$canBuy = CCatalogProduct::GetByID($arItemId);
				$canBuy = $canBuy["QUANTITY"];
				if($canBuy <= 0 || empty($canBuy)) {
					unset($mxResult);
				}
			}
		}		
		$ids[] = is_array($mxResult) ? $mxResult["ID"] : $arItemId;
		$arNewItems[] = array(
			"ID" => $arItemId,
			"PRODUCT_ID" => is_array($mxResult) ? $mxResult["ID"] : $arItemId
		);
	}
	
	$rsElement = CIBlockElement::GetList(
		array(),
		array(
			"ID" => array_unique($ids),
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"SECTION_GLOBAL_ACTIVE" => "Y"
		),
		false, 
		false, 
		array("ID", "IBLOCK_ID")
	);
	while($arElement = $rsElement->GetNext()) {		
		$arActiveItems[$arElement["ID"]] = $arElement;
	}	

	foreach($arNewItems as $i => $arNewItem) {
		if($arActiveItems[$arNewItem["PRODUCT_ID"]])
			$arNewActiveItems[$arNewItem["ID"]] = $arNewItem["ID"];
	}
	if(!empty($arNewActiveItems)) {
		foreach($arResult["CATEGORIES"] as $category_id => $arCategory) {
			foreach($arCategory["ITEMS"] as $i => $arItem) {
				if(isset($arItem["ITEM_ID"]) && $arItem["MODULE_ID"] == "iblock") {
					if(substr($arItem["ITEM_ID"], 0, 1) !== "S") {						
						if($arNewActiveItems[$arItem["ITEM_ID"]]) {							
							$arResult["CATEGORIES"][$category_id]["ITEMS"][$i]["ICON"] = true;
							$arResult["SEARCH"][$arItem["ITEM_ID"]] = &$arResult["CATEGORIES"][$category_id]["ITEMS"][$i];
						} else {
							unset($arResult["CATEGORIES"][$category_id]["ITEMS"][$i]);
						}
					}
				}
			}
		}
	}
}

if(!empty($arNewActiveItems) && CModule::IncludeModule("iblock")) {	
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

	if(is_array($arParams["PRICE_CODE"]))
		$arr["PRICES"] = CIBlockPriceTools::GetCatalogPrices(0, $arParams["PRICE_CODE"]);
	else
		$arr["PRICES"] = array();
	
	$arSelect = array("ID", "IBLOCK_ID", "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_COLLECTION");

	$arFilter = array(
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
		"MIN_PERMISSION" => "R",
		"ID" => array_keys($arNewActiveItems)	
	);

	foreach($arr["PRICES"] as $key => $value) {
		$arSelect[] = $value["SELECT"];
		$arrFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = 1;
	}
	
	$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);	
	while($obElement = $rsElements->GetNextElement()) {
		$arItem = $obElement->GetFields();

		//COLLECTION
		$arProp = CIBlockElement::GetProperty($arParams["IBLOCK_ID"],$arItem["ID"],array("sort" => "asc"),array("CODE" => "THIS_COLLECTION"))->Fetch();
		if(!empty($arProp["VALUE"]))
			$arResult["SEARCH"][$arItem["ID"]]["COLLECTION"] = true;
		else
			$arResult["SEARCH"][$arItem["ID"]]["COLLECTION"] = false;

		//STR_MAIN_ID//
		$arResult["SEARCH"][$arItem["ID"]]["STR_MAIN_ID"] = $this->GetEditAreaId($arItem["ID"]);

		$mxResult = CCatalogSku::GetProductInfo($arItem["ID"]);
		
		if($arItem["PREVIEW_PICTURE"] <= 0 && $arItem["DETAIL_PICTURE"] <= 0) {
			if(is_array($mxResult)) {
				$res = CIBlockElement::GetByID($mxResult["ID"]);
				if($ar_res = $res->GetNext()) {
					$arItem["PREVIEW_PICTURE"] = $ar_res["PREVIEW_PICTURE"];
					$arItem["DETAIL_PICTURE"] = $ar_res["DETAIL_PICTURE"];
				}
			}
		}
		
		//PREVIEW_PICTURE//
		if($arItem["PREVIEW_PICTURE"] > 0) {
			$arFile = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
			if($arFile["WIDTH"] > 178 || $arFile["HEIGHT"] > 178) {
				$arFileTmp = CFile::ResizeImageGet(
					$arFile,
					array("width" => 178, "height" => 178),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arResult["SEARCH"][$arItem["ID"]]["PREVIEW_PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				$arResult["SEARCH"][$arItem["ID"]]["PREVIEW_PICTURE"] = $arFile;
			}
		} elseif($arItem["DETAIL_PICTURE"] > 0) {
			$arFile = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);
			if($arFile["WIDTH"] > 178 || $arFile["HEIGHT"] > 178) {
				$arFileTmp = CFile::ResizeImageGet(
					$arFile,
					array("width" => 178, "height" => 178),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arResult["SEARCH"][$arItem["ID"]]["PREVIEW_PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				$arResult["SEARCH"][$arItem["ID"]]["PREVIEW_PICTURE"] = $arFile;
			}
		}

		$arItem["PROPERTIES"] = $obElement->GetProperties();
		$arResult["SEARCH"][$arItem["ID"]]["PROPERTIES"] = $arItem["PROPERTIES"];	
		
		if(is_array($mxResult)) {
			foreach($arParams["OFFERS_PROPERTY_CODE"] as $pid) {
				if(!isset($arItem["PROPERTIES"][$pid]))
					continue;
				$prop = &$arItem["PROPERTIES"][$pid];
				$boolArr = is_array($prop["VALUE"]);
				if(($boolArr && !empty($prop["VALUE"])) || (!$boolArr && strlen($prop["VALUE"]) > 0)) {
					$arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "catalog_out");
				}
			}
			$arResult["SEARCH"][$arItem["ID"]]["DISPLAY_PROPERTIES"] = $arItem["DISPLAY_PROPERTIES"];			
		}

		//SELECT_PROPS//
		if(is_array($arParams["PROPERTY_CODE_MOD"]) && !empty($arParams["PROPERTY_CODE_MOD"])) {
			$arResult["SEARCH"][$arItem["ID"]]["SELECT_PROPS"] = array();
			foreach($arParams["PROPERTY_CODE_MOD"] as $pid) {				
				if(!isset($arItem["PROPERTIES"][$pid]))
					continue;
				$prop = &$arItem["PROPERTIES"][$pid];				
				$boolArr = is_array($prop["VALUE"]);				
				if($prop["MULTIPLE"] == "Y" && $boolArr && !empty($prop["VALUE"])) {
					$arResult["SEARCH"][$arItem["ID"]]["SELECT_PROPS"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "catalog_out");
					if(!is_array($arResult["SEARCH"][$arItem["ID"]]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"]) && !empty($arResult["SEARCH"][$arItem["ID"]]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"])) {
						$arTmp = $arResult["SEARCH"][$arItem["ID"]]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"];
						unset($arResult["SEARCH"][$arItem["ID"]]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"]);
						$arResult["SEARCH"][$arItem["ID"]]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"][0] = $arTmp;
					}
				} elseif($prop["MULTIPLE"] == "N" && !$boolArr) {
					if($prop["PROPERTY_TYPE"] == "L") {
						$arResult["SEARCH"][$arItem["ID"]]["SELECT_PROPS"][$pid] = $prop;
						$property_enums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "CODE" => $pid));
						while($enum_fields = $property_enums->GetNext()) {
							$arResult["SEARCH"][$arItem["ID"]]["SELECT_PROPS"][$pid]["DISPLAY_VALUE"][] = $enum_fields["VALUE"];
						}
					}
				}
			}
		}
		
		//MEASURE//
		if(!isset($arItem["CATALOG_MEASURE_RATIO"]))
			$arResult["SEARCH"][$arItem["ID"]]["CATALOG_MEASURE_RATIO"] = 1;
		
		$rsRatios = CCatalogMeasureRatio::getList(
			array(),
			array("PRODUCT_ID" => $arItem["ID"]),
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
			$arResult["SEARCH"][$arItem["ID"]]["CATALOG_MEASURE_RATIO"] = $mxRatio;
		}		

		if(!isset($arItem["CATALOG_MEASURE"]))
			$arItem["CATALOG_MEASURE"] = 0;
		$arItem["CATALOG_MEASURE"] = intval($arItem["CATALOG_MEASURE"]);
		if(0 > $arItem["CATALOG_MEASURE"])
			$arItem["CATALOG_MEASURE"] = 0;
		if(!isset($arItem["CATALOG_MEASURE_NAME"]))
			$arItem["CATALOG_MEASURE_NAME"] = "";
			
		if(0 < $arItem["CATALOG_MEASURE"]) {
			$rsMeasures = CCatalogMeasure::getList(
				array(),
				array("ID" => $arItem["CATALOG_MEASURE"]),
				false,
				false,
				array("ID", "SYMBOL_RUS")
			);
			if($arMeasure = $rsMeasures->GetNext()) {				
				$arItem["CATALOG_MEASURE_NAME"] = $arMeasure["SYMBOL_RUS"];
				$arResult["SEARCH"][$arItem["ID"]]["CATALOG_MEASURE_NAME"] = $arItem["CATALOG_MEASURE_NAME"];
			}
		}
		if("" == $arItem["CATALOG_MEASURE_NAME"]) {
			$arDefaultMeasure = CCatalogMeasure::getDefaultMeasure(true, true);
			$arItem["CATALOG_MEASURE_NAME"] = $arDefaultMeasure["SYMBOL_RUS"];
			$arResult["SEARCH"][$arItem["ID"]]["CATALOG_MEASURE_NAME"] = $arItem["CATALOG_MEASURE_NAME"];
		}

		$grab_price = CIBlockPriceTools::GetItemPrices($arItem["IBLOCK_ID"], $arr["PRICES"], $arItem, $arParams["PRICE_VAT_INCLUDE"], $arConvertParams);
		if(!empty($grab_price) && !$arResult["SEARCH"][$arItem["ID"]]["COLLECTION"]) {
			$arResult["SEARCH"][$arItem["ID"]]["MIN_PRICE"] = CIBlockPriceTools::getMinPriceFromList($grab_price);
		}
		
		$arResult["SEARCH"][$arItem["ID"]]["CAN_BUY"] = CIBlockPriceTools::CanBuy($arItem["IBLOCK_ID"], $arr["PRICES"], $arItem);

		//QUANTITY//
		$arResult["SEARCH"][$arItem["ID"]]["CATALOG_QUANTITY"] = $arItem["CATALOG_QUANTITY"];

		//CHECK_QUANTITY//
		$arResult["SEARCH"][$arItem["ID"]]["CHECK_QUANTITY"] = $arItem["CATALOG_QUANTITY_TRACE"] == "Y" && $arItem["CATALOG_CAN_BUY_ZERO"] == "N";
	}

	//OFFERS//
	$offersFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"]
	);
	
	$arOffers = CIBlockPriceTools::GetOffersArray(
		$offersFilter,
		array_keys($arResult["SEARCH"]),
		array(
			$arParams["OFFERS_SORT_FIELD"] => $arParams["OFFERS_SORT_ORDER"],
			$arParams["OFFERS_SORT_FIELD2"] => $arParams["OFFERS_SORT_ORDER2"],
		),
		$arParams["OFFERS_FIELD_CODE"],
		$arParams["OFFERS_PROPERTY_CODE"],
		$arParams["OFFERS_LIMIT"],
		$arr["PRICES"],
		$arParams["PRICE_VAT_INCLUDE"],
		$arConvertParams
	);
	if(!empty($arOffers)) {
		$arElementLink = array();
		foreach($arResult["SEARCH"] as $key => $arElement) {		
			$arElementLink[$arElement["ITEM_ID"]] = &$arResult["SEARCH"][$key];
			$arElementLink[$arElement["ITEM_ID"]]["OFFERS"] = array();
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
	foreach($arResult["SEARCH"] as $key => $arElement) {
		//USE_PRICE_RATIO//
		$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams['IBLOCK_ID'], $arParams['PRICE_CODE']);
		
		foreach ($arResultPrices as $value) {
			$arPriceTypeID[] = $value['ID'];
		}
		if (isset($value))
			unset($value);
		
		$arElement['PRICE_MATRIX'] = CatalogGetPriceTableEx($key, 0, $arPriceTypeID, 'Y');

		$discountPrice = array();
		if(count($arElement['PRICE_MATRIX']["COLS"]) > 1) {
			foreach($arElement['PRICE_MATRIX']["CAN_BUY"] as $key_can => $canBuy) {
				if(is_array($arElement['PRICE_MATRIX']["MATRIX"][$canBuy])) {
					$discountPrice[$key_can] = $arElement['PRICE_MATRIX']["MATRIX"][$canBuy][0]["DISCOUNT_PRICE"];
				}
			}
			if(!$inPriceRatio) {
				$arResult["SEARCH"][$key]["MIN_PRICE"]["DISCOUNT_VALUE"] = min($discountPrice);
			} else {
				$arResult["SEARCH"][$key]["MIN_PRICE"]["DISCOUNT_VALUE"] = min($discountPrice)*$arElement['CATALOG_MEASURE_RATIO'];
			}
		}
		//END_USE_PRICE_RATIO//
		
		//OFFERS//
		if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
			//TOTAL_OFFERS//			
			$totalDiscount = array();			
			
			$minPrice = false;	
			$minDiscount = false;			
			$minCurr = false;
			$minMeasureRatio = false;
			$minMeasure = false;
			$minCheckQnt = false;
			$minQnt = false;
			
			$arResult["SEARCH"][$key]["TOTAL_OFFERS"] = array();
			
			foreach($arElement["OFFERS"] as $key_off => $arOffer) {
				$totalOffer += $arOffer["CATALOG_QUANTITY"];
				if($totalOffer <= 0 || empty($totalOffer)) {
					unset($arResult["SEARCH"][$key]["OFFERS"]);
					
				}
				if($arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] == 0)
					continue;

				$totalDiscount[] = $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"];
				
				if($minDiscount === false || $minDiscount > $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"]) {
					$minPrice = $arOffer["MIN_PRICE"]["VALUE"];			
					$minDiscount = $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"];
					$minCurr = $arOffer["MIN_PRICE"]["CURRENCY"];			
					$minMeasureRatio = $arOffer["CATALOG_MEASURE_RATIO"];
					$minMeasure = $arOffer["CATALOG_MEASURE_NAME"];
					$minCheckQnt = $arOffer["CHECK_QUANTITY"];
					$minQnt = $arOffer["CATALOG_QUANTITY"];
				}		
			}
			
			if(count($totalDiscount) > 0) {
				$arResult["SEARCH"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(					
					"VALUE" => $minPrice,		
					"DISCOUNT_VALUE" => $minDiscount,					
					"CURRENCY" => $minCurr,		
					"CATALOG_MEASURE_RATIO" => $minMeasureRatio,
					"CATALOG_MEASURE_NAME" => $minMeasure,
					"CHECK_QUANTITY" => $minCheckQnt,
					"CATALOG_QUANTITY" => $minQnt,
				);
			} else {
				$arResult["SEARCH"][$key]["TOTAL_OFFERS"]["MIN_PRICE"] = array(
					"VALUE" => "0",
					"CURRENCY" => $arElement["OFFERS"][0]["MIN_PRICE"]["CURRENCY"],
					"CATALOG_MEASURE_RATIO" => $arElement["OFFERS"][0]["CATALOG_MEASURE_RATIO"],
					"CATALOG_MEASURE_NAME" => $arElement["OFFERS"][0]["CATALOG_MEASURE_NAME"]
				);			
			}			
			
			if(count(array_unique($totalDiscount)) > 1) {
				$arResult["SEARCH"][$key]["TOTAL_OFFERS"]["FROM"] = "Y";
			} else {
				$arResult["SEARCH"][$key]["TOTAL_OFFERS"]["FROM"] = "N";
			}
			//END_TOTAL_OFFERS//
		}
		//END_OFFERS//
	}
	//END_ELEMENTS//
}

if(empty($arActiveSections) && empty($arNewActiveItems)) {
	$arResult["CATEGORIES"] = array();
}?>