<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arResult['ITEMS']){
	$arParams["POPUP_POSITION"] = (isset($arParams["POPUP_POSITION"]) && in_array($arParams["POPUP_POSITION"], array("left", "right"))) ? $arParams["POPUP_POSITION"] : "left";
	foreach($arResult["ITEMS"] as $key => $arItem)
	{
		if($arItem["CODE"]=="IN_STOCK"){
			sort($arResult["ITEMS"][$key]["VALUES"]);
			if($arResult["ITEMS"][$key]["VALUES"])
				$arResult["ITEMS"][$key]["VALUES"][0]["VALUE"]=$arItem["NAME"];
		}

		if(isset($arItem['PRICE']) && $arItem['PRICE']){
			if(isset($arItem['VALUES']['MIN']['HTML_VALUE']) || $arItem['VALUES']['MAX']['HTML_VALUE']){
				$arResult['PRICE_SET'] = 'Y';
				break;
			}
		}

		$i = 0;

		if($arItem['PROPERTY_TYPE'] == 'S' || $arItem['PROPERTY_TYPE'] == 'L' || $arItem['PROPERTY_TYPE'] == 'E'){
			foreach($arItem['VALUES'] as $arValue){
				if(isset($arValue['CHECKED']) && $arValue['CHECKED']){
					$arResult["ITEMS"][$key]['PROPERTY_SET'] = 'Y';
					++$i;
				}
			}

			if($i){
				$arResult["ITEMS"][$key]['COUNT_SELECTED'] = $i;
			}
		}

		if($arItem['PROPERTY_TYPE'] == 'N'){
			foreach($arItem['VALUES'] as $arValue){
				if(isset($arValue['HTML_VALUE']) && $arValue['HTML_VALUE']){
					$arResult['ITEMS'][$key]['PROPERTY_SET'] = 'Y';
				}
			}
		}
	}
}

\Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__);

// sort
include 'sort.php';

global $sotbitFilterResult;
$sotbitFilterResult = $arResult;