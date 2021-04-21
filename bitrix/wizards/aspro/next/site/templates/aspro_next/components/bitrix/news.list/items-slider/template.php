<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>
<?if($arResult['ITEMS']):?>
	<div class="row margin0">
		<div class="maxwidth-theme">
			<div class="col-md-12">
				<?
				global $arTheme;
				$slideshowSpeed = abs(intval($arTheme['PARTNERSBANNER_SLIDESSHOWSPEED']['VALUE']));
				$animationSpeed = abs(intval($arTheme['PARTNERSBANNER_ANIMATIONSPEED']['VALUE']));
				$bAnimation = (bool)$slideshowSpeed;
				$isNormalBlock = (isset($arParams['NORMAL_BLOCK']) && $arParams['NORMAL_BLOCK'] == 'Y');


				?>
				<div class="item-views front staff-items table-type-block blocks <?=($isNormalBlock ? 'normal' : '');?>">
					<h3 class="text-center"><?=($arParams["TITLE"] ? $arParams["TITLE"] : Loc::getMessage("TITLE"));?></h3>
					<div class="flexslider unstyled row front dark-nav view-control navigation-vcenter" data-plugin-options='{"directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, <?=($slideshowSpeed >= 0 ? '"slideshowSpeed": '.$slideshowSpeed.',' : '')?> <?=($animationSpeed >= 0 ? '"animationSpeed": '.$animationSpeed.',' : '')?> <?=($isNormalBlock ? '"itemMargin": 32,' : '');?> "counts": [<?=$arParams['COUNT_IN_LINE']?>, <?=$arParams['COUNT_IN_LINE']-1?>, 1]}'>
						<ul class="slides items" data-slice="Y">
							<?foreach($arResult['ITEMS'] as $i => $arItem):?>
								<?
								// edit/add/delete buttons for edit mode
								$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
								$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => Loc::getMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
								// use detail link?
								$bDetailLink = $arParams['SHOW_DETAIL_LINK'] != 'N' && (!strlen($arItem['DETAIL_TEXT']) ? ($arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' && $arParams['HIDE_LINK_WHEN_NO_DETAIL'] != 1) : true);
								// preview image
								$bImage = isset($arItem['FIELDS']['PREVIEW_PICTURE']) && strlen($arItem['PREVIEW_PICTURE']['SRC']);
								$imageSrc = ($bImage ? $arItem['PREVIEW_PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/svg/Staff_noimage2.svg');

								// show active date period
								$bActiveDate = strlen($arItem['DISPLAY_PROPERTIES']['PERIOD']['VALUE']) || ($arItem['DISPLAY_ACTIVE_FROM'] && in_array('DATE_ACTIVE_FROM', $arParams['FIELD_CODE']));
								?>
								<li class="shadow1 col-md-<?=floor(12 / $arParams['COUNT_IN_LINE'])?> col-sm-<?=floor(12 / round($arParams['COUNT_IN_LINE'] / 2))?>">
									<div class="item noborder clearfix" data-slice-block="Y" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
										<?if($imageSrc):?>
											<div class="image shine <?=($bImage ? "" : "wpi" );?>">
												<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?endif;?>
													<img class="img-responsive" src="<?=$imageSrc?>" alt="<?=($bImage ? $arItem['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?=($bImage ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" />
												<?if($bDetailLink):?></a><?endif;?>
											</div>
										<?endif;?>
										<div class="body-info">
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
											<?if(strlen($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['NAME']) && !((isset($arItem['SOCIAL_PROPS']) && $arItem['SOCIAL_PROPS']))):?>
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

											<?// props?>
											<?if((isset($arItem['MIDDLE_PROPS']) && $arItem['MIDDLE_PROPS'])):?>
												<div class="middle-props">
													<?foreach($arItem['MIDDLE_PROPS'] as $key => $arProp):?>
														<div class="value"><?if($key == 'EMAIL'):?><!-- noindex --><a href="mailto:<?=$arProp['VALUE'];?>" target="_blank" rel="nofollow"><?endif;?><?=$arProp['VALUE'];?><?if($key == 'EMAIL'):?></a><!-- /noindex --><?endif;?></div>
													<?endforeach;?>
												</div>
											<?endif;?>

											<?// social props?>
											<?if((isset($arItem['SOCIAL_PROPS']) && $arItem['SOCIAL_PROPS'])):?>
												<div class="bottom-props">
													<!-- noindex -->
														<?foreach($arItem['SOCIAL_PROPS'] as $arProp):?>
															<a href="<?=$arProp['VALUE'];?>" target="_blank" rel="nofollow" class="value <?=strtolower($arProp['CODE']);?>"><?=$arProp['VALUE'];?></a>
														<?endforeach;?>
													<!-- /noindex -->
												</div>
											<?endif;?>
										</div>
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