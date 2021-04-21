<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;
global $isBasketPage, $arBasketItems;

$cartStyle = 'bx-basket';
$cartId = "bx_basket".$component->getNextNumber();
$arParams['cartId'] = $cartId;

if(!function_exists('declOfNum')){
	function declOfNum($number, $titles){
		$cases = array (2, 0, 1, 1, 1, 2); 
		return sprintf($titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]], $number);
		}
	}

$count = $delayCount =  $summ = 0;
$arBasketIDs=array();

/*if($bShowActual)
{
	$sDelayPrice = '';
	if($arResult['CATEGORIES']['DELAY'])
	{
		$price = $summ =  0;
		foreach($arResult['CATEGORIES']['DELAY'] as $arItem)
		{
			$summ += $arItem['PRICE'] * $arItem['QUANTITY'];
		}
		$sDelayPrice = CCurrencyLang::CurrencyFormat($summ, CSaleLang::GetLangCurrency(SITE_ID), true);
	}
	echo \Bitrix\Main\Web\Json::encode(array(
		'BASKET_SUMM' => ($arResult['NUM_PRODUCTS'] ? GetMessage("BASKET_COUNT", array("#PRICE#" => CNext::clearFormatPrice($arResult['TOTAL_PRICE']))) : GetMessage("EMPTY_BLOCK")),
		'DELAY_SUMM' => ($sDelayPrice ? GetMessage("BASKET_DELAY_COUNT", array("#PRICE#" => CNext::clearFormatPrice($sDelayPrice))) : GetMessage("EMPTY_BLOCK")),
		)
	);
}
else
{*/
	if($arParams["SHOW_PRODUCTS"] == "Y"){
		foreach ($arResult["CATEGORIES"] as $category => $items){
			if (empty($items))
				continue;
			if($category=="READY"){
				foreach($items as $arItem){
					++$count;
					/*$price=((isset($arItem["~PRICE"]) && $arItem["~PRICE"]) ? $arItem["~PRICE"] : $arItem["PRICE"] );
					if(0>$price){ //bug fix
						$arBasketItemPrice = CSaleBasket::GetList(
							array(),
							array("ID" => $arItem["ID"]),
							false,
							false,
							array("PRICE", "ID")
						)->Fetch();
						$price=$arBasketItemPrice["PRICE"];
						$arItem["PRICE"]=$price;
					}
					$summ += $price*$arItem["QUANTITY"];*/
					if(!CSaleBasketHelper::isSetItem($arItem))
						$arBasketIDs[$arItem["ID"]]=$arItem;
				}
			}elseif($category=="DELAY"){
				foreach($items as $arItem){
					++$delayCount;
					$summ += $arItem['PRICE'] * $arItem['QUANTITY'];
				}
			}
		}
		$cur = CCurrencyLang::GetCurrencyFormat(CSaleLang::GetLangCurrency(SITE_ID));
		$summ_formated = FormatCurrency($summ, $cur["CURRENCY"]);
	}else{
		$summ_formated=$arResult["TOTAL_PRICE"];
		$count=$arResult["NUM_PRODUCTS"];
	}

	$sDelayPrice = '';
	if($arResult['CATEGORIES']['DELAY'])
	{
		$price = $summ =  0;
		foreach($arResult['CATEGORIES']['DELAY'] as $arItem)
		{
			$summ += $arItem['PRICE'] * $arItem['QUANTITY'];
		}
		$sDelayPrice = CCurrencyLang::CurrencyFormat($summ, CSaleLang::GetLangCurrency(SITE_ID), true);
	}
	$title_basket =  ($arResult['NUM_PRODUCTS'] ? GetMessage("BASKET_COUNT", array("#PRICE#" => CNext::clearFormatPrice($arResult['TOTAL_PRICE']))) : GetMessage("EMPTY_BLOCK"));
	$title_delay = ($sDelayPrice ? GetMessage("BASKET_DELAY_COUNT", array("#PRICE#" => CNext::clearFormatPrice($sDelayPrice))) : GetMessage("EMPTY_BLOCK"));

	if($arBasketIDs){
		$propsIterator = CSaleBasket::GetPropsList(
			array('BASKET_ID' => 'ASC', 'SORT' => 'ASC', 'ID' => 'ASC'),
			array('BASKET_ID' => array_keys($arBasketIDs))
		);
		while ($property = $propsIterator->GetNext()){
			$property['CODE'] = (string)$property['CODE'];
			if ($property['CODE'] == 'CATALOG.XML_ID' || $property['CODE'] == 'PRODUCT.XML_ID')
				continue;
			if (!isset($arBasketIDs[$property['BASKET_ID']]))
				continue;
			$arBasketIDs[$property['BASKET_ID']]['PROPS'][] = $property;
		}
	}
	usort($arBasketIDs, 'CNext::cmpByID');?>

	<?$frame = $this->createFrame()->begin('');?>
		<!-- noindex -->
		<a class="basket-link delay" title="<?=$title_delay;?>">
			<span class="js-basket-block"><i class="svg svg-basket " aria-hidden="true"></i><span class="title dark_link"><?=Loc::getMessage('JS_BASKET_DELAY_TITLE');?></span><span class="count"><?=$delayCount;?></span></span>
		</a>
		<a class="basket-link basket" title="<?=$title_basket;?>">
			<span class="js-basket-block" ><i class="svg svg-basket " aria-hidden="true"></i><span class="title dark_link"><?=Loc::getMessage('JS_BASKET_TITLE');?></span><span class="count"><?=$count;?></span></span>
		</a>
		<!-- /noindex -->
	<?$frame->end();?>
<?//}?>
