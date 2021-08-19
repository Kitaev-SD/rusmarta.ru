<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div <?=(!$total_compact ? 'id="order_total_inside"' : '')?> class="edost edost_main edost_template_div" <?=(!$total_compact && !$save_button ? 'style="display: none;"' : '')?>>
	<a href="<?=$arParams['PATH_TO_BASKET']?>"><div <?=(!$total_compact ? 'id="order_cart_button_inside"' : '')?> class="edost_button_head edost_button_big"><span><?=GetMessage('SOA_TEMPL_CHANGE_BUTTON')?></span></div></a>
<?	$head = GetMessage('SOA_TEMPL_SUM_TITLE');
	if (!$total_compact) { ?>
	<div id="order_sum_title">
		<h4 class="edost_compact_head"><?=$head[0]?></h4>
		<h4 class="edost_compact_head2"><?=$head[1]?></h4>
	</div>
<?	$head = GetMessage('SOA_TEMPL_CART_TITLE'); ?>
	<div id="order_cart_title">
		<h4 class="edost_compact_head"><?=$head[0]?></h4>
		<h4 class="edost_compact_head2"><?=$head[1]?></h4>
	</div>
<?	} else { ?>
	<h4 class="edost_compact_head"><?=$head[0]?></h4>
	<h4 class="edost_compact_head2"><?=$head[1]?></h4>
<?	} ?>

	<div class="edost_div">
