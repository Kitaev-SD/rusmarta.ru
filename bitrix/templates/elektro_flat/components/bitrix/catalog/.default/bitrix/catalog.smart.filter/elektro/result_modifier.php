<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//MAKE_FILTER//
function makeFilter($IBLOCK_ID, $SECTION_ID, $FILTER_NAME, $HIDE_NOT_AVAILABLE, $SHOW_ALL_WO_SECTION) {
	$bOffersIBlockExist = false;
	$arCatalog = false;
	$bCatalog = \Bitrix\Main\Loader::includeModule("catalog");
	if($bCatalog) {
		$arCatalog = CCatalogSKU::GetInfoByIBlock($IBLOCK_ID);
		if(!empty($arCatalog) && is_array($arCatalog)) {
			$bOffersIBlockExist = ($arCatalog["CATALOG_TYPE"] == CCatalogSKU::TYPE_PRODUCT || $arCatalog["CATALOG_TYPE"] == CCatalogSKU::TYPE_FULL);
			
			$SKU_IBLOCK_ID = $arCatalog["IBLOCK_ID"];
			$SKU_PROPERTY_ID = $arCatalog["SKU_PROPERTY_ID"];
		}
	}

	$gFilter = $GLOBALS[$FILTER_NAME];

	$arFilter = array(
		"IBLOCK_ID" => $IBLOCK_ID,
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
		"MIN_PERMISSION" => "R",
		"INCLUDE_SUBSECTIONS" => "Y",
	);

	if(($SECTION_ID > 0) || ($SHOW_ALL_WO_SECTION !== "Y")) {
		$arFilter["SECTION_ID"] = $SECTION_ID;
	}

	if("Y" == $HIDE_NOT_AVAILABLE)
		$arFilter["CATALOG_AVAILABLE"] = "Y";

	if($bCatalog && $bOffersIBlockExist) {
		$arPriceFilter = array();
		foreach($gFilter as $key => $value) {
			if(preg_match("/^(>=|<=|><)CATALOG_PRICE_/", $key)) {
				$arPriceFilter[$key] = $value;
				unset($gFilter[$key]);
			}
		}

		if(!empty($gFilter["OFFERS"])) {
			if(empty($arPriceFilter))
				$arSubFilter = $gFilter["OFFERS"];
			else
				$arSubFilter = array_merge($gFilter["OFFERS"], $arPriceFilter);

			$arSubFilter["IBLOCK_ID"] = $SKU_IBLOCK_ID;
			$arSubFilter["ACTIVE_DATE"] = "Y";
			$arSubFilter["ACTIVE"] = "Y";
			if("Y" == $HIDE_NOT_AVAILABLE)
				$arSubFilter["CATALOG_AVAILABLE"] = "Y";
			$arFilter["=ID"] = CIBlockElement::SubQuery("PROPERTY_".$SKU_PROPERTY_ID, $arSubFilter);
		} elseif(!empty($arPriceFilter)) {
			$arSubFilter = $arPriceFilter;

			$arSubFilter["IBLOCK_ID"] = $SKU_IBLOCK_ID;
			$arSubFilter["ACTIVE_DATE"] = "Y";
			$arSubFilter["ACTIVE"] = "Y";
			$arFilter[] = array(
				"LOGIC" => "OR",
				array($arPriceFilter),
				"=ID" => CIBlockElement::SubQuery("PROPERTY_".$SKU_PROPERTY_ID, $arSubFilter),
			);
		}
		unset($gFilter["OFFERS"]);
	}
	return array_merge($gFilter, $arFilter);
}

global $arSmartFilter;
$arSmartFilter = makeFilter($arParams["IBLOCK_ID"], $arParams["SECTION_ID"], (string)$arParams["FILTER_NAME"], $arParams["HIDE_NOT_AVAILABLE"], $arParams["SHOW_ALL_WO_SECTION"]);

//PROPERTY_COLOR//
foreach($arResult["ITEMS"] as $key => $arItem) {
	if($arItem["CODE"] == "COLOR" && !empty($arItem["VALUES"])) {
		$properties = CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"], "CODE" => $arItem["CODE"]));
		if($prop_fields = $properties->GetNext()) {
			$IBLOCK_ID = $prop_fields["LINK_IBLOCK_ID"];
		}		
		foreach($arItem["VALUES"] as $val => $ar) {
			$arSelect = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_HEX", "PROPERTY_PICT");
			$arFilter = array("IBLOCK_ID" => $IBLOCK_ID, "NAME" => $ar["VALUE"]);
			$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
			if($ob = $res->GetNextElement()) {
				$arFields = $ob->GetFields();
				$arResult["ITEMS"][$key]["VALUES"][$val]["NAME"] = $arFields["NAME"];
				if(!empty($arFields["PROPERTY_HEX_VALUE"]))
					$arResult["ITEMS"][$key]["VALUES"][$val]["HEX"] = $arFields["PROPERTY_HEX_VALUE"];
				if($arFields["PROPERTY_PICT_VALUE"] > 0) {
					$arFile = CFile::GetFileArray($arFields["PROPERTY_PICT_VALUE"]);
					if($arFile["WIDTH"] > 24 || $arFile["HEIGHT"] > 24) {
						$arFileTmp = CFile::ResizeImageGet(
							$arFile,
							array("width" => 24, "height" => 24),
							BX_RESIZE_IMAGE_PROPORTIONAL,
							true
						);
						$arResult["ITEMS"][$key]["VALUES"][$val]["PICT"] = array(
							"SRC" => $arFileTmp["src"],
							"WIDTH" => $arFileTmp["width"],
							"HEIGHT" => $arFileTmp["height"],
						);
					} else {
						$arResult["ITEMS"][$key]["VALUES"][$val]["PICT"] = $arFile;
					}
				}
			}
		}
	}
}?>