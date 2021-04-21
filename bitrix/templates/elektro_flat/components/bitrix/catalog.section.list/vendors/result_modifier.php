<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arParams["VENDOR_ID"] = intval($arParams["VENDOR_ID"]);
if($arParams["VENDOR_ID"] <= 0) {
	$arResult["SECTIONS"] = array();
	return;
}

use Bitrix\Main\Type\Collection;

$arSections = false;
foreach($arResult["SECTIONS"] as $key => $arSection) {
	if($arSection["IBLOCK_SECTION_ID"] > 0) {
		$arSections[$arSection["IBLOCK_SECTION_ID"]]["CHILDREN"][$arSection["ID"]] = $arSection;
	} else {
		$arSections[$arSection["ID"]] = $arSection;
	}
}
$arResult["SECTIONS"] = $arSections;

$arFilter = array(
	"ACTIVE" => "Y",
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],	
	"INCLUDE_SUBSECTIONS" => "Y",
	"PROPERTY_MANUFACTURER" => $arParams["VENDOR_ID"]
);
foreach($arResult["SECTIONS"] as $key => $arSection) {
	$arFilter["SECTION_ID"] = $arSection["ID"];
	$itemsCount = CIBlockElement::GetList(array(), $arFilter, array(), false);
	if($itemsCount > 0) {
		if(isset($arSection["CHILDREN"]) && !empty($arSection["CHILDREN"])) {
			foreach($arSection["CHILDREN"] as $keyChild => $arChild) {
				$arFilter["SECTION_ID"] = $arChild["ID"];
				$itemsCount = CIBlockElement::GetList(array(), $arFilter, array(), false);
				if($itemsCount < 1)
					unset($arResult["SECTIONS"][$key]["CHILDREN"][$keyChild]);
			}
		}
	} else {
		unset($arResult["SECTIONS"][$key]);
	}
}

Collection::sortByColumn($arResult["SECTIONS"], array("SORT" => SORT_NUMERIC, "NAME" => SORT_ASC));

$addToUrl = false;
if($arParams["SEF_MODE"] == "Y") {
	$addToUrl = "filter/manufacturer-is-".rawurlencode(Bitrix\Main\Text\Encoding::convertEncoding(toLower($arParams["VENDOR_NAME"]), LANG_CHARSET, "utf-8"))."/apply/";
} else {	
	$rsProp = CIBlock::GetProperties($arParams["IBLOCK_ID"], array(), array("CODE" => "MANUFACTURER"));
	if($arProp = $rsProp->fetch())
		$addToUrl = "?set_filter=Y&arrFilter_".$arProp["ID"]."_".abs(crc32($arParams["VENDOR_ID"]))."=Y";
}

foreach($arResult["SECTIONS"] as $key => $arSection) {
	$arResult["SECTIONS"][$key]["NAME"] = $arSection["NAME"].(!empty($arParams["VENDOR_NAME"]) ? " ".$arParams["VENDOR_NAME"] : "");
	$arResult["SECTIONS"][$key]["SECTION_PAGE_URL"] = $arSection["SECTION_PAGE_URL"].(!empty($addToUrl) ? $addToUrl : "");
	if(isset($arSection["CHILDREN"]) && !empty($arSection["CHILDREN"])) {
		foreach($arSection["CHILDREN"] as $keyChild => $arChild) {
			$arResult["SECTIONS"][$key]["CHILDREN"][$keyChild]["NAME"] = $arChild["NAME"].(!empty($arParams["VENDOR_NAME"]) ? " ".$arParams["VENDOR_NAME"] : "");
			$arResult["SECTIONS"][$key]["CHILDREN"][$keyChild]["SECTION_PAGE_URL"] = $arChild["SECTION_PAGE_URL"].(!empty($addToUrl) ? $addToUrl : "");
			if(is_array($arChild["PICTURE"])) {
				if($arChild["PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arChild["PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
					$arFileTmp = CFile::ResizeImageGet(
						$arChild["PICTURE"],
						array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$arResult["SECTIONS"][$key]["CHILDREN"][$keyChild]["PICTURE"] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"],
					);
				}
			}
		}
	}
}?>