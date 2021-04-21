<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;?>

<div class="news-block">
	<div class="news-block__title"><?=GetMessage("NEWS_TITLE")?></div>
	<a class="news-block__all-news top" href="<?=str_replace('#SITE_DIR#', SITE_DIR, $arResult['LIST_PAGE_URL']);?>"><?=GetMessage("ALL_NEWS")?></a>
	<div class="news-block__items"> 
		<?foreach($arResult["ITEMS"] as $arItem):?>			
			<a class="news-block__item" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
				<span class="news-block__item-block">
					<span class="news-block__item-image"<?=($arItem["PREVIEW_PICTURE"]["SRC"] ? " style=\"background-image:url('".$arItem["PREVIEW_PICTURE"]["SRC"]."');\"" : "");?>></span>
				</span>
				<span class="news-block__item-block">
					<?if($arItem["DISPLAY_ACTIVE_FROM"]):?>
						<span class="news-block__item-date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></span>
					<?endif;?>
					<span class="news-block__item-text"><?=$arItem["NAME"]?></span>
				</span>
			</a>
		<?endforeach;?>
	</div>
	<a class="news-block__all-news bottom" href="<?=str_replace('#SITE_DIR#', SITE_DIR, $arResult['LIST_PAGE_URL']);?>"><?=GetMessage("ALL_NEWS")?></a>
</div>