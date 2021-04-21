<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader,
	Bitrix\Main\Type\Collection,
	Bitrix\Iblock,
	Bitrix\Catalog,
	Bitrix\Currency\CurrencyTable;

global $USER, $arSetting;

//USE_PRICE_RATIO//
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);

//STR_MAIN_ID//
$arResult["STR_MAIN_ID"] = $this->GetEditAreaId($arResult["ID"]);

//USE_CAPTCHA//
$arResult["USE_CAPTCHA"] = !$USER->IsAuthorized() && $arSetting["FORMS_USE_CAPTCHA"]["VALUE"] == "Y" ? "Y" : "N";

//KIT_ITEMS//
if(CCatalogProductSet::isProductInSet($arResult["ID"])) {
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

	$currentKit = false;
	$productLink = array();
	$allKits = CCatalogProductSet::getAllSetsByProduct($arResult["ID"], CCatalogProductSet::TYPE_SET);	
	if(isset($allKits) && is_array($allKits)) foreach($allKits as $oneKit) {
		if($oneKit["ACTIVE"] == "Y") {
			$currentKit = $oneKit;
			break;
		}
	}
	unset($oneKit, $allKits);
	
	if(!empty($currentKit)) {
		Collection::sortByColumn($currentKit["ITEMS"], array("SORT" => SORT_ASC));
		
		$arKitItemsID = array();
		foreach($currentKit["ITEMS"] as $index => $item) {			
			$arKitItemsID[] = $item["ITEM_ID"];
			$productLink[$item["ITEM_ID"]][] = $index;
		}
		unset($index, $item);
		
		$arSelect = array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PREVIEW_PICTURE", "DETAIL_PICTURE");
		
		$arr["PRICES"] = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);
		foreach($arr["PRICES"] as $key => $value) {
			if(!$value["CAN_VIEW"] && !$value["CAN_BUY"])
				continue;
			$arSelect[] = $value["SELECT"];
		}

		$arr["ITEMS_RATIO"] = array_fill_keys($arKitItemsID, 1);
		$ratioResult = Catalog\ProductTable::getCurrentRatioWithMeasure($arKitItemsID);
		foreach($ratioResult as $ratioProduct => $ratioData)
			$arr["ITEMS_RATIO"][$ratioProduct] = $ratioData["RATIO"];
		unset($ratioProduct, $ratioData);
		
		$itemsList = array();	
		$itemsIterator = CIBlockElement::GetList(
			array(),
			array("ID" => $arKitItemsID, "ACTIVE" => "Y"),
			false,
			false,
			$arSelect
		);
		while($item = $itemsIterator->GetNext()) {
			if($item["PREVIEW_PICTURE"]) {
				$arFile = CFile::GetFileArray($item["PREVIEW_PICTURE"]);
				if($arFile["WIDTH"] > 160 || $arFile["HEIGHT"] > 160) {
					$arFileTmp = CFile::ResizeImageGet(
						$arFile,
						array("width" => 160, "height" => 160),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$item["PREVIEW_PICTURE"] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"],
					);	
				} else {
					$item["PREVIEW_PICTURE"] = $arFile;
				}
			} elseif($item["DETAIL_PICTURE"]) {
				$arFile = CFile::GetFileArray($item["DETAIL_PICTURE"]);
				if($arFile["WIDTH"] > 160 || $arFile["HEIGHT"] > 160) {
					$arFileTmp = CFile::ResizeImageGet(
						$arFile,
						array("width" => 160, "height" => 160),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$item["PREVIEW_PICTURE"] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"],
					);
				} else {
					$item["PREVIEW_PICTURE"] = $arFile;
				}
			}		
			$itemsList[$item["ID"]] = $item;
		}
		unset($arSelect, $item, $itemsIterator);
		
		foreach($itemsList as $item) {		
			$priceList = CIBlockPriceTools::GetItemPrices(
				$item["IBLOCK_ID"],
				$arr["PRICES"],
				$item,
				$arParams["PRICE_VAT_INCLUDE"],
				$arConvertParams
			);
			
			if(empty($priceList))
				continue;
			
			foreach($priceList as $price) {
				if($price["MIN_PRICE"] == "Y") {
					$item["PRICE_CURRENCY"] = $price["CURRENCY"];
					$item["PRICE_DISCOUNT_VALUE"] = $price["DISCOUNT_VALUE"];
					$item["PRICE_PRINT_DISCOUNT_VALUE"] = $price["PRINT_DISCOUNT_VALUE"];
					$item["PRICE_VALUE"] = $price["VALUE"];
					$item["PRICE_PRINT_VALUE"] = $price["PRINT_VALUE"];
					$item["PRICE_DISCOUNT_DIFFERENCE_VALUE"] = $price["DISCOUNT_DIFF"];
					$item["PRICE_DISCOUNT_DIFFERENCE"] = $price["PRINT_DISCOUNT_DIFF"];
					$item["PRICE_DISCOUNT_PERCENT"] = $price["DISCOUNT_DIFF_PERCENT"];
					break;
				}
			}
			unset($price, $priceList);
			
			if(!empty($productLink[$item["ID"]])) {
				foreach($productLink[$item["ID"]] as $index)
					$currentKit["ITEMS"][$index]["ITEM_DATA"] = $item;
				unset($index);
			}
		}
		unset($item, $itemsList);
		
		$defaultMeasure = CCatalogMeasure::getDefaultMeasure(true, true);

		foreach($currentKit["ITEMS"] as $kitItem) {
			if(!isset($kitItem["ITEM_DATA"]))
				continue;
			$kitItem["ITEM_DATA"]["SET_QUANTITY"] = (empty($kitItem["QUANTITY"]) ? 1 : $kitItem["QUANTITY"]);
			$kitItem["ITEM_DATA"]["MEASURE_RATIO"] = $arr["ITEMS_RATIO"][$kitItem["ITEM_DATA"]["ID"]];
			$kitItem["ITEM_DATA"]["MEASURE"] = (!empty($ratioResult[$kitItem["ITEM_DATA"]["ID"]]["MEASURE"]) ? $ratioResult[$kitItem["ITEM_DATA"]["ID"]]["MEASURE"] : $defaultMeasure);
			$kitItem["ITEM_DATA"]["BASKET_QUANTITY"] = $kitItem["ITEM_DATA"]["SET_QUANTITY"] * $kitItem["ITEM_DATA"]["MEASURE_RATIO"];
			
			$arResult["KIT_ITEMS"][] = $kitItem["ITEM_DATA"];
		}
		unset($kitItem, $currentKit);
	}
}

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
			}
		}
	} else {
		foreach($arResult["ITEM_PRICES"] as $keyPrice => $itemPrice) {
			$arResult["ITEM_PRICES"][$keyPrice]["RATIO_BASE_PRICE"] = $arResult["ITEM_PRICES"][$keyPrice]["BASE_PRICE"];
			$arResult["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_BASE_PRICE"] = $arResult["ITEM_PRICES"][$keyPrice]["PRINT_BASE_PRICE"];
			$arResult["ITEM_PRICES"][$keyPrice]["RATIO_PRICE"] = $arResult["ITEM_PRICES"][$keyPrice]["PRICE"];
			$arResult["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_PRICE"] = $arResult["ITEM_PRICES"][$keyPrice]["PRINT_PRICE"];
			$arResult["ITEM_PRICES"][$keyPrice]["RATIO_DISCOUNT"] = $arResult["ITEM_PRICES"][$keyPrice]["DISCOUNT"];
			$arResult["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_DISCOUNT"] = $arResult["ITEM_PRICES"][$keyPrice]["PRINT_DISCOUNT"];
		}
	}
}
foreach($arResult["ITEM_PRICES"] as $keyPrice => $itemPrice) {
	$arResult["ITEM_PRICES"][$keyPrice]["MIN_QUANTITY"] =  $arResult["CATALOG_MEASURE_RATIO"];
}
//END_USE_PRICE_RATIO//