<?
	/* список товаров */
	$cart_count = count($arResult['GRID']['ROWS']);
	if ($arParams['CART'] != 'none' && !($arParams['CART'] == 'inside' && $total_compact)) { ?>
		<div <?=($total_compact ? 'id="order_total_cart"' : '')?>>
<?			$bPropsColumn = $bPriceType = $bWeight = $show_img = $img = false;
			$k_end = -1;
			foreach ($arResult['GRID']['ROWS'] as $k => $v) {
				$s = '';
				if (strlen($v['data']['PREVIEW_PICTURE_SRC']) > 0) $s = $v['data']['PREVIEW_PICTURE_SRC'];
				else if (strlen($v['data']['DETAIL_PICTURE_SRC']) > 0) $s = $v['data']['DETAIL_PICTURE_SRC'];

				if (!empty($s)) $img = true;
				else $s = $templateFolder.'/images/no_photo.png';

				$detail = (!empty($v['data']['DETAIL_PAGE_URL']) ? true : false);
				$arResult['GRID']['ROWS'][$k]['data']['img'] = ($detail ? '<a href="'.$v['data']['DETAIL_PAGE_URL'].'">' : '').'<img alt="" src="'.$s.'">'.($detail ? '</a>' : '');

				$k_end = $k;
			}
			foreach ($arResult['GRID']['HEADERS'] as $v) {
				if ($v['id'] == 'PROPS') $bPropsColumn = true;
				if ($v['id'] == 'NOTES') $bPriceType = true;
				if ($v['id'] == 'WEIGHT_FORMATED') $bWeight = true;
				if (($v['id'] == 'PREVIEW_PICTURE' || $v['id'] == 'DETAIL_PICTURE') && $img) $show_img = true;
			}

			foreach ($arResult['GRID']['ROWS'] as $k => $arData) {
				$name = $price = $price2 = $quantity = $sum = '';
				$props = $props2 = array();
				$detail = (!empty($arData['data']['DETAIL_PAGE_URL']) ? true : false);

				foreach ($arResult['GRID']['HEADERS'] as $id => $arColumn) {
					if (in_array($arColumn['id'], array('PROPS', 'TYPE', 'NOTES', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'DISCOUNT', 'WEIGHT_FORMATED', 'DISCOUNT_PRICE_PERCENT_FORMATED'))) continue;

					$arItem = (isset($arData['columns'][$arColumn['id']])) ? $arData['columns'] : $arData['data'];

					if ($arColumn['id'] == 'NAME') {
						$name = '<span class="edost_order_cart_name">'.($detail ? '<a href="'.$arData['data']['DETAIL_PAGE_URL'].'">'.$arData['data']['NAME'].'</a>' : $arData['data']['NAME']).'</span>';
						if ($bWeight && intval($arData['data']['WEIGHT']) != 0) $props[] = array(GetMessage('SOA_TEMPL_WEIGHT'), $arData['data']['WEIGHT_FORMATED']);
						if ($bPropsColumn) foreach ($arItem['PROPS'] as $val) $props[] = array($val['NAME'], $val['VALUE']);
					}
					else if ($arColumn['id'] == 'PRICE_FORMATED') {
						if (doubleval($arItem['DISCOUNT_PRICE']) != 0) {
							$a = (doubleval($arItem['DISCOUNT_PRICE_PERCENT']) > 0 ? true : false);
							$price .= '<span style="color: #AAA;">'.SaleFormatCurrency($arItem['PRICE'] + $arItem['DISCOUNT_PRICE'], $arItem['CURRENCY']).'</span>';
							if (!empty($arItem['DISCOUNT_PRICE_PERCENT'])) $price .= '<br><span class="edost_color_'.($a ? 'green' : 'red').'">'.(($a ? '-' : '+').'&nbsp;'.abs(round($arItem['DISCOUNT_PRICE_PERCENT']*100)/100)).'%</span>';
							$price .= '<br>';
						}
						$price2 = (!empty($arItem['~PRICE_FORMATED']) ? $arItem['~PRICE_FORMATED'] : $arItem['PRICE_FORMATED']);
						$price .= $price2;
						if ($bPriceType && !empty($arItem['NOTES'])) $price .= '<div>'.$arItem['NOTES'].'</div>';
					}
					else if ($arColumn['id'] == 'SUM') {
						$sum = '<b>'.(!empty($arItem['~'.$arColumn['id']]) ? $arItem['~'.$arColumn['id']] : $arItem[$arColumn['id']]).'</b>';
					}
					else if ($arColumn['id'] == 'QUANTITY') {
						$quantity = $arData['columns']['QUANTITY'];
					}
					else {
						if (empty($arItem[$arColumn['id']])) continue;

						if (!is_array($arItem[$arColumn['id']])) $s = $arItem[$arColumn['id']];
						else {
							$s = array();
							foreach ($arItem[$arColumn['id']] as $arValues) if (!empty($arValues['value'])) $s[] = ($arValues['type'] == 'image' ? '<div class="edost_props_ico"><img alt="" src="'.$arValues['value'].'"></div>' : $arValues['value']);
							if (empty($s)) continue;
							$s = implode(' ', $s);
						}
						$props2[] = array(getColumnName($arColumn), $s);
					}
				}

				for ($i = 1; $i <= 2; $i++) {
					$ar = ($i == 1 ? $props : $props2);
					$s = array();
					foreach ($ar as $v) $s[] = (!empty($v[0]) ? $v[0].': ' : '').'<span>'.$v[1].'</span>';
					$s = implode('<br>', $s);
					if ($i == 1) $props = $s; else $props2 = $s;
				}
?>

				<? /* компактный вариант */ ?>
				<div <?=(!$total_compact ? 'class="edost_order_cart_compact"' : '')?>>
					<div class="edost_order_cart_price">
						<?=$price2?><br>
						<?=(!empty($arData['columns']['QUANTITY']) ? $arData['columns']['QUANTITY'] : $arData['data']['QUANTITY'])?><br>
<?						if ($arData['data']['QUANTITY'] > 1) { ?>
						<div class="edost_delimiter" style="border-color: #EEE; margin: 2px 0px;"></div>
						<b><?=str_replace(array('&lt;', '&gt;', '&quot;'), array('<', '>', '"'), $arData['data']['SUM'])?></b>
<?						} ?>
					</div>
					<?=($show_img && $arParams['COMPACT_CART_SHOW_IMG'] == 'Y' ? '<span class="edost_cart_ico">'.$arData['data']['img'].'</span>' : '')?>
					<?=$name?>
					<?=(!empty($props) ? '<div class="edost_order_cart_props">'.$props.'</div>' : '')?>
					<div style="clear: both;"></div>
				</div>


				<?	/* полный вариант */ ?>
<?				if (!$total_compact) { ?>
				<table class="edost_order_cart" width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr height="60">
<?					if ($show_img) { ?>
					<td class="edost_cart_ico" width="60">
						<div class="edost_cart_ico"><?=$arData['data']['img']?></div>
					</td>
<?					} ?>

					<td class="edost_order_cart_name" rowspan="2">
						<?=$name?>
						<?=(!empty($props) ? '<div class="edost_order_cart_props">'.$props.'</div>' : '')?>
<?						if (!empty($props2)) {
							echo '<div class="edost_order_cart_props" '.($arParams['CART_SHOW_PROPS'] != 'Y' ? ' style="max-height: 0; overflow: hidden; transition: max-height 2000ms ease;"' : '').'>'.$props2.'</div>';
							if ($arParams['CART_SHOW_PROPS'] != 'Y') echo '<span class="edost_props_link" onclick="this.style.display = \'none\'; this.previousSibling.style.maxHeight = \'1000px\'">'.GetMessage('SOA_TEMPL_SHOW_ALL_PROPS').'</span>';
						} ?>
					</td>

					<td class="edost_order_cart_price" width="100">
						<?=$price?>
					</td>
					<td width="50">
						<?=$quantity?>
					</td>
					<td width="90">
						<?=$sum?>
					</td>
<? /*
					<td class="edost_order_cart_price edost_order_cart_small" width="100">
						<?=$price2?><br>
						<?=$quantity?><br>
						<?=$sum?>
					</td>
*/ ?>
				</tr>
				<tr>
<?					if ($show_img) { ?>
					<td><div style="height: 1px;"></div></td>
<?					} ?>
					<td></td><td></td><td></td><td></td>
				</tr>
				</table>
<?				} ?>

				<div class="edost_delimiter<?=($k == $k_end ? ' edost_cart_delimiter_end' : '')?>" style="margin: 4px 0 5px 0;"></div>
<?			} ?>
		</div>
<?	} ?>


		<? /* итого */ ?>
		<div <?=(!$total_compact ? 'id="order_total2_inside"' : '')?> class="edost_order_total">
