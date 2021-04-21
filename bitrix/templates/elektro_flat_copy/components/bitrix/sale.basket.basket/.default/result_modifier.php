<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock")  || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("sale"))
	return;

global $arSetting;

//OFFERS_IBLOCK//
$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams["IBLOCK_ID"]);
$arResult["OFFERS_IBLOCK"] = is_array($arSKU) ? $arSKU["IBLOCK_ID"] : 0;

//BASKET_ITEMS//
if(is_array($arResult["ITEMS"]["AnDelCanBuy"])) {
	foreach($arResult["ITEMS"]["AnDelCanBuy"] as $key => $arItem) {		
		$ar = CIBlockElement::GetList(
			array(), 
			array("ID" => $arItem["PRODUCT_ID"]), 
			false, 
			false, 
			array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
		)->Fetch();		
		if($ar["DETAIL_PICTURE"] > 0) {
			$arResult["ITEMS"]["AnDelCanBuy"][$key]["DETAIL_PICTURE"] = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array("width" => 65, "height" => 65), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		} else {
			$mxResult = CCatalogSku::GetProductInfo($ar["ID"]);
			if(is_array($mxResult)) {
				$ar = CIBlockElement::GetList(
					array(), 
					array("ID" => $mxResult["ID"]), 
					false, 
					false, 
					array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
				)->Fetch();
				if($ar["DETAIL_PICTURE"] > 0) {
					$arResult["ITEMS"]["AnDelCanBuy"][$key]["DETAIL_PICTURE"] = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array("width" => 65, "height" => 65), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				}
			}
		}		

		//MEASURE_RATIO//
		if(!isset($arItem["MEASURE_RATIO"]))
		$arResult["ITEMS"]["AnDelCanBuy"][$key]["MEASURE_RATIO"] = 1;				
		
		//CART_ACCESSORIES//
		$mxResult = CCatalogSku::GetProductInfo($arItem["PRODUCT_ID"]);
		if(!empty($mxResult["ID"])):
			$PARENT_PRODUCT_ID = $mxResult["ID"];
		else:
			$PARENT_PRODUCT_ID = $arItem["PRODUCT_ID"];
		endif;
		
		$arResult["ITEMS"]["PARENT_PRODUCT_IDS"][] = $PARENT_PRODUCT_ID;

		$arr_access = CIBlockElement::GetList(
			Array("sort"=>"asc"), 
			Array("ACTIVE"=>"Y", "ID" => $PARENT_PRODUCT_ID), 
			false, 
			false, 
			Array("PROPERTY_ACCESSORIES")
		);
		
		while($arr_acces = $arr_access->GetNextElement()) {
			$arElement = $arr_acces->GetFields();
			
			if(!empty($arElement["PROPERTY_ACCESSORIES_VALUE"])):
				$arResult["ITEMS"]["ACCESSORIES"][] = $arElement["PROPERTY_ACCESSORIES_VALUE"];
			endif;
		}

		if(in_array("OFFERS_LINK_SHOW", $arSetting["GENERAL_SETTINGS"]["VALUE"]) && is_array($mxResult)) {
			$arResult["ITEMS"]["AnDelCanBuy"][$key]["DETAIL_PAGE_URL"] .= "?offer=".$arItem["PRODUCT_ID"];
		}
		
		//CURRENCY_FORMAT//
		$arCurFormat = false;
		$arCurFormat = CCurrencyLang::GetCurrencyFormat($arItem["CURRENCY"], LANGUAGE_ID);
		if(empty($arCurFormat["THOUSANDS_SEP"])):
			$arCurFormat["THOUSANDS_SEP"] = " ";
		endif;		

		$arResult["ITEMS"]["AnDelCanBuy"][$key]["itemReference_DECIMALS"] = $arCurFormat["DECIMALS"];
		$arResult["ITEMS"]["AnDelCanBuy"][$key]["itemSum_DECIMALS"] = $arCurFormat["DECIMALS"];
		$arResult["ITEMS"]["AnDelCanBuy"][$key]["itemReferenceSum_DECIMALS"] = $arCurFormat["DECIMALS"];
		if($arCurFormat["HIDE_ZERO"] == "Y"):
			if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):
				if(round($arItem["PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arCurFormat["DECIMALS"]) == round($arItem["PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], 0)):
					$arResult["ITEMS"]["AnDelCanBuy"][$key]["itemReference_DECIMALS"] = 0;													
				endif;
			endif;			
			if(round($arItem["PRICE"] * $arItem["QUANTITY"], $arCurFormat["DECIMALS"]) == round($arItem["PRICE"] * $arItem["QUANTITY"], 0)):
				$arResult["ITEMS"]["AnDelCanBuy"][$key]["itemSum_DECIMALS"] = 0;
			endif;
			if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):
				if(round($arItem["PRICE"] * $arItem["QUANTITY"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arCurFormat["DECIMALS"]) == round($arItem["PRICE"] * $arItem["QUANTITY"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], 0)):
					$arResult["ITEMS"]["AnDelCanBuy"][$key]["itemReferenceSum_DECIMALS"] = 0;													
				endif;
			endif;
		endif;
		$arResult["ITEMS"]["AnDelCanBuy"][$key]["item_HIDE_ZERO"] = $arCurFormat["HIDE_ZERO"];
		$arResult["ITEMS"]["AnDelCanBuy"][$key]["item_DEC_POINT"] = $arCurFormat["DEC_POINT"];
		$arResult["ITEMS"]["AnDelCanBuy"][$key]["item_THOUSANDS_SEP"] = $arCurFormat["THOUSANDS_SEP"];

		$arResult["ITEMS"]["AnDelCanBuy"][$key]["item_CURRENCY"] = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
		
		unset($arCurFormat);
	}	
	
	//ALL_SUM_CURRENCY_FORMAT//
	$arCurFormat = false;
	$arCurFormat = CCurrencyLang::GetCurrencyFormat(CSaleLang::GetLangCurrency(SITE_ID), LANGUAGE_ID);
	if(empty($arCurFormat["THOUSANDS_SEP"])):
		$arCurFormat["THOUSANDS_SEP"] = " ";
	endif;
	
	$arResult["allSum_DECIMALS"] = $arCurFormat["DECIMALS"];
	$arResult["allReferenceSum_DECIMALS"] = $arCurFormat["DECIMALS"];
	if($arCurFormat["HIDE_ZERO"] == "Y"):		
		if(round($arResult["allSum"], $arCurFormat["DECIMALS"]) == round($arResult["allSum"], 0)):
			$arResult["allSum_DECIMALS"] = 0;
		endif;
		if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):
			if(round($arResult["allSum"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arCurFormat["DECIMALS"]) == round($arResult["allSum"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], 0)):
				$arResult["allReferenceSum_DECIMALS"] = 0;													
			endif;
		endif;
	endif;
	$arResult["allSum_HIDE_ZERO"] = $arCurFormat["HIDE_ZERO"];
	$arResult["allSum_DEC_POINT"] = $arCurFormat["DEC_POINT"];
	$arResult["allSum_THOUSANDS_SEP"] = $arCurFormat["THOUSANDS_SEP"];
	
	$arResult["allSum_CURRENCY"] = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);

	unset($arCurFormat);
}

