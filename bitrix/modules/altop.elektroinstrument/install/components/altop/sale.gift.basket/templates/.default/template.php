<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$curPage = $APPLICATION->GetCurPage();
	
global $arSetting;
$inOldPrice = in_array("OLD_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inPercentPrice = in_array("PERCENT_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inArticle = in_array("ARTNUMBER", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inRating = in_array("RATING", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inPreviewText = in_array("PREVIEW_TEXT", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inProductQnt = in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"]);
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);

//ITEMS//?>
<div class="filtered-items">	
	<?if(empty($arParams["HIDE_BLOCK_TITLE"]) || $arParams["HIDE_BLOCK_TITLE"] != "Y") {?>
		<div class="h3"><?=($arParams["BLOCK_TITLE"] ? htmlspecialcharsbx($arParams["BLOCK_TITLE"]) : GetMessage("CT_SGB_ELEMENT_TITLE_DEFAULT"))?></div>
	<?}?>
	<div class="catalog-item-cards">
		<?foreach($arResult["ITEMS"] as $key => $arElement) {			
			$arItemIDs = array(
				"ID" => $arElement["STR_MAIN_ID"],				
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
			
			//NEW_HIT_DISCOUNT//
			$sticker = "";			
			if(array_key_exists("PROPERTIES", $arElement) && is_array($arElement["PROPERTIES"])) {
				//NEW//
				if(array_key_exists("NEWPRODUCT", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == false)
					$sticker .= "<span class='new'>".GetMessage("CT_SGB_ELEMENT_NEWPRODUCT")."</span>";
				//HIT//
				if(array_key_exists("SALELEADER", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["SALELEADER"]["VALUE"] == false)
					$sticker .= "<span class='hit'>".GetMessage("CT_SGB_ELEMENT_SALELEADER")."</span>";
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
			}
			
			//PREVIEW_PICTURE_ALT//
			$strAlt = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arElement["NAME"]);

			//PREVIEW_PICTURE_TITLE//
			$strTitle = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arElement["NAME"]);
			
			//ITEM//?>				
			<div class="catalog-item-card">
				<div class="catalog-item-info">							
					<?//ITEM_PREVIEW_PICTURE//?>
					<div class="item-image-cont">
						<div class="item-image">
							<a href="<?=$arElement['DETAIL_PAGE_URL']?>">
								<?if(is_array($arElement["PREVIEW_PICTURE"])) {?>
									<img class="item_img" src="<?=$arElement['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
								<?} else {?>
									<img class="item_img" src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
								<?}?>																	
								<span class="sticker">
									<?=$sticker?>
								</span>
								<?if(is_array($arElement["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"])) {?>
									<img class="manufacturer" src="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" title="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" />
								<?}?>
							</a>							
						</div>
					</div>
					<?//ITEM_TITLE//?>
					<div class="item-all-title">
						<a class="item-title" href="<?=$arElement['DETAIL_PAGE_URL']?>" title="<?=$arElement['NAME']?>">
							<?=$arElement['NAME']?>
						</a>
					</div>
					<?//ARTICLE_RATING//
					if($inArticle || $inRating) {?>
						<div class="article_rating">
							<?//ARTICLE//
							if($inArticle) {?>
								<div class="article">
									<?=GetMessage("CT_SGB_ELEMENT_ARTNUMBER")?><?=!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-";?>
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
						<div class="item-desc">
							<?=strip_tags($arElement["PREVIEW_TEXT"]);?>
						</div>
					<?}
					//TOTAL_OFFERS_ITEM_PRICE//?>
					<div class="item-price-cont<?=(!$inOldPrice && !$inPercentPrice ? ' one' : '').(($inOldPrice && !$inPercentPrice) || (!$inOldPrice && $inPercentPrice) ? ' two' : '').($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>">
						<?//TOTAL_OFFERS_PRICE//
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
							if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_BASE_PRICE"] <= 0) {?>							
								<div class="item-no-price">			
									<span class="unit">
										<?=GetMessage("CT_SGB_ELEMENT_NO_PRICE")?>
										<span><?=GetMessage("CT_SGB_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"];?></span>
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
												<?=GetMessage("CT_SGB_ELEMENT_SKIDKA")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"];?>
											</span>
										<?}
									}?>
									<span class="catalog-item-price">
										<?=($arElement["TOTAL_OFFERS"]["FROM"] == "Y" ? "<span class='from'>".GetMessage("CT_SGB_ELEMENT_FROM")."</span> " : "").number_format($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
										<span class="unit">
											<?=$currency?>
											<span><?=GetMessage("CT_SGB_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"];?></span>
										</span>										
									</span>
									<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
										<span class="catalog-item-price-reference">
											<?=CCurrencyLang::CurrencyFormat($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], true);?>
										</span>
									<?}?>
								</div>							
							<?}
						//ITEM_PRICE//
						} else {
							if($arElement["MIN_PRICE"]["RATIO_BASE_PRICE"] <= 0) {?>
								<div class="item-no-price">	
									<span class="unit">
										<?=GetMessage("CT_SGB_ELEMENT_NO_PRICE")?>
										<span><?=GetMessage("CT_SGB_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["CATALOG_MEASURE_NAME"];?></span>
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
												<?=GetMessage("CT_SGB_ELEMENT_SKIDKA")." ".$arElement["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"];?>
											</span>
										<?}
									}?>
									<span class="catalog-item-price">										
										<?=number_format($arElement["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
										<span class="unit">
											<?=$currency?>
											<span><?=GetMessage("CT_SGB_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arElement["CATALOG_MEASURE_RATIO"] : "1")." ".$arElement["CATALOG_MEASURE_NAME"];?></span>
										</span>
									</span>
									<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
										<span class="catalog-item-price-reference">
											<?=CCurrencyLang::CurrencyFormat($arElement["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["MIN_PRICE"]["CURRENCY"], true);?>
										</span>
									<?}?>
								</div>										
							<?}
						}?>
					</div>
					<?//OFFERS_ITEM_BUY//?>						
					<div class="buy_more">
						<?//OFFERS_AVAILABILITY_BUY//
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
							//TOTAL_OFFERS_AVAILABILITY//?>
							<div class="available">
								<?if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0 || !$arElement["CHECK_QUANTITY"]) {?>
                                     <?if($arParams['SHOW_MAX_QUANTITY'] !== 'N') { ?>
                                        <meta content="InStock" itemprop="availability"/>
                                        <div class="avl">
                                            <i class="fa fa-check-circle"></i>
                                            <span>
                                                <?=(!empty($arParams["MESS_SHOW_MAX_QUANTITY"]) ? $arParams["MESS_SHOW_MAX_QUANTITY"] : GetMessage("CT_SGB_ELEMENT_AVAILABLE")) . ' ';
                                                if($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                                    if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0 && $inProductQnt) {
                                                        if($arParams['RELATIVE_QUANTITY_FACTOR'] > $arElement["TOTAL_OFFERS"]["QUANTITY"])
                                                        echo GetMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_MANY");
                                                        else
                                                            echo GetMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_FEW");
                                                    }
                                                } else {
                                                    if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0 && $inProductQnt)
                                                        echo " " . $arElement["TOTAL_OFFERS"]["QUANTITY"];
                                                }?>
                                            </span>
                                        </div>
                                    <?}?>                           
								<?} else {?>									
									<div class="not_avl">
										<i class="fa fa-times-circle"></i>
										<span><?=GetMessage("CT_SGB_ELEMENT_NOT_AVAILABLE")?></span>
									</div>
								<?}?>
							</div>
							<?//OFFERS_BUY//?>
							<div class="add2basket_block">
								<form action="<?=$curPage?>" class="add2basket_form">
									<a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$arItemIDs['ID']?>"><span>-</span></a>
									<input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['TOTAL_OFFERS']['MIN_PRICE']['MIN_QUANTITY']?>"/>
									<a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$arItemIDs['ID']?>"><span>+</span></a>
									<button type="button" id="<?=$arItemIDs['PROPS_BTN']?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=($arSetting["NAME_BUTTON_TO_CART"]["VALUE"] ? $arSetting["NAME_BUTTON_TO_CART"]["VALUE"] : GetMessage("CT_SGB_ELEMENT_ADD_TO_CART"))?></span></button>
								</form>
							</div>
						<?//ITEM_AVAILABILITY_BUY//
						} else {
							//ITEM_AVAILABILITY//?>
							<div class="available">
								<?if($arElement["CAN_BUY"]) {?>									
									<?if($arParams['SHOW_MAX_QUANTITY'] !== 'N') { ?>                                      
                                        <div class="avl">
                                            <i class="fa fa-check-circle"></i>
                                            <span> 
                                                <?=(!empty($arParams["MESS_SHOW_MAX_QUANTITY"]) ? $arParams["MESS_SHOW_MAX_QUANTITY"] : GetMessage("CT_SGB_ELEMENT_AVAILABLE")) . ' ';
                                                if($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                                    if($arElement["CHECK_QUANTITY"] && $inProductQnt) {
                                                        if($arParams['RELATIVE_QUANTITY_FACTOR'] > $arElement["CATALOG_QUANTITY"])
                                                            echo GetMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_MANY");
                                                        else
                                                            echo GetMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_FEW");
                                                            }
                                                } else {
                                                   if($arElement["CHECK_QUANTITY"] && $inProductQnt)
                                                        echo " " . $arElement["CATALOG_QUANTITY"];
                                                }?>
                                            </span>
                                        </div>
                                    <?}?>                               
								<?} elseif(!$arElement["CAN_BUY"]) {?>									
									<div class="not_avl">
										<i class="fa fa-times-circle"></i>
										<span><?=GetMessage("CT_SGB_ELEMENT_NOT_AVAILABLE")?></span>
									</div>
								<?}?>
							</div>
							<?//ITEM_BUY//?>
							<div class="add2basket_block">
								<?if($arElement["CAN_BUY"]) {
									if($arElement["MIN_PRICE"]["RATIO_BASE_PRICE"] <= 0) {
										//ITEM_ASK_PRICE//?>
										<a id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo" href="javascript:void(0)" rel="nofollow" data-action="ask_price"><i class="fa fa-comment-o"></i><span class="full"><?=GetMessage("CT_SGB_ELEMENT_ASK_PRICE_FULL")?></span><span class="short"><?=GetMessage("CT_SGB_ELEMENT_ASK_PRICE_SHORT")?></span></a>
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
											<button type="button" id="<?=(isset($arElement['SELECT_PROPS']) && !empty($arElement['SELECT_PROPS']) ? $arItemIDs['PROPS_BTN'] : $arItemIDs['BTN_BUY']);?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=($arSetting["NAME_BUTTON_TO_CART"]["VALUE"] ? $arSetting["NAME_BUTTON_TO_CART"]["VALUE"] : GetMessage("CT_SGB_ELEMENT_ADD_TO_CART"))?></span></button>
										</form>
									<?}
								} elseif(!$arElement["CAN_BUY"]) {
									//ITEM_UNDER_ORDER//?>
									<a id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo" href="javascript:void(0)" rel="nofollow" data-action="under_order"><i class="fa fa-clock-o"></i><span><?=GetMessage("CT_SGB_ELEMENT_UNDER_ORDER")?></span></a>
								<?}?>								
							</div>
						<?}?>
						<div class="clr"></div>
						<?//ITEM_COMPARE//
						if($arParams["DISPLAY_COMPARE"]=="Y") {?>
							<div class="compare">
								<a href="javascript:void(0)" class="catalog-item-compare" id="catalog_add2compare_link_<?=$arItemIDs['ID']?>" onclick="return addToCompare('<?=$arElement["COMPARE_URL"]?>', 'catalog_add2compare_link_<?=$arItemIDs["ID"]?>', '<?=SITE_DIR?>');" title="<?=GetMessage('CT_SGB_ELEMENT_ADD_TO_COMPARE')?>" rel="nofollow"><i class="fa fa-bar-chart"></i><i class="fa fa-check"></i></a>
							</div>
						<?}
						//OFFERS_DELAY//
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {								
							if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CAN_BUY"] && $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_BASE_PRICE"] > 0) {
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
									<a href="javascript:void(0)" id="catalog-item-delay-min-<?=$arItemIDs['ID'].'-'.$arElement['TOTAL_OFFERS']['MIN_PRICE']['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', 'quantity_<?=$arItemIDs["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-min-<?=$arItemIDs["ID"]."-".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', '<?=SITE_DIR?>')" title="<?=GetMessage('CT_SGB_ELEMENT_ADD_TO_DELAY')?>" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
								</div>
							<?}
						//ITEM_DELAY//
						} else {
							if($arElement["CAN_BUY"] && $arElement["MIN_PRICE"]["RATIO_BASE_PRICE"] > 0) {
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
									<a href="javascript:void(0)" id="catalog-item-delay-<?=$arItemIDs['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arElement["ID"]?>', 'quantity_<?=$arItemIDs["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-<?=$arItemIDs["ID"]?>', '<?=SITE_DIR?>')" title="<?=GetMessage('CT_SGB_ELEMENT_ADD_TO_DELAY')?>" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
								</div>
							<?}
						}?>								
					</div>					
				</div>
			</div>			
		<?}?>
	</div>
	<div class="clr"></div>
