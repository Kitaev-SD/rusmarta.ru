<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if (!empty($arResult['ORDER'])) {
	echo GetMessage('SOA_TEMPL_ORDER_SUC', array('#ORDER_DATE#' => $arResult['ORDER']['DATE_INSERT']->toUserTime()->format('d.m.Y H:i'), '#ORDER_ID#' => $arResult['ORDER']['ACCOUNT_NUMBER'])).'<br><br>';
//	if (!empty($arResult['ORDER']['PAYMENT_ID'])) echo GetMessage('SOA_TEMPL_PAY_SUC', array('#PAYMENT_ID#' => $arResult['PAYMENT'][$arResult['ORDER']['PAYMENT_ID']]['ACCOUNT_NUMBER'])).'<br><br>';
	if (empty($arParams['NO_PERSONAL']) || $arParams['NO_PERSONAL'] !== 'Y') echo GetMessage('SOA_TEMPL_ORDER_SUC1', array('#LINK#' => $arParams['PATH_TO_PERSONAL'])).'<br><br>';

	if (!empty($arResult['ORDER']['COMMENTS']) && $arResult['ORDER']['COMMENTS'] == GetMessage('SOA_TEMPL_FAST_CONFIRM')) echo GetMessage('SOA_TEMPL_FAST_CONFIRM_INFO');
	else if (!empty($arResult['ORDER']['IS_ALLOW_PAY']) && $arResult['ORDER']['IS_ALLOW_PAY'] !== 'Y') echo $arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR'];
	else if (!empty($arResult['PAYMENT'])) foreach ($arResult['PAYMENT'] as $payment) if ($payment['PAID'] != 'Y') {
		$error = $break = false;
		if (!isset($arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'])) {
			if (!empty($arResult['PAY_SYSTEM'])) $arPaySystem = $arResult['PAY_SYSTEM']; else $error = true;
			$break = true;
		}
		else if (!empty($arResult['PAY_SYSTEM_LIST']) && array_key_exists($payment['PAY_SYSTEM_ID'], $arResult['PAY_SYSTEM_LIST'])) $arPaySystem = $arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][$payment['ID']];
		else $error = true;

		if ($error || !empty($arPaySystem['ERROR'])) echo '<div style="color: #A00;">'.GetMessage('SOA_TEMPL_PAY_ERROR').'</div>';
		else { ?>
			<div style="font-size: 22px; color: #AAA; padding-bottom: 5px;"><?=GetMessage('SOA_TEMPL_PAY')?></div>
			<table cellpadding="0" cellspacing="0" border="0"><tr>
				<td width="70"><img style="width: 60px; vertical-align: middle;" src="<?=$arPaySystem['LOGOTIP']['SRC']?>" border="0"></td>
				<td style="font-size: 20px; color: #555;"><?=$arPaySystem['NAME']?></td>
			</tr></table>
			<div style="height: 8px;"></div>

<?			if (strlen($arPaySystem['ACTION_FILE']) > 0 && $arPaySystem['NEW_WINDOW'] == 'Y' && $arPaySystem['IS_CASH'] != 'Y') {
				$orderAccountNumber = urlencode(urlencode($arResult['ORDER']['ACCOUNT_NUMBER']));
				$paymentAccountNumber = $payment['ACCOUNT_NUMBER']; ?>
				<script>
					window.open('<?=$arParams['PATH_TO_PAYMENT']?>?ORDER_ID=<?=$orderAccountNumber?>&PAYMENT_ID=<?=$paymentAccountNumber?>');
				</script>
<?				echo '<div>'.GetMessage('SOA_TEMPL_PAY_LINK', array('#LINK#' => $arParams['PATH_TO_PAYMENT'].'?ORDER_ID='.$orderAccountNumber.'&PAYMENT_ID='.$paymentAccountNumber)).'</div>';
				if (CSalePdf::isPdfAvailable() && $arPaySystem['IS_AFFORD_PDF']) echo '<div>'.GetMessage('SOA_TEMPL_PAY_PDF', array("#LINK#" => $arParams['PATH_TO_PAYMENT'].'?ORDER_ID='.$orderAccountNumber.'&pdf=1&DOWNLOAD=Y')).'</div>';
			}
			else if (!empty($arPaySystem['BUFFERED_OUTPUT'])) echo $arPaySystem['BUFFERED_OUTPUT'];
//			else if (!empty($arPaySystem['PATH_TO_ACTION'])) include($arPaySystem['PATH_TO_ACTION']);
		}

		if ($break) break;
	}
}
else { ?>
	<b><?=GetMessage('SOA_TEMPL_ERROR_ORDER')?></b><br><br>
	<?=GetMessage('SOA_TEMPL_ERROR_ORDER_LOST', array('#ORDER_ID#' => htmlspecialcharsbx($arResult['ACCOUNT_NUMBER'])))?>
	<?=GetMessage('SOA_TEMPL_ERROR_ORDER_LOST1')?>
	<?
}
?>
