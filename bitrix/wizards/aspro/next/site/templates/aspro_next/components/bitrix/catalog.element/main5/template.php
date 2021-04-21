<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="basket_props_block" id="bx_basket_div_<?=$arResult["ID"];?>" style="display: none;">
	<?if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])){
		foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo){?>
			<input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
			<?if (isset($arResult['PRODUCT_PROPERTIES'][$propID]))
				unset($arResult['PRODUCT_PROPERTIES'][$propID]);
		}
	}
	$arResult["EMPTY_PROPS_JS"]="Y";
	$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
	if (!$emptyProductProperties){
		$arResult["EMPTY_PROPS_JS"]="N";?>
		<div class="wrapper">
			<table>
				<?foreach ($arResult['PRODUCT_PROPERTIES'] as $propID => $propInfo){?>
					<tr>
						<td><? echo $arResult['PROPERTIES'][$propID]['NAME']; ?></td>
						<td>
							<?if('L' == $arResult['PROPERTIES'][$propID]['PROPERTY_TYPE'] && 'C' == $arResult['PROPERTIES'][$propID]['LIST_TYPE']){
								foreach($propInfo['VALUES'] as $valueID => $value){?>
									<label>
										<input type="radio" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]" value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>><? echo $value; ?>
									</label>
								<?}
							}else{?>
								<select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]">
									<?foreach($propInfo['VALUES'] as $valueID => $value){?>
										<option value="<? echo $valueID; ?>" <? echo ($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>><? echo $value; ?></option>
									<?}?>
								</select>
							<?}?>
						</td>
					</tr>
				<?}?>
			</table>
		</div>
	<?}?>
</div>
<?
$this->setFrameMode(true);
$currencyList = '';
if (!empty($arResult['CURRENCIES'])){
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}

$arTmpAssoc = json_decode($arResult['PROPERTIES']['ASSOCIATED_FILTER']['~VALUE'], true);
$arTmpExp = json_decode($arResult['PROPERTIES']['EXPANDABLES_FILTER']['~VALUE'], true);

$templateData = array(
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES' => $currencyList,
	'BRAND_ITEM' => $arResult['BRAND_ITEM'],
	'ASSOCIATED' => $arResult['PROPERTIES']['ASSOCIATED']['VALUE'],
	'ASSOCIATED_FILTER' => ($arTmpAssoc && $arTmpAssoc['CHILDREN'] ? $arResult['PROPERTIES']['ASSOCIATED_FILTER']['~VALUE'] : ''),
	'STORES' => array(
		"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
		"SCHEDULE" => $arParams["SCHEDULE"],
		"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
		"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
		"ELEMENT_ID" => $arResult["ID"],
		"STORE_PATH"  =>  $arParams["STORE_PATH"],
		"MAIN_TITLE"  =>  $arParams["MAIN_TITLE"],
		"MAX_AMOUNT"=>$arParams["MAX_AMOUNT"],
		"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
		"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
		"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
		"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
		"USER_FIELDS" => $arParams['USER_FIELDS'],
		"FIELDS" => $arParams['FIELDS'],
		"STORES_FILTER_ORDER" => $arParams['STORES_FILTER_ORDER'],
		"STORES_FILTER" => $arParams['STORES_FILTER'],
		"STORES" => $arParams['STORES'] = array_diff($arParams['STORES'], array('')),
		"SET_ITEMS" => $arResult["SET_ITEMS"],
	)
);
unset($currencyList, $templateLibrary);

if($arResult["PROPERTIES"]["YM_ELEMENT_ID"] && $arResult["PROPERTIES"]["YM_ELEMENT_ID"]["VALUE"])
	$templateData["YM_ELEMENT_ID"] = $arResult["PROPERTIES"]["YM_ELEMENT_ID"]["VALUE"];

$arSkuTemplate = array();
if (!empty($arResult['SKU_PROPS'])){
	$arSkuTemplate=CNext::GetSKUPropsArray($arResult['SKU_PROPS'], $arResult["SKU_IBLOCK_ID"], "list", $arParams["OFFER_HIDE_NAME_PROPS"]);
}
$strMainID = $this->GetEditAreaId($arResult['ID']);

$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

$arResult["strMainID"] = $this->GetEditAreaId($arResult['ID']);
$arItemIDs=CNext::GetItemsIDs($arResult, "Y");
$totalCount = CNext::GetTotalCount($arResult, $arParams);

$arQuantityData = CNext::GetQuantityArray($totalCount, $arItemIDs["ALL_ITEM_IDS"], "Y");

$arParams["BASKET_ITEMS"]=($arParams["BASKET_ITEMS"] ? $arParams["BASKET_ITEMS"] : array());
$useStores = $arParams["USE_STORE"] == "Y" && $arResult["STORES_COUNT"] && $arQuantityData["RIGHTS"]["SHOW_QUANTITY"];
$showCustomOffer=(($arResult['OFFERS'] && $arParams["TYPE_SKU"] !="N") ? true : false);
if($showCustomOffer){
	$templateData['JS_OBJ'] = $strObName;
}
$strMeasure='';
$arAddToBasketData = array();

$templateData['STR_ID'] = $strObName;

if($arResult["OFFERS"]){
	$strMeasure=$arResult["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
	$templateData["STORES"]["OFFERS"]="Y";
	foreach($arResult["OFFERS"] as $arOffer){
		$templateData["STORES"]["OFFERS_ID"][]=$arOffer["ID"];
	}
}else{
	if (($arParams["SHOW_MEASURE"]=="Y")&&($arResult["CATALOG_MEASURE"])){
		$arMeasure = CCatalogMeasure::getList(array(), array("ID"=>$arResult["CATALOG_MEASURE"]), false, false, array())->GetNext();
		$strMeasure=$arMeasure["SYMBOL_RUS"];
	}
	$arAddToBasketData = CNext::GetAddToBasketArray($arResult, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'btn-lg w_icons', $arParams);
}
$arOfferProps = implode(';', $arParams['OFFERS_CART_PROPERTIES']);

// save item viewed
$arFirstPhoto = reset($arResult['MORE_PHOTO']);
$arItemPrices = $arResult['MIN_PRICE'];
if(isset($arResult['PRICE_MATRIX']) && $arResult['PRICE_MATRIX'])
{
	$rangSelected = $arResult['ITEM_QUANTITY_RANGE_SELECTED'];
	$priceSelected = $arResult['ITEM_PRICE_SELECTED'];
	if(isset($arResult['FIX_PRICE_MATRIX']) && $arResult['FIX_PRICE_MATRIX'])
	{
		$rangSelected = $arResult['FIX_PRICE_MATRIX']['RANGE_SELECT'];
		$priceSelected = $arResult['FIX_PRICE_MATRIX']['PRICE_SELECT'];
	}
	$arItemPrices = $arResult['ITEM_PRICES'][$priceSelected];
	$arItemPrices['VALUE'] = $arItemPrices['BASE_PRICE'];
	$arItemPrices['PRINT_VALUE'] = \Aspro\Functions\CAsproItem::getCurrentPrice('BASE_PRICE', $arItemPrices);
	$arItemPrices['DISCOUNT_VALUE'] = $arItemPrices['PRICE'];
	$arItemPrices['PRINT_DISCOUNT_VALUE'] = \Aspro\Functions\CAsproItem::getCurrentPrice('PRICE', $arItemPrices);
}
$arViewedData = array(
	'PRODUCT_ID' => $arResult['ID'],
	'IBLOCK_ID' => $arResult['IBLOCK_ID'],
	'NAME' => $arResult['NAME'],
	'DETAIL_PAGE_URL' => $arResult['DETAIL_PAGE_URL'],
	'PICTURE_ID' => $arResult['PREVIEW_PICTURE'] ? $arResult['PREVIEW_PICTURE']['ID'] : ($arFirstPhoto ? $arFirstPhoto['ID'] : false),
	'CATALOG_MEASURE_NAME' => $arResult['CATALOG_MEASURE_NAME'],
	'MIN_PRICE' => $arItemPrices,
	'CAN_BUY' => $arResult['CAN_BUY'] ? 'Y' : 'N',
	'IS_OFFER' => 'N',
	'WITH_OFFERS' => $arResult['OFFERS'] ? 'Y' : 'N',
);
$actualItem = $arResult["OFFERS"] ? (isset($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]) ? $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] : reset($arResult['OFFERS'])) : $arResult;
?>
<script type="text/javascript">
setViewedProduct(<?=$arResult['ID']?>, <?=CUtil::PhpToJSObject($arViewedData, false)?>);
</script>
<meta itemprop="name" content="<?=$name = strip_tags(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arResult['NAME'])?>" />
<meta itemprop="category" content="<?=$arResult['CATEGORY_PATH']?>" />
<meta itemprop="description" content="<?=(strlen(strip_tags($arResult['PREVIEW_TEXT'])) ? strip_tags($arResult['PREVIEW_TEXT']) : (strlen(strip_tags($arResult['DETAIL_TEXT'])) ? strip_tags($arResult['DETAIL_TEXT']) : $name))?>" />

