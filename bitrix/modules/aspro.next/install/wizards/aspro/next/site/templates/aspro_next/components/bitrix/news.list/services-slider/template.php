<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>
<?$this->setFrameMode(true);?>
<?if($arResult['ITEMS']):?>
	<div class="row">
		<div class="maxwidth-theme">
			<div class="col-md-12">
				<?
				$qntyItems = count($arResult['ITEMS']);
				$countsm = $countmd = ($qntyItems > 1 ? 2 : 1);
				$colsm = $colmd = ($qntyItems > 1 ? 6 : 12);

				global $arTheme;
				$slideshowSpeed = abs(intval($arTheme['PARTNERSBANNER_SLIDESSHOWSPEED']['VALUE']));
				$animationSpeed = abs(intval($arTheme['PARTNERSBANNER_ANIMATIONSPEED']['VALUE']));
				$bAnimation = (bool)$slideshowSpeed;
				?>
				<div class="item-views partners front slider-items list list-type-block image_<?=$arParams["IMAGE_POSITION"];?>">
					<div class="flexslider unstyled row front dark-nav" data-plugin-options='{"directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, <?=($slideshowSpeed >= 0 ? '"slideshowSpeed": '.$slideshowSpeed.',' : '')?> <?=($animationSpeed >= 0 ? '"animationSpeed": '.$animationSpeed.',' : '')?> "counts": [<?=$countmd?>, 1, 1]}'>
						<ul class="slides items" data-slice="Y">
							<?foreach($arResult['ITEMS'] as $i => $arItem):?>
								<?
								// edit/add/delete buttons for edit mode
								$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
								// use detail link?
								$bDetailLink = $arParams['SHOW_DETAIL_LINK'] != 'N' && (!strlen($arItem['DETAIL_TEXT']) ? ($arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' && $arParams['HIDE_LINK_WHEN_NO_DETAIL'] != 1) : true);
								// preview image
								$bImage = isset($arItem['FIELDS']['PREVIEW_PICTURE']) && strlen($arItem['PREVIEW_PICTURE']['SRC']);
								$imageSrc = ($bImage ? $arItem['PREVIEW_PICTURE']['SRC'] : false);
								?>
								<li class="col-md-<?=$colmd?> col-sm-<?=$colsm?>">
									<div class="item clearfix1" data-slice-block="Y" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
										<?if($bImage):?>
											<div class="image">
												<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?endif;?>
													<img class="img-responsive" src="<?=$imageSrc?>" alt="<?=($bImage ? $arItem['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?=($bImage ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" />
												<?if($bDetailLink):?></a><?endif;?>
											</div>
										<?endif;?>
										<div class="body-info">
											<?// element name?>
											<?if(strlen($arItem['FIELDS']['NAME'])):?>
												<div class="title">
													<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?endif;?>
														<?=$arItem['NAME']?>
													<?if($bDetailLink):?></a><?endif;?>
												</div>
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
										</div>
										<div class="clearfix block"></div>
									</div>
								</li>
							<?endforeach;?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
<?endif;?>