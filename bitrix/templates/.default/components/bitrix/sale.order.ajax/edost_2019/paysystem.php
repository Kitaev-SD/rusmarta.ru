<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if (isset($arResult['edost']['format'])) { ?>

<script type="text/javascript">
	function changePaySystem(param) {

		if (param == 'bonus') {
			param = 'update';
			var E = BX('PAY_BONUS_ACCOUNT');
			if (!E) return;
			E.value = (E.value == 'Y' ? 'N' : 'Y');
		}
		else if (BX("account_only") && BX("PAY_CURRENT_ACCOUNT"))
			if (BX("account_only").value == 'Y') {
				if (param == 'account') {
					if (BX("PAY_CURRENT_ACCOUNT").checked) BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
					else BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");

					var el = document.getElementsByName("PAY_SYSTEM_ID");
					for(var i = 0; i < el.length; i++) el[i].checked = false;
				}
				else {
					BX("PAY_CURRENT_ACCOUNT").checked = false;
					BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
				}
			}
			else if (BX("account_only").value == 'N') {
				if (param == 'account') {
					if (BX("PAY_CURRENT_ACCOUNT").checked) BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
					else BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
				}
			}

		submitForm(param);
	}
</script>

<?
$paysystem_i = 0;
$prepay_i = 0;
$compact_i = 0;

if ((!empty($arResult['PAY_SYSTEM']) || $arResult['PAY_FROM_ACCOUNT'] == 'Y') && (!empty($arResult['edost']['format']['active']['id']) || $priority == 'C')) { ?>
<div class="edost edost_main edost_template_div<?=(isset($resize['delimiter']) ? $resize['delimiter'] : ' edost_delimiter_normal')?>"<?=(!empty($arResult['edost']['format']['active']['cod_tariff']) || $no_office && $priority != 'C' ? ' style="display: none;"' : '')?>>
<?
	$hide_radio = false; //(count($arResult['PAY_SYSTEM']) == 1 && $arResult['PAY_FROM_ACCOUNT'] != 'Y' && $compact == '' ? true : false);
	$ico_default = $templateFolder.'/images/logo-default-ps.gif';
	$head = GetMessage('SOA_TEMPL_PAY_SYSTEM');

	if ($compact != '' && !empty($arResult['PAY_SYSTEM']) && count($arResult['PAY_SYSTEM']) != 1) { $active = true; ?>
	<div class="edost_compact_hide">
		<div class="edost_button_big <?=($active ? 'edost_button_head' : 'edost_button_big_red edost_button_big_active')?>" onclick="edost.window.set('paysystem', 'head=<?=GetMessage('SOA_TEMPL_PAYSYSTEM_HEAD')?>')">
			<span><?=GetMessage($active ? 'SOA_TEMPL_CHANGE_BUTTON' : 'SOA_TEMPL_PAYSYSTEM_SET')?></span>
		</div>
	</div>
<?	} ?>

	<h4 class="edost_compact_head"><?=$head[0]?></h4>
	<h4 class="edost_compact_head2"><?=$head[1]?></h4>

	<div id="edost_paysystem_div" class="edost_div edost_compact_div edost_paysystem_div">
<?
	$pay_account = ($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT'] == 'Y' ? true : false);

	if ($arResult['PAY_FROM_ACCOUNT'] == 'Y') {
		if ($compact == '') $paysystem_i++;
		$compact_i++;
		$id = 'PAY_CURRENT_ACCOUNT';
		$accountOnly = ($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y') ? 'Y' : 'N';

		if ($compact != '') $ico_class = '';
		else $ico_class = 'edost_resize_ico'.(isset($resize['ico'][0]) ? $resize['ico'][0] : ' edost_ico_normal');
?>
		<input type="hidden" id="account_only" value="<?=$accountOnly?>">
		<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
		<div class="edost_window_hide edost_prepay_hide edost_pay_from_account edost_format_tariff_main <?=($pay_account ? ' edost_main_active edost_main_fon' : '')?>">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="edost_resize_show edost_pay_from_account_ico <?=$ico_class?>">
					<input class="edost_format_radio" type="checkbox" name="<?=$id?>" id="<?=$id?>" value="Y" <?=($pay_account ? 'checked="checked"' : '')?> onclick="changePaySystem('account');">

<?					if (!empty($ico_default)) { ?>
					<label class="edost_format_radio edost_compact_hide edost_supercompact_hide" for="<?=$id?>"><img class="edost_ico" src="<?=$ico_default?>" border="0"></label>
<?					} else { ?>
					<div class="edost_ico edost_supercompact_hide"></div>
<?					} ?>

					<label for="<?=$id?>">
					<span class="edost_format_tariff edost_window_hide edost_compact_window_hide <?=($pay_account ? 'edost_format_tariff_active' : '')?>"><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT')?></span>
					</label>
				</td>
			</tr>
			<tr name="edost_description">
				<td colspan="5" class="edost_resize_show edost_description">
					<label for="<?=$id?>">
					<div class="edost_format_description edost_description"><?=GetMessage('SOA_TEMPL_PAY_ACCOUNT1').' <b style="display: inline-block;">'.$arResult['CURRENT_BUDGET_FORMATED']?></b></div>
					<div class="edost_format_description edost_description" style="color: #080;">
						<?=($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y' ? GetMessage('SOA_TEMPL_PAY_ACCOUNT3') : GetMessage('SOA_TEMPL_PAY_ACCOUNT2'))?>
					</div>
					</label>
				</td>
			</tr>
		</table>
		</div>
<?	}


	if ($arParams['MODULE_VBCHEREPANOV_BONUS'] == 'Y' && !empty($arResult['JS_DATA']['BONUSPAY'])) {
		if ($compact == '') $paysystem_i++;
		$compact_i++;
		$id = 'PAY_CURRENT_ACCOUNT';
		$bonus = $arResult['JS_DATA']['BONUSPAY'];
		$pay_bonus = ($arResult['JS_DATA']['PAY_BONUS_ACCOUNT'] == 'Y' ? true : false);
?>
		<input type="hidden" id="PAY_BONUS_ACCOUNT" name="PAY_BONUS_ACCOUNT" value="<?=$arResult['JS_DATA']['PAY_BONUS_ACCOUNT']?>">
		<div class="edost_window_hide edost_prepay_hide edost_pay_from_bonus edost_format_tariff_main <?=($pay_bonus ? 'edost_pay_from_bonus_active' : '')?>">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="edost_resize_show edost_pay_from_account_ico">
<?					if ($pay_bonus) { ?>
					<input type="hidden" name="BONUS_CNT" value="<?=$bonus['MAXPAY']?>">
					<div class="edost_description" style="font-size: 18px; text-align: center;"><?=GetMessage('SOA_TEMPL_BONUS_ACCOUNT_PAY')?> <b style="display: inline-block;"><?=$bonus['MAXPAY_FORMATTED']?></b></div>
<?					} else { ?>
					<div class="edost_description"><?=GetMessage('SOA_TEMPL_BONUS_ACCOUNT_TOTAL')?> <b style="display: inline-block;"><?=$bonus['CURRENT_BONUS_BUDGET_FORMATED']?></b></div>
					<div class="edost_format_description edost_description"><?=str_replace('%percent%', $bonus['ORDER_PAY_PERCENT'], GetMessage('SOA_TEMPL_BONUS_ACCOUNT_PERCENT'))?></div>
<?						if (empty($bonus['USER_INPUT'])) { ?>
						<input type="hidden" name="BONUS_CNT" value="<?=$bonus['MAXPAY']?>">
<?						} else { ?>
						<div class="edost_format_description edost_description">
						<?=GetMessage('SOA_TEMPL_BONUS_ACCOUNT_MANUAL')?> <input type="text" name="BONUS_CNT" value="<?=$bonus['MAXPAY']?>" style="width: 50px;" > <?=GetMessage('SOA_TEMPL_BONUS_ACCOUNT_MANUAL2')?>
						</div>
<?						} ?>
<?					} ?>
				</td>
				<td class="edost_resize_button edost_compact_window_hide edost_supercompact_hide" align="right">
<?					if ($pay_bonus) { ?>
					<div class="edost_button_big2" onclick="changePaySystem('bonus')"><span><?=GetMessage('SOA_TEMPL_BONUS_ACCOUNT_N')?></span></div>
<?					} else { ?>
					<div class="edost_button_big2 edost_button_big_bonus" onclick="changePaySystem('bonus')"><span><?=GetMessage('SOA_TEMPL_BONUS_ACCOUNT_Y')?></span></div>
<?					} ?>
				</td>
			</tr>
			<tr class="edost_resize_button2">
				<td colspan="5" class="edost_resize_show edost_button edost_compact_window_hide edost_supercompact_hide" align="right" style="padding-top: 5px;<?=(1==2 && !$checked ? ' opacity: 0.5;' : '')?>">
<?					if ($pay_bonus) { ?>
					<div class="edost_button_big2 edost_change_button" onclick="changePaySystem('bonus')"><span><?=GetMessage('SOA_TEMPL_BONUS_ACCOUNT_N')?></span></div>
<?					} else { ?>
					<div class="edost_button_big2 edost_change_button edost_button_big_bonus" onclick="changePaySystem('bonus')"><span><?=GetMessage('SOA_TEMPL_BONUS_ACCOUNT_Y')?></span></div>
<?					} ?>
				</td>
			</tr>
		</table>
		</div>
<?	}


	if (empty($arResult['ORDER_TOTAL_LEFT_TO_PAY_FORMATED']) || $arResult['ORDER_TOTAL_LEFT_TO_PAY'] > 0) {
		if ($compact != '' && $compact_i == 1) {
			echo '<div class="edost_pay_from_account_delimiter"></div>';
			$compact_i = 0;
		}

		foreach($arResult['PAY_SYSTEM'] as $v) {
			$c = array();
			if ($compact != '') {
				if (empty($v['compact'])) $c[] = 'edost_compact_hide';
				if (empty($v['supercompact'])) $c[] = 'edost_supercompact_hide';
				if (!empty($v['edost_cod'])) $c[] = 'edost_prepay_hide';
			}
			$tariff_class = (!empty($c) ? implode(' ', $c) : '');

			$c = array('edost_delimiter edost_delimiter_format');
			if ($compact != '') {
				if (!empty($tariff_class)) $c[] = $tariff_class;
				if (!empty($v['supercompact'])) $c[] = 'edost_supercompact_hide';
				if (!empty($v['compact']) && $compact_i == 0) $c[] = 'edost_compact_first';
				if ($paysystem_i == 0) $c[] = 'edost_compact_tariff_first';
				if ($prepay_i == 0) $c[] = 'edost_prepay_first';
			}
			if ($paysystem_i != 0 || $compact != '') echo '<div class="'.implode(' ', $c).'"></div>';

			$paysystem_i++;
			if (!empty($v['compact'])) $compact_i++;
			if (empty($v['edost_cod'])) $prepay_i++;

			$id = 'ID_PAY_SYSTEM_ID_'.$v['ID'];
			$value = $v['ID'];
			$checked = ($v['CHECKED'] == 'Y' && !($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y' && $pay_account) ? true : false);
			$disable = (!empty($v['disable']) ? true : false);
			$onclick_get = "window.edost.window.submit('".$id."')";

			if (!empty($v['PSA_LOGOTIP']['SRC'])) $ico = $v['PSA_LOGOTIP']['SRC'];
			else if (!empty($ico_default)) $ico = $ico_default;
			else $ico = false;

			if ($arParams['FONT_BIG'] == 'Y') $ico_width = ($hide_radio ? '70' : '95');
			else $ico_width = ($hide_radio ? '40' : '64');

			$row = (isset($resize['ico_row']) ? $resize['ico_row'] : 3);
			if ($row == 'auto') $row = 1;

			if (!empty($v['edost_cod']) && $checked) $active_cod = true;

			if ($compact != '' && $arParams['COMPACT_PREPAY_JOIN'] == 'Y') {
				$payment_type = GetMessage(empty($v['edost_cod']) ? 'SOA_TEMPL_PREPAY' : 'SOA_TEMPL_COD');
				$payment_type_show = true;
			}
			else {
				$payment_type = $v['PSA_NAME'];
				$payment_type_show = false;
			}

			$discount = '';
			$discount_head = GetMessage('SOA_TEMPL_PAYSYSTEM_DISCOUNT'.($arParams['DISCOUNT_SAVING'] == 'Y' ? '_SAVING' : ''));
			if (!empty($v['discount'])) {
				$s = array();
				foreach($v['discount'] as $v2) $s[] = '<span class="edost_format_price '.($v2[0] == 'red' ? 'edost_color_red' : 'edost_order_total_green').'" style="display: block; '.($v2[0] != 'red' ? 'color: #FFF; padding: 3px 0;' : '').'">'.($v2[0] == 'red' ? '<b>+ ' : $discount_head.' <b>').$v2[1].'</b></span>';
				$discount = implode('', $s);
			}

			if ($compact != '') $ico_class = '';
			else $ico_class = (isset($resize['ico'][0]) ? $resize['ico'][0] : ' edost_ico_normal');
?>
			<div class="edost_format_tariff_main<?=($checked ? ' edost_main_active edost_main_fon' : '')?>">
			<table class="<?=$tariff_class?>" width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="<?=($disable ? 'opacity: 0.5' : '')?>" class="edost_resize_show edost_resize_ico<?=$ico_class?>" width="<?=($ico_width - (isset($resize['ico'][1]) ? $resize['ico'][1] : 0))?>" data-width="<?=$ico_width?>" rowspan="<?=$row?>">
						<input class="edost_format_radio edost_compact_window_hide edost_supercompact_hide" <?=($hide_radio ? 'style="display: none;"' : '')?><?=($disable ? ' disabled=""' : '')?> type="radio" id="<?=$id?>" name="PAY_SYSTEM_ID" value="<?=$value?>" <?=($checked ? 'checked="checked"' : '')?> onclick="changePaySystem('update');">

<?						if ($ico !== false) { ?>
						<label class="edost_format_radio" for="<?=$id?>"><img class="edost_ico" src="<?=$ico?>" border="0"></label>
<?						} else { ?>
						<div class="edost_ico"></div>
<?						} ?>

<?						if ($compact != '') { ?>
						<label for="<?=$id?>" style="<?=($disable ? 'opacity: 0.5' : '')?>">
							<span class="edost_compact_show edost_format_tariff" style="display: none;"><?=$payment_type?></span>
							<span class="edost_resize_supercompact_show edost_format_tariff2" style="display: none;"><?=$v['PSA_NAME']?></span>
						</label>
<?						} ?>
					</td>

					<td class="edost_resize_tariff_show edost_format_tariff">
						<label for="<?=$id?>" style="<?=($disable ? 'opacity: 0.5' : '')?>">

<?						if ($compact != '') { ?>
						<img class="edost_ico edost_ico2" src="<?=$ico?>" border="0">
						<span class="<?=($payment_type_show ? 'edost_supercompact_hide edost_window_hide' : '')?> edost_prepay_hide edost_format_tariff"><?=$payment_type?><br></span>
<?							if ($payment_type_show) { ?>
							<span class="edost_format_tariff2 <?=($payment_type == $v['PSA_NAME'] ? 'edost_compact_hide' : '')?>"><?=$v['PSA_NAME']?></span>
<?							} ?>
<?						} else { ?>
						<span class="<?=($payment_type_show ? 'edost_supercompact_hide edost_window_hide' : '')?> edost_prepay_hide edost_format_tariff"><?=$v['PSA_NAME']?><br></span>
<?						} ?>

<?						if ($disable) echo '<div class="edost_warning">'.GetMessage('SOA_TEMPL_COD_DISABLE').'</div>'; ?>
						</label>
					</td>

<?					if ($compact != '') { ?>
					<td class="edost_resize_compact_show edost_window_hide" style="display: none;">
						<label for="<?=$id?>" style="<?=($disable ? 'opacity: 0.5' : '')?>">
						<span class="edost_format_tariff2 <?=($payment_type == $v['PSA_NAME'] ? 'edost_compact_hide' : '')?>"><?=$v['PSA_NAME']?></span>
<?						if ($disable) echo '<div class="edost_warning">'.GetMessage('SOA_TEMPL_COD_DISABLE').'</div>'; ?>
						</label>
					</td>
<?					} ?>

<?					if ($disable) { ?>
					<td class="edost_resize_tariff_show edost_resize_button edost_button_cod_disable" align="right">
						<div class="edost_button_cod_disable" onclick="edost.window.set('tariff', 'head=<?=GetMessage('SOA_TEMPL_WINDOW_COD_HEAD')?>;class=edost_compact_main edost edost_compact_cod_main;cod_id=<?=$id?>')">
							<span><?=GetMessage('SOA_TEMPL_COD_DISABLE_BUTTON')?></span>
						</div>
					</td>
<?					} ?>

<?					if (!$disable && $priority != '') { ?>
					<td class="<?=(!empty($discount) ? 'edost_resize_show edost_resize_tariff_show' : '')?> edost_payment_discount" width="105"><?=$discount?></td>

<?						if ($compact != '') { ?>
<?							if (!empty($format['prepay_change'])) { ?>
					<td class="edost_resize_button edost_compact_window_hide edost_supercompact_hide" align="right">
<?								if (empty($v['edost_cod']) && $compact != '') { ?>
						<div class="edost_button_big2" onclick="edost.window.set('paysystem', 'head=<?=GetMessage('SOA_TEMPL_PAYSYSTEM_HEAD_PREPAY')?>;class=edost_window_prepay')">
							<span><?=GetMessage('SOA_TEMPL_PREPAY_CHANGE_BUTTON')?></span>
						</div>
<?								} ?>
					</td>
<?							} ?>
					<td class="edost_resize_tariff_show edost_compact_hide edost_supercompact_hide" width="130" align="center">
						<div class="edost_button_get" onclick="<?=$onclick_get?>"><span><?=GetMessage('SOA_TEMPL_GET')?></span></div>
					</td>
<?						} ?>
<?					} ?>
				</tr>

<?				if (!$disable) {
					if ($compact == '') { ?>
				<tr class="edost_payment_discount2">
					<td colspan="3" class="<?=(!empty($discount) ? 'edost_resize_show edost_resize_tariff_show' : '')?>"><?=$discount?></td>
				</tr>
<?					} ?>

				<tr name="edost_description">
					<td colspan="5" class="edost_resize_show edost_resize_tariff_show edost_description">
<?						if (!empty($v['delivery_bonus'])) { ?>
						<div class="edost_format_description edost_description edost_bonus"><span><?=GetMessage('SOA_TEMPL_BONUS'.(count($v['delivery_bonus']) > 1 ? '2' : '')).':</span> '.implode(', ', $v['delivery_bonus'])?></div>
<?						} ?>

<?						if (!empty($v['DESCRIPTION'])) { ?>
						<div class="edost_format_description edost_description"><?=nl2br($v['DESCRIPTION'])?></div>
<?						} ?>

<?						if (!empty($v['PRICE'])) { ?>
						<div class="edost_format_description edost_warning">
							<?=str_replace('#PAYSYSTEM_PRICE#', SaleFormatCurrency(roundEx($v['PRICE'], SALE_VALUE_PRECISION), $arResult['BASE_LANG_CURRENCY']), GetMessage('SOA_TEMPL_PAYSYSTEM_PRICE'))?>
						</div>
<?						} ?>

<?						if (!empty($v['note_active'])) { ?>
							<div class="edost_format_description edost_window_hide edost_note_active"><?=$v['note_active']?></div>
<?						} ?>

<?						if (!empty($v['note'])) { ?>
							<div class="edost_format_description"><?=$v['note']?></div>
<?						} ?>

<?						if (!empty($v['warning'])) { ?>
							<div class="edost_format_description edost_warning"><?=$v['warning']?></div>
<?						} ?>
					</td>
				</tr>
<?				} ?>

<?				if ($disable) { ?>
				<tr class="edost_resize_button2">
					<td colspan="5" class="edost_resize_show edost_button_cod_disable edost_button edost_compact_window_hide edost_supercompact_hide" align="right" style="padding-top: 5px;">
						<div class="edost_button_cod_disable edost_change_button" style="width: auto; max-width: 280px;" onclick="edost.window.set('tariff', 'head=<?=GetMessage('SOA_TEMPL_WINDOW_COD_HEAD')?>;class=edost_compact_main edost edost_compact_cod_main;cod_id=<?=$id?>')">
							<span><?=GetMessage('SOA_TEMPL_COD_DISABLE_BUTTON')?></span>
						</div>
					</td>
				</tr>
<?				} ?>

<?				if (empty($v['edost_cod']) && $compact != '' && !empty($format['prepay_change'])) { ?>
				<tr class="edost_resize_button2">
					<td colspan="5" class="edost_resize_show edost_button edost_compact_window_hide edost_supercompact_hide" align="right" style="padding-top: 5px;<?=(1==2 && !$checked ? ' opacity: 0.5;' : '')?>">
						<div class="edost_button_big2 edost_change_button" onclick="edost.window.set('paysystem', 'head=<?=GetMessage('SOA_TEMPL_PAYSYSTEM_HEAD_PREPAY')?>;class=edost_window_prepay')">
							<span><?=GetMessage('SOA_TEMPL_PREPAY_CHANGE_BUTTON')?></span>
						</div>
					</td>
				</tr>
<?				} ?>

			</table>
			</div>
<?		} ?>
<?	} ?>

	</div>

</div>
<? } ?>

<? } ?>
