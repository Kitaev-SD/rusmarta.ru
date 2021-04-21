<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?use \Bitrix\Main\Localization\Loc;?>
<?if($arParams["SHOW_ORDER_BASE"]=='Y' && (strlen($arResult["ERROR_MESSAGE"]) || $arResult["ACCOUNT_NUMBER"])):?>
	<div class="wrapper_block" style="color:#888888;font-size:12px;padding:0px;margin-top:10px;margin-bottom:10px;">
		<?if(strlen($arResult["ERROR_MESSAGE"])):?>
			<div style="font-size:14px;color:#c00;"><?=ShowError($arResult["ERROR_MESSAGE"]);?></div>
		<?else:?>	
			<?$allCurrency = CSaleLang::GetLangCurrency($arParams["SITE_ID"]);?>
			<h3 style="font-weight:400;color: #1d2029;"><?=Loc::getMessage("SPOD_ORDER_TITLE", array("#ORDER_NUMBER#" => $arResult["ACCOUNT_NUMBER"], "#ORDER_DATE#" => $arResult["DATE_INSERT_FORMATED"]));?></h3>
			<div style="border:1px solid #dedede;margin-top:21px;">
				<?if($arParams["SHOW_ORDER_BASE"]=='Y'):?>
					<div style="background:#f7f7f7;border-bottom:1px solid #dedede;padding:22px 21px;font-size:0px;">
						<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;">
							<div><?=Loc::getMessage('SPOD_ORDER_STATUS');?></div>
							<div style="font-size:18px;color:#1d2029;font-weight:600;padding-top:5px;"><?=Loc::getMessage('SPOD_ORDER_DATE_FROM', array("#ORDER_DATE#" => $arResult["DATE_INSERT_FORMATED"]));?></div>
						</div>
						<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;">
							<div><?=Loc::getMessage('SPOD_ORDER_SUMM');?></div>
							<div style="font-size:18px;color:#1d2029;font-weight:600;padding-top:5px;"><?=CAllCurrencyLang::CurrencyFormat($arResult["PRICE"], $allCurrency);?></div>
						</div>
						<?if($arResult["CANCELED"] == "Y" || $arResult["CAN_CANCEL"] == "Y"):?>	
							<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;">
								<?if($arResult["CANCELED"] == "Y"):?>
									<div><?=Loc::getMessage('SPOD_ORDER_CANCELED');?></div>
									<div style="font-size:18px;color:#1d2029;font-weight:600;padding-top:5px;">
										<?=GetMessage('SPOD_YES')?><?if(strlen($arResult["DATE_CANCELED_FORMATED"])):?><?=Loc::getMessage('SPOD_ORDER_DATE_FROM', array("#ORDER_DATE#" => $arResult["DATE_CANCELED_FORMATED"]));?><?endif?>
									</div>
								<?elseif($arResult["CAN_CANCEL"] == "Y"):?>
									<div style="font-size:18px;color:#1d2029;text-align:right;">
										<a href="<?=str_replace("ORDER_ID", $arResult["ACCOUNT_NUMBER"], $arResult["URL_TO_CANCEL"]);?>" style="display:inline-block;font-size:14px;border-radius:3px;padding: 11px 19px;color:<?=$arParams["THEME_COLOR"]?>;background:#f7f7f7;border:1px solid <?=$arParams["THEME_COLOR"]?>;"><?=Loc::getMessage("SPOD_ORDER_CANCEL")?></a>
									</div>
								<?endif;?>
							</div>
						<?endif;?>
					</div>
				<?endif;?>

				<?if($arParams["SHOW_ORDER_USER"]=='Y'):?>
					<?if(intval($arResult["USER_ID"])):?>
						<div style="border-bottom:1px solid #dedede;padding:22px 21px;font-size:0px;">
							<div style="font-size:15px;color:#1d2029;font-weight:600;"><?=Loc::getMessage('SPOD_ACCOUNT_DATA');?></div>
							<?if(strlen($arResult["USER_NAME"])):?>
								<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;padding-top:18px;">
									<div><?=Loc::getMessage('SPOD_ACCOUNT');?></div>
									<div style="font-size:14px;color:#1d2029;padding-top:5px;"><?=$arResult["USER_NAME"]?></div>
								</div>
							<?endif;?>
							<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;padding-top:18px;">
								<div><?=Loc::getMessage('SPOD_LOGIN');?></div>
								<div style="font-size:14px;color:#1d2029;padding-top:5px;"><?=$arResult["USER"]["LOGIN"]?></div>
							</div>
							<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;padding-top:18px;">
								<div><?=Loc::getMessage('SPOD_EMAIL');?></div>
								<div style="font-size:14px;color:#1d2029;padding-top:5px;"><a style="color:<?=$arParams["THEME_COLOR"]?>;" href="mailto:<?=$arResult["USER"]["EMAIL"]?>"><?=$arResult["USER"]["EMAIL"]?></a></div>
							</div>
						</div>
					<?endif?>
				<?endif?>

				<?if($arParams["SHOW_ORDER_PARAMS"]=='Y'):?>
					<div style="border-bottom:1px solid #dedede;padding:22px 21px;font-size:0px;">
						<div style="font-size:15px;color:#1d2029;font-weight:600;"><?=Loc::getMessage('SPOD_ORDER_PROPERTIES');?></div>
						<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;padding-top:18px;">
							<div><?=Loc::getMessage('SPOD_ORDER_PERS_TYPE');?></div>
							<div style="font-size:14px;color:#1d2029;padding-top:5px;"><?=$arResult["PERSON_TYPE"]["NAME"]?></div>
						</div>
					</div>
				<?endif?>

				<?if($arParams["SHOW_ORDER_BUYER"]=='Y'):?>
					<div style="border-bottom:1px solid #dedede;padding:22px 21px;font-size:0px;">
						<div style="font-size:15px;color:#1d2029;font-weight:600;"><?=Loc::getMessage('SPOD_ORDER_UPROPERTIES');?></div>
						<?foreach($arResult["ORDER_PROPS"] as $prop):?>
							<?if($prop["TYPE"] != "CHECKBOX" && !$prop["VALUE"])
								continue;?>
							<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;padding-top:18px;">
								<div><?=$prop['NAME']?>:</div>
								<div style="font-size:14px;color:#1d2029;padding-top:5px;">
									<?if($prop["TYPE"] == "CHECKBOX"):?>
										<?=Loc::getMessage('SPOD_'.($prop["VALUE"] == "Y" ? 'YES' : 'NO'))?>
									<?else:?>
										<?=$prop["VALUE"]?>
									<?endif?>
								</div>
							</div>
						<?endforeach;?>
						<?if(!empty($arResult["USER_DESCRIPTION"])):?>
							<div style="display:inline-block;vertical-align:top;font-size:14px;width:100%;padding-top:18px;">
								<div><?=Loc::getMessage('SPOD_ORDER_USER_COMMENT')?></div>
								<div style="font-size:14px;color:#1d2029;padding-top:5px;"><?=$arResult["USER_DESCRIPTION"]?></div>
							</div>
						<?endif?>
					</div>
				<?endif?>

				<?if($arParams["SHOW_ORDER_PAYMENT"]=='Y'):?>
					<div style="padding:22px 21px;font-size:0px;">
						<div style="font-size:15px;color:#1d2029;font-weight:600;"><?=Loc::getMessage('SPOD_ORDER_PAYMENT');?></div>
						<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;padding-top:18px;">
							<div><?=Loc::getMessage('SPOD_PAY_SYSTEM');?></div>
							<div style="font-size:14px;color:#1d2029;padding-top:5px;">
								<?if(intval($arResult["PAY_SYSTEM_ID"])):?>
									<?=$arResult["PAY_SYSTEM"]["NAME"]?>
								<?else:?>
									<?=Loc::getMessage("SPOD_NONE")?>
								<?endif?>
							</div>
						</div>
						<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;padding-top:18px;">
							<div><?=Loc::getMessage('SPOD_ORDER_PAYED');?></div>
							<div style="font-size:14px;color:#1d2029;padding-top:5px;">
								<?if($arResult["PAYED"] == "Y"):?>
									<?=Loc::getMessage("SPOD_YES")?><?if(strlen($arResult["DATE_PAYED_FORMATED"])):?><?=Loc::getMessage('SPOD_ORDER_DATE_FROM', array("#ORDER_DATE#" => $arResult["DATE_PAYED_FORMATED"]));?><?endif?>
								<?else:?>
									<?=Loc::getMessage("SPOD_NO")?>
								<?endif?>
							</div>
						</div>
						<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;padding-top:18px;">
							<div><?=Loc::getMessage('SPOD_ORDER_DELIVERY');?></div>
							<div style="font-size:14px;color:#1d2029;padding-top:5px;">
								<?if(strpos($arResult["DELIVERY_ID"], ":") !== false || intval($arResult["DELIVERY_ID"])):?>
									<?=$arResult["DELIVERY"]["NAME"]?>
								<?else:?>
									<?=Loc::getMessage("SPOD_NONE")?>
								<?endif?>
							</div>
						</div>
						<?if($arResult["TRACKING_NUMBER"]):?>
							<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;padding-top:18px;">
								<div><?=Loc::getMessage('SPOD_ORDER_TRACKING_NUMBER');?></div>
								<div style="font-size:14px;color:#1d2029;padding-top:5px;"><?=$arResult["TRACKING_NUMBER"]?></div>
							</div>
						<?endif;?>
					</div>
				<?endif?>
			</div>
			<?if($arParams["SHOW_ORDER_BASKET"]=='Y' && $arResult["BASKET"]):?>
				<?$arHeaders = array("NAME", "DISCOUNT", "QUANTITY", "SUMM");
				$allCurrency = CSaleLang::GetLangCurrency($arParams["SITE_ID"]);
				$summ = 0;
				if($arParams['CUSTOM_SELECT_PROPS'])
				{
					foreach($arParams['CUSTOM_SELECT_PROPS'] as $key => $headerId)
					{
						if(in_array($headerId, $arHeaders) || $headerId == 'PICTURE')
							unset($arParams['CUSTOM_SELECT_PROPS'][$key]);
					}
				}?>
				<div style="color:#1d2029;font-size:18px;padding: 22px 0px 18px;font-weight:600;"><?=Loc::getMessage('SPOD_ORDER_BASKET')?></div>
				<div style="border: 1px solid #e7e7e7;border-bottom-width:0px;">
					<table class="sale_basket_small" style="margin: 0;padding: 0;min-width: 100%;border-collapse: collapse;">
						<thead>
							<tr style="background:#f7f7f7;">
								<?foreach($arHeaders as $name):?>
									<td style="white-space:nowrap;border-bottom: 1px solid #e7e7e7;padding: 7px 9px 8px 21px;font-size: 12px;line-height: 20px;background: none;color: #888;<?=($name == "NAME" ? 'width:45%' : '');?>">
										<?=Loc::getMessage("SPOD_HEADER_".$name);?>										
									</td>
								<?endforeach;?>
							</tr>
						</thead>
						<tbody>
							<?foreach($arResult["BASKET"] as $arItem):
								$arItem["PRICE"] = str_replace("-", "", $arItem["PRICE"]);?>
								<tr>
									<?foreach($arHeaders as $name):?>
										<td style="padding:18px 20px;border-bottom: 1px solid #e7e7e7;color:#666;font-size:13px;vertical-align:top;">
											<?if($name == "NAME"):?>
												<?if($arItem["PREVIEW_PICTURE"])
												{
													$img = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], array("width" => 75, "height" => 75), BX_RESIZE_PROPORTIONAL_ALT);
													// $src= str_replace(array("//", ":/"), array("/", "://"), $arParams["SITE_ADDRESS"].$img["src"]);
												}?>
												<div class="item" <?=($arItem["PREVIEW_PICTURE"] ? 'style="position:relative; min-height:75px;font-size:0px;"' : '');?>>
													<?if($arItem["PREVIEW_PICTURE"]):?>
															<div class="img" style="display:inline-block;width:75px;text-align:center;height:75px;vertical-align:top;">
																<img src="<?=$img["src"];?>" alt="<?=$arItem[$name];?>" title="<?=$arItem[$name];?>" style="display: inline-block;vertical-align:middle;max-width:100%;max-height:100%;" />
															</div>
															<div style="padding-left:20px;display: inline-block;width: 107px;font-size:13px;vertical-align:top;">
													<?endif;?>
													<a style="color:<?=$arParams["THEME_COLOR"];?>;" href="<?=(str_replace(array("//", ":/"), array("/", "://"), $arParams["SITE_ADDRESS"].$arItem["DETAIL_PAGE_URL"]));?>"><?=$arItem[$name];?></a>

													<?if($arParams['CUSTOM_SELECT_PROPS']):
														$actuallyHasProps = is_array($arItem["PROPS"]) && !empty($arItem["PROPS"]);?>
														<div style="color:#888;font-size:12px;padding-top:10px;padding-bottom:10px;">
															<?foreach ($arParams['CUSTOM_SELECT_PROPS'] as $id => $headerId)
															{
																$headerName = Loc::getMessage('SPOD_'.$headerId);
																
																if(strlen($headerName)<=0)
																{
																	foreach(array_values($arResult['PROPERTY_DESCRIPTION']) as $prop_head_desc):
																		if(array_key_exists($headerId, $prop_head_desc))
																			$headerName = $prop_head_desc[$headerId]['NAME'];
																	endforeach;
																}
																if($headerId == "PROPS" && $arResult['HAS_PROPS'] && $actuallyHasProps):?>
																	<?foreach($arItem["PROPS"] as $prop):?>
																		<div><?=$prop["NAME"]?>:&nbsp;<?=$prop["VALUE"]?></div>
																	<?endforeach?>
																<?else:
																	$value = (strpos($headerId, 'PROPERTY_')===0 ? $headerId."_VALUE" : $headerId);
																	if($arItem[$value]):?>
																		<div><?=$headerName;?>:&nbsp;<?=$arItem[$value]?></div>
																	<?endif;?>
																<?endif;?>
															<?}?>
														</div>
													<?endif;?>

													<?if($arItem["PREVIEW_PICTURE"]):?>
														</div>
													<?endif;?>
												</div>
											<?elseif($name == "DISCOUNT"):?>
												<?=CAllCurrencyLang::CurrencyFormat($arItem["PRICE"], $allCurrency);?>
											<?elseif($name == "SUMM"):?>
												<div style="font-weight:600;font-size:14px;color:#333333;"><?=CAllCurrencyLang::CurrencyFormat($arItem["PRICE"]*$arItem["QUANTITY"], $allCurrency);?></div>
											<?else:?>
												<?=$arItem[$name];?>
											<?endif;?>
										</td>
									<?endforeach;?>
								</tr>
							<?endforeach;?>

						</tbody>
					</table>
					<?if($arParams["SHOW_ORDER_SUM"]=='Y'):?>
						<div style="padding:22px 21px;text-align:right;border-bottom:1px solid #e7e7e7;background:#f7f7f7;color:#333333;">
							<div style="display:inline-block;font-size:0px;">
								<? ///// WEIGHT ?>
								<?if(floatval($arResult["ORDER_WEIGHT"])):?>
									<div>
										<div style="display:inline-block;vertical-align:top;font-size:15px;width:170px;text-align:left;"><?=Loc::getMessage('SPOD_TOTAL_WEIGHT')?></div>
										<div style="display:inline-block;vertical-align:top;font-size:16px;width:120px;text-align:right;font-weight:600;"><?=$arResult['ORDER_WEIGHT_FORMATED']?></div>
									</div>
								<?endif;?>

								<? ///// PRICE SUM ?>
								<div>
									<div style="display:inline-block;vertical-align:top;font-size:15px;width:170px;text-align:left;"><?=Loc::getMessage('SPOD_PRODUCT_SUM')?></div>
									<div style="display:inline-block;vertical-align:top;font-size:16px;width:120px;text-align:right;font-weight:600;"><?=$arResult['PRODUCT_SUM_FORMATTED']?></div>
								</div>

								<? ///// DELIVERY PRICE: print even equals 2 zero ?>
								<?if(strlen($arResult["PRICE_DELIVERY_FORMATED"])):?>
									<div>
										<div style="display:inline-block;vertical-align:top;font-size:15px;width:170px;text-align:left;"><?=Loc::getMessage('SPOD_DELIVERY')?></div>
										<div style="display:inline-block;vertical-align:top;font-size:16px;width:120px;text-align:right;font-weight:600;"><?=CAllCurrencyLang::CurrencyFormat($arResult['PRICE_DELIVERY'], $allCurrency)?></div>
									</div>
								<?endif;?>

								<? ///// TAXES DETAIL ?>
								<?foreach($arResult["TAX_LIST"] as $tax):?>
									<div>
										<div style="display:inline-block;vertical-align:top;font-size:15px;width:170px;text-align:left;"><?=$tax["TAX_NAME"]?></div>
										<div style="display:inline-block;vertical-align:top;font-size:16px;width:120px;text-align:right;font-weight:600;"><?=$tax["VALUE_MONEY_FORMATED"]?></div>
									</div>
								<?endforeach;?>

								<? ///// TAX SUM ?>
								<?if(floatval($arResult["TAX_VALUE"])):?>
									<div>
										<div style="display:inline-block;vertical-align:top;font-size:15px;width:170px;text-align:left;"><?=Loc::getMessage('SPOD_TAX')?></div>
										<div style="display:inline-block;vertical-align:top;font-size:16px;width:120px;text-align:right;font-weight:600;"><?=$arResult['TAX_VALUE_FORMATED']?></div>
									</div>
								<?endif;?>

								<? ///// DISCOUNT ?>
								<?if(floatval($arResult["DISCOUNT_VALUE"])):?>
									<div>
										<div style="display:inline-block;vertical-align:top;font-size:15px;width:170px;text-align:left;"><?=Loc::getMessage('SPOD_DISCOUNT')?></div>
										<div style="display:inline-block;vertical-align:top;font-size:16px;width:120px;text-align:right;font-weight:600;"><?=CAllCurrencyLang::CurrencyFormat($arResult['DISCOUNT_VALUE'], $allCurrency)?></div>
									</div>
								<?endif;?>

								<div>
									<div style="display:inline-block;vertical-align:top;font-size:15px;width:170px;text-align:left;"><?=Loc::getMessage('SPOD_SUMMARY')?></div>
									<div style="display:inline-block;vertical-align:top;font-size:16px;width:120px;text-align:right;font-weight:600;"><?=CAllCurrencyLang::CurrencyFormat($arResult['PRICE'], $allCurrency)?></div>
								</div>
							</div>
						</div>
					<?endif;?>
				</div>
			<?endif;?>
			<div style="text-align:center;">
				<div style="color:#555555;font-size:14px;padding: 27px 0px 17px;"><?= GetMessage("SALE_TOTAL") ?> <?=Loc::getMessage("PERSONAL_PAGE_TEXT");?></div>
				<?if($arParams["SHOW_PERSONAL"] == "Y" && $arParams["PERSONAL_PAGE"]):?>
					<a href="<?=$arParams["PERSONAL_PAGE"]?>" style="color:#fff;background:<?=$arParams["THEME_COLOR"];?>;padding:11px 19px;border-radius:3px;display: inline-block;font-size:14px;"><?=Loc::getMessage("PERSONAL_PAGE") ?></a>
				<?endif;?>
			</div>
		<?endif;?>
	</div>
<?endif;?>