<div class="item_main_info type_clothes <?=(!$showCustomOffer ? "noffer" : "");?> <?=($arParams["SHOW_UNABLE_SKU_PROPS"] != "N" ? "show_un_props" : "unshow_un_props");?>" id="<?=$arItemIDs["strMainID"];?>">
	<div class="img_wrapper swipeignore">
		<div class="stickers">
			<?$prop = ($arParams["STIKERS_PROP"] ? $arParams["STIKERS_PROP"] : "HIT");?>
			<?foreach(CNext::GetItemStickers($arResult["PROPERTIES"][$prop]) as $arSticker):?>
				<div><div class="<?=$arSticker['CLASS']?>"><?=$arSticker['VALUE']?></div></div>
			<?endforeach;?>
			<?if($arParams["SALE_STIKER"] && $arResult["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"]){?>
				<div><div class="sticker_sale_text"><?=$arResult["PROPERTIES"][$arParams["SALE_STIKER"]]["VALUE"];?></div></div>
			<?}?>
		</div>
		<?$countThumb = count($arResult["MORE_PHOTO"]);?>
		<div class="item_slider has_<?=($countThumb > 1 ? 'more' : 'one');?>">
			<?if(($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y") || (strlen($arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) || ($arResult['SHOW_OFFERS_PROPS'] && $showCustomOffer))):?>
				<div class="like_wrapper">
					<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y"):?>
						<div class="like_icons iblock">
							<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
								<?if(!$arResult["OFFERS"]):?>
									<div class="wish_item text" <?=($arAddToBasketData['CAN_BUY'] ? '' : 'style="display:none"');?> data-item="<?=$arResult["ID"]?>" data-iblock="<?=$arResult["IBLOCK_ID"]?>">
										<span class="value" title="<?=GetMessage('CT_BCE_CATALOG_IZB')?>" ><i></i></span>
										<span class="value added" title="<?=GetMessage('CT_BCE_CATALOG_IZB_ADDED')?>"><i></i></span>
									</div>
								<?elseif($arResult["OFFERS"] && $arParams["TYPE_SKU"] === 'TYPE_1' && !empty($arResult['OFFERS_PROP'])):?>
									<div class="wish_item text " <?=($arAddToBasketData['CAN_BUY'] ? '' : 'style="display:none"');?> data-item="" data-iblock="<?=$arResult["IBLOCK_ID"]?>" <?=(!empty($arResult['OFFERS_PROP']) ? 'data-offers="Y"' : '');?> data-props="<?=$arOfferProps?>">
										<span class="value <?=$arParams["TYPE_SKU"];?>" title="<?=GetMessage('CT_BCE_CATALOG_IZB')?>"><i></i></span>
										<span class="value added <?=$arParams["TYPE_SKU"];?>" title="<?=GetMessage('CT_BCE_CATALOG_IZB_ADDED')?>"><i></i></span>
									</div>
								<?endif;?>
							<?endif;?>
							<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
								<?if(!$arResult["OFFERS"] || ($arResult["OFFERS"] && $arParams["TYPE_SKU"] === 'TYPE_1' && !$arResult["OFFERS_PROP"])):?>
									<div data-item="<?=$arResult["ID"]?>" data-iblock="<?=$arResult["IBLOCK_ID"]?>" data-href="<?=$arResult["COMPARE_URL"]?>" class="compare_item text <?=($arResult["OFFERS"] ? $arParams["TYPE_SKU"] : "");?>" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['COMPARE_LINK']; ?>">
										<span class="value" title="<?=GetMessage('CT_BCE_CATALOG_COMPARE')?>"><i></i></span>
										<span class="value added" title="<?=GetMessage('CT_BCE_CATALOG_COMPARE_ADDED')?>"><i></i></span>
									</div>
								<?elseif($arResult["OFFERS"] && $arParams["TYPE_SKU"] === 'TYPE_1'):?>
									<div data-item="<?=$arResult["ID"]?>" data-iblock="<?=$arResult["IBLOCK_ID"]?>" data-href="<?=$arResult["COMPARE_URL"]?>" class="compare_item text <?=$arParams["TYPE_SKU"];?>">
										<span class="value" title="<?=GetMessage('CT_BCE_CATALOG_COMPARE')?>"><i></i></span>
										<span class="value added" title="<?=GetMessage('CT_BCE_CATALOG_COMPARE_ADDED')?>"><i></i></span>
									</div>
								<?endif;?>
							<?endif;?>
						</div>
					<?endif;?>
				</div>
			<?endif;?>

			<?reset($arResult['MORE_PHOTO']);
			$arFirstPhoto = current($arResult['MORE_PHOTO']);
			$viewImgType=$arParams["DETAIL_PICTURE_MODE"];?>
			<div class="slides">
				<?if($showCustomOffer && !empty($arResult['OFFERS_PROP'])){?>
					<div class="offers_img wof">
						<?$alt=$arFirstPhoto["ALT"];
						$title=$arFirstPhoto["TITLE"];?>
						<?if($arFirstPhoto["BIG"]["src"]){?>
							<a href="<?=($viewImgType=="POPUP" ? $arFirstPhoto["BIG"]["src"] : "javascript:void(0)");?>" class="<?=($viewImgType=="POPUP" ? "popup_link" : "line_link");?>" title="<?=$title;?>">
								<img id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PICT']; ?>" src="<?=$arFirstPhoto['SMALL']['src']; ?>" <?=($viewImgType=="MAGNIFIER" ? 'data-large="" data-xpreview="" data-xoriginal=""': "");?> alt="<?=$alt;?>" title="<?=$title;?>" itemprop="image">
								<div class="zoom"></div>
							</a>
						<?}else{?>
							<a href="javascript:void(0)" class="" title="<?=$title;?>">
								<img id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PICT']; ?>" src="<?=$arFirstPhoto['SRC']; ?>" alt="<?=$alt;?>" title="<?=$title;?>" itemprop="image">
								<div class="zoom"></div>
							</a>
						<?}?>
					</div>
				<?}else{
					if($arResult["MORE_PHOTO"]){
						$bMagnifier = ($viewImgType=="MAGNIFIER");?>
						<ul>
							<?foreach($arResult["MORE_PHOTO"] as $i => $arImage){
								if($i && $bMagnifier):?>
									<?continue;?>
								<?endif;?>
								<?$isEmpty=($arImage["SMALL"]["src"] ? false : true );?>
								<?
								$alt=$arImage["ALT"];
								$title=$arImage["TITLE"];
								?>
								<li id="photo-<?=$i?>" <?=(!$i ? 'class="current"' : 'style="display: none;"')?>>
									<?if(!$isEmpty){?>
										<a href="<?=($viewImgType=="POPUP" ? $arImage["BIG"]["src"] : "javascript:void(0)");?>" <?=($bIsOneImage ? '' : 'data-fancybox-group="item_slider"')?> class="<?=($viewImgType=="POPUP" ? "popup_link fancy" : "line_link");?>" title="<?=$title;?>">
											<img  src="<?=$arImage["SMALL"]["src"]?>" <?=($viewImgType=="MAGNIFIER" ? "class='zoom_picture'" : "");?> <?=($viewImgType=="MAGNIFIER" ? 'data-xoriginal="'.$arImage["BIG"]["src"].'" data-xpreview="'.$arImage["THUMB"]["src"].'"' : "");?> alt="<?=$alt;?>" title="<?=$title;?>"<?=(!$i ? ' itemprop="image"' : '')?>/>
											<div class="zoom"></div>
										</a>
									<?}else{?>
										<img  src="<?=$arImage["SRC"]?>" alt="<?=$alt;?>" title="<?=$title;?>" />
									<?}?>
								</li>
							<?}?>
						</ul>
						<?if($countThumb > 1):?>
							<ul class="flex-direction-nav"><li class="flex-nav-prev"><span class="flex-prev">Previous</span></li><li class="flex-nav-next"><span class="flex-next">Next</span></li></ul>
						<?endif;?>
					<?}
				}?>
			</div>
			<?/*thumbs*/?>
			<?if(!$showCustomOffer || empty($arResult['OFFERS_PROP'])){
				if($countThumb > 1 || $arResult['PROPERTIES']['POPUP_VIDEO']['VALUE']):?>
					<div class="wrapp_thumbs xzoom-thumbs top-small-wrapper">
						<?if($countThumb > 1):?>
							<div class="thumbs flexslider1 bxSlider">
								<div class="inner_slider">
									<ul class="slides_block" id="thumbs">
										<?foreach($arResult["MORE_PHOTO"]as $i => $arImage):?>
											<li <?=(!$i ? 'class="current"' : '')?> data-slide_key="<?=$i;?>" data-big_img="<?=$arImage["BIG"]["src"]?>" data-small_img="<?=$arImage["SMALL"]["src"]?>">
												<span><img class="xzoom-gallery" data-xpreview="<?=$arImage["THUMB"]["src"];?>" src="<?=$arImage["THUMB"]["src"]?>" alt="<?=$arImage["ALT"];?>" title="<?=$arImage["TITLE"];?>" /></span>
											</li>
										<?endforeach;?>
									</ul>
								</div>
								<span class="thumbs_navigation bx-controls-direction"><span class="slide-prev"></span><span class="slide-next"></span></span>
							</div>
						<?endif;?>
						<?if($arResult['PROPERTIES']['POPUP_VIDEO']['VALUE']):?>
							<div class="popup_video <?=($countThumb > 5 ? 'fromtop' : '');?>"><a class="various video_link" href="<?=$arResult['PROPERTIES']['POPUP_VIDEO']['VALUE'];?>"><?=GetMessage("VIDEO")?></a></div>
						<?endif;?>
					</div>
					<script>
						$(document).ready(function(){
							$('.item_slider .thumbs li').first().addClass('current');
							$('.item_slider .thumbs .slides_block').delegate('li:not(.current)', 'click', function(){
								var slider_wrapper = $(this).parents('.item_slider'),
									index = $(this).data('slide_key');
								$(this).addClass('current').siblings().removeClass('current')
								$(this).siblings('[data-slide_key='+index+']').addClass('current')//.parents('.item_slider').find('.slides li').fadeOut(333);
								if(arNextOptions['THEME']['DETAIL_PICTURE_MODE'] == 'MAGNIFIER')
								{
									var li = $(this).parents('.item_slider').find('.slides li');
									li.find('img').attr('src', $(this).data('small_img'));
									li.find('img').attr('xoriginal', $(this).data('big_img'));
								}
								else
								{
									slider_wrapper.find('.slides li').removeClass('current').hide();
									slider_wrapper.find('.slides li:eq('+index+')').addClass('current').show();
								}
							});
							$('.bxSlider.thumbs .slides_block').bxSlider({
								mode: 'vertical',
								// infiniteLoop: false,
								minSlides: 5,
								maxSlides: 5,
								slideMargin: 10,
								pager: false,
								adaptiveHeight: false,
								responsive: false,
								touchEnabled: false,
								nextSelector: '.bx-controls-direction .slide-next',
								prevSelector: '.bx-controls-direction .slide-prev',
								oneToOneTouch: false,
								moveSlides: <?=($countThumb > 5 ? 1 : 0);?>,
								preventDefaultSwipeY: true,
								onSliderLoad: function(index)
								{
									<?if($countThumb > 5):?>
										$(this).closest('.bx-viewport').addClass('long');
										$(this).closest('.bxSlider').find('.bx-controls-direction a').addClass('opacityv');
									<?endif;?>
									$('.top-small-wrapper li[data-slide_key="0"]').addClass('flex-active-slide');
								}
							})
						})
					</script>
				<?endif;?>
			<?}else{?>
				<div class="wrapp_thumbs top-small-wrapper">
					<div class="sliders">
						<div class="thumbs bxSlider wof" style=""></div>
					</div>
				</div>
			<?}?>
		</div>
		<?/*mobile*/?>
		<?if(!$showCustomOffer || empty($arResult['OFFERS_PROP'])){?>
			<div class="item_slider flex flexslider color-controls" data-plugin-options='{"animation": "slide", "directionNav": false, "controlNav": true, "animationLoop": false, "slideshow": false, "slideshowSpeed": 10000, "animationSpeed": 600}'>
				<ul class="slides">
					<?if($arResult["MORE_PHOTO"]){
						foreach($arResult["MORE_PHOTO"] as $i => $arImage){?>
							<?$isEmpty=($arImage["SMALL"]["src"] ? false : true );?>
							<li id="mphoto-<?=$i?>" <?=(!$i ? 'class="current"' : 'style="display: none;"')?>>
								<?
								$alt=$arImage["ALT"];
								$title=$arImage["TITLE"];
								?>
								<?if(!$isEmpty){?>
									<a href="<?=$arImage["BIG"]["src"]?>" data-fancybox-group="item_slider_flex" class="fancy popup_link" title="<?=$title;?>" >
										<img src="<?=$arImage["SMALL"]["src"]?>" alt="<?=$alt;?>" title="<?=$title;?>" />
										<div class="zoom"></div>
									</a>
								<?}else{?>
									<img  src="<?=$arImage["SRC"];?>" alt="<?=$alt;?>" title="<?=$title;?>" />
								<?}?>
							</li>
						<?}
					}?>
				</ul>
			</div>
		<?}else{?>
			<div class="item_slider flex color-controls"></div>
		<?}?>
		<?if($arResult['PROPERTIES']['POPUP_VIDEO']['VALUE']):?>
			<div class="popup_video mobile"><a class="various video_link" href="<?=$arResult['PROPERTIES']['POPUP_VIDEO']['VALUE']?>"><?=GetMessage("VIDEO")?></a></div>
		<?endif;?>
	</div>
	<div class="right_info">
		<div class="info_item">
			<?$isArticle=(strlen($arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) || ($arResult['SHOW_OFFERS_PROPS'] && $showCustomOffer));?>
			<?if($isArticle || $arResult["BRAND_ITEM"] || $arParams["SHOW_RATING"] == "Y" || strlen($arResult["PREVIEW_TEXT"])){?>
				<div class="top_info">
					<div class="rows_block">
						<?$col=1;
						if($isArticle && $arResult["BRAND_ITEM"] && $arParams["SHOW_RATING"] == "Y"){
							$col=3;
						}elseif(($isArticle && $arResult["BRAND_ITEM"]) || ($isArticle && $arParams["SHOW_RATING"] == "Y") || ($arResult["BRAND_ITEM"] && $arParams["SHOW_RATING"] == "Y")){
							$col=2;
						}?>
						<?if($arParams["SHOW_RATING"] == "Y"):?>
							<div class="item_block col-<?=$col;?>">
								<?$frame = $this->createFrame('dv_'.$arResult["ID"])->begin('');?>
									<div class="rating">
										<?$APPLICATION->IncludeComponent(
										   "bitrix:iblock.vote",
										   "element_rating",
										   Array(
											  "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
											  "IBLOCK_ID" => $arResult["IBLOCK_ID"],
											  "ELEMENT_ID" => $arResult["ID"],
											  "MAX_VOTE" => 5,
											  "VOTE_NAMES" => array(),
											  "CACHE_TYPE" => $arParams["CACHE_TYPE"],
											  "CACHE_TIME" => $arParams["CACHE_TIME"],
											  "DISPLAY_AS_RATING" => 'vote_avg'
										   ),
										   $component, array("HIDE_ICONS" =>"Y")
										);?>
									</div>
								<?$frame->end();?>
							</div>
						<?endif;?>
						<?if($isArticle):?>
							<div class="item_block col-<?=$col;?>">
								<div class="article iblock" itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue" <?if($arResult['SHOW_OFFERS_PROPS']){?>id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_ARTICLE_DIV'] ?>" style="display: none;"<?}?>>
									<span class="block_title" itemprop="name"><?=$arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["NAME"];?>:</span>
									<span class="value" itemprop="value"><?=$arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]?></span>
								</div>
							</div>
						<?endif;?>

						<?if($arResult["BRAND_ITEM"]){?>
							<div class="item_block col-<?=$col;?>">
								<div class="brand">
									<?if(!$arResult["BRAND_ITEM"]["IMAGE"]):?>
										<b class="block_title"><?=GetMessage("BRAND");?>:</b>
										<a href="<?=$arResult["BRAND_ITEM"]["DETAIL_PAGE_URL"]?>"><?=$arResult["BRAND_ITEM"]["NAME"]?></a>
									<?else:?>
										<a class="brand_picture" href="<?=$arResult["BRAND_ITEM"]["DETAIL_PAGE_URL"]?>">
											<img  src="<?=$arResult["BRAND_ITEM"]["IMAGE"]["src"]?>" alt="<?=$arResult["BRAND_ITEM"]["IMAGE"]["ALT"]?>" title="<?=$arResult["BRAND_ITEM"]["IMAGE"]["TITLE"]?>" />
										</a>
									<?endif;?>
								</div>
							</div>
						<?}?>
					</div>
					<?/*if(strlen($arResult["PREVIEW_TEXT"])):?>
						<div class="preview_text dotdot"><?=$arResult["PREVIEW_TEXT"]?></div>
						<?if(strlen($arResult["DETAIL_TEXT"])):?>
							<div class="more_block icons_fa color_link"><span><?=GetMessage('MORE_TEXT_BOTTOM');?></span></div>
						<?endif;?>
					<?endif;*/?>
				</div>
			<?}?>
			<div class="middle_info main_item_wrapper">
			<?$frame = $this->createFrame()->begin();?>
				<div class="prices_block">
					<div class="cost prices clearfix">
						<?if( count( $arResult["OFFERS"] ) > 0 ){?>
							<div class="with_matrix" style="display:none;">
								<div class="price price_value_block"><span class="values_wrapper"></span></div>
								<?if($arParams["SHOW_OLD_PRICE"]=="Y"):?>
									<div class="price discount"></div>
								<?endif;?>
								<?if($arParams["SHOW_DISCOUNT_PERCENT"]=="Y"){?>
									<div class="sale_block matrix" style="display:none;">
										<div class="sale_wrapper">
											<?if($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] != "Y"):?>
												<span class="title"><?=GetMessage("CATALOG_ECONOMY");?></span>
												<div class="text"><span class="values_wrapper"></span></div>
											<?else:?>
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
							<?\Aspro\Functions\CAsproSku::showItemPrices($arParams, $arResult, $item_id, $min_price_id, $arItemIDs, ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] == "Y" ? "N" : "Y"));?>
						<?}else{?>
							<?
							$item_id = $arResult["ID"];
							//print_r($arResult);
							if(isset($arResult['PRICE_MATRIX']) && $arResult['PRICE_MATRIX']) // USE_PRICE_COUNT
							{
								if($arResult['PRICE_MATRIX']['COLS'])
								{
									$arCurPriceType = current($arResult['PRICE_MATRIX']['COLS']);
									$arCurPrice = current($arResult['PRICE_MATRIX']['MATRIX'][$arCurPriceType['ID']]);
									$min_price_id = $arCurPriceType['ID'];?>
									<div class="" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
										<meta itemprop="price" content="<?=($arResult['MIN_PRICE']['DISCOUNT_VALUE'] ? $arResult['MIN_PRICE']['DISCOUNT_VALUE'] : $arResult['MIN_PRICE']['VALUE'])?>" />
										<meta itemprop="priceCurrency" content="<?=$arResult['MIN_PRICE']['CURRENCY']?>" />
										<link itemprop="availability" href="http://schema.org/<?=($arResult['PRICE_MATRIX']['AVAILABLE'] == 'Y' ? 'InStock' : 'OutOfStock')?>" />
									</div>
								<?}?>
								<?if($arResult['ITEM_PRICE_MODE'] == 'Q' && count($arResult['PRICE_MATRIX']['ROWS']) > 1):?>
									<?=CNext::showPriceRangeTop($arResult, $arParams, GetMessage("CATALOG_ECONOMY"));?>
								<?endif;?>
								<?=CNext::showPriceMatrix($arResult, $arParams, $strMeasure, $arAddToBasketData);?>
							<?
							}
							else
							{?>
								<?\Aspro\Functions\CAsproItem::showItemPrices($arParams, $arResult["PRICES"], $strMeasure, $min_price_id, ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] == "Y" ? "N" : "Y"));?>
							<?}?>
						<?}?>
					</div>
					<?if($arParams["SHOW_DISCOUNT_TIME"]=="Y"){?>
						<?$arUserGroups = $USER->GetUserGroupArray();?>
						<?if($arParams['SHOW_DISCOUNT_TIME_EACH_SKU'] != 'Y' || ($arParams['SHOW_DISCOUNT_TIME_EACH_SKU'] == 'Y' && (!$arResult['OFFERS'] || ($arResult['OFFERS'] && $arParams['TYPE_SKU'] != 'TYPE_1')))):?>
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
													<span class="value" <?=((count( $arResult["OFFERS"] ) > 0 && $arParams["TYPE_SKU"] == 'TYPE_1' && $arResult["OFFERS_PROP"]) ? 'style="opacity:0;"' : '')?>><?=$totalCount;?></span>
													<span class="text"><?=GetMessage("TITLE_QUANTITY");?></span>
												</span>
											</div>
										</div>
									<?endif;?>
								</div>
							<?}?>
						<?else:?>
							<?if($arResult['JS_OFFERS'])
							{

								foreach($arResult['JS_OFFERS'] as $keyOffer => $arTmpOffer2)
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
									$arResult['JS_OFFERS'][$keyOffer]['DISCOUNT_ACTIVE'] = $active_to;
								}
							}?>
							<div class="view_sale_block" style="display:none;">
								<div class="count_d_block">
										<span class="active_to_<?=$arResult["ID"]?> hidden"><?=$arDiscount["ACTIVE_TO"];?></span>
										<div class="title"><?=GetMessage("UNTIL_AKC");?></div>
										<span class="countdown countdown_<?=$arResult["ID"]?> values"></span>
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
					<div class="quantity_block_wrapper">
						<?if($useStores){?>
							<div class="p_block">
						<?}?>
							<?=$arQuantityData["HTML"];?>
						<?if($useStores){?>
							</div>
						<?}?>
						<?/*if($arParams["SHOW_CHEAPER_FORM"] == "Y"):?>
							<div class="cheaper_form">
								<span class="animate-load" data-event="jqm" data-param-form_id="CHEAPER" data-name="cheaper" data-autoload-product_name="<?=CNext::formatJsName($arResult["NAME"]);?>" data-autoload-product_id="<?=$arResult["ID"];?>"><?=($arParams["CHEAPER_FORM_NAME"] ? $arParams["CHEAPER_FORM_NAME"] : GetMessage("CHEAPER"));?></span>
							</div>
						<?endif;*/?>
					</div>
				</div>
				<div class="buy_block">
					<?if($arResult["OFFERS"] && $showCustomOffer){?>
						<div class="sku_props">
							<?if (!empty($arResult['OFFERS_PROP'])){?>
								<div class="bx_catalog_item_scu wrapper_sku" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PROP_DIV']; ?>">
									<?foreach ($arSkuTemplate as $code => $strTemplate){
										if (!isset($arResult['OFFERS_PROP'][$code]))
											continue;
										echo str_replace('#ITEM#_prop_', $arItemIDs["ALL_ITEM_IDS"]['PROP'], $strTemplate);
									}?>
								</div>
							<?}?>
							<?$arItemJSParams=CNext::GetSKUJSParams($arResult, $arParams, $arResult, "Y");?>
							<script type="text/javascript">
								var <? echo $arItemIDs["strObName"]; ?> = new JCCatalogElement(<? echo CUtil::PhpToJSObject($arItemJSParams, false, true); ?>);
							</script>
						</div>
					<?}?>
					<?if(!$arResult["OFFERS"]):?>
						<script>
							$(document).ready(function() {
								$('.catalog_detail input[data-sid="PRODUCT_NAME"]').attr('value', $('h1').text());
							});
						</script>
						<div class="counter_wrapp">
							<div id="<? echo $arItemIDs["ALL_ITEM_IDS"]['BASKET_ACTIONS']; ?>" class="button_block <?=(($arAddToBasketData["ACTION"] == "ORDER" /*&& !$arResult["CAN_BUY"]*/) || !$arAddToBasketData["CAN_BUY"] || !$arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] || ($arAddToBasketData["ACTION"] == "SUBSCRIBE" && $arResult["CATALOG_SUBSCRIBE"] == "Y")  ? "wide" : "");?>">
								<!--noindex-->
									<?=$arAddToBasketData["HTML"]?>
								<!--/noindex-->
							</div>
						</div>
						<?if(isset($arResult['PRICE_MATRIX']) && $arResult['PRICE_MATRIX']) // USE_PRICE_COUNT
						{?>
							<?if($arResult['ITEM_PRICE_MODE'] == 'Q' && count($arResult['PRICE_MATRIX']['ROWS']) > 1):?>
								<?$arOnlyItemJSParams = array(
									"ITEM_PRICES" => $arResult["ITEM_PRICES"],
									"ITEM_PRICE_MODE" => $arResult["ITEM_PRICE_MODE"],
									"ITEM_QUANTITY_RANGES" => $arResult["ITEM_QUANTITY_RANGES"],
									"MIN_QUANTITY_BUY" => $arAddToBasketData["MIN_QUANTITY_BUY"],
									"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
									"ID" => $arItemIDs["strMainID"],
								)?>
								<script type="text/javascript">
									var <? echo $arItemIDs["strObName"]; ?>el = new JCCatalogOnlyElement(<? echo CUtil::PhpToJSObject($arOnlyItemJSParams, false, true); ?>);
								</script>
							<?endif;?>
						<?}?>
						<?if($arAddToBasketData["ACTION"] !== "NOTHING"):?>
							<?if($arAddToBasketData["ACTION"] == "ADD" && $arAddToBasketData["CAN_BUY"] && $arParams["SHOW_ONE_CLICK_BUY"]!="N"):?>
								<div class="wrapp_one_click">
									<span class="btn btn-default white btn-lg type_block transition_bg one_click" data-item="<?=$arResult["ID"]?>" data-iblockID="<?=$arParams["IBLOCK_ID"]?>" data-quantity="<?=$arAddToBasketData["MIN_QUANTITY_BUY"];?>" onclick="oneClickBuy('<?=$arResult["ID"]?>', '<?=$arParams["IBLOCK_ID"]?>', this)">
										<span><?=GetMessage('ONE_CLICK_BUY')?></span>
									</span>
								</div>
							<?endif;?>
						<?endif;?>
					<?elseif($arResult["OFFERS"] && $arParams['TYPE_SKU'] == 'TYPE_1'):?>
						<div class="offer_buy_block buys_wrapp" style="display:none;">
							<div class="counter_wrapp"></div>
						</div>
					<?elseif($arResult["OFFERS"] && $arParams['TYPE_SKU'] != 'TYPE_1'):?>
						<span class="btn btn-default btn-lg slide_offer transition_bg type_block"><i></i><span><?=\Bitrix\Main\Config\Option::get("aspro.next", "EXPRESSION_READ_MORE_OFFERS_DEFAULT", GetMessage("MORE_TEXT_BOTTOM"));?></span></span>
					<?endif;?>
					<?if($arParams['SHOW_SEND_GIFT'] != 'N'):?>
						<?$sCurrentPage = (CMain::IsHTTPS()) ? "https://" : "http://";
						$sCurrentPage .= $_SERVER["HTTP_HOST"];
						$sCurrentPage .= $APPLICATION->GetCurPage();?>
						<div class="gift_form">
							<span><span class="animate-load" data-event="jqm" data-param-form_id="SEND_GIFT" data-name="send_gift" data-autoload-product_name="<?=CNext::formatJsName($arResult["NAME"]);?>" data-autoload-product_link="<?=$sCurrentPage;?>" data-autoload-product_id="<?=$arResult["ID"];?>"><?=($arParams["SEND_GIFT_FORM_NAME"] ? $arParams["SEND_GIFT_FORM_NAME"] : GetMessage("SEND_GIFT_FORM"));?></span></span>
						</div>
					<?endif;?>
				</div>
			<?$frame->end();?>
			</div>
			<?$iCountProps = count($arResult['VISIBLE_PROPS']);?>
			<?if($arResult['VISIBLE_PROPS']):?>
				<div class="info_ext_block">
					<div class="title opened"><?=($arParams["TAB_CHAR_NAME"] ? $arParams["TAB_CHAR_NAME"] : GetMessage("PROPERTIES_TAB"));?></div>
					<div class="text props_list line" style="display:block;">
						<?$j=0;?>
							<?foreach($arResult["VISIBLE_PROPS"] as $arProp):?>
								<?if($j<$arParams['VISIBLE_PROP_COUNT']):?>
									<div class="prop" <?if($iCountProps <= $arParams['VISIBLE_PROP_COUNT']):?> itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue" <?endif;?>>
										<div class="name">
											<div class="char_name">
												<?if($arProp["HINT"] && $arParams["SHOW_HINTS"]=="Y"):?><div class="hint"><span class="icon"><i>?</i></span><div class="tooltip"><?=$arProp["HINT"]?></div></div><?endif;?>
												<div class="props_item <?if($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"){?>whint<?}?>">
													<span <?if($iCountProps <= $arParams['VISIBLE_PROP_COUNT']):?> itemprop="name" <?endif;?>><?=$arProp["NAME"]?>:</span>
												</div>
											</div>
										</div>
										<div class="value">
											<div class="char_value" <?if($iCountProps <= $arParams['VISIBLE_PROP_COUNT']):?> itemprop="value" <?endif;?>>
												<?if(count($arProp["DISPLAY_VALUE"]) > 1):?>
													<?=implode(', ', $arProp["DISPLAY_VALUE"]);?>
												<?else:?>
													<?=$arProp["DISPLAY_VALUE"];?>
												<?endif;?>
											</div>
										</div>
									</div>
								<?endif;?>
								<?$j++;?>
							<?endforeach;?>
					</div>
				</div>
			<?endif;?>
			<?if($arParams['SHOW_GARANTY'] == 'Y'):?>
				<div class="info_ext_block">
					<div class="title"><?=$arParams['TITLE_GARANTY'];?></div>
					<div class="text">
						<?$APPLICATION->IncludeFile(SITE_DIR."include/element_detail_garanty.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('CT_BCE_CATALOG_DOP_GARANTY')));?>
					</div>
				</div>
			<?endif;?>
			<?if(is_array($arResult["STOCK"]) && $arResult["STOCK"]):?>
				<div class="stock_wrapper">
					<?foreach($arResult["STOCK"] as $key => $arStockItem):?>
						<div class="stock_board <?=($arStockItem["PREVIEW_TEXT"] ? '' : 'nt');?>">
							<div class="title"><a class="dark_link" href="<?=$arStockItem["DETAIL_PAGE_URL"]?>"><?=$arStockItem["NAME"];?></a></div>
							<div class="txt"><?=$arStockItem["PREVIEW_TEXT"]?></div>
						</div>
					<?endforeach;?>
				</div>
			<?endif;?>
			<div class="element_detail_text wrap_md">
				<div class="price_txt">
					<div class="sharing">
						<div class="">
							<?$APPLICATION->IncludeFile(SITE_DIR."include/share_buttons.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON')));?>
						</div>
					</div>
					<div class="text">
						<?$APPLICATION->IncludeFile(SITE_DIR."include/element_detail_text.php", Array(), Array("MODE" => "html",  "NAME" => GetMessage('CT_BCE_CATALOG_DOP_DESCR')));?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?$bPriceCount = ($arParams['USE_PRICE_COUNT'] == 'Y');?>
	<?if($arResult['OFFERS']):?>
		<span itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer" style="display:none;">
			<meta itemprop="offerCount" content="<?=count($arResult['OFFERS'])?>" />
			<meta itemprop="lowPrice" content="<?=($arResult['MIN_PRICE']['DISCOUNT_VALUE'] ? $arResult['MIN_PRICE']['DISCOUNT_VALUE'] : $arResult['MIN_PRICE']['VALUE'] )?>" />
			<meta itemprop="priceCurrency" content="<?=$arResult['MIN_PRICE']['CURRENCY']?>" />
			<?foreach($arResult['OFFERS'] as $arOffer):?>
				<?$currentOffersList = array();?>
				<?foreach($arOffer['TREE'] as $propName => $skuId):?>
					<?$propId = (int)substr($propName, 5);?>
					<?foreach($arResult['SKU_PROPS'] as $prop):?>
						<?if($prop['ID'] == $propId):?>
							<?foreach($prop['VALUES'] as $propId => $propValue):?>
								<?if($propId == $skuId):?>
									<?$currentOffersList[] = $propValue['NAME'];?>
									<?break;?>
								<?endif;?>
							<?endforeach;?>
						<?endif;?>
					<?endforeach;?>
				<?endforeach;?>
				<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
					<meta itemprop="sku" content="<?=implode('/', $currentOffersList)?>" />
					<a href="<?=$arOffer['DETAIL_PAGE_URL']?>" itemprop="url"></a>
					<meta itemprop="price" content="<?=($arOffer['MIN_PRICE']['DISCOUNT_VALUE']) ? $arOffer['MIN_PRICE']['DISCOUNT_VALUE'] : $arOffer['MIN_PRICE']['VALUE']?>" />
					<meta itemprop="priceCurrency" content="<?=$arOffer['MIN_PRICE']['CURRENCY']?>" />
					<link itemprop="availability" href="http://schema.org/<?=($arOffer['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
				</span>
			<?endforeach;?>
		</span>
		<?unset($arOffer, $currentOffersList);?>
	<?else:?>
		<?if(!$bPriceCount):?>
			<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="price" content="<?=($arResult['MIN_PRICE']['DISCOUNT_VALUE'] ? $arResult['MIN_PRICE']['DISCOUNT_VALUE'] : $arResult['MIN_PRICE']['VALUE'])?>" />
				<meta itemprop="priceCurrency" content="<?=$arResult['MIN_PRICE']['CURRENCY']?>" />
				<link itemprop="availability" href="http://schema.org/<?=($arResult['MIN_PRICE']['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
			</span>
		<?endif;?>
	<?endif;?>
	<div class="clearleft"></div>
	<?if($arResult["TIZERS_ITEMS"]){?>
		<div class="tizers_block_detail tizers_block">
			<div class="row">
				<?$count_t_items=count($arResult["TIZERS_ITEMS"]);?>
				<?foreach($arResult["TIZERS_ITEMS"] as $arItem){?>
					<div class="col-md-3 col-sm-3 col-xs-6">
						<div class="inner_wrapper item">
							<?if($arItem["UF_FILE"]){?>
								<div class="img">
									<?if($arItem["UF_LINK"]){?>
										<a href="<?=$arItem["UF_LINK"];?>" <?=(strpos($arItem["UF_LINK"], "http") !== false ? "target='_blank' rel='nofollow'" : '')?>>
									<?}?>
									<img src="<?=$arItem["PREVIEW_PICTURE"]["src"];?>" alt="<?=$arItem["UF_NAME"];?>" title="<?=$arItem["UF_NAME"];?>">
									<?if($arItem["UF_LINK"]){?>
										</a>
									<?}?>
								</div>
							<?}?>
							<div class="title">
								<?if($arItem["UF_LINK"]){?>
									<a href="<?=$arItem["UF_LINK"];?>" <?=(strpos($arItem["UF_LINK"], "http") !== false ? "target='_blank' rel='nofollow'" : '')?>>
								<?}?>
								<?=$arItem["UF_NAME"];?>
								<?if($arItem["UF_LINK"]){?>
									</a>
								<?}?>
							</div>
						</div>
					</div>
				<?}?>
			</div>
		</div>
	<?}?>

	<?if($arParams["SHOW_KIT_PARTS"] == "Y" && $arResult["SET_ITEMS"]):?>
		<div class="set_wrapp set_block">
			<div class="title"><?=GetMessage("GROUP_PARTS_TITLE")?></div>
			<ul>
				<?foreach($arResult["SET_ITEMS"] as $iii => $arSetItem):?>
					<li class="item">
						<div class="item_inner">
							<div class="image">
								<a href="<?=$arSetItem["DETAIL_PAGE_URL"]?>">
									<?if($arSetItem["PREVIEW_PICTURE"]):?>
										<?$img = CFile::ResizeImageGet($arSetItem["PREVIEW_PICTURE"], array("width" => 140, "height" => 140), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
										<img  src="<?=$img["src"]?>" alt="<?=$arSetItem["NAME"];?>" title="<?=$arSetItem["NAME"];?>" />
									<?elseif($arSetItem["DETAIL_PICTURE"]):?>
										<?$img = CFile::ResizeImageGet($arSetItem["DETAIL_PICTURE"], array("width" => 140, "height" => 140), BX_RESIZE_IMAGE_PROPORTIONAL, true);?>
										<img  src="<?=$img["src"]?>" alt="<?=$arSetItem["NAME"];?>" title="<?=$arSetItem["NAME"];?>" />
									<?else:?>
										<img  src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_small.png" alt="<?=$arSetItem["NAME"];?>" title="<?=$arSetItem["NAME"];?>" />
									<?endif;?>
								</a>
								<?if($arResult["SET_ITEMS_QUANTITY"]):?>
									<div class="quantity">x<?=$arSetItem["QUANTITY"];?></div>
								<?endif;?>
							</div>
							<div class="item_info">
								<div class="item-title">
									<a href="<?=$arSetItem["DETAIL_PAGE_URL"]?>"><span><?=$arSetItem["NAME"]?></span></a>
								</div>
								<?if($arParams["SHOW_KIT_PARTS_PRICES"] == "Y"):?>
									<div class="cost prices clearfix">
										<?
										$arCountPricesCanAccess = 0;
										foreach($arSetItem["PRICES"] as $key => $arPrice){
											if($arPrice["CAN_ACCESS"]){
												$arCountPricesCanAccess++;
											}
										}?>
										<?foreach($arSetItem["PRICES"] as $key => $arPrice):?>
											<?if($arPrice["CAN_ACCESS"]):?>
												<?$price = CPrice::GetByID($arPrice["ID"]);?>
												<?if($arCountPricesCanAccess > 1):?>
													<div class="price_name"><?=$price["CATALOG_GROUP_NAME"];?></div>
												<?endif;?>
												<?if($arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"]  && $arParams["SHOW_OLD_PRICE"]=="Y"):?>
													<div class="price">
														<?=$arPrice["PRINT_DISCOUNT_VALUE"];?><?if(($arParams["SHOW_MEASURE"] == "Y") && $strMeasure):?><small>/<?=$strMeasure?></small><?endif;?>
													</div>
													<div class="price discount">
														<span><?=$arPrice["PRINT_VALUE"]?></span>
													</div>
												<?else:?>
													<div class="price">
														<?=$arPrice["PRINT_VALUE"];?><?if(($arParams["SHOW_MEASURE"] == "Y") && $strMeasure):?><small>/<?=$strMeasure?></small><?endif;?>
													</div>
												<?endif;?>
											<?endif;?>
										<?endforeach;?>
									</div>
								<?endif;?>
							</div>
						</div>
					</li>
					<?if($arResult["SET_ITEMS"][$iii + 1]):?>
						<li class="separator"></li>
					<?endif;?>
				<?endforeach;?>
			</ul>
		</div>
	<?endif;?>
	<?if($arResult['OFFERS']):?>
		<?if($arResult['OFFER_GROUP']):?>
			<?foreach($arResult['OFFERS'] as $arOffer):?>
				<?if(!$arOffer['OFFER_GROUP']) continue;?>
				<span id="<?=$arItemIDs['ALL_ITEM_IDS']['OFFER_GROUP'].$arOffer['ID']?>" style="display: none;">
					<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "",
						array(
							"IBLOCK_ID" => $arResult["OFFERS_IBLOCK"],
							"ELEMENT_ID" => $arOffer['ID'],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"BUNDLE_ITEMS_COUNT" => $arParams["BUNDLE_ITEMS_COUNT"],
							"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
							"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
							"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
							"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
							"CURRENCY_ID" => $arParams["CURRENCY_ID"]
						), $component, array("HIDE_ICONS" => "Y")
					);?>
				</span>
			<?endforeach;?>
		<?endif;?>
	<?else:?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", "",
			array(
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"ELEMENT_ID" => $arResult["ID"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"BUNDLE_ITEMS_COUNT" => $arParams["BUNDLE_ITEMS_COUNT"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
				"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
				"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
				"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"]
			), $component, array("HIDE_ICONS" => "Y")
		);?>
	<?endif;?>
</div>
<div class="tabs_section">
	<?if($arResult["OFFERS"] && $arParams["TYPE_SKU"]=="N"):?>
		<h4><?=($arParams["TAB_OFFERS_NAME"] ? $arParams["TAB_OFFERS_NAME"] : GetMessage("OFFER_PRICES"));?></h4>
		<?
		$showSkUName = ((in_array('NAME', $arParams['OFFERS_FIELD_CODE'])));
		$showSkUImages = false;
		if(((in_array('PREVIEW_PICTURE', $arParams['OFFERS_FIELD_CODE']) || in_array('DETAIL_PICTURE', $arParams['OFFERS_FIELD_CODE'])))){
			foreach ($arResult["OFFERS"] as $key => $arSKU){
				if($arSKU['PREVIEW_PICTURE'] || $arSKU['DETAIL_PICTURE']){
					$showSkUImages = true;
					break;
				}
			}
		}?>
		<?if($arResult["OFFERS"] && $arParams["TYPE_SKU"] !== "TYPE_1"):?>
			<script>
				$(document).ready(function() {
					$('.catalog_detail .tabs_section .tabs_content .form.inline input[data-sid="PRODUCT_NAME"]').attr('value', $('h1').text());
				});
			</script>
		<?endif;?>
		<?if($arResult["OFFERS"] && $arParams["TYPE_SKU"] !== "TYPE_1"):?>
			<div class="prices_tab">
				<div class="bx_sku_props" style="display:none;">
					<?$arSkuKeysProp='';
					$propSKU=$arParams["OFFERS_CART_PROPERTIES"];
					if($propSKU){
						$arSkuKeysProp=base64_encode(serialize(array_keys($propSKU)));
					}?>
					<input type="hidden" value="<?=$arSkuKeysProp;?>"></input>
				</div>
				<table class="offers_table">
					<thead>
						<tr>
							<?if($useStores):?>
								<td class="str"></td>
							<?endif;?>
							<?if($showSkUImages):?>
								<td class="property img" width="50"></td>
							<?endif;?>
							<?if($showSkUName):?>
								<td class="property names"><?=GetMessage("CATALOG_NAME")?></td>
							<?endif;?>
							<?if($arResult["SKU_PROPERTIES"]){
								foreach ($arResult["SKU_PROPERTIES"] as $key => $arProp){?>
									<?if(!$arProp["IS_EMPTY"]):?>
										<td class="property">
											<div class="props_item char_name <?if($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"){?>whint<?}?>">
												<?if($arProp["HINT"] && $arParams["SHOW_HINTS"]=="Y"):?><div class="hint"><span class="icon"><i>?</i></span><div class="tooltip"><?=$arProp["HINT"]?></div></div><?endif;?>
												<span><?=$arProp["NAME"]?></span>
											</div>
										</td>
									<?endif;?>
								<?}
							}?>
							<td class="price_th"><?=GetMessage("CATALOG_PRICE")?></td>
							<?if($arQuantityData["RIGHTS"]["SHOW_QUANTITY"]):?>
								<td class="count_th"><?=GetMessage("AVAILABLE")?></td>
							<?endif;?>
							<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N"  || $arParams["DISPLAY_COMPARE"] == "Y"):?>
								<td class="like_icons_th"></td>
							<?endif;?>
							<td colspan="3"></td>
						</tr>
					</thead>
					<tbody>
						<?$numProps = count($arResult["SKU_PROPERTIES"]);
						if($arResult["OFFERS"]){
							foreach ($arResult["OFFERS"] as $key => $arSKU){?>
								<?
								if($arResult["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"]){
									$sMeasure = $arResult["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"].".";
								}
								else{
									$sMeasure = GetMessage("MEASURE_DEFAULT").".";
								}
								$skutotalCount = CNext::GetTotalCount($arSKU, $arParams);
								$arskuQuantityData = CNext::GetQuantityArray($skutotalCount, array('quantity-wrapp', 'quantity-indicators'));
								$arSKU["IBLOCK_ID"]=$arResult["IBLOCK_ID"];
								$arSKU["IS_OFFER"]="Y";
								$arskuAddToBasketData = CNext::GetAddToBasketArray($arSKU, $skutotalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, array(), 'small w_icons', $arParams);
								$arskuAddToBasketData["HTML"] = str_replace('data-item', 'data-props="'.$arOfferProps.'" data-item', $arskuAddToBasketData["HTML"]);
								?>
								<?$collspan = 1;?>
								<tr class="main_item_wrapper" id="<?=$this->GetEditAreaId($arSKU["ID"]);?>">
									<?if($useStores):?>
										<td class="opener top">
											<?$collspan++;?>
											<span class="opener_icon"><i></i></span>
										</td>
									<?endif;?>
									<?if($showSkUImages):?>
										<?$collspan++;?>
										<td class="property">
											<?
											$srcImgPreview = $srcImgDetail = false;
											$imgPreviewID = ($arResult['OFFERS'][$key]['PREVIEW_PICTURE'] ? (is_array($arResult['OFFERS'][$key]['PREVIEW_PICTURE']) ? $arResult['OFFERS'][$key]['PREVIEW_PICTURE']['ID'] : $arResult['OFFERS'][$key]['PREVIEW_PICTURE']) : false);
											$imgDetailID = ($arResult['OFFERS'][$key]['DETAIL_PICTURE'] ? (is_array($arResult['OFFERS'][$key]['DETAIL_PICTURE']) ? $arResult['OFFERS'][$key]['DETAIL_PICTURE']['ID'] : $arResult['OFFERS'][$key]['DETAIL_PICTURE']) : false);
											if($imgPreviewID || $imgDetailID){
												$arImgPreview = CFile::ResizeImageGet($imgPreviewID ? $imgPreviewID : $imgDetailID, array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
												$srcImgPreview = $arImgPreview['src'];
											}
											if($imgDetailID){
												$srcImgDetail = CFile::GetPath($imgDetailID);
											}
											?>
											<?if($srcImgPreview || $srcImgDetail):?>
												<a href="<?=($srcImgDetail ? $srcImgDetail : $srcImgPreview)?>" class="fancy" data-fancybox-group="item_slider"><img src="<?=$srcImgPreview?>" alt="<?=$arSKU['NAME']?>" /></a>
											<?endif;?>
										</td>
									<?endif;?>
									<?if($showSkUName):?>
										<?$collspan++;?>
										<td class="property names"><?=$arSKU['NAME']?></td>
									<?endif;?>
									<?foreach( $arResult["SKU_PROPERTIES"] as $arProp ){?>
										<?if(!$arProp["IS_EMPTY"]):?>
											<?$collspan++;?>
											<td class="property">
												<?if($arResult["TMP_OFFERS_PROP"][$arProp["CODE"]]){
													echo $arResult["TMP_OFFERS_PROP"][$arProp["CODE"]]["VALUES"][$arSKU["TREE"]["PROP_".$arProp["ID"]]]["NAME"];?>
												<?}else{
													if (is_array($arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"])){
														echo implode("/", $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"]);
													}else{
														if($arSKU["PROPERTIES"][$arProp["CODE"]]["USER_TYPE"]=="directory" && isset($arSKU["PROPERTIES"][$arProp["CODE"]]["USER_TYPE_SETTINGS"]["TABLE_NAME"])){
															$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('=TABLE_NAME'=>$arSKU["PROPERTIES"][$arProp["CODE"]]["USER_TYPE_SETTINGS"]["TABLE_NAME"])));
													        if ($arData = $rsData->fetch()){
													            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
													            $entityDataClass = $entity->getDataClass();
													            $arFilter = array(
													                'limit' => 1,
													                'filter' => array(
													                    '=UF_XML_ID' => $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"]
													                )
													            );
													            $arValue = $entityDataClass::getList($arFilter)->fetch();
													            if(isset($arValue["UF_NAME"]) && $arValue["UF_NAME"]){
													            	echo $arValue["UF_NAME"];
													            }else{
													            	echo $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"];
													            }
													        }
														}else{
															echo $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"];
														}
													}
												}?>
											</td>
										<?endif;?>
									<?}?>
									<td class="price">
										<div class="cost prices clearfix">
											<?
											$collspan++;
											$arCountPricesCanAccess = 0;
											if(isset($arSKU['PRICE_MATRIX']) && $arSKU['PRICE_MATRIX'] && count($arSKU['PRICE_MATRIX']['ROWS']) > 1) // USE_PRICE_COUNT
											{?>
												<?=CNext::showPriceRangeTop($arSKU, $arParams, GetMessage("CATALOG_ECONOMY"));?>
												<?echo CNext::showPriceMatrix($arSKU, $arParams, $arSKU["CATALOG_MEASURE_NAME"]);
											}
											else
											{
												?>
												<?\Aspro\Functions\CAsproItem::showItemPrices($arParams, $arSKU["PRICES"], $arSKU["CATALOG_MEASURE_NAME"], $min_price_id, ($arParams["SHOW_DISCOUNT_PERCENT_NUMBER"] == "Y" ? "N" : "Y"));?>
											<?}?>
										</div>
									</td>
									<?if(strlen($arskuQuantityData["TEXT"])):?>
										<?$collspan++;?>
										<td class="count">
											<?=$arskuQuantityData["HTML"]?>
										</td>
									<?endif;?>
									<!--noindex-->
										<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N"  || $arParams["DISPLAY_COMPARE"] == "Y"):?>
											<td class="like_icons">
												<?$collspan++;?>
												<?if($arParams["DISPLAY_WISH_BUTTONS"] != "N"):?>
													<?if($arskuAddToBasketData['CAN_BUY']):?>
														<div class="wish_item_button o_<?=$arSKU["ID"];?>">
															<span title="<?=GetMessage('CATALOG_WISH')?>" class="wish_item text to <?=$arParams["TYPE_SKU"];?>" data-item="<?=$arSKU["ID"]?>" data-iblock="<?=$arResult["IBLOCK_ID"]?>" data-offers="Y" data-props="<?=$arOfferProps?>"><i></i></span>
															<span title="<?=GetMessage('CATALOG_WISH_OUT')?>" class="wish_item text in added <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-item="<?=$arSKU["ID"]?>" data-iblock="<?=$arSKU["IBLOCK_ID"]?>"><i></i></span>
														</div>
													<?endif;?>
												<?endif;?>
												<?if($arParams["DISPLAY_COMPARE"] == "Y"):?>
													<div class="compare_item_button o_<?=$arSKU["ID"];?>">
														<span title="<?=GetMessage('CATALOG_COMPARE')?>" class="compare_item to text <?=$arParams["TYPE_SKU"];?>" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arSKU["ID"]?>" ><i></i></span>
														<span title="<?=GetMessage('CATALOG_COMPARE_OUT')?>" class="compare_item in added text <?=$arParams["TYPE_SKU"];?>" style="display: none;" data-iblock="<?=$arParams["IBLOCK_ID"]?>" data-item="<?=$arSKU["ID"]?>"><i></i></span>
													</div>
												<?endif;?>
											</td>
										<?endif;?>
										<?if($arskuAddToBasketData["ACTION"] == "ADD"):?>
											<?if($arskuAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] && !count($arSKU["OFFERS"]) && $arskuAddToBasketData["ACTION"] == "ADD" && $arskuAddToBasketData["CAN_BUY"]):?>
												<td class="counter_wrapp counter_block_wr">
													<div class="counter_block" data-item="<?=$arSKU["ID"];?>">
														<?$collspan++;?>
														<span class="minus">-</span>
														<input type="text" class="text" name="quantity" value="<?=$arskuAddToBasketData["MIN_QUANTITY_BUY"];?>" />
														<span class="plus">+</span>
													</div>
												</td>
											<?endif;?>
										<?endif;?>
										<?if(isset($arSKU['PRICE_MATRIX']) && $arSKU['PRICE_MATRIX'] && count($arSKU['PRICE_MATRIX']['ROWS']) > 1) // USE_PRICE_COUNT
										{?>
											<?$arOnlyItemJSParams = array(
												"ITEM_PRICES" => $arSKU["ITEM_PRICES"],
												"ITEM_PRICE_MODE" => $arSKU["ITEM_PRICE_MODE"],
												"ITEM_QUANTITY_RANGES" => $arSKU["ITEM_QUANTITY_RANGES"],
												"MIN_QUANTITY_BUY" => $arskuAddToBasketData["MIN_QUANTITY_BUY"],
												"SHOW_DISCOUNT_PERCENT_NUMBER" => $arParams["SHOW_DISCOUNT_PERCENT_NUMBER"],
												"ID" => $this->GetEditAreaId($arSKU["ID"]),
											)?>
											<script type="text/javascript">
												var ob<? echo $this->GetEditAreaId($arSKU["ID"]); ?>el = new JCCatalogOnlyElement(<? echo CUtil::PhpToJSObject($arOnlyItemJSParams, false, true); ?>);
											</script>
										<?}?>
										<td class="buy" <?=($arskuAddToBasketData["ACTION"] !== "ADD" || !$arskuAddToBasketData["CAN_BUY"] || $arParams["SHOW_ONE_CLICK_BUY"]=="N" ? 'colspan="3"' : "")?>>
											<?if($arskuAddToBasketData["ACTION"] !== "ADD"  || !$arskuAddToBasketData["CAN_BUY"]):?>
												<?$collspan += 3;?>
											<?else:?>
												<?$collspan++;?>
											<?endif;?>
											<div class="counter_wrapp">
												<?=$arskuAddToBasketData["HTML"]?>
											</div>
										</td>
										<?if($arskuAddToBasketData["ACTION"] == "ADD" && $arskuAddToBasketData["CAN_BUY"] && $arParams["SHOW_ONE_CLICK_BUY"]!="N"):?>
											<td class="one_click_buy">
												<?$collspan++;?>
												<span class="btn btn-default white one_click" data-item="<?=$arSKU["ID"]?>" data-offers="Y" data-iblockID="<?=$arParams["IBLOCK_ID"]?>" data-quantity="<?=$arskuAddToBasketData["MIN_QUANTITY_BUY"];?>" data-props="<?=$arOfferProps?>" onclick="oneClickBuy('<?=$arSKU["ID"]?>', '<?=$arParams["IBLOCK_ID"]?>', this)">
													<span><?=GetMessage('ONE_CLICK_BUY')?></span>
												</span>
											</td>
										<?endif;?>
									<!--/noindex-->
									<?if($useStores):?>
										<td class="opener bottom">
											<?$collspan++;?>
											<span class="opener_icon"><i></i></span>
										</td>
									<?endif;?>
								</tr>
								<?if($useStores):?>
									<?$collspan--;?>
									<tr class="offer_stores"><td colspan="<?=$collspan?>">
										<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", "main", array(
												"PER_PAGE" => "10",
												"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
												"SCHEDULE" => $arParams["SCHEDULE"],
												"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
												"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
												"ELEMENT_ID" => $arSKU["ID"],
												"STORE_PATH"  =>  $arParams["STORE_PATH"],
												"MAIN_TITLE"  =>  $arParams["MAIN_TITLE"],
												"MAX_AMOUNT"=>$arParams["MAX_AMOUNT"],
												"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
												"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
												"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
												"USER_FIELDS" => $arParams['USER_FIELDS'],
												"FIELDS" => $arParams['FIELDS'],
												"STORES" => $arParams['STORES'],
												"CACHE_TYPE" => "A",
												"SET_ITEMS" => $arResult["SET_ITEMS"],
											),
											$component
										);?>
									</tr>
								<?endif;?>
							<?}
						}?>
					</tbody>
				</table>
			</div>
		<?endif;?>
	<?endif;?>
