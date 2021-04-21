<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arFileTmp = CFile::ResizeImageGet(
	$arResult["arUser"]["PERSONAL_PHOTO"],
	array("width" => 57, "height" => 57),
	BX_RESIZE_IMAGE_PROPORTIONAL,
	true
);

$arResult["arUser"]["PERSONAL_IMG"] = array(
	"SRC" => $arFileTmp["src"],
	'WIDTH' => $arFileTmp["width"],
	'HEIGHT' => $arFileTmp["height"],
);?>