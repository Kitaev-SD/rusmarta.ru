<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?foreach($arResult["ITEMS"] as $key => $arItem) {
	if(is_array($arItem["DETAIL_PICTURE"])) {
		$arFileTmp = CFile::ResizeImageGet(
			$arItem["DETAIL_PICTURE"],
			array("width" => 65, "height" => 65),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);

		$arResult["ITEMS"][$key]["PICTURE"] = array(
			"SRC" => $arFileTmp["src"],
			'WIDTH' => $arFileTmp["width"],
			'HEIGHT' => $arFileTmp["height"],
		);
	}
}?>