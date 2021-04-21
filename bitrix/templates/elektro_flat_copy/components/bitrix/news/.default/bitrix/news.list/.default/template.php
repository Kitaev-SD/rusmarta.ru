<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;?>

<div class="news-list">
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<a class="news__item" href="<?=$arItem['DETAIL_PAGE_URL']?>">
			<span class="news__item-image-wrap">
				<span class="news__item-image"<?=(is_array($arItem["PREVIEW_PICTURE"]) ? " style=\"background-image:url('".$arItem["PREVIEW_PICTURE"]["SRC"]."');\"" : "");?>></span>
			</span>
			<span class="news__item-block">
				<?if(!empty($arItem["DISPLAY_ACTIVE_FROM"]) || (isset($arItem["DISPLAY_DATE_CREATE"]) && !empty($arItem["DISPLAY_DATE_CREATE"]))):?>
					<span class="news__item-date"><?=(!empty($arItem["DISPLAY_ACTIVE_FROM"]) ? $arItem["DISPLAY_ACTIVE_FROM"] : $arItem["DISPLAY_DATE_CREATE"]);?></span>
				<?endif;?>
				<span class="news__item-title"><?=$arItem["NAME"]?></span>				
				<span class="news__item-text"><?=$arItem["PREVIEW_TEXT"]?></span>
			</span>
		</a>
	<?endforeach;?>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):
	echo $arResult["NAV_STRING"];
endif;?>