<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$this->setFrameMode(true);?>
<?
global $arTheme;
$iVisibleItemsMenu = ($arTheme['MAX_VISIBLE_ITEMS_MENU']['VALUE'] ? $arTheme['MAX_VISIBLE_ITEMS_MENU']['VALUE'] : 10);
?>
<?if($arResult):?>
	<div class="table-menu">
		<table>
			<tr>
				<?foreach($arResult as $arItem):?>
					<?$bShowChilds = $arParams["MAX_LEVEL"] > 1;
					$bWideMenu = $arItem["PARAMS"]['FROM_IBLOCK'];?>
					<td class="menu-item unvisible <?=($arItem["CHILD"] ? "dropdown" : "")?> <?=($bWideMenu ? 'wide_menu' : '');?> <?=(isset($arItem["PARAMS"]["CLASS"]) ? $arItem["PARAMS"]["CLASS"] : "");?>  <?=($arItem["SELECTED"] ? "active" : "")?>">
						<div class="wrap">
							<a class="<?=($arItem["CHILD"] && $bShowChilds ? "dropdown-toggle" : "")?>" href="<?=$arItem["LINK"]?>">
								<div>
									<?if(isset($arItem["PARAMS"]["CLASS"]) && strpos($arItem["PARAMS"]["CLASS"], "sale_icon") !== false):?>
										<?=CNext::showIconSvg('sale', SITE_TEMPLATE_PATH.'/images/svg/Sale.svg', '', '');?>
									<?endif;?>
									<?=$arItem["TEXT"]?>
									<div class="line-wrapper"><span class="line"></span></div>
								</div>
							</a>
							<?if($arItem["CHILD"] && $bShowChilds):?>
								<span class="tail"></span>
								<ul class="dropdown-menu">
									<?foreach($arItem["CHILD"] as $arSubItem):?>
										<?$bShowChilds = $arParams["MAX_LEVEL"] > 2;?>
										<?$bHasPicture = (isset($arSubItem['PARAMS']['PICTURE']) && $arSubItem['PARAMS']['PICTURE'] && $arTheme['SHOW_CATALOG_SECTIONS_ICONS']['VALUE'] == 'Y');?>
										<li class="<?=($arSubItem["CHILD"] && $bShowChilds ? "dropdown-submenu" : "")?> <?=($arSubItem["SELECTED"] ? "active" : "")?> <?=($bHasPicture ? "has_img" : "")?>">
											<?if($bHasPicture && $bWideMenu):
												$arImg = CFile::ResizeImageGet($arSubItem['PARAMS']['PICTURE'], array('width' => 60, 'height' => 60), BX_RESIZE_PROPORTIONAL_ALT);
												if(is_array($arImg)):?>
													<div class="menu_img"><img src="<?=$arImg["src"]?>" alt="<?=$arSubItem["TEXT"]?>" title="<?=$arSubItem["TEXT"]?>" /></div>
												<?endif;?>
											<?endif;?>
											<a href="<?=$arSubItem["LINK"]?>" title="<?=$arSubItem["TEXT"]?>"><span class="name"><?=$arSubItem["TEXT"]?></span><?=($arSubItem["CHILD"] && $bShowChilds ? '<span class="arrow"><i></i></span>' : '')?></a>
											<?if($arSubItem["CHILD"] && $bShowChilds):?>
												<?$iCountChilds = count($arSubItem["CHILD"]);?>
												<ul class="dropdown-menu toggle_menu">
													<?foreach($arSubItem["CHILD"] as $key => $arSubSubItem):?>
														<?$bShowChilds = $arParams["MAX_LEVEL"] > 3;?>
														<li class="<?=(++$key > $iVisibleItemsMenu ? 'collapsed' : '');?> <?=($arSubSubItem["CHILD"] && $bShowChilds ? "dropdown-submenu" : "")?> <?=($arSubSubItem["SELECTED"] ? "active" : "")?>">
															<a href="<?=$arSubSubItem["LINK"]?>" title="<?=$arSubSubItem["TEXT"]?>"><span class="name"><?=$arSubSubItem["TEXT"]?></span></a>
															<?if($arSubSubItem["CHILD"] && $bShowChilds):?>
																<ul class="dropdown-menu">
																	<?foreach($arSubSubItem["CHILD"] as $arSubSubSubItem):?>
																		<li class="<?=($arSubSubSubItem["SELECTED"] ? "active" : "")?>">
																			<a href="<?=$arSubSubSubItem["LINK"]?>" title="<?=$arSubSubSubItem["TEXT"]?>"><span class="name"><?=$arSubSubSubItem["TEXT"]?></span></a>
																		</li>
																	<?endforeach;?>
																</ul>

															<?endif;?>
														</li>
													<?endforeach;?>
													<?if($iCountChilds > $iVisibleItemsMenu && $bWideMenu):?>
														<li><span class="colored more_items with_dropdown"><?=\Bitrix\Main\Localization\Loc::getMessage("S_MORE_ITEMS");?></span></li>
													<?endif;?>
												</ul>
											<?endif;?>
										</li>
									<?endforeach;?>
								</ul>
							<?endif;?>
						</div>
					</td>
				<?endforeach;?>

				<td class="menu-item dropdown js-dropdown nosave unvisible">
					<div class="wrap">
						<a class="dropdown-toggle more-items" href="#">
							<span><?=\Bitrix\Main\Localization\Loc::getMessage("S_MORE_ITEMS");?></span>
						</a>
						<span class="tail"></span>
						<ul class="dropdown-menu"></ul>
					</div>
				</td>

			</tr>
		</table>
	</div>
<?endif;?>