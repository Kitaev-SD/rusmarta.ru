<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main;

$arSetting = CElektroinstrument::GetFrontParametrsValues(SITE_ID);

$this->setFrameMode(true);

if(isset($arResult['ITEM'])) {
	$inOldPrice = in_array("OLD_PRICE", $arParams["SETTING"]["PRODUCT_TABLE_VIEW"]);
	$inPercentPrice = in_array("PERCENT_PRICE", $arParams["SETTING"]["PRODUCT_TABLE_VIEW"]);
	$inArticle = in_array("ARTNUMBER", $arParams["SETTING"]["PRODUCT_TABLE_VIEW"]);
	$inRating = in_array("RATING", $arParams["SETTING"]["PRODUCT_TABLE_VIEW"]);
	$inPreviewText = in_array("PREVIEW_TEXT", $arParams["SETTING"]["PRODUCT_TABLE_VIEW"]);
	$inMinPrice = in_array("MIN_PRICE", $arParams["SETTING"]["PRODUCT_TABLE_VIEW"]);
	$inProductQnt = in_array("PRODUCT_QUANTITY", $arParams["SETTING"]["GENERAL_SETTINGS"]);
	$inPriceRatio = in_array("PRICE_RATIO", $arParams["SETTING"]["GENERAL_SETTINGS"]);

	$arElement = $arResult['ITEM'];
	$areaId = $arResult['AREA_ID'];
	$itemIds = array(
		'ID' => $areaId,
		'PRICE_RANGES_BTN' => $areaId.'_price_ranges_btn',
		'POPUP_BTN' => $areaId.'_popup_btn',
		'PROPS_BTN' => $areaId.'_props_btn',
		'BTN_BUY' => $areaId.'_btn_buy',
		'PRICE_MATRIX_BTN' => $areaId.'_price_ranges_btn',		
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
	$timeBuy = "";
	$class = "";
	if(array_key_exists("PROPERTIES", $arElement) && is_array($arElement["PROPERTIES"])) {
		//NEW//
		if(array_key_exists("NEWPRODUCT", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == false)
			$sticker .= "<span class='new'>".GetMessage("CT_BCS_BIGDATA_ELEMENT_NEWPRODUCT")."</span>";
		//HIT//
		if(array_key_exists("SALELEADER", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["SALELEADER"]["VALUE"] == false)
			$sticker .= "<span class='hit'>".GetMessage("CT_BCS_BIGDATA_ELEMENT_SALELEADER")."</span>";
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
		//TIME_BUY//
		if(array_key_exists("TIME_BUY", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
			if(!empty($arElement["CURRENT_DISCOUNT"]["ACTIVE_TO"])) {						
				if($haveOffers) {
					$class = " item-tb";
					$timeBuy = "<div class='time_buy_sticker'><span class='time_buy_figure'></span><span class='time_buy_text'>".GetMessage("CT_BCS_BIGDATA_ELEMENT_TIME_BUY")."</span></div>";
				} else {
					if($arElement["CAN_BUY"]) {
						$class = " item-tb";
						$timeBuy = "<div class='time_buy_sticker'><span class='time_buy_figure'></span><span class='time_buy_text'>".GetMessage("CT_BCS_BIGDATA_ELEMENT_TIME_BUY")."</span></div>";
					}
				}
			}
		}
	}
	
	//PRICE_MATRIX//
	if(count($arElement["PRICE_MATRIX_SHOW"]["COLS"]) > 1 && empty($arElement["CURRENT_DISCOUNT"]["ACTIVE_TO"])) {
		$class = " item-pm";
	}
	if($haveOffers) {
		if(count($arElement["TOTAL_OFFERS"]["PRICE_MATRIX_SHOW"]["COLS"]) > 1 && empty($arElement["CURRENT_DISCOUNT"]["ACTIVE_TO"])) {
			$class = " item-pm";
		}
	}
	
	//PREVIEW_PICTURE_ALT//
	$strAlt = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arElement["NAME"]);

	//PREVIEW_PICTURE_TITLE//
	$strTitle = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arElement["NAME"]);?>

	<div class="catalog-item-card<?=$class?>" id="<?=$areaId?>" data-entity="item">
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
					"PRICE_RANGES_BTN_ID" => $itemIds["PRICE_RANGES_BTN"],
					"PROPS_BTN_ID" => $itemIds["PROPS_BTN"],
					"PRICE_MATRIX_BTN_ID" => is_array($arResult["ITEM"]["ID_PRICE_MATRIX_BTN"]) ? $arResult["ITEM"]["ID_PRICE_MATRIX_BTN"] : ""
				),
				"PRODUCT" => array(
					"ID" => $arElement['ID'],
					"ITEM_PRICE_MODE" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICE_MODE"] : $arElement["ITEM_PRICE_MODE"],
					"ITEM_PRICES" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICES"] : $arElement["ITEM_PRICES"],
					"ITEM_PRICE_SELECTED" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICE_SELECTED"] : $arElement["ITEM_PRICE_SELECTED"],
					"ITEM_QUANTITY_RANGES" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_QUANTITY_RANGES"] : $arElement["ITEM_QUANTITY_RANGES"],	
					"CHECK_QUANTITY" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CHECK_QUANTITY"] : $arElement["CHECK_QUANTITY"],
					"QUANTITY_FLOAT" => $haveOffers ? is_double($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]) : is_double($arElement["CATALOG_MEASURE_RATIO"]),
					"MAX_QUANTITY" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_QUANTITY"] : $arElement["CATALOG_QUANTITY"],
					"STEP_QUANTITY" => $haveOffers ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : $arElement["CATALOG_MEASURE_RATIO"],
					"PRICE_MATRIX" =>  $haveOffers ? $arElement["TOTAL_OFFERS"]["PRICE_MATRIX_SHOW"]["MATRIX"] : $arElement["PRICE_MATRIX_SHOW"]["MATRIX"],
					"PRINT_CURRENCY" => $currency
				)
			);
			if($haveOffers)
				$jsParams["OFFER"]["ID"] = $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"];
			if($arElement["SELECT_PROPS"])
				$jsParams["VISUAL"]["POPUP_BTN_ID"] = $itemIds["POPUP_BTN"];
		} else {
			$jsParams = array(					
				"VISUAL" => array(
					"ID" => $itemIds["ID"],
					"PRICE_RANGES_BTN_ID" => $itemIds["PRICE_RANGES_BTN"],
					"POPUP_BTN_ID" => $itemIds["POPUP_BTN"],
					"BTN_BUY_ID" => $itemIds["BTN_BUY"],
					"PRICE_MATRIX_BTN_ID" => is_array($arResult["ITEM"]["ID_PRICE_MATRIX_BTN"]) ? $arResult["ITEM"]["ID_PRICE_MATRIX_BTN"] : ""
				),
				"PRODUCT" => array(
					"ID" => $arElement["ID"],
					"NAME" => $arElement["NAME"],
					"PICT" => is_array($arElement["PREVIEW_PICTURE"]) ? $arElement["PREVIEW_PICTURE"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
					"ITEM_PRICE_MODE" => $arElement["ITEM_PRICE_MODE"],
					"ITEM_PRICES" => $arElement["ITEM_PRICES"],
					"ITEM_PRICE_SELECTED" => $arElement["ITEM_PRICE_SELECTED"],
					"ITEM_QUANTITY_RANGES" => $arElement["ITEM_QUANTITY_RANGES"],
					"CHECK_QUANTITY" => $arElement["CHECK_QUANTITY"],						
					"QUANTITY_FLOAT" => is_double($arElement["CATALOG_MEASURE_RATIO"]),
					"MAX_QUANTITY" => $arElement["CATALOG_QUANTITY"],
					"STEP_QUANTITY" => $arElement["CATALOG_MEASURE_RATIO"],
					"PRICE_MATRIX" => $arElement["PRICE_MATRIX_SHOW"]["MATRIX"],
					"PRINT_CURRENCY" => $currency
				)
			);
		}?>
		<script>
			var <?=$obName?> = new JCCatalogBigdataItem(<?=CUtil::PhpToJSObject($jsParams, false, true);?>);
		</script>
	</div>
	<?unset($arElement, $itemIds, $jsParams);
}