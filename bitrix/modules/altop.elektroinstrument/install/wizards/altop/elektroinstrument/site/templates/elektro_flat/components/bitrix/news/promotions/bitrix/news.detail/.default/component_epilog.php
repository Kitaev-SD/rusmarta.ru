<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);?>

<script type="text/javascript">
	$(function() {
		$(window).resize(function () {
			currentWidth = $(".center:not(.inner)").first().width();
			if(currentWidth < "768") {
				$(".promotions-detail__picture-wrap").css({
					"height": currentWidth * 0.30 + "px"
				});
			} else {
				$(".promotions-detail__picture-wrap").css({"height": ""});
			}
		});
		$(window).resize();
	});
</script>

<?$arCompareDates = 1;
if(!empty($arResult["ACTIVE_TO"])):
	$displayActiveToDate = $arResult["ACTIVE_TO"];
	$displayCurrentDate = ConvertTimeStamp(false, "FULL");
	$arCompareDates = $DB->CompareDates($displayActiveToDate, $displayCurrentDate);
endif;?>

<div class="promotions-detail<?=($arCompareDates <= 0 ? ' completed' : '');?>">
	<?if(is_array($arResult["DETAIL_PICTURE"])):?>
		<div class="promotions-detail__picture-wrap">
			<div class="promotions-detail__picture" style="background-image:url('<?=$arResult['DETAIL_PICTURE']['SRC']?>');"></div>
			<?if($arResult["PROPERTIES"]["TIMER"]["VALUE"] != false && !empty($arResult["ACTIVE_TO"])):
				$new_date = ParseDateTime($arResult["ACTIVE_TO"], FORMAT_DATETIME);
				if(!$new_date["HH"])
					$new_date["HH"] = 00;
				if(!$new_date["MI"])
					$new_date["MI"] = 00;?>
				<script type="text/javascript">												
					$(function() {														
						$("#time_buy_timer_<?=$arResult['ID']?>").countdown({
							until: new Date(<?=$new_date["YYYY"]?>, <?=$new_date["MM"]?> - 1, <?=$new_date["DD"]?>, <?=$new_date["HH"]?>, <?=$new_date["MI"]?>),
							format: "DHMS",
							expiryText: "<span class='over'><?=Loc::getMessage('PROMOTIONS_TIME_BUY_EXPIRY')?></span>",
							alwaysExpire: true
						});
					});												
				</script>
				<span class="time_buy_cont">
					<span class="time_buy_clock"><i class="fa fa-clock-o"></i></span>
					<span class="time_buy_timer" id="time_buy_timer_<?=$arResult['ID']?>"></span>
				</span>
			<?endif;?>
		</div>
	<?endif;?>
	<div class="promotions-detail__date-wrap">
		<div class="promotions-detail__date">
			<?if($arCompareDates <= 0):
				echo Loc::getMessage("PROMOTIONS_ENDED")." ".$arResult["DISPLAY_ACTIVE_TO"];
			else:
				echo Loc::getMessage("PROMOTIONS_RUNNING")." ".(isset($arResult["DISPLAY_ACTIVE_TO"]) && !empty($arResult["DISPLAY_ACTIVE_TO"]) ? Loc::getMessage("PROMOTIONS_UNTIL")." ".$arResult["DISPLAY_ACTIVE_TO"] : Loc::getMessage("PROMOTIONS_ALWAYS"));
			endif;?>
		</div>
	</div>
	<?if(!empty($arResult["PREVIEW_TEXT"])):?>
		<div class="promotions-detail__text-preview"><?=$arResult["PREVIEW_TEXT"]?></div>
	<?endif;
	if($arCompareDates > 0 && !empty($arResult["PRODUCTS_IDS"])):
		global $arPromProdPrFilter;
		$arPromProdPrFilter = array(
			"ID" => $arResult["PRODUCTS_IDS"]
		);?>
		<div class="promotions-detail__products">
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
				array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/promotions_products.php"
				),
				false,
				array("HIDE_ICONS" => "Y")
			);?>
		</div>
	<?endif;
	if(!empty($arResult["DETAIL_TEXT"])):?>
		<div class="promotions-detail__text-detail"><?=$arResult["DETAIL_TEXT"]?></div>
	<?endif;?>
<?
$APPLICATION->SetPageProperty("ogtype", "article");
if(is_array($arResult["DETAIL_PICTURE"])):
	$APPLICATION->SetPageProperty("ogimage", (CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.$arResult['DETAIL_PICTURE']['SRC']);
	$APPLICATION->SetPageProperty("ogimagewidth", $arResult["DETAIL_PICTURE"]["WIDTH"]);
	$APPLICATION->SetPageProperty("ogimageheight", $arResult["DETAIL_PICTURE"]["HEIGHT"]);
endif;
?>