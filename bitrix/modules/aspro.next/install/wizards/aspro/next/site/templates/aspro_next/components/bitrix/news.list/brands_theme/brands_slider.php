<?
global $arTheme;
$slideshowSpeed = abs(intval($arTheme['PARTNERSBANNER_SLIDESSHOWSPEED']['VALUE']));
$animationSpeed = abs(intval($arTheme['PARTNERSBANNER_ANIMATIONSPEED']['VALUE']));
$bAnimation = (bool)$slideshowSpeed;
?>
<div class="brands_slider_wrapp flexslider loading_state clearfix" data-plugin-options='{"animation": "slide", "directionNav": true, "itemMargin":30, "controlNav" :false, "animationLoop": true, <?=($bAnimation ? '"slideshow": true,' : '"slideshow": false,')?> <?=($slideshowSpeed >= 0 ? '"slideshowSpeed": '.$slideshowSpeed.',' : '')?> <?=($animationSpeed >= 0 ? '"animationSpeed": '.$animationSpeed.',' : '')?> "counts": [5,4,3,2,1]}'>
	<ul class="brands_slider slides">
		<?foreach($arResult["ITEMS"] as $arItem){?>
			<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<?if( is_array($arItem["PREVIEW_PICTURE"]) ){?>
				<li class="visible" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						<img class="noborder" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" title="<?=$arItem["NAME"]?>" />
					</a>
				</li>
			<?}?>
		<?}?>
	</ul>
</div>