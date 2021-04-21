<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$curPage = $APPLICATION->GetCurPage();

global $arSetting;

//ORDER_TO_TITLE//
function CmpByName($array1, $array2) {
	if(!isset($array1["NAME"]) || !isset($array2["NAME"]))
		return -1;
	if($array1["NAME"] > $array2["NAME"])
		return -1;
	if($array1["NAME"] < $array2["NAME"])
		return 1;
	if($array1["NAME"] == $array2["NAME"])
		return 0;
}

//JS//?>
<script type="text/javascript">
	//<![CDATA[
	$(function() {
		$(".search_close").click(function() {		
			$(".title-search-result").fadeOut(300);
		});
		$(this).keydown(function(eventObject) {
			if(eventObject.which == 27)
				$(".title-search-result").fadeOut(300);
		});
	});
	//]]>
</script>

<?//CATALOG_SEARCH//
if(!empty($arResult["CATEGORIES"])) {?>
	<a href="javascript:void(0)" class="search_close"><i class="fa fa-times"></i></a>		
	<div id="catalog_search">
		<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory) {
			//ORDER_TO_TITLE//
			if($arParams["ORDER"] == "title" && $arResult["SPHINX"]) {
				uasort($arCategory["ITEMS"],"CmpByName");
			}
			foreach($arCategory["ITEMS"] as $i => $arElement) {
				$arItemIDs = array(
					"ID" => $arElement["STR_MAIN_ID"],
					"POPUP_BTN" => $arElement["STR_MAIN_ID"]."_popup_btn",
					"PROPS_BTN" => $arElement["STR_MAIN_ID"]."_props_btn",
					"BTN_BUY" => $arElement["STR_MAIN_ID"]."_btn_buy"
				);
				
				//PRICES//
				if($arParams["SHOW_PRICE"] == "Y") {
					$price = $currency = false;
					if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
						$price = CCurrencyLang::GetCurrencyFormat($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
						if($price["HIDE_ZERO"] == "Y")
							if(round($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], 0))
								$price["DECIMALS"] = 0;
					} else {
						if($arElement["MIN_PRICE"]["CAN_ACCESS"]) {
							$price = CCurrencyLang::GetCurrencyFormat($arElement["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
							if($price["HIDE_ZERO"] == "Y")
								if(round($arElement["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arElement["MIN_PRICE"]["DISCOUNT_VALUE"], 0))
									$price["DECIMALS"] = 0;
						}
					}
					if(!empty($price)) {
						if(empty($price["THOUSANDS_SEP"]))
							$price["THOUSANDS_SEP"] = " ";
						$currency = str_replace("# ", " ", $price["FORMAT_STRING"]);
					}
				}

				if($category_id === "all") {
					//SEARCH_ALL//
					if($arParams["SHOW_ALL_RESULTS"] == "Y") {?>
						<a class="search_all" href="<?=$arElement['URL']?>"><?=$arElement["NAME"]?></a>
					<?}
				} elseif(isset($arElement["ICON"])) {
					//SEARCH_ITEM//?>
					<div class="tvr_search">						
						<?//ITEM_PREVIEW_PICTURE//?>
						<a class="image" href="<?=$arElement['URL']?>">
							<?if(is_array($arElement["PREVIEW_PICTURE"])) {?>
								<img src="<?=$arElement['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['NAME']?>" title="<?=$arElement['NAME']?>" />
							<?} else {?>
								<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="62" height="62" alt="<?=$arElement['NAME']?>" title="<?=$arElement['NAME']?>" />
							<?}?>
						</a>
						<div class="<?=(!empty($arElement['MIN_PRICE']) || !empty($arElement['TOTAL_OFFERS']['MIN_PRICE']) ? 'item_' : 'cat_');?>title">
							<?//ITEM_ARTICLE//
							if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]) && !$arElement["COLLECTION"]) {?>
								<span class="article"><?=GetMessage("CATALOG_ELEMENT_ARTNUMBER").$arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"];?></span>
							<?}
							//ITEM_TITLE//?>
							<a href="<?=$arElement['URL']?>"><?=$arElement["NAME"]?></a>
							<?//ITEM_PROPERTIES//
							if(!empty($arElement["DISPLAY_PROPERTIES"]) && !$arElement["COLLECTION"]) {?>
								<div class="properties">
									<?foreach($arElement["DISPLAY_PROPERTIES"] as $k => $v) {
										if($v["PROPERTY_TYPE"] != "S") {?>
											<span class="property"><?=$v["NAME"].": ".strip_tags($v["DISPLAY_VALUE"])?></span>
										<?}
									}?>
								</div>
							<?}?>
						</div>						
						<?//TOTAL_OFFERS_ITEM_PRICE//
						if($arParams["SHOW_PRICE"] == "Y") {
							//TOTAL_OFFERS_PRICE//
							if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {?>
								<div class="search_price">
									<?if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0) {?>
										<span class="no-price">											
											<span class="unit">
												<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
												<br />
												<span><?=(!empty($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"] : "";?></span>
											</span>
										</span>													
									<?} else {?>										
										<span class="price">
											<?=($arElement["TOTAL_OFFERS"]["FROM"] == "Y") ? "<span class='from'>".GetMessage("CATALOG_ELEMENT_FROM")."</span>" : "";?>
											<?=number_format($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);
											if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
												<span class="price-reference">
													<?=number_format($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $price["REFERENCE_DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
												</span>
											<?}?>
											<span class="unit">												
												<?=$currency?>
												<span><?=(!empty($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"] : "";?></span>
											</span>											
										</span>									
									<?}?>
								</div>
							<?//ITEM_PRICE//
							} else {
								if($arElement["MIN_PRICE"]["CAN_ACCESS"]) {?>
									<div class="search_price">
										<?if($arElement["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0) {?>
											<span class="no-price">
												<span class="unit">
													<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
													<br />
													<span><?=(!empty($arElement["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["CATALOG_MEASURE_NAME"] : "";?></span>
												</span>
											</span>																
										<?} else {?>													
											<span class="price">
												<?=number_format($arElement["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);
												if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
													<span class="price-reference">
														<?=number_format($arElement["MIN_PRICE"]["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $price["REFERENCE_DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
													</span>
												<?}?>
												<span class="unit">
													<?=$currency?>
													<span><?=(!empty($arElement["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["CATALOG_MEASURE_NAME"] : "";?></span>
												</span>														
											</span>
										<?}?>
									</div>
								<?}
							}
						}						
						//OFFERS_ITEM_BUY//
						if($arParams["SHOW_ADD_TO_CART"] == "Y") {
							//OFFERS_BUY//
							if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {?>
								<div class="buy_more">
									<div class="add2basket_block">
										<form action="<?=$curPage?>" class="add2basket_form">
											<a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$arItemIDs['ID']?>"><span>-</span></a>
											<input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['TOTAL_OFFERS']['MIN_PRICE']['CATALOG_MEASURE_RATIO']?>"/>
											<a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$arItemIDs['ID']?>"><span>+</span></a>
											<button type="button" id="<?=$arItemIDs['PROPS_BTN']?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i></button>
										</form>
									</div>
								</div>
							<?//ITEM_BUY//
							} else {
								if($arElement["CAN_BUY"] && !$arElement["COLLECTION"]) {?>
									<div class="buy_more">
										<div class="add2basket_block">
											<?if($arElement["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0) {
												//ITEM_ASK_PRICE//?>
												<form action="javascript:void(0)" class="apuo_form">										
													<input type="hidden" name="ACTION" value="ask_price" />
													<?$properties = array();
													if(!empty($arElement["DISPLAY_PROPERTIES"])) {
														foreach($arElement["DISPLAY_PROPERTIES"] as $propOffer) {
															if($propOffer["PROPERTY_TYPE"] != "S") {
																$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
															}
														}
														$properties = implode("; ", $properties);
													}
													$elementName = !empty($properties) ? $arElement["NAME"]." (".$properties.")" : $arElement["NAME"];?>
													<input type="hidden" name="NAME" value="<?=$elementName?>" />
													<button type="button" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo"><i class="fa fa-comment-o"></i><span class="short"><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_SHORT")?></span></button>
												</form>
											<?} else {
												if(isset($arElement["SELECT_PROPS"]) && !empty($arElement["SELECT_PROPS"])) {?>
													<form action="<?=$curPage?>" class="add2basket_form">
												<?} else {?>
													<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_search_form">
												<?}?>
													<a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$arItemIDs['ID']?>"><span>-</span></a>
													<input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['CATALOG_MEASURE_RATIO']?>"/>
													<a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$arItemIDs['ID']?>"><span>+</span></a>
													<?if(!isset($arElement["SELECT_PROPS"]) || empty($arElement["SELECT_PROPS"])) {?>
														<input type="hidden" name="ID" value="<?=$arElement['ITEM_ID']?>"/>
														<?$props = array();
														if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {			
															$props[] = array(
																"NAME" => $arElement["PROPERTIES"]["ARTNUMBER"]["NAME"],
																"CODE" => $arElement["PROPERTIES"]["ARTNUMBER"]["CODE"],
																"VALUE" => $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]
															);												
														}
														if(!empty($arElement["DISPLAY_PROPERTIES"])) {										
															foreach($arElement["DISPLAY_PROPERTIES"] as $propOffer) {
																if($propOffer["PROPERTY_TYPE"] != "S") {
																	$props[] = array(
																		"NAME" => $propOffer["NAME"],
																		"CODE" => $propOffer["CODE"],
																		"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
																	);
																}
															}
														}
														$props = !empty($props) ? strtr(base64_encode(serialize($props)), "+/=", "-_,") : "";?>
														<input type="hidden" name="PROPS" value="<?=$props?>" />
													<?}?>															
													<button type="button" id="<?=(isset($arElement['SELECT_PROPS']) && !empty($arElement['SELECT_PROPS']) ? $arItemIDs['PROPS_BTN'] : $arItemIDs['BTN_BUY']);?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i></button>
												</form>
											<?}?>
										</div>
									</div>
								<?} elseif(!$arElement["CAN_BUY"] && !$arElement["COLLECTION"]) {
									if(!empty($arElement["MIN_PRICE"])) {?>
										<div class="buy_more">
											<div class="add2basket_block">
												<?//ITEM_UNDER_ORDER//?>
												<form action="javascript:void(0)" class="apuo_form">										
													<input type="hidden" name="ACTION" value="under_order" />
													<?$properties = array();
													if(!empty($arElement["DISPLAY_PROPERTIES"])) {
														foreach($arElement["DISPLAY_PROPERTIES"] as $propOffer) {
															if($propOffer["PROPERTY_TYPE"] != "S") {
																$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
															}
														}
														$properties = implode("; ", $properties);
													}
													$elementName = !empty($properties) ? $arElement["NAME"]." (".$properties.")" : $arElement["NAME"];?>
													<input type="hidden" name="NAME" value="<?=$elementName?>" />
													<button type="button" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo"><i class="fa fa-clock-o"></i><span class="short"><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></button>
												</form>
											</div>
										</div>
									<?}												
								}
							}
						}?>										
					</div>							
				<?}
			}
		}?>
	</div>

	<?//JS//?>
	<script type="text/javascript">
		BX.ready(function() {
			BX.message({			
				SEARCH_ADDITEMINCART_ADDED: "<?=GetMessageJS('CATALOG_ELEMENT_ADDED')?>",
				SEARCH_POPUP_WINDOW_TITLE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_TITLE')?>",			
				SEARCH_POPUP_WINDOW_BTN_CLOSE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_CLOSE')?>",
				SEARCH_POPUP_WINDOW_BTN_ORDER: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_ORDER')?>",
				SEARCH_SITE_DIR: "<?=SITE_DIR?>",
				SEARCH_POPUP_WINDOW_MORE_OPTIONS: "<?=GetMessageJS('CATALOG_ELEMENT_MORE_OPTIONS')?>",			
				SEARCH_COMPONENT_TEMPLATE: "<?=$this->GetFolder();?>",
				SEARCH_OFFERS_VIEW: "<?=$arSetting['OFFERS_VIEW']['VALUE']?>",
				SEARCH_COMPONENT_PARAMS: "<?=CUtil::PhpToJSObject($arParams)?>"
			});
			<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory) {
				foreach($arCategory["ITEMS"] as $i => $arElement) {			
					if((isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) || $arElement["SELECT_PROPS"]) {				
						$arJSParams = array(					
							"VISUAL" => array(
								"ID" => $arElement["STR_MAIN_ID"],
								"PROPS_BTN_ID" => $arElement["STR_MAIN_ID"]."_props_btn"
							),
							"PRODUCT" => array(
								"ID" => $arElement["ITEM_ID"],
								"CHECK_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CHECK_QUANTITY"] : $arElement["CHECK_QUANTITY"],
								"QUANTITY_FLOAT" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? is_double($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]) : is_double($arElement["CATALOG_MEASURE_RATIO"]),
								"MAX_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_QUANTITY"] : $arElement["CATALOG_QUANTITY"],
								"STEP_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : $arElement["CATALOG_MEASURE_RATIO"]
							)
						);
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]))
							$arJSParams["OFFER"]["ID"] = $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"];
					} else {
						$arJSParams = array(					
							"VISUAL" => array(
								"ID" => $arElement["STR_MAIN_ID"],
								"POPUP_BTN_ID" => $arElement["STR_MAIN_ID"]."_popup_btn",
								"BTN_BUY_ID" => $arElement["STR_MAIN_ID"]."_btn_buy"
							),
							"PRODUCT" => array(
								"ID" => $arElement["ITEM_ID"],
								"NAME" => $arElement["NAME"],
								"PICT" => is_array($arElement["PREVIEW_PICTURE"]) ? $arElement["PREVIEW_PICTURE"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
								"CHECK_QUANTITY" => $arElement["CHECK_QUANTITY"],						
								"QUANTITY_FLOAT" => is_double($arElement["CATALOG_MEASURE_RATIO"]),
								"MAX_QUANTITY" => $arElement["CATALOG_QUANTITY"],
								"STEP_QUANTITY" => $arElement["CATALOG_MEASURE_RATIO"]
							)
						);
					}
					$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $arElement["STR_MAIN_ID"]);?>
					var <?=$strObName?> = new JCCatalogSearchProducts(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
				<?}
			}?>
		});
	</script>
<?} else {?>
	<a href="javascript:void(0)" class="pop-up-close search_close"><i class="fa fa-times"></i></a>	
	<div id="catalog_search_empty"><?=GetMessage("CATALOG_EMPTY_RESULT")?></div>			
<?}?>