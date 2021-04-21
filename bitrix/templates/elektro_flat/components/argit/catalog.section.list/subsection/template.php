<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["SECTIONS"]) < 1)
	return;?>

<div class="catalog-section-list">
	<div class="catalog-section">		
		<div class="catalog-section-childs">
			<?foreach($arResult["SECTIONS"] as $arSection):?>
				<div class="catalog-section-child">
					<a rel="nofollow" href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$arSection['NAME']?>">
						<span class="child">
							<span class="image">
								<?if(is_array($arSection["PICTURE"])):?>
									<img class="data-lazy-src" data-lazy-src="<?=$arSection['PICTURE']['SRC']?>" width="<?=$arSection['PICTURE']['WIDTH']?>" height="<?=$arSection['PICTURE']['HEIGHT']?>" title="<?=$arSection['NAME']?>" />
								<?else:?>
									<img class="data-lazy-src" data-lazy-src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="50" height="50" title="<?=$arSection['NAME']?>" /> 
								<?endif;?>
							</span>
							<span class="text-cont">
								<span class="text"><?=$arSection["NAME"]?></span>
							</span>
						</span>
					</a>
				</div>				
			<?endforeach;?>
			<div class="clr"></div>
		</div>
	</div>
</div>