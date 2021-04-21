<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$frame = $this->createFrame("geolocation_delivery_".$arParams["ELEMENT_ID"])->begin("");

use Bitrix\Main\Localization\Loc;?>

<div id="geolocationDelivery-<?=$arParams['ELEMENT_ID']?>" class="geolocation-delivery" data-element-id="<?=$arParams['ELEMENT_ID']?>" data-element-count="<?=$arParams['ELEMENT_COUNT']?>" data-cart-products="<?=$arParams['CART_PRODUCTS']?>" data-cache-type="<?=$arParams['CACHE_TYPE']?>" data-cache-time="<?=$arParams['CACHE_TIME']?>">
	<div class="geolocation-delivery__title"><i class="fa fa-map-marker"></i><span><?=Loc::getMessage("GEOLOCATION_DELIVERY_IN")?></span><a id="geolocationDeliveryLink-<?=$arParams['ELEMENT_ID']?>" href="javascript:void(0);"><span><?=($arParams["AJAX_CALL"] == "Y" && $arParams["GEOLOCATION_LOCATION_ID"] <= 0 ? Loc::getMessage("GEOLOCATION_DELIVERY_NOT_DEFINED") : (!empty($arParams["GEOLOCATION_CITY"]) ? $arParams["GEOLOCATION_CITY"] : Loc::getMessage("GEOLOCATION_DELIVERY_POSITIONING")));?></span></a></div>
	<?if($arParams["AJAX_CALL"] == "Y"):
		if($arResult["DELIVERY"]):?>
			<div class="geolocation-delivery__delivery-list">
				<?foreach($arResult["DELIVERY"] as $delivery_id => $arDelivery):?>
					<div class="geolocation-delivery__delivery-item">
						<span class="geolocation-delivery__delivery-name"><?=$arDelivery["NAME"]?></span>
						<span class="geolocation-delivery__delivery-dots"></span>
						<span class="geolocation-delivery__delivery-price"><?=$arDelivery["PRICE_FORMATED"]?></span>
					</div>
				<?endforeach;?>
			</div>
		<?else:?>
			<div class="geolocation-delivery__error">
				<?=ShowError(Loc::getMessage("GEOLOCATION_DELIVERY_ERROR"))?>
			</div>
		<?endif;
	endif;?>
</div>

<?//JS_PARAMS//
$arJSParams["TITLE"] = Loc::getMessage("GEOLOCATION_DELIVERY_DETAIL_TITLE");
$arJSParams["COMPONENT_PATH"] = $this->__component->__path;
$arJSParams["COMPONENT_TEMPLATE"] = $this->GetFolder();
$arJSParams["PARAMS"] = $arParams;
$arJSParams = CUtil::PhpToJSObject($arJSParams);?>

<script type="text/javascript">		
	<?if($arParams["AJAX_CALL"] == "Y" && $arResult["DELIVERY"]):?>
		//GEOLOCATION_DELIVERY_DETAIL//
		BX.bind(BX("geolocationDeliveryLink-<?=$arParams['ELEMENT_ID']?>"), "click", function() {
			BX.GeolocationDeliveryDetail(<?=$arJSParams?>);
		});
	<?endif;
	if($arParams["AJAX_CALL"] != "Y" && $arParams["GEOLOCATION_LOCATION_ID"] > 0):?>
		//SHOW_GEOLOCATION_DELIVERY//
		BX.ShowGeolocationDelivery(<?=$arJSParams?>);	
	<?endif;?>
</script>

<?$frame->end();?>