<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arIBlock = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "FIELDS");
$arParams["DISPLAY_IMG_WIDTH"] = $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] ? $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] : 958;
$arParams["DISPLAY_IMG_HEIGHT"] = $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] ? $arIBlock["DETAIL_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] : 304;

//DISPLAY_DATE_CREATE//
if(!empty($arResult["DATE_CREATE"]))
	$arResult["DISPLAY_DATE_CREATE"] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arResult["DATE_CREATE"], CSite::GetDateFormat()));

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
}?>