//PRICE_MATRIX//
$arPriceMatrix = false;
$arPriceMatrix = $arResult["PRICE_MATRIX"]["MATRIX"];
foreach($arPriceMatrix as $key => $item) {
	foreach($item as $key2 => $item2) {
		$arPriceMatrix[$key][$key2]["QUANTITY_FROM"] = $arResult["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_FROM"];
		$arPriceMatrix[$key][$key2]["QUANTITY_TO"] = ($arResult["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"] != 0? $arResult["PRICE_MATRIX"]["ROWS"][$key2]["QUANTITY_TO"]: INF);
		$arCurFormat = CCurrencyLang::GetCurrencyFormat($arPriceMatrix[$key][$key2]["CURRENCY"], LANGUAGE_ID);
		$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
		$arPriceMatrix[$key][$key2]["PRINT_CURRENCY"] = $currency;
		if($inPriceRatio) {
			$arPriceMatrix[$key][$key2]["DISCOUNT_PRICE"] = $arResult["CATALOG_MEASURE_RATIO"]*$arResult["PRICE_MATRIX"]["MATRIX"][$key][$key2]["DISCOUNT_PRICE"];
		}
	}
}

//PRICE_MATRIX_KIT//
foreach($arResult["KIT_ITEMS"] as $key => $kitItem) {
	$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams['IBLOCK_ID'], $arParams['PRICE_CODE']);
	foreach ($arResultPrices as $value) {
		$arPriceTypeID[] = $value['ID'];
	}
	if (isset($value))
		unset($value);
	
	
	$kitItem['PRICE_MATRIX'] = CatalogGetPriceTableEx($kitItem['ID'], 0, $arPriceTypeID, 'Y');
	
	$price = array();
	$discountPrice = array();
	if(count($kitItem['PRICE_MATRIX']["COLS"]) > 1) {
		foreach($kitItem['PRICE_MATRIX']["CAN_BUY"] as $key_can => $canBuy) {
			if(is_array($kitItem['PRICE_MATRIX']["MATRIX"][$canBuy])) {
				$price[$key_can] = $kitItem['PRICE_MATRIX']["MATRIX"][$canBuy][0]["PRICE"];
				$discountPrice[$key_can] = $kitItem['PRICE_MATRIX']["MATRIX"][$canBuy][0]["DISCOUNT_PRICE"];
			}
		}
		$arResult["KIT_ITEMS"][$key]["PRICE_DISCOUNT_VALUE"] = min($discountPrice);
		$arResult["KIT_ITEMS"][$key]["PRICE_VALUE"] = min($price);
	}
	
	if(!$inPriceRatio) {
		$arResult["KIT_ITEMS"][$key]["BASKET_QUANTITY"] = $kitItem["SET_QUANTITY"];
	}
}

//CURRENT_DISCOUNT//
$arPrice = array();
$arResult["CURRENT_DISCOUNT"] = array();

if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
	$minId = false;
	$minRatioPrice = false;
	foreach($arResult["OFFERS"] as $key_off => $arOffer) {
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
		$arResult["CURRENT_DISCOUNT"] = current($arDiscounts);
	}
} else {
	$arDiscounts = CCatalogDiscount::GetDiscountByProduct($arResult["ID"], $USER->GetUserGroupArray(), "N", array(), SITE_ID);
	$arResult["CURRENT_DISCOUNT"] = current($arDiscounts);
}

//WATERMARK//
$arWaterMark = Array();
$detail_picture = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "FIELDS");
$detail_picture = $detail_picture["DETAIL_PICTURE"]["DEFAULT_VALUE"];

if($detail_picture["USE_WATERMARK_FILE"] == "Y") {
	$arWaterMark[] = Array(
		"name" => "watermark",
		"position" => $detail_picture["WATERMARK_FILE_POSITION"] ? $detail_picture["WATERMARK_FILE_POSITION"] : "center",
		"size" => "real",
		"type" => "image",
		"alpha_level" => $detail_picture["WATERMARK_FILE_ALPHA"] && $detail_picture["WATERMARK_FILE_ALPHA"] <= 100 ? 100 - $detail_picture["WATERMARK_FILE_ALPHA"] : 0,
		"file" => $_SERVER["DOCUMENT_ROOT"].$detail_picture["WATERMARK_FILE"],
		"fill" => "exact"
	);
}

