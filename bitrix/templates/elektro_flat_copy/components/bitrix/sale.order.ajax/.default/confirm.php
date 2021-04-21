<?
if($_SESSION['GA_ON']==true){ // метка в сессии, добавляем данные в dataLayer если разрешено
    ?>
    <script>
        window.dataLayer = window.dataLayer || []
        dataLayer.push({
            'transactionId': '<?=$arResult["ORDER"]['ID']?>', // номер заказа
            'transactionTotal': <?=$arResult["ORDER"]['PRICE']?>, // сумма заказа
            'transactionTax': <?=$arResult["ORDER"]['TAX_VALUE']?>, // сумма налога
            'transactionShipping': <?=$arResult["ORDER"]['PRICE_DELIVERY']?>, // стоимость доставки
            'transactionProducts': [
                <?
                $arItems=array();
                $arIds=array();
                $basItems=CSaleBasket::GetList(array(),array('ORDER_ID'=>$arResult["ORDER"]['ID'])); // достаем информацию о товарах в корзине
                while($basItem=$basItems->Fetch()){?>
                {
                    'sku': '<?=$basItem['PRODUCT_ID']?>', // артикул товара
                    'name': '<?=str_replace("'",'"',$basItem['NAME'])?>', // название товара
                    'category': '', // тут категория, если есть
                    'price': '<?=$basItem['PRICE']?>', // стоимость товара
                    'quantity': <?=$basItem['QUANTITY']?> // количество единиц товара
                },
                <?}?>
            ]
        });
    </script>
<?}
unset($_SESSION['GA_ON']); // удаляем метку разрешения отсылки транзакции, чтобы не было дублей
?>
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

<?/*----------------------------------vp----------------------------------*/?>
<?
$rsUser = CUser::GetByID($arResult['ORDER']['USER_ID']);
$arUser = $rsUser->Fetch();
if(!empty($arUser['EMAIL'])):
    ?>

    <!-- BEGIN GCR Opt-in Module Code -->
    <script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>

    <script>
        window.renderOptIn = function() {
            window.gapi.load('surveyoptin', function() {

                window.gapi.surveyoptin.render(

                    {

                        "merchant_id": 132913537,

                        "order_id": <? echo '"'.$arResult["ORDER_ID"].'"';?>,

                        "email": <? echo '"'.$arUser['EMAIL'].'"'; ?>,

                        "delivery_country": "RU",

                        "estimated_delivery_date": <? echo '"'.date("Y-m-d",strtotime("+2 day")).'"';?>,

                        <?/*"products": [{"gtin":"<?php echo $gtin_1 ?>"}, {"gtin":"<?php echo $gtin_2 ?>"}],*/?>
                        "opt_in_style": "BOTTOM_LEFT_DIALOG"

                    });

            });

        }
    </script>
    <!-- END GCR Opt-in Module Code -->


    <!-- BEGIN GCR Language Code -->
    <script>
        window.___gcfg = {
            lang: 'ru'
        };
    </script>
    <!-- END GCR Language Code -->

<?endif;
/*----------------------------------end vp----------------------------------*/?>
