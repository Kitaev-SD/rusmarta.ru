<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;?>

<div class="h3"><?=GetMessage("JOIN_US")?></div>
<ul>
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<li<?=(!empty($arItem["DISPLAY_PROPERTIES"]["BACKGR_HOV"])) ? " style='background:#".$arItem['DISPLAY_PROPERTIES']['BACKGR_HOV']['VALUE']."'" : ""?>>
			<?if(!empty($arItem["DISPLAY_PROPERTIES"]["URL"])):?>
				<a target="_blank" href="<?=$arItem['DISPLAY_PROPERTIES']['URL']['VALUE']?>" title="<?=$arItem['NAME']?>" rel="nofollow">
			<?else:?>
				<a href="javascript:void(0)" title="<?=$arItem['NAME']?>">
			<?endif;?>			
				<i class="fa<?=(!empty($arItem['DISPLAY_PROPERTIES']['ICON']['VALUE'])) ? ' '.$arItem['DISPLAY_PROPERTIES']['ICON']['VALUE'] : ''?>"></i>
			</a>
		</li>
	<?endforeach;?>
</ul>