<?			if (!empty($cart_count) && ($total_compact || !$total_compact && $arParams['CART'] == 'none')) { ?>
			<div <?=($total_compact && $arParams['CART'] != 'none' ? 'id="order_total_cart_count"' : '')?> class="edost_order_total_price">
				<div><?=GetMessage('SOA_TEMPL_SUM_COUNT')?></div>
				<div>
					<?=$cart_count?>
				</div>
			</div>
<?			} ?>

			<div class="edost_order_total_price">
				<div><?=GetMessage($cart_count === false ? 'SOA_TEMPL_SUM_SUMMARY' : 'SOA_TEMPL_SUM_SUMMARY2')?></div>
				<div>
					<?=$arResult['ORDER_PRICE_FORMATED']?>
<?					if (!empty($arResult['PRICE_WITHOUT_DISCOUNT']) && $arResult['PRICE_WITHOUT_DISCOUNT'] != $arResult['ORDER_PRICE_FORMATED']) { ?>
					<br><span class="edost_price_original"><?=$arResult['PRICE_WITHOUT_DISCOUNT']?></span>
<?					} ?>
				</div>
			</div>

<?			/* if (!empty($arResult['arTaxList'])) foreach($arResult['arTaxList'] as $v) { */ ?>
<?			if (!empty($arResult['TAX_LIST'])) foreach($arResult['TAX_LIST'] as $v) { ?>
			<div class="edost_order_total_price">
				<div><?=$v['NAME']?> <?=$v['VALUE_FORMATED']?></div>
				<div><?=$v['VALUE_MONEY_FORMATED']?></div>
			</div>
<?			} ?>


<?			if (!empty(CDeliveryEDOST::$result['order']['weight'])) { ?>
			<div class="edost_order_total_price">
				<div><?=GetMessage('SOA_TEMPL_SUM_WEIGHT_SUM')?></div>
				<div><?=CDeliveryEDOST::$result['order']['weight'].' '.GetMessage('SOA_TEMPL_KG')?></div>
			</div>
<?			} else if (!empty($arResult['ORDER_WEIGHT'])) { ?>
			<div class="edost_order_total_price">
				<div><?=GetMessage('SOA_TEMPL_SUM_WEIGHT_SUM')?></div>
				<div><?=$arResult['ORDER_WEIGHT_FORMATED']?></div>
			</div>
<?			} ?>

