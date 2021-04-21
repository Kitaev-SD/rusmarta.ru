<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
$this->setFrameMode(true);
?>
<div class="detail <?=($templateName = $component->{"__parent"}->{"__template"}->{"__name"})?>">
	<article>		
		<div class="post-content">
			<?// element name?>
			<?if($arParams["DISPLAY_NAME"] != "N" && strlen($arResult["NAME"])):?>
				<h2><?=$arResult["NAME"]?></h2>
			<?endif;?>
			<div class="content">
				<?// text?>
				<?if(strlen($arResult["FIELDS"]["PREVIEW_TEXT"])):?>
					<div class="preview-text">
						<?if($arResult["PREVIEW_TEXT_TYPE"] == "text"):?>
							<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];?></p>
						<?else:?>
							<?=$arResult["FIELDS"]["PREVIEW_TEXT"];?>
						<?endif;?>
					</div>
				<?endif;?>
				<?if(strlen($arResult["FIELDS"]["DETAIL_TEXT"])):?>
					<div class="text">
						<?if($arResult["DETAIL_TEXT_TYPE"] == "text"):?>
							<p><?=$arResult["FIELDS"]["DETAIL_TEXT"];?></p>
						<?else:?>
							<?=$arResult["FIELDS"]["DETAIL_TEXT"];?>
						<?endif;?>
					</div>
				<?endif;?>
				
				<?// display properties?>
				<?if($arResult["DISPLAY_PROPERTIES"]):?>
					<div class="properties">
						<?foreach($arResult['DISPLAY_PROPERTIES'] as $PCODE => $arProperty):?>
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

				<?if($arParams['FORM'] == 'Y'):?>
					<div class="buttons">
						<button class="btn btn-default" data-event="jqm" data-name="resume" data-param-form_id="<?=$arParams["FORM_ID"]?>" data-autoload-POST="<?=CNext::formatJsName($arResult['NAME']);?>" data-autohide=""><?=$arParams["FORM_BUTTON_TITLE"];?></button>
					</div>
				<?endif;?>
				
			</div>
		</div>
	</article>
</div>