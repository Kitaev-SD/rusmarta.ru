<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$frame = $this->createFrame()->begin("");
$templateData = array(
	//'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
	'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME']
);
$injectId = $arParams['UNIQ_COMPONENT_ID'];

if (isset($arResult['REQUEST_ITEMS']))
{
	// code to receive recommendations from the cloud
	CJSCore::Init(array('ajax'));

	// component parameters
	$signer = new \Bitrix\Main\Security\Sign\Signer;
	$signedParameters = $signer->sign(
		base64_encode(serialize($arResult['_ORIGINAL_PARAMS'])),
		'bx.bd.products.recommendation'
	);
	$signedTemplate = $signer->sign($arResult['RCM_TEMPLATE'], 'bx.bd.products.recommendation');

	?>

	<span id="<?=$injectId?>"></span>

	<script type="text/javascript">
		BX.ready(function(){
			bx_rcm_get_from_cloud(
				'<?=CUtil::JSEscape($injectId)?>',
				<?=CUtil::PhpToJSObject($arResult['RCM_PARAMS'])?>,
				{
					'parameters':'<?=CUtil::JSEscape($signedParameters)?>',
					'template': '<?=CUtil::JSEscape($signedTemplate)?>',
					'site_id': '<?=CUtil::JSEscape(SITE_ID)?>',
					'rcm': 'yes'
				}
			);
		});
	</script>
	<?
	$frame->end();
	return;

	// \ end of the code to receive recommendations from the cloud
}
if($arResult['ITEMS']){?>
	<?$arResult['RID'] = ($arResult['RID'] ? $arResult['RID'] : (\Bitrix\Main\Context::getCurrent()->getRequest()->get('RID') != 'undefined' ? \Bitrix\Main\Context::getCurrent()->getRequest()->get('RID') : '' ));?>
	<input type="hidden" name="bigdata_recommendation_id" value="<?=htmlspecialcharsbx($arResult['RID'])?>">
	<div id="<?=$injectId?>_items" class="bigdata_recommended_products_items">
		<?$class_block="s_".$this->randString();?>
		<div class="viewed_slider common_product wrapper_block recomendation <?=$class_block;?>">
			<div class="top_block">
				<?$title_block=($arParams["TITLE_BLOCK"] ? $arParams["TITLE_BLOCK"] : GetMessage('RECOMENDATION_TITLE'));?>
				<div class="title_block"><?=$title_block;?></div>
			</div>
			<ul class="viewed_navigation slider_navigation top_big custom_flex border"></ul>
			<div class="all_wrapp basket">
				<div class="content_inner tab flexslider loading_state shadow border custom_flex top_right" data-plugin-options='{"animation": "slide", "animationSpeed": 600, "directionNav": true, "controlNav" :false, "animationLoop": true, "slideshow": false, "controlsContainer": ".viewed_navigation", "counts": [5,4,3,2,1]}'>
					<ul class="tabs_slider slides catalog_block">
						<?foreach ($arResult['ITEMS'] as $key => $arItem){?>
							<?$strMainID = $this->GetEditAreaId($arItem['ID'] . $key);?>
							<li class="catalog_item visible" id="<?=$strMainID;?>">
								<?
								$totalCount = CNext::GetTotalCount($arItem, $arParams);
								$arQuantityData = CNext::GetQuantityArray($totalCount);
								$arItem["FRONT_CATALOG"]="Y";
								$arItem["RID"]=$arResult["RID"];
								$arAddToBasketData = CNext::GetAddToBasketArray($arItem, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], true);

								$elementName = ((isset($arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arItem['NAME']);

								$strMeasure='';
								if($arItem["OFFERS"]){
									$strMeasure=$arItem["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
								}else{
									if (($arParams["SHOW_MEASURE"]=="Y")&&($arItem["CATALOG_MEASURE"])){
										$arMeasure = CCatalogMeasure::getList(array(), array("ID"=>$arItem["CATALOG_MEASURE"]), false, false, array())->GetNext();
										$strMeasure=$arMeasure["SYMBOL_RUS"];
									}
								}
								?>

								<div class="inner_wrap">
									<div class="image_wrapper_block">
										<a href="<?=$arItem["DETAIL_PAGE_URL"]?><?=($arResult["RID"] ? '?RID='.$arResult["RID"] : '')?>" class="thumb shine">
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
													<?if($arAddToBasketData["CAN_BUY"] && empty($arItem["OFFERS"]) && $arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
														<div class="wish_item_button" <?=($arAddToBasketData["CAN_BUY"] ? '' : 'style="display:none"');?>>
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
											<?
											$a_alt = ($arItem["PREVIEW_PICTURE"] && strlen($arItem["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arItem["NAME"] ));
											$a_title = ($arItem["PREVIEW_PICTURE"] && strlen($arItem["PREVIEW_PICTURE"]['DESCRIPTION']) ? $arItem["PREVIEW_PICTURE"]['DESCRIPTION'] : ($arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] ? $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arItem["NAME"] ));
											?>
											<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
												<img border="0" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
											<?elseif(!empty($arItem["DETAIL_PICTURE"])):?>
												<?$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"], array("width" => 170, "height" => 170), BX_RESIZE_IMAGE_PROPORTIONAL, true );?>
												<img border="0" src="<?=$img["src"]?>" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
											<?else:?>
												<img border="0" src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$a_alt;?>" title="<?=$a_title;?>" />
											<?endif;?>
											<?if($fast_view_text_tmp = CNext::GetFrontParametrValue('EXPRESSION_FOR_FAST_VIEW'))
												$fast_view_text = $fast_view_text_tmp;
											else
												$fast_view_text = GetMessage('FAST_VIEW');?>
											<div class="fast_view_block" data-event="jqm" data-param-form_id="fast_view" data-param-iblock_id="<?=$arItem["IBLOCK_ID"];?>" data-param-id="<?=$arItem["ID"];?>" data-param-item_href="<?=urlencode($arItem["DETAIL_PAGE_URL"]);?>" data-name="fast_view"><?=$fast_view_text;?></div>
										</a>
									</div>
									<div class="item_info">
										<div class="item-title">
											<a href="<?=$arItem["DETAIL_PAGE_URL"]?><?=($arResult["RID"] ? '?RID='.$arResult["RID"] : '')?>" class="dark_link"><span><?=$elementName?></span></a>
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
						<?}?>
					</ul>
				</div>
			</div>
		</div>
	</div>
<?}
$frame->end();?>