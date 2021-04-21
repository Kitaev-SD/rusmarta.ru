<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arIBlock = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "FIELDS");
$arParams["DISPLAY_IMG_WIDTH"] = $arIBlock["SECTION_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] ? $arIBlock["SECTION_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] : 50;
$arParams["DISPLAY_IMG_HEIGHT"] = $arIBlock["SECTION_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] ? $arIBlock["SECTION_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] : 50;

foreach($arResult["SECTIONS"] as $key => $arSection) {	
	if(is_array($arSection["PICTURE"])) {
		if($arSection["PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arSection["PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
			$arFileTmp = CFile::ResizeImageGet(
				$arSection["PICTURE"],
				array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult["SECTIONS"][$key]["PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
			);
		}
	}
}?>