</div>

<?
	$showProps = false;
	if($arResult["DISPLAY_PROPERTIES"]){
		foreach($arResult["DISPLAY_PROPERTIES"] as $arProp){
			if(!in_array($arProp["CODE"], array("SERVICES", "BRAND", "HIT", "RECOMMEND", "NEW", "STOCK", "VIDEO", "VIDEO_YOUTUBE", "CML2_ARTICLE", "POPUP_VIDEO"))){
				if(!is_array($arProp["DISPLAY_VALUE"])){
					$arProp["DISPLAY_VALUE"] = array($arProp["DISPLAY_VALUE"]);
				}
			}
			if(is_array($arProp["DISPLAY_VALUE"])){
				foreach($arProp["DISPLAY_VALUE"] as $value){
					if(strlen($value)){
						$showProps = true;
						break 2;
					}
				}
			}
		}
	}
	if(!$showProps && $arResult['OFFERS']){
		foreach($arResult['OFFERS'] as $arOffer){
			foreach($arOffer['DISPLAY_PROPERTIES'] as $arProp){
				if(!$arResult["TMP_OFFERS_PROP"][$arProp['CODE']])
				{
					if(!is_array($arProp["DISPLAY_VALUE"]))
						$arProp["DISPLAY_VALUE"] = array($arProp["DISPLAY_VALUE"]);


					foreach($arProp["DISPLAY_VALUE"] as $value){
						if(strlen($value)){
							$showProps = true;
							break 3;
						}
					}
				}
			}
		}
	}
	if($showProps && $iCountProps <= $arParams['VISIBLE_PROP_COUNT'])
	{
		$showProps = false;
	}

	$arVideo = array();
	if(strlen($arResult["DISPLAY_PROPERTIES"]["VIDEO"]["VALUE"])){
		$arVideo[] = $arResult["DISPLAY_PROPERTIES"]["VIDEO"]["~VALUE"];
	}
	if(isset($arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"])){
		if(is_array($arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"])){
			$arVideo = $arVideo + $arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["~VALUE"];
		}
		elseif(strlen($arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"])){
			$arVideo[] = $arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["~VALUE"];
		}
	}
	if(strlen($arResult["SECTION_FULL"]["UF_VIDEO"])){
		$arVideo[] = $arResult["SECTION_FULL"]["~UF_VIDEO"];
	}
	if(strlen($arResult["SECTION_FULL"]["UF_VIDEO_YOUTUBE"])){
		$arVideo[] = $arResult["SECTION_FULL"]["~UF_VIDEO_YOUTUBE"];
	}
	?>
	<?$instr_prop = ($arParams["DETAIL_DOCS_PROP"] ? $arParams["DETAIL_DOCS_PROP"] : "INSTRUCTIONS");?>

	<?if($templateData['BRAND_ITEM'] || (\Bitrix\Main\ModuleManager::isModuleInstalled("sale") && (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N'))):?>
		<div class="row <?=($arResult["DETAIL_TEXT"] || $showProps ? 'wdesc' : '');?>">
			<div class="col-md-9">
	<?endif;?>
	<div class="tabs_section type_more">
		<?$strGrupperType = $arParams["GRUPPER_PROPS"];?>
			<?if($arResult["DETAIL_TEXT"] || $showProps):
				$class = (($arResult["DETAIL_TEXT"] && $showProps) ? 6 : 12);?>
				<div class="row desc_tab">
					<?if($arResult["DETAIL_TEXT"]):?>
						<div class="col-md-<?=$class;?>">
							<?=$arResult["DETAIL_TEXT"];?>
						</div>
					<?endif;?>
					<?if($showProps):?>
						<div class="col-md-<?=$class;?>">
							<?if(!$arResult["DETAIL_TEXT"]):?>
								<h4><?=($arParams["TAB_CHAR_NAME"] ? $arParams["TAB_CHAR_NAME"] : GetMessage("PROPERTIES_TAB"));?></h4>
							<?endif;?>
							<?if($strGrupperType == "GRUPPER"):?>
								<div class="char_block line">
									<?$APPLICATION->IncludeComponent(
										"redsign:grupper.list",
										"",
										Array(
											"CACHE_TIME" => "3600000",
											"CACHE_TYPE" => "A",
											"COMPOSITE_FRAME_MODE" => "A",
											"COMPOSITE_FRAME_TYPE" => "AUTO",
											"DISPLAY_PROPERTIES" => $arResult["GROUPS_PROPS"]
										),
										$component, array('HIDE_ICONS'=>'Y')
									);?>
									<table class="props_list line" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_DIV']; ?>"></table>
								</div>
							<?elseif($strGrupperType == "WEBDEBUG"):?>
								<div class="char_block line">
									<?$APPLICATION->IncludeComponent(
										"webdebug:propsorter",
										"linear",
										array(
											"IBLOCK_TYPE" => $arResult['IBLOCK_TYPE'],
											"IBLOCK_ID" => $arResult['IBLOCK_ID'],
											"PROPERTIES" => $arResult['GROUPS_PROPS'],
											"EXCLUDE_PROPERTIES" => array(),
											"WARNING_IF_EMPTY" => "N",
											"WARNING_IF_EMPTY_TEXT" => "",
											"NOGROUP_SHOW" => "Y",
											"NOGROUP_NAME" => "",
											"MULTIPLE_SEPARATOR" => ", "
										),
										$component, array('HIDE_ICONS'=>'Y')
									);?>
									<table class="props_list line" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_DIV']; ?>"></table>
								</div>
							<?elseif($strGrupperType == "YENISITE_GRUPPER"):?>
								<div class="char_block line">
									<?$APPLICATION->IncludeComponent(
										'yenisite:ipep.props_groups',
										'',
										array(
											'DISPLAY_PROPERTIES' => $arResult['GROUPS_PROPS'],
											'IBLOCK_ID' => $arParams['IBLOCK_ID']
										),
										$component, array('HIDE_ICONS'=>'Y')
									)?>
									<table class="props_list colored_char" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_DIV']; ?>"></table>
								</div>
							<?else:?>
								<?if($arParams["PROPERTIES_DISPLAY_TYPE"] != "TABLE"):?>
									<div class="props_block" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_DIV']; ?>">
										<?foreach($arResult["PROPERTIES"] as $propCode => $arProp):?>
											<?if(isset($arResult["DISPLAY_PROPERTIES"][$propCode])):?>
												<?$arProp = $arResult["DISPLAY_PROPERTIES"][$propCode];?>
												<?if(!in_array($arProp["CODE"], array("SERVICES", "BRAND", "HIT", "RECOMMEND", "NEW", "STOCK", "VIDEO", "VIDEO_YOUTUBE", "CML2_ARTICLE", "POPUP_VIDEO"))):?>
													<?if((!is_array($arProp["DISPLAY_VALUE"]) && strlen($arProp["DISPLAY_VALUE"])) || (is_array($arProp["DISPLAY_VALUE"]) && implode('', $arProp["DISPLAY_VALUE"]))):?>
														<div class="char" itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
															<div class="char_name">
																<?if($arProp["HINT"] && $arParams["SHOW_HINTS"]=="Y"):?><div class="hint"><span class="icon"><i>?</i></span><div class="tooltip"><?=$arProp["HINT"]?></div></div><?endif;?>
																<div class="props_item <?if($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"){?>whint<?}?>">
																	<span itemprop="name"><?=$arProp["NAME"]?></span>
																</div>
															</div>
															<div class="char_value" itemprop="value">
																<?if(count($arProp["DISPLAY_VALUE"]) > 1):?>
																	<?=implode(', ', $arProp["DISPLAY_VALUE"]);?>
																<?else:?>
																	<?=$arProp["DISPLAY_VALUE"];?>
																<?endif;?>
															</div>
														</div>
													<?endif;?>
												<?endif;?>
											<?endif;?>
										<?endforeach;?>
									</div>
								<?else:?>
									<div class="char_block">
										<table class="props_list line">
											<?foreach($arResult["DISPLAY_PROPERTIES"] as $arProp):?>
												<?if(!in_array($arProp["CODE"], array("SERVICES", "BRAND", "HIT", "RECOMMEND", "NEW", "STOCK", "VIDEO", "VIDEO_YOUTUBE", "CML2_ARTICLE", "POPUP_VIDEO"))):?>
													<?if((!is_array($arProp["DISPLAY_VALUE"]) && strlen($arProp["DISPLAY_VALUE"])) || (is_array($arProp["DISPLAY_VALUE"]) && implode('', $arProp["DISPLAY_VALUE"]))):?>
														<tr itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
															<td class="char_name">
																<?if($arProp["HINT"] && $arParams["SHOW_HINTS"]=="Y"):?><div class="hint"><span class="icon"><i>?</i></span><div class="tooltip"><?=$arProp["HINT"]?></div></div><?endif;?>
																<div class="props_item <?if($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"){?>whint<?}?>">
																	<span itemprop="name"><?=$arProp["NAME"]?>:</span>
																</div>
															</td>
															<td class="char_value">
																<span itemprop="value">
																	<?if(count($arProp["DISPLAY_VALUE"]) > 1):?>
																		<?=implode(', ', $arProp["DISPLAY_VALUE"]);?>
																	<?else:?>
																		<?=$arProp["DISPLAY_VALUE"];?>
																	<?endif;?>
																</span>
															</td>
														</tr>
													<?endif;?>
												<?endif;?>
											<?endforeach;?>
										</table>
										<table class="props_list line" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_DIV']; ?>"></table>
									</div>
								<?endif;?>
							<?endif;?>
						</div>
					<?endif;?>
				</div>
			<?endif;?>
	<?if($arResult['ADDITIONAL_GALLERY']):?>
		<div class="wraps galerys-block with-padding<?=($arResult['OFFERS'] && 'TYPE_1' === $arParams['TYPE_SKU'] ? ' hidden' : '')?>">
			<hr>
			<h4><?=($arParams["BLOCK_ADDITIONAL_GALLERY_NAME"] ? $arParams["BLOCK_ADDITIONAL_GALLERY_NAME"] : GetMessage("ADDITIONAL_GALLERY_TITLE"))?></h4>
			<?if($arParams['ADDITIONAL_GALLERY_TYPE'] === 'SMALL'):?>
				<div class="small-gallery-block">
					<div class="flexslider unstyled front border small_slider custom_flex top_right color-controls" data-plugin-options='{"animation": "slide", "useCSS": true, "directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, "counts": [4, 3, 2, 1]}'>
						<ul class="slides items">
							<?if(!$arResult['OFFERS'] || 'TYPE_1' !== $arParams['TYPE_SKU']):?>
								<?foreach($arResult['ADDITIONAL_GALLERY'] as $i => $arPhoto):?>
									<li class="col-md-3 item visible">
										<div>
											<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
										</div>
										<a href="<?=$arPhoto['DETAIL']['SRC']?>" class="fancy dark_block_animate" rel="gallery" target="_blank" title="<?=$arPhoto['TITLE']?>"></a>
									</li>
								<?endforeach;?>
							<?endif;?>
						</ul>
					</div>
				</div>
			<?else:?>
				<div class="gallery-block">
					<div class="gallery-wrapper">
						<div class="inner">
							<?if(count($arResult['ADDITIONAL_GALLERY']) > 1 || ($arResult['OFFERS'] && 'TYPE_1' === $arParams['TYPE_SKU'])):?>
								<div class="small-gallery-wrapper">
									<div class="flexslider unstyled small-gallery center-nav ethumbs" data-plugin-options='{"slideshow": false, "useCSS": true, "animation": "slide", "animationLoop": true, "itemWidth": 60, "itemMargin": 20, "minItems": 1, "maxItems": 9, "slide_counts": 1, "asNavFor": ".gallery-wrapper .bigs"}' id="carousel1">
										<ul class="slides items">
											<?if(!$arResult['OFFERS'] || 'TYPE_1' !== $arParams['TYPE_SKU']):?>
												<?foreach($arResult['ADDITIONAL_GALLERY'] as $arPhoto):?>
													<li class="item">
														<img class="img-responsive inline" border="0" src="<?=$arPhoto['THUMB']['src']?>" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
													</li>
												<?endforeach;?>
											<?endif;?>
										</ul>
									</div>
								</div>
							<?endif;?>
							<div class="flexslider big_slider dark bigs color-controls" id="slider" data-plugin-options='{"animation": "slide", "useCSS": true, "directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, "sync": "#carousel1"}'>
								<ul class="slides items">
									<?if(!$arResult['OFFERS'] || 'TYPE_1' !== $arParams['TYPE_SKU']):?>
										<?foreach($arResult['ADDITIONAL_GALLERY'] as $i => $arPhoto):?>
											<li class="col-md-12 item">
												<a href="<?=$arPhoto['DETAIL']['SRC']?>" class="fancy" rel="gallery" target="_blank" title="<?=$arPhoto['TITLE']?>">
													<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
													<span class="zoom"></span>
												</a>
											</li>
										<?endforeach;?>
									<?endif;?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			<?endif;?>
		</div>
	<?endif;?>
	<?if($arVideo):?>
		<div class="wraps hidden_print">
			<hr>
			<h4>
				<?=($arParams["TAB_VIDEO_NAME"] ? $arParams["TAB_VIDEO_NAME"] : GetMessage("VIDEO_TAB"));?>
				<?if(count($arVideo) > 1):?>
					<span class="count empty">&nbsp;(<?=count($arVideo)?>)</span>
				<?endif;?>
			</h4>
			<div class="video_block">
				<?if(count($arVideo) > 1):?>
					<table class="video_table">
						<tbody>
							<?foreach($arVideo as $v => $value):?>
								<?if(($v + 1) % 2):?>
									<tr>
								<?endif;?>
								<td width="50%"><?=str_replace('src=', 'width="458" height="257" src=', str_replace(array('width', 'height'), array('data-width', 'data-height'), $value));?></td>
								<?if(!(($v + 1) % 2)):?>
									</tr>
								<?endif;?>
							<?endforeach;?>
							<?if(($v + 1) % 2):?>
								</tr>
							<?endif;?>
						</tbody>
					</table>
				<?else:?>
					<?=$arVideo[0]?>
				<?endif;?>
			</div>
		</div>
	<?endif;?>
	<?if($useStores && ($showCustomOffer || !$arResult["OFFERS"] )):?>
		<div class="wraps stores_wrapper">
			<hr>
			<h4><?=($arParams["TAB_STOCK_NAME"] ? $arParams["TAB_STOCK_NAME"] : GetMessage("STORES_TAB"));?></h4>
			<div class="stores_tab" id="stores">
				<?if($arResult["OFFERS"]){?>
					<span></span>
				<?}else{?>
					<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", "main", array(
							"PER_PAGE" => "10",
							"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
							"SCHEDULE" => $arParams["SCHEDULE"],
							"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
							"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
							"ELEMENT_ID" => $arResult["ID"],
							"STORE_PATH"  =>  $arParams["STORE_PATH"],
							"MAIN_TITLE"  =>  $arParams["MAIN_TITLE"],
							"MAX_AMOUNT"=>$arParams["MAX_AMOUNT"],
							"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
							"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
							"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
							"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
							"USER_FIELDS" => $arParams['USER_FIELDS'],
							"FIELDS" => $arParams['FIELDS'],
							"STORES" => $arParams['STORES'],
							"SET_ITEMS" => $arResult["SET_ITEMS"],
						),
						$component
					);?>
				<?}?>
			</div>
		</div>
	<?endif;?>
	<?if($arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"] || ($arTmpExp && $arTmpExp["CHILDREN"])):?>
		<div class="wraps hidden_print addon_type">
			<hr>
			<h4><?=($arParams["DETAIL_EXPANDABLES_TITLE"] ? $arParams["DETAIL_EXPANDABLES_TITLE"] : GetMessage("DETAIL_EXPANDABLES_TITLE"))?></h4>
			<div class="bottom_slider specials tab_slider_wrapp custom_type">
			<ul class="slider_navigation top custom_flex border">
				<li class="tabs_slider_navigation access_nav cur" data-code="access"></li>
			</ul>

			<ul class="tabs_content">
				<li class="tab access_wrapp" data-code="access">
					<div class="flexslider loading_state shadow border custom_flex top_right" data-plugin-options='{"animation": "slide", "animationSpeed": 600, "directionNav": true, "controlNav" :false, "animationLoop": true, "slideshow": false, "controlsContainer": ".tabs_slider_navigation.access_nav", "counts": [4,3,3,2,1]}'>
						<ul class="tabs_slider access_slides slides">
							<?
							if($arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"])
								$GLOBALS['arrFilterAccess'] = array("ID" => $arResult["PROPERTIES"]["EXPANDABLES"]["VALUE"]);

							$GLOBALS['arrFilterAccess']['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
							CNext::makeElementFilterInRegion($GLOBALS['arrFilterAccess']);
							?>
							<?$APPLICATION->IncludeComponent(
								"bitrix:catalog.top",
								"main",
								array(
									"USE_REGION" => ($arRegion ? "Y" : "N"),
									"STORES" => $arParams['STORES'],
									"TITLE_BLOCK" => $arParams["SECTION_TOP_BLOCK_TITLE"],
									"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
									"IBLOCK_ID" => $arParams["IBLOCK_ID"],
									"SALE_STIKER" => $arParams["SALE_STIKER"],
									"STIKERS_PROP" => $arParams["STIKERS_PROP"],
									"SHOW_RATING" => $arParams["SHOW_RATING"],
									"FILTER_NAME" => 'arrFilterAccess',
									"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
									"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
									"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
									"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
									"CUSTOM_FILTER" => $arResult['PROPERTIES']['EXPANDABLES_FILTER']['~VALUE'],
									"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
									"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
									"BASKET_URL" => $arParams["BASKET_URL"],
									"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
									"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
									"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
									"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
									"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
									"DISPLAY_COMPARE" => ($arParams["DISPLAY_COMPARE"] ? "Y" : "N"),
									"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
									"ELEMENT_COUNT" => $disply_elements,
									"SHOW_MEASURE_WITH_RATIO" => $arParams["SHOW_MEASURE_WITH_RATIO"],
									"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
									"LINE_ELEMENT_COUNT" => $arParams["TOP_LINE_ELEMENT_COUNT"],
									"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
									"PRICE_CODE" => $arParams['PRICE_CODE'],
									"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
									"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
									"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
									"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
									"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
									"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
									"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
									"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
									"CACHE_TYPE" => $arParams["CACHE_TYPE"],
									"CACHE_TIME" => $arParams["CACHE_TIME"],
									"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
									"CACHE_FILTER" => $arParams["CACHE_FILTER"],
									"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
									"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
									"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
									"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
									"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
									"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
									"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
									"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
									'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
									'CURRENCY_ID' => $arParams['CURRENCY_ID'],
									'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
									'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
									'VIEW_MODE' => (isset($arParams['TOP_VIEW_MODE']) ? $arParams['TOP_VIEW_MODE'] : ''),
									'ROTATE_TIMER' => (isset($arParams['TOP_ROTATE_TIMER']) ? $arParams['TOP_ROTATE_TIMER'] : ''),
									'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
									'LABEL_PROP' => $arParams['LABEL_PROP'],
									'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
									'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

									'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
									'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
									'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
									'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
									'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
									'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
									'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
									'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
									'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
									'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
									'ADD_TO_BASKET_ACTION' => $basketAction,
									'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
									'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
								),
								false, array("HIDE_ICONS"=>"Y")
							);?>
						</ul>
					</div>
				</li>
			</ul>
		</div>
		</div>
	<?endif;?>
	<?if($arResult["PROPERTIES"]["PODBORKI"]["VALUE"]):?>
		<div class="wraps podborki">
			<?$GLOBALS['arrFilterLanding'] = array("ID" => $arResult["PROPERTIES"]["PODBORKI"]["VALUE"]);?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				"news-project",
				array(
					"IBLOCK_TYPE" => "aspro_next_content",
					"IBLOCK_ID" => $arResult["PROPERTIES"]["PODBORKI"]["LINK_IBLOCK_ID"],
					"NEWS_COUNT" => "20",
					"SORT_BY1" => "SORT",
					"SORT_ORDER1" => "ASC",
					"SORT_BY2" => "ID",
					"SORT_ORDER2" => "DESC",
					"FILTER_NAME" => "arrFilterLanding",
					"FIELD_CODE" => array(
						0 => "NAME",
						1 => "PREVIEW_PICTURE",
						2 => "",
					),
					"PROPERTY_CODE" => array(
						0 => "PERIOD",
						1 => "REDIRECT",
						2 => "",
					),
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"CACHE_TYPE" => "N",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "Y",
					"CACHE_GROUPS" => "N",
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"SET_TITLE" => "N",
					"SET_STATUS_404" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "N",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"INCLUDE_SUBSECTIONS" => "Y",
					"PAGER_TEMPLATE" => ".default",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"PAGER_TITLE" => "Новости",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"VIEW_TYPE" => "block",
					"BIG_BLOCK" => "Y",
					"SHOW_MORE" => "N",
					"IMAGE_POSITION" => "left",
					"COUNT_IN_LINE" => "3",
					"TITLE" => ($arParams["BLOCK_LANDINGS_NAME"] == "N" ? GetMessage("BLOCK_LANDINGS_NAME") : $arParams["BLOCK_LANDINGS_NAME"]),
				),
				$component, array("HIDE_ICONS" => "Y")
			);?>
		</div>
	<?endif;?>
	<div class="wraps podborki">
		<?$GLOBALS['arrFilterBlog'] = array("PROPERTY_LINK_GOODS" => $arResult["ID"]);?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:news.list",
			// "news-project",
			"news5",
			array(
				"IBLOCK_TYPE" => "aspro_next_content",
				"IBLOCK_ID" => $arParams["BLOG_IBLOCK_ID"],
				"NEWS_COUNT" => "20",
				"SORT_BY1" => "SORT",
				"SORT_ORDER1" => "ASC",
				"SORT_BY2" => "ID",
				"SORT_ORDER2" => "DESC",
				"FILTER_NAME" => "arrFilterBlog",
				"FIELD_CODE" => array(
					0 => "NAME",
					1 => "PREVIEW_PICTURE",
					2 => "",
				),
				"PROPERTY_CODE" => array(
					0 => "PERIOD",
					1 => "REDIRECT",
					2 => "",
				),
				"CHECK_DATES" => "Y",
				"DETAIL_URL" => "",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"CACHE_TYPE" => "N",
				"CACHE_TIME" => "36000000",
				"CACHE_FILTER" => "Y",
				"CACHE_GROUPS" => "N",
				"PREVIEW_TRUNCATE_LEN" => "",
				"ACTIVE_DATE_FORMAT" => "j F Y",
				"SET_TITLE" => "N",
				"SET_STATUS_404" => "N",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"ADD_SECTIONS_CHAIN" => "N",
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",
				"PARENT_SECTION" => "",
				"PARENT_SECTION_CODE" => "",
				"INCLUDE_SUBSECTIONS" => "Y",
				"PAGER_TEMPLATE" => ".default",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "Y",
				"PAGER_TITLE" => "Новости",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"VIEW_TYPE" => "block",
				"BIG_BLOCK" => "Y",
				"SHOW_MORE" => "N",
				"IMAGE_POSITION" => "left",
				"COUNT_IN_LINE" => "3",
				"TITLE" => ($arParams["BLOCK_BLOG_NAME"] == "N" ? GetMessage("BLOCK_BLOG_NAME") : $arParams["BLOCK_BLOG_NAME"]),
			),
			$component, array("HIDE_ICONS" => "Y")
		);?>
	</div>
	<?if($arParams["USE_REVIEW"] == "Y"):?>
		<div class="wraps product_reviews_tab hidden_print">
			<hr>
			<h4><?=($arParams["TAB_REVIEW_NAME"] ? $arParams["TAB_REVIEW_NAME"] : GetMessage("REVIEW_TAB"))?><span class="count empty"></span></h4>
		</div>
	<?endif;?>
	<?if(($arParams["SHOW_ASK_BLOCK"] == "Y") && (intVal($arParams["ASK_FORM_ID"]))):?>
		<div class="wraps hidden_print">
			<hr>
			<h4><?=($arParams["TAB_FAQ_NAME"] ? $arParams["TAB_FAQ_NAME"] : GetMessage('ASK_TAB'))?></h4>
			<div id="ask" class="tab-pane">
				<div class="row">
					<div class="col-md-3 hidden-sm text_block">
						<?$APPLICATION->IncludeFile(SITE_DIR."include/ask_tab_detail_description.php", array(), array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_ASK_DESCRIPTION')));?>
					</div>
					<div class="col-md-9 form_block">
						<div id="ask_block"></div>
					</div>
				</div>
			</div>
		</div>
	<?endif;?>
	<?if($arResult["SERVICES"]):?>
		<?global $arrSaleFilter; $arrSaleFilter = array("ID" => $arResult["PROPERTIES"]["SERVICES"]["VALUE"]);?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:news.list",
			"items-services",
			array(
				"IBLOCK_TYPE" => "aspro_next_content",
				"IBLOCK_ID" => $arResult["PROPERTIES"]["SERVICES"]["LINK_IBLOCK_ID"],
				"NEWS_COUNT" => "20",
				"SORT_BY1" => "SORT",
				"SORT_ORDER1" => "ASC",
				"SORT_BY2" => "ID",
				"SORT_ORDER2" => "DESC",
				"FILTER_NAME" => "arrSaleFilter",
				"FIELD_CODE" => array(
					0 => "NAME",
					1 => "PREVIEW_TEXT",
					3 => "PREVIEW_PICTURE",
					4 => "",
				),
				"PROPERTY_CODE" => array(
					0 => "PERIOD",
					1 => "REDIRECT",
					2 => "",
				),
				"CHECK_DATES" => "Y",
				"DETAIL_URL" => "",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"CACHE_TYPE" => "N",
				"CACHE_TIME" => "36000000",
				"CACHE_FILTER" => "Y",
				"CACHE_GROUPS" => "N",
				"PREVIEW_TRUNCATE_LEN" => "",
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"SET_TITLE" => "N",
				"SET_STATUS_404" => "N",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"ADD_SECTIONS_CHAIN" => "N",
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",
				"PARENT_SECTION" => "",
				"PARENT_SECTION_CODE" => "",
				"INCLUDE_SUBSECTIONS" => "Y",
				"PAGER_TEMPLATE" => ".default",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "Y",
				"PAGER_TITLE" => "Новости",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"VIEW_TYPE" => "list",
				"BIG_BLOCK" => "Y",
				"IMAGE_POSITION" => "left",
				"COUNT_IN_LINE" => "2",
				"TITLE" => ($arParams["BLOCK_SERVICES_NAME"] ? $arParams["BLOCK_SERVICES_NAME"] : GetMessage("SERVICES_TITLE")),
			),
			$component, array("HIDE_ICONS" => "Y")
		);?>
	<?endif;?>
	<?if((count($arResult["PROPERTIES"][$instr_prop]["VALUE"]) && is_array($arResult["PROPERTIES"][$instr_prop]["VALUE"])) || count($arResult["SECTION_FULL"]["UF_FILES"])):?>
		<?
		$arFiles = array();
		if($arResult["PROPERTIES"][$instr_prop]["VALUE"]){
			$arFiles = $arResult["PROPERTIES"][$instr_prop]["VALUE"];
		}
		else{
			$arFiles = $arResult["SECTION_FULL"]["UF_FILES"];
		}
		if(is_array($arFiles)){
			foreach($arFiles as $key => $value){
				if(!intval($value)){
					unset($arFiles[$key]);
				}
			}
		}
		if($arFiles):?>
			<div class="wraps">
				<hr>
				<h4><?=($arParams["BLOCK_DOCS_NAME"] ? $arParams["BLOCK_DOCS_NAME"] : GetMessage("DOCUMENTS_TITLE"))?></h4>
				<div class="files_block">
					<div class="row flexbox">
						<?foreach($arFiles as $arItem):?>
							<div class="col-md-3 col-sm-6">
								<?$arFile=CNext::GetFileInfo($arItem);?>
								<div class="file_type clearfix <?=$arFile["TYPE"];?>">
									<i class="icon"></i>
									<div class="description">
										<a target="_blank" href="<?=$arFile["SRC"];?>" class="dark_link"><?=$arFile["DESCRIPTION"];?></a>
										<span class="size">
											<?=$arFile["FILE_SIZE_FORMAT"];?>
										</span>
									</div>
								</div>
							</div>
						<?endforeach;?>
					</div>
				</div>
			</div>
		<?endif;?>
	<?endif;?>
	<?if($arParams["SHOW_ADDITIONAL_TAB"] == "Y"):?>
		<div class="wraps">
			<hr>
			<h4><?=($arParams["TAB_DOPS_NAME"] ? $arParams["TAB_DOPS_NAME"] : GetMessage("ADDITIONAL_TAB"));?></h4>
			<div class="additional_block" id="dops">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/additional_products_description.php", array(), array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_ADDITIONAL_DESCRIPTION')));?>
			</div>
		</div>
	<?endif;?>
</div>
<?
if($arResult['CATALOG'] && $actualItem['CAN_BUY'] && $arParams['USE_PREDICTION'] === 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale')){
	$APPLICATION->IncludeComponent(
		'bitrix:sale.prediction.product.detail',
		'main',
		array(
			'BUTTON_ID' => false,
			'CUSTOM_SITE_ID' => isset($arParams['CUSTOM_SITE_ID']) ? $arParams['CUSTOM_SITE_ID'] : null,
			'POTENTIAL_PRODUCT_TO_BUY' => array(
				'ID' => $arResult['ID'],
				'MODULE' => isset($arResult['MODULE']) ? $arResult['MODULE'] : 'catalog',
				'PRODUCT_PROVIDER_CLASS' => isset($arResult['PRODUCT_PROVIDER_CLASS']) ? $arResult['PRODUCT_PROVIDER_CLASS'] : 'CCatalogProductProvider',
				'QUANTITY' => isset($arResult['QUANTITY']) ? $arResult['QUANTITY'] : null,
				'IBLOCK_ID' => $arResult['IBLOCK_ID'],
				'PRIMARY_OFFER_ID' => isset($arResult['OFFERS'][0]['ID']) ? $arResult['OFFERS'][0]['ID'] : null,
				'SECTION' => array(
					'ID' => isset($arResult['SECTION']['ID']) ? $arResult['SECTION']['ID'] : null,
					'IBLOCK_ID' => isset($arResult['SECTION']['IBLOCK_ID']) ? $arResult['SECTION']['IBLOCK_ID'] : null,
					'LEFT_MARGIN' => isset($arResult['SECTION']['LEFT_MARGIN']) ? $arResult['SECTION']['LEFT_MARGIN'] : null,
					'RIGHT_MARGIN' => isset($arResult['SECTION']['RIGHT_MARGIN']) ? $arResult['SECTION']['RIGHT_MARGIN'] : null,
				),
			)
		),
		$component,
		array('HIDE_ICONS' => 'Y')
	);
}
?>
<div class="gifts">
<?if ($arResult['CATALOG'] && $arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled("sale"))
{
	$APPLICATION->IncludeComponent("bitrix:sale.gift.product", "main", array(
			"USE_REGION" => $arParams['USE_REGION'],
			"STORES" => $arParams['STORES'],
			"SHOW_UNABLE_SKU_PROPS"=>$arParams["SHOW_UNABLE_SKU_PROPS"],
			'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
			'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
			'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'],
			'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
			'SUBSCRIBE_URL_TEMPLATE' => $arResult['~SUBSCRIBE_URL_TEMPLATE'],
			'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
			"OFFER_HIDE_NAME_PROPS" => $arParams["OFFER_HIDE_NAME_PROPS"],

			"SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
			"SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
			"PAGE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
			"LINE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
			"HIDE_BLOCK_TITLE" => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
			"BLOCK_TITLE" => $arParams['GIFTS_DETAIL_BLOCK_TITLE'],
			"TEXT_LABEL_GIFT" => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
			"SHOW_NAME" => $arParams['GIFTS_SHOW_NAME'],
			"SHOW_IMAGE" => $arParams['GIFTS_SHOW_IMAGE'],
			"MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],

			"SHOW_PRODUCTS_{$arParams['IBLOCK_ID']}" => "Y",
			"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
			"PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
			"MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
			"MESS_BTN_SUBSCRIBE" => $arParams["MESS_BTN_SUBSCRIBE"],
			"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
			"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
			"PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
			"USE_PRODUCT_QUANTITY" => 'N',
			"OFFER_TREE_PROPS_{$arResult['OFFERS_IBLOCK']}" => $arParams['OFFER_TREE_PROPS'],
			"CART_PROPERTIES_{$arResult['OFFERS_IBLOCK']}" => $arParams['OFFERS_CART_PROPERTIES'],
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
			"SALE_STIKER" => $arParams["SALE_STIKER"],
			"STIKERS_PROP" => $arParams["STIKERS_PROP"],
			"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
			"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
			"DISPLAY_TYPE" => "block",
			"SHOW_RATING" => $arParams["SHOW_RATING"],
			"DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
			"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
			"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
			"TYPE_SKU" => "Y",

			"POTENTIAL_PRODUCT_TO_BUY" => array(
				'ID' => isset($arResult['ID']) ? $arResult['ID'] : null,
				'MODULE' => isset($arResult['MODULE']) ? $arResult['MODULE'] : 'catalog',
				'PRODUCT_PROVIDER_CLASS' => isset($arResult['PRODUCT_PROVIDER_CLASS']) ? $arResult['PRODUCT_PROVIDER_CLASS'] : 'CCatalogProductProvider',
				'QUANTITY' => isset($arResult['QUANTITY']) ? $arResult['QUANTITY'] : null,
				'IBLOCK_ID' => isset($arResult['IBLOCK_ID']) ? $arResult['IBLOCK_ID'] : null,

				'PRIMARY_OFFER_ID' => isset($arResult['OFFERS'][0]['ID']) ? $arResult['OFFERS'][0]['ID'] : null,
				'SECTION' => array(
					'ID' => isset($arResult['SECTION']['ID']) ? $arResult['SECTION']['ID'] : null,
					'IBLOCK_ID' => isset($arResult['SECTION']['IBLOCK_ID']) ? $arResult['SECTION']['IBLOCK_ID'] : null,
					'LEFT_MARGIN' => isset($arResult['SECTION']['LEFT_MARGIN']) ? $arResult['SECTION']['LEFT_MARGIN'] : null,
					'RIGHT_MARGIN' => isset($arResult['SECTION']['RIGHT_MARGIN']) ? $arResult['SECTION']['RIGHT_MARGIN'] : null,
				),
			)
		), $component, array("HIDE_ICONS" => "Y"));
}
if ($arResult['CATALOG'] && $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled("sale"))
{
	$APPLICATION->IncludeComponent(
			"bitrix:sale.gift.main.products",
			"main",
			array(
				"USE_REGION" => $arParams['USE_REGION'],
				"STORES" => $arParams['STORES'],
				"SHOW_UNABLE_SKU_PROPS"=>$arParams["SHOW_UNABLE_SKU_PROPS"],
				"PAGE_ELEMENT_COUNT" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
				"BLOCK_TITLE" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],

				"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
				"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],

				"AJAX_MODE" => $arParams["AJAX_MODE"],
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],

				"ELEMENT_SORT_FIELD" => 'ID',
				"ELEMENT_SORT_ORDER" => 'DESC',
				//"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
				//"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
				"FILTER_NAME" => 'searchFilter',
				"SECTION_URL" => $arParams["SECTION_URL"],
				"DETAIL_URL" => $arParams["DETAIL_URL"],
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
				"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
				"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],

				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],

				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SET_TITLE" => $arParams["SET_TITLE"],
				"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
				"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

				"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"],
				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
				"TEMPLATE_THEME" => (isset($arParams["TEMPLATE_THEME"]) ? $arParams["TEMPLATE_THEME"] : ""),

				"ADD_PICT_PROP" => (isset($arParams["ADD_PICT_PROP"]) ? $arParams["ADD_PICT_PROP"] : ""),

				"LABEL_PROP" => (isset($arParams["LABEL_PROP"]) ? $arParams["LABEL_PROP"] : ""),
				"OFFER_ADD_PICT_PROP" => (isset($arParams["OFFER_ADD_PICT_PROP"]) ? $arParams["OFFER_ADD_PICT_PROP"] : ""),
				"OFFER_TREE_PROPS" => (isset($arParams["OFFER_TREE_PROPS"]) ? $arParams["OFFER_TREE_PROPS"] : ""),
				"SHOW_DISCOUNT_PERCENT" => (isset($arParams["SHOW_DISCOUNT_PERCENT"]) ? $arParams["SHOW_DISCOUNT_PERCENT"] : ""),
				"SHOW_OLD_PRICE" => (isset($arParams["SHOW_OLD_PRICE"]) ? $arParams["SHOW_OLD_PRICE"] : ""),
				"MESS_BTN_BUY" => (isset($arParams["MESS_BTN_BUY"]) ? $arParams["MESS_BTN_BUY"] : ""),
				"MESS_BTN_ADD_TO_BASKET" => (isset($arParams["MESS_BTN_ADD_TO_BASKET"]) ? $arParams["MESS_BTN_ADD_TO_BASKET"] : ""),
				"MESS_BTN_DETAIL" => (isset($arParams["MESS_BTN_DETAIL"]) ? $arParams["MESS_BTN_DETAIL"] : ""),
				"MESS_NOT_AVAILABLE" => (isset($arParams["MESS_NOT_AVAILABLE"]) ? $arParams["MESS_NOT_AVAILABLE"] : ""),
				'ADD_TO_BASKET_ACTION' => (isset($arParams["ADD_TO_BASKET_ACTION"]) ? $arParams["ADD_TO_BASKET_ACTION"] : ""),
				'SHOW_CLOSE_POPUP' => (isset($arParams["SHOW_CLOSE_POPUP"]) ? $arParams["SHOW_CLOSE_POPUP"] : ""),
				'DISPLAY_COMPARE' => (isset($arParams['DISPLAY_COMPARE']) ? $arParams['DISPLAY_COMPARE'] : ''),
				'COMPARE_PATH' => (isset($arParams['COMPARE_PATH']) ? $arParams['COMPARE_PATH'] : ''),
				"SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
				"SALE_STIKER" => $arParams["SALE_STIKER"],
				"STIKERS_PROP" => $arParams["STIKERS_PROP"],
				"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
				"DISPLAY_TYPE" => "block",
				"SHOW_RATING" => $arParams["SHOW_RATING"],
				"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
				"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
			)
			+ array(
				'OFFER_ID' => empty($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID']) ? $arResult['ID'] : $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'],
				'SECTION_ID' => $arResult['SECTION']['ID'],
				'ELEMENT_ID' => $arResult['ID'],
			),
			$component,
			array("HIDE_ICONS" => "Y")
	);
}
?>
</div>
<script type="text/javascript">
	BX.message({
		QUANTITY_AVAILIABLE: '<? echo COption::GetOptionString("aspro.next", "EXPRESSION_FOR_EXISTS", GetMessage("EXPRESSION_FOR_EXISTS_DEFAULT"), SITE_ID); ?>',
		QUANTITY_NOT_AVAILIABLE: '<? echo COption::GetOptionString("aspro.next", "EXPRESSION_FOR_NOTEXISTS", GetMessage("EXPRESSION_FOR_NOTEXISTS"), SITE_ID); ?>',
		ADD_ERROR_BASKET: '<? echo GetMessage("ADD_ERROR_BASKET"); ?>',
		ADD_ERROR_COMPARE: '<? echo GetMessage("ADD_ERROR_COMPARE"); ?>',
		ONE_CLICK_BUY: '<? echo GetMessage("ONE_CLICK_BUY"); ?>',
		MORE_TEXT_BOTTOM: '<?=\Bitrix\Main\Config\Option::get("aspro.next", "EXPRESSION_READ_MORE_OFFERS_DEFAULT", GetMessage("MORE_TEXT_BOTTOM"));?>',
		TYPE_SKU: '<? echo $arParams['TYPE_SKU']; ?>',
		HAS_SKU_PROPS: '<? echo ($arResult['OFFERS_PROP'] ? 'Y' : 'N'); ?>',
		SITE_ID: '<? echo SITE_ID; ?>'
	})
</script>
<?if($templateData['BRAND_ITEM'] || (\Bitrix\Main\ModuleManager::isModuleInstalled("sale") && (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N'))):?>
	</div>
<?endif;?>