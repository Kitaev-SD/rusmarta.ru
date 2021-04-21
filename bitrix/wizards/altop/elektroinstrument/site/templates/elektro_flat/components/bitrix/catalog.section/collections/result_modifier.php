<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader,
	Bitrix\Catalog,
	Bitrix\Currency\CurrencyTable;

global $arSetting;

//USE_PRICE_RATIO//
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);

//COLLECTION//
$arValue = array();
$arValueAll = array();
foreach($arResult["ITEMS"] as $arItemSection) {
	$arItems = CIBlockElement::GetList(array("SORT" => "ID"),array("PROPERTY_COLLECTION" => $arItemSection["ID"]),false,false,array("ID"));
	while($arItem = $arItems->GetNext()) {
		if(!empty($arItem["ID"]))
			$arValue[$arItemSection["ID"]][] = $arItem["ID"];
			$arValueAll[] = $arItem["ID"];
	}
}
if(!empty($arValue)){
	$arResult["COLLECTION"]["THIS"] = true;
	$arResult["COLLECTION"]["VALUE"] = $arValue;
	$arResult["COLLECTION"]["VALUE_ALL"] = $arValueAll; 
}

//ITEMS_COLLECTION//
$arConvertParams = array();
if($arParams["CONVERT_CURRENCY"] == "Y") {
	if(!Loader::includeModule("currency")) {
		$arParams["CONVERT_CURRENCY"] = "N";
		$arParams["CURRENCY_ID"] = "";
	} else {
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

$arSelect = array("ID", "IBLOCK_ID");
	
$arr["PRICES"] = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);
foreach($arr["PRICES"] as $key => $value) {
	if(!$value["CAN_VIEW"] && !$value["CAN_BUY"])
		continue;
	$arSelect[] = $value["SELECT"];
}

$ratioResult = Catalog\ProductTable::getCurrentRatioWithMeasure($arResult["COLLECTION"]["VALUE_ALL"],1);
$itemsList = array();
$ratioItem = array();
if(isset($arResult["COLLECTION"]["VALUE"]) && is_array($arResult["COLLECTION"]["VALUE"])) foreach($arResult["COLLECTION"]["VALUE"] as $key => $arItem) {
	foreach($arItem as $itemID) {
		$itemsIterator = CIBlockElement::GetList(
			array(),
			array("ID" => $itemID, "ACTIVE" => "Y"),
			false,
			false,
			$arSelect
		);

		while($item = $itemsIterator->GetNext()) {
			$itemsList[$key][$item["ID"]] = $item;
			$ratioItem[$key][$item["ID"]] = $ratioResult[$item["ID"]]["RATIO"];
		}
	}
}

$arSumPrice = array();
foreach($itemsList as $key => $item) {
	foreach($item as $sectionItem) {
		$priceList = CIBlockPriceTools::GetItemPrices(
			$sectionItem["IBLOCK_ID"],
			$arr["PRICES"],
			$sectionItem,
			$arParams["PRICE_VAT_INCLUDE"],
			$arConvertParams
		);
		if(is_array($priceList) && !empty($priceList))
			foreach($priceList as $price) {
				if($price["MIN_PRICE"] == "Y" && $price["DISCOUNT_VALUE"] > 0) {
					if($inPriceRatio)
						$arSumPrice[$key][] = $price["DISCOUNT_VALUE"]*$ratioItem[$key][$sectionItem["ID"]];
					else
						$arSumPrice[$key][] = $price["DISCOUNT_VALUE"];
				}
			}
		else {
			$arOffers = CIBlockPriceTools::GetOffersArray(
				$sectionItem['IBLOCK_ID'],
				$sectionItem["ID"],
				array("SORT"=>"ASC"),
				array(),
				array(),
				0,
				$arr["PRICES"],
				$arParams["PRICE_VAT_INCLUDE"],
				$arConvertParams
			);

			foreach($arOffers as $offer) {
				$ratioResultOffer = Catalog\ProductTable::getCurrentRatioWithMeasure($offer["ID"],1);
				foreach($offer["PRICES"] as $key_p => $price) {
					if($price["MIN_PRICE"] == "Y" && $price["DISCOUNT_VALUE"] > 0) {
						if($inPriceRatio)
							$arSumPrice[$key][] = $price["DISCOUNT_VALUE"]*$ratioResultOffer[$offer["ID"]]["RATIO"];
						else
							$arSumPrice[$key][] = $price["DISCOUNT_VALUE"];
					}
				}
			}
		}
	}
}

foreach($arResult["ITEMS"] as $key => $arElement) {
	$priceFormat = CCurrencyLang::GetCurrencyFormat($price["CURRENCY"], LANGUAGE_ID);
	if(empty($priceFormat["THOUSANDS_SEP"])):
		$priceFormat["THOUSANDS_SEP"] = " ";
	endif;					
	if($priceFormat["HIDE_ZERO"] == "Y"):
		if(round(min($arSumPrice[$key]), $priceFormat["DECIMALS"]) == round(min($arSumPrice[$key]), 0)):
			$priceFormat["DECIMALS"] = 0;
		endif;
	endif;
	$currency = str_replace("# ", " ", $priceFormat["FORMAT_STRING"]);

	foreach($arElement["ITEM_PRICES"] as $keyPrice => $itemPrice) {
		$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_PRICE"] = number_format(min($arSumPrice[$arElement["ID"]]),$priceFormat["DECIMALS"],$priceFormat["DEC_POINT"],$priceFormat["THOUSANDS_SEP"])." ".$currency;
		$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_PRICE"] = min($arSumPrice[$arElement["ID"]]);
		$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["BASE_PRICE"] = min($arSumPrice[$arElement["ID"]]);
		$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["UNROUND_PRICE"] = min($arSumPrice[$arElement["ID"]]);
		$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRICE"] = min($arSumPrice[$arElement["ID"]]);
		$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["DISCOUNT"] = 0;
		$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PERCENT"] = 0;
		$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_BASE_PRICE"] = number_format(min($arSumPrice[$arElement["ID"]]),$priceFormat["DECIMALS"],$priceFormat["DEC_POINT"],$priceFormat["THOUSANDS_SEP"])." ".$currency;;
		$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_PRICE"] = number_format(min($arSumPrice[$arElement["ID"]]),$priceFormat["DECIMALS"],$priceFormat["DEC_POINT"],$priceFormat["THOUSANDS_SEP"])." ".$currency;;
		$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_DISCOUNT"] = 0;
	}
}

//USE_PRICE_RATIO//
if(!$inPriceRatio) {
	foreach($arResult["ITEMS"] as $key => $arElement) {	
		foreach($arElement["ITEM_PRICES"] as $keyPrice => $itemPrice) {
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_BASE_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["BASE_PRICE"];
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_BASE_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["PRINT_BASE_PRICE"];
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["PRICE"];
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_PRICE"] = $arElement["ITEM_PRICES"][$keyPrice]["PRINT_PRICE"];
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["RATIO_DISCOUNT"] = $arElement["ITEM_PRICES"][$keyPrice]["DISCOUNT"];
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_DISCOUNT"] = $arElement["ITEM_PRICES"][$keyPrice]["PRINT_DISCOUNT"];
		}
	}
} else {
	foreach($arResult["ITEMS"] as $key => $arElement) {	
		foreach($arElement["ITEM_PRICES"] as $keyPrice => $itemPrice) {
			$arResult["ITEMS"][$key]["ITEM_PRICES"][$keyPrice]["PRICE"] =  $arElement["CATALOG_MEASURE_RATIO"]*$arElement["ITEM_PRICES"][$keyPrice]["PRICE"];
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
	//CURRENT_DISCOUNT//
	$arPrice = array();
	$arResult["ITEMS"][$key]["CURRENT_DISCOUNT"] = array();	

	$arDiscounts = CCatalogDiscount::GetDiscountByProduct($arElement["ID"], $USER->GetUserGroupArray(), "N", array(), SITE_ID);
	$arResult["ITEMS"][$key]["CURRENT_DISCOUNT"] = current($arDiscounts);
	
	//PREVIEW_PICTURE//	
	if(is_array($arElement["DETAIL_PICTURE"])) {
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

	//VERSIONS_PERFORMANCE//
	if(!empty($arElement["PROPERTIES"]["VERSIONS_PERFORMANCE"]["VALUE"])) {
		$obElColorCollection = CIBlockElement::GetList(
			array("SORT" => "ASC"),
			array(
				"ID" => $arElement["PROPERTIES"]["VERSIONS_PERFORMANCE"]["VALUE"],
				"ACTIVE" => "Y",
				"IBLOCK_ID" => $arElement["PROPERTIES"]["VERSIONS_PERFORMANCE"]["LINK_IBLOCK_ID"]
			),
			false,
			false,
			array("ID", "CODE", "NAME", "PROPERTY_HEX", "PROPERTY_PICT")
		);
		
		while($arElColorCollection = $obElColorCollection->GetNext()) {
			$arResult["ITEMS"][$key]["VERSIONS_PERFORMANCE"]["ITEMS"][$arElColorCollection["ID"]] = $arElColorCollection;
			
			if($arElColorCollection["PROPERTY_PICT_VALUE"] > 0) {
				$arFile = CFile::GetFileArray($arElColorCollection["PROPERTY_PICT_VALUE"]);
				if($arFile["WIDTH"] > 24 || $arFile["HEIGHT"] > 24) {
					$arFileTmp = CFile::ResizeImageGet(
						$arFile,
						array("width" => 24, "height" => 24),
						BX_RESIZE_IMAGE_EXACT,
						true
					);
					$arResult["ITEMS"][$key]["VERSIONS_PERFORMANCE"]["ITEMS"][$arElColorCollection["ID"]]["PICTURE"] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"],
					);
				} else {
					$arResult["ITEMS"][$key]["VERSIONS_PERFORMANCE"]["ITEMS"][$arElColorCollection["ID"]]["PICTURE"] = $arFile;
				}
			}
		}
	}
	
	//MIN_PRICE//
	if(count($arElement["ITEM_QUANTITY_RANGES"]) > 1) {
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