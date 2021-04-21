<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;?>

<?$APPLICATION->IncludeComponent("bitrix:catalog.product.subscribe.list", "",
	array(
		"SET_TITLE" => $arParams["SET_TITLE_SUBSCRIBE"]
	),
	false
);?>

<?//BREADCRUMBS//
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_SUBSCRIBE"));?>