<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;

$arSetting = CElektroinstrument::GetFrontParametrsValues(SITE_ID);
$isPreviewPicture = is_array($arResult["PREVIEW_PICTURE"]);
$inProductQnt = in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]);
$inBtnBoc = in_array("BUTTON_BOC", $arSetting["CATALOG_DETAIL"]);
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]);

$strMainID = $arParams["STR_MAIN_ID"];
$arItemIDs = array(
	"ID" => $strMainID,
	"PICT" => $strMainID."_picture",
	"PRICE" => $strMainID."_price",
	"BUY" => $strMainID."_buy",
	"PROP_DIV" => $strMainID."_sku_tree",
	"PROP" => $strMainID."_prop_",
	"SELECT_PROP_DIV" => $strMainID."_propdiv",
	"SELECT_PROP" => $strMainID."_select_prop_",
	"POPUP_BTN" => $strMainID."_popup_btn",
	"BTN_BUY" => $strMainID."_btn_buy"
);
if(!$arParams["IS_GIFT"])
	$arItemIDs["PRICE_MATRIX_BTN"] = $strMainID."_price_ranges_btn_props";
$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

$templateData = array(	
	"CURRENCIES" => CUtil::PhpToJSObject($arResult["CURRENCIES"], false, true, true)
);

//PREVIEW_PICTURE_ALT//
$strAlt = (isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != "" ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arResult["NAME"]);

//PREVIEW_PICTURE_TITLE//
$strTitle = (isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != "" ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arResult["NAME"]);

//JS//?>
<script type="text/javascript">
	BX.ready(function() {
		//OFFERS_LIST_PROPS//
		<?if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"] == "LIST") {
			foreach($arResult["OFFERS"] as $key_off => $arOffer) {?>
				props = BX.findChildren(BX("catalog-offer-item-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>"), {className: "catalog-item-prop"}, true);
				if(!!props && 0 < props.length) {
					for(i = 0; i < props.length; i++) {
						if(!BX.hasClass(props[i], "empty")) {
							BX("catalog-item-props-mob-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>").appendChild(BX.create(
								"DIV",
								{
									props: {
										className: "catalog-item-prop"
									},
									html: props[i].innerHTML
								}
							));
						}
					}
				}
			<?}
		}?>
		
		//QUANTITY//
		<?if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {?>
			var parentQntInput = BX("quantity_<?=$arItemIDs['ID']?>"),
				qntInput = BX("quantity_<?=$arItemIDs['ID']?>_<?=$arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID']?>");
			if(!!parentQntInput && !!qntInput)
				qntInput.value = parentQntInput.value;
		<?}?>
		var parentQntSelectInput = BX("quantity_<?=$arItemIDs['ID']?>"),
			qntSelectInput = BX("quantity_select_<?=$arItemIDs['ID']?>");
		if(!!parentQntSelectInput && !!qntSelectInput)
			qntSelectInput.value = parentQntSelectInput.value;

		//DISABLE_FORM_SUBMIT_ENTER//
		$(".add2basket_form").on("keyup keypress", function(e) {
			var keyCode = e.keyCode || e.which;
			if(keyCode === 13) {
				e.preventDefault();
				return false;
			}
		});

		//FANCYBOX//
		$(".fancybox").fancybox({
			"transitionIn": "elastic",
			"transitionOut": "elastic",
			"speedIn": 600,
			"speedOut": 200,
			"overlayShow": false,
			"cyclic" : true,
			"padding": 20,
			"titlePosition": "over",
			"onComplete": function() {
				$("#fancybox-title").css({"top":"100%", "bottom":"auto"});
			} 
		});
	});
</script>

