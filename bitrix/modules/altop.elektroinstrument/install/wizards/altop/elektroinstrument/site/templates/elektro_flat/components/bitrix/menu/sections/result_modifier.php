<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if(count($arResult) < 1)
	return;

foreach($arResult as $key => $arItem) {	
	if($arItem["DEPTH_LEVEL"] == 2) {
		if($arItem["PARAMS"]["PICTURE"] > 0) {		
			$arFileTmp = CFile::ResizeImageGet(
				$arItem["PARAMS"]["PICTURE"],
				array("width" => 50, "height" => 50),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arResult[$key]["PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
			);
		}
	}
}

//SELECTED_ITEM//
if($arParams["CACHE_SELECTED_ITEMS"] != "Y") {
	$items = array();
	$selectedItem = false;
	foreach($arResult as $arItem) {
		$items[] = $arItem;		
		if($arItem["SELECTED"]) {
			$selectedItem = true;
			break;
		}
	}
	unset($arItem);
	
	if($selectedItem) {
		krsort($items);
		
		foreach($items as $arItem) {
			if($arItem["DEPTH_LEVEL"] == 1) {
				$arResult[$arItem["ITEM_INDEX"]]["SELECTED"] = true;
				break;
			}
		}
		unset($arItem, $items);
	}
	unset($selectedItem);
}?>