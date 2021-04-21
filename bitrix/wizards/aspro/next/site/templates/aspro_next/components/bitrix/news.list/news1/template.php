<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?if($arResult['ITEMS']):?>
	<?$isAjax = (isset($_GET["AJAX_REQUEST"]) && $_GET["AJAX_REQUEST"] == "Y");?>
	<?$isWideImg = (isset($arParams['IMAGE_WIDE']) && $arParams['IMAGE_WIDE'] == 'Y');?>
	<?if(!$isAjax):?>
	<div class="item-views list list-type-block wide_img <?=($arParams['IMAGE_POSITION'] ? 'image_'.$arParams['IMAGE_POSITION'] : '')?> <?=($templateName = $component->{'__parent'}->{'__template'}->{'__name'})?>">

		<?// top pagination?>
		<?if($arParams['DISPLAY_TOP_PAGER']):?>
			<?=$arResult['NAV_STRING']?>
		<?endif;?>

		<?
		$bHasSection = false;
		if($arParams['PARENT_SECTION'] && (isset($arResult['SECTIONS']) && $arResult['SECTIONS']))
		{
			if(isset($arResult['SECTIONS'][$arParams['PARENT_SECTION']]) && $arResult['SECTIONS'][$arParams['PARENT_SECTION']])
				$bHasSection = true;
		}
		if($bHasSection)
		{
			// edit/add/delete buttons for edit mode
			$arSectionButtons = CIBlock::GetPanelButtons($arResult['SECTIONS'][$arParams['PARENT_SECTION']]['IBLOCK_ID'], 0, $arResult['SECTIONS'][$arParams['PARENT_SECTION']]['ID'], array('SESSID' => false, 'CATALOG' => true));
			$this->AddEditAction($arResult['SECTIONS'][$arParams['PARENT_SECTION']]['ID'], $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arResult['SECTIONS'][$arParams['PARENT_SECTION']]['IBLOCK_ID'], 'SECTION_EDIT'));
			$this->AddDeleteAction($arResult['SECTIONS'][$arParams['PARENT_SECTION']]['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arResult['SECTIONS'][$arParams['PARENT_SECTION']]['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<div class="section" id="<?=$this->GetEditAreaId($arResult['SECTIONS'][$arParams['PARENT_SECTION']]['ID'])?>">
			<?
		}?>
		<div class="items row">
			<?// show section items?>
	<?endif;?>
			<?
				$count=count($arResult['ITEMS']);
				$current=0;
			?>
			<?foreach($arResult['ITEMS'] as $i => $arItem):?>
				<?
				$current++;
				// edit/add/delete buttons for edit mode
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				// use detail link?
				$bDetailLink = $arParams['SHOW_DETAIL_LINK'] != 'N' && (!strlen($arItem['DETAIL_TEXT']) ? ($arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' && $arParams['HIDE_LINK_WHEN_NO_DETAIL'] != 1) : true);
				$bImage = isset($arItem['FIELDS']['PREVIEW_PICTURE']) && strlen($arItem['PREVIEW_PICTURE']['SRC']);
				$imageSrc = ($bImage ? $arItem['PREVIEW_PICTURE']['SRC'] : false);
				$imageDetailSrc = ($bImage ? $arItem['DETAIL_PICTURE']['SRC'] : false);
				// show active date period
				$bActiveDate = strlen($arItem['DISPLAY_PROPERTIES']['PERIOD']['VALUE']) || ($arItem['DISPLAY_ACTIVE_FROM'] && in_array('DATE_ACTIVE_FROM', $arParams['FIELD_CODE']));
				?>
				<div class="col-md-12">
					<div class="item noborder<?=($bImage ? '' : ' wti')?><?=($bActiveDate ? ' wdate' : '')?> clearfix" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
						<?if($bImage):?>
							<div class="image shine">
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
								<div class="title">
									<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="dark-color"><?endif;?>
										<?=$arItem['NAME']?>
									<?if($bDetailLink):?></a><?endif;?>
								</div>
							<?endif;?>

							<?// date active period?>
							<?if($bActiveDate):?>
								<div class="period">
									<?if(strlen($arItem['DISPLAY_PROPERTIES']['PERIOD']['VALUE'])):?>
										<span class="date"><?=$arItem['DISPLAY_PROPERTIES']['PERIOD']['VALUE']?></span>
									<?else:?>
										<span class="date"><?=$arItem['DISPLAY_ACTIVE_FROM']?></span>
									<?endif;?>
								</div>
							<?endif;?>

							<?// section title?>
							<?if(strlen($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['NAME']) && $isWideImg):?>
								<span class="section_name">
									//&nbsp;<?=$arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['NAME']?>
								</span>
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
								<div class="properties">
									<?foreach($arItem['DISPLAY_PROPERTIES'] as $PCODE => $arProperty):?>
										<?if(in_array($PCODE, array('PERIOD', 'TITLE_BUTTON', 'LINK_BUTTON'))) continue;?>
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
													<a href="<?=(strpos($arProperty['VALUE'], 'http') === false ? 'http://' : '').$arProperty['VALUE'];?>" rel="nofollow" target="_blank">
														<?=$arProperty['VALUE'];?>
													</a>
													<!--/noindex-->
												<?elseif($PCODE == 'EMAIL'):?>
													<a href="mailto:<?=$val?>"><?=$val?></a>
												<?else:?>
													<?=$val?>
												<?endif;?>
											</div>
										</div>
									<?endforeach;?>
								</div>
							<?endif;?>

							<?if($bDetailLink):?>
								<div class="link-block-more">
									<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="btn-inline sm rounded black"><?=GetMessage('TO_ALL')?><i class="fa fa-angle-right"></i></a>
								</div>
							<?endif;?>
						</div>
					</div>
					<?//if($current<$count):?>
						<hr />
					<?//endif;?>
				</div>
			<?endforeach;?>
		<?if(!$isAjax):?>
		</div>
		<?if($bHasSection):?>
			</div>
		<?endif;?>
		<?endif;?>

		<?// bottom pagination?>
		<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
			<div class="bottom_nav" <?=($isAjax ? "style='display: none; '" : "");?>><?=$arResult['NAV_STRING']?></div>
		<?endif;?>
	<?if(!$isAjax):?>
	</div>
	<?endif;?>
<?endif;?>
