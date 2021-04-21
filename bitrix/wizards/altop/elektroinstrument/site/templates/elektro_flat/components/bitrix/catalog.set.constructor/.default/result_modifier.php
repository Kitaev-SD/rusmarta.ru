<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $arSetting;

if(!is_array($arSetting))
	$arSetting = $arParams["SETTING_PRODUCT"];

//USE_PRICE_RATIO//
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);

//CONVERT_CURRENCY//
$arCurrencyParams = array();
if($arParams["CONVERT_CURRENCY"] == "Y") {
	if(!CModule::IncludeModule("currency")) {
		$arParams["CONVERT_CURRENCY"] = "N";
		$arParams["CURRENCY_ID"] = "";
	} else {
		$arCurrencyInfo = CCurrency::GetByID($arParams["CURRENCY_ID"]);
		if(!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo))) {
			$arParams["CONVERT_CURRENCY"] = "N";
			$arParams["CURRENCY_ID"] = "";
		} else {
			$arParams["CURRENCY_ID"] = $arCurrencyInfo["CURRENCY"];
			$arCurrencyParams["CURRENCY_ID"] = $arCurrencyInfo["CURRENCY"];
		}
	}
}

//ELEMENT_PRICE//
//USE_PRICE_RATIO//
$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);
foreach($arResultPrices as $value) {
	$arPriceTypeID[] = $value["ID"];
}
if(isset($value))
	unset($value);

$arResult["ELEMENT"]["PRICE_MATRIX"] = CatalogGetPriceTableEx($arResult["ELEMENT"]["ID"], 0, $arPriceTypeID, "Y", $arCurrencyParams);

$price = array();
$discountPrice = array();
if(count($arResult["ELEMENT"]["PRICE_MATRIX"]["COLS"]) > 1) {
	foreach($arResult["ELEMENT"]["PRICE_MATRIX"]["CAN_BUY"] as $key_can => $canBuy) {
		if(is_array($arResult["ELEMENT"]["PRICE_MATRIX"]["MATRIX"][$canBuy])) {
			$price[$key_can] = $arResult["ELEMENT"]["PRICE_MATRIX"]["MATRIX"][$canBuy][0]["PRICE"];
			$discountPrice[$key_can] = $arResult["ELEMENT"]["PRICE_MATRIX"]["MATRIX"][$canBuy][0]["DISCOUNT_PRICE"];
		}
	}	
	
	$arResult["ELEMENT"]["PRICE_VALUE"] = min($price);
	$arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] = min($discountPrice);
	$arResult["ELEMENT"]["PRICE_PRINT_VALUE"] = CCurrencyLang::CurrencyFormat($arResult["ELEMENT"]["PRICE_VALUE"], $arResult["ELEMENT"]["PRICE_CURRENCY"], true);	
	$arResult["ELEMENT"]["PRICE_PRINT_DISCOUNT_VALUE"] = CCurrencyLang::CurrencyFormat($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], $arResult["ELEMENT"]["PRICE_CURRENCY"], true);	
}

if($inPriceRatio) {		
	$arResult["ELEMENT"]["PRICE_VALUE"] = $arResult["ELEMENT"]["PRICE_VALUE"] * $arResult["ELEMENT"]["MEASURE_RATIO"];
	$arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] = $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] * $arResult["ELEMENT"]["MEASURE_RATIO"];		
	$arResult["ELEMENT"]["PRICE_PRINT_VALUE"] = CCurrencyLang::CurrencyFormat($arResult["ELEMENT"]["PRICE_VALUE"], $arResult["ELEMENT"]["PRICE_CURRENCY"], true);	$arResult["ELEMENT"]["PRICE_PRINT_DISCOUNT_VALUE"] = CCurrencyLang::CurrencyFormat($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], $arResult["ELEMENT"]["PRICE_CURRENCY"], true);
}
//END_USE_PRICE_RATIO//
//END_ELEMENT_PRICE//

//ADDED_ITEMS_PRICE//
$arPrices = array();
$arMinPrice;