if($detail_picture["USE_WATERMARK_TEXT"] == "Y") {
	$arWaterMark[] = Array(
		"name" => "watermark",
		"position" => $detail_picture["WATERMARK_TEXT_POSITION"] ? $detail_picture["WATERMARK_TEXT_POSITION"] : "center",
		"size" => "medium",
		"coefficient" => $detail_picture["WATERMARK_TEXT_SIZE"] ? $detail_picture["WATERMARK_TEXT_SIZE"] : 100,
		"type" => "text",
		"text" => $detail_picture["WATERMARK_TEXT"] ? $detail_picture["WATERMARK_TEXT"] : SITE_SERVER_NAME,
		"color" => $detail_picture["WATERMARK_TEXT_COLOR"] ? $detail_picture["WATERMARK_TEXT_COLOR"] : "000000",
		"font" => $detail_picture["WATERMARK_TEXT_FONT"] ? $_SERVER["DOCUMENT_ROOT"].$detail_picture["WATERMARK_TEXT_FONT"] : $_SERVER["DOCUMENT_ROOT"]."/bitrix/fonts/pt_sans-bold.ttf"
	);
}


//DETAIL_PREVIEW_IMG//
if(is_array($arResult["DETAIL_PICTURE"])) {
	//DETAIL_IMG//
	if($arResult["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_DETAIL_IMG_WIDTH"] || $arResult["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_DETAIL_IMG_HEIGHT"]) {
		$arFileTmp = CFile::ResizeImageGet(
			$arResult["DETAIL_PICTURE"],
			array("width" => $arParams["DISPLAY_DETAIL_IMG_WIDTH"], "height" => $arParams["DISPLAY_DETAIL_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["DETAIL_IMG"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"],
		);
	} else {
		$arResult["DETAIL_IMG"] = $arResult["DETAIL_PICTURE"];
	}

	//PREVIEW_IMG//
	if($arResult["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arResult["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
		$arFileTmp = CFile::ResizeImageGet(
			$arResult["DETAIL_PICTURE"],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["PREVIEW_IMG"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"],
		);
	} else {
		$arResult["PREVIEW_IMG"] = $arResult["DETAIL_PICTURE"];
	}
}

//MORE_PICTURES_ALL//
if(is_array($arResult["MORE_PHOTO"]) && count($arResult["MORE_PHOTO"]) > 0) {
	unset($arResult["DISPLAY_PROPERTIES"]["MORE_PHOTO"]);

	//MORE_PICTURES//
	foreach($arResult["MORE_PHOTO"] as $key => $arFile) {
		//MORE_PICTURES_WATERMARK//
		if(!empty($arWaterMark)) {
			$arFileTmp = CFile::ResizeImageGet(
				$arFile,
				array("width" => 10000, "height" => 10000),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true,
				$arWaterMark
			);
			$arResult["MORE_PHOTO"][$key] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
			);
		}

		//MORE_PICTURES_PREVIEW//
		$arFileTmp = CFile::ResizeImageGet(
			$arFile,
			array("width" => $arParams["DISPLAY_MORE_PHOTO_WIDTH"] ? $arParams["DISPLAY_MORE_PHOTO_WIDTH"] : 86, "height" => $arParams["DISPLAY_MORE_PHOTO_HEIGHT"] ? $arParams["DISPLAY_MORE_PHOTO_HEIGHT"] : 86),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["MORE_PHOTO"][$key]["PREVIEW"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"],
		);
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

//GIFT//
if(!empty($arResult["PROPERTIES"]["GIFT"]["VALUE"])) {	
	$dbElement = CIBlockElement::GetList(
		array(), 
		array("IBLOCK_ID" => $arResult["PROPERTIES"]["GIFT"]["LINK_IBLOCK_ID"], "ID" => $arResult["PROPERTIES"]["GIFT"]["VALUE"], "ACTIVE" => "Y"), 
		false, 
		false, 
		array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE")
	);
	while($arEl = $dbElement->GetNext()) {
		$arResult["PROPERTIES"]["GIFT"]["FULL_VALUE"][$arEl["ID"]]["NAME"] = $arEl["NAME"];

		//PREVIEW_PICTURE//
		if($arEl["PREVIEW_PICTURE"] > 0) {
			$arFile = CFile::GetFileArray($arEl["PREVIEW_PICTURE"]);
			if($arFile["WIDTH"] > 70 || $arFile["HEIGHT"] > 70) {
				$arFileTmp = CFile::ResizeImageGet(
					$arFile,
					array("width" => 70, "height" => 70),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arResult["PROPERTIES"]["GIFT"]["FULL_VALUE"][$arEl["ID"]]["PREVIEW_PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				$arResult["PROPERTIES"]["GIFT"]["FULL_VALUE"][$arEl["ID"]]["PREVIEW_PICTURE"] = $arFile;
			}
		}
	}
}

//ADVANTAGES//
$rsSections = CIBlockSection::GetList(
	array(),
	array("ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y", "ID" => $arResult["IBLOCK_SECTION_ID"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]),
	false,
	array("ID", "UF_ADVANTAGES")
);
if($arSection = $rsSections->Fetch()) {
	$arResult["ADVANTAGES"] = $arSection["UF_ADVANTAGES"];
}

//FILES_DOCS//
if(!empty($arResult["PROPERTIES"]["FILES_DOCS"]["VALUE"])) {
	foreach($arResult["PROPERTIES"]["FILES_DOCS"]["VALUE"] as $key => $arDocId) {
		$arDocFile = CFile::GetFileArray($arDocId);
		
		$fileTypePos = strrpos($arDocFile["FILE_NAME"], ".");		
		$fileType = substr($arDocFile["FILE_NAME"], $fileTypePos + 1);
		$fileTypeFull = substr($arDocFile["FILE_NAME"], $fileTypePos);
		
		$fileName = str_replace($fileTypeFull, "", $arDocFile["ORIGINAL_NAME"]);		
		
		$fileSize = $arDocFile["FILE_SIZE"];
		$metrics = array(
			0 => GetMessage("CATALOG_ELEMENT_SIZE_B"),
			1 => GetMessage("CATALOG_ELEMENT_SIZE_KB"),
			2 => GetMessage("CATALOG_ELEMENT_SIZE_MB"),
			3 => GetMessage("CATALOG_ELEMENT_SIZE_GB")
		);
		$metric = 0;
		while(floor($fileSize / 1024) > 0) {
			$metric ++;
			$fileSize /= 1024;
		}
		$fileSizeFormat = round($fileSize, 1)." ".$metrics[$metric];

		$arResult["PROPERTIES"]["FILES_DOCS"]["FULL_VALUE"][] = array(
			"NAME" => $fileName,
			"DESCRIPTION" => $arDocFile["DESCRIPTION"],
			"TYPE" => $fileType,
			"SIZE" => $fileSizeFormat,
			"SRC" => $arDocFile["SRC"]			
		);
	}
}

//PROPERTIES_FILTER_HINT//
if(!empty($arResult["DISPLAY_PROPERTIES"])) {
	$rsProperty = CIBlockSectionPropertyLink::GetArray($arParams["IBLOCK_ID"],$arResult["IBLOCK_SECTION_ID"]);
	foreach($arResult["DISPLAY_PROPERTIES"] as $code => $arProp) {
		if("F" == $arProp["PROPERTY_TYPE"]) {
			unset($arResult["DISPLAY_PROPERTIES"][$code]);
		} else {
			$arResult["DISPLAY_PROPERTIES"][$code]["FILTER_HINT"] = $rsProperty[$arProp["ID"]]["FILTER_HINT"];			
		}
	}
}

//OFFERS_PROPERTIES_FILTER_HINT//
$mxResult = CCatalogSKU::GetInfoByProductIBlock($arParams["IBLOCK_ID"]);
if(is_array($mxResult)) {
	$rsProperty = CIBlockSectionPropertyLink::GetArray($mxResult["IBLOCK_ID"],$arResult["IBLOCK_SECTION_ID"]);
	if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
		foreach($arResult["OFFERS"] as $key_off => $arOffer) {
			foreach($arOffer["DISPLAY_PROPERTIES"] as $code => $arProp) {
				$filterHint = $rsProperty[$arProp["ID"]]["FILTER_HINT"];
				$arResult["OFFERS"][$key_off]["DISPLAY_PROPERTIES"][$code]["FILTER_HINT"] = $filterHint;
				if($arProp["PROPERTY_TYPE"] == "S") {							
					$arResult["OFFERS"][$key_off]["DISPLAY_S_PROPERTIES"][$code]["NAME"] = $arProp["NAME"];
					$arResult["OFFERS"][$key_off]["DISPLAY_S_PROPERTIES"][$code]["FILTER_HINT"] = $filterHint;
					$arResult["OFFERS"][$key_off]["DISPLAY_S_PROPERTIES"][$code]["VALUE"] = $arProp["VALUE"];
				}
			}
		}
	}
}

//MIN_PRICE//
$arResult["MIN_PRICE"] = $arResult["ITEM_PRICES"][$arResult["ITEM_PRICE_SELECTED"]];

//CHECK_QUANTITY//
$arResult["CHECK_QUANTITY"] = $arResult["CATALOG_QUANTITY_TRACE"] == "Y" && $arResult["CATALOG_CAN_BUY_ZERO"] == "N";

//SELECT_PROPS//
if(is_array($arParams["PROPERTY_CODE_MOD"]) && !empty($arParams["PROPERTY_CODE_MOD"])) {
	$arResult["SELECT_PROPS"] = array();
	foreach($arParams["PROPERTY_CODE_MOD"] as $pid) {
		if(!isset($arResult["PROPERTIES"][$pid]))
			continue;
		$prop = &$arResult["PROPERTIES"][$pid];
		$boolArr = is_array($prop["VALUE"]);
		if($prop["MULTIPLE"] == "Y" && $boolArr && !empty($prop["VALUE"])) {
			$arResult["SELECT_PROPS"][$pid] = CIBlockFormatProperties::GetDisplayValue($arResult, $prop, "catalog_out");
			if(!is_array($arResult["SELECT_PROPS"][$pid]["DISPLAY_VALUE"]) && !empty($arResult["SELECT_PROPS"][$pid]["DISPLAY_VALUE"])) {
				$arTmp = $arResult["SELECT_PROPS"][$pid]["DISPLAY_VALUE"];
				unset($arResult["SELECT_PROPS"][$pid]["DISPLAY_VALUE"]);
				$arResult["SELECT_PROPS"][$pid]["DISPLAY_VALUE"][0] = $arTmp;
			}
		} elseif($prop["MULTIPLE"] == "N" && !$boolArr) {
			if($prop["PROPERTY_TYPE"] == "L") {
				$arResult["SELECT_PROPS"][$pid] = $prop;
				$property_enums = CIBlockPropertyEnum::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "CODE" => $pid));
				while($enum_fields = $property_enums->GetNext()) {
					$arResult["SELECT_PROPS"][$pid]["DISPLAY_VALUE"][] = $enum_fields["VALUE"];
				}
			}
		}
	}
}

//OFFERS//
if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
	//TOTAL_OFFERS//	
	$totalQnt = false;
	$minPrice = false;
	$totalPrices = false;		
	foreach($arResult["OFFERS"] as $key_off => $arOffer) {				
		$totalQnt += $arOffer["CATALOG_QUANTITY"];				
		foreach($arOffer["ITEM_PRICES"] as $itemPrice) {
			if($itemPrice["RATIO_PRICE"] == 0)
				continue;						
			if($minPrice === false || $minPrice > $itemPrice["RATIO_PRICE"]) {							
				$minPrice = $itemPrice["RATIO_PRICE"];
				$arResult["TOTAL_OFFERS"]["MIN_PRICE"] = array(		
					"RATIO_BASE_PRICE" => $itemPrice["RATIO_BASE_PRICE"],
					"PRINT_RATIO_BASE_PRICE" => $itemPrice["PRINT_RATIO_BASE_PRICE"],
					"RATIO_PRICE" => $minPrice,
					"PRINT_RATIO_PRICE" => $itemPrice["PRINT_RATIO_PRICE"],
					"PRINT_RATIO_DISCOUNT" => $itemPrice["PRINT_RATIO_DISCOUNT"],
					"PERCENT" => $itemPrice["PERCENT"],
					"CURRENCY" => $itemPrice["CURRENCY"],		
					"CATALOG_MEASURE_RATIO" => $arOffer["CATALOG_MEASURE_RATIO"],
					"CATALOG_MEASURE_NAME" => $arOffer["CATALOG_MEASURE_NAME"],
					"CATALOG_QUANTITY_TRACE" => $arOffer["CATALOG_QUANTITY_TRACE"]
				);
			}			
			$totalPrices[] = $itemPrice["RATIO_PRICE"];
		}
	}	
	if($minPrice === false) {
		$arResult["TOTAL_OFFERS"]["MIN_PRICE"] = array(
			"RATIO_PRICE" => "0",
			"CURRENCY" => $arResult["OFFERS"][0]["ITEM_PRICES"][$arResult["OFFERS"][0]["ITEM_PRICE_SELECTED"]]["CURRENCY"],	
			"CATALOG_MEASURE_RATIO" => $arResult["OFFERS"][0]["CATALOG_MEASURE_RATIO"],
			"CATALOG_MEASURE_NAME" => $arResult["OFFERS"][0]["CATALOG_MEASURE_NAME"]
		);
	}		
	$arResult["TOTAL_OFFERS"]["QUANTITY"] = $totalQnt;	
	if(count(array_unique($totalPrices)) > 1) {
		$arResult["TOTAL_OFFERS"]["FROM"] = "Y";
	} else {
		$arResult["TOTAL_OFFERS"]["FROM"] = "N";
	}	
	//END_TOTAL_OFFERS//
	
	foreach($arResult["OFFERS"] as $keyOffer => $arOffer) {		
		//DETAIL_PREVIEW_IMG//
		if(isset($arOffer["DETAIL_PICTURE"])) {
			if(!is_array($arOffer["DETAIL_PICTURE"]) && $arOffer["DETAIL_PICTURE"] > 0) {
				$arFile = CFile::GetFileArray($arOffer["DETAIL_PICTURE"]);
				$arResult["OFFERS"][$keyOffer]["DETAIL_PICTURE"] = $arFile;
			}
			
			//DETAIL_IMG//
			if($arOffer["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_DETAIL_IMG_WIDTH"] || $arOffer["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_DETAIL_IMG_HEIGHT"]) {
				$arFileTmp = CFile::ResizeImageGet(
					$arOffer["DETAIL_PICTURE"],
					array("width" => $arParams["DISPLAY_DETAIL_IMG_WIDTH"], "height" => $arParams["DISPLAY_DETAIL_IMG_HEIGHT"]),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arResult["OFFERS"][$keyOffer]["DETAIL_IMG"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				$arResult["OFFERS"][$keyOffer]["DETAIL_IMG"] = $arOffer["DETAIL_PICTURE"];
			}
			
			//PREVIEW_IMG//
			if($arOffer["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arOffer["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
				$arFileTmp = CFile::ResizeImageGet(
					$arOffer["DETAIL_PICTURE"],
					array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arResult["OFFERS"][$keyOffer]["PREVIEW_IMG"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			} else {
				$arResult["OFFERS"][$keyOffer]["PREVIEW_IMG"] = $arOffer["DETAIL_PICTURE"];
			}
		}
		//END_DETAIL_PREVIEW_IMG//
		
		//MIN_PRICE//
		if(count($arOffer["ITEM_QUANTITY_RANGES"]) > 1 && $arSetting["OFFERS_VIEW"]["VALUE"] == "LIST") {			
			$minPrice = false;
			foreach($arOffer["ITEM_PRICES"] as $itemPrice) {
				if($itemPrice["RATIO_PRICE"] == 0)
					continue;
				if($minPrice === false || $minPrice > $itemPrice["RATIO_PRICE"]) {								
					$minPrice = $itemPrice["RATIO_PRICE"];					
					$arResult["OFFERS"][$keyOffer]["MIN_PRICE"] = array(		
						"RATIO_BASE_PRICE" => $itemPrice["RATIO_BASE_PRICE"],
						"PRINT_RATIO_BASE_PRICE" => $itemPrice["PRINT_RATIO_BASE_PRICE"],
						"RATIO_PRICE" => $minPrice,						
						"PRINT_RATIO_DISCOUNT" => $itemPrice["PRINT_RATIO_DISCOUNT"],
						"PERCENT" => $itemPrice["PERCENT"],
						"CURRENCY" => $itemPrice["CURRENCY"],
						"PERCENT" => $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]]["PERCENT"],
						"MIN_QUANTITY" => $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]]["MIN_QUANTITY"]
					);
				}
			}
			if($minPrice === false) {
				$arResult["OFFERS"][$keyOffer]["MIN_PRICE"] = array(
					"RATIO_PRICE" => "0",
					"CURRENCY" => $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]]["CURRENCY"]
				);
			}
			if(!$inPriceRatio){
				$arResult["OFFERS"][$keyOffer]["CATALOG_MEASURE_RATIO"] = 1;
			}
		} else {
			$arResult["OFFERS"][$keyOffer]["MIN_PRICE"] = $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]];
			
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
			$arResult["OFFERS"][$keyOffer]["PRICE_MATRIX_SHOW"]["COLS"] = $arOffer["PRICE_MATRIX"]["COLS"];
			$arResult["OFFERS"][$keyOffer]["PRICE_MATRIX_SHOW"]["MATRIX"] = $arPriceMatrix;
		}
	}	
}
//END_OFFERS//