</div>

<?$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedParams = $signer->sign(base64_encode(serialize($arParams)), "sale.gift.basket");

//JS//?>
<script type="text/javascript">
	BX.ready(function() {
		BX.message({			
			GIFT_ADDITEMINCART_ADDED: "<?=GetMessageJS('CT_SGB_ELEMENT_ADDED')?>",			
			GIFT_SITE_DIR: "<?=SITE_DIR?>",
			GIFT_POPUP_WINDOW_MORE_OPTIONS: "<?=GetMessageJS('CT_SGB_ELEMENT_MORE_OPTIONS')?>",			
			GIFT_COMPONENT_TEMPLATE: "<?=$this->GetFolder();?>",
			GIFT_OFFERS_VIEW: "<?=$arSetting['OFFERS_VIEW']['VALUE']?>",
			GIFT_COMPONENT_PARAMS: "<?=CUtil::JSEscape($signedParams)?>"
		});	
		<?foreach($arResult["ITEMS"] as $key => $arElement) {
			if((isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) || $arElement["SELECT_PROPS"]) {				
				$arJSParams = array(					
					"VISUAL" => array(
						"ID" => $arElement["STR_MAIN_ID"],						
						"PROPS_BTN_ID" => $arElement["STR_MAIN_ID"]."_props_btn"
					),
					"PRODUCT" => array(
						"ID" => $arElement["ID"],
						"ITEM_PRICE_MODE" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICE_MODE"] : $arElement["ITEM_PRICE_MODE"],
						"ITEM_PRICES" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICES"] : $arElement["ITEM_PRICES"],
						"ITEM_PRICE_SELECTED" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_PRICE_SELECTED"] : $arElement["ITEM_PRICE_SELECTED"],						
						"CHECK_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CHECK_QUANTITY"] : $arElement["CHECK_QUANTITY"],
						"QUANTITY_FLOAT" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? is_double($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]) : is_double($arElement["CATALOG_MEASURE_RATIO"]),
						"MAX_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : $arElement["CATALOG_MEASURE_RATIO"],
						"STEP_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : $arElement["CATALOG_MEASURE_RATIO"],						
						"PRINT_CURRENCY" => $currency
					)
				);
				$signer = new \Bitrix\Main\Security\Sign\Signer;
				if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
					$arJSParams["OFFER"]["ID"] = $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"];
					$arJSParams["OFFERS_DEF"] = $signer->sign(base64_encode(serialize($arElement["OFFERS_DEF"])), "sale.gift.basket");
				}
				if($arElement["SELECT_PROPS"]) {
					$arJSParams["VISUAL"]["POPUP_BTN_ID"] = $arElement["STR_MAIN_ID"]."_popup_btn";
					$arJSParams["PRODUCT"]["ITEM_PRICES_DEF"] = $signer->sign(base64_encode(serialize($arElement["ITEM_PRICES_DEF"])), "sale.gift.basket");
				}
			} else {
				$arJSParams = array(					
					"VISUAL" => array(
						"ID" => $arElement["STR_MAIN_ID"],						
						"POPUP_BTN_ID" => $arElement["STR_MAIN_ID"]."_popup_btn",
						"BTN_BUY_ID" => $arElement["STR_MAIN_ID"]."_btn_buy"
					),
					"PRODUCT" => array(
						"ID" => $arElement["ID"],
						"NAME" => $arElement["NAME"],
						"PICT" => is_array($arElement["PREVIEW_PICTURE"]) ? $arElement["PREVIEW_PICTURE"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
						"ITEM_PRICE_MODE" => $arElement["ITEM_PRICE_MODE"],
						"ITEM_PRICES" => $arElement["ITEM_PRICES"],
						"ITEM_PRICE_SELECTED" => $arElement["ITEM_PRICE_SELECTED"],						
						"CHECK_QUANTITY" => $arElement["CHECK_QUANTITY"],						
						"QUANTITY_FLOAT" => is_double($arElement["CATALOG_MEASURE_RATIO"]),
						"MAX_QUANTITY" => $arElement["CATALOG_MEASURE_RATIO"],
						"STEP_QUANTITY" => $arElement["CATALOG_MEASURE_RATIO"],						
						"PRINT_CURRENCY" => $currency
					)
				);
			}
			$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $arElement["STR_MAIN_ID"]);?>
			var <?=$strObName?> = new JCSaleGiftBasket(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
		<?}?>
	});
</script>