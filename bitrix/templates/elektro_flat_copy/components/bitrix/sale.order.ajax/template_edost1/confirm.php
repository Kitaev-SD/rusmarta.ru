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

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (!empty($arResult["ORDER"]))
{
	?>
	<b><?=GetMessage("SOA_TEMPL_ORDER_COMPLETE")?></b><br /><br />
	<table class="sale_order_full_table">
		<tr>
			<td>
				<?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]))?>
				<br /><br />
				<?= GetMessage("SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"])) ?>
			</td>
		</tr>
	</table>
	<?
	if (!empty($arResult["PAY_SYSTEM"]))
	{
		?>
		<br /><br />

		<table class="sale_order_full_table">
			<tr>
				<td class="ps_logo">
					<div class="pay_name"><?=GetMessage("SOA_TEMPL_PAY")?></div>
					<?=CFile::ShowImage($arResult["PAY_SYSTEM"]["LOGOTIP"], 100, 100, "border=0", "", false);?>
					<div class="paysystem_name"><?= $arResult["PAY_SYSTEM"]["NAME"] ?></div><br>
				</td>
			</tr>
			<?
			if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0)
			{
				?>
				<tr>
					<td>
						<?
						if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y")
						{
							?>
							<script language="JavaScript">
								window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
							</script>
							<?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))))?>
							<?
							if (CSalePdf::isPdfAvailable())
							{
								?><br />
								<?= GetMessage("SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))."&pdf=1&DOWNLOAD=Y")) ?>
								<?
							}
						}
						else
						{
							if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0)
							{
								include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
							}
						}
						?>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
	}
}
else
{
	?>
	<b><?=GetMessage("SOA_TEMPL_ERROR_ORDER")?></b><br /><br />

	<table class="sale_order_full_table">
		<tr>
			<td>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ORDER_ID"]))?>
				<?=GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1")?>
			</td>
		</tr>
	</table>
	<?
}
?>

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