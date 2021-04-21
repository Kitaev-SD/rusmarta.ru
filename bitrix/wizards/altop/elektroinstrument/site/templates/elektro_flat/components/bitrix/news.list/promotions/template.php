<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;?>

<div class="promotions-block">
	<div class="promotions-block__title"><?=($APPLICATION->GetCurPage(true)== SITE_DIR."index.php" ? GetMessage("PROMOTIONS_TITLE") : GetMessage("PROMOTIONS_OTHER_TITLE"));?></div>
	<a class="promotions-block__all-promotions" href="<?=str_replace('#SITE_DIR#', SITE_DIR, $arResult['LIST_PAGE_URL']);?>"><?=GetMessage("ALL_PROMOTIONS")?></a>
	<div class="promotions-block__items">
		<ul class="promotions-block__slider">
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<li>
					<a class="promotions__item" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<span class="promotions__item-image-wrap">
							<span class="promotions__item-image"<?=(is_array($arItem["PREVIEW_PICTURE"]) ? " style=\"background-image:url('".$arItem["PREVIEW_PICTURE"]["SRC"]."');\"" : "");?>></span>
							<?if($arItem["PROPERTIES"]["TIMER"]["VALUE"] != false && !empty($arItem["ACTIVE_TO"])):
								$new_date = ParseDateTime($arItem["ACTIVE_TO"], FORMAT_DATETIME);
								if(!$new_date["HH"])
									$new_date["HH"] = 00;
								if(!$new_date["MI"])
									$new_date["MI"] = 00;?>
								<script type="text/javascript">
									$(function() {														
										$("#time_buy_timer_<?=$arItem['ID']?>").countdown({
											until: new Date(<?=$new_date["YYYY"]?>, <?=$new_date["MM"]?> - 1, <?=$new_date["DD"]?>, <?=$new_date["HH"]?>, <?=$new_date["MI"]?>),
											format: "DHMS",
											expiryText: "<span class='over'><?=GetMessage('PROMOTIONS_TIME_BUY_EXPIRY')?></span>"
										});
									});												
								</script>
								<span class="time_buy_cont">
									<span class="time_buy_clock"><i class="fa fa-clock-o"></i></span>
									<span class="time_buy_timer" id="time_buy_timer_<?=$arItem['ID']?>"></span>
								</span>
							<?endif;?>
						</span>
						<span class="promotions__item-block">
							<span class="promotions__item-date-wrap">
								<span class="promotions__item-date"><?=GetMessage("PROMOTIONS_RUNNING")." ".(isset($arItem["DISPLAY_ACTIVE_TO"]) && !empty($arItem["DISPLAY_ACTIVE_TO"]) ? GetMessage("PROMOTIONS_UNTIL")." ".$arItem["DISPLAY_ACTIVE_TO"] : GetMessage("PROMOTIONS_ALWAYS"));?></span>
							</span>
							<span class="promotions__item-name-wrap-wrap">
								<span class="promotions__item-name-wrap">
									<span class="promotions__item-name"><?=$arItem["NAME"]?></span>
								</span>
							</span>
						</span>
					</a>
				</li>
			<?endforeach;?>
		</ul>
	</div>
</div>

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		$(window).resize(function() {
			var curWidth = $(".center:not(.inner)").first().width(),
				slider = $(".promotions-block__slider");

			slider.anythingSlider({
				"theme": "promotions-block",
				"resizeContents": false,
				"showMultiple": curWidth > 994 ? 3 : (curWidth >= 768 && curWidth <= 994 ? 2 : false),					
				"easing": "easeInOutExpo",
				"buildNavigation": false,
				"buildStartStop": false,
				"forwardText": "<i class='fa fa-chevron-right'></i>",
				"backText": "<i class='fa fa-chevron-left'></i>",
				"hashTags": false
			});
			
			var sliderData = slider.data("AnythingSlider");
			
			if((curWidth > 994 && sliderData.pages <= 3) || (curWidth >= 768 && curWidth <= 994 && sliderData.pages <= 2) || curWidth < 768) {
				sliderData.$back.hide();
				sliderData.$forward.hide();
			} else {
				sliderData.$back.show();
				sliderData.$forward.show();
			}

			if(curWidth < 768) {
				slider.find(".cloned").remove();
				slider.find(".panel").removeAttr("style").children().removeAttr("style");
			}
		});
		$(window).resize();
	});
	//]]>
</script>