foreach($arResult["SET_ITEMS"]["DEFAULT"] as $key => $arItem) {
	$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);
	foreach($arResultPrices as $value) {
		$arPriceTypeID[] = $value["ID"];
	}
	if(isset($value))
		unset($value);
  
	$arItem["PRICE_MATRIX"] = CatalogGetPriceTableEx($arItem["ID"], 0, $arPriceTypeID, "Y", $arCurrencyParams);
	
	$price = array();
	$discountPrice = array();
	if(count($arItem["PRICE_MATRIX"]["COLS"]) > 1) {
		foreach($arItem["PRICE_MATRIX"]["CAN_BUY"] as $key_can => $canBuy) {
			if(is_array($arItem["PRICE_MATRIX"]["MATRIX"][$canBuy])) {
				$price[$key_can] = $arItem["PRICE_MATRIX"]["MATRIX"][$canBuy][0]["PRICE"];
				$discountPrice[$key_can] = $arItem["PRICE_MATRIX"]["MATRIX"][$canBuy][0]["DISCOUNT_PRICE"];
			}
		}
		$arResult["SET_ITEMS"]["DEFAULT"][$key]["PRICE_DISCOUNT_VALUE"] = min($discountPrice);
		$arResult["SET_ITEMS"]["DEFAULT"][$key]["PRICE_VALUE"] = min($price);
	}
	
	if(!$inPriceRatio) {
		$arResult["SET_ITEMS"]["DEFAULT"][$key]["BASKET_QUANTITY"] = $arItem["SET_QUANTITY"];
	}
}
//END_ADDED_ITEMS_PRICE//