//PROPERTIES_JS_OFFERS//
$arParams["OFFER_TREE_PROPS"] = $arParams["OFFERS_PROPERTY_CODE"];
if(!is_array($arParams["OFFER_TREE_PROPS"]))
	$arParams["OFFER_TREE_PROPS"] = array($arParams["OFFER_TREE_PROPS"]);
foreach($arParams["OFFER_TREE_PROPS"] as $key => $value) {
	$value = (string)$value;
	if("" == $value || "-" == $value)
		unset($arParams["OFFER_TREE_PROPS"][$key]);
}
if(empty($arParams["OFFER_TREE_PROPS"]) && isset($arParams["OFFERS_CART_PROPERTIES"]) && is_array($arParams["OFFERS_CART_PROPERTIES"])) {
	$arParams["OFFER_TREE_PROPS"] = $arParams["OFFERS_CART_PROPERTIES"];
	foreach($arParams["OFFER_TREE_PROPS"] as $key => $value) {
		$value = (string)$value;
		if("" == $value || "-" == $value)
			unset($arParams["OFFER_TREE_PROPS"][$key]);
	}
}

$arSKUPropList = array();
$arSKUPropIDs = array();
$arSKUPropKeys = array();
$boolSKU = false;

if($arResult["MODULES"]["catalog"]) {
	$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams["IBLOCK_ID"]);
	$boolSKU = !empty($arSKU) && is_array($arSKU);

	if($boolSKU && !empty($arParams["OFFER_TREE_PROPS"])) {
		$arSKUPropList = CIBlockPriceTools::getTreeProperties(
			$arSKU,
			$arParams["OFFER_TREE_PROPS"],
			array()
		);
		$arSKUPropIDs = array_keys($arSKUPropList);
	}
}

