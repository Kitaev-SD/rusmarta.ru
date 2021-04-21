<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arIBlock = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "FIELDS");
$arParams["DISPLAY_IMG_WIDTH"] = $arIBlock["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] ? $arIBlock["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] : 208;
$arParams["DISPLAY_IMG_HEIGHT"] = $arIBlock["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] ? $arIBlock["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] : 140;

foreach($arResult["ITEMS"] as $key => $arItem) {
	//DISPLAY_ACTIVE_TO//
	if(!isset($arItem["DISPLAY_ACTIVE_TO"]) && !empty($arItem["ACTIVE_TO"]))
		$arResult["ITEMS"][$key]["DISPLAY_ACTIVE_TO"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_TO"], CSite::GetDateFormat()));
	
	//PREVIEW_PICTURE//
	if(is_array($arItem["PREVIEW_PICTURE"])) {						
		if($arItem["PREVIEW_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arItem["PREVIEW_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
			$arFileTmp = CFile::ResizeImageGet(
				$arItem["PREVIEW_PICTURE"],
				array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
			);
		}
	} elseif(is_array($arItem["DETAIL_PICTURE"])) {
		if($arItem["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arItem["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
			$arFileTmp = CFile::ResizeImageGet(
				$arItem["DETAIL_PICTURE"],
				array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
			);
		} else {
			$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = $arItem["DETAIL_PICTURE"];
		}
	}
}?>