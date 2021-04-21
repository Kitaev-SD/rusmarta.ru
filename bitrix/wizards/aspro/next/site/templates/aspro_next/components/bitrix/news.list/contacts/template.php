<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<div class="item-views-wrapper <?=$templateName;?>">
	
	<?if($arResult['SECTIONS']):?>
		<div class="maxwidth-theme">
			<div class="row">
				<div class="col-md-12">
					<table class="contacts-stores no-border shops list">
						<?foreach($arResult['SECTIONS'] as $si => $arSection):?>
							<?$bHasSection = (isset($arSection['SECTION']) && $arSection['SECTION'])?>
							<?if($bHasSection):?>
								<?// edit/add/delete buttons for edit mode
								$arSectionButtons = CIBlock::GetPanelButtons($arSection['SECTION']['IBLOCK_ID'], 0, $arSection['SECTION']['ID'], array('SESSID' => false, 'CATALOG' => true));
								$this->AddEditAction($arSection['SECTION']['ID'], $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['SECTION']['IBLOCK_ID'], 'SECTION_EDIT'));
								$this->AddDeleteAction($arSection['SECTION']['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['SECTION']['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
								<tr id="<?=$this->GetEditAreaId($arSection['SECTION']['ID'])?>">
									<td colspan="3"><h4><?=$arSection['SECTION']['NAME'];?></h4></td>
									<td class="hidden-xs"></td>
									<td class="hidden-xs hidden-sm"></td>
								</tr>
							<?endif;?>
							<?foreach($arSection['ITEMS'] as $i => $arItem):?>
								<?
								// edit/add/delete buttons for edit mode
								$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
								// use detail link?
								$bDetailLink = $arParams['SHOW_DETAIL_LINK'] != 'N' && (!strlen($arItem['DETAIL_TEXT']) ? ($arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' && $arParams['HIDE_LINK_WHEN_NO_DETAIL'] != 1) : true);
								// preview picture
								$bImage = (isset($arItem['FIELDS']['PREVIEW_PICTURE']) && strlen($arItem['PREVIEW_PICTURE']['SRC']));
								$imageSrc = ($bImage ? $arItem['PREVIEW_PICTURE']['SRC'] : false);
								$imageDetailSrc = ($bImage ? $arItem['DETAIL_PICTURE']['SRC'] : false);
								$address = ($arItem['PROPERTIES']['ADDRESS']['VALUE'] ? ", ".$arItem['PROPERTIES']['ADDRESS']['VALUE'] : "");
								?>

								<tr class="item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
									<?if($imageSrc):?>
										<td class="hidden-xs img">
											<a href="<?=$arItem["DETAIL_PAGE_URL"];?>">
												<img src="<?=$imageSrc;?>" alt="<?=$arItem['NAME'];?>" title="<?=$arItem['NAME'];?>" class="img-responsive"/>
											</a>
										</td>
									<?endif;?>
									<td colspan="<?=($imageSrc ? 1 : 2);?>" class="hidden-xs" <?=(($arResult['ITEMS_HAS_IMG'] && !$imageSrc) ? 'colspan=2' : '');?>>
										<div class="title">
											<a href="<?=$arItem["DETAIL_PAGE_URL"];?>" class="dark_link"><?=$arItem['NAME'];?><?=$address;?></a>
										</div>
										<?if($arItem['PROPERTIES']['METRO']['VALUE']):?>
											<div class="muted">
												<?foreach($arItem['PROPERTIES']['METRO']['VALUE'] as $metro):?>
													<div class="icons-text metro1">
														<i class="fa"><?=CNext::showIconSvg("metro colored", SITE_TEMPLATE_PATH."/images/svg/Metro.svg");?></i>
														<div class="text"><?=$metro;?></div>
													</div>
												<?endforeach;?>
											</div>
										<?endif;?>
										<?if($arItem['PROPERTIES']['SCHEDULE']['VALUE']):?>
											<div class="muted">
												<div class="icons-text schedule grey s25"><i class="fa"><?=CNext::showIconSvg("clock colored", SITE_TEMPLATE_PATH."/images/svg/WorkingHours.svg");?></i> <div class="text"><?=$arItem['PROPERTIES']['SCHEDULE']['~VALUE']['TEXT'];?></div></div>
											</div>
										<?endif;?>
										<?if($arItem['PROPERTIES']['EMAIL']['VALUE']):?>
											<div class="muted">
												<div class="icons-text schedule grey s25"><i class="fa"><?=CNext::showIconSvg("email colored", SITE_TEMPLATE_PATH."/images/svg/Email.svg");?></i> <div class="text"><a href="mailto:<?=$arItem['PROPERTIES']['EMAIL']['VALUE'];?>"><?=$arItem['PROPERTIES']['EMAIL']['VALUE'];?></a></div>
											</div>
											</div>
										<?endif;?>
										<?if($arItem['DISPLAY_PROPERTIES']):?>
											<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
												<?if($arProperty["DISPLAY_VALUE"]):?>
													<div class="muted custom_prop <?=strtolower($pid);?>">
														<div class="icons-text schedule grey s25">
															<i class="fa"></i>
															<span class="text_custom">
																<span class="name"><?=$arProperty["NAME"]?>:&nbsp;</span>
																<span class="value">
																	<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
																		<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
																	<?else:?>
																		<?=$arProperty["DISPLAY_VALUE"];?>
																	<?endif?>
																</span>
															</span>
														</div>
													</div>
												<?endif?>
											<?endforeach;?>
										<?endif;?>
									</td>
									<td class="phone hidden-xs">
									<?if($arItem['PROPERTIES']['PHONE']['VALUE'])
									{
										foreach($arItem['PROPERTIES']['PHONE']['VALUE'] as $phone):?>
											<a href="tel:+<?=str_replace(array(' ', ',', '-', '(', ')'), '', $phone);?>" class="black"><?=$phone;?></a>
										<?endforeach;
									}?>
									</td>
									<td class="visible-xs mobile-title-phone">
										<div class="row">
											<div class="col-xs-8">
												<div class="titles-block">
													<div class="title"><a href="<?=$arItem["DETAIL_PAGE_URL"];?>" class="dark_link"><?=$arItem['NAME'];?><?=$address;?></a></div>
													<?if($arItem['PROPERTIES']['METRO']['VALUE']):?>
														<div class="muted">
															<?foreach($arItem['PROPERTIES']['METRO']['VALUE'] as $metro):?>
																<div class="icons-text metro1">
																	<i class="fa"><?=CNext::showIconSvg("metro colored", SITE_TEMPLATE_PATH."/images/svg/Metro.svg");?></i>
																	<div class="text"><?=$metro;?></div>
																</div>
															<?endforeach;?>
														</div>
													<?endif;?>
													<?if($arItem['PROPERTIES']['SCHEDULE']['VALUE']):?>
														<div class="muted">
															<div class="icons-text schedule grey s25"><i class="fa"><?=CNext::showIconSvg("clock colored", SITE_TEMPLATE_PATH."/images/svg/WorkingHours.svg");?></i> <div class="text"><?=$arItem['PROPERTIES']['SCHEDULE']['~VALUE']['TEXT'];?></div></div>
														</div>
													<?endif;?>
													<?if($arItem['PROPERTIES']['EMAIL']['VALUE']):?>
														<div class="muted">
															<div class="icons-text schedule grey s25"><i class="fa"><?=CNext::showIconSvg("email colored", SITE_TEMPLATE_PATH."/images/svg/Email.svg");?></i><div class="text"><a href="mailto:<?=$arItem['PROPERTIES']['EMAIL']['VALUE'];?>"><?=$arItem['PROPERTIES']['EMAIL']['VALUE'];?></a></div>
															</div>
														</div>
													<?endif;?>
													<?if($arItem['DISPLAY_PROPERTIES']):?>
														<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
															<?if($arProperty["DISPLAY_VALUE"]):?>
																<div class="muted custom_prop <?=strtolower($pid);?>">
																	<div class="icons-text schedule grey s25">
																		<i class="fa"></i>
																		<span class="text_custom">
																			<span class="name"><?=$arProperty["NAME"]?>:&nbsp;</span>
																			<span class="value">
																				<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
																					<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
																				<?else:?>
																					<?=$arProperty["DISPLAY_VALUE"];?>
																				<?endif?>
																			</span>
																		</span>
																	</div>
																</div>
															<?endif?>
														<?endforeach;?>
													<?endif;?>
													
												</div>
											</div>
											<div class="col-xs-4">
												<div class="phones-block">
													<?if($arItem['PROPERTIES']['PHONE']['VALUE'])
													{
														foreach($arItem['PROPERTIES']['PHONE']['VALUE'] as $phone):?>
															<a href="tel:+<?=str_replace(array(' ', ',', '-', '(', ')'), '', $phone);?>" class="black"><?=$phone;?></a>
														<?endforeach;
													}?>
												</div>
											</div>
										</div>
									</td>
									<td class="hidden-xs hidden-sm">
										<?if($arItem['PROPERTIES']['PAY_TYPE']['VALUE'])
										{
											foreach($arItem['PROPERTIES']['PAY_TYPE']['FORMAT'] as $arPays):?>
												<span class="icon-text grey s30">
													<?if($arPays['UF_ICON_CLASS']):?><i class="fa <?=$arPays['UF_ICON_CLASS'];?>"></i>
													<?elseif($arPays['UF_FILE']):?>
														<i><img src="<?=CFile::GetPath($arPays['UF_FILE']);?>" width="17" alt="<?=$arPays['UF_NAME'];?>"/></i>
													<?endif;?> <?=$arPays['UF_NAME'];?>
												</span>
											<?endforeach;
										}?>
									</td>
								</tr>
							<?endforeach;?>
						<?endforeach;?>
					</table>
				</div>
			</div>
		</div>
	<?endif;?>
</div>