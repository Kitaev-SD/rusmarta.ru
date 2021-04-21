<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>

<div class="item-views list list-type-block <?=($arParams['IMAGE_POSITION'] ? 'image_'.$arParams['IMAGE_POSITION'] : '')?> <?=((isset($arParams['IS_STAFF']) && $arParams['IS_STAFF'] == 'Y') ? 'staff-block' : '')?> staff <?=($templateName = $component->{'__template'}->{'__name'})?>">
	<?// top pagination?>
	<?if($arParams['DISPLAY_TOP_PAGER']):?>
		<?=$arResult['NAV_STRING']?>
	<?endif;?>

	<?if($arResult['ITEMS']):?>
		<?$count = count($arResult['ITEMS']);?>
		<div class="row items">
			<?foreach($arResult['ITEMS'] as $i => $arItem):?>
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

				<div class="col-md-12">
					<div class="item <?=($bImage ? '' : ' wti')?> clearfix" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
						<?if($bImage):?>
							<div class="image">
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
					<?/*if($i && $i != ($count-1)):?>
						<hr />
					<?endif;*/?>
				</div>
			<?endforeach;?>
		</div>

		<?// bottom pagination?>
		<?if($arParams['DISPLAY_BOTTOM_PAGER'] && $arResult['NAV_STRING']):?>
			<hr />
			<?=$arResult['NAV_STRING']?>
		<?endif;?>
	<?endif;?>
</div>