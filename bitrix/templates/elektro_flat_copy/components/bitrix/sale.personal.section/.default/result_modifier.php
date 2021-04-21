<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader,
	Bitrix\Sale,
	Bitrix\Catalog;

if(!Loader::includeModule("sale") || !Loader::includeModule("catalog"))
	return;

global $USER;

//ORDERS//
$arResult["ORDERS"]["CURRENT"]["COUNT"] = 0;
$arResult["ORDERS"]["HISTORY"]["COUNT"] = 0;

$arFilter = array(
	"select" => array("ID", "STATUS_ID"),
	"filter" => array(
		"USER_ID" => $USER->GetID(),
		"LID" => SITE_ID,
		"CANCELED" => "N"
	)
);
$dbOrders = Sale\Order::getList($arFilter);
while($arOrder = $dbOrders->fetch()) {
	if($arOrder["STATUS_ID"] != "F") {
		$arResult["ORDERS"]["CURRENT"]["COUNT"]++;
	} else {
		$arResult["ORDERS"]["HISTORY"]["COUNT"]++;
	}
}
unset($arOrder, $dbOrders, $arFilter);

//BASKET//
$arResult["BASKET"]["AnDelCanBuy"]["COUNT"] = 0;
$arResult["BASKET"]["DelDelCanBuy"]["COUNT"] = 0;

$basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite())->getBasketItems();
foreach($basket as $basketItem) {	
	if($basketItem->canBuy() && !$basketItem->isDelay()) {
		$arResult["BASKET"]["AnDelCanBuy"]["COUNT"] += $basketItem->getQuantity();
	} elseif($basketItem->canBuy() && $basketItem->isDelay()) {
		$arResult["BASKET"]["DelDelCanBuy"]["COUNT"] += $basketItem->getQuantity();
	}
}
unset($basketItem, $basket);

//SUBSCRIBE//
$arFilter = array(
	"USER_ID" => $USER->GetID(),
	"=SITE_ID" => SITE_ID,
	array(
		"LOGIC" => "OR",
		array("=DATE_TO" => false),
		array(">DATE_TO" => date($DB->dateFormatToPHP(\CLang::getDateFormat("FULL")), time()))
	)
);
$countQuery = Catalog\SubscribeTable::getList(
	array(		
		"filter" => $arFilter,
		"select" => array(new Bitrix\Main\Entity\ExpressionField("CNT", "COUNT(1)"))
	)
);
$totalCount = $countQuery->fetch();
$totalCount = (int)$totalCount["CNT"];
$arResult["SUBSCRIBE"]["COUNT"] = $totalCount;
unset($totalCount, $countQuery, $arFilter);

//ACCOUNT//
$arResult["ACCOUNT"]["SUM"] = 0;

$baseCurrencyCode = Bitrix\Sale\Internals\SiteCurrencyTable::getSiteCurrency(SITE_ID);
$accountList = CSaleUserAccount::GetList(
	array("CURRENCY" => "ASC"),
	array(
		"USER_ID" => $USER->GetID(),
		"CURRENCY" => $baseCurrencyCode
	),
	false,
	false,
	array("ID", "CURRENT_BUDGET")
);
while($account = $accountList->Fetch()) {
	$arResult["ACCOUNT"]["SUM"] += $account["CURRENT_BUDGET"];
}
$arResult["ACCOUNT"]["SUM"] = SaleFormatCurrency($arResult["ACCOUNT"]["SUM"], $baseCurrencyCode);
unset($account, $accountList, $baseCurrencyCode);?>