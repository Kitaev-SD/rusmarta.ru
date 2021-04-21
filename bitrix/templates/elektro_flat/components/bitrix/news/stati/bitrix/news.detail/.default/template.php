<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>

<script type="text/javascript">
	$(function() {
		$(window).resize(function () {
			currentWidth = $(".content-wrapper").children(".center").width();
			if(currentWidth < "768") {
				$(".reviews-detail__picture").css({
					"height": currentWidth * 0.30 + "px"
				});
			} else {
				$(".reviews-detail__picture").css({"height": ""});
			}
		});
		$(window).resize();
	});
</script>

<div class="reviews-detail">
	<?if(is_array($arResult["DETAIL_PICTURE"])):?>
		<div class="reviews-detail__picture" style="background-image:url('<?=$arResult['DETAIL_PICTURE']['SRC']?>');"></div>
	<?endif;
	if(!empty($arResult["DETAIL_TEXT"])):?>
		<div class="reviews-detail__text"><?=$arResult["DETAIL_TEXT"]?></div>
	<?endif;?>
</div>