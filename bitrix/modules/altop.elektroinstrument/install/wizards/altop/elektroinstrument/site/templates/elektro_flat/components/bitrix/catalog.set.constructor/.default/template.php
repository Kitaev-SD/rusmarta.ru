<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

global $arSetting;

$templateData = array(	
	"CURRENCIES" => CUtil::PhpToJSObject($arResult["CURRENCIES"], false, true, true)
);
$curJsId = $arParams["STR_MAIN_ID"];?>

<script type="text/javascript">
	BX.ready(function() {
		BX.bindDelegate(BX("set-constructor-<?=$curJsId?>"), "click", {className: "other-items-section-title"}, function() {
			BX.toggleClass(this, ["active", ""]);
			
			var currIcon = BX.findChild(this, {tagName: "i"}, true, false);
			if(!!currIcon)
				BX.toggleClass(currIcon, ["fa-minus", "fa-plus"]);
			
			var currItemsCont = BX.findChild(this.parentNode, {className: "other-items-section-childs"}, true, false);
			if(!!currItemsCont)
				$(currItemsCont).slideToggle();
		});
	});
</script>

<div id="set-constructor-<?=$curJsId?>" class="set-constructor">
	<div class="h3"><?=GetMessage("CATALOG_SET_TITLE")?></div>
	<div class="catalog-item-cards">		
		<?//ORIGINAL_ITEM//?>
		<div class="catalog-item-card original-item">			
			<div class="catalog-item-info">
				<div class="item-image-cont">
					<div class="item-image">					
						<span>
							<?if(is_array($arResult["ELEMENT"]["PREVIEW_PICTURE"])):?>
								<img class="item_img" src="<?=$arResult['ELEMENT']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arResult['ELEMENT']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arResult['ELEMENT']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arResult['ELEMENT']['NAME']?>" />
							<?else:?>
								<img class="item_img" src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arResult['ELEMENT']['NAME']?>" />
							<?endif?>
						</span>							
					</div>
				</div>
				<div class="item-all-title">
					<span class="item-title" title="<?=$arResult['ELEMENT']['NAME']?>">
						<?=$arResult["ELEMENT"]["NAME"]?>
					</span>
				</div>
				<div class="item-price-cont<?=(!in_array('OLD_PRICE', $arSetting['PRODUCT_TABLE_VIEW']['VALUE']) ? ' one' : '').($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>">					
					<?$price = CCurrencyLang::GetCurrencyFormat($arResult["ELEMENT"]["PRICE_CURRENCY"], LANGUAGE_ID);
					if(empty($price["THOUSANDS_SEP"])):
						$price["THOUSANDS_SEP"] = " ";
					endif;					
					if($price["HIDE_ZERO"] == "Y"):						
						if(round($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], 0)):
							$price["DECIMALS"] = 0;
						endif;
					endif;
					$currency = str_replace("# ", " ", $price["FORMAT_STRING"]);

					if($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] <= 0):?>												
						<div class="item-no-price">
							<span class="unit">
								<?=GetMessage("CATALOG_SET_NO_PRICE")." ".GetMessage("CATALOG_SET_UNIT")." ".($arResult["ELEMENT"]["SET_QUANTITY"] > 0 && $arResult["ELEMENT"]["SET_QUANTITY"] != 1 ? $arResult["ELEMENT"]["SET_QUANTITY"]." " : "").$arResult["ELEMENT"]["MEASURE"]["SYMBOL_RUS"];?>
							</span>
						</div>
					<?else:?>
						<div class="item-price">
							<?if(in_array("OLD_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):
								if($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] < $arResult["ELEMENT"]["PRICE_VALUE"]):?>
									<span class="catalog-item-price-old">
										<?=$arResult["ELEMENT"]["PRICE_PRINT_VALUE"];?>
									</span>
								<?endif;
							endif;?>
							<span class="catalog-item-price">
								<?=number_format($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
								<span class="unit"><?=$currency?></span>
							</span>
							<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
								<span class="catalog-item-price-reference">
									<?=CCurrencyLang::CurrencyFormat($arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["ELEMENT"]["PRICE_CURRENCY"], true);?>
								</span>
							<?endif;?>
						</div>
					<?endif;?>
				</div>
			</div>
		</div>
		<?//ADDED_ITEMS//?>
		<div class="added-items" data-role="added-items">
			<div data-set-message="empty-set" style="display:none;"></div>
			<?foreach($arResult["SET_ITEMS"]["DEFAULT"] as $key => $arItem):
				if(!empty($arItem["DISPLAY_PROPERTIES"])):
					$shortProperties = array();					
					foreach($arItem["DISPLAY_PROPERTIES"] as $propItem) {
						$shortProperties[] = strip_tags($propItem["DISPLAY_VALUE"]);						
					}
					$shortProperties = implode(", ", $shortProperties);
					$itemShortName = strip_tags($arItem["NAME"])." (".$shortProperties.")";
				else:
					$itemShortName = strip_tags($arItem["NAME"]);
				endif;

				$price = CCurrencyLang::GetCurrencyFormat($arItem["PRICE_CURRENCY"], LANGUAGE_ID);
				if(empty($price["THOUSANDS_SEP"])):
					$price["THOUSANDS_SEP"] = " ";
				endif;						
				if($price["HIDE_ZERO"] == "Y"):							
					if(round($arItem["PRICE_DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arItem["PRICE_DISCOUNT_VALUE"], 0)):
						$price["DECIMALS"] = 0;
					endif;
				endif;
				$currency = str_replace("# ", " ", $price["FORMAT_STRING"]);?>
				
				<div class="catalog-item added-item" data-id="<?=$arItem['ID']?>" data-iblock-id="<?=$arItem['IBLOCK_ID']?>" data-section-id="<?=$arItem['IBLOCK_SECTION_ID']?>" data-url="<?=$arItem['DETAIL_PAGE_URL']?>" data-img="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" data-name="<?=$itemShortName?>" data-article="<?=(in_array('ARTNUMBER', $arSetting['PRODUCT_TABLE_VIEW']['VALUE']) ? (!empty($arItem['PROPERTIES']['ARTNUMBER']['VALUE']) ? $arItem['PROPERTIES']['ARTNUMBER']['VALUE'] : '-') : '');?>" data-price="<?=$arItem['PRICE_DISCOUNT_VALUE'] * $arItem['BASKET_QUANTITY']?>" data-format-price="<?=number_format($arItem['PRICE_DISCOUNT_VALUE'] * $arItem['BASKET_QUANTITY'], $price['DECIMALS'], $price['DEC_POINT'], $price['THOUSANDS_SEP'])?>" data-reference-price="<?=($arSetting['REFERENCE_PRICE']['VALUE'] == 'Y' && !empty($arSetting['REFERENCE_PRICE_COEF']['VALUE']) ? $arItem['PRICE_DISCOUNT_VALUE']  * $arItem['BASKET_QUANTITY'] * $arSetting['REFERENCE_PRICE_COEF']['VALUE'] : $arItem['PRICE_DISCOUNT_VALUE']  * $arItem['BASKET_QUANTITY']);?>" data-format-reference-price="<?=($arSetting['REFERENCE_PRICE']['VALUE'] == 'Y' && !empty($arSetting['REFERENCE_PRICE_COEF']['VALUE']) ? number_format($arItem['PRICE_DISCOUNT_VALUE']  * $arItem['BASKET_QUANTITY'] * $arSetting['REFERENCE_PRICE_COEF']['VALUE'], $price['DECIMALS'], $price['DEC_POINT'], $price['THOUSANDS_SEP']) : number_format($arItem['PRICE_DISCOUNT_VALUE']  * $arItem['BASKET_QUANTITY'], $price['DECIMALS'], $price['DEC_POINT'], $price['THOUSANDS_SEP']));?>" data-old-price="<?=$arItem['PRICE_VALUE'] * $arItem['BASKET_QUANTITY']?>" data-diff-price="<?=$arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE'] * $arItem['BASKET_QUANTITY']?>" data-currency="<?=$currency?>" data-measure="<?=GetMessage('CATALOG_SET_UNIT').' '.($arItem['BASKET_QUANTITY'] > 0 && $arItem['BASKET_QUANTITY'] != 1 ? $arItem['BASKET_QUANTITY'].' ' : '').$arItem['MEASURE']['SYMBOL_RUS']?>">
					<div class="catalog-item-info">
						<div class="catalog-item-image-cont">
							<div class="catalog-item-image">
								<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
									<?if(is_array($arItem["PREVIEW_PICTURE"])):?>
										<img class="item_img" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
									<?else:?>
										<img class="item_img" src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arItem['NAME']?>" />
									<?endif;?>
								</a>							
							</div>
						</div>
						<div class="catalog-item-title">
							<a href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$itemShortName?>"><?=$itemShortName?></a>
							<?if(in_array("ARTNUMBER", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
								<div class="catalog-item-article">
									<?=GetMessage("CATALOG_SET_ARTNUMBER").(!empty($arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-");?>
								</div>
							<?endif;?>
						</div>
						<div class="item-price">
							<span class="catalog-item-price">
								<span><?=number_format($arItem["PRICE_DISCOUNT_VALUE"] * $arItem["BASKET_QUANTITY"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?></span>
								<span class="unit">
									<?=$currency?>
									<span><?=GetMessage("CATALOG_SET_UNIT")." ".($arItem["BASKET_QUANTITY"] > 0 && $arItem["BASKET_QUANTITY"] != 1 ? $arItem["BASKET_QUANTITY"]." " : "").$arItem["MEASURE"]["SYMBOL_RUS"];?></span>
								</span>
								<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
									<span class="catalog-item-price-reference">
										<?=number_format($arItem["PRICE_DISCOUNT_VALUE"] * $arItem["BASKET_QUANTITY"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
										<span><?=$currency?></span>
									</span>
								<?endif;?>
							</span>
							<?if(in_array("OLD_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):
								if($arItem["PRICE_DISCOUNT_VALUE"] < $arItem["PRICE_VALUE"]):?>
									<span class="catalog-item-price-old">
										<?=CCurrencyLang::CurrencyFormat($arItem["PRICE_VALUE"] * $arItem["BASKET_QUANTITY"], $arItem["PRICE_CURRENCY"], true);?>
									</span>
								<?endif;
							endif;?>
						</div>
						<div class="catalog-item-delete">
							<a href="javascript:void(0)" data-role="set-delete-btn"><i class="fa fa-times"></i></a>
						</div>
					</div>
				</div>
			<?endforeach?>
		</div>		
		<?//RESULT_ITEM//?>
		<div class="catalog-item-card result-item">
			<div class="catalog-item-info">
				<div class="item-image-cont">
					<div class="item-image">
						<i class="fa fa-check"></i>
					</div>
				</div>
				<div class="item-price-cont<?=(!in_array('OLD_PRICE', $arSetting['PRODUCT_TABLE_VIEW']['VALUE']) ? ' one' : '').($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>">
					<?$price = CCurrencyLang::GetCurrencyFormat($arResult["SET_ITEMS"]["PRICE_CURRENCY"], LANGUAGE_ID);
					if(empty($price["THOUSANDS_SEP"])):
						$price["THOUSANDS_SEP"] = " ";
					endif;					
					if($price["HIDE_ZERO"] == "Y"):						
						if(round($arResult["SET_ITEMS"]["PRICE_VALUE"], $price["DECIMALS"]) == round($arResult["SET_ITEMS"]["PRICE_VALUE"], 0)):
							$price["DECIMALS"] = 0;
						endif;
					endif;
					$currency = str_replace("# ", " ", $price["FORMAT_STRING"]);?>

					<div class="item-price">
						<?$showOldDiffPrice = false;
						if($arResult["SET_ITEMS"]["PRICE_VALUE"] < $arResult["SET_ITEMS"]["OLD_PRICE_VALUE"]):
							$showOldDiffPrice = true;
						endif;
						if(in_array("OLD_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
							<span class="catalog-item-price-old" data-role="set-old-price"<?=($showOldDiffPrice != true ? " style='display:none;'" : "");?>><?=($showOldDiffPrice == true ? $arResult["SET_ITEMS"]["OLD_PRICE"] : "");?></span>
						<?endif;
						if(in_array("PERCENT_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
							<span class="catalog-item-price-percent"<?=($showOldDiffPrice != true ? " style='display:none;'" : "");?>>
								<?=GetMessage("CATALOG_SET_DISCOUNT_DIFF")?>
								<span data-role="set-diff-price"><?=($showOldDiffPrice == true ? $arResult["SET_ITEMS"]["PRICE_DISCOUNT_DIFFERENCE"] : "");?></span>
							</span>
						<?endif;?>
						<span class="catalog-item-price">
							<span data-role="set-price">
								<?=number_format($arResult["SET_ITEMS"]["PRICE_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
							</span>
							<span class="unit"><?=$currency?></span>
						</span>						
						<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
							<span class="catalog-item-price-reference" data-role="set-ref-price">
								<?=CCurrencyLang::CurrencyFormat($arResult["SET_ITEMS"]["PRICE_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["SET_ITEMS"]["PRICE_CURRENCY"], true);?>
							</span>
						<?endif;?>
					</div>
				</div>
				<div class="buy_more">
					<div class="add2basket_block">						
						<button name="add2basket" class="btn_buy" data-role="set-buy-btn" value="<?=GetMessage('CATALOG_SET_ADD_TO_CART')?>"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_SET_ADD_TO_CART")?></span></button>
					</div>				
				</div>
			</div>
		</div>	
	</div>	
	<?//OTHER_ITEMS//
	if(!empty($arResult["SET_ITEMS"]["SECTIONS"])):?>
		<div class="other-items" data-role="other-items">
			<?foreach($arResult["SET_ITEMS"]["SECTIONS"] as $arSection):?>
				<div class="other-items-section">					
					<div class="other-items-section-title active">
						<span class="cont">
							<span class="text"><?=$arSection["NAME"]?></span>
							<span class="qnt_cont">
								<span class="qnt"><?=count($arSection["ITEMS"])?></span>
							</span>
						</span>
						<i class="fa fa-minus"></i>
					</div>
					<div class="other-items-section-childs">
						<div class="other-items-section-slider-cont">
							<div class="other-items-section-slider" id="other-items-section-slider-<?=$arSection['ID']?>" data-style-left="0" style="left:0px;">
								<?foreach($arSection["ITEMS"] as $arItem):
									if(!empty($arItem["DISPLAY_PROPERTIES"])):
										$shortProperties = array();
										$properties = array();
										foreach($arItem["DISPLAY_PROPERTIES"] as $propItem) {
											$shortProperties[] = strip_tags($propItem["DISPLAY_VALUE"]);
											$properties[] = $propItem["NAME"].": ".strip_tags($propItem["DISPLAY_VALUE"]);
										}
										$shortProperties = implode(", ", $shortProperties);
										$properties = implode("; ", $properties);										
										$itemShortName = strip_tags($arItem["NAME"])." (".$shortProperties.")";
										$itemName = strip_tags($arItem["NAME"])." (".$properties.")";
									else:
										$itemShortName = strip_tags($arItem["NAME"]);
										$itemName = strip_tags($arItem["NAME"]);
									endif;

									$price = CCurrencyLang::GetCurrencyFormat($arItem["PRICE_CURRENCY"], LANGUAGE_ID);
									if(empty($price["THOUSANDS_SEP"])):
										$price["THOUSANDS_SEP"] = " ";
									endif;									
									if($price["HIDE_ZERO"] == "Y"):										
										if(round($arItem["PRICE_DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arItem["PRICE_DISCOUNT_VALUE"], 0)):
											$price["DECIMALS"] = 0;
										endif;
									endif;
									$currency = str_replace("# ", " ", $price["FORMAT_STRING"]);?>

									<div class="catalog-item-card other-item" data-id="<?=$arItem['ID']?>" data-iblock-id="<?=$arItem['IBLOCK_ID']?>" data-section-id="<?=$arItem['IBLOCK_SECTION_ID']?>" data-url="<?=$arItem['DETAIL_PAGE_URL']?>" data-img="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" data-name="<?=$itemShortName?>" data-article="<?=(in_array('ARTNUMBER', $arSetting['PRODUCT_TABLE_VIEW']['VALUE']) ? (!empty($arItem['PROPERTIES']['ARTNUMBER']['VALUE']) ? $arItem['PROPERTIES']['ARTNUMBER']['VALUE'] : '-') : '');?>" data-price="<?=$arItem['PRICE_DISCOUNT_VALUE'] * $arItem['BASKET_QUANTITY']?>" data-format-price="<?=number_format($arItem['PRICE_DISCOUNT_VALUE'] * $arItem['BASKET_QUANTITY'], $price['DECIMALS'], $price['DEC_POINT'], $price['THOUSANDS_SEP'])?>" data-reference-price="<?=($arSetting['REFERENCE_PRICE']['VALUE'] == 'Y' && !empty($arSetting['REFERENCE_PRICE_COEF']['VALUE']) ? $arItem['PRICE_DISCOUNT_VALUE']  * $arItem['BASKET_QUANTITY'] * $arSetting['REFERENCE_PRICE_COEF']['VALUE'] : $arItem['PRICE_DISCOUNT_VALUE']  * $arItem['BASKET_QUANTITY']);?>" data-format-reference-price="<?=($arSetting['REFERENCE_PRICE']['VALUE'] == 'Y' && !empty($arSetting['REFERENCE_PRICE_COEF']['VALUE']) ? number_format($arItem['PRICE_DISCOUNT_VALUE']  * $arItem['BASKET_QUANTITY'] * $arSetting['REFERENCE_PRICE_COEF']['VALUE'], $price['DECIMALS'], $price['DEC_POINT'], $price['THOUSANDS_SEP']) : number_format($arItem['PRICE_DISCOUNT_VALUE']  * $arItem['BASKET_QUANTITY'], $price['DECIMALS'], $price['DEC_POINT'], $price['THOUSANDS_SEP']));?>" data-old-price="<?=$arItem['PRICE_VALUE'] * $arItem['BASKET_QUANTITY']?>" data-diff-price="<?=$arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE'] * $arItem['BASKET_QUANTITY']?>" data-currency="<?=$currency?>" data-measure="<?=GetMessage('CATALOG_SET_UNIT').' '.($arItem['BASKET_QUANTITY'] > 0 && $arItem['BASKET_QUANTITY'] != 1 ? $arItem['BASKET_QUANTITY'].' ' : '').$arItem['MEASURE']['SYMBOL_RUS']?>">
										<div class="catalog-item-info">
											<div class="item-image-cont">
												<div class="item-image">
													<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
														<?if(is_array($arItem["PREVIEW_PICTURE"])):?>
															<img class="item_img" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
														<?else:?>
															<img class="item_img" src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arItem['NAME']?>" />
														<?endif;?>
													</a>									
												</div>
											</div>																						
											<div class="item-all-title">												
												<a class="item-title" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$itemShortName?>">
													<?=$itemShortName?>
												</a>
											</div>
											<?if(in_array("ARTNUMBER", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
												<div class="item-article">
													<?=GetMessage("CATALOG_SET_ARTNUMBER").(!empty($arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arItem["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-");?>
												</div>
											<?endif;?>
											<div class="item-price-cont<?=(!in_array('OLD_PRICE', $arSetting['PRODUCT_TABLE_VIEW']['VALUE']) ? ' one' : '').($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>">
												<?if($arItem["PRICE_DISCOUNT_VALUE"] <= 0):?>												
													<div class="item-no-price">	
														<span class="unit">
															<?=GetMessage("CATALOG_SET_NO_PRICE")?>
															<span><?=GetMessage("CATALOG_SET_UNIT")." ".($arItem["BASKET_QUANTITY"] > 0 && $arItem["BASKET_QUANTITY"] != 1 ? $arItem["BASKET_QUANTITY"]." " : "").$arItem["MEASURE"]["SYMBOL_RUS"];?></span>
														</span>												
													</div>
												<?else:?>
													<div class="item-price">
														<?if(in_array("OLD_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):
															if($arItem["PRICE_DISCOUNT_VALUE"] < $arItem["PRICE_VALUE"]):?>
																<span class="catalog-item-price-old">
																	<?=CCurrencyLang::CurrencyFormat($arItem["PRICE_VALUE"] * $arItem["BASKET_QUANTITY"], $arItem["PRICE_CURRENCY"], true);?>
																</span>
															<?endif;
														endif;?>
														<span class="catalog-item-price">
															<?=number_format($arItem["PRICE_DISCOUNT_VALUE"] * $arItem["BASKET_QUANTITY"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
														</span>
														<span class="unit">
															<?=$currency?>
															<span><?=GetMessage("CATALOG_SET_UNIT")." ".($arItem["BASKET_QUANTITY"] > 0 && $arItem["BASKET_QUANTITY"] != 1 ? $arItem["BASKET_QUANTITY"]." " : "").$arItem["MEASURE"]["SYMBOL_RUS"];?></span>
														</span>
														<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
															<span class="catalog-item-price-reference">
																<?=CCurrencyLang::CurrencyFormat($arItem["PRICE_DISCOUNT_VALUE"] * $arItem["BASKET_QUANTITY"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arItem["PRICE_CURRENCY"], true);?>
															</span>
														<?endif;?>
													</div>
												<?endif;?>
											</div>
											<div class="buy_more">
												<div class="add2basket_block">
													<?if($arItem["CAN_BUY"]):
														if($arItem["PRICE_DISCOUNT_VALUE"] <= 0):
															//ASK_PRICE//?>
															<a class="btn_buy apuo" href="javascript:void(0)" rel="nofollow" data-action="ask_price"><i class="fa fa-comment-o"></i><span class="full"><?=GetMessage("CATALOG_SET_ASK_PRICE_FULL")?></span><span class="short"><?=GetMessage("CATALOG_SET_ASK_PRICE_SHORT")?></span></a>
														<?else:?>
															<button name="add2set" class="btn_buy" data-role="set-add-btn" value="<?=GetMessage('CATALOG_SET_ADD_TO_SET')?>"><i class="fa fa-plus"></i><span class="full"><?=GetMessage("CATALOG_SET_ADD_TO_SET_FULL")?></span><span class="short"><?=GetMessage("CATALOG_SET_ADD_TO_SET_SHORT")?></span></button>
														<?endif;
													else:
														//UNDER_ORDER//?>
														<a class="btn_buy apuo" href="javascript:void(0)" rel="nofollow" data-action="under_order"><i class="fa fa-clock-o"></i><span><?=GetMessage("CATALOG_SET_UNDER_ORDER")?></span></a>
													<?endif;?>
												</div>				
											</div>
										</div>						
									</div>
								<?endforeach;?>
							</div>
						</div>
						<div class="other-items-section-slider-arrow left" id="other-items-section-slider-left-<?=$arSection['ID']?>" data-role="arrow-left"><span class="arrow-cont"><i class="fa fa-chevron-left"></i></span></div>
						<div class="other-items-section-slider-arrow right" id="other-items-section-slider-right-<?=$arSection['ID']?>" data-role="arrow-right"><span class="arrow-cont"><i class="fa fa-chevron-right"></i></span></div>
					</div>
				</div>
			<?endforeach;?>
		</div>
	<?endif;?>
</div>

<?$arJsParams = array(		
	"numSetItems" => count($arResult["SET_ITEMS"]["DEFAULT"]),	
	"jsId" => $curJsId,
	"parentContId" => "set-constructor-".$curJsId,	
	"ajaxPath" => $this->GetFolder().'/ajax.php',
	"popupPath" => $this->GetFolder().'/popup.php',
	"currency" => $arResult["ELEMENT"]["PRICE_CURRENCY"],	
	"mainElementPict" => is_array($arResult["ELEMENT"]["PREVIEW_PICTURE"]) ? $arResult["ELEMENT"]["PREVIEW_PICTURE"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
	"mainElementPrice" => $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"],
	"mainElementRefPrice" => $arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"] : $arResult["ELEMENT"]["PRICE_DISCOUNT_VALUE"],	
	"mainElementOldPrice" => $arResult["ELEMENT"]["PRICE_VALUE"],
	"mainElementDiffPrice" => $arResult["ELEMENT"]["PRICE_DISCOUNT_DIFFERENCE_VALUE"],
	"mainElementBasketQuantity" => $arResult["ELEMENT"]["BASKET_QUANTITY"],
	"siteDir" => SITE_DIR,
	"lid" => SITE_ID,	
	"basketUrl" => $arParams["BASKET_URL"],
	"setIds" => $arResult["DEFAULT_SET_IDS"],
	"offersCartProps" => $arParams["OFFERS_CART_PROPERTIES"],
	"itemsRatio" => $arResult["BASKET_QUANTITY"],
	"noFotoSrc" => SITE_TEMPLATE_PATH."/images/no-photo.jpg",
	"messages" => array(		
		"ARTICLE" => GetMessage("CATALOG_SET_ARTNUMBER"),		
		"ADD_BUTTON_FULL" => GetMessage("CATALOG_SET_ADD_TO_SET_FULL"),
		"ADD_BUTTON_SHORT" => GetMessage("CATALOG_SET_ADD_TO_SET_SHORT"),
		"ADDITEMINCART_ADDED" => GetMessage("CATALOG_SET_ADDED"),
		"EMPTY_SET" => GetMessage("CATALOG_SET_EMPTY_SET"),
		"POPUP_TITLE" => GetMessage("CATALOG_SET_ADDITEMINCART_TITLE"),
		"POPUP_BTN_CLOSE" => GetMessage("CATALOG_SET_ADDITEMINCART_BTN_CLOSE"),
		"POPUP_BTN_ORDER" => GetMessage("CATALOG_SET_ADDITEMINCART_BTN_ORDER")
	)
);?>

<script type="text/javascript">
	BX.ready(function() {		
		new BX.Catalog.SetConstructor(<?=CUtil::PhpToJSObject($arJsParams, false, true, true)?>);
	});
</script>