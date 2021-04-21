<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

global $arSetting;
$inMinPrice = in_array("MIN_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);

//JS//?>
<script type="text/javascript">
	//<![CDATA[
	$(function() {
		$(".leftSlider").anythingSlider({
			"theme": "left-slider",
			"resizeContents": false,
			"easing": "easeInOutExpo",
			"buildArrows": false,					
			"buildStartStop": false,
			"hashTags": false,
			"autoPlay": true,
			"autoPlayLocked": true
		});				
	});
	//]]>
</script>

<?//ITEMS//?>
<div class="left-slider<?=($arSetting['REFERENCE_PRICE']['VALUE'] == 'Y' && !empty($arSetting['REFERENCE_PRICE_COEF']['VALUE']) ? ' reference' : '');?>">
	<ul class="leftSlider">
		<?foreach($arResult["ITEMS"] as $key => $arElement) {
			//CURRENCY_FORMAT//
			$arCurFormat = $currency = false;
			if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
				$arCurFormat = CCurrencyLang::GetCurrencyFormat($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
				if($arCurFormat["HIDE_ZERO"] == "Y")
					if(round($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"]) == round($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], 0))
						$arCurFormat["DECIMALS"] = 0;
			} else {
				$arCurFormat = CCurrencyLang::GetCurrencyFormat($arElement["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
				if($arCurFormat["HIDE_ZERO"] == "Y")
					if(round($arElement["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"]) == round($arElement["MIN_PRICE"]["RATIO_PRICE"], 0))
						$arCurFormat["DECIMALS"] = 0;
			}
			if(empty($arCurFormat["THOUSANDS_SEP"]))
				$arCurFormat["THOUSANDS_SEP"] = " ";
			$currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);

			//NEW_HIT_DISCOUNT//
			$sticker = "";
			if(array_key_exists("PROPERTIES", $arElement) && is_array($arElement["PROPERTIES"])) {
				//NEW//
				if(array_key_exists("NEWPRODUCT", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == false)
					$sticker .= "<span class='new'>".GetMessage("CATALOG_ELEMENT_NEWPRODUCT")."</span>";
				//HIT//
				if(array_key_exists("SALELEADER", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["SALELEADER"]["VALUE"] == false)
					$sticker .= "<span class='hit'>".GetMessage("CATALOG_ELEMENT_SALELEADER")."</span>";
				//DISCOUNT//
				if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {						
					if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PERCENT"] > 0)
						$sticker .= "<span class='discount'>-".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PERCENT"]."%</span>";
					else
						if(array_key_exists("DISCOUNT", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["DISCOUNT"]["VALUE"] == false)
							$sticker .= "<span class='discount'>%</span>";
				} else {
					if($arElement["MIN_PRICE"]["PERCENT"] > 0)
						$sticker .= "<span class='discount'>-".$arElement["MIN_PRICE"]["PERCENT"]."%</span>";
					else
						if(array_key_exists("DISCOUNT", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["DISCOUNT"]["VALUE"] == false)
							$sticker .= "<span class='discount'>%</span>";
				}
			}

			//PREVIEW_PICTURE_ALT//
			$strAlt = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arElement["NAME"]);

			//PREVIEW_PICTURE_TITLE//
			$strTitle = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arElement["NAME"]);

			//ITEM//?>
			<li>
				<div class="item-image">
					<?//ITEM_PREVIEW_PICTURE//?>									
					<a href="<?=$arElement['DETAIL_PAGE_URL']?>">
						<?if(is_array($arElement["PREVIEW_PICTURE"])) {?>
							<img class="item_img" src="<?=$arElement['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
						<?} else {?>
							<img class="item_img" src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
						<?}?>								
						<span class="sticker">
							<?=$sticker?>
						</span>
						<?if(is_array($arElement["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"])) {?>
							<img class="manufacturer" src="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" title="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" />
						<?}?>
					</a>							
				</div>
				<?//ITEM_TITLE//?>
				<a class="item-title" href="<?=$arElement['DETAIL_PAGE_URL']?>" title="<?=$arElement['NAME']?>">
					<?=$arElement["NAME"]?>
				</a>
				<div class="item-price">
					<?//TOTAL_OFFERS_ITEM_PRICE//
					if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
						if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>									
							<span class="catalog-item-no-price">
								<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>											
							</span>									
						<?} else {?>
							<span class="catalog-item-price">
								<?=($arElement["TOTAL_OFFERS"]["FROM"] == "Y" ? "<span class='from'>".GetMessage("CATALOG_ELEMENT_FROM")."</span> " : "").number_format($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
								<span><?=$currency?></span>
								<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
									<span class="catalog-item-price-reference">
										<?=CCurrencyLang::CurrencyFormat($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], true);?>
									</span>
								<?}?>
							</span>
							<?if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] < $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
								<span class="catalog-item-price-old">
									<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
								</span>
							<?}															
						}
					//ITEM_PRICE//
					} else {
						if($arElement["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>
							<span class="catalog-item-no-price">
								<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
							</span>
						<?} else {?>
							<span class="catalog-item-price">
								<?if(count($arElement["ITEM_QUANTITY_RANGES"]) > 1 && $inMinPrice) {?>
									<span class="from"><?=GetMessage("CATALOG_ELEMENT_FROM")?></span>
								<?}
								echo number_format($arElement["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
								<span><?=$currency?></span>
								<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
									<span class="catalog-item-price-reference">
										<?=CCurrencyLang::CurrencyFormat($arElement["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["MIN_PRICE"]["CURRENCY"], true);?>
									</span>
								<?}?>
							</span>
							<?if($arElement["MIN_PRICE"]["RATIO_PRICE"] < $arElement["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
								<span class="catalog-item-price-old">
									<?=$arElement["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
								</span>
							<?}											
						}
					}?>
				</div>
			</li>
		<?}?>
	</ul>
</div>