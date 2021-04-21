<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if( count( $arResult["ITEMS"] ) >= 1 ){?>
	<div class="viewed_block">
		<h3 class="title_block sm"><?=($arParams["TITLE_BLOCK"] ? $arParams["TITLE_BLOCK"] : GetMessage("TITLE_BLOCK_NAME"))?></h3>
		<div class="outer_wrap flexslider shadow items border custom_flex top_right" data-plugin-options='{"animation": "slide", "directionNav": true, "itemMargin":10, "controlNav" :false, "animationLoop": true, "slideshow": false, "counts": [8,4,3,2,1]}'>
			<ul class="rows_block slides">
				<?foreach($arResult["ITEMS"] as $arItem){
					$isItem=(isset($arItem['ID']) ? true : false);?>
					<li class="item_block visible">
						<div class="item_wrap item <?=($isItem ? 'has-item' : '' );?>" <?=($isItem ? "id='".$this->GetEditAreaId($arItem['ID'])."'" : "")?>>
							<?if($isItem){?>
								<?$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

								$item_id = $arItem["ID"];
								$strMeasure = '';
								if($arParams["SHOW_MEASURE"] == "Y" && $arItem["CATALOG_MEASURE"]){
									$arMeasure = CCatalogMeasure::getList(array(), array("ID" => $arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
									$strMeasure = $arMeasure["SYMBOL_RUS"];
								}
								$elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);?>
								<div class="inner_wrap">
									<div class="image_wrapper_block">
										<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb">
											<?
											$a_alt = ($arItem["PREVIEW_PICTURE"] && strlen($arItem["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"] ));
											$a_title = ($arItem["PREVIEW_PICTURE"] && strlen($arItem["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"] ));
											?>
											<?if(isset($arItem["IMG"]) && $arItem["IMG"]):?>
												<img src="<?=$arItem["IMG"]["src"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
											<?else:?>
												<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
											<?endif;?>
										</a>
									</div>
									<div class="item_info">
										<div class="item-title">
											<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="dark-color"><span><?=$elementName;?></span></a>
										</div>
										<div class="cost prices clearfix">
											<?if( $arItem["OFFERS"]){?>
												<?$minPrice = false;
												if (isset($arItem['MIN_PRICE']) || isset($arItem['RATIO_PRICE'])){
													// $minPrice = (isset($arItem['RATIO_PRICE']) ? $arItem['RATIO_PRICE'] : $arItem['MIN_PRICE']);
													$minPrice = $arItem['MIN_PRICE'];
												}
												$offer_id=0;
												if($arParams["TYPE_SKU"]=="N"){
													$offer_id=$minPrice["MIN_ITEM_ID"];
												}
												$min_price_id=$minPrice["MIN_PRICE_ID"];
												if(!$min_price_id)
													$min_price_id=$minPrice["PRICE_ID"];
												if($minPrice["MIN_ITEM_ID"])
													$item_id=$minPrice["MIN_ITEM_ID"];
												$prefix = '';
												if('N' == $arParams['TYPE_SKU'] || $arParams['DISPLAY_TYPE'] !== 'block'){
													$prefix = GetMessage("CATALOG_FROM");
												}?>
												<div class="price only_price">
													<?if(strlen($minPrice["PRINT_VALUE"])):?>
														<?=$prefix;?> <span class="values_wrapper"><?=($minPrice['PRINT_DISCOUNT_VALUE'] ? $minPrice['PRINT_DISCOUNT_VALUE'] : $minPrice['PRINT_VALUE']);?></span><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure){?><span class="measure">/<?=$strMeasure?></span><?}?>
													<?endif;?>
												</div>
											<?}elseif ( $arItem["PRICES"] ){?>
												<? $arCountPricesCanAccess = 0;
												$min_price_id=0;
												foreach( $arItem["PRICES"] as $key => $arPrice ) { if($arPrice["CAN_ACCESS"]){$arCountPricesCanAccess++;} } ?>
												<?foreach($arItem["PRICES"] as $key => $arPrice){?>
													<?if($arPrice["CAN_ACCESS"]){
														$percent=0;
														if($arPrice["MIN_PRICE"]=="Y"){
															$min_price_id=$arPrice["PRICE_ID"];
														}?>
														<?$price = CPrice::GetByID($arPrice["ID"]);?>
														<?if($arCountPricesCanAccess > 1):?>
															<div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div>
														<?endif;?>
														<div class="price only_price">
															<?if(strlen($arPrice["PRINT_VALUE"])):?>
																<span class="values_wrapper"><?=\Aspro\Functions\CAsproItem::getCurrentPrice("DISCOUNT_VALUE", $arPrice);?></span><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure){?><span class="measure">/<?=$strMeasure?></span><?}?>
															<?endif;?>
														</div>
													<?}?>
												<?}?>
											<?}?>
										</div>
									</div>
								</div>
							<?}?>
						</div>
					</li>
				<?}?>
			</ul>
		</div>
	</div>
<?}?>
