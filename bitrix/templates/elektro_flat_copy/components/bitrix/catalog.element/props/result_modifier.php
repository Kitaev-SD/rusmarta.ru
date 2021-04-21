<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Type\Collection,
	Bitrix\Iblock,
	Bitrix\Currency\CurrencyTable;

$arSetting = CElektroinstrument::GetFrontParametrsValues(SITE_ID);

//USE_PRICE_RATIO//
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]);

//OFFERS//
if(!empty($arParams["OFFERS_DEF"]))
	$arResult["OFFERS"] = $arParams["OFFERS_DEF"];

//PRICES//
if(!empty($arParams["PRICES_DEF"]))
	$arResult["ITEM_PRICES"] = $arParams["PRICES_DEF"];

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
			unset($keyPrice, $itemPrice);
		}
		unset($key_off, $arOffer);
	} else {
		foreach($arResult["ITEM_PRICES"] as $keyPrice => $itemPrice) {
			$arResult["ITEM_PRICES"][$keyPrice]["RATIO_BASE_PRICE"] = $arResult["ITEM_PRICES"][$keyPrice]["BASE_PRICE"];
			$arResult["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_BASE_PRICE"] = $arResult["ITEM_PRICES"][$keyPrice]["PRINT_BASE_PRICE"];
			$arResult["ITEM_PRICES"][$keyPrice]["RATIO_PRICE"] = $arResult["ITEM_PRICES"][$keyPrice]["PRICE"];
			$arResult["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_PRICE"] = $arResult["ITEM_PRICES"][$keyPrice]["PRINT_PRICE"];
			$arResult["ITEM_PRICES"][$keyPrice]["RATIO_DISCOUNT"] = $arResult["ITEM_PRICES"][$keyPrice]["DISCOUNT"];
			$arResult["ITEM_PRICES"][$keyPrice]["PRINT_RATIO_DISCOUNT"] = $arResult["ITEM_PRICES"][$keyPrice]["PRINT_DISCOUNT"];
		}
		unset($keyPrice, $itemPrice);
	}
}

//MIN_QUANTITY//
foreach($arResult["ITEM_PRICES"] as $keyPrice => $itemPrice) {
	$arResult["ITEM_PRICES"][$keyPrice]["MIN_QUANTITY"] = $arResult["CATALOG_MEASURE_RATIO"];
}
unset($keyPrice, $itemPrice);

