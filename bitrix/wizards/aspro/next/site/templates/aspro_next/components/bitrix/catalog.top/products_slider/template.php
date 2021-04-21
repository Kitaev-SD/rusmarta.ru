<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<? $this->setFrameMode( true ); ?>
<?
$sliderID  = "specials_slider_wrapp_".$this->randString();
$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);
?>
<?if($arResult["ITEMS"]):?>
	<hr />
	<?if(strlen($arParams['TITLE'])):?>
		<h5><?=$arParams['TITLE'];?></h5>
	<?endif;?>
	<div class="common_product wrapper_block" id="<?=$sliderID?>">
		<ul class="slider_navigation top_big custom_flex border"></ul>
		<div class="all_wrapp">
			<div class="content_inner tab flexslider loading_state shadow border custom_flex top_right" data-plugin-options='{"animation": "slide", "animationSpeed": 600, "directionNav": true, "controlNav" :false, "animationLoop": true, "slideshow": false, "counts": [4,3,3,2,2]}'>
				<ul class="tabs_slider items slides">
					<?foreach($arResult["ITEMS"] as $key => $arItem):?>
						<?
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));
						$totalCount = CNext::GetTotalCount($arItem, $arParams);
						$arQuantityData = CNext::GetQuantityArray($totalCount);
						$arItem["FRONT_CATALOG"]="Y";

						$strMeasure='';
						if($arItem["OFFERS"]){
							$strMeasure=$arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
						}else{
							if (($arParams["SHOW_MEASURE"]=="Y")&&($arItem["CATALOG_MEASURE"])){
								$arMeasure = CCatalogMeasure::getList(array(), array("ID"=>$arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
								$strMeasure=$arMeasure["SYMBOL_RUS"];
							}
						}

						$elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);
						?>
						<?$arAddToBasketData = CNext::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], true);?>
						<li id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="catalog_item visible">
							<div class="inner_wrap">
								<div class="image_wrapper_block">
									<?if($arItem["PROPERTIES"]["HIT"]["VALUE"] || ($arParams["SALE_STIKER"] && $arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"])){?>
										<div class="stickers">
											<?if($arItem["PROPERTIES"]["HIT"]["VALUE"]):?>
												<?$prop = ($arParams["STIKERS_PROP"] ? $arParams["STIKERS_PROP"] : "HIT");?>
												<?foreach(CNext::GetItemStickers($arItem["PROPERTIES"][$prop]) as $arSticker):?>
													<div><div class="<?=$arSticker['CLASS']?>"><?=$arSticker['VALUE']?></div></div>
												<?endforeach;?>
											<?endif;?>
											<?if($arParams["SALE_STIKER"] && $arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"]){?>
												<div><div class="sticker_sale_text"><?=$arItem["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"];?></div></div>
											<?}?>
										</div>
									<?}?>
									<?if( ($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y")):?>
										<div class="like_icons">
											<?if($arAddToBasketData["CAN_BUY"] && empty($arItem["OFFERS"]) && $arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
												<div class="wish_item_button" <?=($arAddToBasketData['CAN_BUY'] ? '' : 'style="display:none"');?>>
													<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item to" data-item="<?=$arItem["ID"]?>"><i></i></span>
													<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item in added" style="display: none;" data-item="<?=$arItem["ID"]?>"><i></i></span>
												</div>
											<?endif;?>
											<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
												<div class="compare_item_button">
													<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>" ><i></i></span>
													<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arItem["ID"]?>"><i></i></span>
												</div>
											<?endif;?>
										</div>
									<?endif;?>
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="thumb shine">
										<?
										$a_alt = ($arItem["PREVIEW_PICTURE"] && strlen($arItem["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"] ));
										$a_title = ($arItem["PREVIEW_PICTURE"] && strlen($arItem["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"] ));
										?>
										<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
											<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
										<?elseif(!empty($arItem["DETAIL_PICTURE"])):?>
											<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array("width" => 170, "height" => 170), BX_RESIZE_IMAGE_PROPORTIONAL, true );?>
											<img src="<?=$img["src"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
										<?else:?>
											<img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
										<?endif;?>
										<?if($fast_view_text_tmp = CNext::GetFrontParametrValue('EXPRESSION_FOR_FAST_VIEW'))
											$fast_view_text = $fast_view_text_tmp;
										else
											$fast_view_text = GetMessage('FAST_VIEW');?>
									</a>
									<div class="fast_view_block" data-event="jqm" data-param-form_id="fast_view" data-param-iblock_id="<?=$arParams["IBLOCK_ID"];?>" data-param-id="<?=$arItem["ID"];?>" data-param-item_href="<?=urlencode($arItem["DETAIL_PAGE_URL"]);?>" data-name="fast_view"><?=$fast_view_text;?></div>
								</div>
								<div class="item_info">
									<div class="item-title">
										<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="dark_link"><span><?=$elementName?></span></a>
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
										<?if($arItem["OFFERS"]):?>
											<?\Aspro\Functions\CAsproSku::showItemPrices($arParams, $arItem, $item_id, $min_price_id, array(), ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] == "Y" ? "N" : "Y"));?>
										<?else:?>
											<?
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
											{?>
												<?\Aspro\Functions\CAsproItem::showItemPrices($arParams, $arItem["PRICES"], $strMeasure, $min_price_id, ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] == "Y" ? "N" : "Y"));?>
											<?}?>
										<?endif;?>
									</div>
								</div>
								<div class="footer_button">
									<?=$arAddToBasketData["HTML"]?>
								</div>
							</div>
						</li>
					<?endforeach;?>
				</ul>
			</div>
		</div>
	</div>
<?else:?>
	<?$this->setFrameMode(true);?>
	<script type="text/javascript">
	$(document).ready(function(){
		$(".news_detail_wrapp .similar_products_wrapp").remove();
	}); /* dirty hack, remove this code */
	</script>
<?endif;?>