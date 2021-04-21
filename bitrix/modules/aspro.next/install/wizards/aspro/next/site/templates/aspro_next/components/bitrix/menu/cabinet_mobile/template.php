<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?global $USER, $arTheme;?>
<?$bParent = $arResult && $USER->IsAuthorized();?>
<?$this->setFrameMode(true);?>
<!-- noindex -->
<div class="menu middle">
	<ul>
		<li<?=(CNext::isPersonalPage() ? ' class="selected"' : '')?>>
			<?$link = str_replace('//', '/', str_replace('#SITE_DIR#', SITE_DIR, \Bitrix\Main\Config\Option::get('aspro.next', 'PERSONAL_PAGE_URL', SITE_DIR.'personal/')));?>
			<a rel="nofollow" class="dark-color<?=($bParent ? ' parent' : '')?>" href="<?=$link;?>">
				<?=CNext::showIconSvg("cabinet", SITE_TEMPLATE_PATH."/images/svg/".($USER->IsAuthorized() ? 'User' : 'Lock')."_black.svg");?>
				<span><?=GetMessage('CABINET_LINK2')?></span>
				<?if($bParent):?>
					<span class="arrow"><i class="svg svg_triangle_right"></i></span>
				<?endif;?>
			</a>
			<?if($bParent):?>
				<ul class="dropdown">
					<li class="menu_back"><a href="" class="dark-color" rel="nofollow"><i class="svg svg-arrow-right"></i><?=GetMessage('NEXT_T_MENU_BACK')?></a></li>
					<li class="menu_title"><a href="<?=$link;?>"><?=GetMessage('CABINET_LINK2')?></a></li>
					<?foreach($arResult as $arItem):?>
						<?$bShowChilds = $arParams['MAX_LEVEL'] > 1;?>
						<?$bParent = $arItem['CHILD'] && $bShowChilds;?>
						<li<?=($arItem['SELECTED'] ? ' class="selected"' : '')?>>
							<a class="dark-color<?=($bParent ? ' parent' : '')?>" href="<?=$arItem["LINK"]?>" title="<?=$arItem["TEXT"]?>">
								<span><?=$arItem['TEXT']?></span>
								<?if($bParent):?>
									<span class="arrow"><i class="svg svg_triangle_right"></i></span>
								<?endif;?>
							</a>
							<?if($bParent):?>
								<ul class="dropdown">
									<li class="menu_back"><a href="" class="dark-color" rel="nofollow"><i class="svg svg-arrow-right"></i><?=GetMessage('NEXT_T_MENU_BACK')?></a></li>
									<li class="menu_title"><?=$arItem['TEXT']?></li>
									<?foreach($arItem['CHILD'] as $arSubItem):?>
										<?$bShowChilds = $arParams['MAX_LEVEL'] > 2;?>
										<?$bParent = $arSubItem['CHILD'] && $bShowChilds;?>
										<li<?=($arSubItem['SELECTED'] ? ' class="selected"' : '')?>>
											<a class="dark-color<?=($bParent ? ' parent' : '')?>" href="<?=$arSubItem["LINK"]?>" title="<?=$arSubItem["TEXT"]?>">
												<span><?=$arSubItem['TEXT']?></span>
												<?if($bParent):?>
													<span class="arrow"><i class="svg svg_triangle_right"></i></span>
												<?endif;?>
											</a>
											<?if($bParent):?>
												<ul class="dropdown">
													<li class="menu_back"><a href="" class="dark-color" rel="nofollow"><i class="svg svg-arrow-right"></i><?=GetMessage('NEXT_T_MENU_BACK')?></a></li>
													<li class="menu_title"><?=$arSubItem['TEXT']?></li>
													<?foreach($arSubItem["CHILD"] as $arSubSubItem):?>
														<?$bShowChilds = $arParams['MAX_LEVEL'] > 3;?>
														<?$bParent = $arSubSubItem['CHILD'] && $bShowChilds;?>
														<li<?=($arSubSubItem['SELECTED'] ? ' class="selected"' : '')?>>
															<a class="dark-color<?=($bParent ? ' parent' : '')?>" href="<?=$arSubSubItem["LINK"]?>" title="<?=$arSubSubItem["TEXT"]?>">
																<span><?=$arSubSubItem['TEXT']?></span>
																<?if($bParent):?>
																	<span class="arrow"><i class="svg svg_triangle_right"></i></span>
																<?endif;?>
															</a>
															<?if($bParent):?>
																<ul class="dropdown">
																	<li class="menu_back"><a href="" class="dark-color" rel="nofollow"><i class="svg svg-arrow-right"></i><?=GetMessage('NEXT_T_MENU_BACK')?></a></li>
																	<li class="menu_title"><?=$arSubSubItem['TEXT']?></li>
																	<?foreach($arSubSubItem["CHILD"] as $arSubSubSubItem):?>
																		<li<?=($arSubSubSubItem['SELECTED'] ? ' class="selected"' : '')?>>
																			<a class="dark-color<?=($bParent ? ' parent' : '')?>" href="<?=$arSubSubSubItem["LINK"]?>" title="<?=$arSubSubSubItem["TEXT"]?>">
																				<span><?=$arSubSubSubItem['TEXT']?></span>
																			</a>
																		</li>
																	<?endforeach;?>
																</ul>
															<?endif;?>
														</li>
													<?endforeach;?>
												</ul>
											<?endif;?>
										</li>
									<?endforeach;?>
								</ul>
							<?endif;?>
						</li>
					<?endforeach;?>
				</ul>
			<?endif;?>
		</li>
	</ul>
</div>
<!-- /noindex -->