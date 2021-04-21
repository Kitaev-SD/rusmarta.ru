<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

global $arBasketPrices;
$sDelayPrice = '';
$iDelayCount = $summ =  0;
if($arResult['CATEGORIES']['DELAY'])
{
	foreach($arResult['CATEGORIES']['DELAY'] as $arItem)
	{
		++$iDelayCount;
		$summ += $arItem['PRICE'] * $arItem['QUANTITY'];
	}
	$sDelayPrice = CCurrencyLang::CurrencyFormat($summ, CSaleLang::GetLangCurrency(SITE_ID), true);
}
$title_basket =  ($arResult['NUM_PRODUCTS'] ? Loc::getMessage("BASKET_COUNT", array("#PRICE#" => CNext::clearFormatPrice($arResult['TOTAL_PRICE']))) : Loc::getMessage("EMPTY_BLOCK_BASKET"));
$title_delay = ($sDelayPrice ? Loc::getMessage("BASKET_DELAY_COUNT", array("#PRICE#" => CNext::clearFormatPrice($sDelayPrice))) : Loc::getMessage("EMPTY_BLOCK_DELAY"));

$arBasketPrices = array(
	'BASKET_COUNT' => (int)$arResult['NUM_PRODUCTS'],
	'BASKET_SUMM' => $arResult['TOTAL_PRICE'],
	'BASKET_SUMM_TITLE' => $title_basket,
	'BASKET_SUMM_TITLE_SMALL' => Loc::getMessage('EMPTY_BASKET'),
	'DELAY_COUNT' => $iDelayCount,
	'DELAY_SUMM_TITLE' => $title_delay,
);
?>
<?if(isset($arParams['BY_AJAX']) && $arParams['BY_AJAX'] == 'Y'):?>
	<script type="text/javascript">
		var arBasketPrices = <? echo CUtil::PhpToJSObject($arBasketPrices, false, true); ?>;
	</script>
<?else:?>
	<div id="ajax_basket"></div>
<?endif;?>
