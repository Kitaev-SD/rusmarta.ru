<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("ќформление заказа");

$flag = true;

use Bitrix\Sale,
    Bitrix\Sale\Basket;

Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");

$price = \Bitrix\Sale\BasketComponentHelper::getFUserBasketPrice(Sale\Fuser::getId(),Bitrix\Main\Context::getCurrent()->getSite());

if(!empty($arSetting["ORDER_MIN_PRICE"]["VALUE"])){
    $pageUrl = $APPLICATION->GetCurPageParam();
    $query_str = parse_url($pageUrl, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    if (!array_key_exists('ORDER_ID', $query_params))
        $flag = false;

    if ($price > $arSetting["ORDER_MIN_PRICE"]["VALUE"])
        $flag = true;
    else
        $flag = false;
}?>
<?if($flag){?>
    <? $APPLICATION->IncludeComponent("bitrix:sale.order.ajax", ".default",
        array(
            "COMPONENT_TEMPLATE" => ".default",
            "PAY_FROM_ACCOUNT" => "N",
            "ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
            "ALLOW_AUTO_REGISTER" => "N",
            "SEND_NEW_USER_NOTIFY" => "Y",
            "DELIVERY_NO_AJAX" => "N",
            "SHOW_NOT_CALCULATED_DELIVERIES" => "L",
            "DELIVERY_NO_SESSION" => "Y",
            "TEMPLATE_LOCATION" => "popup",
            "DELIVERY_TO_PAYSYSTEM" => "d2p",
            "USE_PREPAYMENT" => "N",
            "COMPATIBLE_MODE" => "Y",
            "USE_PRELOAD" => "Y",
            "ALLOW_USER_PROFILES" => "Y",
            "ALLOW_NEW_PROFILE" => "Y",
            "SHOW_ORDER_BUTTON" => "final_step",
            "SHOW_TOTAL_ORDER_BUTTON" => "N",
            "SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",
            "SHOW_PAY_SYSTEM_INFO_NAME" => "Y",
            "SHOW_DELIVERY_LIST_NAMES" => "Y",
            "SHOW_DELIVERY_INFO_NAME" => "Y",
            "SHOW_DELIVERY_PARENT_NAMES" => "Y",
            "SHOW_STORES_IMAGES" => "Y",
            "SKIP_USELESS_BLOCK" => "Y",
            "BASKET_POSITION" => "before",
            "SHOW_BASKET_HEADERS" => "N",
            "DELIVERY_FADE_EXTRA_SERVICES" => "Y",
            "SHOW_COUPONS_BASKET" => "Y",
            "SHOW_COUPONS_DELIVERY" => "Y",
            "SHOW_COUPONS_PAY_SYSTEM" => "Y",
            "SHOW_NEAREST_PICKUP" => "Y",
            "DELIVERIES_PER_PAGE" => "160",
            "PAY_SYSTEMS_PER_PAGE" => "160",
            "PICKUPS_PER_PAGE" => "160",
            "SHOW_MAP_IN_PROPS" => "N",
            "PROPS_FADE_LIST_1" => array(
                0 => "1",
                1 => "2",
                2 => "3",
            ),
            "PROPS_FADE_LIST_2" => array(
                0 => "8",
                1 => "12",
                2 => "13",
                3 => "14",
            ),
            "ACTION_VARIABLE" => "action",
            "PATH_TO_BASKET" => "/personal/cart/",
            "PATH_TO_PERSONAL" => "/personal/orders/",
            "PATH_TO_PAYMENT" => "/personal/order/payment/",
            "PATH_TO_AUTH" => "/personal/private/",
            "SET_TITLE" => "Y",
            "DISABLE_BASKET_REDIRECT" => "N",
            "PRODUCT_COLUMNS_VISIBLE" => array(
                0 => "PREVIEW_PICTURE",
                1 => "PROPS",
                2 => "DISCOUNT_PRICE_PERCENT_FORMATED",
                3 => "PRICE_FORMATED",
            ),
            "ADDITIONAL_PICT_PROP_15" => "-",
            "ADDITIONAL_PICT_PROP_15" => "-",
            "BASKET_IMAGES_SCALING" => "standard",
            "SERVICES_IMAGES_SCALING" => "standard",
            "USE_YM_GOALS" => "N",
            "USE_ENHANCED_ECOMMERCE" => "N",
            "USE_CUSTOM_MAIN_MESSAGES" => "N",
            "USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
            "USE_CUSTOM_ERROR_MESSAGES" => "N",
            "TEMPLATE_THEME" => "site",
            "PRODUCT_COLUMNS_HIDDEN" => ""
        ),
        false
    );

}else{ ?>
    <div id="min_price_message" class="alertMsg info ">
        <i class="fa fa-info"></i>
        <span class="text"><?= GetMessage('ORDER_MIN_PRICE_VALUE') ?><?= CurrencyFormat($arSetting["ORDER_MIN_PRICE"]["VALUE"], Bitrix\Currency\CurrencyManager::getBaseCurrency()) ?></span>
    </div>
<?}?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>