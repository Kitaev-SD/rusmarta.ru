<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	
if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog"))
	return;

$arElement = CIBlockElement::GetList(
	array(), 
	array("ID" => $arParams["NOTIFY_ID"]), 
	false, 
	false, 
	array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
)->Fetch();

$arResult["NAME"] = $arElement["NAME"];

if($arElement["DETAIL_PICTURE"] > 0) {
	$arFileTmp = CFile::ResizeImageGet(
		$arElement["DETAIL_PICTURE"],
		array("width" => 178, "height" => 178),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true
	);		
	$arResult["PREVIEW_IMG"] = array(
		"SRC" => $arFileTmp["src"],
		"WIDTH" => $arFileTmp["width"],
		"HEIGHT" => $arFileTmp["height"],
	);
} else {
	$mxResult = CCatalogSku::GetProductInfo($arElement["ID"]);
	if(is_array($mxResult)) {
		$arElement = CIBlockElement::GetList(
			array(), 
			array("ID" => $mxResult["ID"]), 
			false, 
			false, 
			array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
		)->Fetch();
		if($arElement["DETAIL_PICTURE"] > 0) {
			$arFileTmp = CFile::ResizeImageGet(
				$arElement["DETAIL_PICTURE"],
				array("width" => 178, "height" => 178),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);		
			$arResult["PREVIEW_IMG"] = array(
				"SRC" => $arFileTmp["src"],
				"WIDTH" => $arFileTmp["width"],
				"HEIGHT" => $arFileTmp["height"],
			);
		}
	}
}?>