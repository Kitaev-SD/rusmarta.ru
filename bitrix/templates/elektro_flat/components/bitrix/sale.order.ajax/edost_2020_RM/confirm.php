<?
//if($_SESSION['GA_ON']==true){ // ����� � ������, ��������� ������ � dataLayer ���� ���������
    ?>
    <script>
        window.dataLayer = window.dataLayer || []
        dataLayer.push({
            'transactionId': '<?=$arResult["ORDER"]['ID']?>', // ����� ������
            'transactionTotal': <?=$arResult["ORDER"]['PRICE']?>, // ����� ������
            'transactionTax': <?=$arResult["ORDER"]['TAX_VALUE']?>, // ����� ������
            'transactionShipping': <?=$arResult["ORDER"]['PRICE_DELIVERY']?>, // ��������� ��������
            'transactionProducts': [
                <?
                $arItems=array();
                $arIds=array();
                $basItems=CSaleBasket::GetList(array(),array('ORDER_ID'=>$arResult["ORDER"]['ID'])); // ������� ���������� � ������� � �������
                while($basItem=$basItems->Fetch()){?>
                {
                    'sku': '<?=$basItem['PRODUCT_ID']?>', // ������� ������
                    'name': '<?=str_replace("'",'"',$basItem['NAME'])?>', // �������� ������
                    'category': '', // ��� ���������, ���� ����
                    'price': '<?=$basItem['PRICE']?>', // ��������� ������
                    'quantity': <?=$basItem['QUANTITY']?> // ���������� ������ ������
                },
                <?}?>
            ]
        });
    </script>
    <script>
        gtag('event', 'purchase', {
            'send_to': 'AW-977520268',
            'value': <?=$arResult["ORDER"]['PRICE']?>,
            'items': [
                <?
                $basItems=CSaleBasket::GetList(array(),array('ORDER_ID'=>$arResult["ORDER"]['ID']));
                while($basItem=$basItems->Fetch()){?>
                {
                    'id': '<?=$basItem['PRODUCT_ID']?>',
                    'google_business_vertical': 'retail'
                },
                <?}?>
            ]
        });
    </script>
<?//}
unset($_SESSION['GA_ON']); // ������� ����� ���������� ������� ����������, ����� �� ���� ������
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$PRICE_ALL = 0;
$arIDS = [];
$basItems=CSaleBasket::GetList(array(),array('ORDER_ID'=>$arResult["ORDER"]['ID'])); // ������� ���������� � ������� � �������
while($basItem=$basItems->Fetch()) {
    $arIDS[] = $basItem['PRODUCT_ID'];
    $PRICE_ALL += $basItem['PRICE'];
}
?>
<script>
    gtag('event', 'purchase', {
        'ecomm_pagetype': 'purchase',
        'ecomm_prodid': '[<?=implode(',', $arIDS)?>]', // ������������� ������ ( ���� ����� 1 ������, �� ���������� �������� ['123', '231', ...] )
        'ecomm_totalvalue': '<?=$PRICE_ALL?>', // ����� ��������� ������/��
        'non_interaction': true
    });
</script>

<? if (!empty($arResult['ORDER'])) {
	echo GetMessage('SOA_TEMPL_ORDER_SUC', array('#ORDER_DATE#' => $arResult['ORDER']['DATE_INSERT'], '#ORDER_ID#' => $arResult['ORDER']['ACCOUNT_NUMBER'])).'<br><br>';
	echo GetMessage('SOA_TEMPL_ORDER_SUC1', array('#LINK#' => $arParams['PATH_TO_PERSONAL'])).'<br><br>';

	if (!empty($arResult['ORDER']['COMMENTS']) && $arResult['ORDER']['COMMENTS'] == GetMessage('SOA_TEMPL_FAST_CONFIRM')) echo GetMessage('SOA_TEMPL_FAST_CONFIRM_INFO');
	else if (!empty($arResult['PAY_SYSTEM'])) { ?>
		<div style="font-size: 22px; color: #AAA; padding-bottom: 5px;"><?=GetMessage('SOA_TEMPL_PAY')?></div>
		<table cellpadding="0" cellspacing="0" border="0"><tr>
                <? if(isset($arResult['PAY_SYSTEM']['LOGOTIP']['SRC'])){?><td width="70"><img style="width: 60px; vertical-align: middle;" src="<?=$arResult['PAY_SYSTEM']['LOGOTIP']['SRC']?>" border="0"></td><?}?>
			<td style="font-size: 20px; color: #555;"><?=$arResult['PAY_SYSTEM']['NAME']?></td>
		</tr>
            <tr>
                <td><?=$arResult['PAY_SYSTEM']["BUFFERED_OUTPUT"]?></td>
            </tr>
        </table>

<?		if (strlen($arResult['PAY_SYSTEM']['ACTION_FILE']) > 0) {
			if ($arResult['PAY_SYSTEM']['NEW_WINDOW'] == 'Y') { ?>
				<script language="JavaScript">
					window.open('<?=$arParams['PATH_TO_PAYMENT']?>?ORDER_ID=<?=urlencode(urlencode($arResult['ORDER']['ACCOUNT_NUMBER']))?>');
				</script>
<?				echo GetMessage('SOA_TEMPL_PAY_LINK', Array("#LINK#" => $arParams['PATH_TO_PAYMENT'].'?ORDER_ID='.urlencode(urlencode($arResult['ORDER']['ACCOUNT_NUMBER']))));
				if (CSalePdf::isPdfAvailable()) echo GetMessage('SOA_TEMPL_PAY_PDF', array("#LINK#" => $arParams['PATH_TO_PAYMENT'].'?ORDER_ID='.urlencode(urlencode($arResult['ORDER']['ACCOUNT_NUMBER'])).'&pdf=1&DOWNLOAD=Y'));
			}
			else if (strlen($arResult['PAY_SYSTEM']['PATH_TO_ACTION']) > 0) include($arResult['PAY_SYSTEM']['PATH_TO_ACTION']);
		}
	}
}
else { ?>
	<b><?=GetMessage('SOA_TEMPL_ERROR_ORDER')?></b><br><br>
	<?=GetMessage('SOA_TEMPL_ERROR_ORDER_LOST', array('#ORDER_ID#' => $arResult['ORDER_ID']))?>
	<?=GetMessage('SOA_TEMPL_ERROR_ORDER_LOST1')?>
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
