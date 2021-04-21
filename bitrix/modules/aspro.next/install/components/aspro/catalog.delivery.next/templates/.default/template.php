<?
use Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
Loc::loadMessages(__FILE__);

$bCanSearch = in_array('LOCATION', $arParams['CHANGEABLE_FIELDS']);
$bNeedSearch = $bCanSearch && (!$arResult['LOCATION'] || !$arResult['LOCATION']['ID']);
$bEmptyFieldsBlock = !array_intersect(array('QUANTITY', 'PERSON_TYPE', 'PAY_SYSTEM', 'ADD_BASKET'), $arParams['CHANGEABLE_FIELDS']);
?>
<?/* hide main block while template css not loaded */?>
<div id="catalog-delivery-<?=$arResult['RAND']?>" class="catalog-delivery form <?=($bCanSearch ? 'cansearch' : '')?> <?=($arResult['ERROR'] ? 'haserror' : '')?> <?=($bNeedSearch ? 'search' : '')?>" style="height:0;overflow: hidden;">
	<div class="catalog-delivery-title">
		<h2><?=str_replace(
			array('#LOCATION_NAME#'),
			array($arResult['LOCATION'] && $arResult['LOCATION']['ID'] ? '<span class="catalog-delivery-title-city"><span>'.$arResult['LOCATION']['LOCATION_NAME'].'</span><span>'.CNext::showIconSvg('down colored_theme_hover_bg-el', $this->{'__folder'}.'/images/svg/trianglearrow_down.svg', '', '', true, false, true, false).'</span></span>' : ''),
			$arResult['MESSAGES']['DETAIL_TITLE']
		)?></h2>
		<?if($arResult['LOCATION'] && !$arResult['ERROR'] && $arParams['SHOW_LOCATION_SOURCE'] === 'Y'):?>
			<div class="catalog-delivery-locationsource">
				<?if($arResult['LOCATION_SOURCE'] === 'lastOrder'):?>
					<div class="alert alert-warning"><?=Loc::getMessage('CD_T_LOCATION_SOURCE_LAST_ORDER')?></div>
				<?elseif($arResult['LOCATION_SOURCE'] === 'profile'):?>
					<div class="alert alert-warning"><?=Loc::getMessage('CD_T_LOCATION_SOURCE_PROFILE')?></div>
				<?elseif($arResult['LOCATION_SOURCE'] === 'geoIp'):?>
					<div class="alert alert-warning"><?=Loc::getMessage('CD_T_LOCATION_SOURCE_GEOIP')?></div>
				<?endif;?>
			</div>
		<?endif;?>
	</div>
	<div class="catalog-delivery-error">
		<div class="catalog-delivery-error-icon"><?=\CNext::showIconSvg('fail colored', $this->{'__folder'}.'/images/svg/fail.svg', '', '', true, false, true, false)?></div>
		<div class="catalog-delivery-error-text">
			<?if($arResult['ERROR']):?>
				<?=implode('<br />', $arResult['ERROR'])?>
			<?endif;?>
		</div>
	</div>
	<?if(!$arResult['ERROR']):?>
		<form action="<?=$arResult['ACTION_URL']?>" name="catalog-delivery" method="post" enctype="multipart/form-data"><?=bitrix_sessid_post()?>
			<input type="hidden" name="is_ajax_post" value="Y" />
			<input type="hidden" name="SITE_ID" value="<?=$arResult['SITE_ID']?>" />
			<input type="hidden" name="LANGUAGE_ID" value="<?=$arResult['LANGUAGE_ID']?>" />
			<input type="hidden" name="SIGNED_PARAMS" value="<?=$arResult['SIGNED_PARAMS']?>" />
			<input type="hidden" name="TEMPLATE" value="<?=$this->{'__name'}?>" />
			<input type="hidden" name="PRODUCT" value="<?=$arResult['PRODUCT_ID']?>" data-send="Y" />
			<input type="hidden" name="LOCATION" value="<?=($arResult['LOCATION'] ? $arResult['LOCATION']['CODE'] : '')?>" data-send="Y" />
			<input type="hidden" name="LOCATION_CHANGED" value="N" />
			<?if($arResult['DELIVERY_ID']):?>
				<?foreach($arResult['DELIVERY_ID'] as $i => $deliveryId):?>
					<input type="hidden" name="DELIVERY[<?=$i?>]" value="<?=$deliveryId?>" />
				<?endforeach;?>
			<?else:?>
				<input type="hidden" name="DELIVERY" value="" />
			<?endif;?>
			<div class="catalog-delivery-fields <?=($bEmptyFieldsBlock ? 'empty' : '')?>">
				<?if($bCanSearch):?>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="catalog-delivery-field catalog-delivery-field_locationsearch">
								<?$APPLICATION->IncludeComponent(
									"bitrix:sale.location.selector.search",
									"",
									Array(
										"COMPONENT_TEMPLATE" => "",
										"CODE" => ($arResult['LOCATION'] ? $arResult['LOCATION']['CODE'] : ''),
										"INPUT_NAME" => "LOCATION_SEARCH",
										"PROVIDE_LINK_BY" => "code",
										"JSCONTROL_GLOBAL_ID" => "",
										"JS_CALLBACK" => "",
										"FILTER_BY_SITE" => "Y",
										"SHOW_DEFAULT_LOCATIONS" => "Y",
										"CACHE_TYPE" => "A",
										"CACHE_TIME" => "36000000",
										"FILTER_SITE_ID" => SITE_ID,
										"INITIALIZE_BY_GLOBAL_EVENT" => "",
										"SUPPRESS_ERRORS" => "N"
									),
									false,
									array('HIDE_ICONS' => 'Y')
								);?>
							</div>
						</div>
					</div>
				<?endif;?>
				<?if(!$bEmptyFieldsBlock):?>
					<div class="catalog-delivery-fields-opener">
						<span><?=CNext::showIconSvg('icon', $this->{'__folder'}.'/images/svg/filter.svg', '', '', true, false, true, false)?></span>
						<span><?=Loc::getMessage('CD_T_PARAMS').(in_array('QUANTITY', $arParams['CHANGEABLE_FIELDS']) ? '<span class="catalog-delivery-sp">: '.$arResult['PRODUCT_QUANTITY'].($arResult['PRODUCT']['MEASURE']['SYMBOL'] ? ' '.$arResult['PRODUCT']['MEASURE']['SYMBOL'] : '').($arResult['ADD_BASKET'] === 'Y' ? Loc::getMessage('CD_T_PARAMS_ADD_BASKET') : '').'.</span>' : '')?></span>
					</div>
					<div class="catalog-delivery-fields-base">
						<?if(in_array('QUANTITY', $arParams['CHANGEABLE_FIELDS'])):?>
							<div class="catalog-delivery-field catalog-delivery-field_quantity">
								<div class="catalog-delivery-field-title">
									<label for="catalog-delivery-quantity"><?=Loc::getMessage('CD_T_FIELD_QUANTITY')?></label>
								</div>
								<div class="catalog-delivery-field-input" data-item="<?=$arResult['PRODUCT_ID']?>">
									<div class="catalog-delivery-field-box">
										<div class="catalog-delivery-field-box-value">
											<span class="minus dark-color"><?=\CNext::showIconSvg('', $this->{'__folder'}.'/images/svg/minus.svg')?></span>
											<input type="text" id="catalog-delivery-quantity" class="required text" name="QUANTITY" value="<?=$arResult['PRODUCT_QUANTITY']?>" data-send="Y" />
											<span class="plus dark-color"><?=\CNext::showIconSvg('', $this->{'__folder'}.'/images/svg/plus.svg')?></span>
										</div>
									</div>
								</div>
							</div>
						<?endif;?>
						<?if(in_array('PERSON_TYPE', $arParams['CHANGEABLE_FIELDS'])):?>
							<div class="catalog-delivery-field catalog-delivery-field_persontype">
								<div class="catalog-delivery-field-title">
									<label for="catalog-delivery-persontype"><?=Loc::getMessage('CD_T_FIELD_PERSON_TYPE')?></label>
								</div>
								<div class="catalog-delivery-field-input">
									<div class="catalog-delivery-field-box hasdropdown">
										<select id="catalog-delivery-persontype" class="required" name="PERSON_TYPE" data-send="Y">
											<?if($arResult['PERSON_TYPE']):?>
												<?foreach($arResult['PERSON_TYPE'] as $arPersonType):?>
													<option value="<?=$arPersonType['ID']?>" <?=($arPersonType['CHECKED'] === 'Y' ? 'selected' : '')?>><?=$arPersonType['ID']?></option>
												<?endforeach;?>
											<?else:?>
												<option value="0" selected><?=Loc::getMessage('CD_T_ANY')?></option>
											<?endif;?>
										</select>
										<?$name = $arResult['PERSON_TYPE'] ? htmlspecialcharsbx($arResult['PERSON_TYPE'][$arResult['PERSON_TYPE_ID']]['NAME']) : Loc::getMessage('CD_T_ANY');?>
										<div class="catalog-delivery-field-box-value" title="<?=$name?>"><span><?=$name?></span><?=CNext::showIconSvg('down colored_theme_hover_bg-el', $this->{'__folder'}.'/images/svg/trianglearrow_down.svg', '', '', true, false);?></div>
										<div class="catalog-delivery-field-box-dropdown">
											<?if($arResult['PERSON_TYPE']):?>
												<?foreach($arResult['PERSON_TYPE'] as $arPersonType):?>
													<?$name = htmlspecialcharsbx($arPersonType['NAME']);?>
													<div class="catalog-delivery-field-box-dropdown-item <?=($arPersonType['CHECKED'] === 'Y' ? 'current' : '')?>" data-value="<?=$arPersonType['ID']?>" title="<?=$name?>" <?=($arPersonType['CHECKED'] === 'Y' ? 'class="current"' : '')?>><?=$name?></div>
												<?endforeach;?>
											<?else:?>
												<div class="catalog-delivery-field-box-dropdown-item current" data-value="0" title="<?=$name?>" class="current"><?=$name?></div>
											<?endif;?>
										</div>
									</div>
								</div>
							</div>
						<?endif;?>
						<?if(in_array('PAY_SYSTEM', $arParams['CHANGEABLE_FIELDS'])):?>
							<div class="catalog-delivery-field catalog-delivery-field_paysystem">
								<div class="catalog-delivery-field-title">
									<label for="catalog-delivery-paysystem"><?=Loc::getMessage('CD_T_FIELD_PAY_SYSTEM')?></label>
								</div>
								<div class="catalog-delivery-field-input">
									<div class="catalog-delivery-field-box hasdropdown">
										<select id="catalog-delivery-paysystem" class="required" name="PAY_SYSTEM" data-send="Y">
											<?if($arResult['PAY_SYSTEM']):?>
												<?foreach($arResult['PAY_SYSTEM'] as $arPaySystem):?>
													<option value="<?=$arPaySystem['ID']?>" <?=($arPaySystem['CHECKED'] === 'Y' ? 'selected' : '')?>><?=$arPaySystem['ID']?></option>
												<?endforeach;?>
											<?else:?>
												<option value="0" selected><?=Loc::getMessage('CD_T_ANY')?></option>
											<?endif;?>
										</select>
										<?$name = $arResult['PAY_SYSTEM'] ? htmlspecialcharsbx($arResult['PAY_SYSTEM'][$arResult['PAY_SYSTEM_ID']]['NAME']) : Loc::getMessage('CD_T_ANY');?>
										<div class="catalog-delivery-field-box-value" title="<?=$name?>"><span><?=$name?></span><?=CNext::showIconSvg('down colored_theme_hover_bg-el', $this->{'__folder'}.'/images/svg/trianglearrow_down.svg', '', '', true, false, true, false);?></div>
										<div class="catalog-delivery-field-box-dropdown">
											<?if($arResult['PAY_SYSTEM']):?>
												<?foreach($arResult['PAY_SYSTEM'] as $arPaySystem):?>
													<?$name = htmlspecialcharsbx($arPaySystem['NAME']);?>
													<div class="catalog-delivery-field-box-dropdown-item <?=($arPaySystem['CHECKED'] === 'Y' ? 'current' : '')?>" data-value="<?=$arPaySystem['ID']?>" title="<?=$name?>" <?=($arPaySystem['CHECKED'] === 'Y' ? 'class="current"' : '')?>><?=$name?></div>
												<?endforeach;?>
											<?else:?>
												<div class="catalog-delivery-field-box-dropdown-item current" data-value="0" title="<?=$name?>" class="current"><?=$name?></div>
											<?endif;?>
										</div>
									</div>
								</div>
							</div>
						<?endif;?>
						<?if(in_array('ADD_BASKET', $arParams['CHANGEABLE_FIELDS'])):?>
							<div class="catalog-delivery-field catalog-delivery-field_addbasket">
								<div class="catalog-delivery-field-title">
									<span for="catalog-delivery-addbasket"><?=Loc::getMessage('CD_T_BASKET')?></span>
								</div>
								<div class="catalog-delivery-field-input filter label_block">
									<input type="checkbox" id="catalog-delivery-addbasket" name="ADD_BASKET" value="Y" <?=($arResult['ADD_BASKET'] === 'Y' ? 'checked' : '')?> data-send="Y" />
									<label for="catalog-delivery-addbasket"><?=Loc::getMessage('CD_T_FIELD_ADD_BASKET')?></label>
								</div>
							</div>
						<?endif;?>
					</div>
				<?endif;?>
			</div>
			<?if($arResult['DELIVERY']):?>
				<?if(!$bNeedSearch):?>
					<div class="catalog-delivery-items">
						<?foreach($arResult['DELIVERY'] as $arDelivery):?>
							<?
							$bOpen = $arDelivery['CHECKED'] === 'Y';
							$bLogo = $arDelivery['LOGOTIP'] && $arDelivery['LOGOTIP']['SRC'];
							$bPreriod = strlen(trim(strip_tags($arDelivery['PERIOD_TEXT'], '')));
							$bDescription = strlen(trim(strip_tags($arDelivery['DESCRIPTION'], '')));
							$bPaySystem = boolval($arDelivery['PAY_SYSTEM']);
							$bMore = $bDescription || $bPaySystem;
							$bError = $arDelivery['CALCULATE_ERRORS'];
							$price = $bError ? false : (array_key_exists('DELIVERY_DISCOUNT_PRICE', $arDelivery) ? $arDelivery['DELIVERY_DISCOUNT_PRICE'] : $arDelivery['PRICE']);
							$priceFormatted = $bError ? false : (array_key_exists('DELIVERY_DISCOUNT_PRICE_FORMATED', $arDelivery) ? $arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED'] : $arDelivery['PRICE_FORMATED']);
							?>
							<div class="catalog-delivery-item <?=($bOpen ? 'open' : '')?> <?=($bLogo ? 'haslogo' : '')?> <?=($bPreriod ? 'hasperiod' : '')?> <?=($bDescription ? 'hasdesc' : '')?> <?=($bError ? 'haserror' : '')?>" data-id="<?=$arDelivery['ID']?>">
								<div class="catalog-delivery-item-head">
									<div class="catalog-delivery-flexline flexline-1">
										<?if($bLogo):?>
											<div class="catalog-delivery-item-logo">
												<img src="<?=$arDelivery['LOGOTIP']['SRC']?>" title="<?=htmlspecialcharsbx($arDelivery['NAME'])?>" alt="<?=htmlspecialcharsbx($arDelivery['NAME'])?>" />
											</div>
										<?endif;?>
										<?ob_start();?>
										<div class="catalog-delivery-item-info"><!--
											--><div class="catalog-delivery-item-name"><span><?=htmlspecialcharsbx($arDelivery['NAME'])?><span></div>
											<?if(!$bError):?>
												<?if($bPreriod):?><div class="catalog-delivery-item-period"><?=$arDelivery['PERIOD_TEXT']?></div><?endif;?>
											<?endif;?><!--
										--></div>
										<?=$infoHtml = ob_get_clean();?>
										<?if(!$bError):?>
											<div class="catalog-delivery-item-price"><?=($price > 0 ? $priceFormatted : Loc::getMessage('CD_T_DELIVERY_PRICE_FREE'))?></div>
										<?endif;?>
										<?if($bMore):?>
											<div class="catalog-delivery-item-opener"></div>
										<?endif;?>
									</div>
									<?if($bLogo):?>
										<div class="catalog-delivery-flexline flexline-2"><?=$infoHtml?></div>
									<?endif;?>
									<?if($bError):?>
										<div class="catalog-delivery-flexline flexline-3">
											<div class="catalog-delivery-item-calculate-error"><div class="alert alert-danger"><?=$arDelivery['CALCULATE_ERRORS']?></div></div>
										</div>
									<?endif;?>
								</div>
								<?if($bMore):?>
									<div class="catalog-delivery-item-more" <?=($bOpen ? '' : 'style="display:none;"')?>>
										<?if($bDescription):?>
											<div class="catalog-delivery-item-description"><?=$arDelivery['DESCRIPTION']?></div>
										<?endif;?>
										<?if($bPaySystem):?>
											<div class="catalog-delivery-item-paysystem-title"><span><?=Loc::getMessage('CD_T_DELIVERY_PAY_SYSTEM')?></span></div>
											<div class="catalog-delivery-paysystem-items">
												<?foreach($arDelivery['PAY_SYSTEM'] as $paySystemId):?>
													<?
													$arPaySystem = $arResult['PAY_SYSTEM'][$paySystemId];
													$bLogo = $arPaySystem['LOGOTIP'] && $arPaySystem['LOGOTIP']['SRC'];
													?>
													<div class="catalog-delivery-paysystem-item <?=($bLogo ? 'haslogo' : '')?>">
														<?if($bLogo):?>
															<div class="catalog-delivery-paysystem-item-logo">
																<img src="<?=$arPaySystem['LOGOTIP']['SRC']?>" title="<?=htmlspecialcharsbx($arPaySystem['NAME'])?>" alt="<?=htmlspecialcharsbx($arPaySystem['NAME'])?>" />
															</div>
														<?endif;?>
														<div class="catalog-delivery-paysystem-item-name"><span><?=htmlspecialcharsbx($arPaySystem['NAME'])?></span></div>
													</div>
												<?endforeach;?>
											</div>
										<?endif;?>
									</div>
								<?endif;?>
							</div>
						<?endforeach;?>
					</div>
				<?endif;?>
			<?else:?>
				<div class="catalog-delivery-item-calculate-error"><div class="alert alert-danger"><?=Loc::getMessage('CD_T_ERROR_DELIVERY')?></div></div>
			<?endif;?>
		</form>
	<?endif;?>
	<script>
	BX.message({
		CD_T_ERROR_REQUEST: '<?=GetMessageJS('CD_T_ERROR_REQUEST')?>'
	});

	var <?='obcd'.$arResult['RAND']?> = new JCCatalogDelivery('<?=$arResult['RAND']?>', <?=CUtil::PhpToJSObject($arParams, false, true)?>, <?=CUtil::PhpToJSObject($arResult, false, true)?>);
	</script>
</div>