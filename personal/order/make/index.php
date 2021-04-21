<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");

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
}?><?if($flag){?> <?$APPLICATION->IncludeComponent(
	"bitrix:sale.order.ajax",
	"edost_2020_RM",
	Array(
		"ACTION_VARIABLE" => "action",
		"ACTIVE_LIGHT" => "Y",
		"ACTIVE_LIGHT2" => "Y",
		"ADDITIONAL_PICT_PROP_13" => "-",
		"ADDITIONAL_PICT_PROP_15" => "-",
		"ADDITIONAL_PICT_PROP_16" => "-",
		"ADDITIONAL_PICT_PROP_17" => "-",
		"ADDITIONAL_PICT_PROP_22" => "-",
		"ALLOW_APPEND_ORDER" => "Y",
		"ALLOW_AUTO_REGISTER" => "Y",
		"ALLOW_NEW_PROFILE" => "N",
		"ALLOW_USER_PROFILES" => "N",
		"BASKET_IMAGES_SCALING" => "standard",
		"BASKET_POSITION" => "before",
		"BORDER_COLOR" => "Y",
		"BORDER_RADIUS" => "0",
		"CART" => "compact",
		"CART_SHOW_PROPS" => "N",
		"COD_LIGHT" => "Y",
		"COD_POST_NAME" => "Y",
		"COLOR" => "blue",
		"COMPACT" => "S",
		"COMPACT_CART_SHOW_IMG" => "Y",
		"COMPACT_PREPAY_JOIN" => "N",
		"COMPATIBLE_MODE" => "Y",
		"COMPONENT_TEMPLATE" => "edost_2020_RM",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"DELIVERIES_PER_PAGE" => "160",
		"DELIVERY_FADE_EXTRA_SERVICES" => "Y",
		"DELIVERY_NO_AJAX" => "Y",
		"DELIVERY_NO_SESSION" => "Y",
		"DELIVERY_TO_PAYSYSTEM" => "d2p",
		"DISABLE_BASKET_REDIRECT" => "N",
		"DISCOUNT_SAVING" => "Y",
		"EMPTY_BASKET_HINT_PATH" => "/",
		"FAST" => "none",
		"FONT_BIG" => "Y",
		"MENU" => "N",
		"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
		"ORDER_FORMAT" => "progress_compact",
		"PATH_TO_AUTH" => "/personal/private/",
		"PATH_TO_BASKET" => "/personal/cart/",
		"PATH_TO_PAYMENT" => "/personal/order/payment/",
		"PATH_TO_PERSONAL" => "/personal/orders/",
		"PAYMENT_BONUS" => "Y",
		"PAY_FROM_ACCOUNT" => "N",
		"PAY_SYSTEMS_PER_PAGE" => "160",
		"PICKUPS_PER_PAGE" => "160",
		"PICKUP_MAP_TYPE" => "yandex",
		"POLICY" => "bitrix",
		"PRIORITY" => "B",
		"PRODUCT_COLUMNS_HIDDEN" => "",
		"PRODUCT_COLUMNS_VISIBLE" => array(0=>"PREVIEW_PICTURE",),
		"PROPS_FADE_LIST_1" => array(0=>"1",1=>"2",2=>"3",),
		"PROPS_FADE_LIST_2" => array(0=>"8",1=>"12",2=>"13",3=>"14",),
		"SEND_NEW_USER_NOTIFY" => "N",
		"SERVICES_IMAGES_SCALING" => "standard",
		"SET_TITLE" => "Y",
		"SHOW_BASKET_HEADERS" => "N",
		"SHOW_COUPONS_BASKET" => "Y",
		"SHOW_COUPONS_DELIVERY" => "Y",
		"SHOW_COUPONS_PAY_SYSTEM" => "Y",
		"SHOW_DELIVERY_INFO_NAME" => "Y",
		"SHOW_DELIVERY_LIST_NAMES" => "Y",
		"SHOW_DELIVERY_PARENT_NAMES" => "Y",
		"SHOW_MAP_IN_PROPS" => "N",
		"SHOW_NEAREST_PICKUP" => "Y",
		"SHOW_NOT_CALCULATED_DELIVERIES" => "N",
		"SHOW_ORDER_BUTTON" => "final_step",
		"SHOW_PAYMENT_SERVICES_NAMES" => "Y",
		"SHOW_PAY_SYSTEM_INFO_NAME" => "Y",
		"SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",
		"SHOW_PICKUP_MAP" => "Y",
		"SHOW_STORES_IMAGES" => "Y",
		"SHOW_TOTAL_ORDER_BUTTON" => "N",
		"SHOW_VAT_PRICE" => "Y",
		"SKIP_USELESS_BLOCK" => "Y",
		"SPOT_LOCATION_BY_GEOIP" => "Y",
		"STYLE" => "bright",
		"TEMPLATE_LOCATION" => "popup",
		"TEMPLATE_THEME" => "site",
		"USER_CONSENT" => "Y",
		"USER_CONSENT_ID" => "1",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
		"USE_CUSTOM_ERROR_MESSAGES" => "N",
		"USE_CUSTOM_MAIN_MESSAGES" => "N",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"USE_PHONE_NORMALIZATION" => "Y",
		"USE_PRELOAD" => "Y",
		"USE_PREPAYMENT" => "N",
		"USE_YM_GOALS" => "N",
		"YANDEX_API_KEY" => ""
	)
);?>
<div id="min_price_message" class="alertMsg info ">
 <i class="fa fa-info"></i> <span class="text"><?= GetMessage('ORDER_MIN_PRICE_VALUE') ?><?= CurrencyFormat($arSetting["ORDER_MIN_PRICE"]["VALUE"], Bitrix\Currency\CurrencyManager::getBaseCurrency()) ?></span>
</div>
 <?}?> <br>
<p style="text-align: center;">
 <a href="/promotions/darim-200-rubley-na-telefon-za-otzyv/" target="_blank"><img width="512" alt="Дарим-200-рублей-за-отзыв-веб.jpg" src="/upload/medialibrary/136/Darim_200_rubley_za_otzyv_veb.jpg" height="148" title="Дарим-200-рублей-за-отзыв-веб.jpg" align="middle"></a> <img width="740" alt="dostavka.jpg" src="/upload/medialibrary/9a7/dostavka.jpg" height="148" title="dostavka.jpg">
</p>
 <br><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>