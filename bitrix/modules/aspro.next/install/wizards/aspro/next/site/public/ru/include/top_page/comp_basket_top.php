<?if(CNext::getShowBasket()):?>
	<?
	global $arTheme, $arRegion, $arBasketPrices;
	// get actual basket counters from session
	$arCounters = CNext::getBasketCounters();
	// and show fly counters in static content
	global $arBasketPrices;?>
	<div class="basket_fly">
		<div class="wrap_cont">
			<div class="opener">
				<div title="<?=$arBasketPrices['BASKET_SUMM_TITLE']?>" data-type="AnDelCanBuy" class="basket_count small clicked empty">
					<a href="<?=(is_array($arCounters['READY']['HREF']) ? $arCounters['READY']['HREF']['VALUE'] : $arCounters['READY']['HREF']);?>"></a>
					<div class="wraps_icon_block basket">
						<div class="count <?=($arCounters['READY']['COUNT'] ? '' : 'empty_items');?>">
							<span>
								<span class="items">
									<span><?=$arCounters['READY']['COUNT'];?></span>
								</span>
							</span>
						</div>
					</div>
				</div>
				<div title="<?=$arBasketPrices['DELAY_SUMM_TITLE']?>" data-type="DelDelCanBuy" class="wish_count small clicked empty">
					<a href="<?=(is_array($arCounters['DELAY']['HREF']) ? $arCounters['DELAY']['HREF']['VALUE']."#delayed" : $arCounters['DELAY']['HREF']);?>"></a>
					<div class="wraps_icon_block delay">
						<div class="count <?=($arCounters['DELAY']['COUNT'] ? '' : 'empty_items');?>">
							<span>
								<span class="items">
									<span><?=$arCounters['DELAY']['COUNT'];?></span>
								</span>
							</span>
						</div>
					</div>
				</div>
				<?if(CNext::GetFrontParametrValue('CATALOG_COMPARE') != 'N'):?>
					<div title="<?=$arCounters['COMPARE']['TITLE']?>" class="compare_count small">
						<a href="<?=(is_array($arCounters['COMPARE']['HREF']) ? $arCounters['COMPARE']['HREF']['VALUE'] : $arCounters['COMPARE']['HREF']);?>"></a>
						<div id="compare_fly" class="wraps_icon_block compare <?=($arCounters['COMPARE']['COUNT'] ? '' : 'empty_block');?>">
							<div class="count <?=($arCounters['COMPARE']['COUNT'] ? '' : 'empty_items');?>">
								<span>
									<span class="items">
										<span><?=$arCounters['COMPARE']['COUNT'];?></span>
									</span>
								</span>
							</div>
						</div>
					</div>
				<?endif;?>
			</div>
			<div class="basket_sort">
				<span class="basket_title"><?=GetMessage('T_BASKET')?></span>
			</div>
		</div>
	</div>
	<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("header-cart");?>
		<?$basketType = (isset($arTheme['ORDER_BASKET_VIEW']['VALUE']) ? $arTheme['ORDER_BASKET_VIEW']['VALUE'] : $arTheme['ORDER_BASKET_VIEW']);?>
		<?if($basketType != "NORMAL"):?>
			<script type="text/javascript">
				arBasketAsproCounters = <?=CUtil::PhpToJSObject($arCounters, false)?>;
				SetActualBasketFlyCounters();
			</script>
		<?endif;?>
	<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("header-cart", "");?>
<?endif;?>