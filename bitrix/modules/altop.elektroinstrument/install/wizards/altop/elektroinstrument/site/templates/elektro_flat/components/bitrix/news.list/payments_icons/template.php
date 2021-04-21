<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;?>

<div class="payment_methods">
	<div class="h3"><?=GetMessage("PAYMENT_METHODS")?></div>
	<ul>
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<li>
				<?if(!empty($arItem["DISPLAY_PROPERTIES"]["URL"])):?>
					<a target="_blank" href="<?=$arItem['DISPLAY_PROPERTIES']['URL']['VALUE']?>" title="<?=$arItem['NAME']?>" rel="nofollow">
				<?else:?>
					<a href="javascript:void(0)" title="<?=$arItem['NAME']?>">
				<?endif;?>
					<img src="<?=$arItem['PICTURE_PREVIEW']['SRC']?>" width="<?=$arItem['PICTURE_PREVIEW']['WIDTH']?>" height="<?=$arItem['PICTURE_PREVIEW']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
				</a>
			</li>
		<?endforeach;?>
	</ul>
</div>