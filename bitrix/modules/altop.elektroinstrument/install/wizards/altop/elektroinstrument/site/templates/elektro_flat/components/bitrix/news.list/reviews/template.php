<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;?>

<div class="reviews-block">
	<div class="reviews-block__title"><?=GetMessage("REVIEWS_TITLE")?></div>
	<a class="reviews-block__all-reviews top" href="<?=str_replace('#SITE_DIR#', SITE_DIR, $arResult['LIST_PAGE_URL']);?>"><?=GetMessage("ALL_REVIEWS")?></a>
	<div class="reviews-block__items"> 
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<a class="reviews-block__item" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
				<span class="reviews-block__item-block">
					<span class="reviews-block__item-image"<?=($arItem["PREVIEW_PICTURE"]["SRC"] ? " style=\"background-image:url('".$arItem["PREVIEW_PICTURE"]["SRC"]."');\"" : "");?>></span>
				</span>
				<span class="reviews-block__item-block">
					<span class="reviews-block__item-text"><?=$arItem["NAME"]?></span>
				</span>
			</a>
		<?endforeach;?>
	</div>
	<a class="reviews-block__all-reviews bottom" href="<?=str_replace('#SITE_DIR#', SITE_DIR, $arResult['LIST_PAGE_URL']);?>"><?=GetMessage("ALL_REVIEWS")?></a>
</div>