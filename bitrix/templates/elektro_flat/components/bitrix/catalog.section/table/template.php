<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(count($arResult["ITEMS"]) < 1) {
	echo "<br />";
	ShowNote(GetMessage("CATALOG_EMPTY_RESULT"), "infotext");
	return;
}

$curPage = $APPLICATION->GetCurPage();

global $arSetting;
$inOldPrice = in_array("OLD_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inPercentPrice = in_array("PERCENT_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inArticle = in_array("ARTNUMBER", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inRating = in_array("RATING", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inPreviewText = in_array("PREVIEW_TEXT", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inProductQnt = in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"]);
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);

//JS//?>
<script type="text/javascript">
	//<![CDATA[
	BX.ready(function() {
		//ITEMS_HEIGHT//
		var itemsTable = $(".catalog-item-table-view .catalog-item-card");
		if(!!itemsTable && itemsTable.length > 0) {
			$(window).resize(function() {
				adjustItemHeight(itemsTable);
			});
			adjustItemHeight(itemsTable);
		}
	});
	//]]>
</script>

<?//CATALOG//?>
<div id="catalog" itemtype="http://schema.org/ItemList">
	<link href="<?=$curPage?>" itemprop="url" />
	<div class="catalog-item-table-view">
		<?foreach($arResult["ITEMS"] as $key => $arElement) {
			$arItemIDs = array(
				"ID" => $arElement["STR_MAIN_ID"],
				"PRICE_RANGES_BTN" => $arElement["STR_MAIN_ID"]."_price_ranges_btn",
				"POPUP_BTN" => $arElement["STR_MAIN_ID"]."_popup_btn",
				"PROPS_BTN" => $arElement["STR_MAIN_ID"]."_props_btn",
				"BTN_BUY" => $arElement["STR_MAIN_ID"]."_btn_buy",
				"PRICE_MATRIX_BTN" => $arElement["STR_MAIN_ID"]."_price_ranges_btn"
			);

			//CURRENCY_FORMAT//
			$arCurFormat = $currency = false;
			if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
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
				//FREEDOST//
				if(array_key_exists("FREEDOST", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["FREEDOST"]["VALUE"] == false):
					$sticker .= "<span class='freedost'>".GetMessage("CATALOG_ELEMENT_FREEDOST")."</span>";
				endif;
				//???????????????????????? 1 ?????????//
				if(array_key_exists("GARANT1", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["GARANT1"]["VALUE"] == false):
					$sticker .= "<span class='gar1'>".GetMessage("CATALOG_ELEMENT_GARANT1")."</span>";
				endif;
				//???????????????????????? 3 ????????????//
				if(array_key_exists("GARANT3", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["GARANT3"]["VALUE"] == false):
					$sticker .= "<span class='gar3'>".GetMessage("CATALOG_ELEMENT_GARANT3")."</span>";
				endif;
				//POE//
				if(array_key_exists("POE", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["POE"]["VALUE"] == false):
					$sticker .= "<span class='poe'>".GetMessage("CATALOG_ELEMENT_POE")."</span>";
				endif;
				//SEN//
				if(array_key_exists("SEN", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["SEN"]["VALUE"] == false):
					$sticker .= "<span class='sen'>".GetMessage("CATALOG_ELEMENT_SEN")."</span>";
				endif;
				//AUTO//
				if(array_key_exists("AUTO", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["AUTO"]["VALUE"] == false):
					$sticker .= "<span class='auto'>".GetMessage("CATALOG_ELEMENT_AUTO")."</span>";
				endif;
				//sticker_avtonom//
				if(array_key_exists("sticker_avtonom", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["sticker_avtonom"]["VALUE"] == false):
					$sticker .= "<span class='sticker_avtonom'>".GetMessage("CATALOG_ELEMENT_sticker_avtonom")."</span>";
				endif;
				//Sticker_ZigBee//
				if(array_key_exists("sticker_zb", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["sticker_zb"]["VALUE"] == false):
					$sticker .= "<span class='sticker_zb'>".GetMessage("CATALOG_ELEMENT_sticker_zb")."</span>";
				endif;
				//Sticker_AHD//
				if(array_key_exists("sticker_ahd", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["sticker_ahd"]["VALUE"] == false):
					$sticker .= "<span class='sticker_ahd'>".GetMessage("CATALOG_ELEMENT_sticker_ahd")."</span>";
				endif;
				//Sticker_IP//
				if(array_key_exists("sticker_ip", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["sticker_ip"]["VALUE"] == false):
					$sticker .= "<span class='sticker_ip'>".GetMessage("CATALOG_ELEMENT_sticker_ip")."</span>";
				endif;
				//Sticker_WIFI//
				if(array_key_exists("sticker_wifi", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["sticker_wifi"]["VALUE"] == false):
					$sticker .= "<span class='sticker_wifi'>".GetMessage("CATALOG_ELEMENT_sticker_wifi")."</span>";
				endif;
				//Sticker_LTE//
				if(array_key_exists("sticker_lte", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["sticker_lte"]["VALUE"] == false):
					$sticker .= "<span class='sticker_lte'>".GetMessage("CATALOG_ELEMENT_sticker_lte")."</span>";
				endif;
				//Sticker_GSM//
				if(array_key_exists("sticker_gsm", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["sticker_gsm"]["VALUE"] == false):
					$sticker .= "<span class='sticker_gsm'>".GetMessage("CATALOG_ELEMENT_sticker_gsm")."</span>";
				endif;
				//SPEZ5//
				if(array_key_exists("SPEZ5MP", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["SPEZ5MP"]["VALUE"] == false):
					$sticker .= "<span class='spez5'>".GetMessage("CATALOG_ELEMENT_SPEZ5MP")."</span>";
				endif;
				//SPEZ8MP//
				if(array_key_exists("SPEZ8MP", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["SPEZ8MP"]["VALUE"] == false):
					$sticker .= "<span class='spez8mp'>".GetMessage("CATALOG_ELEMENT_SPEZ8MP")."</span>";
				endif;
				//NEW//
				if(array_key_exists("NEWPRODUCT", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == false)
					$sticker .= "<span class='new'>".GetMessage("CATALOG_ELEMENT_NEWPRODUCT")."</span>";				
				//HIT//
				if(array_key_exists("SALELEADER", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["SALELEADER"]["VALUE"] == false)
					$sticker .= "<span class='hit'>".GetMessage("CATALOG_ELEMENT_SALELEADER")."</span>";
				//SPEZ//
				if(array_key_exists("SPEZPEDLOG", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["SPEZPEDLOG"]["VALUE"] == false):
					$sticker .= "<span class='spez'>".GetMessage("CATALOG_ELEMENT_SPEZPEDLOG")."</span>";
				endif;
				//DISCOUNT//
				if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {						
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
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
							$class = " item-tb";
							$timeBuy = "<div class='time_buy_sticker'><span class='time_buy_figure'></span><span class='time_buy_text'>".GetMessage("CATALOG_ELEMENT_TIME_BUY")."</span></div>";
						} else {
							if($arElement["CAN_BUY"]) {
								$class = " item-tb";
								$timeBuy = "<div class='time_buy_sticker'><span class='time_buy_figure'></span><span class='time_buy_text'>".GetMessage("CATALOG_ELEMENT_TIME_BUY")."</span></div>";
							}
						}
					}
				}
			}
			
			//PRICE_MATRIX//
			if(count($arElement["PRICE_MATRIX_SHOW"]["COLS"]) > 1 && $arElement["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
				$class = " item-pm";
			}
			if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
				if(count($arResult["ITEMS"][$key]["TOTAL_OFFERS"]["PRICE_MATRIX_SHOW"]["COLS"]) && $arElement["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
					$class = " item-pm";
				}
			}

			//PREVIEW_PICTURE_ALT//
			$strAlt = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arElement["NAME"]);

			//PREVIEW_PICTURE_TITLE//
			$strTitle = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arElement["NAME"]);

			//ITEM//?>				
			<div class="catalog-item-card<?=$class?>" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/Product">
				<div class="catalog-item-info">							
					<?//ITEM_PREVIEW_PICTURE//?>
					<div class="item-image-cont">
						<div class="item-image">								
							<meta content="<?=(is_array($arElement['PREVIEW_PICTURE']) ? $arElement['PREVIEW_PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/no-photo.jpg');?>" itemprop="image" />
							<a rel="nofollow" href="<?=$arElement['DETAIL_PAGE_URL']?>">
								<?if(is_array($arElement["PREVIEW_PICTURE"])) {?>
									<img class="item_img data-lazy-src" data-lazy-src="<?=$arElement['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
								<?} else {?>
									<img class="item_img data-lazy-src" data-lazy-src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
								<?}?>
								<?=$timeBuy?>									
								<span class="sticker">
									<?=$sticker?>
								</span>
								<?if(is_array($arElement["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"])) {?>
									<img class="manufacturer data-lazy-src" data-lazy-src="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" title="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" />
								<?}?>
							</a>							
						</div>
					</div>
					<?//ITEM_TITLE//?>
					<div class="item-all-title">
						<a class="item-title" href="<?=$arElement['DETAIL_PAGE_URL']?>" title="<?=$arElement['NAME']?>" itemprop="url">
							<span itemprop="name"><?=$arElement['NAME']?></span>
						</a>
					</div>
					<?//ARTICLE_RATING//
					if($inArticle || $inRating) {?>
						<div class="article_rating">
							<?//ARTICLE//
							if($inArticle) {?>
								<div class="article">
									<?=GetMessage("CATALOG_ELEMENT_ARTNUMBER")?><?=!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-";?>
								</div>
							<?}
							//RATING//
							if($inRating) {?>
								<div class="rating">
									<?if($arElement["PROPERTIES"]["vote_count"]["VALUE"])
										$ratingAvg = round($arElement["PROPERTIES"]["vote_sum"]["VALUE"] / $arElement["PROPERTIES"]["vote_count"]["VALUE"], 2);
									else
										$ratingAvg = 0;
									if($ratingAvg) {									
										for($i = 0; $i <= 4; $i++) {?>
											<div class="star<?=($ratingAvg > $i ? ' voted' : ' empty');?>" title="<?=$i+1?>"><i class="fa fa-star"></i></div>
										<?}
									} else {
										for($i = 0; $i <= 4; $i++) {?>
											<div class="star empty" title="<?=$i+1?>"><i class="fa fa-star"></i></div>
										<?}
									}?>
								</div>
							<?}?>
							<div class="clr"></div>
						</div>
					<?}
					//ITEM_PREVIEW_TEXT//
					if($inPreviewText) {?>
						<div class="item-desc" itemprop="description">

<?/*vp*/?>
                            <?//=strip_tags($arElement["PREVIEW_TEXT"]);?>
                            <?=htmlspecialcharsBack($arElement["PROPERTIES"]["CHARACTERISTICS"]["VALUE"]["TEXT"])?>
<?/*vp*/?>

						</div>
					<?}
					//TOTAL_OFFERS_ITEM_PRICE//?>
					<div class="item-price-cont<?=(!$inOldPrice && !$inPercentPrice ? ' one' : '').(($inOldPrice && !$inPercentPrice) || (!$inOldPrice && $inPercentPrice) ? ' two' : '').($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<?//TOTAL_OFFERS_PRICE//
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
							if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>							
								<div class="item-no-price">			
									<span class="unit">
										<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
										<span><?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"];?></span>
									</span>									
								</div>
							<?} else {?>
								<div class="item-price">
									<?if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] < $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_BASE_PRICE"]) {
										if($inOldPrice) {?>
											<span class="catalog-item-price-old">
												<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
											</span>
										<?}
										if($inPercentPrice) {?>
											<span class="catalog-item-price-percent">
												<?=GetMessage("CATALOG_ELEMENT_SKIDKA")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"];?>
											</span>
										<?}
									}?>
									<span class="catalog-item-price">
										<?=($arElement["TOTAL_OFFERS"]["FROM"] == "Y" ? "<span class='from'>".GetMessage("CATALOG_ELEMENT_FROM")."</span> " : "").number_format($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
										<span class="unit">
											<?=$currency?>
											<span><?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"];?></span>
										</span>
										<?if($arParams["USE_PRICE_COUNT"] && count($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_QUANTITY_RANGES"]) > 1) {?>
											<span class="catalog-item-price-ranges-wrap">
												<a id="<?=$arItemIDs['PRICE_RANGES_BTN']?>" class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
											</span>
										<?}?>
									</span>
									<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
										<span class="catalog-item-price-reference">
											<?=CCurrencyLang::CurrencyFormat($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], true);?>
										</span>
									<?}?>
								</div>							
							<?}?>
							<meta itemprop="price" content="<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"]?>" />
							<meta itemprop="priceCurrency" content="<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"]?>" />
							<?if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0) {?>							
								<meta content="InStock" itemprop="availability" />									
							<?} else {?>
								<meta content="OutOfStock" itemprop="availability" />
							<?}
						//ITEM_PRICE//
						} else {
							if($arElement["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>
								<div class="item-no-price">	
									<span class="unit">
										<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
										<span><?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["CATALOG_MEASURE_NAME"];?></span>
									</span>												
								</div>
							<?} else {?>
								<div class="item-price">
									<?if($arElement["MIN_PRICE"]["RATIO_PRICE"] < $arElement["MIN_PRICE"]["RATIO_BASE_PRICE"]) {
										if($inOldPrice) {?>
											<span class="catalog-item-price-old">
												<?=$arElement["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
											</span>
										<?}
										if($inPercentPrice) {?>
											<span class="catalog-item-price-percent">
												<?=GetMessage("CATALOG_ELEMENT_SKIDKA")." ".$arElement["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"];?>
											</span>
										<?}
									}?>
									<span class="catalog-item-price">
										<?if(count($arElement["ITEM_QUANTITY_RANGES"]) > 1) {?>
											<span class="from"><?=GetMessage("CATALOG_ELEMENT_FROM")?></span>
										<?}
										echo number_format($arElement["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
										<span class="unit">
											<?=$currency?>
											<span><?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["CATALOG_MEASURE_NAME"];?></span>
										</span>
										<?if($arParams["USE_PRICE_COUNT"] && count($arElement["ITEM_QUANTITY_RANGES"]) > 1) {?>
											<span class="catalog-item-price-ranges-wrap">
												<a id="<?=$arItemIDs['PRICE_RANGES_BTN']?>" class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
											</span>
										<?}?>
									</span>
									<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
										<span class="catalog-item-price-reference">
											<?=CCurrencyLang::CurrencyFormat($arElement["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["MIN_PRICE"]["CURRENCY"], true);?>
										</span>
									<?}?>
								</div>
							<?}?>
							<meta itemprop="price" content="<?=$arElement["MIN_PRICE"]["RATIO_PRICE"]?>" />
							<meta itemprop="priceCurrency" content="<?=$arElement["MIN_PRICE"]["CURRENCY"]?>" />
							<?if($arElement["CAN_BUY"]) {?>
								<meta content="InStock" itemprop="availability" />
							<?} elseif(!$arElement["CAN_BUY"]) {?>
								<meta content="OutOfStock" itemprop="availability" />									
							<?}
						}?>
					</div>
					<?//OTHER_PRICE//
					if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
						if(count($arElement["TOTAL_OFFERS"]["PRICE_MATRIX_SHOW"]["COLS"]) > 1) {?>
							<div class="catalog-price-ranges">
								<?foreach($arElement["TOTAL_OFFERS"]["PRICE_MATRIX_SHOW"]["COLS"] as $key_matrix => $item) {
									$priceMatrix[$key_matrix] = $arElement["TOTAL_OFFERS"]["PRICE_MATRIX_SHOW"]["MATRIX"][$key_matrix];
									$oneRange = array_pop($priceMatrix[$key_matrix]);
									array_push($priceMatrix[$key], $oneRange);
									$countRange = count($arElement["TOTAL_OFFERS"]["PRICE_MATRIX_SHOW"]["MATRIX"][$key_matrix]);?>
									<div class="catalog-detail-price-ranges__row">
										<div class="catalog-detail-price-ranges__sort">
											<?=$item["NAME_LANG"]?>
										</div>
										<div class="catalog-detail-price-ranges__dots"></div>
										<?if($countRange > 1) {?>
											<span class="from"><?=GetMessage("CATALOG_ELEMENT_FROM");?></span>
										<?}?>	
										<div class="catalog-detail-price-ranges__price"><?=$oneRange["DISCOUNT_PRICE"]?></div>
										<span class="unit"><?=$oneRange["PRINT_CURRENCY"]?></span>
										<?if($countRange > 1):?>
											<span class="catalog-item-price-ranges-wrap">
												<a id="<?=$arItemIDs['PRICE_MATRIX_BTN']?>_<?=$key_matrix?>" data-key="<?=$key_matrix?>" class="catalog-item-price-ranges" href="javascript:void(0);">
													<i class="fa fa-question-circle-o"></i>
												</a>
											</span>
											<?$arResult["ITEMS"][$key]["ID_PRICE_MATRIX_BTN"][$key_matrix] = $arItemIDs['PRICE_MATRIX_BTN']."_".$key_matrix;
										endif;?>
									</div>
									<?unset($countRange);
								}
								?>
							</div>
						<?}
					} else {?>
						<?if(count($arElement["PRICE_MATRIX_SHOW"]["COLS"]) > 1) {?>
							<div class="catalog-price-ranges">
								<?foreach($arElement["PRICE_MATRIX_SHOW"]["COLS"] as $key_matrix => $item) {
									$priceMatrix[$key_matrix] = $arElement["PRICE_MATRIX_SHOW"]["MATRIX"][$key_matrix];
									$oneRange = array_pop($priceMatrix[$key_matrix]);
									array_push($priceMatrix[$key_matrix], $oneRange);
									$countRange = count($arElement["PRICE_MATRIX_SHOW"]["MATRIX"][$key_matrix]);?>
									<div class="catalog-detail-price-ranges__row">
										<div class="catalog-detail-price-ranges__sort">
											<?=$item["NAME_LANG"]?>
										</div>
										<div class="catalog-detail-price-ranges__dots"></div>
										<?if($countRange > 1) {?>
											<span class="from"><?=GetMessage("CATALOG_ELEMENT_FROM");?></span>
										<?}?>	
										<div class="catalog-detail-price-ranges__price"><?=$oneRange["DISCOUNT_PRICE"]?></div>
										<span class="unit"><?=$oneRange["PRINT_CURRENCY"]?></span>
										<?if($countRange > 1):?>
											<span class="catalog-item-price-ranges-wrap">
												<a id="<?=$arItemIDs['PRICE_MATRIX_BTN']?>_<?=$key_matrix?>" data-key="<?=$key_matrix?>" class="catalog-item-price-ranges" href="javascript:void(0);">
													<i class="fa fa-question-circle-o"></i>
												</a>
											</span>
											<?$arResult["ITEMS"][$key]["ID_PRICE_MATRIX_BTN"][$key_matrix] = $arItemIDs['PRICE_MATRIX_BTN']."_".$key_matrix;
										endif;?>
									</div>
									<?unset($countRange);
								}
								?>
							</div>
						<?}
					}?>
					<?//TIME_BUY//
					if(array_key_exists("TIME_BUY", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
						if(!empty($arElement["CURRENT_DISCOUNT"]["ACTIVE_TO"])) {							
							$showBar = false;													
							if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
								if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0) {
									$showBar = true;									
									$startQnt = $arElement["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] ? $arElement["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] : $arElement["TOTAL_OFFERS"]["QUANTITY"];	
									$currQnt = $arElement["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] ? $arElement["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] : $arElement["TOTAL_OFFERS"]["QUANTITY"];		
									$currQntPercent = round($currQnt * 100 / $startQnt);
								} else {
									$showBar = true;
									$currQntPercent = 100;
								}
							} else {
								if($arElement["CAN_BUY"]) {
									if($arElement["CHECK_QUANTITY"]) {
										$showBar = true;
										$startQnt = $arElement["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] ? $arElement["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] : $arElement["CATALOG_QUANTITY"];
										$currQnt = $arElement["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] ? $arElement["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] : $arElement["CATALOG_QUANTITY"];
										$currQntPercent = round($currQnt * 100 / $startQnt);
									} else {
										$showBar = true;
										$currQntPercent = 100;
									}
								}
							}							
							if($showBar == true) {?>
								<div class="item_time_buy_cont">
									<div class="item_time_buy">
										<div class="progress_bar_block">
											<span class="progress_bar_title"><?=GetMessage("CATALOG_ELEMENT_QUANTITY_PERCENT")?></span>
											<div class="progress_bar_cont">
												<div class="progress_bar_bg">
													<div class="progress_bar_line" style="width:<?=$currQntPercent?>%;"></div>
												</div>
											</div>
											<span class="progress_bar_percent"><?=$currQntPercent?>%</span>
										</div>
										<?$new_date = ParseDateTime($arElement["CURRENT_DISCOUNT"]["ACTIVE_TO"], FORMAT_DATETIME);?>
										<script type="text/javascript">												
											$(function() {														
												$("#time_buy_timer_<?=$arItemIDs['ID']?>").countdown({
													until: new Date(<?=$new_date["YYYY"]?>, <?=$new_date["MM"]?> - 1, <?=$new_date["DD"]?>, <?=$new_date["HH"]?>, <?=$new_date["MI"]?>),
													format: "DHMS",
													expiryText: "<div class='over'><?=GetMessage('CATALOG_ELEMENT_TIME_BUY_EXPIRY')?></div>"
												});
											});												
										</script>
										<div class="time_buy_cont">
											<div class="time_buy_clock">
												<i class="fa fa-clock-o"></i>
											</div>
											<div class="time_buy_timer" id="time_buy_timer_<?=$arItemIDs['ID']?>"></div>
										</div>
									</div>
								</div>
							<?}
						}
					}
					//OFFERS_ITEM_BUY//?>						
					<div class="buy_more">
						<?//OFFERS_AVAILABILITY_BUY//
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
							//TOTAL_OFFERS_AVAILABILITY//?>
							<div class="available">
								<?if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0 || !$arElement["CHECK_QUANTITY"]) {?>	
									<div class="avl">
										<i class="fa fa-check-circle"></i>
										<span>
											<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
											if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0 && $inProductQnt)
												echo " ".$arElement["TOTAL_OFFERS"]["QUANTITY"];?>
										</span>
									</div>
								<?} else {?>									
									<div class="not_avl">
										<i class="fa fa-times-circle"></i>
										<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
									</div>
								<?}?>
							</div>
							<?//OFFERS_BUY//?>
							<div class="add2basket_block">
								<form action="<?=$curPage?>" class="add2basket_form">
									<a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$arItemIDs['ID']?>"><span>-</span></a>
									<input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['TOTAL_OFFERS']['MIN_PRICE']['MIN_QUANTITY']?>"/>
									<a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$arItemIDs['ID']?>"><span>+</span></a>
									<button type="button" id="<?=$arItemIDs['PROPS_BTN']?>" class="btn_buy" onclick="ym(22745254,'reachGoal','addcart'); ga('send', 'event', 'addcart', 'click'); add2cart_gtag_bs(<?=$arItemIDs['ID']?>,<?=$arElement["MIN_PRICE"]["RATIO_PRICE"]?>); return true;"  name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
								</form>
							</div>
						<?//ITEM_AVAILABILITY_BUY//
						} else {
							//ITEM_AVAILABILITY//?>
							<div class="available">
								<?if($arElement["CAN_BUY"]) {?>									
									<div class="avl">
										<i class="fa fa-check-circle"></i>
										<span>
											<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
											if($arElement["CHECK_QUANTITY"] && $inProductQnt)
												echo " ".$arElement["CATALOG_QUANTITY"];?>
										</span>
									</div>
								<?} elseif(!$arElement["CAN_BUY"]) {?>									
									<div class="not_avl">
										<i class="fa fa-times-circle"></i>
										<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
									</div>
								<?}?>
							</div>
							<?//ITEM_BUY//?>
							<div class="add2basket_block">
								<?if($arElement["CAN_BUY"]) {
									if($arElement["MIN_PRICE"]["RATIO_PRICE"] <= 0) {
										//ITEM_ASK_PRICE//?>
										<a id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo" href="javascript:void(0)" rel="nofollow" data-action="ask_price"><i class="fa fa-comment-o"></i><span class="full"><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_FULL")?></span><span class="short"><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_SHORT")?></span></a>
									<?} else {
										if(isset($arElement["SELECT_PROPS"]) && !empty($arElement["SELECT_PROPS"])) {?>
											<form action="<?=$curPage?>" class="add2basket_form">
										<?} else {?>
											<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
										<?}?>
											<a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$arItemIDs['ID']?>"><span>-</span></a>
											<input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['MIN_PRICE']['MIN_QUANTITY']?>"/>
											<a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$arItemIDs['ID']?>"><span>+</span></a>
											<?if(!isset($arElement["SELECT_PROPS"]) || empty($arElement["SELECT_PROPS"])) {?>
												<input type="hidden" name="ID" value="<?=$arElement['ID']?>" />
												<?if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
													$props = array();
													$props[] = array(
														"NAME" => $arElement["PROPERTIES"]["ARTNUMBER"]["NAME"],
														"CODE" => $arElement["PROPERTIES"]["ARTNUMBER"]["CODE"],
														"VALUE" => $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]
													);												
													$props = strtr(base64_encode(serialize($props)), "+/=", "-_,");?>
													<input type="hidden" name="PROPS" value="<?=$props?>" />
												<?}
											}?>
											<button onclick="ym(22745254,'reachGoal','addcart'); ga('send', 'event', 'addcart', 'click'); add2cart_gtag_bs(<?=$arItemIDs['ID']?>,<?=$arElement["MIN_PRICE"]["RATIO_PRICE"]?>);"  type="button" id="<?=(isset($arElement['SELECT_PROPS']) && !empty($arElement['SELECT_PROPS']) ? $arItemIDs['PROPS_BTN'] : $arItemIDs['BTN_BUY']);?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
										<script>
                                            $('.btn_buy').click(function () {
                                                fbq('track', 'AddToCart'); 
                                            });
                                        </script>
                                            </form>
									<?}
								} elseif(!$arElement["CAN_BUY"]) {
									//ITEM_UNDER_ORDER//?>
									<a id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo" href="javascript:void(0)" rel="nofollow" data-action="under_order"><i class="fa fa-clock-o"></i><span><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></a>
								<?}?>								
							</div> 
						<?}?>
						<div class="clr"></div>
						<?//ITEM_COMPARE//
						if($arParams["DISPLAY_COMPARE"]=="Y") {?>
							<div class="compare">
								<a href="javascript:void(0)" class="catalog-item-compare" id="catalog_add2compare_link_<?=$arItemIDs['ID']?>" onclick="return addToCompare('<?=$arElement["COMPARE_URL"]?>', 'catalog_add2compare_link_<?=$arItemIDs["ID"]?>', '<?=SITE_DIR?>');" title="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_COMPARE')?>" rel="nofollow"><i class="fa fa-bar-chart"></i><i class="fa fa-check"></i></a>
							</div>
						<?}
						//OFFERS_DELAY//
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {								
							if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CAN_BUY"] && $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] > 0) {
								$props = array();
								if(!empty($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
									$props[] = array(
										"NAME" => $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["NAME"],
										"CODE" => $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["CODE"],
										"VALUE" => $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["VALUE"]
									);																
								}
								foreach($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISPLAY_PROPERTIES"] as $propOffer) {
									if($propOffer["PROPERTY_TYPE"] != "S") {
										$props[] = array(
											"NAME" => $propOffer["NAME"],
											"CODE" => $propOffer["CODE"],
											"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
										);
									}
								}
								$props = !empty($props) ? strtr(base64_encode(serialize($props)), "+/=", "-_,") : "";?>
								<div class="delay">
									<a href="javascript:void(0)" id="catalog-item-delay-min-<?=$arItemIDs['ID'].'-'.$arElement['TOTAL_OFFERS']['MIN_PRICE']['ID']?>" class="catalog-item-delay 5" onclick="return addToDelay('<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', 'quantity_<?=$arItemIDs["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-min-<?=$arItemIDs["ID"]."-".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', '<?=SITE_DIR?>')" title="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?>" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
								</div>
							<?}
						//ITEM_DELAY//
						} else {
							if($arElement["CAN_BUY"] && $arElement["MIN_PRICE"]["RATIO_PRICE"] > 0) {
								$props = "";
								if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {		
									$props = array();
									$props[] = array(
										"NAME" => $arElement["PROPERTIES"]["ARTNUMBER"]["NAME"],
										"CODE" => $arElement["PROPERTIES"]["ARTNUMBER"]["CODE"],
										"VALUE" => $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]
									);
									$props = strtr(base64_encode(serialize($props)), "+/=", "-_,");
								}?>
								<div class="delay">
									<a href="javascript:void(0)" id="catalog-item-delay-<?=$arItemIDs['ID']?>" class="catalog-item-delay " onclick="ym(22745254,'reachGoal','aside'); ga('send', 'event', 'delay', 'click'); return addToDelay('<?=$arElement["ID"]?>', 'quantity_<?=$arItemIDs["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-<?=$arItemIDs["ID"]?>', '<?=SITE_DIR?>'); " title="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?>" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
								</div>
							<?}
						}?>								
					</div>

<?/*vp*/?>
        <div class="postfix">
            <?=htmlspecialcharsBack($arElement["PROPERTIES"]["DETAIL"]["VALUE"]["TEXT"])?>
        </div>
<?/*vp*/?>

				</div>
			</div>			
		<?}?>
	</div>	
	<?//PAGINATION//
	if($arParams["DISPLAY_BOTTOM_PAGER"])
		echo $arResult["NAV_STRING"];?>	
</div>
<div class="clr"></div>

<?//JS//?>
<script type="text/javascript">
	BX.ready(function() {
		BX.message({			
			TABLE_ELEMENT_FROM: "<?=GetMessageJS('CATALOG_ELEMENT_FROM')?>",
			TABLE_ADDITEMINCART_ADDED: "<?=GetMessageJS('CATALOG_ELEMENT_ADDED')?>",
			TABLE_POPUP_WINDOW_TITLE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_TITLE')?>",			
			TABLE_POPUP_WINDOW_BTN_CLOSE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_CLOSE')?>",
			TABLE_POPUP_WINDOW_BTN_ORDER: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_ORDER')?>",
			TABLE_SITE_DIR: "<?=SITE_DIR?>",
			TABLE_POPUP_WINDOW_MORE_OPTIONS: "<?=GetMessageJS('CATALOG_ELEMENT_MORE_OPTIONS')?>",			
			TABLE_COMPONENT_TEMPLATE: "<?=$this->GetFolder();?>",
			TABLE_OFFERS_VIEW: "<?=$arSetting['OFFERS_VIEW']['VALUE']?>",
			TABLE_COMPONENT_PARAMS: "<?=CUtil::PhpToJSObject($arParams, false, true)?>"
		});	
		<?foreach($arResult["ITEMS"] as $key => $arElement) {
			if((isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) || $arElement["SELECT_PROPS"]) {				
				$arJSParams = array(					
					"VISUAL" => array(
						"ID" => $arElement["STR_MAIN_ID"],
						"PRICE_RANGES_BTN_ID" => $arElement["STR_MAIN_ID"]."_price_ranges_btn",
						"PROPS_BTN_ID" => $arElement["STR_MAIN_ID"]."_props_btn",
						"PRICE_MATRIX_BTN_ID" => is_array($arElement["ID_PRICE_MATRIX_BTN"]) ? $arElement["ID_PRICE_MATRIX_BTN"] : ""
					),
					"PRODUCT" => array(
						"ID" => $arElement["ID"],												
						"ITEM_PRICE_MODE" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICE_MODE"] : $arElement["ITEM_PRICE_MODE"],
						"ITEM_PRICES" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICES"] : $arElement["ITEM_PRICES"],
						"ITEM_PRICE_SELECTED" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICE_SELECTED"] : $arElement["ITEM_PRICE_SELECTED"],
						"ITEM_QUANTITY_RANGES" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_QUANTITY_RANGES"] : $arElement["ITEM_QUANTITY_RANGES"],						
						"CHECK_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CHECK_QUANTITY"] : $arElement["CHECK_QUANTITY"],
						"QUANTITY_FLOAT" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? is_double($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]) : is_double($arElement["CATALOG_MEASURE_RATIO"]),
						"MAX_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_QUANTITY"] : $arElement["CATALOG_QUANTITY"],
						"STEP_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : $arElement["CATALOG_MEASURE_RATIO"],
						"PRICE_MATRIX" =>  isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["PRICE_MATRIX_SHOW"]["MATRIX"] : $arElement["PRICE_MATRIX_SHOW"]["MATRIX"],
						"PRINT_CURRENCY" => $currency
					)
				);
				if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]))
					$arJSParams["OFFER"]["ID"] = $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"];
			} else {
				$arJSParams = array(					
					"VISUAL" => array(
						"ID" => $arElement["STR_MAIN_ID"],
						"PRICE_RANGES_BTN_ID" => $arElement["STR_MAIN_ID"]."_price_ranges_btn",
						"POPUP_BTN_ID" => $arElement["STR_MAIN_ID"]."_popup_btn",
						"BTN_BUY_ID" => $arElement["STR_MAIN_ID"]."_btn_buy",
						"PRICE_MATRIX_BTN_ID" => is_array($arElement["ID_PRICE_MATRIX_BTN"]) ? $arElement["ID_PRICE_MATRIX_BTN"] : ""
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
			}
			$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $arElement["STR_MAIN_ID"]);?>
			var <?=$strObName?> = new JCCatalogSectionTable(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
		<?}?>
	});
</script>
<script>
	function add2cart_gtag_bs(id, price){
		console.log('add_to_cart', id, price);
	    gtag('event', 'add_to_cart', {
	        'send_to': 'AW-977520268',
	        'value': price,
	        'items': [{
	            'id': id,
	            'google_business_vertical': 'retail'
	        }]
	    });
	}
</script>