<?		if ($delivery_location !== false && $save_button) { ?>
<?			if (!empty($active_tariff) && empty($active_tariff['error']) && !($active_tariff['automatic'] == 'edost' && $active_tariff['profile'] == 0) && (!isset($active_tariff['free']) || $active_tariff['free'] != '') && (!isset($active_tariff['priceinfo']) || $active_tariff['price'] > 0)) { ?>
			<div class="edost_order_total_price">
				<div><?=GetMessage(!empty($active_tariff['priceinfo_formatted']) ? 'SOA_TEMPL_SUM_DELIVERY2' : 'SOA_TEMPL_SUM_DELIVERY')?></div>
				<div>
<?					if (!empty($active_tariff[$active_cod ? 'cod_free' : 'free'])) { ?>
						<span class="edost_price_free"><?=$active_tariff['free']?></span>
<?					} else if (!empty($active_tariff['price_formatted']) && !empty($active_cod) && $priority == 'C') { ?>
						<?=$active_tariff['price_formatted']?>
<?					} else { ?>
<?						if ($active_cod && !empty($active_tariff['pricecashplus'])) { ?>
							<?=(!empty($active_tariff['price_formatted']) ? $active_tariff['price_formatted'].'<br>' : '')?>
							<b style="color: #F00;"><?=(!empty($active_tariff['price_formatted']) ? '+ ' : '').$active_tariff['pricecashplus_formatted']?></b>
<?						} else { ?>
							<?=$arResult['DELIVERY_PRICE_FORMATED']?>
<?						} ?>
<?					} ?>

<?					if (isset($active_tariff['price_original'])) { ?>
						<br><span class="edost_price_original"><?=(!empty($active_cod) ? $active_tariff['pricecash_original_formatted'] : $active_tariff['price_original_formatted'])?></span>
<?					} ?>
				</div>
			</div>
<?			} ?>

			<div style="height: 8px;"></div>

			<? /* основное "итого заказа" или поясняющая "сумма заказа" (при оплате с внутреннего счета или бонусами) */ ?>
<?			$payed_from_account = (!empty($arResult['ORDER_TOTAL_LEFT_TO_PAY_FORMATED']) ? true : false);
			$order_base_price = ($payed_from_account || $bonus_pay ? true : false);
//			if (!$payed_from_account || $arResult['ORDER_TOTAL_PRICE_FORMATED'] != $arResult['ORDER_PRICE_FORMATED'] || $bonus_pay) {
				if ($bonus_pay) {
					$p = $logictim_bonus['ORDER_SUM_FORMATED'];
					if (!empty($active_tariff['price_formatted']) && !empty($active_cod)) $p = edost_class::GetPrice('formatted', $logictim_bonus['ORDER_SUM'] + $active_tariff['pricecashplus'] + ($priority == 'C' ? $active_tariff['transfer'] : 0), '', $arResult['BASE_LANG_CURRENCY']);
				}
				else {
					$p = $arResult['ORDER_TOTAL_PRICE_FORMATED'];
					if (!empty($active_tariff['price_formatted']) && !empty($active_cod) && $priority == 'C') $p = edost_class::GetPrice('formatted', $arResult['ORDER_TOTAL_PRICE'] + $active_tariff['transfer'], '', $arResult['BASE_LANG_CURRENCY']);
				} ?>
			<div class="edost_order_total_info <?=($order_base_price? 'edost_order_total_info2 edost_order_total_white' : 'edost_order_total_main')?>" style="margin-top: 0;">
				<span class="edost_order_total_main_head"><?=GetMessage($order_base_price ? 'SOA_TEMPL_SUM_IT_DISCOUNT' : 'SOA_TEMPL_SUM_IT')?></span>
				<span class="edost_order_total_main_price"><?=$p?></span>
			</div>
<?
//			}
?>

<?			if ($bonus_pay || $bonus_pay_total_original) { ?>
			<div class="edost_order_total_info edost_order_total_info2_white edost_order_total_green">
				<span><?=trim(str_replace(':', '', $logictim_bonus['TEXT_BONUS_PAY']))?></span> <span><?=$logictim_bonus['PAY_BONUS_FORMATED']?></span>
			</div>
<?			} ?>

<?			if (!empty($arResult['PAYED_FROM_ACCOUNT_FORMATED'])) { ?>
			<div class="edost_order_total_info edost_order_total_info2_white edost_order_total_orange">
				<span><?=GetMessage('SOA_TEMPL_SUM_PAYED')?></span>
				<span><?=$arResult['PAYED_FROM_ACCOUNT_FORMATED']?></span>
			</div>
<?			} ?>

			<? /* итого заказа при оплате с внутреннего счета (осталось доплатить) */ ?>
<?			if ($order_base_price) {
				if ($bonus_pay) {
					$p = $arResult['ORDER_TOTAL_PRICE_FORMATED'];
					if (!empty($active_tariff['price_formatted']) && !empty($active_cod)) $p = edost_class::GetPrice('formatted', $arResult['ORDER_TOTAL_PRICE'] - $logictim_bonus['PAY_BONUS'] + ($priority == 'C' ? $active_tariff['transfer'] : 0), '', $arResult['BASE_LANG_CURRENCY']);
				}
				else {
					$p = $arResult['ORDER_TOTAL_LEFT_TO_PAY_FORMATED'];
					if (!empty($active_tariff['price_formatted']) && !empty($active_cod) && $priority == 'C') $p = edost_class::GetPrice('formatted', $arResult['ORDER_TOTAL_LEFT_TO_PAY'] + $active_tariff['transfer'], '', $arResult['BASE_LANG_CURRENCY']);
				} ?>
			<div class="edost_order_total_info edost_order_total_main">
				<span class="edost_order_total_main_head"><?=GetMessage('SOA_TEMPL_SUM_IT')?></span>
				<span class="edost_order_total_main_price"><?=$p?></span>
			</div>
<?			} ?>

<?			if (!empty($arResult['PRICE_WITHOUT_DISCOUNT_VALUE']) && isset($arResult['ORDER_TOTAL_PRICE']) && class_exists('edost_class')) {
				$p = $arResult['DELIVERY_PRICE'];
				if (isset($active_tariff['price_original'])) $p = (!empty($active_cod) ? $active_tariff['pricecash_original'] : $active_tariff['price_original']);
				$p = $arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] + $p - $arResult['ORDER_TOTAL_PRICE'];
				if ($p > 0) { ?>
			<div class="edost_order_total_info edost_order_total_info2_white edost_order_total_green">
				<span><?=GetMessage('SOA_TEMPL_SUM_DISCOUNT'.($arParams['DISCOUNT_SAVING'] == 'Y' ? '_SAVING' : ''))?></span> <span><?=edost_class::GetPrice('formatted', $p, '', $arResult['BASE_LANG_CURRENCY'])?></span>
			</div>
<?				}
			} ?>

