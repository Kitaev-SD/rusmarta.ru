<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="compare_line">
	<?$frame = $this->createFrame("compare")->begin();?>
		<a href="<?=$arParams['COMPARE_URL']?>" title="<?=GetMessage("CATALOG_COMPARE_ELEMENTS")?>" rel="nofollow">
			<i class="fa fa-bar-chart"></i>
			<span class="text"><?=GetMessage("CATALOG_COMPARE_ELEMENTS")?></span>
			<span class="qnt_cont">
				<span class="qnt"><?=count($arResult)?></span>
			</span>
		</a>
	<?$frame->end();?>
</div>