<div id="<?=$strMainID?>_info" class="item_info">	
	<div class="item_image" id="<?=$arItemIDs['PICT']?>">
		<?//OFFERS_IMAGE//
		if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"] != "LIST") {
			foreach($arResult["OFFERS"] as $key_off => $arOffer) {
				$isOfferPreviewPicture = is_array($arOffer["PREVIEW_PICTURE"]);
				$offerName = isset($arOffer["NAME"]) && !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"];?>
				<div id="img_<?=$arItemIDs['ID']?>_<?=$arOffer['ID']?>" class="img<?=($key_off == $arResult['OFFERS_SELECTED'] ? '' : ' hidden');?>">
					<?if($isOfferPreviewPicture) {?>
						<img src="<?=$arOffer['PREVIEW_PICTURE']['SRC']?>" width="<?=$arOffer['PREVIEW_PICTURE']["WIDTH"]?>" height="<?=$arOffer['PREVIEW_PICTURE']["HEIGHT"]?>" alt="<?=$offerName?>" title="<?=$offerName?>" />
					<?} elseif($isPreviewPicture) {?>
						<img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
					<?} else {?>
						<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
					<?}?>
				</div>
			<?}
			unset($offerName, $isOfferPreviewPicture);
		//ITEM_IMAGE//
		} else {?>
			<div class="img">
				<?if($isPreviewPicture) {?>
					<img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
				<?} else {?>
					<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
				<?}?>
			</div>
		<?}
		//ITEM_NAME//?>
		<div class="item_name"><?=$arResult["NAME"]?></div>
	</div>
	<div class="item_block<?=(isset($arResult['OFFERS']) && !empty($arResult['OFFERS']) && $arSetting['OFFERS_VIEW'] == 'LIST' ? ' offers-list' : '');?>">
		<?//OFFERS_PROPS//
		if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"] != "LIST") {
			$arSkuProps = array();?>
			<table class="offer_block" id="<?=$arItemIDs['PROP_DIV'];?>">			
				<?foreach($arResult["SKU_PROPS"] as $arProp) {
					if(!isset($arResult["OFFERS_PROP"][$arProp["CODE"]]))
						continue;
					$arSkuProps[] = array(
						"ID" => $arProp["ID"],
						"SHOW_MODE" => $arProp["SHOW_MODE"]
					);?>	
					<tr class="<?=$arProp['CODE']?>" id="<?=$arItemIDs['PROP'].$arProp['ID'];?>_cont">					
						<td class="h3"><?=htmlspecialcharsex($arProp["NAME"]);?></td>
						<td class="props">
							<ul id="<?=$arItemIDs['PROP'].$arProp['ID'];?>_list" class="<?=$arProp['CODE']?><?=$arProp['SHOW_MODE'] == 'PICT' ? ' COLOR' : '';?>">
								<?foreach($arProp["VALUES"] as $arOneValue) {
									$arOneValue["NAME"] = htmlspecialcharsbx($arOneValue["NAME"]);?>
									<li data-treevalue="<?=$arProp['ID'].'_'.$arOneValue['ID'];?>" data-onevalue="<?=$arOneValue['ID'];?>" style="display:none;">
										<span title="<?=$arOneValue['NAME'];?>">
											<?if("TEXT" == $arProp["SHOW_MODE"]) {
												echo $arOneValue["NAME"];
											} elseif("PICT" == $arProp["SHOW_MODE"]) {
												if(is_array($arOneValue["PICT"])) {?>
													<img src="<?=$arOneValue['PICT']['SRC']?>" width="<?=$arOneValue['PICT']['WIDTH']?>" height="<?=$arOneValue['PICT']['HEIGHT']?>" alt="<?=$arOneValue['NAME']?>" title="<?=$arOneValue['NAME']?>" />
												<?} else {?>
													<i style="background:#<?=$arOneValue['HEX']?>"></i>
												<?}
											}?>
										</span>
									</li>
								<?}?>
							</ul>
							<div class="bx_slide_left" style="display:none;" id="<?=$arItemIDs['PROP'].$arProp['ID']?>_left" data-treevalue="<?=$arProp['ID']?>"></div>
							<div class="bx_slide_right" style="display:none;" id="<?=$arItemIDs['PROP'].$arProp['ID']?>_right" data-treevalue="<?=$arProp['ID']?>"></div>
						</td>
					</tr>
				<?}
				unset($arProp);?>
			</table>
		<?}
		//SELECT_PROPS//
		if(isset($arResult["SELECT_PROPS"]) && !empty($arResult["SELECT_PROPS"])) {
			$arSelProps = array();?>
			<table class="offer_block" id="<?=$arItemIDs['SELECT_PROP_DIV'];?>">
				<?foreach($arResult["SELECT_PROPS"] as $key => $arProp) {
					$arSelProps[] = array(
						"ID" => $arProp["ID"]
					);?>
					<tr class="<?=$arProp['CODE']?>" id="<?=$arItemIDs['SELECT_PROP'].$arProp['ID'];?>">
						<td class="h3"><?=htmlspecialcharsex($arProp["NAME"]);?></td>
						<td class="props">		
							<ul class="<?=$arProp['CODE']?>">
								<?$props = array();
								foreach($arProp["DISPLAY_VALUE"] as $arOneValue) {
									$props[$key] = array(
										"NAME" => $arProp["NAME"],
										"CODE" => $arProp["CODE"],
										"VALUE" => strip_tags($arOneValue)
									);
									$props[$key] = !empty($props[$key]) ? strtr(base64_encode(serialize($props[$key])), "+/=", "-_,") : "";?>
									<li data-select-onevalue="<?=$props[$key]?>">
										<span title="<?=$arOneValue;?>"><?=$arOneValue?></span>
									</li>
								<?}?>
							</ul>
							<div class="clr"></div>
						</td>
					</tr>
				<?}
				unset($arProp);?>
			</table>
		<?}
		//OFFERS_LIST//		
		if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"] == "LIST") {?>
			<div class="catalog-detail-offers-list">
				<div class="h3"><?=Loc::getMessage("CATALOG_ELEMENT_OFFERS_LIST")?></div>
				<div class="offers-items">
					<div class="thead">
						<div class="offers-items-image"><?=Loc::getMessage("CATALOG_ELEMENT_OFFERS_LIST_IMAGE")?></div>
						<div class="offers-items-name"><?=Loc::getMessage("CATALOG_ELEMENT_OFFERS_LIST_NAME")?></div>
						<?$i = 1;										
						foreach($arResult["SKU_PROPS"] as $arProp) {											
							if(!isset($arResult["OFFERS_PROP"][$arProp["CODE"]]))
								continue;
							if($i > 3)
								continue;?>						
							<div class="offers-items-prop"><?=htmlspecialcharsex($arProp["NAME"]);?></div>
							<?$i++;											
						}
						unset($arProp);?>
						<div class="offers-items-price"></div>
						<div class="offers-items-buy"><?=Loc::getMessage("CATALOG_ELEMENT_OFFERS_LIST_PRICE")?></div>
					</div>
					<div class="tbody">
						<?foreach($arResult["OFFERS"] as $keyOffer => $arOffer) {							
							$sticker = "";
							if($arOffer["MIN_PRICE"]["PERCENT"] > 0) {
								$sticker .= "<span class='discount'>-".$arOffer["MIN_PRICE"]["PERCENT"]."%</span>";	
							}
							$isOfferPreviewPicture = is_array($arOffer["PREVIEW_PICTURE"]);
							$offerName = isset($arOffer["NAME"]) && !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"];?>
							<div class="catalog-item" id="catalog-offer-item-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>" data-offer-num="<?=$keyOffer?>">
								<div class="catalog-item-info">							
									<?//OFFERS_LIST_IMAGE//?>
									<div class="catalog-item-image-cont">
										<div class="catalog-item-image">
											<?if($isOfferPreviewPicture || $isPreviewPicture) {?>
												<a rel="lightbox" class="fancybox" href="<?=($isOfferPreviewPicture ? $arOffer['DETAIL_PICTURE']['SRC'] : $arResult['DETAIL_PICTURE']['SRC']);?>">
											<?} else {?>
												<div>
											<?}
											if($isOfferPreviewPicture) {?>
												<img src="<?=$arOffer['PREVIEW_PICTURE']['SRC']?>" width="<?=$arOffer['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arOffer['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$offerName?>" title="<?=$offerName?>" />
											<?} elseif($isPreviewPicture) {?>
												<img src="<?=$arResult['PREVIEW_PICTURE']['SRC']?>" width="<?=$arResult['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arResult['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
											<?} else {?>
												<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="72" height="72" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
											<?}?>
											<div class="sticker">
												<?=$sticker?>
											</div>
											<?if($isOfferPreviewPicture || $isPreviewPicture) {?>
												<div class="zoom"><i class="fa fa-search-plus"></i></div>
											<?}?>
											<?=($isOfferPreviewPicture || $isPreviewPicture ? "</a>" : "</div>");?>
										</div>
									</div>
									<?//OFFERS_LIST_NAME_ARTNUMBER//?>
									<div class="catalog-item-title">
										<?//OFFERS_LIST_NAME//?>
										<span class="name"><?=$offerName?></span>
										<?//OFFERS_LIST_ARTNUMBER//?>
										<span class="article"><?=Loc::getMessage("CATALOG_ELEMENT_ARTNUMBER")?><?=!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-";?></span>
									</div>
									<?//OFFERS_LIST_PROPS//
									$i = 1;
									foreach($arResult["SKU_PROPS"] as $arProp) {									
										if(!isset($arResult["OFFERS_PROP"][$arProp["CODE"]]))
											continue;
										if($i > 3)
											continue;?>	
										<div class="catalog-item-prop<?=(!isset($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]]) || empty($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]]) ? ' empty' : '');?>">
											<?if(isset($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]]) && !empty($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]])) {
												$v = $arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]];
												if($arProp["SHOW_MODE"] == "TEXT") {
													echo strip_tags($v["DISPLAY_VALUE"]);
												} elseif($arProp["SHOW_MODE"] == "PICT") {?>
													<span class="prop_cont">
														<span class="prop" title="<?=$arProp['VALUES'][$v['VALUE']]['NAME']?>">
															<?if(is_array($arProp["VALUES"][$v["VALUE"]]["PICT"])) {?>
																<img src="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['SRC']?>" width="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['WIDTH']?>" height="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['HEIGHT']?>" alt="<?=$arProp['VALUES'][$v['VALUE']]['NAME']?>" title="<?=$arProp['VALUES'][$v['VALUE']]['NAME']?>" />
															<?} else {?>
																<i style="background:#<?=$arProp['VALUES'][$v['VALUE']]['HEX']?>"></i>
															<?}?>
														</span>
													</span>
												<?}
											}?>
										</div>
										<?$i++;
									}
									unset($arProp);
									//OFFERS_LIST_PRICE//?>
									<div class="item-price">
										<?$arCurFormat = CCurrencyLang::GetCurrencyFormat($arOffer["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
										if(empty($arCurFormat["THOUSANDS_SEP"]))
											$arCurFormat["THOUSANDS_SEP"] = " ";
										$arCurFormat["REFERENCE_DECIMALS"] = $arCurFormat["DECIMALS"];
										if($arCurFormat["HIDE_ZERO"] == "Y") {
											if($arSetting["REFERENCE_PRICE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]))
												if(round($arOffer["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"], $arCurFormat["DECIMALS"]) == round($arOffer["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"], 0))
													$arCurFormat["REFERENCE_DECIMALS"] = 0;
											if(round($arOffer["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"]) == round($arOffer["MIN_PRICE"]["RATIO_PRICE"], 0))
												$arCurFormat["DECIMALS"] = 0;
										}
										$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);

										if(!$arParams["IS_GIFT"] && $arOffer["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>							
											<span class="catalog-item-no-price">
												<span class="unit">
													<?=Loc::getMessage("CATALOG_ELEMENT_NO_PRICE")?>
													<br />
													<span><?=Loc::getMessage("CATALOG_ELEMENT_UNIT")." ".$arOffer["CATALOG_MEASURE_RATIO"]." ".$arOffer["CATALOG_MEASURE_NAME"];?></span>
												</span>
											</span>
										<?} elseif($arParams["IS_GIFT"] && $arOffer["MIN_PRICE"]["RATIO_BASE_PRICE"] <= 0) {?>
											<span class="catalog-item-no-price">
												<span class="unit">
													<?=Loc::getMessage("CATALOG_ELEMENT_NO_PRICE")?>
													<br />
													<span><?=Loc::getMessage("CATALOG_ELEMENT_UNIT")." ".$arOffer["ITEM_MEASURE_RATIOS"][$arOffer["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"]." ".$arOffer["ITEM_MEASURE"]["TITLE"];?></span>
												</span>
											</span>
										<?} else {?>
											<span class="catalog-item-price">
												<?if(!$arParams["IS_GIFT"] && count($arOffer["ITEM_QUANTITY_RANGES"]) > 1) {?>
													<span class="from"><?=GetMessage("CATALOG_ELEMENT_FROM")?></span>
												<?}
												echo number_format($arOffer["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);
												if(!$arParams["IS_GIFT"] && $arParams["USE_PRICE_COUNT"] && count($arOffer["ITEM_QUANTITY_RANGES"]) > 1) {?>
													<span class="catalog-item-price-ranges-wrap">
														<a class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
													</span>
												<?}
												if(!$arParams["IS_GIFT"] && count($arOffer["PRICE_MATRIX_SHOW"]["COLS"]) > 1 && count($arOffer["ITEM_QUANTITY_RANGES"]) <= 1) {?>
													<span class="catalog-item-price-ranges-wrap">
														<a class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
													</span>
												<?}?>
												<span class="unit">
													<?=$currency?>
													<span><?=Loc::getMessage("CATALOG_ELEMENT_UNIT")." ".(!$arParams["IS_GIFT"] ? $arOffer["CATALOG_MEASURE_RATIO"] : $arOffer["ITEM_MEASURE_RATIOS"][$arOffer["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"])." ".(!$arParams["IS_GIFT"] ? $arOffer["CATALOG_MEASURE_NAME"] : $arOffer["ITEM_MEASURE"]["TITLE"]);?></span>
												</span>
												<?if($arSetting["REFERENCE_PRICE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"])) {?>
													<span class="catalog-item-price-reference">
														<?=number_format($arOffer["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"], $arCurFormat["REFERENCE_DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
														<span><?=$currency?></span>
													</span>
												<?}?>
											</span>
											<?if($arOffer["MIN_PRICE"]["RATIO_PRICE"] < $arOffer["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
												<span class="catalog-item-price-old">
													<?=$arOffer["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
												</span>
												<span class="catalog-item-price-percent">
													<?=Loc::getMessage("CATALOG_ELEMENT_SKIDKA")?>
													<br />
													<?=$arOffer["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"]?>
												</span>
											<?}											
										}?>
									</div>
									<?//OFFERS_LIST_MOBILE_PROPS//
									if(!empty($arOffer["DISPLAY_PROPERTIES"])) {?>
										<div id="catalog-item-props-mob-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>" class="catalog-item-props-mob"></div>
									<?}
									//OFFERS_LIST_AVAILABILITY_BUY//?>
									<div class="buy_more<?=(!$inBtnBoc) ? " no-one-click" : ""?>">
										<?//OFFERS_LIST_AVAILABILITY//?>
										<div class="available">
											<?if($arOffer["CAN_BUY"]) {?>
												<?if($arParams['SHOW_MAX_QUANTITY'] !== 'N') { ?>                      
                                                    <div class="avl">
                                                        <i class="fa fa-check-circle"></i>
                                                        <span> 
                                                            <?=(!empty($arParams["MESS_SHOW_MAX_QUANTITY"]) ? $arParams["MESS_SHOW_MAX_QUANTITY"] : Loc::getMessage("CATALOG_ELEMENT_AVAILABLE")) . ' ';
                                                            if($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                                                if($arOffer["CHECK_QUANTITY"] && $inProductQnt) {
                                                                    if($arParams['RELATIVE_QUANTITY_FACTOR'] > $arOffer["CATALOG_QUANTITY"])
                                                                        echo Loc::getMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_MANY");
                                                                    else
                                                                        echo Loc::getMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_FEW");
                                                                }
                                                            } else {
                                                                if($arOffer["CHECK_QUANTITY"] && $inProductQnt)
                                                                    echo " " . $arOffer["CATALOG_QUANTITY"];
                                                            }?>
                                                        </span>
                                                    </div> 
                                                <?}?>                                 
											<?} elseif(!$arOffer["CAN_BUY"]) {?>
												<div class="not_avl">
													<i class="fa fa-times-circle"></i>
													<span><?=Loc::getMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
												</div>
											<?}?>
										</div>
										<div class="clr"></div>											
										<?//OFFERS_LIST_BUY//										
										if($arOffer["CAN_BUY"]) {
											if((!$arParams["IS_GIFT"] && $arOffer["MIN_PRICE"]["RATIO_PRICE"] <= 0) || ($arParams["IS_GIFT"] && $arOffer["MIN_PRICE"]["RATIO_BASE_PRICE"] <= 0)) {
												//OFFERS_LIST_ASK_PRICE//?>
												<form action="javascript:void(0)" class="apuo_form">										
													<input type="hidden" name="ACTION" value="ask_price" />
													<?$properties = array();
													foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
														if($propOffer["PROPERTY_TYPE"] != "S")
															$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
													}
													$properties = implode("; ", $properties);
													$elementName = !empty($properties) ? $offerName." (".$properties.")" : $offerName;?>
													<input type="hidden" name="NAME" value="<?=$elementName?>" />
													<button type="button" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo"><i class="fa fa-comment-o"></i><span class="short"><?=Loc::getMessage("CATALOG_ELEMENT_ASK_PRICE_SHORT")?></span></button>
												</form>
											<?} else {
												$props = array();
												if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {	
													$props[] = array(
														"NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
														"CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
														"VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
													);
												}
												foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
													if($propOffer["PROPERTY_TYPE"] != "S") {
														$props[] = array(
															"NAME" => $propOffer["NAME"],
															"CODE" => $propOffer["CODE"],
															"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
														);
													}
												}
												$props = !empty($props) ? strtr(base64_encode(serialize($props)), "+/=", "-_,") : "";?>
												<div class="add2basket_block">
													<?//OFFERS_LIST_DELAY//?>													
													<div class="delay">
														<a href="javascript:void(0)" id="catalog-item-delay-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arOffer["ID"]?>', 'quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-<?=$arItemIDs['ID']."-".$arOffer["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
													</div>
													<?//OFFERS_LIST_BUY_FORM//?>
													<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
														<div class="qnt_cont">
															<a href="javascript:void(0)" class="minus"><span>-</span></a>
															<input type="text" id="quantity_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" name="quantity" class="quantity" value="<?=$arOffer['MIN_PRICE']['MIN_QUANTITY']?>"/>
															<a href="javascript:void(0)" class="plus"><span>+</span></a>
														</div>
														<input type="hidden" name="ID" class="offer_id" value="<?=$arOffer['ID']?>" />
														<input type="hidden" name="PROPS" id="props_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" value="<?=$props?>" />
														<?if(!empty($arResult["SELECT_PROPS"])) {?>
															<input type="hidden" name="SELECT_PROPS" id="select_props_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" value="" />
														<?}?>
														<button type="button" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i></button>
													</form>
													<?//OFFERS_LIST_BUY_ONE_CLICK//?>
													<?if($inBtnBoc){?>
														<button id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy boc_anch" data-action="boc"><i class="fa fa-bolt"></i><span><?=Loc::getMessage("CATALOG_ELEMENT_BOC_SHORT")?></span></button>
													<?}?>	
												</div>
											<?}
										} elseif(!$arOffer["CAN_BUY"]) {
											//OFFERS_LIST_UNDER_ORDER//?>
											<form action="javascript:void(0)" class="apuo_form">										
												<input type="hidden" name="ACTION" value="under_order" />
												<?$properties = array();
												foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
													if($propOffer["PROPERTY_TYPE"] != "S")
														$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
												}
												$properties = implode("; ", $properties);
												$elementName = !empty($properties) ? $offerName." (".$properties.")" : $offerName;?>
												<input type="hidden" name="NAME" value="<?=$elementName?>" />
												<button type="button" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo"><i class="fa fa-clock-o"></i><span class="short"><?=Loc::getMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></button>
											</form>
										<?}?>										
									</div>										
								</div>
							</div>							
						<?}?>
					</div>
				</div>
			</div>
		<?//OFFERS_ITEM//
		} else {?>
			<div class="item_sale">
				<?//OFFERS_ITEM_PRICE//?>
				<div class="catalog_price" id="<?=$arItemIDs['PRICE'];?>">
					<?//OFFERS_PRICE//
					if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
						foreach($arResult["OFFERS"] as $key_off => $arOffer) {?>
							<div id="price_<?=$arItemIDs['ID']?>_<?=$arOffer['ID']?>" class="price<?=($key_off == $arResult['OFFERS_SELECTED'] ? '' : ' hidden');?>">
								<?if(!$arParams["IS_GIFT"] && $arOffer["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>			
									<span class="no-price">
										<?=Loc::getMessage("CATALOG_ELEMENT_NO_PRICE")." ".Loc::getMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arOffer["CATALOG_MEASURE_RATIO"] : "1")." ".$arOffer["CATALOG_MEASURE_NAME"];?>
									</span>
								<?} elseif($arParams["IS_GIFT"] && $arOffer["MIN_PRICE"]["RATIO_BASE_PRICE"] <= 0) {?>
									<span class="no-price">
										<?=Loc::getMessage("CATALOG_ELEMENT_NO_PRICE")." ".Loc::getMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arOffer["ITEM_MEASURE_RATIOS"][$arOffer["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"] : "1")." ".$arOffer["ITEM_MEASURE"]["TITLE"];?>
									</span>
								<?} else {
									if($arOffer["MIN_PRICE"]["RATIO_PRICE"] < $arOffer["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>				
										<span class="price-old">
											<?=$arOffer["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
										</span>
										<span class="price-percent">
											<?=Loc::getMessage("CATALOG_ELEMENT_SKIDKA")." ".$arOffer["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"];?>
										</span>
									<?}?>
									<span class="price-normal">
										<span class="price-current">
											<?=$arOffer["MIN_PRICE"]["PRINT_RATIO_PRICE"]?>
										</span>
										<span class="unit">
											<?=Loc::getMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? (!$arParams["IS_GIFT"] ? $arOffer["CATALOG_MEASURE_RATIO"] : $arOffer["ITEM_MEASURE_RATIOS"][$arOffer["ITEM_MEASURE_RATIO_SELECTED"]]["RATIO"]) : "1")." ".(!$arParams["IS_GIFT"] ? $arOffer["CATALOG_MEASURE_NAME"] : $arOffer["ITEM_MEASURE"]["TITLE"]);?>
										</span>
									</span>
									<?if($arSetting["REFERENCE_PRICE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"])) {?>
										<span class="price-reference">
											<?=CCurrencyLang::CurrencyFormat($arOffer["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"], $arOffer["MIN_PRICE"]["CURRENCY"], true);?>
										</span>
									<?}
								}
								//OFFERS_PRICE_RANGES//					
								if(!$arParams["IS_GIFT"] && $arParams["USE_PRICE_COUNT"] && count($arOffer["ITEM_QUANTITY_RANGES"]) > 1) {?>
									<div class="price-ranges">
										<?foreach($arOffer["ITEM_QUANTITY_RANGES"] as $range) {
											if($range["HASH"] !== "ZERO-INF") {
												$itemPrice = false;
												foreach($arOffer["ITEM_PRICES"] as $itemPrice) {
													if($itemPrice["QUANTITY_HASH"] === $range["HASH"]) {
														break;
													}
												}
												if($itemPrice) {?>
													<div class="price-ranges__row">
														<div class="price-ranges__sort">
															<?if(is_infinite($range["SORT_TO"])) {
																echo GetMessage("CATALOG_ELEMENT_FROM")." ".$range["SORT_FROM"];
															} else {
																echo $range["SORT_FROM"]." - ".$range["SORT_TO"];
															}?>
														</div>
														<div class="price-ranges__dots"></div>
														<div class="price-ranges__price"><?=$itemPrice["RATIO_PRICE"]?></div>
														<span class="unit">
															<?$arCurFormat = CCurrencyLang::GetCurrencyFormat($arOffer['MIN_PRICE']['CURRENCY'], LANGUAGE_ID);
															$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
															?>
															<?=$currency?>
														</span>
													</div>
												<?}
											}
										}?>
									</div>
									<?unset($itemPrice, $range);
								}
								//OTHER_PRICE//
								if(!$arParams["IS_GIFT"] && count($arOffer["PRICE_MATRIX_SHOW"]["COLS"]) > 1) {?>
									<div class="price-ranges">
										<?foreach($arOffer["PRICE_MATRIX_SHOW"]["COLS"] as $key_matrix => $item) {
											$priceMatrix[$key_matrix] = $arOffer["PRICE_MATRIX_SHOW"]["MATRIX"][$key_matrix];
											$oneRange = array_pop($priceMatrix[$key_matrix]);
											array_push($priceMatrix[$key_matrix], $oneRange);
											$countRange = count($arOffer["PRICE_MATRIX_SHOW"]["MATRIX"][$key_matrix]);?>
											<div class="price-ranges__row">
												<div class="price-ranges__sort">
													<?=$item["NAME_LANG"]?>
												</div>
												<div class="price-ranges__dots"></div>
												<?if($countRange > 1) {?>
													<span class="from"><?=GetMessage("CATALOG_ELEMENT_FROM");?></span>
												<?}?>	
												<div class="price-ranges__price"><?=$oneRange["DISCOUNT_PRICE"]?></div>
												<span class="unit"><?=$oneRange["PRINT_CURRENCY"]?></span>
												<?if($countRange > 1):?>
													<span class="price-ranges-wrap">
														<a id="<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>_<?=$key_matrix?>" data-key="<?=$key_matrix?>"  class="catalog-item-price-ranges" href="javascript:void(0);">
															<i class="fa fa-question-circle-o" ></i>
														</a>
													</span>
													<?$arResult["ID_PRICE_MATRIX_BTN"][$key][$key_matrix] = $arItemIDs['ID'].'_'.$arOffer['ID']."_".$key_matrix;
												endif;?>
											</div>
											<?unset($countRange);
										}?>
									</div>	
								<?}
								//OFFERS_AVAILABILITY//?>
								<div class="available">
									<?if($arOffer["CAN_BUY"]) {?>													
					 					<?if($arParams['SHOW_MAX_QUANTITY'] !== 'N') { ?>                       
                                            <div class="avl">
                                                <i class="fa fa-check-circle"></i>
                                                <span> 
                                                    <?=(!empty($arParams["MESS_SHOW_MAX_QUANTITY"]) ? $arParams["MESS_SHOW_MAX_QUANTITY"] : Loc::getMessage("CATALOG_ELEMENT_AVAILABLE")) . ' ';
                                                    if($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                                        if($arOffer["CHECK_QUANTITY"] && $inProductQnt) {
                                                            if($arParams['RELATIVE_QUANTITY_FACTOR'] > $arOffer["CATALOG_QUANTITY"])
                                                                echo Loc::getMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_MANY");
                                                            else
                                                                echo Loc::getMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_FEW");
                                                        }
                                                    } else {
                                                        if($arOffer["CHECK_QUANTITY"] && $inProductQnt)
                                                            echo " " . $arOffer["CATALOG_QUANTITY"];
                                                    }?>
                                                </span>
                                            </div>
                                       <?}?>
									<?} elseif(!$arOffer["CAN_BUY"]) {?>												
										<div class="not_avl">
											<i class="fa fa-times-circle"></i>
											<span><?=Loc::getMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
										</div>
									<?}?>
								</div>
							</div>
						<?}
					//ITEM_PRICE//
					} else {
						if($arResult["MIN_PRICE"]["RATIO_PRICE"] < $arResult["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
							<span class="price-old">
								<?=$arResult["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
							</span>
							<span class="price-percent">
								<?=Loc::getMessage("CATALOG_ELEMENT_SKIDKA")." ".$arResult["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"];?>
							</span>
						<?}?>
						<span class="price-normal">
							<span class="price-current">
								<?=$arResult["MIN_PRICE"]["PRINT_RATIO_PRICE"]?>
							</span>
							<span class="unit">
								<?=Loc::getMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arResult["CATALOG_MEASURE_RATIO"] : "1")." ".$arResult["CATALOG_MEASURE_NAME"];?>
							</span>
						</span>
						<?if($arSetting["REFERENCE_PRICE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"])) {?>
							<span class="price-reference">
								<?=CCurrencyLang::CurrencyFormat($arResult["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"], $arResult["MIN_PRICE"]["CURRENCY"], true);?>
							</span>
						<?}
						//ITEM_PRICE_RANGES//					
						if(!$arParams["IS_GIFT"] && $arParams["USE_PRICE_COUNT"] && count($arResult["ITEM_QUANTITY_RANGES"]) > 1) {?>
							<div class="price-ranges">
								<?foreach($arResult["ITEM_QUANTITY_RANGES"] as $range) {
									if($range["HASH"] !== "ZERO-INF") {
										$itemPrice = false;
										foreach($arResult["ITEM_PRICES"] as $itemPrice) {
											if($itemPrice["QUANTITY_HASH"] === $range["HASH"]) {
												break;
											}
										}
										if($itemPrice) {?>
											<div class="price-ranges__row">
												<div class="price-ranges__sort">
													<?if(is_infinite($range["SORT_TO"])) {
														echo GetMessage("CATALOG_ELEMENT_FROM")." ".$range["SORT_FROM"];
													} else {
														echo $range["SORT_FROM"]." - ".$range["SORT_TO"];
													}?>
												</div>
												<div class="price-ranges__dots"></div>
												<div class="price-ranges__price"><?=$itemPrice["RATIO_PRICE"]?></div>
												<span class="unit">
													<?$arCurFormat = CCurrencyLang::GetCurrencyFormat($arResult['MIN_PRICE']['CURRENCY'], LANGUAGE_ID);
													$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
													?>
													<?=$currency?>
												</span>
											</div>
										<?}
									}
								}?>
							</div>
							<?unset($itemPrice, $range);
						}
						//OTHER_PRICE//
						if(!$arParams["IS_GIFT"] && count($arResult["PRICE_MATRIX_SHOW"]["COLS"]) > 1) {?>
							<div class="price-ranges">
								<?foreach($arResult["PRICE_MATRIX_SHOW"]["COLS"] as $key => $item) {
									$priceMatrix[$key] = $arResult["PRICE_MATRIX_SHOW"]["MATRIX"][$key];
									$oneRange = array_pop($priceMatrix[$key]);
									array_push($priceMatrix[$key], $oneRange);
									$countRange = count($arResult["PRICE_MATRIX_SHOW"]["MATRIX"][$key]);?>
									<div class="price-ranges__row">
										<div class="price-ranges__sort">
											<?=$item["NAME_LANG"]?>
										</div>
										<div class="price-ranges__dots"></div>
										<?if($countRange > 1) {?>
											<span class="from"><?=GetMessage("CATALOG_ELEMENT_FROM");?></span>
										<?}?>	
										<div class="price-ranges__price"><?=$oneRange["DISCOUNT_PRICE"]?></div>
										<span class="unit"><?=$oneRange["PRINT_CURRENCY"]?></span>
										<?if($countRange > 1):?>
											<span class="price-ranges-wrap">
												<a id="<?=$arItemIDs['PRICE_MATRIX_BTN']?>_<?=$key?>" data-key="<?=$key?>"  class="catalog-item-price-ranges" href="javascript:void(0);">
													<i class="fa fa-question-circle-o" ></i>
												</a>
											</span>
											<?$arIdPriceMatrix[$key] = $arItemIDs['PRICE_MATRIX_BTN']."_".$key;
										endif;?>
									</div>
									<?unset($countRange);
								}
								?>
							</div>
						<?}
						//ITEM_AVAILABILITY//?>
						<div class="available">
							<?if($arResult["CAN_BUY"]) {?>												
								<?if($arParams['SHOW_MAX_QUANTITY'] !== 'N') { ?>                      
                                    <div class="avl">
                                        <i class="fa fa-check-circle"></i>
                                        <span> 
                                            <?=(!empty($arParams["MESS_SHOW_MAX_QUANTITY"]) ? $arParams["MESS_SHOW_MAX_QUANTITY"] : Loc::getMessage("CATALOG_ELEMENT_AVAILABLE")) . ' ';
                                            if($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                                if($arResult["CHECK_QUANTITY"] && $inProductQnt) {
                                                    if($arParams['RELATIVE_QUANTITY_FACTOR'] > $arResult["CATALOG_QUANTITY"])
                                                        echo Loc::getMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_MANY");
                                                    else
                                                        echo Loc::getMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_FEW");
                                                }
                                            } else {
                                                if($arResult["CHECK_QUANTITY"] && $inProductQnt)
                                                    echo " " . $arResult["CATALOG_QUANTITY"];
                                            }?>
                                        </span>
                                    </div>
                                <?}?>
							<?} elseif(!$arResult["CAN_BUY"]) {?>												
								<div class="not_avl">
									<i class="fa fa-times-circle"></i>
									<span><?=Loc::getMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
								</div>
							<?}?>
						</div>
					<?}?>
				</div>
				<?//OFFERS_ITEM_BUY//?>
				<div class="catalog_buy_more" id="<?=$arItemIDs['BUY'];?>">
					<?//OFFERS_BUY//
					if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
						foreach($arResult["OFFERS"] as $key_off => $arOffer) {?>
							<div id="buy_more_<?=$arItemIDs['ID']?>_<?=$arOffer['ID']?>" class="buy_more<?=($key_off == $arResult['OFFERS_SELECTED'] ? '' : ' hidden');?>">
								<?if($arOffer["CAN_BUY"]) {											
									if((!$arParams["IS_GIFT"] && $arOffer["MIN_PRICE"]["RATIO_PRICE"] <= 0) || ($arParams["IS_GIFT"] && $arOffer["MIN_PRICE"]["RATIO_BASE_PRICE"] <= 0)) {
										//OFFERS_ASK_PRICE//?>
										<form action="javascript:void(0)">										
											<input type="hidden" name="ACTION" value="ask_price" />
											<?$offerName = isset($arOffer["NAME"]) && !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"];
											$properties = array();
											foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
												if($propOffer["PROPERTY_TYPE"] != "S")
													$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
											}
											$properties = implode("; ", $properties);
											$elementName = !empty($properties) ? $offerName." (".$properties.")" : $offerName;?>
											<input type="hidden" name="NAME" value="<?=$elementName?>" />
											<button type="button" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo"><i class="fa fa-comment-o"></i><span><?=Loc::getMessage("CATALOG_ELEMENT_ASK_PRICE_FULL")?></span></button>
										</form>
									<?} else {?>											
										<div class="add2basket_block">
											<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
												<div class="qnt_cont">
													<a href="javascript:void(0)" class="minus"><span>-</span></a>
													<input type="text" id="quantity_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" name="quantity" class="quantity" value="<?=$arOffer['MIN_PRICE']['MIN_QUANTITY']?>"/>
													<a href="javascript:void(0)" class="plus"><span>+</span></a>
												</div>
												<input type="hidden" name="ID" class="offer_id" value="<?=$arOffer["ID"]?>" />
												<?$props = array();
												if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {		
													$props[] = array(
														"NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
														"CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
														"VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
													);
												}
												foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
													if($propOffer["PROPERTY_TYPE"] != "S") {
														$props[] = array(
															"NAME" => $propOffer["NAME"],
															"CODE" => $propOffer["CODE"],
															"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
														);
													}
												}
												$props = !empty($props) ? strtr(base64_encode(serialize($props)), "+/=", "-_,") : "";?>
												<input type="hidden" name="PROPS" id="props_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" value="<?=$props?>" />
												<?if(!empty($arResult["SELECT_PROPS"])) {?>
													<input type="hidden" name="SELECT_PROPS" id="select_props_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" value="" />
												<?}?>
												<button type="button" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=Loc::getMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
											</form>
										</div>
									<?}
								} elseif(!$arOffer["CAN_BUY"]) {
									//OFFERS_UNDER_ORDER//?>								
									<form action="javascript:void(0)">										
										<input type="hidden" name="ACTION" value="under_order" />
										<?$offerName = isset($arOffer["NAME"]) && !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"];
										$properties = array();
										foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
											if($propOffer["PROPERTY_TYPE"] != "S")
												$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
										}
										$properties = implode("; ", $properties);
										$elementName = !empty($properties) ? $offerName." (".$properties.")" : $offerName;?>
										<input type="hidden" name="NAME" value="<?=$elementName?>" />
										<button type="button" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo"><i class="fa fa-clock-o"></i><span><?=Loc::getMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></button>
									</form>
								<?}?>
							</div>
						<?}
					//ITEM_BUY//
					} else {?>
						<div class="buy_more">
							<div class="add2basket_block">
								<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
									<div class="qnt_cont">
										<a href="javascript:void(0)" class="minus" id="quantity_select_minus_<?=$arItemIDs['ID']?>"><span>-</span></a>
										<input type="text" id="quantity_select_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arResult['MIN_PRICE']['MIN_QUANTITY']?>"/>
										<a href="javascript:void(0)" class="plus" id="quantity_select_plus_<?=$arItemIDs['ID']?>"><span>+</span></a>
									</div>
									<input type="hidden" name="ID" class="id" value="<?=$arResult['ID']?>" />
									<?if(!empty($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
										$props = array();
										$props[] = array(
											"NAME" => $arResult["PROPERTIES"]["ARTNUMBER"]["NAME"],
											"CODE" => $arResult["PROPERTIES"]["ARTNUMBER"]["CODE"],
											"VALUE" => $arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]
										);												
										$props = strtr(base64_encode(serialize($props)), "+/=", "-_,");?>
										<input type="hidden" name="PROPS" id="props_<?=$arItemIDs['ID']?>" value="<?=$props?>" />
									<?}?>
									<input type="hidden" name="SELECT_PROPS" id="select_props_<?=$arItemIDs['ID']?>" value="" />
									<button type="button" id="<?=$arItemIDs['BTN_BUY']?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=Loc::getMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
								</form>
							</div>
						</div>
					<?}?>
				</div>
			</div>
		<?}?>		
	</div>
</div>

<?//JS//
if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
	$arJSParams = array(
		"CONFIG" => array(
			"USE_CATALOG" => $arResult["CATALOG"],
			"REFERENCE_PRICE_COEF" => $arSetting["REFERENCE_PRICE_COEF"]
		),
		"PRODUCT_TYPE" => $arResult["CATALOG_TYPE"],
		"VISUAL" => array(
			"ID" => $arItemIDs["ID"],
			"PICT_ID" => $arItemIDs["PICT"],
			"PRICE_ID" => $arItemIDs["PRICE"],
			"BUY_ID" => $arItemIDs["BUY"],
			"TREE_ID" => $arItemIDs["PROP_DIV"],
			"TREE_ITEM_ID" => $arItemIDs["PROP"],
			"POPUP_BTN_ID" => $arItemIDs["POPUP_BTN"]
		),
		"PRODUCT" => array(
			"ID" => $arResult["ID"],
			"NAME" => $arResult["NAME"],
			"PICT" => is_array($arResult["PREVIEW_PICTURE"]) ? $arResult["PREVIEW_PICTURE"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
			"PRINT_CURRENCY" => $currency
		),		
		"OFFERS_VIEW" => $arSetting["OFFERS_VIEW"],
		"OFFERS" => $arResult["JS_OFFERS"],
		"OFFER_SELECTED" => $arResult["OFFERS_SELECTED"],
		"TREE_PROPS" => $arSkuProps
	);
	if(!$arParams["IS_GIFT"])
		$arJSParams["VISUAL"]["PRICE_MATRIX_BTN_ID"] = is_array($arResult["ID_PRICE_MATRIX_BTN"]) ? $arResult["ID_PRICE_MATRIX_BTN"] : "";
} else {
	$arJSParams = array(
		"CONFIG" => array(
			"USE_CATALOG" => $arResult["CATALOG"],
			"REFERENCE_PRICE_COEF" => $arSetting["REFERENCE_PRICE_COEF"]
		),
		"PRODUCT_TYPE" => $arResult["CATALOG_TYPE"],
		"VISUAL" => array(
			"ID" => $arItemIDs["ID"],
			"BTN_BUY_ID" => $arItemIDs["BTN_BUY"]
		),
		"PRODUCT" => array(
			"ID" => $arResult["ID"],
			"NAME" => $arResult["NAME"],
			"PICT" => is_array($arResult["PREVIEW_PICTURE"]) ? $arResult["PREVIEW_PICTURE"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
			"ITEM_PRICE_MODE" => $arResult["ITEM_PRICE_MODE"],
			"ITEM_PRICES" => $arResult["ITEM_PRICES"],
			"ITEM_PRICE_SELECTED" => $arResult["ITEM_PRICE_SELECTED"],			
			"CHECK_QUANTITY" => $arResult["CHECK_QUANTITY"],
			"QUANTITY_FLOAT" => is_double($arResult["CATALOG_MEASURE_RATIO"]),
			"MAX_QUANTITY" => !$arParams["IS_GIFT"] ? $arResult["CATALOG_QUANTITY"] : $arResult["CATALOG_MEASURE_RATIO"],
			"STEP_QUANTITY" => $arResult["CATALOG_MEASURE_RATIO"],			
			"PRINT_CURRENCY" => $currency
		)
	);
	if(!$arParams["IS_GIFT"]) {
		$arJSParams["VISUAL"]["RICE_MATRIX_BTN_ID"] = $arIdPriceMatrix;
		$arJSParams["PRODUCT"]["ITEM_QUANTITY_RANGES"] = $arResult["ITEM_QUANTITY_RANGES"];
		$arJSParams["PRODUCT"]["ITEM_QUANTITY_RANGE_SELECTED"] = $arResult["ITEM_QUANTITY_RANGE_SELECTED"];
		$arJSParams["PRODUCT"]["PRICE_MATRIX"] = $arResult["PRICE_MATRIX_SHOW"]["MATRIX"];
	}
}				

if(isset($arResult["SELECT_PROPS"]) && !empty($arResult["SELECT_PROPS"])) {
	$arJSParams["VISUAL"]["SELECT_PROP_ID"] = $arItemIDs["SELECT_PROP_DIV"];
	$arJSParams["VISUAL"]["SELECT_PROP_ITEM_ID"] = $arItemIDs["SELECT_PROP"];
	$arJSParams["SELECT_PROPS"] = $arSelProps;
}

$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedParams = $signer->sign(base64_encode(serialize($arResult["ORIGINAL_PARAMETERS"])), "catalog.element");?>

<script type="text/javascript">
	BX.message({			
		PROPS_ELEMENT_SKIDKA: "<?=GetMessageJS('CATALOG_ELEMENT_SKIDKA')?>",
		PROPS_ELEMENT_FROM: "<?=GetMessageJS('CATALOG_ELEMENT_FROM')?>",
		PROPS_ADDITEMINCART_ADDED: "<?=GetMessageJS('CATALOG_ELEMENT_ADDED')?>",
		PROPS_POPUP_WINDOW_TITLE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_TITLE')?>",			
		PROPS_POPUP_WINDOW_BTN_CLOSE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_CLOSE')?>",
		PROPS_POPUP_WINDOW_BTN_ORDER: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_ORDER')?>",
		PROPS_SITE_DIR: "<?=SITE_DIR?>",
		PROPS_COMPONENT_TEMPLATE: "<?=$this->GetFolder();?>",
		PROPS_COMPONENT_PARAMS: "<?=CUtil::JSEscape($signedParams)?>"
	});	
	var <?=$strObName;?> = new JCCatalogElementProps(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
</script>