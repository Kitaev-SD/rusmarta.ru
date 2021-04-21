<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $arSetting;

$arParams["DISPLAY_IMG_WIDTH"] = 160;
$arParams["DISPLAY_IMG_HEIGHT"] = 160;

$arNewItems = array();
foreach($arResult["ITEMS"] as $key => $arElement) {	
	//STR_MAIN_ID//
	$arElement["STR_MAIN_ID"] = $this->GetEditAreaId($arElement["ID"]);

	//PREVIEW_PICTURE//	
	if(is_array($arElement["PREVIEW_PICTURE"])) {
		if($arElement["PREVIEW_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arElement["PREVIEW_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
			$arFileTmp = CFile::ResizeImageGet(
				$arElement["PREVIEW_PICTURE"],
				array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arElement["PREVIEW_PICTURE"] = array(
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
			$arElement["PREVIEW_PICTURE"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"]
			);
		} else {
			$arElement["PREVIEW_PICTURE"] = $arElement["DETAIL_PICTURE"];
		}
	}	
	
	//NEW_ITEMS//
	if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {		
		//OFFERS//
		$newOfferProps = array();
		foreach($arElement["OFFERS"] as $keyOffer => $arOffer) {
			if(!array_key_exists($arOffer["ID"], $arParams["LIST_SUBSCRIPTIONS"]))
				continue;
			
			//STR_MAIN_ID//
			$arOffer["STR_MAIN_ID"] = $this->GetEditAreaId($arOffer["ID"]);
			
			//DETAIL_PAGE_URL//
			$arOffer["DETAIL_PAGE_URL"] = $arElement["DETAIL_PAGE_URL"];
			
			if(in_array("OFFERS_LINK_SHOW", $arSetting["GENERAL_SETTINGS"]["VALUE"])) {
				$arOffer["DETAIL_PAGE_URL"] .= "?offer=".$arOffer["ID"];
			}
			
			//PREVIEW_PICTURE//
			if(is_array($arOffer["PREVIEW_PICTURE"])) {
				if($arOffer["PREVIEW_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arOffer["PREVIEW_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
					$arFileTmp = CFile::ResizeImageGet(
						$arOffer["PREVIEW_PICTURE"],
						array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$arOffer["PREVIEW_PICTURE"] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"]
					);
				}
			} elseif(is_array($arOffer["DETAIL_PICTURE"])) {
				if($arOffer["DETAIL_PICTURE"]["WIDTH"] > $arParams["DISPLAY_IMG_WIDTH"] || $arOffer["DETAIL_PICTURE"]["HEIGHT"] > $arParams["DISPLAY_IMG_HEIGHT"]) {
					$arFileTmp = CFile::ResizeImageGet(
						$arOffer["DETAIL_PICTURE"],
						array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$arOffer["PREVIEW_PICTURE"] = array(
						"SRC" => $arFileTmp["src"],
						"WIDTH" => $arFileTmp["width"],
						"HEIGHT" => $arFileTmp["height"]
					);
				} else {
					$arOffer["PREVIEW_PICTURE"] = $arOffer["DETAIL_PICTURE"];
				}
			} elseif(is_array($arElement["PREVIEW_PICTURE"])) {
				$arOffer["PREVIEW_PICTURE"] = $arElement["PREVIEW_PICTURE"];
			}

			//DISPLAY_PROPERTIES//			
			if(!empty($arParams["OFFER_TREE_PROPS"][$arElement["ID"]])) {				
				foreach($arParams["OFFER_TREE_PROPS"][$arElement["ID"]] as $propCode)
					$newOfferProps[$propCode] = $arOffer["DISPLAY_PROPERTIES"][$propCode];
			}
			$arOffer["DISPLAY_PROPERTIES"] = $newOfferProps;
			
			$arNewItems[$arOffer["ID"]] = $arOffer;
		}
	} else {
		$arNewItems[$arElement["ID"]] = $arElement;
	}
}
$arResult["ITEMS"] = $arNewItems;?>