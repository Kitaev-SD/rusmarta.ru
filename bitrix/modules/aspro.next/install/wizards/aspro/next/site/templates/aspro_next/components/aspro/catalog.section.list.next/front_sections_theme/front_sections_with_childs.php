<?
$arRootItems = $arChildItems = array();
foreach($arResult['SECTIONS'] as $key => $arSection)
{
	if($arSection['DEPTH_LEVEL'] == 1)
		$arRootItems[$arSection['ID']] = $arSection;
	else
		$arChildItems[$arSection['ID']] = $arSection;
	unset($arResult['SECTIONS'][$key]);
}
if($arChildItems)
{
	foreach($arChildItems as $key => $arSection)
	{
		$arRootSection = CNextCache::CIBlockSection_GetList(array('CACHE' => array('MULTI' =>'N', 'TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))), array('GLOBAL_ACTIVE' => 'Y', '<=LEFT_BORDER' => $arSection['LEFT_MARGIN'], '>=RIGHT_BORDER' => $arSection['RIGHT_MARGIN'], 'DEPTH_LEVEL' => 1, 'IBLOCK_ID' => $arParams['IBLOCK_ID']), false, array('ID', 'NAME', 'SORT', 'SECTION_PAGE_URL', 'PICTURE'));
		if(!isset($arRootItems[$arRootSection['ID']]))
			$arRootItems[$arRootSection['ID']] = $arRootSection;
	}
}
\Bitrix\Main\Type\Collection::sortByColumn($arRootItems, array('SORT' => array(SORT_NUMERIC, SORT_ASC), 'ID' => array(SORT_NUMERIC, SORT_ASC)));
foreach($arRootItems as $key => $arSection)
{
	$arSections = CNextCache::CIBlockSection_GetList(array('SORT' => 'ASC', 'ID' => 'ASC', 'CACHE' => array('MULTI' =>'Y', 'TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))), array('GLOBAL_ACTIVE' => 'Y', 'SECTION_ID' => $arSection['ID'], 'DEPTH_LEVEL' => 2, 'IBLOCK_ID' => $arParams['IBLOCK_ID']), $arParams['COUNT_ELEMENTS'], array('ID', 'NAME', 'SORT', 'SECTION_PAGE_URL'));
	$arRootItems[$key]['ITEMS'] = $arSections;
}
global $arTheme;
$iVisibleItemsMenu = ($arTheme['MAX_VISIBLE_ITEMS_MENU']['VALUE'] ? $arTheme['MAX_VISIBLE_ITEMS_MENU']['VALUE'] : 10);
?>
<div class="list items catalog_section_list">
	<div class="row margin0 flexbox">
		<?foreach($arRootItems as $arSection):
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
		<?if(isset($arParams["COMPACT_VIEW_MOBILE"]) && $arParams["COMPACT_VIEW_MOBILE"]=="Y" && ($arParams["TITLE_BLOCK"] || $arParams["TITLE_BLOCK_ALL"])):?>
			<div class="visible-xs col-xs-<?=(isset($arParams["COMPACT_VIEW_MOBILE"]) && $arParams["COMPACT_VIEW_MOBILE"]=="Y") ? "12" : "6"?>">
				<div class="item" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
					<div class="name no-img">
						<a href="<?=SITE_DIR.$arParams["ALL_URL"];?>" class="dark_link"><?=$arParams["TITLE_BLOCK_ALL"] ;?></a>
					</div>
				</div>
			</div>
		<?endif;?>
	</div>
</div>