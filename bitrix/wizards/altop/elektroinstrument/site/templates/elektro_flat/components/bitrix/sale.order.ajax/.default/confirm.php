<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if($arParams["SET_TITLE"] == "Y") {
	$APPLICATION->SetTitle(Loc::getMessage("SOA_ORDER_COMPLETE"));
}

if(!empty($arResult["ORDER"])) {?>
	<p><?=Loc::getMessage("SOA_ORDER_SUC", array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]));?></p>
	<?if(!empty($arResult["ORDER"]["PAYMENT_ID"])) {?>
		<p><?=Loc::getMessage("SOA_PAYMENT_SUC", array("#PAYMENT_ID#" => $arResult["PAYMENT"][$arResult["ORDER"]["PAYMENT_ID"]]["ACCOUNT_NUMBER"]));?></p>
	<?}?>
	<p><?=Loc::getMessage("SOA_ORDER_SUC1", array("#LINK#" => $arParams["PATH_TO_PERSONAL"]))?></p>	
	
	<?if($arResult["ORDER"]["IS_ALLOW_PAY"] === "Y") {
		if(!empty($arResult["PAYMENT"])) {
			foreach($arResult["PAYMENT"] as $payment) {
				if($payment["PAID"] != "Y") {
					if(!empty($arResult["PAY_SYSTEM_LIST"]) && array_key_exists($payment["PAY_SYSTEM_ID"], $arResult["PAY_SYSTEM_LIST"])) {
						$arPaySystem = $arResult["PAY_SYSTEM_LIST"][$payment["PAY_SYSTEM_ID"]];
						if(empty($arPaySystem["ERROR"])) {?>
							<table class="sale_order_full_table">
								<tr>
									<td class="ps_logo">
										<div class="pay_name"><?=Loc::getMessage("SOA_PAY")?></div>
										<?=CFile::ShowImage($arPaySystem["LOGOTIP"], 100, 100, "border=0\" style=\"width:100px\"", "", false);?>
										<div class="paysystem_name"><?=$arPaySystem["NAME"]?></div>
										<br/>
									</td>
								</tr>
								<tr>
									<td>
										<?if(strlen($arPaySystem["ACTION_FILE"]) > 0 && $arPaySystem["NEW_WINDOW"] == "Y" && $arPaySystem["IS_CASH"] != "Y") {
											$orderAccountNumber = urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]));
											$paymentAccountNumber = $payment["ACCOUNT_NUMBER"];?>
											<script>
												window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=$orderAccountNumber?>&PAYMENT_ID=<?=$paymentAccountNumber?>');
											</script>
											<?=Loc::getMessage("SOA_PAY_LINK", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$orderAccountNumber."&PAYMENT_ID=".$paymentAccountNumber));
											if(CSalePdf::isPdfAvailable() && $arPaySystem["IS_AFFORD_PDF"]) {?>
												<br/>
												<?=Loc::getMessage("SOA_PAY_PDF", array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".$orderAccountNumber."&pdf=1&DOWNLOAD=Y"));
											}
										} else {
											echo $arPaySystem["BUFFERED_OUTPUT"];
										}?>
									</td>
								</tr>
							</table>
						<?} else {
							ShowError(Loc::getMessage("SOA_ORDER_PS_ERROR"));
						}
					} else {
						ShowError(Loc::getMessage("SOA_ORDER_PS_ERROR"));
					}
				}
			}
		}
	} else {
		ShowNote($arParams["MESS_PAY_SYSTEM_PAYABLE_ERROR"], "infotext");
	}
} else {
	ShowError(Loc::getMessage("SOA_ERROR_ORDER")."<br />".Loc::getMessage("SOA_ERROR_ORDER_LOST", array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"]))."<br />".Loc::getMessage("SOA_ERROR_ORDER_LOST1"));
}?>