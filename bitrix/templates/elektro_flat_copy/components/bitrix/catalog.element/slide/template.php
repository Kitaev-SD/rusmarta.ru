<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

global $arSetting;
$inMinPrice = in_array("MIN_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inReferencePrice = $arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]);
$isPreviewPicture = is_array($arResult["PREVIEW_PICTURE"]);

//PREVIEW_PICTURE_ALT//
$strAlt = (isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != "" ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arResult["NAME"]);

//PREVIEW_PICTURE_TITLE//
$strTitle = (isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != "" ? $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arResult["NAME"]);

if(array_key_exists("PROPERTIES", $arResult) && is_array($arResult["PROPERTIES"])) {
	//NEW//
	if(array_key_exists("NEWPRODUCT", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == false)
		$sticker .= "<span class='new'>".Loc::getMessage("CATALOG_ELEMENT_NEWPRODUCT")."</span>";
	//HIT//
	if(array_key_exists("SALELEADER", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["SALELEADER"]["VALUE"] == false)
		$sticker .= "<span class='hit'>".Loc::getMessage("CATALOG_ELEMENT_SALELEADER")."</span>";
	//DISCOUNT//
	if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {		
		if($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["PERCENT"] > 0)
			$sticker .= "<span class='discount'>-".$arResult["TOTAL_OFFERS"]["MIN_PRICE"]["PERCENT"]."%</span>";
		else
			if(array_key_exists("DISCOUNT", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["DISCOUNT"]["VALUE"] == false)
				$sticker .= "<span class='discount'>%</span>";
	} else {
		if($arResult["MIN_PRICE"]["PERCENT"] > 0)
			$sticker .= "<span class='discount'>-".$arResult["MIN_PRICE"]["PERCENT"]."%</span>";
		else
			if(array_key_exists("DISCOUNT", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["DISCOUNT"]["VALUE"] == false)
				$sticker .= "<span class='discount'>%</span>";
	}
}

$rand = $this->randString();
?>
<div id="prod_<?=$arResult["ID"]?>_<?=$rand?>" class="slide-card-container product">
	<div class="slide-card">
		<div class="slide-prod">
			<div class="slide-prod-image-container">
				<div class="slide-prod-image">
					<?//ITEM_IMAGE//?>
					<div class="img">
						<?if($isPreviewPicture) {?>
							<img src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arResult["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arResult["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" class="<?=($arResult["PREVIEW_PICTURE"]["WIDTH"] >= $arResult["PREVIEW_PICTURE"]["HEIGHT"]? 'full-width': 'full-height')?>">
						<?} else {?>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$strAlt?>" title="<?=$strTitle?>">
						<?}?>
					</div>
				</div>
				<?//MANUFACTURER//?>
				<?if(is_array($arResult["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"])) {?>
					<img class="manufacturer" src="<?=$arResult['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arResult['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arResult['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arResult['PROPERTIES']['MANUFACTURER']['NAME']?>" title="<?=$arResult['PROPERTIES']['MANUFACTURER']['NAME']?>" />
				<?}?>
			</div>
			<div class="slide-prod-price">
				<?//CURRENCY_FORMAT//
				$arCurFormat = $currency = false;
				if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
					$arCurFormat = CCurrencyLang::GetCurrencyFormat($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
					if($arCurFormat["HIDE_ZERO"] == "Y")
						if(round($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"]) == round($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], 0))
							$arCurFormat["DECIMALS"] = 0;
				} else {
					$arCurFormat = CCurrencyLang::GetCurrencyFormat($arResult["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
					if($arCurFormat["HIDE_ZERO"] == "Y")
						if(round($arResult["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"]) == round($arResult["MIN_PRICE"]["RATIO_PRICE"], 0))
							$arCurFormat["DECIMALS"] = 0;
				}
				if(empty($arCurFormat["THOUSANDS_SEP"]))
					$arCurFormat["THOUSANDS_SEP"] = " ";
				$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
				
				//TOTAL_OFFERS_ITEM_PRICE//
				if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
					if($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>									
						<span class="no-price">
							<?=Loc::getMessage("CATALOG_ELEMENT_NO_PRICE")?>											
						</span>									
					<?} else {?>
						<span class="price">
							<?if($arResult["TOTAL_OFFERS"]["FROM"] == "Y") {?>
								<span><?=Loc::getMessage("CATALOG_ELEMENT_FROM")?></span>
							<?}?>
							<?=number_format($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
							<span><?=$currency?></span>
							<?if($inReferencePrice) {?>
								<span class="price-reference">
									<?=CCurrencyLang::CurrencyFormat($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], true);?>
								</span>
							<?}?>
						</span>
						<?if($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] < $arResult["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
							<span class="price-old">
								<?=$arResult["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
							</span>
						<?}															
					}
				//ITEM_PRICE//
				} else {
					if($arResult["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>
						<span class="no-price">
							<?=Loc::getMessage("CATALOG_ELEMENT_NO_PRICE")?>
						</span>
					<?} else {?>
						<span class="price">
							<?if(count($arResult["ITEM_QUANTITY_RANGES"]) > 1 && $inMinPrice) {?>
								<span class="from"><?=Loc::getMessage("CATALOG_ELEMENT_FROM")?></span>
							<?}
							echo number_format($arResult["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
							<span><?=$currency?></span>
							<?if($inReferencePrice) {?>
								<span class="price-reference">
									<?=CCurrencyLang::CurrencyFormat($arResult["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["MIN_PRICE"]["CURRENCY"], true);?>
								</span>
							<?}?>
						</span>
						<?if($arResult["MIN_PRICE"]["RATIO_PRICE"] < $arResult["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
							<span class="price-old">
								<?=$arResult["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
							</span>
						<?}											
					}
				}?>
			</div>
			<span class="shadow-modile"></span>
		</div>
		<span class="slide-prod-sticker">
			<?=$sticker?>
		</span>
	</div>
	<span class="slide-card-bg"></span>
</div>

<script>
	$(function() {
		var link = $('#prod_<?=$arResult["ID"]?>_<?=$rand?>').parent();
		// console.log($('#prod_<?=$arResult["ID"]?>_<?=$rand?>').parent().attr('href'));
		if(link.attr('href') == "javascript:void(0)") {
			link.attr('href', '<?=$arResult['DETAIL_PAGE_URL']?>');
		}
		// $('#prod_<?=$arResult["ID"]?>_<?=$rand?>')
	});
</script>