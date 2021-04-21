<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;

$this->setFrameMode(true);

$arSetting = CElektroinstrument::GetFrontParametrsValues(SITE_ID);

if(isset($arResult['ITEM'])) {
	$inOldPrice = in_array("OLD_PRICE", $arParams["SETTING"]["PRODUCT_TABLE_VIEW"]);
	$inPercentPrice = in_array("PERCENT_PRICE", $arParams["SETTING"]["PRODUCT_TABLE_VIEW"]);
	$inArticle = in_array("ARTNUMBER", $arParams["SETTING"]["PRODUCT_TABLE_VIEW"]);
	$inRating = in_array("RATING", $arParams["SETTING"]["PRODUCT_TABLE_VIEW"]);
	$inPreviewText = in_array("PREVIEW_TEXT", $arParams["SETTING"]["PRODUCT_TABLE_VIEW"]);	
	$inProductQnt = in_array("PRODUCT_QUANTITY", $arParams["SETTING"]["GENERAL_SETTINGS"]);
	$inPriceRatio = in_array("PRICE_RATIO", $arParams["SETTING"]["GENERAL_SETTINGS"]);

	$arElement = $arResult['ITEM'];	
	$areaId = $arResult['AREA_ID'];
	$itemIds = array(
		'ID' => $areaId,		
		'POPUP_BTN' => $areaId.'_popup_btn',
		'PROPS_BTN' => $areaId.'_props_btn',
		'BTN_BUY' => $areaId.'_btn_buy'
	);
	$obName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $areaId);

	$haveOffers = !empty($arElement['OFFERS']);

	//CURRENCY_FORMAT//
	$arCurFormat = $currency = false;
	if($haveOffers) {
		$arCurFormat = CCurrencyLang::GetCurrencyFormat($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
		if($arCurFormat["HIDE_ZERO"] == "Y")
			if(round($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"]) == round($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], 0))
				$arCurFormat["DECIMALS"] = 0;
	} else {
		$arCurFormat = CCurrencyLang::GetCurrencyFormat($arElement["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
		if($arCurFormat["HIDE_ZERO"] == "Y")
			if(round($arElement["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"]) == round($arElement["MIN_PRICE"]["RATIO_PRICE"], 0))
				$arCurFormat["DECIMALS"] = 0;
	}
	if(empty($arCurFormat["THOUSANDS_SEP"]))
		$arCurFormat["THOUSANDS_SEP"] = " ";
	$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);

	//NEW_HIT_DISCOUNT_TIME_BUY//
	$sticker = "";
	if(array_key_exists("PROPERTIES", $arElement) && is_array($arElement["PROPERTIES"])) {
		//NEW//
		if(array_key_exists("NEWPRODUCT", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == false)
			$sticker .= "<span class='new'>".GetMessage("CT_SPG_ELEMENT_NEWPRODUCT")."</span>";
		//HIT//
		if(array_key_exists("SALELEADER", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["SALELEADER"]["VALUE"] == false)
			$sticker .= "<span class='hit'>".GetMessage("CT_SPG_ELEMENT_SALELEADER")."</span>";
		//DISCOUNT//				
		if($haveOffers) {						
			if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PERCENT"] > 0)
				$sticker .= "<span class='discount'>-".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PERCENT"]."%</span>";
			else
				if(array_key_exists("DISCOUNT", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["DISCOUNT"]["VALUE"] == false)
					$sticker .= "<span class='discount'>%</span>";
		} else {
			if($arElement["MIN_PRICE"]["PERCENT"] > 0)
				$sticker .= "<span class='discount'>-".$arElement["MIN_PRICE"]["PERCENT"]."%</span>";
			else
				if(array_key_exists("DISCOUNT", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["DISCOUNT"]["VALUE"] == false)
					$sticker .= "<span class='discount'>%</span>";
		}
	}

	//PREVIEW_PICTURE_ALT//
	$strAlt = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arElement["NAME"]);

	//PREVIEW_PICTURE_TITLE//
	$strTitle = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arElement["NAME"]);?>

	<div class="catalog-item-card" id="<?=$areaId?>" data-entity="item">
		<?$documentRoot = Main\Application::getDocumentRoot();
		$templatePath = strtolower($arResult['TYPE']).'/template.php';
		$file = new Main\IO\File($documentRoot.$templateFolder.'/'.$templatePath);
		if($file->isExists()) {
			include($file->getPath());
		}

		if($haveOffers || $arElement["SELECT_PROPS"]) {
			$jsParams = array(					
				"VISUAL" => array(
					"ID" => $itemIds["ID"],					
					"PROPS_BTN_ID" => $itemIds["PROPS_BTN"]
				),
				"PRODUCT" => array(
					"ID" => $arElement['ID'],
					"ITEM_PRICE_MODE" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICE_MODE"] : $arElement["ITEM_PRICE_MODE"],
					"ITEM_PRICES" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICES"] : $arElement["ITEM_PRICES"],
					"ITEM_PRICE_SELECTED" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICE_SELECTED"] : $arElement["ITEM_PRICE_SELECTED"],
					"CHECK_QUANTITY" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CHECK_QUANTITY"] : $arElement["CHECK_QUANTITY"],
					"QUANTITY_FLOAT" => $haveOffers ? is_double($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_MEASURE_RATIO"]) : is_double($arElement["ITEM_MEASURE_RATIOS"][$arElement["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"]),
					"MAX_QUANTITY" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_MEASURE_RATIO"] : $arElement["ITEM_MEASURE_RATIOS"][$arElement["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"],
					"STEP_QUANTITY" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_MEASURE_RATIO"] : $arElement["ITEM_MEASURE_RATIOS"][$arElement["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"],
					"PRINT_CURRENCY" => $currency
				)
			);
			$signer = new \Bitrix\Main\Security\Sign\Signer;
			if($haveOffers) {
				$jsParams["OFFER"]["ID"] = $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"];
				$jsParams["OFFERS_DEF"] = $signer->sign(base64_encode(serialize($arElement["OFFERS_DEF"])), "sale.products.gift");
			}
			if($arElement["SELECT_PROPS"]) {
				$jsParams["VISUAL"]["POPUP_BTN_ID"] = $itemIds["POPUP_BTN"];
				$jsParams["PRODUCT"]["ITEM_PRICES_DEF"] = $signer->sign(base64_encode(serialize($arElement["ITEM_PRICES_DEF"])), "sale.products.gift");
			}
		} else {
			$jsParams = array(					
				"VISUAL" => array(
					"ID" => $itemIds["ID"],					
					"POPUP_BTN_ID" => $itemIds["POPUP_BTN"],
					"BTN_BUY_ID" => $itemIds["BTN_BUY"]
				),
				"PRODUCT" => array(
					"ID" => $arElement["ID"],
					"NAME" => $arElement["NAME"],
					"PICT" => is_array($arElement["PREVIEW_PICTURE"]) ? $arElement["PREVIEW_PICTURE"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
					"ITEM_PRICE_MODE" => $arElement["ITEM_PRICE_MODE"],
					"ITEM_PRICES" => $arElement["ITEM_PRICES"],
					"ITEM_PRICE_SELECTED" => $arElement["ITEM_PRICE_SELECTED"],					
					"CHECK_QUANTITY" => $arElement["CHECK_QUANTITY"],						
					"QUANTITY_FLOAT" => is_double($arElement["ITEM_MEASURE_RATIOS"][$arElement["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"]),
					"MAX_QUANTITY" => $arElement["ITEM_MEASURE_RATIOS"][$arElement["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"],
					"STEP_QUANTITY" => $arElement["ITEM_MEASURE_RATIOS"][$arElement["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"],
					"PRINT_CURRENCY" => $currency
				)
			);
		}?>
		<script>
			var <?=$obName?> = new JCCatalogGiftItem(<?=CUtil::PhpToJSObject($jsParams, false, true);?>);
		</script>
	</div>
	<?unset($arElement, $itemIds, $jsParams);
}