if($arResult["MODULES"]["catalog"]) {
	$arResult["CATALOG"] = true;
	if(!isset($arResult["CATALOG_TYPE"]))
		$arResult["CATALOG_TYPE"] = CCatalogProduct::TYPE_PRODUCT;
	if((CCatalogProduct::TYPE_PRODUCT == $arResult["CATALOG_TYPE"] || CCatalogProduct::TYPE_SKU == $arResult["CATALOG_TYPE"]) && !empty($arResult["OFFERS"])) {
		$arResult["CATALOG_TYPE"] = CCatalogProduct::TYPE_SKU;
	}
	switch($arResult["CATALOG_TYPE"]) {
		case CCatalogProduct::TYPE_SET:
			$arResult["OFFERS"] = array();
			break;
		case CCatalogProduct::TYPE_SKU:
			break;
		case CCatalogProduct::TYPE_PRODUCT:
		default:
			break;
	}
} else {
	$arResult["CATALOG_TYPE"] = 0;
	$arResult["OFFERS"] = array();
}

if($arResult["CATALOG"] && isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
	$arResultSKUPropIDs = array();
	$arFilterProp = array();
	$arNeedValues = array();
	foreach($arResult["OFFERS"] as $arOffer) {
		foreach($arSKUPropIDs as $strOneCode) {
			if(isset($arOffer["DISPLAY_PROPERTIES"][$strOneCode])) {
				$arResultSKUPropIDs[$strOneCode] = true;
				if(!isset($arNeedValues[$arSKUPropList[$strOneCode]["ID"]]))
					$arNeedValues[$arSKUPropList[$strOneCode]["ID"]] = array();
				$valueId = (
					$arSKUPropList[$strOneCode]["PROPERTY_TYPE"] == Iblock\PropertyTable::TYPE_LIST
					? $arOffer["DISPLAY_PROPERTIES"][$strOneCode]["VALUE_ENUM_ID"]
					: $arOffer["DISPLAY_PROPERTIES"][$strOneCode]["VALUE"]
				);
				$arNeedValues[$arSKUPropList[$strOneCode]["ID"]][$valueId] = $valueId;
				unset($valueId);
				if(!isset($arFilterProp[$strOneCode]))
					$arFilterProp[$strOneCode] = $arSKUPropList[$strOneCode];
			}
		}
		unset($strOneCode);
	}
	unset($arOffer);

	CIBlockPriceTools::getTreePropertyValues($arSKUPropList, $arNeedValues);
	
	if($arSetting["OFFERS_VIEW"]["VALUE"] == "LIST") {
		 $propertyIterator = Iblock\PropertyTable::getList(array(
			"select" => array(
				"ID", "IBLOCK_ID", "CODE", "NAME", "SORT", "LINK_IBLOCK_ID", "PROPERTY_TYPE", "USER_TYPE", "USER_TYPE_SETTINGS"
			),
            "filter" => array(
                "=IBLOCK_ID" => $arSKU["IBLOCK_ID"],
                "=PROPERTY_TYPE" => array(
					Iblock\PropertyTable::TYPE_STRING
                ),
				"=ACTIVE" => "Y", "=MULTIPLE" => "N"
			),
			"order" => array(
				"SORT" => "ASC", "ID" => "ASC"
			)
        ));
        while($propInfo = $propertyIterator->fetch()) {			
			if(!in_array($propInfo["CODE"], $arParams["OFFER_TREE_PROPS"]))
				continue;			
			$arSKUPropList[$propInfo["CODE"]] = $propInfo;
			$arSKUPropList[$propInfo["CODE"]]["VALUES"] = array();
			$arSKUPropList[$propInfo["CODE"]]["SHOW_MODE"] = "TEXT";
			$arSKUPropList[$propInfo["CODE"]]["DEFAULT_VALUES"] = array(
				"PICT" => false,
				"NAME" => "-"
			);
		}
	}
	
	$arSKUPropIDs = array_keys($arSKUPropList);
	$arSKUPropKeys = array_fill_keys($arSKUPropIDs, false);

	$arMatrixFields = $arSKUPropKeys;
	$arMatrix = array();
	
	$arNewOffers = array();
	
	$arResult["OFFERS_PROP"] = false;
	
	$arDouble = array();	
	foreach($arResult["OFFERS"] as $keyOffer => $arOffer) {
		$arOffer["ID"] = (int)$arOffer["ID"];
		if(isset($arDouble[$arOffer["ID"]]))
			continue;
		$arRow = array();
		foreach($arSKUPropIDs as $propkey => $strOneCode) {			
			$arCell = array(
				"VALUE" => 0,
				"SORT" => PHP_INT_MAX,
				"NA" => true
			);			
			if(isset($arOffer["DISPLAY_PROPERTIES"][$strOneCode])) {
				$arMatrixFields[$strOneCode] = true;
				$arCell["NA"] = false;
				if("directory" == $arSKUPropList[$strOneCode]["USER_TYPE"]) {
					$intValue = $arSKUPropList[$strOneCode]["XML_MAP"][$arOffer["DISPLAY_PROPERTIES"][$strOneCode]["VALUE"]];
					$arCell["VALUE"] = $intValue;
				} elseif("L" == $arSKUPropList[$strOneCode]["PROPERTY_TYPE"]) {
					$arCell["VALUE"] = (int)$arOffer["DISPLAY_PROPERTIES"][$strOneCode]["VALUE_ENUM_ID"];
				} elseif("E" == $arSKUPropList[$strOneCode]["PROPERTY_TYPE"]) {
					$arCell["VALUE"] = (int)$arOffer["DISPLAY_PROPERTIES"][$strOneCode]["VALUE"];
				} elseif("S" == $arSKUPropList[$strOneCode]["PROPERTY_TYPE"]) {
					$arCell["VALUE"] = (int)$arOffer["DISPLAY_PROPERTIES"][$strOneCode]["PROPERTY_VALUE_ID"];					
				}
				$arCell["SORT"] = $arSKUPropList[$strOneCode]["VALUES"][$arCell["VALUE"]]["SORT"];
			}
			$arRow[$strOneCode] = $arCell;
		}
		$arMatrix[$keyOffer] = $arRow;
		
		$arDouble[$arOffer["ID"]] = true;
		$arNewOffers[$keyOffer] = $arOffer;
	}
	$arResult["OFFERS"] = $arNewOffers;
	
	$arUsedFields = array();
	$arSortFields = array();
	
	foreach($arSKUPropIDs as $propkey => $strOneCode) {
		$boolExist = $arMatrixFields[$strOneCode];
		foreach($arMatrix as $keyOffer => $arRow) {
			if($boolExist) {
				if(!isset($arResult["OFFERS"][$keyOffer]["TREE"]))
					$arResult["OFFERS"][$keyOffer]["TREE"] = array();
				$arResult["OFFERS"][$keyOffer]["TREE"]["PROP_".$arSKUPropList[$strOneCode]["ID"]] = $arMatrix[$keyOffer][$strOneCode]["VALUE"];
				$arResult["OFFERS"][$keyOffer]["SKU_SORT_".$strOneCode] = $arMatrix[$keyOffer][$strOneCode]["SORT"];
				$arUsedFields[$strOneCode] = true;
				$arSortFields["SKU_SORT_".$strOneCode] = SORT_NUMERIC;
			} else {
				unset($arMatrix[$keyOffer][$strOneCode]);
			}
		}
	}
	$arResult["OFFERS_PROP"] = $arUsedFields;
	
	if($arParams["OFFERS_SORT_FIELD3"] == "PROPERTIES" || $arSetting["OFFERS_VIEW"]["VALUE"] != "LIST")
		Collection::sortByColumn($arResult["OFFERS"], $arSortFields);	
	
	$intSelected = -1;
	$intSelectedLink = -1;		
	$minRatioPrice = false;
	foreach($arResult["OFFERS"] as $keyOffer => $arOffer) {
		foreach($arOffer["ITEM_PRICES"] as $itemPrice) {
			if($itemPrice["RATIO_PRICE"] == 0)
				continue;				
			if($minRatioPrice === false || $minRatioPrice > $itemPrice["RATIO_PRICE"]) {
				$intSelected = $keyOffer;
				$minRatioPrice = $itemPrice["RATIO_PRICE"];			
			}
		}
		if(isset($_GET['offer']) && !empty($_GET['offer']) && $arOffer['ID'] == intval($_GET['offer']))
			$intSelectedLink = $keyOffer;
	}
	$arMatrix = array();
	foreach($arResult["OFFERS"] as $keyOffer => $arOffer) {	
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
					$arPriceMatrix["COLS"] = $arOffer["PRICE_MATRIX"]["COLS"];
					$arPriceMatrix["ROWS"] = $arOffer["PRICE_MATRIX"]["ROWS"];
					if($inPriceRatio) {
						$arPriceMatrix[$key_matrix][$key2]["DISCOUNT_PRICE"] = $arOffer["CATALOG_MEASURE_RATIO"]*$arOffer["PRICE_MATRIX"]["MATRIX"][$key_matrix][$key2]["DISCOUNT_PRICE"];
					}
				}
			}
		}
		
		$arMorePhoto = array();
		if(is_array($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"])) {
			foreach($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $key_photo => $pic) {
				//MORE_PICTURES_DETAIL//
				if(!empty($arWaterMark)) {
					$arFileTmp = CFile::ResizeImageGet(
						$pic,
						array("width" => 10000, "height" => 10000),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true,
						$arWaterMark
					);
					$arMorePhoto["PHOTO"][$key_photo]["DETAIL"] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"],
					);
				} else {
					$arFileInfo = CFile::GetFileArray($pic);
					$arMorePhoto[$key_photo]["DETAIL"] = array(
						"SRC" => $arFileInfo["SRC"],
						"WIDTH" => $arFileInfo["WIDTH"],
						"HEIGHT" => $arFileInfo["HEIGHT"],
					);
				}
				
				//MORE_PICTURES_PREVIEW//
				$arFileTmp = CFile::ResizeImageGet(
					$pic,
					array("width" => $arParams["DISPLAY_MORE_PHOTO_WIDTH"] ? $arParams["DISPLAY_MORE_PHOTO_WIDTH"] : 86, "height" => $arParams["DISPLAY_MORE_PHOTO_HEIGHT"] ? $arParams["DISPLAY_MORE_PHOTO_HEIGHT"] : 86),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true,
					$arWaterMark
				);
				
				$arMorePhoto[$key_photo]["PREVIEW"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"],
				);
			}
		}
		
		$arOneRow = array(
			"ID" => $arOffer["ID"],
			"IBLOCK_ID" => $arOffer["IBLOCK_ID"],
			"NAME" => $arOffer["~NAME"],
			"PREVIEW_IMG" => $arOffer["PREVIEW_IMG"],
			"DETAIL_PICTURE" => $arOffer["DETAIL_PICTURE"],
			"TREE" => $arOffer["TREE"],			
			"ITEM_PRICE_MODE" => $arOffer["ITEM_PRICE_MODE"],
			"ITEM_PRICES" => $arOffer["ITEM_PRICES"],
			"ITEM_PRICE_SELECTED" => $arOffer["ITEM_PRICE_SELECTED"],
			"ITEM_QUANTITY_RANGES" => $arOffer["ITEM_QUANTITY_RANGES"],
			"ITEM_QUANTITY_RANGE_SELECTED" => $arOffer["ITEM_QUANTITY_RANGE_SELECTED"],			
			"CHECK_QUANTITY" => $arOffer["CHECK_QUANTITY"],
			"MAX_QUANTITY" => $arOffer["CATALOG_QUANTITY"],
			"STEP_QUANTITY" => $arOffer["CATALOG_MEASURE_RATIO"],
			"QUANTITY_FLOAT" => is_double($arOffer["CATALOG_MEASURE_RATIO"]),
			"CAN_BUY" => $arOffer["CAN_BUY"],
			"PRICE_MATRIX" => $arPriceMatrix,
			"MORE_PHOTO" => $arMorePhoto
		);
		$arMatrix[$keyOffer] = $arOneRow;
	}
	if(-1 == $intSelected)
		$intSelected = 0;
	$arResult["JS_OFFERS"] = $arMatrix;
	$arResult["OFFERS_SELECTED"] = ($intSelectedLink != -1? $intSelectedLink: $intSelected);
	
	$arResult["OFFERS_IBLOCK"] = $arSKU["IBLOCK_ID"];
}

