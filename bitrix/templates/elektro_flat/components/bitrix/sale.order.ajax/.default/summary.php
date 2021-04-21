<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h2><?=GetMessage("SOA_TEMPL_SUM_TITLE")?></h2>
<div class="cart-items" style="margin:0px 0px 10px 0px;">
	<div class="equipment-order">
		<div class="thead">
			<div class="cart-item-name"><?=GetMessage("SOA_TEMPL_SUM_NAME")?></div>
			<div class="cart-item-price"><?=GetMessage("SOA_TEMPL_SUM_PRICE")?></div>
			<div class="cart-item-quantity"><?=GetMessage("SOA_TEMPL_SUM_QUANTITY")?></div>
			<div class="cart-item-summa"><?=GetMessage("SOA_TEMPL_SUM_SUMMA")?></div>
		</div>
		<div class="tbody">
			<?$i = 1;
			foreach($arResult["BASKET_ITEMS"] as $arBasketItems):?>
				<div class="tr">
					<div class="tr_into">
						<div class="cart-item-number"><?=$i?></div>
						<div class="cart-item-image">
							<?if(is_array($arBasketItems["DETAIL_PICTURE"])):?>
								<img src="<?=$arBasketItems['DETAIL_PICTURE']['src']?>" width="<?=$arBasketItems['DETAIL_PICTURE']['width']?>" height="<?=$arBasketItems['DETAIL_PICTURE']['height']?>" />
							<?else:?>
								<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="30" height="30" />
							<?endif?>
						</div>
						<div class="cart-item-name">
							<span style="display:block;"><?=$arBasketItems["NAME"];?></span>
							<?if(!empty($arBasketItems["PROPS"])) {
								foreach($arBasketItems["PROPS"] as $val) {
									echo "<span style='display:block;'>".$val["NAME"].": ".$val["VALUE"]."</span>";
								}
							}?>
						</div>
						<div class="cart-item-price">
							<div class="price">
								<?=$arBasketItems["PRICE_FORMATED"]?>
							</div>
							<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
								<div class="reference-price">
									<?=CCurrencyLang::CurrencyFormat($arBasketItems["PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arBasketItems["CURRENCY"], true);?>
								</div>
							<?endif;?>
						</div>
						<div class="cart-item-quantity">
							<?=$arBasketItems["QUANTITY"];
							if(!empty($arBasketItems["MEASURE_TEXT"])):
								echo " ".$arBasketItems["MEASURE_TEXT"];
							endif;?>
						</div>
						<div class="cart-item-summa">
							<span class="sum">
								<?=$arBasketItems["SUM"]?>
							</span>							
							<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
								<span class="reference-sum">
									<?=CCurrencyLang::CurrencyFormat($arBasketItems["SUM_NUM"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arBasketItems["CURRENCY"], true);?>
								</span>
							<?endif;?>							
						</div>
					</div>
				</div>
				<?$i++;
			endforeach;
			if(doubleval($arResult["DELIVERY_PRICE"]) > 0) {?>
				<div class="tr">
					<div class="tr_into">
						<div class="cart-itogo"><?=GetMessage("SOA_TEMPL_SUM_DELIVERY")?></div>
						<div class="cart-allsum">
							<span class="allsum">
								<?=$arResult["DELIVERY_PRICE_FORMATED"]?>
							</span>
							<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
								<span class="reference-allsum">
									<?=CCurrencyLang::CurrencyFormat($arResult["DELIVERY_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["BASE_LANG_CURRENCY"], true);?>
								</span>
							<?endif;?>
						</div>
					</div>
				</div>
			<?}
			if(strlen($arResult["PAYED_FROM_ACCOUNT_FORMATED"]) > 0) {?>
				<div class="tr">
					<div class="tr_into">
						<div class="cart-itogo"><?=GetMessage("SOA_TEMPL_SUM_PAYED")?></div>
						<div class="cart-allsum">
							<span class="allsum">
								<?=$arResult["PAYED_FROM_ACCOUNT_FORMATED"]?>
							</span>							
							<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
								<span class="reference-allsum">
									<?=CCurrencyLang::CurrencyFormat(($arResult["ORDER_TOTAL_PRICE"] - $arResult["ORDER_TOTAL_LEFT_TO_PAY"]) * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["BASE_LANG_CURRENCY"], true);?>
								</span>
							<?endif;?>
						</div>
					</div>
				</div>
			<?}?>
		</div>
		<div class="myorders_itog<?=($arSetting['REFERENCE_PRICE']['VALUE'] == 'Y' && !empty($arSetting['REFERENCE_PRICE_COEF']['VALUE']) ? ' reference' : '');?>">
			<div class="cart-itogo"><?=GetMessage("SOA_TEMPL_SUM_IT")?></div>
			<div class="cart-allsum">
				<span class="allsum">
					<?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?>
				</span>				
				<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
					<span class="reference-allsum">
						<?=CCurrencyLang::CurrencyFormat($arResult["ORDER_TOTAL_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["BASE_LANG_CURRENCY"], true);?>
					</span>
				<?endif;?>
			</div>
		</div>
	</div>
</div>

<h2><?=GetMessage("SOA_TEMPL_SUM_ADIT_INFO")?></h2>
<div class="order-info">
	<div class="order-info_in">
		<label><?=GetMessage("SOA_TEMPL_SUM_COMMENTS")?></label>
		<br />
		<textarea rows="5" cols="100" name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>
		<input type="hidden" name="" value="" />
	</div>
</div>