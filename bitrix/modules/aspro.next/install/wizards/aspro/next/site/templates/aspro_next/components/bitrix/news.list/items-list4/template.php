<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?if($arResult['SECTIONS']):?>
	<?if($arParams['SHOW_TITLE'] == 'Y'):?>
		<div class="title-tab-heading visible-xs"><?=$arParams["T_TITLE"];?></div>
	<?endif;?>
<div class="item-views list list-type-block <?=($templateName = $component->{'__parent'}->{'__template'}->{'__name'})?>">
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
						$this->AddDeleteAction($arSection['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
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
							<div class="row sid items">
								<?foreach($arSection['ITEMS'] as $i => $arItem):?>
									<?
									// edit/add/delete buttons for edit mode
									$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
									$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
									// use detail link?
									$bDetailLink = $arParams['SHOW_DETAIL_LINK'] != 'N' && (!strlen($arItem['DETAIL_TEXT']) ? ($arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' && $arParams['HIDE_LINK_WHEN_NO_DETAIL'] != 1) : true);
									?>
									<div class="col-md-12">
										<div class="item shadow" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
											<div class="text">
												<div class="row">
													<div class="col-md-9 col-sm-9">
														<?// element name?>
														<?if(strlen($arItem['FIELDS']['NAME'])):?>
															<div class="title">
																<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="dark-color"><?endif;?>
																	<?=$arItem['NAME']?>
																<?if($bDetailLink):?></a><?endif;?>
															</div>
														<?endif;?>
														<?// element preview text?>
														<div class="previewtext">
															<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT']) || strlen($arItem['FIELDS']['DETAIL_TEXT'])):?>
																<div>
																	<?if(strlen($arItem['FIELDS']['PREVIEW_TEXT'])):?>
																		<?if($arItem['PREVIEW_TEXT_TYPE'] == 'text'):?>
																			<p><?=$arItem['FIELDS']['PREVIEW_TEXT']?></p>
																		<?else:?>
																			<?=$arItem['FIELDS']['PREVIEW_TEXT']?>
																		<?endif;?>
																	<?endif;?>
																</div>
															<?endif;?>
														</div>
													</div>
													<div class="col-md-3 col-sm-3 pays">
														<?// salary?>
														<?if(strlen($arItem['DISPLAY_PROPERTIES']['PAY']['VALUE'])):?>
															<div class="pay"><?=GetMessage('PAY');?><?=$arItem['DISPLAY_PROPERTIES']['PAY']['VALUE']?></div>
														<?endif;?>											
													</div>
												</div>
											</div>
										</div>
									</div>
								<?endforeach;?>
							</div>
						</div>
					<?endforeach;?>
				</div>

	<?// bottom pagination?>
	<?if($arParams['DISPLAY_BOTTOM_PAGER']):?>
		<?=$arResult['NAV_STRING']?>
	<?endif;?>
</div>
<?endif;?>