<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if( count( $arResult["ITEMS"] ) >= 1 ){
	$injectId = 'sale_gift_product_'.$this->randString();?>
	<ul class="viewed_navigation slider_navigation top_big custom_flex border"></ul>
	<div class="s_<?=$injectId;?> <?=($arParams["SHOW_UNABLE_SKU_PROPS"] != "N" ? "show_un_props" : "unshow_un_props");?>">
		<div class="all_wrapp s_<?=$injectId;?>">
			<div class="content_inner tab flexslider loading_state shadow border custom_flex top_right" data-plugin-options='{"animation": "slide", "animationSpeed": 600, "directionNav": true, "controlNav" :false, "animationLoop": true, "slideshow": false, "controlsContainer": ".viewed_navigation", "counts": [4,3,3,2,1]}'>
				<ul class="tabs_slider slides">
					<?
					$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
					$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
					$elementDeleteParams = array('CONFIRM' => GetMessage('CVP_TPL_ELEMENT_DELETE_CONFIRM'));
					?>
					<?foreach($arResult['ITEMS'] as $key => $arItem){
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $elementEdit);
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $elementDelete, $elementDeleteParams);
						$arItem["strMainID"] = $this->GetEditAreaId($arItem['ID']);
						$arItemIDs=CNext::GetItemsIDs($arItem);

						$strMeasure = '';
						$totalCount = CNext::GetTotalCount($arItem, $arParams);
						$arQuantityData = CNext::GetQuantityArray($totalCount, $arItemIDs["ALL_ITEM_IDS"], "N", $arItem["PRODUCT"]["TYPE"]);

						$elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);

						if(!$arItem["OFFERS"]){
							if($arParams["SHOW_MEASURE"] == "Y" && $arItem["CATALOG_MEASURE"]){
								$arMeasure = CCatalogMeasure::getList(array(), array("ID" => $arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
								$strMeasure = $arMeasure["SYMBOL_RUS"];
							}
							$arAddToBasketData = CNext::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
						}
						elseif($arItem["OFFERS"]){
							$strMeasure = $arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
							if($arParams['TYPE_SKU'] == 'TYPE_1' && $arItem['OFFERS_PROP'])
							{
								if($arParams["SET_SKU_TITLE"] === "Y"){
									$skuName = ((isset($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['NAME']);
									$arItem["NAME"] = $elementName = $skuName;
								}
							}

							if(!$arItem['OFFERS_PROP']){
								$arAddToBasketData = CNext::GetAddToBasketArray($arItem["OFFERS"][0], $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
							}
						}
						?>
						<li class="catalog_item visible" id="<?=$arItem["strMainID"];?>">
							<div class="inner_wrap">
								<div class="image_wrapper_block">
									<div class="stickers">
										<?$prop = ($arParams["STIKERS_PROP"] ? $arParams["STIKERS_PROP"] : "HIT");?>
										<?foreach(CNext::GetItemStickers($arItem["PROPERTIES"][$prop]) as $arSticker):?>
											<div><div class="<?=$arSticker['CLASS']?>"><?=$arSticker['VALUE']?></div></div>
										<?endforeach;?>
										<?if($arParams["SALE_STIKER"] && $arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"]){?>
											<div><div class="sticker_sale_text"><?=$arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"];?></div></div>
										<?}?>
									</div>
									<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y"):?>
										<div class="like_icons">
											<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
												<?if(!$arItem["OFFERS"]):?>
													<div class="wish_item_button" <?=($arAddToBasketData['CAN_BUY'] ? '' : 'style="display:none"');?>>
														<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
														<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added" style="display: none;" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
													</div>
												<?elseif($arItem["OFFERS"] && !empty($arItem['OFFERS_PROP'])):?>
													<div class="wish_item_button" style="display: none;">
														<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to <?=$arParams["TYPE_SKU"];?>" data-item="" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-offers="Y" data-props="<?=$arOfferProps?>"><i></i></span>
														<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-item="" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
													</div>
												<?endif;?>
											<?endif;?>
											<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
												<?if(!$arItem["OFFERS"] || ($arParams["TYPE_SKU"] !== 'TYPE_1' || ($arParams["TYPE_SKU"] == 'TYPE_1' && !$arItem["OFFERS_PROP"]))):?>
													<div class="compare_item_button">
														<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" ><i></i></span>
														<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added" style="display: none;" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
													</div>
												<?elseif($arItem["OFFERS"]):?>
													<div class="compare_item_button">
														<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to <?=$arParams["TYPE_SKU"];?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item="" ><i></i></span>
														<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-iblock="<?=$arItem["IBLOCK_ID"]?>" data-item=""><i></i></span>
													</div>
												<?endif;?>
											<?endif;?>
										</div>
									<?endif;?>
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb shine" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PICT']; ?>">
										<?
										if($arParams["SET_SKU_TITLE"] === "Y" && $arItem['OFFERS']){
											$a_alt = ($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"] && strlen($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $skuName ));
											$a_title = ($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"] && strlen($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $skuName ));
										}
										else{
											$a_alt = ($arItem["PREVIEW_PICTURE"] && strlen($arItem["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"] ));
											$a_title = ($arItem["PREVIEW_PICTURE"] && strlen($arItem["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"] ));
										}
										?>
										<?if( !empty($arItem["PREVIEW_PICTURE"]) ):?>
											<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
										<?elseif( !empty($arItem["DETAIL_PICTURE"])):?>
											<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => 170, "height" => 170 ), BX_RESIZE_IMAGE_PROPORTIONAL,true );?>
											<img src="<?=$img["src"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
										<?else:?>
											<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
										<?endif;?>
										<?if($fast_view_text_tmp = CNext::GetFrontParametrValue('EXPRESSION_FOR_FAST_VIEW'))
											$fast_view_text = $fast_view_text_tmp;
										else
											$fast_view_text = GetMessage('FAST_VIEW');?>
									</a>
									<div class="fast_view_block" data-event="jqm" data-param-form_id="fast_view" data-param-iblock_id="<?=$arItem["IBLOCK_ID"];?>" data-param-id="<?=$arItem["ID"];?>" data-param-item_href="<?=urlencode($arItem["DETAIL_PAGE_URL"]);?>" data-name="fast_view"><?=$fast_view_text;?></div>
								</div>
								<div class="item_info <?=$arParams["TYPE_SKU"]?>">
									<div class="item-title">
										<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="dark_link"><span><?=$elementName;?></span></a>
									</div>
									<?if($arParams["SHOW_RATING"] == "Y"):?>
										<div class="rating">
											<?$APPLICATION->IncludeComponent(
											   "bitrix:iblock.vote",
											   "element_rating_front",
											   Array(
												  "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
												  "IBLOCK_ID" => $arItem["IBLOCK_ID"],
												  "ELEMENT_ID" =>$arItem["ID"],
												  "MAX_VOTE" => 5,
												  "VOTE_NAMES" => array(),
												  "CACHE_TYPE" => $arParams["CACHE_TYPE"],
												  "CACHE_TIME" => $arParams["CACHE_TIME"],
												  "DISPLAY_AS_RATING" => 'vote_avg'
											   ),
											   $component, array("HIDE_ICONS" =>"Y")
											);?>
										</div>
									<?endif;?>
									<div class="sa_block">
										<?=$arQuantityData["HTML"];?>
									</div>
									<div class="cost prices clearfix">
										<?if( $arItem["OFFERS"]){?>
											<?\Aspro\Functions\CAsproSku::showItemPrices($arParams, $arItem, $item_id, $min_price_id, $arItemIDs);?>
										<?}elseif ( $arItem["PRICES"] ){?>
											<?$item_id = $arItem["ID"];?>
											<?foreach($arItem["PRICES"] as $priceCode => $arTmpPrice)
											{
												$arItem["PRICES"][$priceCode]["DISCOUNT_VALUE"] = $arItem["PRICES"][$priceCode]["DISCOUNT_DIFF"];
												$arItem["PRICES"][$priceCode]["PRINT_DISCOUNT_VALUE"] = $arItem["PRICES"][$priceCode]["PRINT_DISCOUNT_DIFF"];
											}?>
											<?\Aspro\Functions\CAsproItem::showItemPrices($arParams, $arItem["PRICES"], $strMeasure, $min_price_id);?>
										<?}?>
									</div>
									<?if($arParams["SHOW_DISCOUNT_TIME"]=="Y"){?>
										<?$arUserGroups = $USER->GetUserGroupArray();?>
										<?if($arParams['SHOW_DISCOUNT_TIME_EACH_SKU'] != 'Y' || ($arParams['SHOW_DISCOUNT_TIME_EACH_SKU'] == 'Y' && !$arItem['OFFERS'])):?>
											<?$arDiscounts = CCatalogDiscount::GetDiscountByProduct($item_id, $arUserGroups, "N", $min_price_id, SITE_ID);
											$arDiscount=array();
											if($arDiscounts)
												$arDiscount=current($arDiscounts);
											if($arDiscount["ACTIVE_TO"]){?>
												<div class="view_sale_block <?=($arQuantityData["HTML"] ? '' : 'wq');?>">
													<div class="count_d_block">
														<span class="active_to hidden"><?=$arDiscount["ACTIVE_TO"];?></span>
														<div class="title"><?=GetMessage("UNTIL_AKC");?></div>
														<span class="countdown values"><span class="item"></span><span class="item"></span><span class="item"></span><span class="item"></span></span>
													</div>
													<?if($arQuantityData["HTML"]):?>
														<div class="quantity_block">
															<div class="title"><?=GetMessage("TITLE_QUANTITY_BLOCK");?></div>
															<div class="values">
																<span class="item">
																	<span class="value" <?=((count( $arItem["OFFERS"] ) > 0 && $arParams["TYPE_SKU"] == 'TYPE_1'  && $arItem["OFFERS_PROP"]) ? 'style="opacity:0;"' : '')?>><?=$totalCount;?></span>
																	<span class="text"><?=GetMessage("TITLE_QUANTITY");?></span>
																</span>
															</div>
														</div>
													<?endif;?>
												</div>
											<?}?>
										<?else:?>
											<?if($arItem['JS_OFFERS'])
											{
												foreach($arItem['JS_OFFERS'] as $keyOffer => $arTmpOffer2)
												{
													$active_to = '';
													$arDiscounts = CCatalogDiscount::GetDiscountByProduct( $arTmpOffer2['ID'], $arUserGroups, "N", array(), SITE_ID );
													if($arDiscounts)
													{
														foreach($arDiscounts as $arDiscountOffer)
														{
															if($arDiscountOffer['ACTIVE_TO'])
															{
																$active_to = $arDiscountOffer['ACTIVE_TO'];
																break;
															}
														}
													}
													$arItem['JS_OFFERS'][$keyOffer]['DISCOUNT_ACTIVE'] = $active_to;
												}
											}?>
											<div class="view_sale_block" style="display:none;">
												<div class="count_d_block">
														<span class="active_to_<?=$arItem["ID"]?> hidden"><?=$arDiscount["ACTIVE_TO"];?></span>
														<div class="title"><?=GetMessage("UNTIL_AKC");?></div>
														<span class="countdown countdown_<?=$arItem["ID"]?> values"></span>
												</div>
												<?if($arQuantityData["HTML"]):?>
													<div class="quantity_block">
														<div class="title"><?=GetMessage("TITLE_QUANTITY_BLOCK");?></div>
														<div class="values">
															<span class="item">
																<span class="value"><?=$totalCount;?></span>
																<span class="text"><?=GetMessage("TITLE_QUANTITY");?></span>
															</span>
														</div>
													</div>
												<?endif;?>
											</div>
										<?endif;?>
									<?}?>
								</div>
								<div class="footer_button">
									<?if($arItem["OFFERS"]){?>
											<?if(!empty($arItem['OFFERS_PROP'])){?>
												<div class="sku_props">
													<div class="bx_catalog_item_scu wrapper_sku" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PROP_DIV']; ?>">
														<?$arSkuTemplate = array();?>
														<?$arSkuTemplate=CNext::GetSKUPropsArray($arItem['OFFERS_PROPS_JS'], $arResult["SKU_IBLOCK_ID"], $arParams["DISPLAY_TYPE"], $arParams["OFFER_HIDE_NAME_PROPS"], "N", $arItem, $arParams['OFFER_SHOW_PREVIEW_PICTURE_PROPS']);?>
														<?foreach ($arSkuTemplate as $code => $strTemplate){
															if (!isset($arItem['OFFERS_PROP'][$code]))
																continue;
															echo '<div>', str_replace('#ITEM#_prop_', $arItemIDs["ALL_ITEM_IDS"]['PROP'], $strTemplate), '</div>';
														}?>
													</div>
													<?$arItemJSParams=CNext::GetSKUJSParams($arResult, $arParams, $arItem);?>

													<script type="text/javascript">
														var <? echo $arItemIDs["strObName"]; ?> = new JCCatalogSection(<? echo CUtil::PhpToJSObject($arItemJSParams, false, true); ?>);
													</script>
												</div>
											<?}?>
										<?}?>
									<?if(!$arItem["OFFERS"] || ($arItem["OFFERS"] && !$arItem['OFFERS_PROP'])):?>
										<div class="counter_wrapp <?=($arItem["OFFERS"] && $arParams["TYPE_SKU"] == "TYPE_1" ? 'woffers' : '')?>">
											<?if(($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && $arAddToBasketData["ACTION"] == "ADD") && $arAddToBasketData["CAN_BUY"]):?>
												<div class="counter_block" data-offers="<?=($arItem["OFFERS"] ? "Y" : "N");?>" data-item="<?=$arItem["ID"];?>">
													<span class="minus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_DOWN']; ?>">-</span>
													<input type="text" class="text" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY']; ?>" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<?=$arAddToBasketData["MIN_QUANTITY_BUY"]?>" />
													<span class="plus" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_UP']; ?>" <?=($arAddToBasketData["MAX_QUANTITY_BUY"] ? "data-max='".$arAddToBasketData["MAX_QUANTITY_BUY"]."'" : "")?>>+</span>
												</div>
											<?endif;?>
											<div id="<?=$arItemIDs["ALL_ITEM_IDS"]['BASKET_ACTIONS']; ?>" class="button_block <?=(($arAddToBasketData["ACTION"] == "ORDER"/*&& !$arItem["CAN_BUY"]*/)  || !$arAddToBasketData["CAN_BUY"] || !$arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] || $arAddToBasketData["ACTION"] == "SUBSCRIBE" ? "wide" : "");?>">
												<!--noindex-->
													<?=$arAddToBasketData["HTML"]?>
												<!--/noindex-->
											</div>
										</div>
									<?elseif($arItem["OFFERS"]):?>
										<?if(empty($arItem['OFFERS_PROP'])){?>
											<div class="offer_buy_block buys_wrapp woffers">
												<?
												$arItem["OFFERS_MORE"] = "Y";
												$arAddToBasketData = CNext::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small read_more1', $arParams);?>
												<!--noindex-->
													<?=$arAddToBasketData["HTML"]?>
												<!--/noindex-->
											</div>
										<?}else{?>
											<div class="offer_buy_block buys_wrapp woffers" style="display:none;">
												<div class="counter_wrapp"></div>
											</div>
										<?}?>
									<?endif;?>
								</div>
							</div>
						</li>
					<?}?>
				</ul>
			</div>
		</div>
	</div>
<?}else{?>
	<script>
		$(document).ready(function(){
			if($('.bx_item_list_you_looked_horizontal').length){
				$('.bx_item_list_you_looked_horizontal').remove();
			}
		})
	</script>
<?}?>

<script>
	BX.message({
		QUANTITY_AVAILIABLE: '<? echo COption::GetOptionString("aspro.next", "EXPRESSION_FOR_EXISTS", GetMessage("EXPRESSION_FOR_EXISTS_DEFAULT"), SITE_ID); ?>',
		QUANTITY_NOT_AVAILIABLE: '<? echo COption::GetOptionString("aspro.next", "EXPRESSION_FOR_NOTEXISTS", GetMessage("EXPRESSION_FOR_NOTEXISTS"), SITE_ID); ?>',
		ADD_ERROR_BASKET: '<? echo GetMessage("ADD_ERROR_BASKET"); ?>',
		ADD_ERROR_COMPARE: '<? echo GetMessage("ADD_ERROR_COMPARE"); ?>',
	})
</script>