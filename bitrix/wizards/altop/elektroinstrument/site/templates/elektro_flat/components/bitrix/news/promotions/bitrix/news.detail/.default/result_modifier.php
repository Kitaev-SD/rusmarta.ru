<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arIBlock = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "FIELDS");
$arParams["DISPLAY_IMG_WIDTH"] = $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] ? $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] : 958;
$arParams["DISPLAY_IMG_HEIGHT"] = $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] ? $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] : 304;

//DISPLAY_ACTIVE_TO//
if(!isset($arResult["DISPLAY_ACTIVE_TO"]) && !empty($arResult["ACTIVE_TO"]))
	$arResult["DISPLAY_ACTIVE_TO"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arResult["ACTIVE_TO"], CSite::GetDateFormat()));

//DETAIL_PICTURE//
if(is_array($arResult["DETAIL_PICTURE"])) {
	if($arResult["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arResult["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
		$arFileTmp = CFile::ResizeImageGet(
			$arResult["DETAIL_PICTURE"],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
		$arResult["DETAIL_PICTURE"] = array(
			"SRC" => $arFileTmp["src"],
			"WIDTH" => $arFileTmp["width"],
			"HEIGHT" => $arFileTmp["height"],
		);
	}
}

//PRODUCTS_IDS//
$arResult["PRODUCTS_IDS"] = array();

if(!empty($arResult["PROPERTIES"]["SECTIONS"]["VALUE"]) || !empty($arResult["PROPERTIES"]["BRANDS"]["VALUE"])) {
	$arrFilter = array();
	if(!empty($arResult["PROPERTIES"]["SECTIONS"]["VALUE"]))
		$arrFilter = array(
			"IBLOCK_ID" => $arResult["PROPERTIES"]["SECTIONS"]["LINK_IBLOCK_ID"],
			"SECTION_ID" => $arResult["PROPERTIES"]["SECTIONS"]["VALUE"],
			"INCLUDE_SUBSECTIONS" => "Y"
		);	
	if(!empty($arResult["PROPERTIES"]["BRANDS"]["VALUE"]))
		$arrFilter["PROPERTY_MANUFACTURER"] = $arResult["PROPERTIES"]["BRANDS"]["VALUE"];
	
	$rsElements = CIBlockElement::GetList(
		array(),
		$arrFilter,
		false,
		false,
		array("ID", "IBLOCK_ID")
	);	
	while($arElement = $rsElements->GetNext()) {	
		$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
	}
}

if(!empty($arResult["PROPERTIES"]["PRODUCTS"]["VALUE"])) {
	$rsElements = CIBlockElement::GetList(
		array(),
		array(
			"IBLOCK_ID" => $arResult["PROPERTIES"]["PRODUCTS"]["LINK_IBLOCK_ID"],
			"ID" => $arResult["PROPERTIES"]["PRODUCTS"]["VALUE"]
		),
		false,
		false,
		array("ID", "IBLOCK_ID")
	);	
	while($arElement = $rsElements->GetNext()) {	
		$arResult["PRODUCTS_IDS"][] = $arElement["ID"];
	}
}

//CACHE_KEYS//
$this->__component->SetResultCacheKeys(
	array(
		"ID",
		"ACTIVE_TO",
		"DISPLAY_ACTIVE_TO",
		"PREVIEW_TEXT",
		"DETAIL_PICTURE",
		"DETAIL_TEXT",		
		"PROPERTIES",
		"PRODUCTS_IDS"
	)
);?>