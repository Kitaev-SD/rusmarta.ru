<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;

if(!isset($arParams['SLIDER_AUTOPLAY']) || $arParams['SLIDER_AUTOPLAY'] != 'N')
	$arParams['SLIDER_AUTOPLAY'] = 'Y';

if(!isset($arParams['SLIDER_DELAY']) || empty($arParams['SLIDER_DELAY']) || !is_numeric($arParams['SLIDER_DELAY']))
	$arParams['SLIDER_DELAY'] = 3000;
else
	$arParams['SLIDER_DELAY'] = intval($arParams['SLIDER_DELAY']);

if(!isset($arParams['SLIDER_ASPECT_RATIO']) || empty($arParams['SLIDER_ASPECT_RATIO']))
	$arParams['SLIDER_ASPECT_RATIO'] = 'DEFAULT';

$arSlideHight = array(
	'DEFAULT' => 304,
	'16_7' => 419,
	'16_9' => 538
);

$arParam['SLIDER_HEIGHT'] = $arSlideHight[$arParams['SLIDER_ASPECT_RATIO']];

$arResult['IN_VIDEO'] = false;

foreach($arResult["ITEMS"] as $key => $arItem) {
	if(is_array($arItem["PREVIEW_PICTURE"])) {
		$arFilter = '';
						
		$arFileTmp = CFile::ResizeImageGet(
			$arItem["PREVIEW_PICTURE"],
			array("width" => 958, "height" => $arParam['SLIDER_HEIGHT']),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);

		$arResult["ITEMS"][$key]['PICTURE_PREVIEW'] = array(
			'SRC' => $arFileTmp["src"],
			'WIDTH' => $arFileTmp["width"],
			'HEIGHT' => $arFileTmp["height"],
		);
	}
	
	foreach($arItem['PROPERTIES'] as $keyProp => $arProp) {
		if(!empty($arProp['VALUE']) && $keyProp == 'CODE_YOUTUBE') {
			$arResult['IN_VIDEO'] = true;
			break;
		}
	}
}

$this->__component->SetResultCacheKeys(
	array(
		"IN_VIDEO"
	)
);