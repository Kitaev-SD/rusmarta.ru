<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>

<script type="text/javascript">
	$(function() {
		$(window).resize(function () {
			currentWidth = $(".center:not(.inner)").first().width();
			if(currentWidth < "768") {
				$(".news-detail__picture").css({
					"height": currentWidth * 0.30 + "px"
				});
			} else {
				$(".news-detail__picture").css({"height": ""});
			}
		});
		$(window).resize();
	});
</script>

<div class="news-detail">
	<?if(is_array($arResult["DETAIL_PICTURE"])):?>
		<div class="news-detail__picture" style="background-image:url('<?=$arResult['DETAIL_PICTURE']['SRC']?>');"></div>
	<?endif;
	if(!empty($arResult["DISPLAY_ACTIVE_FROM"]) || (isset($arResult["DISPLAY_DATE_CREATE"]) && !empty($arResult["DISPLAY_DATE_CREATE"]))):?>
		<div class="news-detail__date-wrap">
			<div class="news-detail__date"><?=(!empty($arResult["DISPLAY_ACTIVE_FROM"]) ? $arResult["DISPLAY_ACTIVE_FROM"] : $arResult["DISPLAY_DATE_CREATE"]);?></div>
		</div>	
	<?endif;
	if(!empty($arResult["DETAIL_TEXT"])):?>
		<div class="news-detail__text"><?=$arResult["DETAIL_TEXT"]?></div>
	<?endif;?>
</div>