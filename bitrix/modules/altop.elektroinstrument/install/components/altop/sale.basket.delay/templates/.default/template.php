<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="delay_line">
	<?$frame = $this->createFrame("delay")->begin();?>
		<a href="<?=$arParams["PATH_TO_DELAY"]?>" title="<?=GetMessage("MY_DELAY")?>" rel="nofollow">
			<i class="fa fa-heart"></i>
			<span class="text"><?=GetMessage("MY_DELAY")?></span>
			<span class="qnt_cont">
				<span class="qnt">
					<?=(isset($arResult["QUANTITY"]) && $arResult["QUANTITY"] > 0) ? $arResult["QUANTITY"] : "0";?>
				</span>
			</span>
		</a>
	<?$frame->end();?>
</div>