<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;
?>

<div class="vendors_bottom">
	<div class="h3"><?=GetMessage("VENDORS_TITLE")?></div>
	<a class="all" href="<?=SITE_DIR?>vendors/"><?=GetMessage("ALL_VENDORS")?></a>
	<div class="clr"></div>
	<div class="vendors-section-list">
		<div class="vendors-section-items">
			<?foreach($arResult["ITEMS"] as $arItem):?>
				<div class="vendors-section-item">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<span class="item">
							<span class="image">
								<?if(!empty($arItem["PICTURE_PREVIEW"]["SRC"])):?>
									<img src="<?=$arItem["PICTURE_PREVIEW"]["SRC"]?>" width="<?=$arItem["PICTURE_PREVIEW"]['WIDTH']?>" height="<?=$arItem["PICTURE_PREVIEW"]['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
								<?else:?>
									<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="50px" height="50px" alt="<?=$arItem['NAME']?>" />
								<?endif;?>
							</span>
							<span class="item-title">
								<?=$arItem["NAME"]?>
							</span>
						</span>
					</a>
				</div>
			<?endforeach;?>
			<div class="clr"></div>
		</div>
	</div>
</div>