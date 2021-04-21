<?
use Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if(!$arResult['LOCATION'] || !$arResult['LOCATION']['ID'] || !$arResult['DELIVERY'] || $arResult['ERROR']){
	return;
}

Loc::loadMessages(__FILE__);

$minPrice = $minPriceFormatted = false;
$arMinPriceByParent = array();
foreach($arResult['DELIVERY'] as $arDelivery){
	if(!$arDelivery['CALCULATE_ERRORS']){
		$price = array_key_exists('DELIVERY_DISCOUNT_PRICE', $arDelivery) ? $arDelivery['DELIVERY_DISCOUNT_PRICE'] : $arDelivery['PRICE'];
		$priceFormatted = array_key_exists('DELIVERY_DISCOUNT_PRICE_FORMATED', $arDelivery) ? $arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED'] : $arDelivery['PRICE_FORMATED'];

		if($minPrice === false || ($minPrice > $price)){
			$minPrice = $price;
			$minPriceFormatted = $priceFormatted;
		}

		if($arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID']){
			$parentId = $arDelivery['PARENT_ID'] ? $arDelivery['PARENT_ID'] : $arDelivery['ID'];
			if(in_array($parentId, $arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID'])){
				if(!$arMinPriceByParent[$parentId] || ($arMinPriceByParent[$parentId]['PRICE'] > $price)){
					$arMinPriceByParent[$parentId] = array(
						'PRICE' => $price,
						'PRICE_FORMATED' => $priceFormatted,
						'NAME' => $arDelivery['PARENT_NAME'],
					);
				}
			}
		}
	}
}
?>
<div id="catalog-delivery-preview-<?=$arResult['RAND']?>" class="catalog-delivery-preview">
	<div class="catalog-delivery-preview-title font_sxs <?=($arMinPriceByParent ? 'darken' : '')?>"><?=str_replace(
		array('#LOCATION_NAME#'),
		array($arResult['LOCATION'] && $arResult['LOCATION']['ID'] ? '<span class="catalog-delivery-preview-title-city"><span>'.$arResult['LOCATION']['LOCATION_NAME'].'</span></span>' : ''),
		$arResult['MESSAGES']['PREVIEW_TITLE']
	)?><?=(!$arMinPriceByParent ? '&nbsp;<span class="catalog-delivery-preview-title-price">'.($minPrice > 0 ? Loc::getMessage('CD_T_FROM_PRICE', array('#PRICE_FORMATTED#' => $minPriceFormatted)) : Loc::getMessage('CD_T_DELIVERY_PRICE_FREE')).'</span>' : '')?></div>
	<?if($arMinPriceByParent):?>
		<div class="catalog-delivery-preview-items font_sxs">
			<?foreach($arMinPriceByParent as $parentId => $arPrice):?>
				<div class="catalog-delivery-preview-item">
					<span class="catalog-delivery-preview-item-name"><?=$arPrice['NAME']?></span>&nbsp;&mdash;&nbsp;<span class="catalog-delivery-preview-item-price"><?=($arPrice['PRICE'] ? Loc::getMessage('CD_T_FROM_PRICE', array('#PRICE_FORMATTED#' => $arPrice['PRICE_FORMATED'])) : Loc::getMessage('CD_T_DELIVERY_PRICE_FREE'))?></span>
				</div>
			<?endforeach;?>
		</div>
	<?endif;?>
	<div class="font_xs"><span class="animate-load dotted colored_theme_text_with_hover colored" data-event="jqm" data-param-form_id="delivery" data-name="delivery" data-param-product_id="<?=$arResult['PRODUCT_ID']?>" data-param-quantity="<?=$arResult['PRODUCT_QUANTITY']?>" data-time="<?=time()?>" onclick="$(this).parent().addClass('loadings')"><?=$arResult['MESSAGES']['PREVIEW_MORE_TITLE']?></span></div>
	<script>
	BX.loadCSS(['<?=$templateFolder?>/preview.css']);
	BX.loadScript(['<?=$templateFolder?>/preview.js']);
	</script>
</div>