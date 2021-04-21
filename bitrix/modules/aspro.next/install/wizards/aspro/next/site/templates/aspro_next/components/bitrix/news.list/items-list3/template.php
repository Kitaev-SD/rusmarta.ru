<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>
<div class="item-views table-type-block <?=$templateName;?>">
	<?// top pagination?>
	<?if($arParams['DISPLAY_TOP_PAGER']):?>
		<?=$arResult['NAV_STRING']?>
	<?endif;?>
	<div class="group-content">
		<?$arParams['COUNT_IN_LINE'] = ($arParams['COUNT_IN_LINE'] <=0 ? 3 : $arParams['COUNT_IN_LINE']);
		$line_element_count = ($arParams['COUNT_IN_LINE'] >= 6 ? 4 : $arParams['COUNT_IN_LINE']);
		?>
		<?// group elements by sections?>
		<?foreach($arResult['SECTIONS'] as $SID => $arSection):?>
			<?
			// edit/add/delete buttons for edit mode
			$arSectionButtons = CIBlock::GetPanelButtons($arSection['IBLOCK_ID'], 0, $arSection['ID'], array('SESSID' => false, 'CATALOG' => true));
			$this->AddEditAction($arSection['ID'], $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_EDIT'));
			$this->AddDeleteAction($arSection['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<div id="<?=$this->GetEditAreaId($arSection['ID'])?>" class="tab-pane <?=(!$si++ || !$arSection['ID'] ? 'active' : '')?>">

				<?if($arParams['SHOW_SECTION_PREVIEW_DESCRIPTION'] == 'Y'):?>
					<?if($arParams['SHOW_SECTION_NAME'] != 'N'):?>
						<?// section name?>
						<?if(strlen($arSection['NAME'])):?>
							<h3><?=$arSection['NAME']?></h3>
						<?endif;?>
					<?endif;?>

					<?// section description text/html?>
					<?if(strlen($arSection['DESCRIPTION']) && strpos($_SERVER['REQUEST_URI'], 'PAGEN') === false):?>
						<div class="text_before_items">
							<?=$arSection['DESCRIPTION']?>
						</div>
						<?if($arParams['SHOW_SECTION_DESC_DIVIDER'] == 'Y'):?>
							<hr class="sect-divider" />
						<?endif;?>
					<?endif;?>
				<?endif;?>

				<div class="row items">
					<?foreach($arSection['ITEMS'] as $i => $arItem):?>
						<?
						// edit/add/delete buttons for edit mode
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
						// use detail link?
						$bDetailLink = $arParams['SHOW_DETAIL_LINK'] != 'N' && (!strlen($arItem['DETAIL_TEXT']) ? ($arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' && $arParams['HIDE_LINK_WHEN_NO_DETAIL'] != 1) : true);
						// show preview picture?
						$bImage = isset($arItem['FIELDS']['PREVIEW_PICTURE']) && strlen($arItem['PREVIEW_PICTURE']['SRC']);
						$imageSrc = ($bImage ? $arItem['PREVIEW_PICTURE']['SRC'] : false);
						$imageDetailSrc = ($bImage ? $arItem['DETAIL_PICTURE']['SRC'] : false);
						?>

						<div class="shadow col-md-<?=floor(12 / $line_element_count)?> col-sm-<?=floor(12 / round($line_element_count / 2))?>">
							<div class="item clearfix" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
								<div class="image">
									<?if($bDetailLink):?>
										<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
									<?endif;?>
										<img src="<?=$imageSrc?>" alt="<?=($bImage ? $arItem['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?=($bImage ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" class="img-responsive" />
									<?if($bDetailLink):?>
										</a>
									<?endif;?>
								</div>
							</div>
						</div>
					<?endforeach;?>
				</div>
			</div>
		<?endforeach;?>
	</div>

	<?// bottom pagination?>
	<?if($arParams['DISPLAY_BOTTOM_PAGER'] && $arResult['NAV_STRING']):?>
		<hr />
		<?=$arResult['NAV_STRING']?>
	<?endif;?>
</div>