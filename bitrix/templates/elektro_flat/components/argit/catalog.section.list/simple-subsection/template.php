<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["SECTIONS"]) < 1)
	return;?>

<div class="catalog-section-list">
	<div class="catalog-section">		
		<div class="catalog-section-childs bottom-tags show-more-subcategories">
			<?foreach($arResult["SECTIONS"] as $arSection):?>
				<div class="bottom-tag">
					<a rel="nofollow" href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$arSection['NAME']?>">
						<span class="child">
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
