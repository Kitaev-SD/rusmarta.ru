<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>
<?if($arResult['SECTIONS']):?>
	<?if($arParams['SHOW_TITLE'] == 'Y'):?>
		<div class="title-tab-heading visible-xs"><?=$arParams["T_TITLE"];?></div>
	<?endif;?>
<div class="item-views <?=$arParams['VIEW_TYPE']?> <?=$arParams['VIEW_TYPE']?>-type-block <?=($arParams['SHOW_TABS'] == 'Y' ? 'with_tabs' : '')?> <?=($arParams['IMAGE_POSITION'] ? 'image_'.$arParams['IMAGE_POSITION'] : '')?> staff <?=$templateName;?>">
	<?// top pagination?>
	<?if($arParams['DISPLAY_TOP_PAGER']):?>
		<?=$arResult['NAV_STRING']?>
	<?endif;?>

		<?// tabs?>
		<?if($arParams['SHOW_TABS'] == 'Y'):?>
			<div class="tabs">
				<ul class="nav nav-tabs">
					<?$i = 0;?>
					<?foreach($arResult['SECTIONS'] as $SID => $arSection):?>
						<?if(!$SID) continue;?>
						<li class="<?=$i++ == 0 ? 'active' : ''?>"><a data-toggle="tab" href="#<?=$this->GetEditAreaId($arSection['ID'])?>"><?=$arSection['NAME']?></a></li>
					<?endforeach;?>
				</ul>
		<?endif;?>
				<?$arParams['COUNT_IN_LINE'] = ($arParams['COUNT_IN_LINE'] <=0 ? 3 : $arParams['COUNT_IN_LINE']);
				$line_element_count = ($arParams['COUNT_IN_LINE'] >= 6 ? 4 : $arParams['COUNT_IN_LINE']);
				?>
				<div class="<?=($arParams['SHOW_TABS'] == 'Y' ? 'tab-content' : 'group-content')?>">
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

							<?// show section items?>
							<?if($arParams['VIEW_TYPE'] !== 'accordion'):?>
								<div class="row sid items">
							<?endif;?>
								<?foreach($arSection['ITEMS'] as $i => $arItem):?>
									<?
									// edit/add/delete buttons for edit mode
									$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
									$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
									// use detail link?
									$bDetailLink = $arParams['SHOW_DETAIL_LINK'] != 'N' && (!strlen($arItem['DETAIL_TEXT']) ? ($arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' && $arParams['HIDE_LINK_WHEN_NO_DETAIL'] != 1) : true);
									// preview picture
									$bImage = isset($arItem['FIELDS']['PREVIEW_PICTURE']) && strlen($arItem['PREVIEW_PICTURE']['SRC']);
									$imageSrc = ($bImage ? $arItem['PREVIEW_PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/svg/Staff_noimage2.svg');
									$imageDetailSrc = ($bImage ? $arItem['DETAIL_PICTURE']['SRC'] : false);
									// show active date period
									$bActiveDate = strlen($arItem['DISPLAY_PROPERTIES']['PERIOD']['VALUE']) || ($arItem['DISPLAY_ACTIVE_FROM'] && in_array('DATE_ACTIVE_FROM', $arParams['FIELD_CODE']));
									?>

									<?ob_start();?>
										<div class="top-block-wrapper">
											<?// element name?>
											<?if(strlen($arItem['FIELDS']['NAME'])):?>
												<div class="title">
													<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?endif;?>
														<?=$arItem['NAME']?>
													<?if($bDetailLink):?></a><?endif;?>
												</div>
											<?endif;?>

											<?// post?>
												<?if((isset($arItem['PROPERTIES']['POST']) && $arItem['PROPERTIES']['POST']) && (isset($arItem['PROPERTIES']['POST']['VALUE']) && $arItem['PROPERTIES']['POST']['VALUE'])):?>
													<div class="post"><?=$arItem['PROPERTIES']['POST']['VALUE'];?></div>
												<?endif;?>
										</div>

										<?// element preview text?>
										<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT']) || strlen($arItem['FIELDS']['DETAIL_TEXT'])):?>
											<div class="previewtext">
												<div>
													<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT'])):?>
														<?if($arItem['PREVIEW_TEXT_TYPE'] == 'text'):?>
															<p><?=$arItem['FIELDS']['PREVIEW_TEXT']?></p>
														<?else:?>
															<?=$arItem['FIELDS']['PREVIEW_TEXT']?>
														<?endif;?>
													<?endif;?>
												</div>
											</div>
										<?endif;?>

										<?// element display properties?>
										<?if((isset($arItem['MIDDLE_PROPS']) && $arItem['MIDDLE_PROPS'])):?>
											<div class="middle-props">
												<?foreach($arItem['MIDDLE_PROPS'] as $key => $arProp):?>
													<div class="value"><?if($key == 'EMAIL'):?><!-- noindex --><a href="mailto:<?=$arProp['VALUE'];?>" target="_blank" rel="nofollow"><?endif;?><?=$arProp['VALUE'];?><?if($key == 'EMAIL'):?></a><!-- /noindex --><?endif;?></div>
												<?endforeach;?>
											</div>
										<?endif;?>

										<?if((isset($arItem['SOCIAL_PROPS']) && $arItem['SOCIAL_PROPS'])):?>
											<div class="bottom-props">
												<!-- noindex -->
													<?foreach($arItem['SOCIAL_PROPS'] as $arProp):?>
														<a href="<?=$arProp['VALUE'];?>" target="_blank" rel="nofollow" class="value <?=strtolower($arProp['CODE']);?>"><?=$arProp['VALUE'];?></a>
													<?endforeach;?>
												<!-- /noindex -->
											</div>
										<?endif;?>
									<?$textPart = ob_get_clean();?>

									<?ob_start();?>
										<?if($imageSrc):?>
											<div class="image <?=($bImage ? '' : 'wpi')?>">
												<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?endif;?>
													<img src="<?=$imageSrc?>" alt="<?=($bImage ? $arItem['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?=($bImage ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" class="img-responsive" />
												<?if($bDetailLink):?></a><?endif;?>
											</div>
										<?endif;?>
									<?$imagePart = ob_get_clean();?>

									<?if($arParams['VIEW_TYPE'] == 'list'):?>
										<div class="col-md-12">
											<div class="item<?=($imageSrc ? '' : ' wti')?> clearfix noborder" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
												<?if($bImage):?>
													<?=$imagePart?>
												<?endif;?>
												<div class="body-info">
													<?=$textPart?>
												</div>
											</div>
											<hr />
										</div>
									<?elseif($arParams['VIEW_TYPE'] == 'table'):?>
										<div class="shadow col-md-<?=floor(12 / $line_element_count)?> col-sm-<?=floor(12 / round($line_element_count / 2))?>">
											<div class="item<?=($imageSrc ? '' : ' wti')?>" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
												<div class="row">
													<div class="col-md-12">
														<?if(!$imageSrc):?>
															<div class="body-info"><?=$textPart?></div>
														<?else:?>
															<?=$imagePart?>
															<div class="body-info"><?=$textPart?></div>
														<?endif;?>
													</div>
												</div>
											</div>
										</div>
									<?endif;?>
								<?endforeach;?>
							<?if($arParams['VIEW_TYPE'] !== 'accordion'):?>
								</div>
							<?endif;?>
						</div>
					<?endforeach;?>
				</div>

		<?if($arParams['SHOW_TABS'] == 'Y'):?>
			</div>
		<?endif;?>

	<?// bottom pagination?>
	<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
		<?=$arResult['NAV_STRING']?>
	<?endif;?>
</div>
<?endif;?>