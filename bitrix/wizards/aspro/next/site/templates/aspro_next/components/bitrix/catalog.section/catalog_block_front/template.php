<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if( count( $arResult["ITEMS"] ) >= 1 ){?>

	<?
	$currencyList = '';
	if (!empty($arResult['CURRENCIES'])){
		$templateLibrary[] = 'currency';
		$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
	}
	$templateData = array(
		'TEMPLATE_LIBRARY' => $templateLibrary,
		'CURRENCIES' => $currencyList
	);
	unset($currencyList, $templateLibrary);
	?>

	<div class="top_wrapper items_wrapper">
		<div class="fast_view_params" data-params="<?=urlencode(serialize($arTransferParams));?>"></div>
		<div class="catalog_block items row margin0 ajax_load block">

		<?
		$arOfferProps = implode(';', $arParams['OFFERS_CART_PROPERTIES']);

		// params for catalog elements compact view
		$arParamsCE_CMP = $arParams;
		$arParamsCE_CMP['TYPE_SKU'] = 'N';
		?>

		<?foreach($arResult["ITEMS"] as $arItem){?>
			<?$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

			$totalCount = CNext::GetTotalCount($arItem, $arParams);
			$arQuantityData = CNext::GetQuantityArray($totalCount, array(), "N", $arItem["PRODUCT"]["TYPE"]);

			$item_id = $arItem["ID"];
			$strMeasure = '';

			$arItem["strMainID"] = $this->GetEditAreaId($arItem['ID'])."_".$arParams["FILTER_HIT_PROP"];
			$arItemIDs=CNext::GetItemsIDs($arItem);

			if($arParams["SHOW_MEASURE"] == "Y" && $arItem["CATALOG_MEASURE"]){
				if(isset($arItem["ITEM_MEASURE"]) && (is_array($arItem["ITEM_MEASURE"]) && $arItem["ITEM_MEASURE"]["TITLE"]))
				{
					$strMeasure = $arItem["ITEM_MEASURE"]["TITLE"];
				}
				else
				{
					$arMeasure = CCatalogMeasure::getList(array(), array("ID" => $arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
					$strMeasure = $arMeasure["SYMBOL_RUS"];
				}
			}
			$bUseSkuProps = ($arItem["OFFERS"] && !empty($arItem['OFFERS_PROP']));

			$elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);

			if($bUseSkuProps)
			{
				if(!$arItem["OFFERS"])
				{
					$arAddToBasketData = CNext::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false,  $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
				}
				elseif($arItem["OFFERS"])
				{

					$strMeasure = $arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
					$totalCount = CNext::GetTotalCount($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]], $arParams);
					$arQuantityData = CNext::GetQuantityArray($totalCount, $arItemIDs["ALL_ITEM_IDS"], "N", $arItem["PRODUCT"]["TYPE"]);

					$currentSKUIBlock = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["IBLOCK_ID"];
					$currentSKUID = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["ID"];

					$arItem["DETAIL_PAGE_URL"] = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DETAIL_PAGE_URL"];
					if($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"])
						$arItem["PREVIEW_PICTURE"] = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"];
					if($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PREVIEW_PICTURE"])
						$arItem["DETAIL_PICTURE"] = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DETAIL_PICTURE"];

					if($arParams["SET_SKU_TITLE"] === "Y"){
						$skuName = ((isset($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['NAME']);
						$arItem["NAME"] = $elementName = $skuName;
					}

					$item_id = $currentSKUID;

					// ARTICLE
					if($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DISPLAY_PROPERTIES"]["ARTICLE"]["VALUE"])
					{
						$arItem["ARTICLE"]["NAME"] = $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DISPLAY_PROPERTIES"]["ARTICLE"]["NAME"];
						$arItem["ARTICLE"]["VALUE"] = (is_array($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DISPLAY_PROPERTIES"]["ARTICLE"]["VALUE"]) ? reset($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DISPLAY_PROPERTIES"]["ARTICLE"]["VALUE"]) : $arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["DISPLAY_PROPERTIES"]["ARTICLE"]["VALUE"]);
					}

					$arCurrentSKU = $arItem["JS_OFFERS"][$arItem["OFFERS_SELECTED"]];
					$strMeasure = $arCurrentSKU["MEASURE"];
				}
			}
			else
			{
				$arAddToBasketData = CNext::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, array(), 'small', $arParams);
			}
			switch ($arParams["LINE_ELEMENT_COUNT"]){
				case '2':
					$col=6;
					break;
				case '4':
					$col=3;
					break;
				default:
					$col=4;
					break;
			}
			?>

			<div class="catalog_item_wrapp col-m-20 col-lg-<?=$col;?> col-md-4 col-sm-<?=floor(12 / round($arParams['LINE_ELEMENT_COUNT'] / 2))?> item item_block" data-col="<?=$col;?>">

				<div class="basket_props_block" id="bx_basket_div_<?=$arItem["ID"];?>_<?=$arParams["FILTER_HIT_PROP"]?>" style="display: none;">
					<?if (!empty($arItem['PRODUCT_PROPERTIES_FILL'])){
						foreach ($arItem['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo){?>
							<input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
							<?if (isset($arItem['PRODUCT_PROPERTIES'][$propID]))
								unset($arItem['PRODUCT_PROPERTIES'][$propID]);
						}
					}
					$arItem["EMPTY_PROPS_JS"]="Y";
					$emptyProductProperties = empty($arItem['PRODUCT_PROPERTIES']);
					if (!$emptyProductProperties){
						$arItem["EMPTY_PROPS_JS"]="N";?>
						<div class="wrapper">
							<table>
								<?foreach ($arItem['PRODUCT_PROPERTIES'] as $propID => $propInfo){?>
									<tr>
										<td><? echo $arItem['PROPERTIES'][$propID]['NAME']; ?></td>
										<td>
											<?if('L' == $arItem['PROPERTIES'][$propID]['PROPERTY_TYPE']	&& 'C' == $arItem['PROPERTIES'][$propID]['LIST_TYPE']){
												foreach($propInfo['VALUES'] as $valueID => $value){?>
													<label>
														<input type="radio" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>><? echo $value; ?>
													</label>
												<?}
											}else{?>
												<select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"><?
													foreach($propInfo['VALUES'] as $valueID => $value){?>
														<option value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>><? echo $value; ?></option>
													<?}?>
												</select>
											<?}?>
										</td>
									</tr>
								<?}?>
							</table>
						</div>
						<?
					}?>
				</div>

				<div class="catalog_item item_wrap main_item_wrapper" id="<?=$this->GetEditAreaId($arItem['ID']);?>_<?=$arParams["FILTER_HIT_PROP"]?>">
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
									<?if($arParams["DISPLAY_WISH_BUTTONS"] == "Y"):?>
										<?if(!$arItem["OFFERS"]):?>
											<div class="wish_item_button" <?=(CNext::checkShowDelay($arParams, $totalCount, $arItem) ? '' : 'style="display:none"');?>>
												<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
												<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added" style="display: none;" data-item="<?=$arItem["ID"]?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
											</div>
										<?elseif($bUseSkuProps):?>
											<div class="wish_item_button ce_cmp_hidden">
												<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to <?=$arParams["TYPE_SKU"];?>" data-item="<?=$currentSKUID;?>" data-iblock="<?=$currentSKUIBlock?>" data-offers="Y" data-props="<?=$arOfferProps?>"><i></i></span>
												<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-item="<?=$currentSKUID;?>" data-iblock="<?=$currentSKUIBlock?>"><i></i></span>
											</div>
										<?endif;?>
									<?endif;?>
									<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
										<?if(!$bUseSkuProps):?>
											<div class="compare_item_button">
												<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" ><i></i></span>
												<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
											</div>
										<?elseif($arItem["OFFERS"]):?>
											<div class="compare_item_button ce_cmp_hidden">
												<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to <?=$arParams["TYPE_SKU"];?>" data-item="<?=$currentSKUID;?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>" ><i></i></span>
												<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-item="<?=$currentSKUID;?>" data-iblock="<?=$arItem["IBLOCK_ID"]?>"><i></i></span>
											</div>
											<div class="compare_item_button ce_cmp_visible">
												<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" ><i></i></span>
												<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
											</div>
										<?endif;?>
									<?endif;?>
								</div>
							<?endif;?>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb shine">
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
									<img class="noborder" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
								<?elseif( !empty($arItem["DETAIL_PICTURE"])):?>
									<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array( "width" => 170, "height" => 170 ), BX_RESIZE_IMAGE_PROPORTIONAL,true );?>
									<img class="noborder" src="<?=$img["src"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
								<?else:?>
									<img class="noborder" src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
								<?endif;?>
								<?if($fast_view_text_tmp = CNext::GetFrontParametrValue('EXPRESSION_FOR_FAST_VIEW'))
									$fast_view_text = $fast_view_text_tmp;
								else
									$fast_view_text = GetMessage('FAST_VIEW');?>
							</a>
							<div class="fast_view_block" data-event="jqm" data-param-form_id="fast_view" data-param-iblock_id="<?=$arParams["IBLOCK_ID"];?>" data-param-id="<?=$arItem["ID"];?>" data-param-item_href="<?=urlencode($arItem["DETAIL_PAGE_URL"]);?>" data-name="fast_view"><?=$fast_view_text;?></div>
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
								<div class="article_block" <?if(isset($arItem['ARTICLE']) && $arItem['ARTICLE']['VALUE']):?>data-name="<?=$arItem['ARTICLE']['NAME'];?>" data-value="<?=$arItem['ARTICLE']['VALUE'];?>"<?endif;?>>
									<?if(isset($arItem['ARTICLE']) && $arItem['ARTICLE']['VALUE']){?>
										<div ><?=$arItem['ARTICLE']['NAME'];?>: <?=$arItem['ARTICLE']['VALUE'];?></div>
									<?}?>
								</div>
							</div>
							<div class="cost prices clearfix">
								<?if( $arItem["OFFERS"]){?>
									<div class="with_matrix <?=($arParams["SHOW_OLD_PRICE"]=="Y" ? 'with_old' : '');?>" style="display:none;">
										<div class="price price_value_block"><span class="values_wrapper"></span></div>
										<?if($arParams["SHOW_OLD_PRICE"]=="Y"):?>
											<div class="price discount"></div>
										<?endif;?>
										<?if($arParams["SHOW_DISCOUNT_PERCENT"]=="Y"){?>
											<div class="sale_block matrix" style="display:none;">
												<div class="sale_wrapper">
													<?if($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] != "Y"):?>
														<div class="text">
															<span class="title"><?=GetMessage("CATALOG_ECONOMY");?></span>
															<span class="values_wrapper"></span>
														</div>
													<?else:?>
														<div class="value">-<span></span>%</div>
														<div class="text">
															<span class="title"><?=GetMessage("CATALOG_ECONOMY");?></span>
															<span class="values_wrapper"></span>
														</div>
													<?endif;?>
													<div class="clearfix"></div>
												</div>
											</div>
										<?}?>
									</div>
									<?if($arCurrentSKU):?>
										<div class="ce_cmp_visible">
											<?\Aspro\Functions\CAsproSku::showItemPrices($arParamsCE_CMP, $arItem, $item_id, $min_price_id, $arItemIDs, ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] == "Y" ? "N" : "Y"));?>
										</div>
									<?endif;?>
									<div class="js_price_wrapper price">
										<?if($arCurrentSKU){?>
											<?
											$item_id = $arCurrentSKU["ID"];
											$arCurrentSKU['PRICE_MATRIX'] = $arCurrentSKU['PRICE_MATRIX_RAW'];
											$arCurrentSKU['CATALOG_MEASURE_NAME'] = $arCurrentSKU['MEASURE'];
											if(isset($arCurrentSKU['PRICE_MATRIX']) && $arCurrentSKU['PRICE_MATRIX']) // USE_PRICE_COUNT
											{?>
												<?if($arCurrentSKU['ITEM_PRICE_MODE'] == 'Q' && count($arCurrentSKU['PRICE_MATRIX']['ROWS']) > 1):?>
													<?=CNext::showPriceRangeTop($arCurrentSKU, $arParams, GetMessage("CATALOG_ECONOMY"));?>
												<?endif;?>
												<?=CNext::showPriceMatrix($arCurrentSKU, $arParams, $strMeasure, $arAddToBasketData);?>
												<?$arMatrixKey = array_keys($arCurrentSKU['PRICE_MATRIX']['MATRIX']);
												$min_price_id=current($arMatrixKey);?>
											<?
											}
											else
											{
												$arCountPricesCanAccess = 0;
												$min_price_id=0;?>
												<?\Aspro\Functions\CAsproItem::showItemPrices($arParams, $arCurrentSKU["PRICES"], $strMeasure, $min_price_id, ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] == "Y" ? "N" : "Y"));?>
											<?}?>
										<?}else{?>
												<?\Aspro\Functions\CAsproSku::showItemPrices($arParams, $arItem, $item_id, $min_price_id, array(), ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] == "Y" ? "N" : "Y"));?>
										<?}?>
									</div>
								<?}else{?>
									<?
									$item_id = $arItem["ID"];
									if(isset($arItem['PRICE_MATRIX']) && $arItem['PRICE_MATRIX']) // USE_PRICE_COUNT
									{?>
										<?if($arItem['ITEM_PRICE_MODE'] == 'Q' && count($arItem['PRICE_MATRIX']['ROWS']) > 1):?>
											<?=CNext::showPriceRangeTop($arItem, $arParams, GetMessage("CATALOG_ECONOMY"));?>
										<?endif;?>
										<?=CNext::showPriceMatrix($arItem, $arParams, $strMeasure, $arAddToBasketData);?>
										<?$arMatrixKey = array_keys($arItem['PRICE_MATRIX']['MATRIX']);
										$min_price_id=current($arMatrixKey);?>
									<?
									}
									elseif($arItem["PRICES"])
									{
										$arCountPricesCanAccess = 0;
										$min_price_id=0;?>
										<?\Aspro\Functions\CAsproItem::showItemPrices($arParams, $arItem["PRICES"], $strMeasure, $min_price_id, ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] == "Y" ? "N" : "Y"));?>
									<?}?>
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
															<span class="value"><?=$totalCount;?></span>
															<span class="text"><?=GetMessage("TITLE_QUANTITY");?></span>
														</span>
													</div>
												</div>
											<?endif;?>
										</div>
									<?}?>
								<?else:?>
									<?$arDiscounts = CCatalogDiscount::GetDiscountByProduct($item_id, $arUserGroups, "N", array(), SITE_ID);
									$arDiscount=array();
									if($arDiscounts)
										$arDiscount=current($arDiscounts);
									?>
									<div class="view_sale_block <?=($arQuantityData["HTML"] ? '' : 'wq');?>" <?=($arDiscount["ACTIVE_TO"] ? '' : 'style="display:none;"');?> >
										<div class="count_d_block">
											<span class="active_to hidden"><?=($arDiscount["ACTIVE_TO"] ? $arDiscount["ACTIVE_TO"] : "");?></span>
											<div class="title"><?=GetMessage("UNTIL_AKC");?></div>
											<span class="countdown values"><span class="item"></span><span class="item"></span><span class="item"></span><span class="item"></span></span>
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
						<div class="footer_button <?=($arItem["OFFERS"] && $arItem['OFFERS_PROP'] ? 'has_offer_prop' : '');?> inner_content js_offers__<?=$arItem['ID'];?>_<?=$arParams["FILTER_HIT_PROP"]?>">
							<div class="sku_props">
								<?if($arItem["OFFERS"]){?>
									<?if(!empty($arItem['OFFERS_PROP'])){?>
										<div class="bx_catalog_item_scu wrapper_sku" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PROP_DIV']; ?>" data-site_id="<?=SITE_ID;?>" data-id="<?=$arItem["ID"];?>" data-offer_id="<?=$arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["ID"];?>" data-propertyid="<?=$arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["PROPERTIES"]["CML2_LINK"]["ID"];?>" data-offer_iblockid="<?=$arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["IBLOCK_ID"];?>">
											<?$arSkuTemplate = array();?>
											<?$arSkuTemplate=CNext::GetSKUPropsArray($arItem['OFFERS_PROPS_JS'], $arResult["SKU_IBLOCK_ID"], $arParams["DISPLAY_TYPE"], $arParams["OFFER_HIDE_NAME_PROPS"], "N", $arItem, $arParams['OFFER_SHOW_PREVIEW_PICTURE_PROPS']);?>
											<?foreach ($arSkuTemplate as $code => $strTemplate){
												if (!isset($arItem['OFFERS_PROP'][$code]))
													continue;
												echo '<div class="item_wrapper">', str_replace('#ITEM#_prop_', $arItemIDs["ALL_ITEM_IDS"]['PROP'], $strTemplate), '</div>';
											}?>
										</div>
										<?$arItemJSParams=CNext::GetSKUJSParams($arResult, $arParams, $arItem);?>
									<?}?>
								<?}?>
							</div>
							<?/*
							<div class="counter_wrapp">
								<div class="button_block">
									<!--noindex-->
										<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-default basket read_more"><?=\Bitrix\Main\Config\Option::get('aspro.next', "EXPRESSION_READ_MORE_OFFERS_DEFAULT", GetMessage("CATALOG_READ_MORE"));?></a>
									<!--/noindex-->
								</div>
							</div>
							*/?>
							<?if(!$arItem["OFFERS"]):?>
								<?if($arParams["SHOW_BUY_BTN"] == "Y"):?>
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
								<?else:?>
									<div class="counter_wrapp">
										<div class="button_block">
											<!--noindex-->
												<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-default basket read_more"><?=\Bitrix\Main\Config\Option::get('aspro.next', "EXPRESSION_READ_MORE_OFFERS_DEFAULT", GetMessage("CATALOG_READ_MORE"));?></a>
											<!--/noindex-->
										</div>
									</div>
								<?endif;?>
								<?
								if(isset($arItem['PRICE_MATRIX']) && $arItem['PRICE_MATRIX']) // USE_PRICE_COUNT
								{?>
									<?if($arItem['ITEM_PRICE_MODE'] == 'Q' && count($arItem['PRICE_MATRIX']['ROWS']) > 1):?>
										<?$arOnlyItemJSParams = array(
											"ITEM_PRICES" => $arItem["ITEM_PRICES"],
											"ITEM_PRICE_MODE" => $arItem["ITEM_PRICE_MODE"],
											"ITEM_QUANTITY_RANGES" => $arItem["ITEM_QUANTITY_RANGES"],
											"MIN_QUANTITY_BUY" => $arAddToBasketData["MIN_QUANTITY_BUY"],
											"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
											"ID" => $arItemIDs["strMainID"],
										)?>
										<script type="text/javascript">
											var <? echo $arItemIDs["strObName"]; ?>el = new JCCatalogSectionOnlyElement(<? echo CUtil::PhpToJSObject($arOnlyItemJSParams, false, true); ?>);
										</script>
									<?endif;?>
								<?}?>
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
									<div class="offer_buy_block">
										<?
										$arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['IS_OFFER'] = 'Y';
											$arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
										$arAddToBasketData = CNext::GetAddToBasketArray($arItem["OFFERS"][$arItem["OFFERS_SELECTED"]], $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small', $arParams);
										?>
										<div class="counter_wrapp">
											<?if(($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_LIST"] && $arAddToBasketData["ACTION"] == "ADD") && $arAddToBasketData["CAN_BUY"]):?>
												<div class="counter_block" data-item="<?=$arItem["OFFERS"][$arItem["OFFERS_SELECTED"]]["ID"];?>">
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
									</div>
									<?
									if(isset($arCurrentSKU['PRICE_MATRIX']) && $arCurrentSKU['PRICE_MATRIX']) // USE_PRICE_COUNT
									{?>
										<?if($arCurrentSKU['ITEM_PRICE_MODE'] == 'Q' && count($arCurrentSKU['PRICE_MATRIX']['ROWS']) > 1):?>
											<?$arOnlyItemJSParams = array(
												"ITEM_PRICES" => $arCurrentSKU["ITEM_PRICES"],
												"ITEM_PRICE_MODE" => $arCurrentSKU["ITEM_PRICE_MODE"],
												"ITEM_QUANTITY_RANGES" => $arCurrentSKU["ITEM_QUANTITY_RANGES"],
												"MIN_QUANTITY_BUY" => $arAddToBasketData["MIN_QUANTITY_BUY"],
												"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
												"ID" => $arItemIDs["strMainID"],
												"NOT_SHOW" => "Y",
											)?>
											<script type="text/javascript">
												var <? echo $arItemIDs["strObName"]; ?>el = new JCCatalogSectionOnlyElement(<? echo CUtil::PhpToJSObject($arOnlyItemJSParams, false, true); ?>);
											</script>
										<?endif;?>
									<?}?>
								<?}?>
								<div class="counter_wrapp ce_cmp_visible">
									<div id="<?=$arItemIDs["ALL_ITEM_IDS"]['BASKET_ACTIONS']; ?>" class="button_block wide">
										<a class="btn btn-default basket read_more" rel="nofollow" data-item="<?=$arItem['ID']?>" href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=GetMessage('CATALOG_READ_MORE')?></a>
									</div>
								</div>
							<?endif;?>
						</div>
					</div>
				</div>
			</div>
		<?}?>
		</div>
	</div>
<?}?>
