<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true );?>
<?if($arResult['SECTIONS']):?>
	<?global $arTheme;
	$iVisibleItemsMenu = ($arTheme['MAX_VISIBLE_ITEMS_MENU']['VALUE'] ? $arTheme['MAX_VISIBLE_ITEMS_MENU']['VALUE'] : 10);
	?>
	<div class="sections_wrapper">
		<?if($arParams["TITLE_BLOCK"] || $arParams["TITLE_BLOCK_ALL"]):?>
			<div class="top_block">
				<h3 class="title_block"><?=$arParams["TITLE_BLOCK"];?></h3>
				<a href="<?=SITE_DIR.$arParams["ALL_URL"];?>"><?=$arParams["TITLE_BLOCK_ALL"] ;?></a>
			</div>
		<?endif;?>
		<div class="list items catalog_section_list">
			<div class="row margin0 flexbox">
				<?foreach($arResult['SECTIONS'] as $arSection):
					$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'ELEMENT_EDIT'));
					$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
					<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
						<div class="item section_item" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
							<div class="section_item_inner">
								<div class="img">
									<?if(is_array($arSection['PICTURE']) && $arSection['PICTURE']['SRC']):?>
										<?$img = CFile::ResizeImageGet($arSection['PICTURE']['ID'], array( "width" => 120, "height" => 120 ), BX_RESIZE_IMAGE_EXACT, true );?>
										<a href="<?=$arSection['SECTION_PAGE_URL']?>" class="thumb"><img src="<?=$img['src']?>" alt="<?=($arSection['PICTURE']['ALT'] ? $arSection['PICTURE']['ALT'] : $arSection['NAME'])?>" title="<?=($arSection['PICTURE']['TITLE'] ? $arSection['PICTURE']['TITLE'] : $arSection['NAME'])?>" /></a>
									<?elseif($arSection['~PICTURE']):?>
										<?$img = CFile::ResizeImageGet($arSection['~PICTURE'], array( "width" => 120, "height" => 120 ), BX_RESIZE_IMAGE_EXACT, true );?>
										<a href="<?=$arSection['SECTION_PAGE_URL']?>" class="thumb"><img src="<?=$img['src']?>" alt="<?=$arSection['NAME']?>" title="<?=$arSection['NAME']?>" /></a>
									<?else:?>
										<a href="<?=$arSection['SECTION_PAGE_URL']?>" class="thumb"><img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arSection['NAME']?>" title="<?=$arSection['NAME']?>" /></a>
									<?endif;?>
								</div>
								<div class="section_info toggle">
									<ul>
										<li class="name">
											<a href="<?=$arSection['SECTION_PAGE_URL']?>" class="dark_link"><span><?=$arSection['NAME']?></span></a>
										</li>
										<?if($arSection['ITEMS']):
											$iCountChilds = count($arSection['ITEMS']);
											foreach($arSection['ITEMS'] as $key => $arItem):?>
												<li class="sect <?=(++$key > $iVisibleItemsMenu ? 'collapsed' : '');?>"><a href="<?=$arItem['SECTION_PAGE_URL']?>" class="dark_link"><?=$arItem['NAME']?><? echo $arItem['ELEMENT_CNT']?'&nbsp;<span>'.$arItem['ELEMENT_CNT'].'</span>':'';?></a></li>
											<?endforeach;?>
											<?if($iCountChilds > $iVisibleItemsMenu):?>
												<li class="sect"><span class="colored more_items with_dropdown" data-resize="Y"><?=\Bitrix\Main\Localization\Loc::getMessage('S_MORE_ITEMS');?></span></li>
											<?endif;?>
										<?endif;?>
									</ul>
								</div>
							</div>
						</div>
					</div>
				<?endforeach;?>
			</div>
		</div>
	</div>
<?endif;?>