<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$this->setFrameMode(true);?>
<div class="catalog item-views list big-img <?=($arParams["IMAGE_POSITION"] ? "image_".$arParams["IMAGE_POSITION"] : "")?> <?=$templateName;?>">
	<?// top pagination?>
	<?if($arParams["DISPLAY_TOP_PAGER"]):?>
		<?=$arResult["NAV_STRING"]?>
	<?endif;?>

	<?if($arResult["SECTIONS"]):?>

		<div class="items row">
			<?foreach($arResult["SECTIONS"] as $i => $arSection):?>
				<div class="col-md-12">
				<?if(isset($arSection["NAME"]) && $arSection["NAME"]):?>
					<h2><a href="<?=$arSection["SECTION_PAGE_URL"];?>" class="dark-color"><?=$arSection["NAME"];?></a></h2>
				<?endif;?>
					<div class="row">
				<?// show section items?>
				<?foreach($arSection["ITEMS"] as $i => $arItem):?>
					<?
					// edit/add/delete buttons for edit mode
					$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BNL_ELEMENT_DELETE_CONFIRM")));
					// use detail link?
					$bDetailLink = $arParams['SHOW_DETAIL_LINK'] != 'N' && (!strlen($arItem['DETAIL_TEXT']) ? ($arParams['HIDE_LINK_WHEN_NO_DETAIL'] !== 'Y' && $arParams['HIDE_LINK_WHEN_NO_DETAIL'] != 1) : true);
					// preview picture
					$bImage = isset($arItem['FIELDS']['PREVIEW_PICTURE']) && strlen($arItem['PREVIEW_PICTURE']['SRC']);
					$imageSrc = ($bImage ? $arItem['PREVIEW_PICTURE']['SRC'] : false);
					$imageDetailSrc = ($bImage ? $arItem['DETAIL_PICTURE']['SRC'] : false);
					// show active date period
					$bActiveDate = strlen($arItem["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"]) || ($arItem["DISPLAY_ACTIVE_FROM"] && in_array("DATE_ACTIVE_FROM", $arParams["FIELD_CODE"]));
					?>

					<?ob_start();?>
						<?// element name?>
						<?if(strlen($arItem["FIELDS"]["NAME"])):?>
							<div class="title">
								<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" <?=(strpos($arItem['DETAIL_PAGE_URL'], 'http') !== false ? 'target="_blank"' : "")?>><?endif;?>
									<?=$arItem['NAME']?>
								<?if($bDetailLink):?></a><?endif;?>
							</div>
						<?endif;?>

						<?// date active period?>
						<?if($bActiveDate):?>
							<div class="period">
								<?if(strlen($arItem["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"])):?>
									<span class="date"><?=$arItem["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"]?></span>
								<?else:?>
									<span class="date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></span>
								<?endif;?>
							</div>
						<?endif;?>

						<?// element preview text?>
						<?if(strlen($arItem["FIELDS"]["PREVIEW_TEXT"])):?>
							<div class="previewtext">
								<?if($arItem["PREVIEW_TEXT_TYPE"] == "text"):?>
									<p><?=$arItem["FIELDS"]["PREVIEW_TEXT"]?></p>
								<?else:?>
									<?=$arItem["FIELDS"]["PREVIEW_TEXT"]?>
								<?endif;?>
							</div>
						<?endif;?>

						<?// element display properties?>
						<?if(isset($arItem['SHOW_PROPS']) && $arItem['SHOW_PROPS']):?>
							<hr />
							<div class="properties">
								<?foreach($arItem['SHOW_PROPS'] as $PCODE => $arProperty):?>
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
								<a href="<?=$arItem['DETAIL_PAGE_URL']?>" <?=(strpos($arItem['DETAIL_PAGE_URL'], 'http') !== false ? 'target="_blank"' : "")?> class="btn-inline sm rounded black"><?=GetMessage('TO_ALL')?><i class="fa fa-angle-right"></i></a>
							</div>
						<?endif;?>

					<?$textPart = ob_get_clean();?>

					<?ob_start();?>
						<?if($bImage):?>
							<div class="image shine <?=($bImage ? ' w-picture' : ' wo-picture')?>">
								<?if($bDetailLink):?><a href="<?=$arItem['DETAIL_PAGE_URL']?>" <?=(strpos($arItem['DETAIL_PAGE_URL'], 'http') !== false ? 'target="_blank"' : "")?>><?endif;?>
									<img src="<?=$imageSrc?>" alt="<?=($bImage ? $arItem['PREVIEW_PICTURE']['ALT'] : $arItem['NAME'])?>" title="<?=($bImage ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'])?>" class="img-responsive" />
								<?if($bDetailLink):?></a><?endif;?>
							</div>
						<?endif;?>
					<?$imagePart = ob_get_clean();?>
					<div class="col-md-12">
						<?/*if($i):?>
							<hr />
						<?endif;*/?>
						<div id="<?=$this->GetEditAreaId($arItem['ID'])?>" class="item noborder1<?=($bImage ? '' : ' wti')?><?=($bActiveDate ? ' wdate' : '')?>">
							<div class="row">
								<?if(!$bImage):?>
									<div class="col-md-12"><div class="text"><?=$textPart?></div></div>
								<?elseif($arParams["IMAGE_POSITION"] == "right"):?>
									<div class="col-md-5 col-sm-7 col-xs-12"><div class="text"><?=$textPart?></div></div>
									<div class="col-md-7 col-sm-5 col-xs-12"><?=$imagePart?></div>
								<?else:?>
									<div class="col-md-7 col-sm-5 col-xs-12"><?=$imagePart?></div>
									<div class="col-md-5 col-sm-7 col-xs-12"><div class="text"><?=$textPart?></div></div>
								<?endif;?>
							</div>
						</div>
					</div>
				<?endforeach;?>
				</div>
			</div>
			<?endforeach;?>
		</div>
	<?endif;?>

	<?// bottom pagination?>
	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<?=$arResult["NAV_STRING"]?>
	<?endif;?>
</div>