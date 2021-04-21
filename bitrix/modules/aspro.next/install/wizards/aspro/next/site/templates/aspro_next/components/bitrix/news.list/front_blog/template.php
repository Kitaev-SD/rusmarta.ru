<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if($arResult["ITEMS"]):?>
	<div class="blog_wrapper banners-small blog">
		<?if($arParams["TITLE_BLOCK"] || $arParams["TITLE_BLOCK_ALL"]):?>
			<div class="top_block">
				<h3 class="title_block"><?=$arParams["TITLE_BLOCK"];?></h3>
				<a href="<?=SITE_DIR.$arParams["ALL_URL"];?>"><?=$arParams["TITLE_BLOCK_ALL"] ;?></a>
			</div>
		<?endif;?>
		<div class="items">
			<div class="row">
				<?foreach($arResult["ITEMS"] as $key => $arItem)
				{
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					$picture = '';
					if(is_array($arItem["PREVIEW_PICTURE"])):
						$picture = $arItem["PREVIEW_PICTURE"]["SRC"];
					elseif(is_array($arItem["DETAIL_PICTURE"])):
						$picture = $arItem["DETAIL_PICTURE"]["SRC"];
					endif;
					?>
					<div class="col-m-<?=(!$key ? '40 first-item' : '20');?> col-md-4 col-sm-6 col-xs-6">
						<div class="item shadow animation-boxs <?=($picture && !$key ? 'shine' : '');?>" <?=($picture && !$key ? "style='background-image:url(".$picture.")'" : "");?> id="<?=$this->GetEditAreaId($arItem['ID']);?>">
							<div class="inner-item">
								<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="gradient_block"></a>
								<?if($picture):?>
									<div class="image shine">
										<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
											<?if(is_array($arItem["PREVIEW_PICTURE"])):?>
												<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=($arItem["PREVIEW_PICTURE"]["ALT"]?$arItem["PREVIEW_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["PREVIEW_PICTURE"]["TITLE"]?$arItem["PREVIEW_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
												
											<?elseif(is_array($arItem["DETAIL_PICTURE"])):?>
												<img src="<?=$arItem["DETAIL_PICTURE"]["SRC"]?>" alt="<?=($arItem["DETAIL_PICTURE"]["ALT"]?$arItem["DETAIL_PICTURE"]["ALT"]:$arItem["NAME"]);?>" title="<?=($arItem["DETAIL_PICTURE"]["TITLE"]?$arItem["DETAIL_PICTURE"]["TITLE"]:$arItem["NAME"]);?>" />
											<?endif;?>
										</a>
									</div>
								<?endif;?>
								<div class="title">
									<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span><?=$arItem["NAME"]?></span></a>
									<?if($arItem['DISPLAY_ACTIVE_FROM']):?>
										<div class="date-block"><?=$arItem['DISPLAY_ACTIVE_FROM'];?></div>
									<?endif;?>
								</div>
							</div>
						</div>
					</div>
				<?}?>
			</div>
		</div>
	</div>
<?endif;?>