<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

//TITLE//
$APPLICATION->SetTitle(Loc::getMessage("SPS_CHAIN_TITLE_ORDER_DETAIL", array("#ID#" => $arResult["ACCOUNT_NUMBER"])));

//BREADCRUMBS//
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_ORDERS"), $arResult["URL_TO_LIST"]);
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_TITLE_ORDER_DETAIL", array("#ID#" => $arResult["ACCOUNT_NUMBER"])));
?>