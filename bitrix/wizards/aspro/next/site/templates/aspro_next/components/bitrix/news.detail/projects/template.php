<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<?$this->setFrameMode(true);?>	
<?use \Bitrix\Main\Localization\Loc;?>

<?// shot top banners start?>
<?$bShowTopBanner = (isset($arResult['SECTION_BNR_CONTENT'] ) && $arResult['SECTION_BNR_CONTENT'] == true);?>
<?if($bShowTopBanner):?>
	<?$this->SetViewTarget("section_bnr_content");?>
		<?CNext::ShowTopDetailBanner($arResult, $arParams);?>
	<?$this->EndViewTarget();?>
<?endif;?>
<?// shot top banners end?>

<?$bShowAskBlock = ($arResult['DISPLAY_PROPERTIES']['FORM_QUESTION']['VALUE_XML_ID'] == 'YES');?>
<?$bShowOrderBlock = ($arResult['DISPLAY_PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] == 'YES');?>
<?$bShowAllChar = (isset($arResult['DISPLAY_PROPERTIES_FORMATTED']) && count($arResult['DISPLAY_PROPERTIES_FORMATTED'])>3);?>
<div class="item projects-blocks">
	<?// element name?>
	<?if($arParams['DISPLAY_NAME'] != 'N' && strlen($arResult['NAME'])):?>
		<h2 itemprop="name"><?=$arResult['NAME']?></h2>
	<?endif;?>
	<div class="head-block<?=($arResult['GALLERY'] ? '' : ' wti')?>">
		<div class="row">
			<?if($arResult['GALLERY']):?>
				<div class="col-md-7 col-sm-7">
					<div class="inner">
						<div class="flexslider color-controls dark show-nav-controls bigs top_slider" data-slice="Y" data-plugin-options='{"animation": "slide", "directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false}'>
							<ul class="slides items">
								<?$countAll = count($arResult['GALLERY']);?>
								<?foreach($arResult['GALLERY'] as $i => $arPhoto):?>
									<li class="item" data-slice-block="Y" data-slice-params='{"lineheight": -3}'>
										<a href="<?=$arPhoto['DETAIL']['SRC']?>" target="_blank" title="<?=$arPhoto['TITLE']?>" class="fancy" data-fancybox-group="gallery">
											<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" itemprop="image" />
											<span class="zoom"></span>
										</a>
									</li>
								<?endforeach;?>
							</ul>
						</div>
					</div>
				</div>
			<?endif;?>
			<div class="<?=($arResult['GALLERY'] ? 'col-md-5 col-sm-5' : 'col-md-12 col-sm-12');?>">
				<div class="info" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
					<?if(isset($arResult['PROPERTIES']['TASK_PROJECT']) && $arResult['PROPERTIES']['TASK_PROJECT']['~VALUE']['TEXT']):?>
						<div class="hh">
							<div class="title_grey_small"><?=$arResult['PROPERTIES']['TASK_PROJECT']['NAME'];?></div>
							<div class="text"><?=$arResult['PROPERTIES']['TASK_PROJECT']['~VALUE']['TEXT'];?></div>
						</div>
					<?endif;?>
					<?if($arResult['DISPLAY_PROPERTIES_FORMATTED'] || ($bShowAskBlock || $bShowOrderBlock)):?>
						<div class="row">
							<?if($arResult['DISPLAY_PROPERTIES_FORMATTED']):?>
								<div class="col-md-<?=(($bShowAskBlock || $bShowOrderBlock) ? 6 : 12)?>">
									<?foreach($arResult['DISPLAY_PROPERTIES_FORMATTED'] as $code => $arProp):?>
										<div class="prop-block">
											<div class="title title_grey_small">
												<?if($arProp['HINT']):?>
													<div class="hint">
														<span class="icons" data-toggle="tooltip" data-placement="top" title="<?=$arProp['HINT']?>"></span>
													</div>
												<?endif;?>
												<span><?=$arProp['NAME']?></span>
											</div>
											<div class="value">
												<?if(is_array($arProp['DISPLAY_VALUE'])):?>
													<?foreach($arProp['DISPLAY_VALUE'] as $key => $value):?>
														<?if($arProp['DISPLAY_VALUE'][$key + 1]):?>
															<?=$value.'&nbsp;/ '?>
														<?else:?>
															<?=$value?>
														<?endif;?>
													<?endforeach;?>
												<?else:?>
													<?=$arProp['DISPLAY_VALUE']?>
												<?endif;?>
											</div>
										</div>
									<?endforeach;?>
									<?if($bShowAllChar):?>
										<div class="all_char colored"><span class="choise" data-block=".chars-block"><?=Loc::getMessage('ALL_CHAR');?></span></div>
									<?endif;?>
								</div>
							<?endif;?>
							<?if($bShowAskBlock || $bShowOrderBlock):?>
								<div class="col-md-<?=($arResult['DISPLAY_PROPERTIES_FORMATTED'] ? 6 : 12)?>">
									<div class="buttons-block">
										<?if($bShowOrderBlock):?>
											<div class="block">
												<span class="btn btn-default btn-lg animate-load" data-event="jqm" data-param-form_id="<?=($arParams["FORM_ID_ORDER_SERVISE"] ? $arParams["FORM_ID_ORDER_SERVISE"] : 'PROJECTS');?>" data-name="order_services" data-autoload-service="<?=CNext::formatJsName($arResult['NAME']);?>" data-autoload-project="<?=CNext::formatJsName($arResult['NAME']);?>"><span><?=(strlen($arParams['S_ORDER_SERVISE']) ? $arParams['S_ORDER_SERVISE'] : Loc::getMessage('S_ORDER_SERVISE'))?></span></span>
											</div>
										<?endif;?>
										<?if($bShowAskBlock):?>
											<div class="block">
												<span class="btn btn-default btn-lg white animate-load" data-event="jqm" data-param-form_id="ASK" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : Loc::getMessage('S_ASK_QUESTION'))?></span></span>
											</div>
											<div class="text">
												<?$APPLICATION->IncludeComponent(
													 'bitrix:main.include',
													 '',
													 Array(
														  'AREA_FILE_SHOW' => 'page',
														  'AREA_FILE_SUFFIX' => 'detail',
														  'EDIT_TEMPLATE' => ''
													 )
												);?>
											</div>
										<?endif;?>
									</div>
								</div>
							<?endif;?>
						</div>
					<?endif;?>
				</div>
			</div>
		</div>
	</div>
	<?/*tizers block start*/?>
	<?$useBrands = ('Y' == $arParams['BRAND_USE']);
	if($useBrands){?>
		<?$APPLICATION->IncludeComponent("bitrix:catalog.brandblock", "main", array(
			"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
			"IBLOCK_ID" => $arParams['IBLOCK_ID'],
			"ELEMENT_ID" => $arResult['ID'],
			"ELEMENT_CODE" => "",
			"PROP_CODE" => $arParams["BRAND_PROP_CODE"],
			"CACHE_TYPE" => $arParams['CACHE_TYPE'],
			"CACHE_TIME" => $arParams['CACHE_TIME'],
			"CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
			"ELEMENT_COUNT" => 5,
			"WIDTH" => "60",
			"WIDTH_SMALL" => "60",
			"HEIGHT" => "60",
			"HEIGHT_SMALL" => "60",
			),
			$component,
			array("HIDE_ICONS" => "Y")
		);?>
	<?}?>
	<?/*tizers block end*/?>
</div>

<?if($bShowAskBlock):?>
	<div class="row">
		<div class="col-md-9">
<?endif;?>

<?if(!$bShowTopBanner && strlen($arResult['FIELDS']['PREVIEW_TEXT'])):?>
	<div class="introtext">
		<?if($arResult['PREVIEW_TEXT_TYPE'] == 'text'):?>
			<p><?=$arResult['FIELDS']['PREVIEW_TEXT'];?></p>
		<?else:?>
			<?=$arResult['FIELDS']['PREVIEW_TEXT'];?>
		<?endif;?>		
	</div>
<?endif;?>

<?if(strlen($arResult['FIELDS']['DETAIL_TEXT'])):?>
	<div class="content">
		<?// element detail text?>
		<?if(strlen($arResult['FIELDS']['DETAIL_TEXT'])):?>
			<?if($arResult['DETAIL_TEXT_TYPE'] == 'text'):?>
				<p><?=$arResult['FIELDS']['DETAIL_TEXT'];?></p>
			<?else:?>
				<?=$arResult['FIELDS']['DETAIL_TEXT'];?>
			<?endif;?>
			<br>
		<?endif;?>
	</div>
<?endif;?>

<?// gallery?>
<?if($arResult['GALLERY_BIG']):?>
	<div class="wraps galerys-block">
		<hr />	
		<?if($arParams['GALLERY_TYPE'] == 'small'):?>
			<div class="small-gallery-block">
				<div class="flexslider row front bigs dark small_slider color-controls" data-plugin-options='{"animation": "slide", "directionNav": false, "useCSS": true, "controlNav" :true, "animationLoop": true, "slideshow": false, "counts": [4, 3, 2, 1]}'>
					<ul class="slides items">
						<?foreach($arResult['GALLERY_BIG'] as $i => $arPhoto):?>
							<li class="col-md-3 item visible">
								<div>
									<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
								</div>
								<a href="<?=$arPhoto['DETAIL']['SRC']?>" class="fancy dark_block_animate" rel="gallery" target="_blank" title="<?=$arPhoto['TITLE']?>"></a>
							</li>
						<?endforeach;?>
					</ul>
				</div>
			</div>
		<?else:?>
			<div class="gallery-block">
				<div class="gallery-wrapper">
					<div class="inner">
						<?if(count($arResult["GALLERY_BIG"]) > 1):?>
							<div class="small-gallery-wrapper">
								<div class="thmb1 flexslider unstyled small-gallery center-nav" data-plugin-options='{"slideshow": false, "useCSS": true, "animation": "slide", "animationLoop": true, "itemWidth": 60, "itemMargin": 20, "minItems": 1, "maxItems": 9, "slide_counts": 1, "asNavFor": ".gallery-wrapper .bigs"}' id="carousel1">
									<ul class="slides items">	
										<?foreach($arResult["GALLERY_BIG"] as $arPhoto):?>
											<li class="item">
												<img class="img-responsive inline" border="0" src="<?=$arPhoto["THUMB"]["src"]?>" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
											</li>
										<?endforeach;?>
									</ul>
								</div>
							</div>
						<?endif;?>
						<div class="flexslider dark bigs big_slider color-controls" id="slider" data-plugin-options='{"animation": "slide", "useCSS": true, "directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, "sync": ".gallery-wrapper .small-gallery", "counts": [1, 1, 1]}'>
							<ul class="slides items">
								<?foreach($arResult['GALLERY_BIG'] as $i => $arPhoto):?>
									<li class="col-md-12 item">
										<a href="<?=$arPhoto['DETAIL']['SRC']?>" class="fancy" rel="gallery" target="_blank" title="<?=$arPhoto['TITLE']?>">
											<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
											<span class="zoom"></span>
										</a>
									</li>
								<?endforeach;?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		<?endif;?>
	</div>
<?endif;?>

<?if($arResult['COMPANY']):?>
	<div class="wraps barnd-block">
		<hr />
		<h5><?=(strlen($arParams['T_CLIENTS']) ? $arParams['T_CLIENTS'] : GetMessage('T_CLIENTS'))?></h5>
		<div class="item-views list list-type-block image_left">
			<div class="items row">
				<div class="col-md-12">
					<div class="item noborder clearfix">
						<?if($arResult['COMPANY']['IMAGE-BIG']):?>
							<div class="image">
								<a href="<?=$arResult['COMPANY']['DETAIL_PAGE_URL'];?>">
									<img src="<?=$arResult['COMPANY']['IMAGE-BIG']['src'];?>" alt="<?=$arResult['COMPANY']['NAME'];?>" title="<?=$arResult['COMPANY']['NAME'];?>" class="img-responsive">
								</a>
							</div>
						<?endif;?>
						<div class="body-info">
							<?if($arResult['COMPANY']['NAME']):?>
								<div class="title"><?=$arResult['COMPANY']['NAME'];?></div>
							<?endif;?>
							<?if($arResult['COMPANY']['DETAIL_TEXT']):?>
								<div class="previewtext">
									<?=$arResult['COMPANY']['DETAIL_TEXT'];?>
								</div>
							<?endif;?>
							<?if($arResult['COMPANY']['PROPERTY_SITE_VALUE']):?>
								<div class="properties">
									<div class="inner-wrapper">
										<!-- noindex -->
										<a class="property icon-block site" href="<?=$arResult['COMPANY']['PROPERTY_SITE_VALUE'];?>" target="_blank" rel="nofollow">
											<?=$arResult['COMPANY']['PROPERTY_SITE_VALUE'];?>
										</a>
										<!-- /noindex -->
									</div>
								</div>
							<?endif;?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?endif;?>


<?// display properties?>
<?if($arResult['DISPLAY_PROPERTIES_FORMATTED'] && $bShowAllChar):?>
	<div class="wraps chars-block" data-offset="-20">
		<hr />
		<h5><?=(strlen($arParams['T_CHARACTERISTICS']) ? $arParams['T_CHARACTERISTICS'] : Loc::getMessage('T_CHARACTERISTICS'))?></h5>
		<div class="chars">
			<div class="char-wrapp">
				<table class="props_table">
					<?foreach($arResult['DISPLAY_PROPERTIES_FORMATTED'] as $PCODE => $arProp):?>
						<tr class="char">
							<td class="char_name">
								<?if($arProp['HINT']):?>
									<div class="hint">
										<span class="icons" data-toggle="tooltip" data-placement="top" title="<?=$arProp['HINT']?>"></span>
									</div>
								<?endif;?>
								<span><?=$arProp['NAME']?></span>
							</td>
							<td class="char_value">
								<span>
									<?if(is_array($arProp['DISPLAY_VALUE'])):?>
										<?foreach($arProp['DISPLAY_VALUE'] as $key => $value):?>
											<?if($arProp['DISPLAY_VALUE'][$key + 1]):?>
												<?=$value.'&nbsp;/ '?>
											<?else:?>
												<?=$value?>
											<?endif;?>
										<?endforeach;?>
									<?else:?>
										<?=$arProp['DISPLAY_VALUE']?>
									<?endif;?>
								</span>
							</td>
						</tr>
					<?endforeach;?>
				</table>
			</div>
		</div>
	</div>
<?endif;?>

<?// docs files?>
<?if($arResult['DISPLAY_PROPERTIES']['DOCUMENTS']['VALUE']):?>
	<div class="wraps docs-block">
		<hr/>	
		<h5><?=(strlen($arParams['T_DOCS']) ? $arParams['T_DOCS'] : Loc::getMessage('T_DOCS'))?></h5>
		<div class="files_block">
			<div class="row">
				<?foreach($arResult['PROPERTIES']['DOCUMENTS']['VALUE'] as $arItem):?>
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

<?// ask question?>
<?if($arResult['DISPLAY_PROPERTIES']['FORM_QUESTION']['VALUE_XML_ID'] == 'YES'):?>
	<?global $isMenu;?>
	<?if($isMenu):?>
		<?$this->SetViewTarget('under_sidebar_content');?>
	<?else:?>
		</div>
		<div class="col-md-3 hidden-xs hidden-sm">
			<div class="fixed_block_fix"></div>
			<div class="ask_a_question_wrapper">
	<?endif;?>
			<div class="ask_a_question">
				<div class="inner">
					<div class="text-block">
						<?$APPLICATION->IncludeComponent(
							 'bitrix:main.include',
							 '',
							 Array(
								  'AREA_FILE_SHOW' => 'page',
								  'AREA_FILE_SUFFIX' => 'ask',
								  'EDIT_TEMPLATE' => ''
							 )
						);?>
					</div>
				</div>
				<div class="outer">
					<span><span class="btn btn-default btn-lg white animate-load" data-event="jqm" data-param-form_id="ASK" data-autoload-need_product="<?=CNext::formatJsName($arResult['NAME']);?>" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : Loc::getMessage('S_ASK_QUESTION'))?></span></span></span>
				</div>
			</div>
	<?if($isMenu):?>
		<?$this->EndViewTarget();?>
	<?else:?>
				</div>
			</div>
		</div>
	<?endif;?>
<?endif;?>