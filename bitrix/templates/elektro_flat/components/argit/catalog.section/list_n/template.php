<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(count($arResult["ITEMS"]) < 1) {
	echo "<br />";
	ShowNote(GetMessage("CATALOG_EMPTY_RESULT"), "infotext");
	return;
}

$curPage = $APPLICATION->GetCurPage();

global $arSetting;
$inProductQnt = in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"]);
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);

//CATALOG//?>

<div id="catalog">
	<div class="catalog-item-list-view" itemtype="http://schema.org/ItemList">
		<link href="<?=$curPage?>" itemprop="url" />
		<?foreach($arResult["ITEMS"] as $key => $arElement) {				
			$arItemIDs = array(
				"ID" => $arElement["STR_MAIN_ID"],
				"PRICE_RANGES_BTN" => $arElement["STR_MAIN_ID"]."_price_ranges_btn",
				"POPUP_BTN" => $arElement["STR_MAIN_ID"]."_popup_btn",
				"PROPS_BTN" => $arElement["STR_MAIN_ID"]."_props_btn",
				"BTN_BUY" => $arElement["STR_MAIN_ID"]."_btn_buy"
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

			//PREVIEW_PICTURE_ALT//
			$strAlt = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arElement["NAME"]);

			//PREVIEW_PICTURE_TITLE//
			$strTitle = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arElement["NAME"]);

			//ITEM//?>
			<div class="catalog-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/Product">
				<div class="catalog-item-info">
					<?//ITEM_PREVIEW_PICTURE//?>
					<div class="catalog-item-image-cont">
						<div class="catalog-item-image">
							<meta content="<?=(is_array($arElement['PREVIEW_PICTURE']) ? $arElement['PREVIEW_PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/no-photo.jpg');?>" itemprop="image" />
							<a href="<?=$arElement['DETAIL_PAGE_URL']?>">
								<?if(is_array($arElement["PREVIEW_PICTURE"])) {?>
									<img class="item_img data-lazy-src" data-lazy-src="<?=$arElement['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
								<?} else {?>
									<img class="item_img data-lazy-src" data-lazy-src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
								<?}?>
								<div class="sticker-cont">
									<?=$timeBuy?>
									<span class="sticker">
										<?=$sticker?>
									</span>
								</div>
								<?if(is_array($arElement["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"])) {?>
									<img class="manufacturer" src="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" title="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" />
								<?}?>
							</a>							
						</div>
					</div>
					<div class="catalog-item-desc">
						<?//ITEM_TITLE//?>
						<div class="catalog-item-title">
							<a href="<?=$arElement['DETAIL_PAGE_URL']?>" title="<?=$arElement['NAME']?>" itemprop="url">
								<span itemprop="name"><?=$arElement["NAME"]?></span>
							</a>
						</div>
						<?//ITEM_PREVIEW_TEXT//?>
						<div class="catalog-item-preview-text" itemprop="description">
							<?=strip_tags($arElement["PREVIEW_TEXT"])?>
						</div>
						<?//ARTICLE_RATING//?>
						<div class="article_rating">
							<?//ARTICLE//?>
							<div class="article">
								<?=GetMessage("CATALOG_ELEMENT_ARTNUMBER")?><?=!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-";?>
							</div>
							<?//RATING//?>
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
								<div class="vote-result">(<?=($arElement["PROPERTIES"]["vote_count"]["VALUE"] ? $arElement["PROPERTIES"]["vote_count"]["VALUE"] : 0);?>)</div>
							</div>
							<div class="clr"></div>
						</div>
						<?//ITEM_PROPERTIES//
						if(!empty($arElement["DISPLAY_PROPERTIES"])) {?>
							<div class="properties">								
								<?foreach($arElement["DISPLAY_PROPERTIES"] as $k => $v) {?>
									<div class="property">
										<span class="name"><?=$v["NAME"]?></span> 
										<span class="val">
											<?=is_array($v["DISPLAY_VALUE"]) ? implode(", ", $v["DISPLAY_VALUE"]) : $v["DISPLAY_VALUE"];?>
										</span>
										<div class="clr"></div>
									</div>
								<?}?>
							</div>
						<?}
						//COMPARE_DELAY//?>	
						<div class="compare_delay">
							<?//ITEM_COMPARE//
							if($arParams["DISPLAY_COMPARE"]=="Y") {?>
								<div class="compare">
									<a href="javascript:void(0)" class="catalog-item-compare" id="catalog_add2compare_link_<?=$arItemIDs['ID']?>" onclick="return addToCompare('<?=$arElement["COMPARE_URL"]?>', 'catalog_add2compare_link_<?=$arItemIDs["ID"]?>', '<?=SITE_DIR?>');" rel="nofollow"><i class="fa fa-bar-chart"></i><i class="fa fa-check"></i><span><?=GetMessage('CATALOG_ELEMENT_ADD_TO_COMPARE')?></span></a>
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
										<a href="javascript:void(0)" id="catalog-item-delay-min-<?=$arItemIDs['ID'].'-'.$arElement['TOTAL_OFFERS']['MIN_PRICE']['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', 'quantity_<?=$arItemIDs["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-min-<?=$arItemIDs["ID"]."-".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i><span><?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?></span></a>
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
										<a href="javascript:void(0)" id="catalog-item-delay-<?=$arItemIDs['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arElement["ID"]?>', 'quantity_<?=$arItemIDs["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-<?=$arItemIDs["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i><span><?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?></span></a>
									</div>
								<?}
							}?>			
							<div class="clr"></div>
						</div>						
					</div>					
					<?//TOTAL_OFFERS_ITEM_PRICE//?>
					<div class="item-price<?=$class?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<?//TOTAL_OFFERS_PRICE//
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
							if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>								
								<span class="unit">
									<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
									<br />
									<span><?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"];?></span>
								</span>								
							<?} else {?>								
								<span class="catalog-item-price">
									<?=($arElement["TOTAL_OFFERS"]["FROM"] == "Y" ? "<span class='from'>".GetMessage("CATALOG_ELEMENT_FROM")."</span> " : "").number_format($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);
									if($arParams["USE_PRICE_COUNT"] && count($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_QUANTITY_RANGES"]) > 1) {?>
										<span class="catalog-item-price-ranges-wrap">
											<a id="<?=$arItemIDs['PRICE_RANGES_BTN']?>" class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
										</span>
									<?}?>
									<?if(count($arElement["TOTAL_OFFERS"]["MATRIX"]["MATRIX"]) > 1 && count($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_QUANTITY_RANGES"]) <= 1) {?>
										<span class="catalog-item-price-ranges-wrap">
											<a id="<?=$arItemIDs['PRICE_RANGES_BTN']?>" class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
										</span>
									<?}?>
									<span class="unit">
										<?=$currency?>
										<span><?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"];?></span>
									</span>									
									<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
										<span class="catalog-item-price-reference">
											<?=number_format($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arCurFormat["REFERENCE_DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
											<span><?=$currency?></span>
										</span>
									<?}?>
								</span>								
								<?if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] < $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
									<span class="catalog-item-price-old">
										<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>								
									</span>
									<span class="catalog-item-price-percent">
										<?=GetMessage("CATALOG_ELEMENT_SKIDKA")."<br />".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"];?>
									</span>
								<?}							
							}?>
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
								<span class="unit">
									<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
									<br />
									<span><?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["CATALOG_MEASURE_NAME"];?></span>
								</span>
							<?} else {?>											
								<span class="catalog-item-price">
									<?if(count($arElement["ITEM_QUANTITY_RANGES"]) > 1) {?>
										<span class="from"><?=GetMessage("CATALOG_ELEMENT_FROM")?></span>
									<?}
									echo number_format($arElement["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);
									if($arParams["USE_PRICE_COUNT"] && count($arElement["ITEM_QUANTITY_RANGES"]) > 1) {?>
										<span class="catalog-item-price-ranges-wrap">
											<a id="<?=$arItemIDs['PRICE_RANGES_BTN']?>" class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
										</span>
									<?}?>
									<?if(count($arElement["PRICE_MATRIX"]["MATRIX"]) > 1 && count($arElement["ITEM_QUANTITY_RANGES"]) <= 1) {?>
										<span class="catalog-item-price-ranges-wrap">
											<a id="<?=$arItemIDs['PRICE_RANGES_BTN']?>" class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
										</span>
									<?}?>
									<span class="unit">
										<?=$currency?>
										<span><?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["CATALOG_MEASURE_NAME"];?></span>
									</span>									
									<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
										<span class="catalog-item-price-reference">
											<?=number_format($arElement["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arCurFormat["REFERENCE_DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
											<span><?=$currency?></span>
										</span>
									<?}?>
								</span>											
								<?if($arElement["MIN_PRICE"]["RATIO_PRICE"] < $arElement["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
									<span class="catalog-item-price-old">
										<?=$arElement["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
									</span>
									<span class="catalog-item-price-percent">
										<?=GetMessage("CATALOG_ELEMENT_SKIDKA")."<br />".$arElement["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"];?>
									</span>
								<?}										
							}?>
							<meta itemprop="price" content="<?=$arElement["MIN_PRICE"]["RATIO_PRICE"]?>" />
							<meta itemprop="priceCurrency" content="<?=$arElement["MIN_PRICE"]["CURRENCY"]?>" />
							<?if($arElement["CAN_BUY"]) {?>
								<meta content="InStock" itemprop="availability" />
							<?} elseif(!$arElement["CAN_BUY"]) {?>
								<meta content="OutOfStock" itemprop="availability" />									
							<?}
						}?>
					</div>
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
								<div class="item_time_buy">										
									<div class="progress_bar_bg">
										<div class="progress_bar_line" style="width:<?=$currQntPercent?>%;"></div>
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
									<div class="qnt_cont">
										<a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$arItemIDs['ID']?>"><span>+</span></a>
										<input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['TOTAL_OFFERS']['MIN_PRICE']['MIN_QUANTITY']?>"/>
										<a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$arItemIDs['ID']?>"><span>-</span></a>
									</div>
									<button type="button" id="<?=$arItemIDs['PROPS_BTN']?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
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
											<div class="qnt_cont">
												<a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$arItemIDs['ID']?>"><span>+</span></a>
												<input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['MIN_PRICE']['MIN_QUANTITY']?>"/>
												<a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$arItemIDs['ID']?>"><span>-</span></a>
											</div>
											<?if(!isset($arElement["SELECT_PROPS"]) || empty($arElement["SELECT_PROPS"])) {?>
												<input type="hidden" name="ID" value="<?=$arElement['ID']?>"/>
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
											<button type="button" id="<?=(isset($arElement['SELECT_PROPS']) && !empty($arElement['SELECT_PROPS']) ? $arItemIDs['PROPS_BTN'] : $arItemIDs['BTN_BUY']);?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
										</form>
									<?}
								} elseif(!$arElement["CAN_BUY"]) {
									//ITEM_UNDER_ORDER//?>
									<a id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo" href="javascript:void(0)" rel="nofollow" data-action="under_order"><i class="fa fa-clock-o"></i><span><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></a>
								<?}?>								
							</div>								
						<?}?>
					</div>
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
			LIST_ELEMENT_FROM: "<?=GetMessageJS('CATALOG_ELEMENT_FROM')?>",
			LIST_ADDITEMINCART_ADDED: "<?=GetMessageJS('CATALOG_ELEMENT_ADDED')?>",
			LIST_POPUP_WINDOW_TITLE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_TITLE')?>",			
			LIST_POPUP_WINDOW_BTN_CLOSE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_CLOSE')?>",
			LIST_POPUP_WINDOW_BTN_ORDER: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_ORDER')?>",
			LIST_SITE_DIR: "<?=SITE_DIR?>",
			LIST_POPUP_WINDOW_MORE_OPTIONS: "<?=GetMessageJS('CATALOG_ELEMENT_MORE_OPTIONS')?>",			
			LIST_COMPONENT_TEMPLATE: "<?=$this->GetFolder();?>",
			LIST_OFFERS_VIEW: "<?=$arSetting['OFFERS_VIEW']['VALUE']?>",
			LIST_COMPONENT_PARAMS: "<?=CUtil::PhpToJSObject($arParams, false, true)?>"
		});	
		<?foreach($arResult["ITEMS"] as $key => $arElement) {
			if((isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) || $arElement["SELECT_PROPS"]) {				
				$arJSParams = array(					
					"VISUAL" => array(
						"ID" => $arElement["STR_MAIN_ID"],
						"PRICE_RANGES_BTN_ID" => $arElement["STR_MAIN_ID"]."_price_ranges_btn",
						"PROPS_BTN_ID" => $arElement["STR_MAIN_ID"]."_props_btn",
						"PRICE_MATRIX_BTN_ID" => $arElement["STR_MAIN_ID"]."_price_ranges_btn_"
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
						"PRICE_MATRIX" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MATRIX"] : $arElement["MATRIX"],
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
						"PRICE_MATRIX_BTN_ID" => $arElement["STR_MAIN_ID"]."_price_ranges_btn_"
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
						"PRICE_MATRIX" => $arElement["MATRIX"],
						"PRINT_CURRENCY" => $currency
					)
				);
			}
			$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $arElement["STR_MAIN_ID"]);?>
			var <?=$strObName?> = new JCCatalogSectionList(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
		<?}?>
	});
</script>