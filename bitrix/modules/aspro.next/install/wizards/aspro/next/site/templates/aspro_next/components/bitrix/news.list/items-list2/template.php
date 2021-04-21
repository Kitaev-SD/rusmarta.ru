<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>
<div class="item-views list list-type-block <?=($arParams['IMAGE_POSITION'] ? 'image_'.$arParams['IMAGE_POSITION'] : '')?> <?=((isset($arParams['IS_STAFF']) && $arParams['IS_STAFF'] == 'Y') ? 'staff-block' : '')?> staff <?=$templateName;?>">
	<?// top pagination?>
	<?if($arParams['DISPLAY_TOP_PAGER']):?>
		<?=$arResult['NAV_STRING']?>
	<?endif;?>
	<div class="group-content">
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

					<div class="item shadow <?=(isset($arParams['IMG_PADDING']) && $arParams['IMG_PADDING'] == 'Y' ? 'padding-img' : '');?> <?=($bImage ? '' : ' wti')?> clearfix" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
						<?if($bImage):?>
							<div class="image <?=(isset($arParams['IMG_PADDING']) && $arParams['IMG_PADDING'] == 'Y' ? 'padding' : '');?>">
								<?if($bDetailLink):?>
									<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
								<?endif;?>
									<img src="<?=$imageSrc?>" alt="<?=($bImage ? $arItem['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?=($bImage ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" class="img-responsive" />
								<?if($bDetailLink):?>
									</a>
								<?endif;?>
							</div>
						<?endif;?>
						<div class="body-info">
							<?// element name?>
							<?if(strlen($arItem['FIELDS']['NAME'])):?>
								<?$bHasSocProps = (isset($arItem['SOCIAL_PROPS']) && $arItem['SOCIAL_PROPS']);?>
								<div class="title-wrapper <?=($bHasSocProps ? 'bottom-props' : '');?>">
									<div class="title">
										<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="dark-color"><?endif;?>
											<?=$arItem['NAME']?>
										<?if($bDetailLink):?></a><?endif;?>
									</div>
									<?if($bHasSocProps):?>
										<!-- noindex -->
											<?foreach($arItem['SOCIAL_PROPS'] as $arProp):?>
												<a href="<?=$arProp['VALUE'];?>" target="_blank" rel="nofollow" class="value <?=strtolower($arProp['CODE']);?>"><?=$arProp['VALUE'];?></a>
											<?endforeach;?>
										<!-- /noindex -->
									<?endif;?>
								</div>
							<?endif;?>

							<?// element post?>
							<?if(strlen($arItem['DISPLAY_PROPERTIES']['POST']['VALUE'])):?>
								<div class="post"><?=$arItem['DISPLAY_PROPERTIES']['POST']['VALUE']?></div>
								<?unset($arItem['DISPLAY_PROPERTIES']['POST']);?>
							<?endif;?>

							<?// element preview text?>
							<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT'])):?>
								<div class="previewtext">
									<?if($arItem['PREVIEW_TEXT_TYPE'] == 'text'):?>
										<p><?=$arItem['FIELDS']['PREVIEW_TEXT']?></p>
									<?else:?>
										<?=$arItem['FIELDS']['PREVIEW_TEXT']?>
									<?endif;?>
								</div>
							<?endif;?>

							<?// element display properties?>
							<?if($arItem['DISPLAY_PROPERTIES']):?>
								<hr/>
								<div class="properties">
									<?foreach($arItem['DISPLAY_PROPERTIES'] as $PCODE => $arProperty):?>
										<?$bIconBlock = ($PCODE == 'EMAIL' || $PCODE == 'PHONE' || $PCODE == 'SITE');?>
										<div class="inner-wrapper">
											<div class="property <?=($bIconBlock ? "icon-block" : "");?> <?=strtolower($PCODE);?>">
												<?if(!$bIconBlock):?>
													<?=$arProperty['NAME']?>:&nbsp;
												<?endif;?>
												<?if(is_array($arProperty['DISPLAY_VALUE'])):?>
													<?$val = implode('&nbsp;/&nbsp;', $arProperty['DISPLAY_VALUE']);?>
												<?else:?>
													<?$val = $arProperty['DISPLAY_VALUE'];?>
												<?endif;?>
												<?if($PCODE == 'SITE'):?>
													<!--noindex-->
													<?=str_replace("href=", "rel='nofollow' class='colored' target='_blank' href=", $val);?>
													<!--/noindex-->
												<?elseif($PCODE == 'EMAIL'):?>
													<a class="colored" href="mailto:<?=$val?>"><?=$val?></a>
												<?else:?>
													<?=$val?>
												<?endif;?>
											</div>
										</div>
									<?endforeach;?>
								</div>
							<?endif;?>
						</div>
					</div>
			<?endforeach;?>
			</div>
		<?endforeach;?>
	</div>

	<?// bottom pagination?>
	<?if($arParams['DISPLAY_BOTTOM_PAGER'] && $arResult['NAV_STRING']):?>
		<hr />
		<?=$arResult['NAV_STRING']?>
	<?endif;?>
</div>