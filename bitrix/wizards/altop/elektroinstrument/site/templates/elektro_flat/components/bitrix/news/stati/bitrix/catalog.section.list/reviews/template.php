<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["SECTIONS"]) < 1)
	return;?>

<div class="reviews-section-childs">
	<?foreach($arResult["SECTIONS"] as $arSection) {?>
		<div class="reviews-section-child">
			<a href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$arSection['NAME']?>">
				<span class="child">
					<span class="graph">
						<?if(is_array($arSection["PICTURE"])) {?>
							<img src="<?=$arSection['PICTURE']['SRC']?>" width="<?=$arSection['PICTURE']['WIDTH']?>" height="<?=$arSection['PICTURE']['HEIGHT']?>" alt="<?=$arSection['NAME']?>" />
						<?} else {?>
							<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="50" height="50" alt="<?=$arSection['NAME']?>" />
						<?}?>
					</span>
					<span class="text-cont">
						<span class="text"><?=$arSection["NAME"]?></span>
					</span>
				</span>
			</a>
		</div>
	<?}?>	
</div>