//CLEAR_BASKET_ITEMS//
if(isset($_REQUEST["BasketClear"]) && $_REQUEST["BasketClear"] == "Y") {	
	if(is_array($arResult["ITEMS"]["AnDelCanBuy"])) {
		foreach($arResult["ITEMS"]["AnDelCanBuy"] as $key => $arItem) {
			CSaleBasket::Delete($arItem["ID"]);
		}
		
		LocalRedirect($APPLICATION->GetCurPage());
	}
}

//DELAY_ITEMS//
if(is_array($arResult["ITEMS"]["DelDelCanBuy"])) {
	foreach($arResult["ITEMS"]["DelDelCanBuy"] as $key => $arItem) {
		$ar = CIBlockElement::GetList(
			array(), 
			array("ID" => $arItem["PRODUCT_ID"]), 
			false, 
			false, 
			array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
		)->Fetch();		
		if($ar["DETAIL_PICTURE"] > 0) {
			$arResult["ITEMS"]["DelDelCanBuy"][$key]["DETAIL_PICTURE"] = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array("width" => 65, "height" => 65), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		} else {
			$mxResult = CCatalogSku::GetProductInfo($ar["ID"]);
			if(is_array($mxResult)) {
				$ar = CIBlockElement::GetList(
					array(), 
					array("ID" => $mxResult["ID"]), 
					false, 
					false, 
					array("ID", "IBLOCK_ID", "DETAIL_PICTURE")
				)->Fetch();
				if($ar["DETAIL_PICTURE"] > 0) {
					$arResult["ITEMS"]["DelDelCanBuy"][$key]["DETAIL_PICTURE"] = CFile::ResizeImageGet($ar["DETAIL_PICTURE"], array("width" => 65, "height" => 65), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				}
			}
		}
		
		if(in_array("OFFERS_LINK_SHOW", $arSetting["GENERAL_SETTINGS"]["VALUE"]) && is_array(CCatalogSku::GetProductInfo($arItem["PRODUCT_ID"]))) {
			$arResult["ITEMS"]["DelDelCanBuy"][$key]["DETAIL_PAGE_URL"] .= "?offer=".$arItem["PRODUCT_ID"];
		}
	}
}

//CLEAR_DELAY_ITEMS//
if(isset($_REQUEST["DelayClear"]) && $_REQUEST["DelayClear"] == "Y") {	
	if(is_array($arResult["ITEMS"]["DelDelCanBuy"])) {
		foreach($arResult["ITEMS"]["DelDelCanBuy"] as $key => $arItem) {
			CSaleBasket::Delete($arItem["ID"]);
		}
		
		LocalRedirect($APPLICATION->GetCurPage());
	}
}?>