<?php if($_GET['change_password']) {
	header("Location: /personal/private_n/?".$_SERVER['QUERY_STRING'],TRUE,301);
	exit();
}
?>

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Diag\Debug;
//Debug::dump($_SERVER); 
//Debug::dump($USER); 

?>

<?
$APPLICATION->IncludeComponent("bitrix:main.profile", "",
	Array(
		"SET_TITLE" => $arParams["SET_TITLE"],
		"AJAX_MODE" => $arParams["AJAX_MODE_PRIVATE"],
		"SEND_INFO" => $arParams["SEND_INFO_PRIVATE"],
		"CHECK_RIGHTS" => $arParams["CHECK_RIGHTS_PRIVATE"]
	),
	false
);?>

<?
//BREADCRUMBS//
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_PRIVATE"));?>