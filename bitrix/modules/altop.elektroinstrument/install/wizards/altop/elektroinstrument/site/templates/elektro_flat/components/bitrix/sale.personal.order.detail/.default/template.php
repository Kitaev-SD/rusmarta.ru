<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Page\Asset;

if ($arParams['GUEST_MODE'] !== 'Y') {
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/components/bitrix/sale.order.payment.change/.default/script.js");
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/components/bitrix/sale.order.payment.change/.default/style.css");
}

CJSCore::Init(array('clipboard', 'fx'));

if (!empty($arResult['ERRORS']['FATAL'])) {
	foreach ($arResult['ERRORS']['FATAL'] as $error) {
		ShowError($error);
	}

	$component = $this->__component;

	if ($arParams['AUTH_FORM_IN_TEMPLATE'] && isset($arResult['ERRORS']['FATAL'][$component::E_NOT_AUTHORIZED])) {
		$APPLICATION->AuthForm('', false, false, 'N', false);
	}
} else {
	if (!empty($arResult['ERRORS']['NONFATAL'])) {
		foreach ($arResult['ERRORS']['NONFATAL'] as $error) {
			ShowError($error);
		}
	}
	?>
	<? //ORDER_DETAIL// ?>
	<div class="sale-order-detail">
		<div class="sale-order-detail-general">
			<?//ABOUT_ORDER//?>
			<div class="sale-order-detail-about-order sale-order-detail-item">
				<div class="sale-order-detail-about-order-container">
					<div class="sale-order-detail-about-order-title">
						<div class="sale-order-detail-about-order-title-element">
							<?=Loc::getMessage('SPOD_LIST_ORDER_INFO')?>
						</div>
					</div>
					<div class="sale-order-detail-about-order-inner-container">
						<div class="sale-order-detail-about-order-inner-container-name">
							<div class="sale-order-detail-about-order-inner-container-name-title">
								<? $userName = $arResult["USER"]["NAME"] ." ". $arResult["USER"]["SECOND_NAME"] ." ". $arResult["USER"]["LAST_NAME"];
								if (strlen($userName) || strlen($arResult['FIO'])) {
									echo Loc::getMessage('SPOD_LIST_FIO').':';
								} else {
									echo Loc::getMessage('SPOD_LOGIN').':';
								} ?>
							</div>
							<div class="sale-order-detail-about-order-inner-container-name-detail">
								<? if (strlen($userName)) {
									echo htmlspecialcharsbx($userName);
								} elseif (strlen($arResult['FIO'])) {
									echo htmlspecialcharsbx($arResult['FIO']);
								} else {
									echo htmlspecialcharsbx($arResult["USER"]['LOGIN']);
								} ?>
							</div>
						</div>
						<div class="sale-order-detail-about-order-inner-container-status">
							<div class="sale-order-detail-about-order-inner-container-status-title">
								<?=Loc::getMessage('SPOD_LIST_CURRENT_STATUS', array('#DATE_ORDER_CREATE#' => $arResult["DATE_INSERT_FORMATED"]))?>
							</div>
							<div class="sale-order-detail-about-order-inner-container-status-detail">
								<?if ($arResult['CANCELED'] !== 'Y') {?>
									<span class="item-status-<?=toLower($arResult["STATUS"]["ID"])?>">
										<?=htmlspecialcharsbx($arResult["STATUS"]["NAME"]);?>
									</span>
								<?} else {?>
									<span class="item-status-d">
										<?=Loc::getMessage('SPOD_ORDER_CANCELED');?>
									</span>
								<?}?>
							</div>
						</div>
						<div class="sale-order-detail-about-order-inner-container-price">
							<div class="sale-order-detail-about-order-inner-container-price-title">
								<?=Loc::getMessage('SPOD_ORDER_PRICE')?>:
							</div>
							<div class="sale-order-detail-about-order-inner-container-price-detail">
								<?=$arResult["PRICE_FORMATED"]?>
							</div>
						</div>
						<? if ($arParams['GUEST_MODE'] !== 'Y') { ?>
							<div class="sale-order-detail-about-order-inner-container-repeat order-item-actions">
								<div>
									<a class="btn_buy apuo order_repeat" href="<?=$arResult["URL_TO_COPY"]?>" title="<?=Loc::getMessage('SPOD_ORDER_REPEAT')?>">
										<i class="fa fa-repeat"></i>
										<span><?=Loc::getMessage('SPOD_ORDER_REPEAT')?></span>
									</a>
								</div>
								<? if ($arResult["CAN_CANCEL"] === "Y") { ?>
									<div>
										<a class="btn_buy apuo order_delete" href="<?=$arResult["URL_TO_CANCEL"]?>" title="<?=Loc::getMessage('SPOD_ORDER_CANCEL')?>">
											<i class="fa fa-times"></i>
											<span><?=Loc::getMessage('SPOD_ORDER_CANCEL')?></span>
										</a>
									</div>
								<? } ?>
							</div>
						<?}?>
					</div>
					<? //link for show or hide details information order ?>
					<a id="sod-user-info-link" class="sale-order-detail-about-order-inner-container-name-read">
						<span><?=Loc::getMessage('SPOD_USER_INFORMATION');?></span>
						<i class="fa fa-minus read-less-i"></i>
						<i class="fa fa-plus read-more-i"></i>
					</a>
					<? //details information order ?>
					<div id="sod-order-info-block" class="sale-order-detail-about-order-inner-container-details">
						<ul class="sale-order-detail-about-order-inner-container-details-list">
							<? if (strlen($arResult["USER"]["LOGIN"]) && !in_array("LOGIN", $arParams['HIDE_USER_INFO'])) { ?>
								<li class="sale-order-detail-about-order-inner-container-list-item">
									<?=Loc::getMessage('SPOD_LOGIN')?>:
									<div class="sale-order-detail-about-order-inner-container-list-item-element">
										<?=htmlspecialcharsbx($arResult["USER"]["LOGIN"])?>
									</div>
								</li>
							<? }
							if (strlen($arResult["USER"]["EMAIL"]) && !in_array("EMAIL", $arParams['HIDE_USER_INFO'])) { ?>
								<li class="sale-order-detail-about-order-inner-container-list-item">
									<?=Loc::getMessage('SPOD_EMAIL')?>:
									<a class="sale-order-detail-about-order-inner-container-list-item-link" href="mailto:<?=htmlspecialcharsbx($arResult["USER"]["EMAIL"])?>">
									<?=htmlspecialcharsbx($arResult["USER"]["EMAIL"])?>
									</a>
								</li>
							<? }
							if (strlen($arResult["USER"]["PERSON_TYPE_NAME"]) && !in_array("PERSON_TYPE_NAME", $arParams['HIDE_USER_INFO'])) { ?>
								<li class="sale-order-detail-about-order-inner-container-list-item">
									<?=Loc::getMessage('SPOD_PERSON_TYPE_NAME')?>:
									<div class="sale-order-detail-about-order-inner-container-list-item-element">
										<?=htmlspecialcharsbx($arResult["USER"]["PERSON_TYPE_NAME"])?>
									</div>
								</li>
							<? }
							if (isset($arResult["ORDER_PROPS"])) {
								foreach ($arResult["ORDER_PROPS"] as $property) { ?>
									<li class="sale-order-detail-about-order-inner-container-list-item">
										<?=htmlspecialcharsbx($property['NAME']) ?>:
										<div class="sale-order-detail-about-order-inner-container-list-item-element">
											<? if ($property["TYPE"] == "Y/N") {
												echo Loc::getMessage('SPOD_' . ($property["VALUE"] == "Y" ? 'YES' : 'NO'));
											} else {
												if ($property['MULTIPLE'] == 'Y' && $property['TYPE'] !== 'FILE' && $property['TYPE'] !== 'LOCATION') {
													$propertyList = unserialize($property["VALUE"]);
													foreach ($propertyList as $propertyElement) {
														echo $propertyElement . '</br>';
													}
												} elseif ($property['TYPE'] == 'FILE') {
													echo $property["VALUE"];
												} else {
													echo htmlspecialcharsbx($property["VALUE"]);
												}
											} ?>
										</div>
									</li>
								<? }
							} ?>
							<? if (strlen($arResult["USER_DESCRIPTION"])) { ?>
								<li class="sale-order-detail-about-order-inner-container-list-item">
									<?=Loc::getMessage('SPOD_ORDER_DESC') ?>:
									<div class="sale-order-detail-about-order-inner-container-list-item-element">
										<?=nl2br(htmlspecialcharsbx($arResult["USER_DESCRIPTION"]))?>
									</div>
								</li>
							<? } ?>
						</ul>
					</div>
				</div>
			</div>
			<?//PAYMENT_OPTIONS//?>
			<div class="sale-order-detail-payment-options sale-order-detail-item">
				<div class="sale-order-detail-payment-options-container">
					<div class="sale-order-detail-payment-options-title">
						<div class="sale-order-detail-payment-options-title-element">
							<?=Loc::getMessage('SPOD_ORDER_PAYMENT')?>
						</div>
					</div>
					<div class="sale-order-detail-payment-options-inner-container">
						<div class="sale-order-detail-payment-options-info">
							<div class="sale-order-detail-payment-options-info-image"></div>
							<div class="sale-order-detail-payment-options-info-container">
								<div class="sale-order-detail-payment-options-info-order-number">
									<?=Loc::getMessage(
										'SPOD_SUB_ORDER_TITLE', 
										array(
											"#ACCOUNT_NUMBER#" => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"]),
											"#DATE_ORDER_CREATE#" => $arResult["DATE_INSERT_FORMATED"]
										)
									);?>
									<? if ($arResult['CANCELED'] !== 'Y') { ?>
										<span class="item-status-<?=toLower($arResult["STATUS"]["ID"])?>">
											<?=htmlspecialcharsbx($arResult["STATUS"]["NAME"]);?>
										</span>
									<?
									} else {
										echo Loc::getMessage('SPOD_ORDER_CANCELED');
									} ?>
								</div>
								<div class="sale-order-detail-payment-options-info-total-price">
									<?=Loc::getMessage('SPOD_ORDER_PRICE_FULL')?>:
									<span><?=$arResult["PRICE_FORMATED"]?></span>
								</div>
							</div>
						</div>
						<? //sale-order-detail-payment-options-info// ?>
						<div id="sod-payment-options-block" class="sale-order-detail-payment-options-methods-container">
							<? foreach ($arResult['PAYMENT'] as $keyPayment => $payment) { ?>
								<div class="payment-options-methods-row">
									<div class="sale-order-detail-payment-options-methods">
										<div class="sale-order-detail-payment-options-methods-information-block">
											<div class="sale-order-detail-payment-options-methods-image-container">
												<span class="sale-order-detail-payment-options-methods-image-element" style="background-image: url('<?=strlen($payment['PAY_SYSTEM']["SRC_LOGOTIP"]) ? htmlspecialcharsbx($payment['PAY_SYSTEM']["SRC_LOGOTIP"]) : '/bitrix/images/sale/nopaysystem.gif'?>');"></span>
											</div>
											<div class="sale-order-detail-payment-options-methods-info">
												<? if ($payment['PAID'] === 'Y') { ?>
													<div class="alertMsg good">
														<i class="fa fa-check"></i>
														<span class="text"><?=Loc::getMessage('SPOD_PAYMENT_PAID')?></span>
													</div>
												<?
												} elseif ($arResult['IS_ALLOW_PAY'] == 'N') { ?>
													<div class="alertMsg info">
														<i class="fa fa-info"></i>
														<span class="text"><?=Loc::getMessage('SPOD_TPL_RESTRICTED_PAID')?></span>
													</div>
												<? } else { ?>
													<span class="alertMsg bad">
														<i class="fa fa-exclamation-triangle"></i>
														<span class="text"><?=Loc::getMessage('SPOD_PAYMENT_UNPAID')?></span>
													</span>
												<? } ?>
												<div class="sale-order-detail-payment-options-methods-info-container">
													<div class="sale-order-detail-payment-options-methods-info-container-block">
														<div class="sale-order-detail-payment-options-methods-info-title">
															<div class="sale-order-detail-methods-title">
																
																<? $paymentData[$payment['ACCOUNT_NUMBER']] = array(
																	"payment" => $payment['ACCOUNT_NUMBER'],
																	"order" => $arResult['ACCOUNT_NUMBER'],
																	"allow_inner" => $arParams['ALLOW_INNER'],
																	"only_inner_full" => $arParams['ONLY_INNER_FULL']
																);
																$paymentSubTitle = Loc::getMessage('SPOD_TPL_BILL')." ".Loc::getMessage('SPOD_NUM_SIGN').$payment['ACCOUNT_NUMBER'];
																if(isset($payment['DATE_BILL'])) {
																	$paymentSubTitle .= " ".Loc::getMessage('SPOD_FROM')." ".$payment['DATE_BILL']->format($arParams['ACTIVE_DATE_FORMAT']);
																}
																$paymentSubTitle .=",";
																echo htmlspecialcharsbx($paymentSubTitle);
																?>
																<span class="sale-order-list-payment-title-element"><?=$payment['PAY_SYSTEM_NAME']?></span>
															</div>
														</div>
														<div class="sale-order-detail-payment-options-methods-info-total-price">
															<span class="sale-order-detail-sum-name"><?=Loc::getMessage('SPOD_ORDER_PRICE_BILL')?>:</span>
															<span class="sale-order-detail-sum-number"><?=$payment['PRICE_FORMATED']?></span>
														</div>
														<?
														if ($arResult["IS_ALLOW_PAY"] !== "N" && $payment['PAID'] !== 'Y' && $arResult["CANCELED"] !== "Y" && $payment['PAY_SYSTEM']["IS_CASH"] !== "Y") {
															$paySystemService = Bitrix\Sale\PaySystem\Manager::getObjectById($payment["PAY_SYSTEM_ID"]);
															if(CSalePdf::isPdfAvailable() && $paySystemService->isAffordPdf()) { ?>
																<div class="sale-order-detail-payment-options-download-pdf">
																	<?=Loc::getMessage("SPOD_PAY_PDF", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."/?ORDER_ID=".htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"])."&pdf=1&DOWNLOAD=Y".(!empty($_REQUEST['access'])? "&HASH=".$_REQUEST['access']: "")));?>
																</div>
															<?}
														}?>
														<? if (!empty($payment['CHECK_DATA'])) {
															$listCheckLinks = "";
															foreach ($payment['CHECK_DATA'] as $checkInfo) {
																$title = Loc::getMessage('SPOD_CHECK_NUM', array('#CHECK_NUMBER#' => $checkInfo['ID']))." - ". htmlspecialcharsbx($checkInfo['TYPE_NAME']);
																if (strlen($checkInfo['LINK']) > 0) {
																	$link = $checkInfo['LINK'];
																	$listCheckLinks .= "<div><a href='$link' target='_blank'>$title</a></div>";
																}
															}
															if (strlen($listCheckLinks) > 0) { ?>
																<div class="sale-order-detail-payment-options-methods-info-total-check">
																	<div class="sale-order-detail-sum-check-left"><?=Loc::getMessage('SPOD_CHECK_TITLE')?>:</div>
																	<div class="sale-order-detail-sum-check-left">
																		<?=$listCheckLinks?>
																	</div>
																</div>
																<div class="clr"></div>
															<? }
														}
														if ( $payment['PAID'] !== 'Y' && $arResult['CANCELED'] !== 'Y' && $arParams['GUEST_MODE'] !== 'Y' && $arResult['LOCK_CHANGE_PAYSYSTEM'] !== 'Y' ) {?>
															<div class="sale-order-detail-payment-options-methods-info-change">
																<a href="#" id="<?=$payment['ACCOUNT_NUMBER']?>" class="sale-order-detail-payment-options-methods-info-change-link">
																	<i class="fa fa-angle-down"></i>
																	<?=Loc::getMessage('SPOD_CHANGE_PAYMENT_TYPE')?>
																</a>
															</div>
														<? } ?>
														<? if ($arResult['IS_ALLOW_PAY'] === 'N' && $payment['PAID'] !== 'Y') { ?>
															<div class="sale-order-detail-status-restricted-message-block">
																<span class="sale-order-detail-status-restricted-message"><?=Loc::getMessage('SOPD_TPL_RESTRICTED_PAID_MESSAGE')?></span>
															</div>
														<? } ?>
													</div>
													<? if ($payment['PAY_SYSTEM']["IS_CASH"] !== "Y") { ?>
														<div class="sale-order-detail-payment-options-methods-button-container">
															<? if ($payment['PAY_SYSTEM']['PSA_NEW_WINDOW'] === 'Y' && $arResult["IS_ALLOW_PAY"] !== "N") { ?>
																<a class="btn_buy popdef" target="_blank" href="<?=htmlspecialcharsbx($payment['PAY_SYSTEM']['PSA_ACTION_FILE'])?>">
																	<?=Loc::getMessage('SPOD_ORDER_PAY') ?>
																</a>
															<? } else {
																if ($payment["PAID"] === "Y" || $arResult["CANCELED"] === "Y" || $arResult["IS_ALLOW_PAY"] === "N") { ?>
																<? } else { ?>
																	<a class="active-button btn_buy popdef"><?= Loc::getMessage('SPOD_ORDER_PAY') ?></a>
																<? }
															} ?>
														</div>
													<? } ?>
												</div>
											</div>
											<div class="sale-order-detail-payment-inner-row-template">
												<a class="btn_buy apuo sale-order-list-cancel-payment">
													<i class="fa fa-arrow-left"></i><?=Loc::getMessage('SPOD_CANCEL_PAYMENT')?>
												</a>
											</div>
										</div>
										<?
										if ($payment["PAID"] !== "Y" && $payment['PAY_SYSTEM']["IS_CASH"] !== "Y" && $payment['PAY_SYSTEM']['PSA_NEW_WINDOW'] !== 'Y' && $arResult['CANCELED'] !== 'Y' && $arResult["IS_ALLOW_PAY"] !== "N") {
										?>
											<div class="sale-order-detail-payment-options-methods-template">
												<span class="sale-paysystem-close active-button">
													<span class="sale-paysystem-close-item sale-order-payment-cancel"></span>
												</span><? //sale-paysystem-close// ?>
												<?=$payment['BUFFERED_OUTPUT']?>
											</div>
										<?
										}
										?>
									</div>
								</div>
							<?
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<?
			//DELIVERY_OPTIONS//
			if (count($arResult['SHIPMENT'])) {
			?>
				<div class="sale-order-detail-payment-options sale-order-detail-item">
					<div class="sale-order-detail-payment-options-container">
						<div class="sale-order-detail-payment-options-title">
							<div class="sale-order-detail-payment-options-title-element">
								<?=Loc::getMessage('SPOD_ORDER_SHIPMENT') ?>
							</div>
						</div>
						<div id="sod-delivery-options-block" class="sale-order-detail-payment-options-inner-container">
							<?
							foreach ($arResult['SHIPMENT'] as $shipment) {
							?>
								<div class="sale-order-detail-payment-options-shipment-container">
									<div class="sale-order-detail-payment-options-shipment">
										<div class="sale-order-detail-payment-options-shipment-image-container">
											<?
											if (strlen($shipment['DELIVERY']["SRC_LOGOTIP"])) {
											?>
												<span class="sale-order-detail-payment-options-shipment-image-element" style="background-image: url('<?=htmlspecialcharsbx($shipment['DELIVERY']["SRC_LOGOTIP"])?>')"></span>
											<?
											}
											?>
										</div>
										<div class="sale-order-detail-payment-options-methods-shipment-list">
											<div class="sale-order-detail-payment-options-methods-shipment-list-item-title">
												<?
												//change date//
												if (!strlen($shipment['PRICE_DELIVERY_FORMATED'])) {
													$shipment['PRICE_DELIVERY_FORMATED'] = 0;
												}
												$shipmentRow = Loc::getMessage('SPOD_SUB_ORDER_SHIPMENT')." ".Loc::getMessage('SPOD_NUM_SIGN').$shipment["ACCOUNT_NUMBER"];
												if ($shipment["DATE_DEDUCTED"]) {
													$shipmentRow .= " ".Loc::getMessage('SPOD_FROM')." ".$shipment["DATE_DEDUCTED"]->format($arParams['ACTIVE_DATE_FORMAT']);
												}
												$shipmentRow = htmlspecialcharsbx($shipmentRow);
												$shipmentRow .= ", ".Loc::getMessage('SPOD_SUB_PRICE_DELIVERY', array('#PRICE_DELIVERY#' => $shipment['PRICE_DELIVERY_FORMATED']));
												echo $shipmentRow;
												?>
											</div>
											<?
											if (strlen($shipment["DELIVERY_NAME"])) {
											?>
												<div class="sale-order-detail-payment-options-methods-shipment-list-item">
													<?=Loc::getMessage('SPOD_ORDER_DELIVERY');?>: <span style="font-weight: 700; color: #000;"><?=htmlspecialcharsbx($shipment["DELIVERY_NAME"]);?></span>
												</div>
											<?
											}
											?>
											<div class="sale-order-detail-payment-options-methods-shipment-list-item">
												<?=Loc::getMessage('SPOD_ORDER_SHIPMENT_STATUS');?>: <span style="font-weight: 700; color: #000;"><?=htmlspecialcharsbx($shipment['STATUS_NAME']);?></span>
											</div>
											<?
											if (strlen($shipment['TRACKING_NUMBER'])) {
											?>
											<div class="sale-order-detail-payment-options-methods-shipment-list-item">
												<span class="sale-order-list-shipment-id-name"><?=Loc::getMessage('SPOD_ORDER_TRACKING_NUMBER')?>:</span>
												<span id="sod-tracking-id" class="sale-order-detail-shipment-id"><?=htmlspecialcharsbx($shipment['TRACKING_NUMBER'])?></span>
												<i class="fa fa-clone sale-order-detail-shipment-id-icon" aria-hidden="true"></i>
											</div>
											<?}?>
											<?//EXTRA_SERVICE//
											if(is_array($shipment['EXTRA_SERVICE']) && !empty($shipment['EXTRA_SERVICE'])) {
												foreach($shipment['EXTRA_SERVICE'] as $keyEX => $arEX) {
													if($arEX['PARAMS']['TYPE'] === "Y/N" && $arEX['VALUE'] === "Y") {?>
														<div class="sale-order-detail-payment-options-methods-shipment-list-item">
															<?=htmlspecialcharsbx($arEX['NAME'])?>: <span style="font-weight: 700; color: #000;"><?=SaleFormatCurrency($arEX['PARAMS']['PRICE'], $arResult["CURRENCY"]);?></span>
														</div>
													<?} elseif($arEX['PARAMS']['TYPE'] === "STRING" && !empty($arEX['VALUE'])) {?>
														<div class="sale-order-detail-payment-options-methods-shipment-list-item">
															<?=htmlspecialcharsbx($arEX['NAME'])?>: <span style="font-weight: 700; color: #000;"><?=SaleFormatCurrency($arEX['PARAMS']['PRICE'] * $arEX['VALUE'], $arResult["CURRENCY"]);?></span>
														</div>
													<?} elseif($arEX['PARAMS']['TYPE'] === "ENUM" && !empty($arEX['VALUE'])) {?>
														<div class="sale-order-detail-payment-options-methods-shipment-list-item">
															<?=htmlspecialcharsbx($arEX['NAME'])?>: <span style="font-weight: 700; color: #000;"><?=htmlspecialcharsbx($arEX['PARAMS']['PRICES'][$arEX['VALUE']]['TITLE'])?> - <?=SaleFormatCurrency($arEX['PARAMS']['PRICES'][$arEX['VALUE']]['PRICE'], $arResult["CURRENCY"]);?></span>
														</div>
													<?}
												}
											}?>
											<div class="sale-order-detail-payment-options-methods-shipment-list-item-link">
												<a class="sale-order-detail-show-link"><i class="fa fa-angle-down"></i><?=Loc::getMessage('SPOD_LIST_SHOW_ALL');?></a>
												<a class="sale-order-detail-hide-link"><i class="fa fa-angle-up"></i><?=Loc::getMessage('SPOD_LIST_LESS');?></a>
											</div>
										</div>
										<?
										if (strlen($shipment['TRACKING_URL'])) {
										?>
											<div class="sale-order-detail-payment-options-shipment-button-container">
												<a class="sale-order-detail-payment-options-shipment-button-element" href="<?=$shipment['TRACKING_URL']?>">
													<?= Loc::getMessage('SPOD_ORDER_CHECK_TRACKING')?>
												</a>
											</div>
										<?
										}
										?>
										<?//ORDER_DELIVERI_MAP_STORE//?>
										<div class="sale-order-detail-payment-options-shipment-composition-map">
											<?
											$store = $arResult['DELIVERY']['STORE_LIST'][$shipment['STORE_ID']];
											if (isset($store)) {
											?>
												<div class="sale-order-detail-map-container">
													<div class="sale-order-detail-payment-options-shipment-composition-map-title">
														<?=Loc::getMessage('SPOD_SHIPMENT_STORE');?>
													</div>
													<?
													$APPLICATION->IncludeComponent(
														"bitrix:map.yandex.view",
														"",
														Array(
															"INIT_MAP_TYPE" => "COORDINATES",
															"MAP_DATA" =>   serialize(
																array(
																	'yandex_lon' => $store['GPS_S'],
																	'yandex_lat' => $store['GPS_N'],
																	'PLACEMARKS' => array(
																		array(
																			"LON" => $store['GPS_S'],
																			"LAT" => $store['GPS_N'],
																			"TEXT" => htmlspecialcharsbx($store['TITLE'])
																		)
																	)
																)
															),
															"MAP_WIDTH" => "100%",
															"MAP_HEIGHT" => "300",
															"CONTROLS" => array("ZOOM", "SMALLZOOM", "SCALELINE"),
															"OPTIONS" => array(
																"ENABLE_DRAGGING",
																"ENABLE_SCROLL_ZOOM",
																"ENABLE_DBLCLICK_ZOOM"
															),
															"MAP_ID" => ""
														)
													);
													?>
												</div>
												<?
												if (strlen($store['ADDRESS'])) {
												?>
													<div class="sale-order-detail-payment-options-shipment-map-address">
														<span class="sale-order-detail-payment-options-shipment-map-address-title">
															<?=Loc::getMessage('SPOD_STORE_ADDRESS')?>:
														</span>
														<span class="sale-order-detail-payment-options-shipment-map-address-element">
															<?=htmlspecialcharsbx($store['ADDRESS'])?>
														</span>
													</div>
												<?
												}
											}
											?>
											<div class="sale-order-detail-payment-options-shipment-composition-container">
												<div class="sale-order-detail-order-section bx-active">
													<div class="sale-order-detail-order-section-content">
														<div class="sale-order-detail-order-table-fade sale-order-detail-order-table-fade-right">
															<div style="width: 100%; overflow-x: auto; overflow-y: hidden;">
																<div class="sale-order-detail-order-item-table">
																	<div class="sale-order-detail-order-item-tr">
																		<div class="sale-order-detail-order-item-td">
																			<div class="sale-order-detail-order-item-td-title">
																				<?=Loc::getMessage('SPOD_ORDER_SHIPMENT_BASKET')?>
																			</div>
																		</div>
																		<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
																			<div class="sale-order-detail-order-item-td-title">
																				<?= Loc::getMessage('SPOD_QUANTITY')?>
																			</div>
																		</div>
																	</div>
																	<?
																	$i = 1;
																	foreach ($shipment['ITEMS'] as $item) {
																		$basketItem = $arResult['BASKET'][$item['BASKET_ID']];
																	?>
																		<div class="sale-order-detail-order-item-tr sale-order-detail-order-basket-info sale-order-detail-order-item-tr-first">
																			<div class="sale-order-detail-order-item-td m__wight">
																				<div class="sale-order-detail-order-item-block">
																					<div class="sale-order-detail-order-item-number"><?=$i?></div>
																					<div class="sale-order-detail-order-item-img-block">
																						<a href="<?=htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL'])?>">
																							<?
																							if (strlen($basketItem['DETAIL_PICTURE']['src'])) {
																								$imageSrc = $basketItem['DETAIL_PICTURE']['src'];
																								$imagesW = $basketItem['DETAIL_PICTURE']['width'];
																								$imagesH = $basketItem['DETAIL_PICTURE']['height'];
																							} else {
																								$imageSrc = SITE_TEMPLATE_PATH.'/images/no-photo.jpg';
																								$imagesW = 42;
																								$imagesH = 42;
																							}
																							?>
																							<div class="sale-order-detail-order-item-imgcontainer" 
																								style="
																									background-image: url(<?=$imageSrc?>);
																									background-image: -webkit-image-set(url(<?=$imageSrc?>) 1x, url(<?=$imageSrc?>) 2x);
																									width: <?=$imagesW?>px;
																									height: <?=$imagesH?>px;
																								"
																							></div>
																						</a>
																					</div>
																					<div class="sale-order-detail-order-item-content">
																						<div class="sale-order-detail-order-item-title">
																							<a href="<?=htmlspecialcharsbx($basketItem['DETAIL_PAGE_URL'])?>"><?=htmlspecialcharsbx($basketItem['NAME'])?></a>
																						</div>
																						<?
																						if (isset($basketItem['PROPS']) && is_array($basketItem['PROPS'])) {
																							foreach ($basketItem['PROPS'] as $itemProps) {
																						?>
																							<div class="sale-order-detail-order-item-color">
																								<span class="sale-order-detail-order-item-color-name"><?=htmlspecialcharsbx($itemProps['NAME']) ?>:</span>
																								<span class="sale-order-detail-order-item-color-type"><?= htmlspecialcharsbx($itemProps['VALUE']) ?></span>
																							</div>
																						<?
																							}
																						}
																						?>
																					</div>
																				</div>
																			</div>
																			<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right mob-text-left">
																				<div class="sale-order-detail-order-item-td-title sale-order-detail-order-item-td-title-mob">
																					<?= Loc::getMessage('SPOD_QUANTITY')?>
																				</div>
																				<div class="sale-order-detail-order-item-td-text mob-text-left">
																					<span><?=$item['QUANTITY']?>&nbsp;
																					<? if(strlen($basketItem['MEASURE_NAME'])) {
																						echo htmlspecialcharsbx($basketItem['MEASURE_NAME']);
																					} else {
																						echo Loc::getMessage('SPOD_DEFAULT_MEASURE');
																					}
																					?>
																					</span>
																				</div>
																			</div>
																		</div>
																	<?
																	$i++;
																	}
																	?>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?
							}
							?>
						</div>
					</div>
				</div>
			<?
			}
			?>
			<?//LIST_ORDER_ELEMENT//?>
			<div class="sale-order-detail-payment-options-order-content  sale-order-detail-item">
				<div class="sale-order-detail-payment-options-order-content-container">
					<?//ORDER_DETAIL_ELEMENT//?>
					<div class="sale-order-detail-order-section bx-active">
						<div class="sale-order-detail-order-section-content">
							<div class="sale-order-detail-order-table-fade sale-order-detail-order-table-fade-right">
								<div style="width: 100%; overflow-x: auto; overflow-y: hidden;">
									<div class="sale-order-detail-order-item-table">
										<div class="sale-order-detail-order-item-tr sale-order-detail-order-title">
											<div class="sale-order-detail-order-item-td">
												<div class="sale-order-detail-order-item-td-title">
													<?= Loc::getMessage('SPOD_ORDER_BASKET')?>
												</div>
											</div>
											<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
												<div class="sale-order-detail-order-item-td-title">
													<?= Loc::getMessage('SPOD_PRICE')?>
												</div>
											</div>
											<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
												<div class="sale-order-detail-order-item-td-title">
													<?= Loc::getMessage('SPOD_QUANTITY')?>
												</div>
											</div>
											<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right">
												<div class="sale-order-detail-order-item-td-title">
													<?= Loc::getMessage('SPOD_ORDER_PRICE')?>
												</div>
											</div>
										</div>
										<?
										$i = 1;
										foreach ($arResult['BASKET'] as $basketItem) {
										?>
											<div class="sale-order-detail-order-item-tr sale-order-detail-order-basket-info sale-order-detail-order-item-tr-first">
												<div class="sale-order-detail-order-item-td m__wight">
													<div class="sale-order-detail-order-item-block">
														<div class="sale-order-detail-order-item-number"><?=$i?></div>
														<div class="sale-order-detail-order-item-img-block">
															<a href="<?=$basketItem['DETAIL_PAGE_URL']?>">
																<?
																if (strlen($basketItem['DETAIL_PICTURE']['src'])) {
																	$imageSrc = $basketItem['DETAIL_PICTURE']['src'];
																	$imagesW = $basketItem['DETAIL_PICTURE']['width'];
																	$imagesH = $basketItem['DETAIL_PICTURE']['height'];
																} else {
																	$imageSrc = SITE_TEMPLATE_PATH.'/images/no-photo.jpg';
																	$imagesW = 30;
																	$imagesH = 30;
																}
																?>
																<div class="sale-order-detail-order-item-imgcontainer" 
																	style="
																		background-image: url(<?=$imageSrc?>);
																		background-image: -webkit-image-set(url(<?=$imageSrc?>) 1x, url(<?=$imageSrc?>) 2x);
																		width: <?=$imagesW?>px;
																		height: <?=$imagesH?>px;
																		"
																></div>
															</a>
														</div>
														<div class="sale-order-detail-order-item-content">
															<div class="sale-order-detail-order-item-title">
																<a href="<?=$basketItem['DETAIL_PAGE_URL']?>">
																	<?=htmlspecialcharsbx($basketItem['NAME'])?>
																</a>
															</div>
															<?
															if (isset($basketItem['PROPS']) && is_array($basketItem['PROPS'])) { 
																foreach ($basketItem['PROPS'] as $itemProps) {
															?>
																<div class="sale-order-detail-order-item-color">
																	<span class="sale-order-detail-order-item-color-name"><?=htmlspecialcharsbx($itemProps['NAME'])?>:</span>
																	<span class="sale-order-detail-order-item-color-type"><?=htmlspecialcharsbx($itemProps['VALUE'])?></span>
																</div>
															<?
																}
															}
															?>
														</div>
													</div>
												</div>
												<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right mob-text-left">
													<div class="sale-order-detail-order-item-td-title sale-order-detail-order-item-td-title-mob">
														<?= Loc::getMessage('SPOD_PRICE')?>
													</div>
													<div class="sale-order-detail-order-item-td-text mob-text-left">
														<?if (strlen($basketItem["DISCOUNT_PRICE_PERCENT_FORMATED"])) {?>
															<div class="old-price"><?=$basketItem['BASE_PRICE_FORMATED']?></div>
														<?}?>
														<div class="bx-price"><?=$basketItem['PRICE_FORMATED']?></div>
													</div>
												</div>
												<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right mob-text-center">
													<div class="sale-order-detail-order-item-td-title sale-order-detail-order-item-td-title-mob">
														<?= Loc::getMessage('SPOD_QUANTITY')?>
													</div>
													<div class="sale-order-detail-order-item-td-text mob-text-center">
													<span><?=$basketItem['QUANTITY']?>&nbsp;
														<? if (strlen($basketItem['MEASURE_NAME'])) {
															echo htmlspecialcharsbx($basketItem['MEASURE_NAME']);
														} else {
															echo Loc::getMessage('SPOD_DEFAULT_MEASURE');
														}
														?></span>
													</div>
												</div>
												<div class="sale-order-detail-order-item-td sale-order-detail-order-item-properties bx-text-right mob-text-right mob-text-right">
													<div class="sale-order-detail-order-item-td-title sale-order-detail-order-item-td-title-mob"><?=Loc::getMessage('SPOD_ORDER_PRICE')?></div>
													<div class="sale-order-detail-order-item-td-text mob-text-right">
														<div class="bx-price all mob-text-right"><?=$basketItem['FORMATED_SUM']?></div>
													</div>
												</div>
											</div>
										<?
										$i++;
										}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?//TOTAL_ORDER//?>
			<div class="sale-order-detail-total-payment sale-order-detail-item">
				<div class="sale-order-detail-total-payment-container">
					<?
					if (floatval($arResult["ORDER_WEIGHT"])):
					?>
						<div class="sale-order-detail-total-payment-list">
							<div class="sale-order-detail-total-payment-list-left-item">
								<?=Loc::getMessage('SPOD_TOTAL_WEIGHT');?>:
							</div>
							<div class="sale-order-detail-total-payment-list-right-item">
								<?=$arResult['ORDER_WEIGHT_FORMATED'];?>
							</div>
						</div>
					<?
					endif;
					if ($arResult['PRODUCT_SUM_FORMATED'] != $arResult['PRICE_FORMATED'] && !empty($arResult['PRODUCT_SUM_FORMATED'])):
					?>
						<div class="sale-order-detail-total-payment-list">
							<div class="sale-order-detail-total-payment-list-left-item">
								<?= Loc::getMessage('SPOD_COMMON_SUM');?>:
							</div>
							<div class="sale-order-detail-total-payment-list-right-item">
								<?=$arResult['PRODUCT_SUM_FORMATED'];?>
							</div>
						</div>
					<?
					endif;
					if (strlen($arResult["PRICE_DELIVERY_FORMATED"])):
					?>
						<div class="sale-order-detail-total-payment-list">
							<div class="sale-order-detail-total-payment-list-left-item">
								<?=Loc::getMessage('SPOD_DELIVERY');?>:
							</div>
							<div class="sale-order-detail-total-payment-list-right-item">
								<?=$arResult["PRICE_DELIVERY_FORMATED"];?>
							</div>
						</div>
					<?
					endif;
					if ((float)$arResult["TAX_VALUE"] > 0):
					?>
						<div class="sale-order-detail-total-payment-list">
							<div class="sale-order-detail-total-payment-list-left-item">
								<?=Loc::getMessage('SPOD_TAX');?>:
							</div>
							<div class="sale-order-detail-total-payment-list-right-item">
								<?=$arResult["TAX_VALUE_FORMATED"];?>
							</div>
						</div>
					<?
					endif;
					?>
					<div class="sale-order-detail-total-payment-list">
						<div class="sale-order-detail-total-payment-list-left-item sale-order-detail-total-payment-list-left-item-price">
							<?=Loc::getMessage('SPOD_SUMMARY');?>:
						</div>
						<div class="sale-order-detail-total-payment-list-right-item sale-order-detail-total-payment-list-right-item-price">
							<?=$arResult['PRICE_FORMATED'];?>
						</div>
					</div>
				</div>
			</div>
		</div><?//sale-order-detail-general//?>
	</div>
	<?
	$javascriptParams = array(
		"url" => CUtil::JSEscape($this->__component->GetPath().'/ajax.php'),
		"templateFolder" => CUtil::JSEscape($templateFolder),
		"paymentList" => $paymentData
	);
	$javascriptParams = CUtil::PhpToJSObject($javascriptParams);
	?>
	<script>
		BX.Sale.PersonalOrderComponent.PersonalOrderDetail.init(<?=$javascriptParams?>);
	</script>
<?
}
?>