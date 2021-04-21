<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $USER;
if(!$USER->IsAuthorized())
	return;
	
global $arSetting;

if($_REQUEST["show_canceled"] == "Y")
	$page = "cancel";
elseif($_REQUEST["filter_history"] == "Y")
	$page = "all";
else
	$page = "active";?>

<div class="order-list">
	<div class="sort tabfilter order">
		<div class="sorttext"><?=GetMessage("STPOL_F_NAME")?></div>
		<a class="sortbutton active<?=($page == 'active' ? ' current' : '');?>" href="<?=($page != 'active' ? $arResult['CURRENT_PAGE'].'?filter_history=N' : 'javascript:void(0)');?>"><?=GetMessage("STPOL_CUR_ORDERS")?></a>
		<a class="sortbutton all<?=($page == 'all' ? ' current' : '');?>" href="<?=($page != 'all' ? $arResult['CURRENT_PAGE'].'?filter_history=Y' : 'javascript:void(0)');?>"><?=GetMessage("STPOL_ORDERS_HISTORY")?></a>
		<a class="sortbutton cancel<?=($page == 'cancel' ? ' current' : '');?>" href="<?=($page != 'cancel' ? $arResult['CURRENT_PAGE'].'?filter_history=Y&show_canceled=Y' : 'javascript:void(0)');?>"><?=GetMessage("STPOL_ORDERS_CANCELED")?></a>
	</div>

	<?if(!empty($arResult["ORDERS"])) {?>
		<div class="cart-items">
			<div class="equipment-order list">
				<div class="thead">					
					<div class="cart-item-number-date"><?=GetMessage("STPOL_ORDER_NUMBER_DATE")?></div>
					<div class="cart-item-status"><?=GetMessage("STPOL_ORDER_STATUS")?></div>
					<div class="cart-item-payment"><?=GetMessage("STPOL_ORDER_PAYMENT")?></div>
					<div class="cart-item-payed"><?=GetMessage("STPOL_ORDER_PAYED")?></div>
					<div class="cart-item-summa"><?=GetMessage("STPOL_ORDER_SUMMA")?></div>
				</div>
				<div class="tbody">
					<?foreach($arResult["ORDERS"] as $key => $val) {
						$accountHashNumber = md5($val["ORDER"]["ACCOUNT_NUMBER"]);?>
						<div class="tr">
							<div class="tr_into">
								<div class="tr_into_in">
									<div class="cart-item-plus-minus">
										<script type="text/javascript">
											$(document).ready(function() {
												$("#plus-minus-<?=$accountHashNumber?>").click(function() {
													var clickitem = $(this);
													if(clickitem.hasClass("plus")) {
														clickitem.removeClass().addClass("minus active");							
													} else {
														clickitem.removeClass().addClass("plus");									
													}
													$(".cart-items.basket.<?=$accountHashNumber?>, .order-recipient.<?=$accountHashNumber?>, .order-item-actions.<?=$accountHashNumber?>").slideToggle();
												});
											});
										</script>
										<a href="javascript:void(0)" id="plus-minus-<?=$accountHashNumber?>" class="plus"><i class="fa fa-plus-circle"></i><i class="fa fa-minus-circle"></i></a>
									</div>									
									<div class="cart-item-number-date">
										<span class="cart-item-number"><?=$val["ORDER"]["ACCOUNT_NUMBER"]?></span>
										<?=$val["ORDER"]["DATE_INSERT_FORMATED"];?>
									</div>
									<div class="cart-item-status">
										<?if($val["ORDER"]["CANCELED"] == "Y") {?>
											<span class="item-status-d">
												<?=GetMessage("STPOL_ORDER_DELETE");?>
											</span>
										<?} else {?>
											<span class="item-status-<?=toLower($val["ORDER"]["STATUS_ID"])?>">
												<?=$arResult["INFO"]["STATUS"][$val["ORDER"]["STATUS_ID"]]["NAME"];?>
											</span>
										<?}?>
									</div>
									<div class="cart-item-payment">
									<?foreach ($val['PAYMENT'] as $payment) {?>
										<div class="cart-item-payment-title">
											<?=$payment['PAY_SYSTEM_NAME'];?>
											<?if ($payment['PAID'] === 'N' && $payment['IS_CASH'] !== 'Y') {?>
												<?if ($val['ORDER']['IS_ALLOW_PAY'] != 'N' && $page != 'cancel') {?>
													<?if($payment['NEW_WINDOW'] === 'Y'){?>
														<a href="<?=htmlspecialcharsbx($payment['PSA_ACTION_FILE'])?>" target="_blank"><?=GetMessage("STPOL_REPEAT_PAY")?></a>
													<?}else{?>
														<a href="<?=htmlspecialcharsbx($val['ORDER']['URL_TO_DETAIL'])?>"><?=GetMessage("STPOL_REPEAT_PAY")?></a>
													<?}?>
												<?}?>
											<?}?>
										</div>
									<?}?>
									</div>
									<div class="cart-item-payed">
										<?if($val["ORDER"]["PAYED"] == "Y") {
											echo "<span class='item-payed-yes'>".GetMessage("STPOL_YES")."</span>";
										} else {
											echo GetMessage("STPOL_NO");
										}?>
									</div>
									<div class="cart-item-summa">
										<span class="sum">
											<?=$val["ORDER"]["FORMATED_PRICE"];?>
										</span>
										<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
											<span class="reference-sum">
												<?=CCurrencyLang::CurrencyFormat($val["ORDER"]["PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $val["ORDER"]["CURRENCY"], true);?>
											</span>
										<?}?>
									</div>
								</div>
								
								<div class="cart-items basket <?=$accountHashNumber?>" style="display:none;">
									<div class="equipment-order basket">
										<div class="thead">
											<div class="cart-item-name"><?=GetMessage("STPOL_ORDER_NAME")?></div>
											<div class="cart-item-price"><?=GetMessage("STPOL_ORDER_PRICE")?></div>
											<div class="cart-item-quantity"><?=GetMessage("STPOL_ORDER_QUANTITY")?></div>
											<div class="cart-item-summa"><?=GetMessage("STPOL_ORDER_SUMMA")?></div>
										</div>
										<div class="tbody">
											<?$i = 1;
											foreach($val["BASKET_ITEMS"] as $arBasketItems) {?>
												<div class="tr">
													<div class="tr_into">
														<div class="cart-item-number"><?=$i?></div>
														<div class="cart-item-image">
															<?if(is_array($arBasketItems["DETAIL_PICTURE"])) {?>
																<img src="<?=$arBasketItems['DETAIL_PICTURE']['src']?>" width="<?=$arBasketItems['DETAIL_PICTURE']['width']?>" height="<?=$arBasketItems['DETAIL_PICTURE']['height']?>" />
															<?} else {?>
																<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="30" height="30" />
															<?}?>
														</div>
														<div class="cart-item-name">
															<?if(strlen($arBasketItems["DETAIL_PAGE_URL"]) > 0) {?>
																<a href="<?=$arBasketItems["DETAIL_PAGE_URL"]?>">
															<?}
															echo $arBasketItems["NAME"];
															if(strlen($arBasketItems["DETAIL_PAGE_URL"]) > 0) {?>
																</a>
															<?}
															if(!empty($arBasketItems["PROPS"])) {?>
																<div class="item-props">
																	<?foreach($arBasketItems["PROPS"] as $props) {
																		echo "<span style='display:block;'>".$props["NAME"].": ".$props["VALUE"]."</span>";
																	}?>
																	<div class="clr"></div>
																</div>
															<?}?>
														</div>
														<div class="cart-item-price">
															<div class="price">
																<?=CCurrencyLang::CurrencyFormat($arBasketItems["PRICE"], $arBasketItems["CURRENCY"], true);?>
															</div>
															<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
																<span class="reference-price">
																	<?=CCurrencyLang::CurrencyFormat($arBasketItems["PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arBasketItems["CURRENCY"], true);?>
																</span>
															<?}?>
														</div>
														<div class="cart-item-quantity">
															<?=$arBasketItems["QUANTITY"];
															if(!empty($arBasketItems["MEASURE_TEXT"])) {
																echo " ".$arBasketItems["MEASURE_TEXT"];
															}?>
														</div>
														<div class="cart-item-summa">
															<span class="sum">
																<?=CCurrencyLang::CurrencyFormat($arBasketItems["PRICE"] * $arBasketItems["QUANTITY"], $arBasketItems["CURRENCY"], true);?>
															</span>							
															<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
																<span class="reference-sum">
																	<?=CCurrencyLang::CurrencyFormat($arBasketItems["PRICE"] * $arBasketItems["QUANTITY"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arBasketItems["CURRENCY"], true);?>
																</span>
															<?}?>
														</div>
													</div>
												</div>
												<?$i++;
											}
											if(IntVal($val["ORDER"]["DELIVERY_ID"]) > 0) {?>
												<div class="tr">
													<div class="tr_into">
														<div class="cart-itogo">
															<?=$arResult["INFO"]["DELIVERY"][$val["ORDER"]["DELIVERY_ID"]]["NAME"]?>
														</div>
														<div class="cart-allsum">
															<?if($val["ORDER"]["PRICE_DELIVERY"] > 0) {?>
																<span class="allsum">
																	<?=CCurrencyLang::CurrencyFormat($val["ORDER"]["PRICE_DELIVERY"], $val["ORDER"]["CURRENCY"], true);?>
																</span>
																<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
																	<span class="reference-allsum">
																		<?=CCurrencyLang::CurrencyFormat($val["ORDER"]["PRICE_DELIVERY"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $val["ORDER"]["CURRENCY"], true);?>
																	</span>
																<?}?>
															<?}?>
														</div>
													</div>
												</div>
											<?}?>
										</div>
										<div class="myorders_itog<?=($arSetting['REFERENCE_PRICE']['VALUE'] == 'Y' && !empty($arSetting['REFERENCE_PRICE_COEF']['VALUE']) ? ' reference' : '');?>">
											<div class="cart-itogo"><?=GetMessage("STPOL_ORDER_SUM_IT")?></div>
											<div class="cart-allsum">
												<span class="allsum">
													<?=$val["ORDER"]["FORMATED_PRICE"];?>
												</span>
												<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
													<span class="reference-allsum">
														<?=CCurrencyLang::CurrencyFormat($val["ORDER"]["PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $val["ORDER"]["CURRENCY"], true);?>
													</span>
												<?}?>
											</div>
										</div>
									</div>
								</div>

								<table class="order-recipient <?=$accountHashNumber?>" style="display:none;">
									<?if(!empty($val["ORDER"]["ORDER_PROPS"])) {										
										foreach($val["ORDER"]["ORDER_PROPS"] as $orderProps) {?>
											<tr>
												<td class="field-name"><?=$orderProps["NAME"]?>:</td>
												<td class="field-value">
													<?if($orderProps["TYPE"] == "CHECKBOX") {
														if($orderProps["VALUE"] == "Y")
															echo GetMessage("STPOL_YES");
														else
															echo GetMessage("STPOL_NO");
													} else {
														echo $orderProps["VALUE"];
													}?>
												</td>
											</tr>
										<?}
									}?>
									<?if(strlen($val["ORDER"]["USER_DESCRIPTION"]) > 0) {?>
										<tr>
											<td class="field-name"><?=GetMessage("STPOL_ORDER_USER_COMMENT")?></td>
											<td class="field-value"><?=$val["ORDER"]["USER_DESCRIPTION"]?></td>
										</tr>
									<?}?>
								</table>

								<div class="order-item-actions <?=$accountHashNumber?>" style="display:none;">
									<a class="btn_buy apuo order_repeat" href="<?=htmlspecialcharsbx($val['ORDER']['URL_TO_COPY'])?>" title="<?=GetMessage('STPOL_REPEAT_ORDER')?>"><i class="fa fa-repeat"></i><span><?=GetMessage("STPOL_REPEAT_ORDER")?></span></a>
									<?if($page != 'all' && $page != 'cancel'): ?>
									<a class="btn_buy apuo order_delete" href="<?=htmlspecialcharsbx($val['ORDER']['URL_TO_CANCEL'])?>" title="<?=GetMessage('STPOL_CANCEL_ORDER')?>"><i class="fa fa-times"></i><span><?=GetMessage("STPOL_CANCEL_ORDER")?></span></a>
									<?endif;?>
									<a class="btn_buy apuo order_detail" href="<?=htmlspecialcharsbx($val['ORDER']['URL_TO_DETAIL'])?>" title="<?=GetMessage('STPOL_DETAIL_ORDER')?>"><i class="fa fa-chevron-right"></i><span><?=GetMessage("STPOL_DETAIL_ORDER")?></span></a>
									<div class="clr"></div>
								</div>
							</div>
						</div>
					<?}?>
				</div>
			</div>
		</div>		
	<?} else {		
		echo ShowNote(GetMessage("STPOL_NO_ORDERS_NEW"), "infotext");	
	}?>
</div>

<?if(strlen($arResult["NAV_STRING"]) > 0) {?>
	<div class="navigation"><?=$arResult["NAV_STRING"]?></div>
<?}?>