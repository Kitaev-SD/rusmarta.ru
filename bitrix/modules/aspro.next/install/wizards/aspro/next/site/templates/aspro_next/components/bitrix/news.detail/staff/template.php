<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
$this->setFrameMode(true);
if($arParams["DISPLAY_PICTURE"] != "N"){
	$picture = ($arResult["FIELDS"]["DETAIL_PICTURE"] ? "DETAIL_PICTURE" : "PREVIEW_PICTURE");
	CNext::getFieldImageData($arResult, array($picture));
	$arPhoto = $arResult[$picture];
	if($arPhoto){
		$arImgs[] = array(
			'DETAIL' => $arPhoto,
			'PREVIEW' => CFile::ResizeImageGet($arPhoto["ID"], array('width' => 300, 'height' => 300), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
			'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME'])),
			'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME'])),
		);
	}
	/*if($arResult["PROPERTIES"]["GALLERY"]["VALUE"]){
		foreach($arResult["PROPERTIES"]["GALLERY"]["VALUE"] as $arImg){
			$arImgs[] = array(
				'DETAIL' => ($arPhoto = CFile::GetFileArray($arImg)),
				'PREVIEW' => CFile::ResizeImageGet($arImg, array('width' => 300, 'height' => 300), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
				'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME'])),
				'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME'])),
			);
		}
	}*/
}
?>
<div class="detail <?=($templateName = $component->{"__parent"}->{"__template"}->{"__name"})?>">
	<article>
		<?// images?>
		<?if($arImgs):?>
			<div class="detailimage">
				<?// images slider?>
				<?/*
				<div class="flexslider" data-plugin-options='{"directionNav":false, "animation":"slide", "slideshow": false}'>
					<ul class="slides">
						<?foreach($arImgs as $arImg):?>
							<li>
								<a class="img-thumbnail fancybox" href="<?=$arImg["DETAIL"]["SRC"]?>" rel="galery" title="<?=$arImg["TITLE"]?>">
									<img class="img-rounded" src="<?=$arImg["PREVIEW"]["src"]?>" border="0" width="<?=$arImg["PREVIEW"]["width"]?>" height="<?=$arImg["PREVIEW"]["height"]?>" title="<?=$arImg["TITLE"]?>" alt="<?=$arImg["ALT"]?>" />
									<span class="zoom"><i class="fa fa-16 fa-white-shadowed fa-search"></i></span>
								</a>
							</li>
						<?endforeach;?>
					</ul>
				</div>
				*/?>
				<?// or single detail image?>
				<?if($arImgs):?>
					<img src="<?=$arImgs[0]["DETAIL"]["SRC"]?>" title="<?=$arImgs[0]["TITLE"]?>" alt="<?=$arImgs[0]["ALT"]?>" class="img-responsive" />
				<?endif;?>
			</div>
		<?endif;?>
		
		<?// date active from or dates period active?>
		<?if(strlen($arResult["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"]) || ($arResult["DISPLAY_ACTIVE_FROM"] && in_array("DATE_ACTIVE_FROM", $arParams["FIELD_CODE"]))):?>
			<div class="period">
				<?if(strlen($arResult["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"])):?>
					<span class="date"><?=$arResult["DISPLAY_PROPERTIES"]["PERIOD"]["VALUE"]?></span>
				<?else:?>
					<span class="date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
				<?endif;?>
			</div>
		<?endif;?>
		
		<div class="post-content">
			<?if($arParams["DISPLAY_NAME"] != "N" && strlen($arResult["NAME"])):?>
				<h2><?=$arResult["NAME"]?></h2>
			<?endif;?>
			<div class="content">
				<?if((isset($arResult['SOCIAL_PROPS']) && $arResult['SOCIAL_PROPS'])):?>
					<div class="item-views">
						<div class="item">
							<div class="bottom-props">
								<!-- noindex -->
									<?foreach($arResult['SOCIAL_PROPS'] as $arProp):?>
										<a href="<?=$arProp['VALUE'];?>" target="_blank" rel="nofollow" class="value <?=strtolower($arProp['CODE']);?>"><?=$arProp['VALUE'];?></a>
									<?endforeach;?>
								<!-- /noindex -->
							</div>
						</div>
					</div>
				<?endif;?>
				<?// text?>
				<?if(strlen($arResult["FIELDS"]["PREVIEW_TEXT"].$arResult["FIELDS"]["DETAIL_TEXT"])):?>
					<div class="text">
						<?if($arResult["PREVIEW_TEXT_TYPE"] == "text"):?>
							<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];?></p>
						<?else:?>
							<?=$arResult["FIELDS"]["PREVIEW_TEXT"];?>
						<?endif;?>
						<?if($arResult["DETAIL_TEXT_TYPE"] == "text"):?>
							<p><?=$arResult["FIELDS"]["DETAIL_TEXT"];?></p>
						<?else:?>
							<?=$arResult["FIELDS"]["DETAIL_TEXT"];?>
						<?endif;?>
					</div>
				<?endif;?>
				
				<?// display properties?>
				<?if($arResult["DISPLAY_PROPERTIES"]):?>
					<hr/>
					<div class="properties">
						<?foreach($arResult["DISPLAY_PROPERTIES"] as $PCODE => $arProperty):?>
							<?$bIconBlock = ($PCODE == 'EMAIL' || $PCODE == 'PHONE' || $PCODE == 'SITE');?>
							<div class="inner-wrapper">
								<div class="property <?=($bIconBlock ? "icon-block" : "");?> <?=strtolower($PCODE);?>">
									<?if(!$bIconBlock):?>
										<?=$arProperty['NAME']?>:&nbsp;
									<?endif;?>
									<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
										<?$val = implode("&nbsp;/ ", $arProperty["DISPLAY_VALUE"]);?>
									<?else:?>
										<?$val = $arProperty["DISPLAY_VALUE"];?>
									<?endif;?>
									<?if($PCODE == "SITE"):?>
										<!--noindex-->
										<a href="<?=(strpos($arProperty['VALUE'], 'http') === false ? 'http://' : '').$arProperty['VALUE'];?>" rel="nofollow" target="_blank">
											<?=$arProperty['VALUE'];?>
										</a>
										<!--/noindex-->
									<?elseif($PCODE == "EMAIL"):?>
										<a href="mailto:<?=$val?>"><?=$val?></a>
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
	</article>
</div>