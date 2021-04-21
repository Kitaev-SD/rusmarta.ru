<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>
<?if($arParams["DISPLAY_PICTURE"] != "N"){
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
}
if(isset($arResult['PROPERTIES']['BNR_TOP']) && $arResult['PROPERTIES']['BNR_TOP']['VALUE_XML_ID'] == 'YES' && $arParams["SHOW_TOP_BANNER"] == "Y")
	$templateData['SECTION_BNR_CONTENT'] = true;
?>
<?// shot top banners start?>
<?$bShowTopBanner = (isset($templateData['SECTION_BNR_CONTENT'] ) && $templateData['SECTION_BNR_CONTENT'] == true);?>
<?if($bShowTopBanner):?>
	<?$this->SetViewTarget("section_bnr_content");?>
		<?CNext::ShowTopDetailBanner($arResult, $arParams);?>
	<?$this->EndViewTarget();?>
<?endif;?>
<?// shot top banners end?>
<div class="detail <?=($templateName = $component->{"__parent"}->{"__template"}->{"__name"})?>">
	<article>
		<?// images?>
		<?if($arImgs):?>
			<div class="detailimage">
				<?if($arImgs):?>
					<div class="img-partner">
						<img src="<?=$arImgs[0]["DETAIL"]["SRC"]?>" title="<?=$arImgs[0]["TITLE"]?>" alt="<?=$arImgs[0]["ALT"]?>" class="img-responsive" />
					</div>
				<?endif;?>
			</div>
		<?endif;?>
		
		<div class="post-content">
			<?if($arParams["DISPLAY_NAME"] != "N" && strlen($arResult["NAME"])):?>
				<h2><?=$arResult["NAME"]?></h2>
			<?endif;?>
			<div class="content">
				<?// text?>
				<?if(strlen($arResult["FIELDS"]["PREVIEW_TEXT"].$arResult["FIELDS"]["DETAIL_TEXT"])):?>
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

<?if($arResult['GALLERY']):?>
	<div class="wraps with-padding galerys-block">
		<hr />
		<h5><?=$arParams['T_GALLERY'];?></h5>
		<div class="small-gallery-block">
			<div class="flexslider unstyled front border custom_flex top_right color-controls" data-plugin-options='{"animation": "slide", "directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, "counts": [4, 3, 2, 1]}'>
				<ul class="slides items">
					<?foreach($arResult['GALLERY'] as $i => $arPhoto):?>
						<li class="col-md-3 item">
							<div>
								<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
							</div>
							<a href="<?=$arPhoto['DETAIL']['SRC']?>" class="fancy dark_block_animate" rel="gallery" target="_blank" title="<?=$arPhoto['TITLE']?>"></a>
						</li>
					<?endforeach;?>
				</ul>
			</div>
		</div>
	</div>
<?endif;?>

<?// docs files?>
<?if($arResult['DOCUMENTS']):?>
	<div class="wraps docs-block">
		<hr/>
		<h5><?=(strlen($arParams['T_DOCS']) ? $arParams['T_DOCS'] : Loc::getMessage('T_DOCS'))?></h5>
		<div class="files_block">
			<div class="row">
				<?foreach($arResult['DOCUMENTS']['VALUE'] as $arItem):?>
					<div class="col-md-3 col-sm-6">
						<?$arFile=CNext::GetFileInfo($arItem);?>
						<div class="file_type clearfix <?=$arFile["TYPE"];?>">
							<i class="icon"></i>
							<div class="description">
								<a target="_blank" href="<?=$arFile["SRC"];?>" class="dark_link"><?=$arFile["DESCRIPTION"];?></a>
								<span class="size">
									<?=$arFile["FILE_SIZE_FORMAT"];?>
								</span>
							</div>
						</div>
					</div>
				<?endforeach;?>
			</div>
		</div>
	</div>
<?endif;?>