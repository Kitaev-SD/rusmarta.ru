<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!empty($arResult["ITEMS"])) {
	//ELEMENTS//
	foreach($arResult["ITEMS"] as $key => $arElement) {		
		//STR_MAIN_ID//
		$arResult["ITEMS"][$key]["STR_MAIN_ID"] = $this->GetEditAreaId($arElement["ID"]);

		//CURRENT_DISCOUNT//
		$arPrice = array();
		$arResult["ITEMS"][$key]["CURRENT_DISCOUNT"] = array();		

		if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
			$minId = false;
			$minDiscount = false;
			foreach($arElement["OFFERS"] as $key_off => $arOffer) {				
				if($arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] == 0)
					continue;
				if($minDiscount === false || $minDiscount > $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"]) {
					$minId = $arOffer["ID"];
					$minDiscount = $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"];
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

		//PREVIEW_TEXT//
		if(!isset($arElement["PREVIEW_TEXT"])) {
			$obElement = CIBlockElement::GetByID($arElement["ID"]);
			if($arEl = $obElement->GetNext()) {
				$arResult["ITEMS"][$key]["PREVIEW_TEXT"] = $arEl["PREVIEW_TEXT"];
			}
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
			$totalDiscount = array();
			
			$minId = false;
			$minIblockId = false;
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
					$minIblockId = $arOffer["IBLOCK_ID"];
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
					"IBLOCK_ID" => $minIblockId,
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
	}
}?>