//ELEMENT_PREVIEW_PICTURE//
if($arResult["ELEMENT"]["PREVIEW_PICTURE"] > 0) {
	$arFile = CFile::GetFileArray($arResult["ELEMENT"]["PREVIEW_PICTURE"]);
	if($arFile["WIDTH"] > 160 || $arFile["HEIGHT"] > 160) {
		$arFileTmp = CFile::ResizeImageGet(
			$arFile,
			array("width" => 160, "height" => 160),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["ELEMENT"]["PREVIEW_PICTURE"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"],
		);
	} else {
		$arResult["ELEMENT"]["PREVIEW_PICTURE"] = $arFile;
	}
} elseif($arResult["ELEMENT"]["DETAIL_PICTURE"] > 0) {
	$arFile = CFile::GetFileArray($arResult["ELEMENT"]["DETAIL_PICTURE"]);
	if($arFile["WIDTH"] > 160 || $arFile["HEIGHT"] > 160) {
		$arFileTmp = CFile::ResizeImageGet(
			$arFile,
			array("width" => 160, "height" => 160),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);	
		$arResult["ELEMENT"]["PREVIEW_PICTURE"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"],
		);
	} else {
		$arResult["ELEMENT"]["PREVIEW_PICTURE"] = $arFile;
	}
}

//SET_ITEMS_PREVIEW_IMG//
foreach(array("DEFAULT", "OTHER") as $type) {
	foreach($arResult["SET_ITEMS"][$type] as $key => $arItem) {		
		if($arItem["PREVIEW_PICTURE"] > 0) {
			$arFile = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
			if($arFile["WIDTH"] > 160 || $arFile["HEIGHT"] > 160) {
				$arFileTmp = CFile::ResizeImageGet(
					$arFile,
					array("width" => 160, "height" => 160),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arItem["PREVIEW_PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				$arItem["PREVIEW_PICTURE"] = $arFile;
			}
		} elseif($arItem["DETAIL_PICTURE"] > 0) {
			$arFile = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);
			if($arFile["WIDTH"] > 160 || $arFile["HEIGHT"] > 160) {
				$arFileTmp = CFile::ResizeImageGet(
					$arFile,
					array("width" => 160, "height" => 160),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arItem["PREVIEW_PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				$arItem["PREVIEW_PICTURE"] = $arFile;
			}
		}
		$arResult["SET_ITEMS"][$type][$key] = $arItem;		
	}
}

//SET_ITEMS_DEFAULT_NO_PRICE//
foreach($arResult["SET_ITEMS"]["DEFAULT"] as $key => $arItem) {
	if($arItem["PRICE_DISCOUNT_VALUE"] <= 0) {
		unset($arResult["SET_ITEMS"]["DEFAULT"][$key]);
		$arResult["SET_ITEMS"]["DEFAULT"][] = $arResult["SET_ITEMS"]["OTHER"][0];
		unset($arResult["SET_ITEMS"]["OTHER"][0]);
	}
}

//SET_ITEMS_PROPERTIES_SECTIONS//
if($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] > 0) {
	$arDefaultSetIDs[] = array(
		"ID" => $arResult["ELEMENT"]["ID"],
		"IBLOCK_ID" => $arResult["ELEMENT"]["IBLOCK_ID"]
	);
} else {
	$arDefaultSetIDs = array();
}
$arSetItems = array();

foreach(array("DEFAULT", "OTHER") as $type) {
	foreach($arResult["SET_ITEMS"][$type] as $key => $arItem) {
		if($type == "DEFAULT") {
			$arDefaultSetIDs[] = array(
				"ID" => $arItem["ID"],
				"IBLOCK_ID" => $arItem["IBLOCK_ID"]
			);
		}
		$arSetItemsIds[] = $arItem["ID"];
		
		$mxResult = CCatalogSku::GetProductInfo($arItem["ID"]);
		if(is_array($mxResult)) {
			$res = CIBlockElement::GetByID($mxResult["ID"]);
			if($ar_res = $res->GetNext()) {
				$arItem["IBLOCK_SECTION_ID"] = $ar_res["IBLOCK_SECTION_ID"];
				$arSetItems[$arItem["IBLOCK_SECTION_ID"]]["ITEMS"][$arItem["ID"]] = $arItem;
				$arResult["SET_ITEMS"][$type][$key]["IBLOCK_SECTION_ID"] = $arItem["IBLOCK_SECTION_ID"];
			}
		} else {
			$arSetItems[$arItem["IBLOCK_SECTION_ID"]]["ITEMS"][$arItem["ID"]] = $arItem;
		}
		
		//OFFERS_LINK_SHOW//
		if(in_array("OFFERS_LINK_SHOW", $arSetting["GENERAL_SETTINGS"]["VALUE"]) && is_array($mxResult)) {
			$arResult["SET_ITEMS"][$type][$key]["DETAIL_PAGE_URL"] .= "?offer=".$arItem["ID"];
		}
	}
}

$arResult["DEFAULT_SET_IDS"] = $arDefaultSetIDs;

//SET_ITEMS_PROPERTIES//
if(count($arSetItemsIds) > 0) {
	$rsElements = CIBlockElement::GetList(
		array(),
		array("=ID" => $arSetItemsIds),
		false,
		false,
		array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID")
	);	
	while($obElement = $rsElements->GetNextElement()) {	
		$arItem = $obElement->GetFields();			

		$arItem["PROPERTIES"] = $obElement->GetProperties();		

		$mxResult = CCatalogSku::GetProductInfo($arItem["ID"]);
		if(is_array($mxResult)) {
			foreach($arParams["OFFERS_CART_PROPERTIES"] as $pid) {
				if(!isset($arItem["PROPERTIES"][$pid]))
					continue;
				$prop = &$arItem["PROPERTIES"][$pid];
				$boolArr = is_array($prop["VALUE"]);
				if(($boolArr && !empty($prop["VALUE"])) || (!$boolArr && strlen($prop["VALUE"]) > 0)) {
					$arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "catalog_out");
				}
			}

			$res = CIBlockElement::GetByID($mxResult["ID"]);
			if($ar_res = $res->GetNext()) {
				$arSetItems[$ar_res["IBLOCK_SECTION_ID"]]["ITEMS"][$arItem["ID"]]["PROPERTIES"] = $arItem["PROPERTIES"];
				$arSetItems[$ar_res["IBLOCK_SECTION_ID"]]["ITEMS"][$arItem["ID"]]["DISPLAY_PROPERTIES"] = $arItem["DISPLAY_PROPERTIES"];
			}
		} else {
			$arSetItems[$arItem["IBLOCK_SECTION_ID"]]["ITEMS"][$arItem["ID"]]["PROPERTIES"] = $arItem["PROPERTIES"];
		}
		
		//OFFERS_LINK_SHOW//
		if(in_array("OFFERS_LINK_SHOW", $arSetting["GENERAL_SETTINGS"]["VALUE"]) && is_array($mxResult)) {
			$arSetItems[$ar_res["IBLOCK_SECTION_ID"]]["ITEMS"][$arItem["ID"]]["DETAIL_PAGE_URL"] .= "?offer=".$arItem["ID"];
		}
	}
}

//SET_ITEMS_SECTIONS//
$arSetSectIds = array_keys($arSetItems);
if(count($arSetSectIds) > 0) {
	$rsSections = CIBlockSection::GetList(
		array(),
		array(
			"=ID" => $arSetSectIds
		),
		false,
		array("ID", "IBLOCK_ID", "NAME")
	);
	while($arSection = $rsSections->GetNext()) {		
		if($arSetItems[$arSection["ID"]]) {
			$arSetItems[$arSection["ID"]]["ID"] = $arSection["ID"];
			$arSetItems[$arSection["ID"]]["NAME"] = $arSection["NAME"];
		}
	}
}

foreach($arResult["SET_ITEMS"]["DEFAULT"] as $key => $arItem) {				
	$arSetItem = $arSetItems[$arItem["IBLOCK_SECTION_ID"]]["ITEMS"][$arItem["ID"]];
	if($arSetItem) {
		if($arSetItem["PROPERTIES"])
			$arResult["SET_ITEMS"]["DEFAULT"][$key]["PROPERTIES"] = $arSetItem["PROPERTIES"];
		if($arSetItem["DISPLAY_PROPERTIES"])
			$arResult["SET_ITEMS"]["DEFAULT"][$key]["DISPLAY_PROPERTIES"] = $arSetItem["DISPLAY_PROPERTIES"];
		unset($arSetItems[$arItem["IBLOCK_SECTION_ID"]]["ITEMS"][$arItem["ID"]]);
	}
}

$arResult["SET_ITEMS"]["SECTIONS"] = $arSetItems;

//SECTIONS_ITEMS_PRICE//
foreach($arResult["SET_ITEMS"]["SECTIONS"] as $key => $arSection) {
	foreach($arSection["ITEMS"] as $keyItem => $arItem) {
		$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);
		foreach($arResultPrices as $value) {
			$arPriceTypeID[] = $value["ID"];
		}
		if(isset($value))
			unset($value);
	  
		$arItem["PRICE_MATRIX"] = CatalogGetPriceTableEx($arItem["ID"], 0, $arPriceTypeID, "Y", $arCurrencyParams);
		
		$price = array();
		$discountPrice = array();
		if(count($arItem["PRICE_MATRIX"]["COLS"]) > 1) {
			foreach($arItem["PRICE_MATRIX"]["CAN_BUY"] as $key_can => $canBuy) {
				if(is_array($arItem["PRICE_MATRIX"]["MATRIX"][$canBuy])) {
					$price[$key_can] = $arItem["PRICE_MATRIX"]["MATRIX"][$canBuy][0]["PRICE"];
					$discountPrice[$key_can] = $arItem["PRICE_MATRIX"]["MATRIX"][$canBuy][0]["DISCOUNT_PRICE"];
				}
			}
			$arResult["SET_ITEMS"]["SECTIONS"][$key]["ITEMS"][$keyItem]["PRICE_DISCOUNT_VALUE"] = min($discountPrice);
			$arResult["SET_ITEMS"]["SECTIONS"][$key]["ITEMS"][$keyItem]["PRICE_VALUE"] = min($price);
		}
		
		if(!$inPriceRatio) {
			$arResult["SET_ITEMS"]["SECTIONS"][$key]["ITEMS"][$keyItem]["BASKET_QUANTITY"] = $arItem["SET_QUANTITY"];
		}
	}
}
//END_OSECTIONS_ITEMS_PRICE//

//SET_ITEMS_PRICE//
$arResult["SET_ITEMS"]["PRICE_VALUE"] = 0;
$arResult["SET_ITEMS"]["OLD_PRICE_VALUE"] = 0;

foreach($arResult["SET_ITEMS"]["DEFAULT"] as $key => $arItem) {
	$arResult["SET_ITEMS"]["PRICE_VALUE"] += $arItem["PRICE_DISCOUNT_VALUE"] * $arItem["BASKET_QUANTITY"];
	$arResult["SET_ITEMS"]["OLD_PRICE_VALUE"] += $arItem["PRICE_VALUE"] * $arItem["BASKET_QUANTITY"];	
}

$arResult["SET_ITEMS"]["PRICE_VALUE"] = $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] + $arResult["SET_ITEMS"]["PRICE_VALUE"];
$arResult["SET_ITEMS"]["OLD_PRICE_VALUE"] = $arResult["ELEMENT"]["PRICE_VALUE"] + $arResult["SET_ITEMS"]["OLD_PRICE_VALUE"];
$arResult["SET_ITEMS"]["PRICE_CURRENCY"] = $arResult["ELEMENT"]["PRICE_CURRENCY"];?>