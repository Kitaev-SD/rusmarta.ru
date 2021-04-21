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
<div id="catalog" itemscope itemtype="http://schema.org/ItemList">
	<link href="<?=$curPage?>" itemprop="url" />
	<div class="catalog-item-collection-view">
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
				//NEW//
				if(array_key_exists("NEWPRODUCT", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == false)
					$sticker .= "<span class='new'>".GetMessage("CATALOG_ELEMENT_NEWPRODUCT")."</span>";				
				//HIT//
				if(array_key_exists("SALELEADER", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["SALELEADER"]["VALUE"] == false)
					$sticker .= "<span class='hit'>".GetMessage("CATALOG_ELEMENT_SALELEADER")."</span>";
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
						if($arElement["CAN_BUY"]) {
							$class = " item-tb";
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
							<a href="<?=$arElement['DETAIL_PAGE_URL']?>"<?=(is_array($arElement["PREVIEW_PICTURE"])) ? " style='background-image:url(".$arElement['PREVIEW_PICTURE']['SRC'].")'" : " style='background-image:url(".SITE_TEMPLATE_PATH."/images/no-photo.jpg)'"?>></a>
							<span class="sticker">
								<?=$sticker?>
							</span>
							<?if(is_array($arElement["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"])) {?>
								<img class="manufacturer" src="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" title="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" />
							<?}?>
						</div>
					</div>
					<?//TIME_BUY//
					if(array_key_exists("TIME_BUY", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
						if(!empty($arElement["CURRENT_DISCOUNT"]["ACTIVE_TO"])) {
							$showBar = false;													
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
					}?>		
					<?//AVAILABLE_RATING//
					//AVAILABLE//?>
					<a class="item-all" href="<?=$arElement['DETAIL_PAGE_URL']?>">
						<div class="item-available-rating">
							<div class="available">
								<?if($arElement["CAN_BUY"]) {?>									
									<div class="avl">
										<i class="fa fa-check-circle"></i>
										<span>
											<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");?>
										</span>
									</div>
								<?} elseif(!$arElement["CAN_BUY"]) {?>									
									<div class="not_avl">
										<i class="fa fa-times-circle"></i>
										<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
									</div>
								<?}?>
							</div>
							<?//RATING//
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
						</div>
					</a>	
					<?//ITEM_TITLE//?>
					<div class="item-all-title">
						<a class="item-title" href="<?=$arElement['DETAIL_PAGE_URL']?>" title="<?=$arElement['NAME']?>" itemprop="url">
							<span itemprop="name"><?=$arElement['NAME']?></span>
						</a>
					</div>
					<?//ITEM_PREVIEW_TEXT//
					if($inPreviewText) {?>
					<a class="item-all" href="<?=$arElement['DETAIL_PAGE_URL']?>">
						<div class="item-desc" itemprop="description">
							<?=strip_tags($arElement["PREVIEW_TEXT"]);?>
						</div>
					</a>	
					<?}
					//ITEM_PRICE//?>
					<div class="item-price-cont<?=(!$inOldPrice && !$inPercentPrice ? ' one' : '').(($inOldPrice && !$inPercentPrice) || (!$inOldPrice && $inPercentPrice) ? ' two' : '').($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<?if($arElement["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>
							<div class="item-no-price">	
								<span class="unit">
									<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
								</span>												
							</div>
						<?} else {?>
							<div class="item-price">
								<span class="catalog-item-price">
									<span class="from">
										<?=GetMessage("CATALOG_ELEMENT_FROM")?>
									</span>
									<?echo number_format($arElement["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
									<span class="unit">
										<?=$currency?>
									</span>
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
						<?}?>
					</div>
					<?//VERSIONS_PERFORMANCE//?>
					<?if(!empty($arElement["VERSIONS_PERFORMANCE"]["ITEMS"]) && count($arElement["VERSIONS_PERFORMANCE"]["ITEMS"]) > 0) {?>
						<div class="color-collection-container">
							<?foreach($arElement["VERSIONS_PERFORMANCE"]["ITEMS"] as $arColor) {?>
								<?if((is_array($arColor["PICTURE"]) && !empty($arColor["PICTURE"])) || (isset($arColor["PROPERTY_HEX_VALUE"]) && !empty($arColor["PROPERTY_HEX_VALUE"]))) {?>
									<div class="color-collection-item" title="<?=$arColor["NAME"]?>">
										<div class="image-color" style="
											<?if(is_array($arColor["PICTURE"]) && !empty($arColor["PICTURE"])) {?>
											background-image: url(<?=$arColor["PICTURE"]['SRC']?>);
											background-repeat: no-repeat;
											background-size: cover;
											background-position: center;
											<?} else {?>
											background-color: #<?=$arColor["PROPERTY_HEX_VALUE"]?>;
											<?}?>
										"></div>
									</div>
								<?}?>
							<?}?>
						</div>
					<?}?>
				</div>
			</div>			
		<?}?>
	</div>	
	<?//PAGINATION//
	if($arParams["DISPLAY_BOTTOM_PAGER"])
		echo $arResult["NAV_STRING"];?>	
</div>
<div class="clr"></div>