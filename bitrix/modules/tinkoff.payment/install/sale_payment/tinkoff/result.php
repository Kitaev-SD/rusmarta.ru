<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

CModule::IncludeModule('sale');

$APPLICATION->SetPageProperty("title", GetMessage("SALE_TINKOFF_ORDER_INFO"));
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetTitle(GetMessage("SALE_TINKOFF_ORDER_PAYMENT"));

$orderID = $_REQUEST['OrderId'];
$order = CSaleOrder::GetByID($orderID);

if (!$order) {
    $arFilter = array(
        "ACCOUNT_NUMBER" => $orderID,
    );
    $accountNumberList = CSaleOrder::GetList(array("ACCOUNT_NUMBER" => "ASC"), $arFilter);
    $order = $accountNumberList->arResult[0];
}

if ($order) {
    $status = $_REQUEST['Success'] == 'true' ? GetMessage("SALE_TINKOFF_SUCCESS") : GetMessage("SALE_TINKOFF_FAIL");
    $statusPageURL = sprintf('%s/%s',GetPagePath('personal/orders'), $orderID);
    echo sprintf(GetMessage("SALE_TINKOFF_SUCCESS_TEXT"), $orderID, $status, $statusPageURL);
} else {
    echo sprintf(GetMessage("SALE_TINKOFF_FAIL_TEXT"), $orderID);
}
