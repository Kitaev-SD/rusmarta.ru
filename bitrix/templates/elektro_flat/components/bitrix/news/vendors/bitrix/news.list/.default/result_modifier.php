<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["ITEMS"] as $key => $arItem) {
	//PREVIEW_PICTURE//
	if(is_array($arItem["PREVIEW_PICTURE"])) {
		if($arItem["PREVIEW_PICTURE"]["WIDTH"] > 69 || $arItem["PREVIEW_PICTURE"]["HEIGHT"] > 24) {
			$arFileTmp = CFile::ResizeImageGet(
				$arItem["PREVIEW_PICTURE"],
				array("width" => 69, "height" => 24),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true, $arFilter
			);
			$arResult["ITEMS"][$key]["PREVIEW_PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
			);
		}
	} elseif(is_array($arItem["DETAIL_PICTURE"])) {
		if($arItem["DETAIL_PICTURE"]["WIDTH"] > 69 || $arItem["DETAIL_PICTURE"]["HEIGHT"] > 24) {
			$arFileTmp = CFile::ResizeImageGet(
				$arItem["DETAIL_PICTURE"],
				array("width" => 69, "height" => 24),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true, $arFilter
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