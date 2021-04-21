<?php
/**
 * Created by PhpStorm.
 * Developer: s.skubach, 2017
 */

use Bitrix\Main\Application,
    Bitrix\Main\Text\Encoding,
    Bitrix\Main\Localization\Loc;

$request = Application::getInstance()->getContext()->getRequest();
//$locationCity = $request->getCookie("GEOLOCATION_CITY");

$locationCity = $_COOKIE['bxmaker_geoip_2_3_8_s1_city'];
//Encoding::convertEncoding($locationCity, "utf-8", SITE_CHARSET);

if (SITE_CHARSET != "utf-8")
    $locationCity = Encoding::convertEncoding($locationCity, "utf-8", SITE_CHARSET);
?><div class="header-location" id="header_location">
    <? if (empty($locationCity)): ?>
        <div><?= Loc::getMessage("HEADER_DELIVERY_RUSSIA_POST_EMC") ?></div>
    <? else:
        global $arrHeaderLocation;
        $arrHeaderLocation = array(
            "PROPERTY_CITY" => $locationCity
        );
        ?><? $APPLICATION->IncludeComponent("bitrix:catalog.section", "header_location",
        array(
            "ACTION_VARIABLE" => "action",
            "ADD_PICT_PROP" => "MORE_PHOTO",
            "ADD_PROPERTIES_TO_BASKET" => "N",
            "ADD_SECTIONS_CHAIN" => "N",
            "ADD_TO_BASKET_ACTION" => "ADD",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "N",
            "BACKGROUND_IMAGE" => "-",
            "BASKET_URL" => "/basket/",
            "BROWSER_TITLE" => "-",
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "N",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "A",
            "COMPATIBLE_MODE" => "N",
            "COMPONENT_TEMPLATE" => ".default",
            "CONVERT_CURRENCY" => "N",
            "CUSTOM_FILTER" => "",
            "DETAIL_URL" => "/contacts/#ELEMENT_CODE#/",
            "DISABLE_INIT_JS_IN_COMPONENT" => "N",
            "DISPLAY_BOTTOM_PAGER" => "N",
            "DISPLAY_COMPARE" => "N",
            "DISPLAY_TOP_PAGER" => "N",
            "ELEMENT_SORT_FIELD" => "sort",
            "ELEMENT_SORT_FIELD2" => "name",
            "ELEMENT_SORT_ORDER" => "asc",
            "ELEMENT_SORT_ORDER2" => "asc",
            "ENLARGE_PRODUCT" => "STRICT",
            "FILTER_NAME" => "arrHeaderLocation",
            "HIDE_NOT_AVAILABLE" => "N",
            "HIDE_NOT_AVAILABLE_OFFERS" => "N",
            "IBLOCK_ID" => "27",
            "IBLOCK_TYPE" => "content",
            "IBLOCK_TYPE_ID" => "content",
            "INCLUDE_SUBSECTIONS" => "Y",
            "LABEL_PROP" => "",
            "LABEL_PROP_MOBILE" => "",
            "LABEL_PROP_POSITION" => "top-left",
            "LAZY_LOAD" => "N",
            "LINE_ELEMENT_COUNT" => "3",
            "LOAD_ON_SCROLL" => "N",
            "MESSAGE_404" => "",
            "MESS_BTN_ADD_TO_BASKET" => "В корзину",
            "MESS_BTN_BUY" => "Купить",
            "MESS_BTN_DETAIL" => "Подробнее",
            "MESS_BTN_SUBSCRIBE" => "Подписаться",
            "MESS_NOT_AVAILABLE" => "Нет в наличии",
            "META_DESCRIPTION" => "-",
            "META_KEYWORDS" => "-",
            "OFFERS_CART_PROPERTIES" => "",
            "OFFERS_FIELD_CODE" => array(
                0 => "",
                1 => "",
            ),
            "OFFERS_LIMIT" => "5",
            "OFFERS_PROPERTY_CODE" => array(
                0 => "",
                1 => "",
            ),
            "OFFERS_SORT_FIELD" => "sort",
            "OFFERS_SORT_FIELD2" => "id",
            "OFFERS_SORT_ORDER" => "desc",
            "OFFERS_SORT_ORDER2" => "desc",
            "OFFER_ADD_PICT_PROP" => "-",
            "OFFER_TREE_PROPS" => "",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => "round",
            "PAGER_TITLE" => "Товары",
            "PAGE_ELEMENT_COUNT" => "3",
            "PARTIAL_PRODUCT_PROPERTIES" => "N",
            "PRICE_CODE" => array(
                0 => "BASE",
            ),
            "PRICE_VAT_INCLUDE" => "Y",
            "PRODUCT_BLOCKS_ORDER" => "",
            "PRODUCT_DISPLAY_MODE" => "Y",
            "PRODUCT_ID_VARIABLE" => "id",
            "PRODUCT_PROPERTIES" => "",
            "PRODUCT_PROPS_VARIABLE" => "prop",
            "PRODUCT_QUANTITY_VARIABLE" => "",
            "PRODUCT_ROW_VARIANTS" => "",
            "PRODUCT_SUBSCRIPTION" => "N",
            "PROPERTY_CODE" => array(
                0 => "CITY",
                1 => "ADDRESS",
            ),
            "PROPERTY_CODE_MOBILE" => "",
            "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
            "RCM_TYPE" => "personal",
            "SECTION_CODE" => "",
            "SECTION_ID" => "0",
            "SECTION_ID_VARIABLE" => "SECTION_ID",
            "SECTION_URL" => "",
            "SECTION_USER_FIELDS" => array(
                0 => "",
                1 => "",
            ),
            "SEF_MODE" => "Y",
            "SEF_RULE" => "/contacts/",
            "SET_BROWSER_TITLE" => "N",
            "SET_LAST_MODIFIED" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_STATUS_404" => "N",
            "SET_TITLE" => "N",
            "SHOW_404" => "N",
            "SHOW_ALL_WO_SECTION" => "Y",
            "SHOW_CLOSE_POPUP" => "N",
            "SHOW_DISCOUNT_PERCENT" => "N",
            "SHOW_FROM_SECTION" => "N",
            "SHOW_MAX_QUANTITY" => "N",
            "SHOW_OLD_PRICE" => "Y",
            "SHOW_PRICE_COUNT" => "1",
            "SHOW_SLIDER" => "Y",
            "SLIDER_INTERVAL" => "3000",
            "SLIDER_PROGRESS" => "N",
            "TEMPLATE_THEME" => "site",
            "USE_ENHANCED_ECOMMERCE" => "N",
            "USE_MAIN_ELEMENT_SECTION" => "N",
            "USE_PRICE_COUNT" => "N",
            "USE_PRODUCT_QUANTITY" => "N"
        ),
        false,
        array(
            "ACTIVE_COMPONENT" => "Y"
        )
    ); ?>
    <? endif; ?>
</div>