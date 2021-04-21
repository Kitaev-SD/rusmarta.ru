<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $arSetting;?>

<div class="cart-items" id="id-shelve-list" style="display:none;">
	<div class="sort-clear">
		<div class="sort">
			<div class="sorttext"><?=GetMessage("SALE_PRD_IN_BASKET")?></div>
			<a href="javascript:void(0)" onclick="showBasketItems(1);" class="sortbutton"><?=GetMessage("SALE_PRD_IN_BASKET_ACT")?> (<?=count($arResult["ITEMS"]["AnDelCanBuy"])?>)</a>
			<a href="javascript:void(0)" class="sortbutton current"><?=GetMessage("SALE_PRD_IN_BASKET_SHELVE")?></a>
		</div>
		<?if(count($arResult["ITEMS"]["DelDelCanBuy"]) > 0):?>
			<div class="clear clear-shelve">
				<a class="btn_buy apuo clear_cart" href="<?=$arUrlTempl['DelayClear']?>" title="<?=GetMessage('SALE_CLEAR_SHELVE')?>"><span class="clear_cont"><i class="fa fa-times"></i><span><?=GetMessage("SALE_CLEAR_SHELVE")?></span></span></a>
			</div>
		<?endif;?>
	</div>
	<?if(count($arResult["ITEMS"]["DelDelCanBuy"]) > 0):?>		
		<div class="equipment" id="shelve_equipment">
			<div class="thead">
				<div class="cart-item-image"><?=GetMessage("SALE_IMAGE")?></div>
				<?if(in_array("NAME", $arParams["COLUMNS_LIST"])):?>
					<div class="cart-item-name"><?=GetMessage("SALE_NAME")?></div>
				<?endif;?>
				<?if(in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
					<div class="cart-item-price"><?=GetMessage("SALE_PRICE")?></div>
				<?endif;?>
				<?if(in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
					<div class="cart-item-quantity"><?=GetMessage("SALE_QUANTITY")?></div>
				<?endif;?>
				<div class="cart-item-summa"><?=GetMessage("SALE_SUMMA")?></div>
				<div class="cart-item-actions"><?=GetMessage("SALE_ACTION")?></div>
			</div>
			<div class="tbody">
				<?foreach($arResult["ITEMS"]["DelDelCanBuy"] as $arBasketItems) {?>
					<div class="tr" id="<?=$arBasketItems['ID']?>">
						<div class="tr_into">
							<div class="cart-item-image">
								<?if(is_array($arBasketItems["DETAIL_PICTURE"])):?>
									<img src="<?=$arBasketItems['DETAIL_PICTURE']['src']?>" width="<?=$arBasketItems['DETAIL_PICTURE']['width']?>" height="<?=$arBasketItems['DETAIL_PICTURE']['height']?>" />
								<?else:?>
									<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="65" height="65" />
								<?endif?>
							</div>					
							<?if(in_array("NAME", $arParams["COLUMNS_LIST"])):?>
								<div class="cart-item-name">
									<?if(strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
										<a href="<?=$arBasketItems["DETAIL_PAGE_URL"] ?>">
									<?endif;?>
									<?=$arBasketItems["NAME"] ?>
									<?if(strlen($arBasketItems["DETAIL_PAGE_URL"])>0):?>
										</a>
									<?endif;?>
									<?if(in_array("PROPS", $arParams["COLUMNS_LIST"])):
										foreach($arBasketItems["PROPS"] as $val) {
											echo "<br />".$val["NAME"].": ".$val["VALUE"];
										}
									endif;?>
								</div>
							<?endif;?>
							<?if(in_array("PRICE", $arParams["COLUMNS_LIST"])):?>
								<div class="cart-item-price">
									<div class="price">
										<?=$arBasketItems["PRICE_FORMATED"]?>
									</div>
									<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
										<div class="reference-price">
											<?=CCurrencyLang::CurrencyFormat($arBasketItems["PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arBasketItems["CURRENCY"], true);?>
										</div>
									<?endif;
									if(!empty($arBasketItems["MEASURE_TEXT"])):?>
										<div class="unit">
											<?=GetMessage('UNIT')." ".$arBasketItems["MEASURE_TEXT"]?>
										</div>
									<?endif;?>
								</div>
							<?endif;?>					
							<?if(in_array("QUANTITY", $arParams["COLUMNS_LIST"])):?>
								<div class="cart-item-quantity">
									<?=$arBasketItems["QUANTITY"]?>
								</div>
							<?endif;?>
							<div class="cart-item-summa">
								<span class="sum">
									<?=CCurrencyLang::CurrencyFormat($arBasketItems["PRICE"] * $arBasketItems["QUANTITY"], $arBasketItems["CURRENCY"], true);?>
								</span>
								<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
									<span class="reference-sum">
										<?=CCurrencyLang::CurrencyFormat($arBasketItems["PRICE"] * $arBasketItems["QUANTITY"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arBasketItems["CURRENCY"], true);?>
									</span>
								<?endif;?>
							</div>										
							<?if(in_array("DELAY", $arParams["COLUMNS_LIST"])):?>
								<div class="cart-item-actions">
									<div class="in-order">
										<a class="setaside" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["add"])?>" title="<?=GetMessage("SALE_ADD_CART")?>"><i class="fa fa-shopping-cart"></i></a>
									</div>
									<?if(in_array("DELETE", $arParams["COLUMNS_LIST"])):?>
										<div class="delete">
											<a class="deleteitem" href="<?=str_replace("#ID#", $arBasketItems["ID"], $arUrlTempl["delete"])?>" onclick="return DeleteFromCart(this);" title="<?=GetMessage("SALE_DELETE_PRD")?>"><i class="fa fa-trash-o"></i></a>
										</div>
									<?endif;?>
								</div>
							<?endif;?>						
							<input type="hidden" name="DELAY_<?=$arBasketItems["ID"]?>" value="Y"/>
						</div>
					</div>
				<?}?>
			</div>
		</div>	
	<?else:
		ShowNote(GetMessage("SALE_NO_ACTIVE_PRD_SHELVE"), "infotext");
	endif;?>
</div>