//PRICE_MATRIX//
if(!$arParams["IS_GIFT"]) {
	$arPriceMatrix = false;
	$arPriceMatrix = $arResult["PRICE_MATRIX"]["MATRIX"];
	if(isset($arPriceMatrix) && is_array($arPriceMatrix)) foreach($arPriceMatrix as $key => $item) {
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
		unset($key2, $item2);
	}
	unset($key, $item);

	$arResult["PRICE_MATRIX_SHOW"]["COLS"] = $arResult["PRICE_MATRIX"]["COLS"];
	$arResult["PRICE_MATRIX_SHOW"]["MATRIX"] = $arPriceMatrix;
	unset($arPriceMatrix);
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

//PREVIEW_PICTURE//	
if(is_array($arResult["PREVIEW_PICTURE"])) {
	if($arResult["PREVIEW_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arResult["PREVIEW_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
		$arFileTmp = CFile::ResizeImageGet(
			$arResult["PREVIEW_PICTURE"],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["PREVIEW_PICTURE"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"]
		);
		unset($arFileTmp);
	}
} elseif(is_array($arResult["DETAIL_PICTURE"])) {
	if($arResult["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arResult["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
		$arFileTmp = CFile::ResizeImageGet(
			$arResult["DETAIL_PICTURE"],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["PREVIEW_PICTURE"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"]
		);
		unset($arFileTmp);
	} else {
		$arResult["PREVIEW_PICTURE"] = $arResult["DETAIL_PICTURE"];
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
	unset($pid);
}

//OFFERS//
if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {	
	foreach($arResult["OFFERS"] as $keyOffer => $arOffer) {		
		//PREVIEW_PICTURE//
		if(is_array($arOffer["PREVIEW_PICTURE"])) {
			if($arOffer["PREVIEW_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arOffer["PREVIEW_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
				$arFileTmp = CFile::ResizeImageGet(
					$arOffer["PREVIEW_PICTURE"],
					array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arResult["OFFERS"][$keyOffer]["PREVIEW_PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"]
				);
				unset($arFileTmp);
			}
		} elseif(is_array($arOffer["DETAIL_PICTURE"])) {
			if($arOffer["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arOffer["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
				$arFileTmp = CFile::ResizeImageGet(
					$arOffer["DETAIL_PICTURE"],
					array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				$arResult["OFFERS"][$keyOffer]["PREVIEW_PICTURE"] = array(
					"SRC" => $arFileTmp["src"],
					"WIDTH" => $arFileTmp["width"],
					"HEIGHT" => $arFileTmp["height"]
				);
				unset($arFileTmp);
			} else {
				$arResult["OFFERS"][$keyOffer]["PREVIEW_PICTURE"] = $arOffer["DETAIL_PICTURE"];
			}
		}
		//PREVIEW_PICTURE//

		//MIN_PRICE//		
		if(!$arParams["IS_GIFT"] && count($arOffer["ITEM_QUANTITY_RANGES"]) > 1 && $arSetting["OFFERS_VIEW"] == "LIST") {			
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
			unset($itemPrice);
			if($minPrice === false) {
				$arResult["OFFERS"][$keyOffer]["MIN_PRICE"] = array(
					"RATIO_PRICE" => "0",
					"CURRENCY" => $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]]["CURRENCY"]
				);
			}
		} else {
			$arResult["OFFERS"][$keyOffer]["MIN_PRICE"] = $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]];
			
			//PRICE_MATRIX//
			if(!$arParams["IS_GIFT"]) {
				$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams['IBLOCK_ID'], $arParams['PRICE_CODE']);
				foreach($arResultPrices as $value) {
					$arPriceTypeID[] = $value['ID'];
				}
				if(isset($value))
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
						unset($key2, $item2);
					}
					unset($key_matrix, $item);
				}
				$arResult["OFFERS"][$keyOffer]["PRICE_MATRIX_SHOW"]["COLS"] = $arOffer["PRICE_MATRIX"]["COLS"];
				$arResult["OFFERS"][$keyOffer]["PRICE_MATRIX_SHOW"]["MATRIX"] = $arPriceMatrix;
				unset($arPriceMatrix);
			}
		}
	}
	unset($keyOffer, $arOffer);
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
unset($key, $value);
if(empty($arParams["OFFER_TREE_PROPS"]) && isset($arParams["OFFERS_CART_PROPERTIES"]) && is_array($arParams["OFFERS_CART_PROPERTIES"])) {
	$arParams["OFFER_TREE_PROPS"] = $arParams["OFFERS_CART_PROPERTIES"];
	foreach($arParams["OFFER_TREE_PROPS"] as $key => $value) {
		$value = (string)$value;
		if("" == $value || "-" == $value)
			unset($arParams["OFFER_TREE_PROPS"][$key]);
	}
	unset($key, $value);
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
	
	if($arSetting["OFFERS_VIEW"] == "LIST") {
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
		unset($propkey, $strOneCode);
		$arMatrix[$keyOffer] = $arRow;
		
		$arDouble[$arOffer["ID"]] = true;
		$arNewOffers[$keyOffer] = $arOffer;
	}
	unset($keyOffer, $arOffer);
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
		unset($keyOffer, $arRow);
	}
	unset($propkey, $strOneCode);
	$arResult["OFFERS_PROP"] = $arUsedFields;
	
	if($arParams["OFFERS_SORT_FIELD3"] == "PROPERTIES" || $arSetting["OFFERS_VIEW"] != "LIST")
		Collection::sortByColumn($arResult["OFFERS"], $arSortFields);	
	
	$intSelected = -1;
	$minRatioPrice = false;
	foreach($arResult["OFFERS"] as $keyOffer => $arOffer) {
		if(!$arParams["IS_GIFT"]) {
			foreach($arOffer["ITEM_PRICES"] as $itemPrice) {
				if($itemPrice["RATIO_PRICE"] == 0)
					continue;		
				if($minRatioPrice === false || $minRatioPrice > $itemPrice["RATIO_PRICE"]) {
					$intSelected = $keyOffer;
					$minRatioPrice = $itemPrice["RATIO_PRICE"];
				}
			}
			unset($itemPrice);
		} else {
			$itemPrice = $arOffer["ITEM_PRICES"][$arOffer["ITEM_PRICE_SELECTED"]];
			if($itemPrice["RATIO_BASE_PRICE"] == 0)
				continue;
			if($minRatioPrice === false || $minRatioPrice > $itemPrice["RATIO_BASE_PRICE"]) {
				$intSelected = $keyOffer;
				$minRatioPrice = $itemPrice["RATIO_BASE_PRICE"];
			}
		}
	}
	unset($keyOffer, $arOffer);
	$arMatrix = array();
	foreach($arResult["OFFERS"] as $keyOffer => $arOffer) {
		//PRICE_MATRIX//
		if(!$arParams["IS_GIFT"]) {
			$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arParams['IBLOCK_ID'], $arParams['PRICE_CODE']);
			foreach($arResultPrices as $value) {
				$arPriceTypeID[] = $value['ID'];
			}
			if(isset($value))
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
					unset($key2, $item2);
				}
				unset($key_matrix, $item);
			}
		}
		
		$arMorePhoto = array();
		if(is_array($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"])) {
			foreach($arOffer["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $key_photo => $pic) {
				//MORE_PICTURES_WATERMARK//
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
					unset($arFileTmp);
				} else {
					$arFileInfo = CFile::GetFileArray($pic);
					$arMorePhoto[$key_photo]["DETAIL"] = array(
						"SRC" => $arFileInfo["SRC"],
						"WIDTH" => $arFileInfo["WIDTH"],
						"HEIGHT" => $arFileInfo["HEIGHT"],
					);
					unset($arFileInfo);
				}
			}
			unset($key_photo, $pic);
		}

		$arOneRow = array(
			"ID" => $arOffer["ID"],
			"NAME" => $arOffer["~NAME"],
			"PREVIEW_IMG" => $arOffer["PREVIEW_IMG"],
			"TREE" => $arOffer["TREE"],
			"ITEM_PRICE_MODE" => $arOffer["ITEM_PRICE_MODE"],
			"ITEM_PRICES" => $arOffer["ITEM_PRICES"],
			"ITEM_PRICE_SELECTED" => $arOffer["ITEM_PRICE_SELECTED"],			
			"CHECK_QUANTITY" => $arOffer["CHECK_QUANTITY"],
			"MAX_QUANTITY" => !$arParams["IS_GIFT"] ? $arOffer["CATALOG_QUANTITY"] : $arOffer["ITEM_MEASURE_RATIOS"][$arOffer["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"],
			"STEP_QUANTITY" => !$arParams["IS_GIFT"] ? $arOffer["CATALOG_MEASURE_RATIO"] : $arOffer["ITEM_MEASURE_RATIOS"][$arOffer["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"],
			"QUANTITY_FLOAT" => !$arParams["IS_GIFT"] ? is_double($arOffer["CATALOG_MEASURE_RATIO"]) : is_double($arOffer["ITEM_MEASURE_RATIOS"][$arOffer["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"]),
			"CAN_BUY" => $arOffer["CAN_BUY"],			
			"MORE_PHOTO" => $arMorePhoto
		);
		if(!$arParams["IS_GIFT"]) {
			$arOneRow["ITEM_QUANTITY_RANGES"] = $arOffer["ITEM_QUANTITY_RANGES"];
			$arOneRow["ITEM_QUANTITY_RANGE_SELECTED"] = $arOffer["ITEM_QUANTITY_RANGE_SELECTED"];
			$arOneRow["PRICE_MATRIX"] = $arPriceMatrix;
		}
		$arMatrix[$keyOffer] = $arOneRow;		
	}
	unset($keyOffer, $arOffer);
	
	if(-1 == $intSelected)
		$intSelected = 0;
	
	$arResult["JS_OFFERS"] = $arMatrix;
	$arResult["OFFERS_SELECTED"] = $intSelected;
	
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
		unset($key2, $arSKU);
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
			} else {
				if(!empty($arSKUPropList[$key]["VALUES"][$arFields["ID"]]["PICT"]))
					$arSKUPropList[$key]["VALUES"][$arFields["ID"]]["PICT"] = null;
			}
		}
	}
}
unset($key, $arSKUProp);

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
}?>