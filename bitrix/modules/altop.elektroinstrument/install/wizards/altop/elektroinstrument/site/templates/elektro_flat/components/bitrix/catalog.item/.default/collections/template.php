<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;?>

<div class="catalog-item-info">							
	<?//ITEM_PREVIEW_PICTURE//?>
	<div class="item-image-cont">
		<div class="item-image">								
			<meta content="<?=(is_array($arElement['PREVIEW_PICTURE']) ? $arElement['PREVIEW_PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/no-photo.jpg');?>" itemprop="image" />
			<a href="<?=$arElement['DETAIL_PAGE_URL']?>"<?=(is_array($arElement["PREVIEW_PICTURE"])) ? " style='background-image:url(".$arElement['PREVIEW_PICTURE']['SRC'].")'" : " style='background-image:url(".SITE_TEMPLATE_PATH."/images/no-photo.jpg)'"?>></a>
			<span class="sticker">
				<?=$sticker?>
			</span>
			<?if(is_array($arElement["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"])) {?>
				<img class="manufacturer" src="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" title="<?=$arElement['PROPERTIES']['MANUFACTURER']['NAME']?>" />
			<?}?>
		</div>
	</div>
	<?//TIME_BUY//
	if(array_key_exists("TIME_BUY", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
		if(!empty($arElement["CURRENT_DISCOUNT"]["ACTIVE_TO"])) {
			$showBar = false;													
			if($arElement["CAN_BUY"]) {
				if($arElement["CHECK_QUANTITY"]) {
					$showBar = true;
					$startQnt = $arElement["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] ? $arElement["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] : $arElement["CATALOG_QUANTITY"];
					$currQnt = $arElement["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] ? $arElement["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] : $arElement["CATALOG_QUANTITY"];
					$currQntPercent = round($currQnt * 100 / $startQnt);
				} else {
					$showBar = true;
					$currQntPercent = 100;
				}
			}
			if($showBar == true) {?>
				<div class="item_time_buy_cont">
					<div class="item_time_buy">
						<div class="progress_bar_block">
							<span class="progress_bar_title"><?=Loc::getMessage("CT_BCS_ELEMENT_QUANTITY_PERCENT")?></span>
							<div class="progress_bar_cont">
								<div class="progress_bar_bg">
									<div class="progress_bar_line" style="width:<?=$currQntPercent?>%;"></div>
								</div>
							</div>
							<span class="progress_bar_percent"><?=$currQntPercent?>%</span>
						</div>
						<?$new_date = ParseDateTime($arElement["CURRENT_DISCOUNT"]["ACTIVE_TO"], FORMAT_DATETIME);?>
						<script type="text/javascript">												
							$(function() {														
								$("#time_buy_timer_<?=$itemIds['ID']?>").countdown({
									until: new Date(<?=$new_date["YYYY"]?>, <?=$new_date["MM"]?> - 1, <?=$new_date["DD"]?>, <?=$new_date["HH"]?>, <?=$new_date["MI"]?>),
									format: "DHMS",
									expiryText: "<div class='over'><?=Loc::getMessage('CT_BCS_ELEMENT_TIME_BUY_EXPIRY')?></div>"
								});
							});												
						</script>
						<div class="time_buy_cont">
							<div class="time_buy_clock">
								<i class="fa fa-clock-o"></i>
							</div>
							<div class="time_buy_timer" id="time_buy_timer_<?=$itemIds['ID']?>"></div>
						</div>
					</div>
				</div>
			<?}
		}
	}
	//AVAILABLE_RATING//?>
	<a class="item-all" href="<?=$arElement['DETAIL_PAGE_URL']?>">
		<div class="item-available-rating">
			<?//AVAILABLE//?>
			<div class="available">
				<?if($arElement["CAN_BUY"]) {?>									
					<div class="avl">
						<i class="fa fa-check-circle"></i>
						<span>
							<?=Loc::getMessage("CT_BCS_ELEMENT_AVAILABLE");?>
						</span>
					</div>
				<?} elseif(!$arElement["CAN_BUY"]) {?>									
					<div class="not_avl">
						<i class="fa fa-times-circle"></i>
						<span><?=Loc::getMessage("CT_BCS_ELEMENT_NOT_AVAILABLE")?></span>
					</div>
				<?}?>
			</div>
			<?//RATING//
			if($inRating) {?>
				<div class="rating">
					<?if($arElement["PROPERTIES"]["vote_count"]["VALUE"])
						$ratingAvg = round($arElement["PROPERTIES"]["vote_sum"]["VALUE"] / $arElement["PROPERTIES"]["vote_count"]["VALUE"], 2);
					else
						$ratingAvg = 0;
					if($ratingAvg) {									
						for($i = 0; $i <= 4; $i++) {?>
							<div class="star<?=($ratingAvg > $i ? ' voted' : ' empty');?>" title="<?=$i+1?>"><i class="fa fa-star"></i></div>
						<?}
					} else {
						for($i = 0; $i <= 4; $i++) {?>
							<div class="star empty" title="<?=$i+1?>"><i class="fa fa-star"></i></div>
						<?}
					}?>
				</div>
			<?}?>
		</div>
	</a>	
	<?//ITEM_TITLE//?>
	<div class="item-all-title">
		<a class="item-title" href="<?=$arElement['DETAIL_PAGE_URL']?>" title="<?=$arElement['NAME']?>" itemprop="url">
			<span itemprop="name"><?=$arElement['NAME']?></span>
		</a>
	</div>
	<?//ITEM_PREVIEW_TEXT//
	if($inPreviewText) {?>
		<a class="item-all" href="<?=$arElement['DETAIL_PAGE_URL']?>">
			<div class="item-desc" itemprop="description">
				<?=strip_tags($arElement["PREVIEW_TEXT"]);?>
			</div>
		</a>
	<?}
	//ITEM_PRICE//?>
	<div class="item-price-cont<?=(!$inOldPrice && !$inPercentPrice ? ' one' : '').(($inOldPrice && !$inPercentPrice) || (!$inOldPrice && $inPercentPrice) ? ' two' : '').($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		<?if($arElement["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>
			<div class="item-no-price">	
				<span class="unit">
					<?=Loc::getMessage("CT_BCS_ELEMENT_NO_PRICE")?>
				</span>												
			</div>
		<?} else {?>
			<div class="item-price">
				<span class="catalog-item-price">
					<span class="from">
						<?=Loc::getMessage("CT_BCS_ELEMENT_FROM")?>
					</span>
					<?echo number_format($arElement["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
					<span class="unit">
						<?=$currency?>
					</span>
				</span>
				<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
					<span class="catalog-item-price-reference">
						<?=CCurrencyLang::CurrencyFormat($arElement["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["MIN_PRICE"]["CURRENCY"], true);?>
					</span>
				<?}?>
			</div>
		<?}?>
		<meta itemprop="price" content="<?=$arElement["MIN_PRICE"]["RATIO_PRICE"]?>" />
		<meta itemprop="priceCurrency" content="<?=$arElement["MIN_PRICE"]["CURRENCY"]?>" />
		<?if($arElement["CAN_BUY"]) {?>
			<meta content="InStock" itemprop="availability" />
		<?} elseif(!$arElement["CAN_BUY"]) {?>
			<meta content="OutOfStock" itemprop="availability" />									
		<?}?>
	</div>
	<?//VERSIONS_PERFORMANCE//?>
	<?if(!empty($arElement["VERSIONS_PERFORMANCE"]["ITEMS"]) && count($arElement["VERSIONS_PERFORMANCE"]["ITEMS"]) > 0) {?>
		<div class="color-collection-container">
			<?foreach($arElement["VERSIONS_PERFORMANCE"]["ITEMS"] as $arColor) {
				if((is_array($arColor["PICTURE"]) && !empty($arColor["PICTURE"])) || (isset($arColor["PROPERTY_HEX_VALUE"]) && !empty($arColor["PROPERTY_HEX_VALUE"]))) {?>
					<div class="color-collection-item" title="<?=$arColor["NAME"]?>">
						<div class="image-color" style="
							<?if(is_array($arColor["PICTURE"]) && !empty($arColor["PICTURE"])) {?>
							background-image: url(<?=$arColor["PICTURE"]['SRC']?>);
							background-repeat: no-repeat;
							background-size: cover;
							background-position: center;
							<?} else {?>
							background-color: #<?=$arColor["PROPERTY_HEX_VALUE"]?>;
							<?}?>
						"></div>
					</div>
				<?}?>
			<?}?>
		</div>
	<?}?>
</div>