//SKU_PROPS_PICT//
$arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_HEX", "PROPERTY_PICT");
foreach($arSKUPropList as $key => $arSKUProp) {
	if($arSKUProp["SHOW_MODE"] == "PICT") {		
		$arSkuID = array();
		foreach($arSKUProp["VALUES"] as $key2 => $arSKU) {
			if($arSKU["ID"] > 0)
				$arSkuID[] = $arSKU["ID"];
		}
		$arFilter = array("IBLOCK_ID" => $arSKUProp["LINK_IBLOCK_ID"], "ID" => $arSkuID);
		$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
		while($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			if(!empty($arFields["PROPERTY_HEX_VALUE"]))
				$arSKUPropList[$key]["VALUES"][$arFields["ID"]]["HEX"] = $arFields["PROPERTY_HEX_VALUE"];
			if($arFields["PROPERTY_PICT_VALUE"] > 0) {
				$arFile = CFile::GetFileArray($arFields["PROPERTY_PICT_VALUE"]);
				if($arFile["WIDTH"] > 24 || $arFile["HEIGHT"] > 24) {
					$arFileTmp = CFile::ResizeImageGet(
						$arFile,
						array("width" => 24, "height" => 24),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$arSKUPropList[$key]["VALUES"][$arFields["ID"]]["PICT"] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"],
					);
				} else {
					$arSKUPropList[$key]["VALUES"][$arFields["ID"]]["PICT"] = $arFile;
				}
			}
		}			
	}
}