<?			if (!empty($logictim_bonus) && $logictim_bonus['LOGICTIM_BONUS_USER_DOSTUP'] == 'Y' && $logictim_bonus['ADD_BONUS'] > 0) { ?>
			<div class="edost_order_total_info edost_order_total_info2 edost_order_total_bonus">
				<span><?=trim(str_replace(':', '', $logictim_bonus['TEXT_BONUS_BALLS']))?></span> <span><?=$logictim_bonus['ADD_BONUS']?></span>
			</div>
<?			} ?>

<?			if ($arParams['MODULE_VBCHEREPANOV_BONUS'] == 'Y' && !empty($arResult['JS_DATA']['TOTAL']['BONUS_ORDER'])) { ?>
			<div class="edost_order_total_info edost_order_total_info2 edost_order_total_bonus">
				<span><?=GetMessage('SOA_TEMPL_BONUS_ORDER')?></span> <span><?=$arResult['JS_DATA']['TOTAL']['BONUS_ORDER']?></span>
			</div>
<?			} ?>

<?			if (!empty($active_tariff['transfer_formatted']) && !empty($active_cod) && $priority != 'C') { ?>
			<div class="edost_order_total_info edost_order_total_info2_white edost_order_total_red">
				<span><?=GetMessage('SOA_TEMPL_TRANSFER')?></span> <span><?=$active_tariff['transfer_formatted']?></span>
			</div>
<?			} ?>

<?			if (!empty($active_tariff['priceinfo_formatted'])) { ?>
			<div class="edost_order_total_info edost_order_total_info2_white edost_order_total_light">
				<span><?=GetMessage('SOA_TEMPL_INFO')?></span> <span><?=$active_tariff['priceinfo_formatted']?></span>
			</div>
<?			} ?>
<?		} ?>
		</div>
		<div style="clear:both;"></div>

<?		if ($delivery_location !== false && $save_button && $total_compact) { ?>
		<div id="order_save_button2" class="edost_button_big" onclick="if (window.edost.window.props()) submitForm('Y')"><span><?=GetMessage('SOA_TEMPL_BUTTON')?></span></div>
<?		} ?>
	</div>


<?	if ($total_compact && ($arParams['FAST'] == 'full' || $arParams['FAST'] == 'compact')) { ?>
	<div class="edost_delimiter" style="border-color: #EEE; margin: 0 10px;"></div>
	<div class="edost_main2 edost_template_div2 edost_fast2" style="padding: 20px;">
		<div class="edost_button_big2" style="margin: 0 auto; width: 94%; padding: 8px 0;" onclick="edost.window.agreement('fast')"><span style="display: inline-block; padding: 0 5px; font-size: 17px;"><?=GetMessage('SOA_TEMPL_FAST_BUTTON')?></span></div>
<?		if ($arParams['FAST_INFO'] == 'Y') { ?>
		<div class="edost_fast_total"><?=GetMessage('SOA_TEMPL_FAST_INFO')?></div>
<?		} ?>
	</div>
<?	} ?>

</div>
