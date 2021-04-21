<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;?>

<div class="banners_left">
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<a href="<?=(!empty($arItem['DISPLAY_PROPERTIES']['URL'])) ? $arItem['DISPLAY_PROPERTIES']['URL']['VALUE'] : 'javascript:void(0)'?>">
			<img src="<?=$arItem['PICTURE_PREVIEW']['SRC']?>" width="<?=$arItem['PICTURE_PREVIEW']['WIDTH']?>" height="<?=$arItem['PICTURE_PREVIEW']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
		</a>
	<?endforeach;?>
</div>