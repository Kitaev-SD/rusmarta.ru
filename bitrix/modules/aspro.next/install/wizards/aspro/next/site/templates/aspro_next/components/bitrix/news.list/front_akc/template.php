<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if($arResult["ITEMS"]){?>
	<div class="news_akc_block clearfix">
		<div class="top_block">
			<?
			$title_block=($arParams["TITLE_BLOCK"] ? $arParams["TITLE_BLOCK"] : GetMessage('AKC_TITLE'));
			$title_all_block=($arParams["TITLE_BLOCK_ALL"] ? $arParams["TITLE_BLOCK_ALL"] : GetMessage('ALL_AKC'));
			$url=($arParams["ALL_URL"] ? $arParams["ALL_URL"] : "sale/");
			$count=ceil(count($arResult["ITEMS"])/4);
			?>
			<h3 class="title_block"><?=$title_block;?></h3>
			<a href="<?=SITE_DIR.$url;?>"><?=$title_all_block;?></a>
		</div>
		<?$col=4;
		if($arParams["LINE_ELEMENT_COUNT"]>=3 && $arParams["LINE_ELEMENT_COUNT"]<4)
			$col=3;?>
		<div class="news_wrapp">
			<div class="flexslider loading_state shadow border custom_flex top_right" data-lg_count="5" data-plugin-options='{"animation": "slide", "directionNav": true, "itemMargin":30, "controlNav" :false, "animationLoop": true, "slideshow": false, "counts": [4,3,3,2,1]}'>
				<ul class="items slides">
					<?foreach($arResult["ITEMS"] as $arItem){
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						$img_source='';
						?>
						<li class="item_block visible">
							<div id="<?=$this->GetEditAreaId($arItem['ID']);?>" class="item inner_wrap">
								<?if($arItem["PREVIEW_PICTURE"]){
									$img_source=$arItem["PREVIEW_PICTURE"];
								}elseif($arItem["DETAIL_PICTURE"]){
									$img_source=$arItem["DETAIL_PICTURE"];
								}?>
								<?if($img_source){?>
									<div class="img shine">
										<?$img = CFile::ResizeImageGet($img_source, array("width" => 400, "height" => 270), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true, false, false, 75 );?>
										<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
											<img src="<?=$img["src"]?>" alt="<?=$arItem["NAME"];?>"  />
										</a>
									</div>
								<?}?>
								<div class="info">
									<?if($arParams["DISPLAY_DATE"]=="Y"){?>
										<?if( $arItem["PROPERTIES"]["PERIOD"]["VALUE"] ){?>
											<div class="date"><?=$arItem["PROPERTIES"]["PERIOD"]["VALUE"]?></div>
										<?}elseif($arItem["DISPLAY_ACTIVE_FROM"]){?>
											<div class="date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></div>
										<?}?>
									<?}?>
									<a class="name dark_link" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
								</div>
							</div>
						</li>
					<?}?>
				</ul>
			</div>
		</div>
	</div>
<?}?>