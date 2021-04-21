<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;?>

<div class="reviews-list">
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<a class="reviews__item" href="<?=$arItem['DETAIL_PAGE_URL']?>">			
			<span class="reviews__item-image-wrap">
				<span class="reviews__item-image"<?=(is_array($arItem["PREVIEW_PICTURE"]) ? " style=\"background-image:url('".$arItem["PREVIEW_PICTURE"]["SRC"]."');\"" : "");?>></span>
			</span>			
			<span class="reviews__item-block">
				<span class="reviews__item-title"><?=$arItem["NAME"]?></span>				
				<span class="reviews__item-text"><?=$arItem["PREVIEW_TEXT"]?></span>
			</span>
		</a>
	<?endforeach;?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):
	echo $arResult["NAV_STRING"];
endif;?>