$arResult["SKU_PROPS"] = $arSKUPropList;

//CURRENCIES//
$arResult["CURRENCIES"] = array();
if($arResult["MODULES"]["currency"]) {
	if($boolConvert) {
		$currencyFormat = CCurrencyLang::GetFormatDescription($arResult["CONVERT_CURRENCY"]["CURRENCY_ID"]);
		$arResult["CURRENCIES"] = array(
			array(
				"CURRENCY" => $arResult["CONVERT_CURRENCY"]["CURRENCY_ID"],
				"FORMAT" => array(
					"FORMAT_STRING" => $currencyFormat["FORMAT_STRING"],
					"DEC_POINT" => $currencyFormat["DEC_POINT"],
					"THOUSANDS_SEP" => $currencyFormat["THOUSANDS_SEP"],
					"DECIMALS" => $currencyFormat["DECIMALS"],
					"THOUSANDS_VARIANT" => $currencyFormat["THOUSANDS_VARIANT"],
					"HIDE_ZERO" => $currencyFormat["HIDE_ZERO"]
				)
			)
		);
		unset($currencyFormat);
	} else {
		$currencyIterator = CurrencyTable::getList(array(
			"select" => array("CURRENCY")
		));
		while($currency = $currencyIterator->fetch()) {
			$currencyFormat = CCurrencyLang::GetFormatDescription($currency["CURRENCY"]);
			$arResult["CURRENCIES"][] = array(
				"CURRENCY" => $currency["CURRENCY"],
				"FORMAT" => array(
					"FORMAT_STRING" => $currencyFormat["FORMAT_STRING"],
					"DEC_POINT" => $currencyFormat["DEC_POINT"],
					"THOUSANDS_SEP" => $currencyFormat["THOUSANDS_SEP"],
					"DECIMALS" => $currencyFormat["DECIMALS"],
					"THOUSANDS_VARIANT" => $currencyFormat["THOUSANDS_VARIANT"],
					"HIDE_ZERO" => $currencyFormat["HIDE_ZERO"]
				)
			);
		}
		unset($currencyFormat, $currency, $currencyIterator);
	}
}

//PROPERTY_ACCESSORIES_ID//
$arResult["PROPERTY_ACCESSORIES_ID"] = $arResult["PROPERTIES"]["ACCESSORIES"]["VALUE"];

$arResult["PRICE_MATRIX_SHOW"]["COLS"] = $arResult["PRICE_MATRIX"]["COLS"];
$arResult["PRICE_MATRIX_SHOW"]["MATRIX"] = $arPriceMatrix;


//CACHE_KEYS//
$this->__component->SetResultCacheKeys(
	array(
		"STR_MAIN_ID",
		"USE_CAPTCHA",
		"CATALOG_SUBSCRIBE",
		"NAME",
		"PREVIEW_TEXT",
		"DETAIL_PICTURE",
		"MORE_PHOTO",
		"PROPERTY_ACCESSORIES_ID",
		"MIN_PRICE",
		"CAN_BUY",		
		"JS_OFFERS",
		"OFFERS_IBLOCK",
		"OFFERS_SELECTED"
	)
);?>