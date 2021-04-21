<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(count($arResult["ITEMS"]) < 1)
	return;

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);?>

<div class="promotions-list">	
	<?foreach($arResult["ITEMS"] as $arItem):
		$arCompareDates = 1;
		if(!empty($arItem["ACTIVE_TO"])):
			$displayActiveToDate = $arItem["ACTIVE_TO"];
			$displayCurrentDate = ConvertTimeStamp(false, "FULL");
			$arCompareDates = $DB->CompareDates($displayActiveToDate, $displayCurrentDate);
		endif;?>		
		<a class="promotions__item<?=($arCompareDates <= 0 ? ' completed' : '');?>" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
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
								expiryText: "<span class='over'><?=GetMessage('PROMOTIONS_TIME_BUY_EXPIRY')?></span>",
								alwaysExpire: true
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
					<span class="promotions__item-date">
						<?if($arCompareDates <= 0):
							echo Loc::getMessage("PROMOTIONS_ENDED")." ".$arItem["DISPLAY_ACTIVE_TO"];
						else:
							echo Loc::getMessage("PROMOTIONS_RUNNING")." ".(isset($arItem["DISPLAY_ACTIVE_TO"]) && !empty($arItem["DISPLAY_ACTIVE_TO"]) ? Loc::getMessage("PROMOTIONS_UNTIL")." ".$arItem["DISPLAY_ACTIVE_TO"] : Loc::getMessage("PROMOTIONS_ALWAYS"));
						endif;?>
					</span>
				</span>
				<span class="promotions__item-name-wrap-wrap">
					<span class="promotions__item-name-wrap">
						<span class="promotions__item-name"><?=$arItem["NAME"]?></span>
					</span>
				</span>
			</span>
		</a>
	<?endforeach;?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):
	echo $arResult["NAV_STRING"];
endif;?>