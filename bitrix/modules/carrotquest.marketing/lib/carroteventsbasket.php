<?php

namespace CarrotQuest\Marketing;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Bitrix\Sale;
use Bitrix\Sale\Compatible\BasketCompatibility;
use Bitrix\Sale\Compatible\OrderCompatibility;
use Bitrix\Catalog;
use Bitrix\Main\EventResult;
use Bitrix\Main\Event;

/*
170 строка prodItem вместо cartItem
*/
/*
NOTE: передайте пожалуйста разработчикам, возможно их это заинтересует, 
при вызове CCatalogProduct::GetByIDEx, передаю вторым параметром true 
чтобы получить все значения множественного свойства, иначе возвращает только последнего из списка
*/

class CarrotEventsBasket extends CarrotEvents
{

    /**
     * Посещение пользователем корзины
     */
    public static function VisitedBasket()
    {
        if (!\CModule::IncludeModule("sale") or !\CModule::IncludeModule("catalog")) return;
        $userId = $_COOKIE["carrotquest_uid"];
        if (defined("CARROTQUEST_API_KEY") && defined("CARROTQUEST_API_SECRET") && $userId != "") {
            $dbBasketItems = \CSaleBasket::GetList(
                array(
                    "NAME" => "ASC",
                    "ID" => "ASC"
                ),
                array(
                    "FUSER_ID" => \CSaleBasket::GetBasketUserID(),
                    "LID" => SITE_ID,
                    "ORDER_ID" => "NULL"
                ),
                false,
                false,
                array()
            );

            $opArrItem[] = array(
                "op" => "delete",
                "key" => '$cart_items',
                "value" => "0"
            );
            $i = 0;

            while ($bItem = $dbBasketItems->Fetch()) {
                $itemPic = "";
                $prodItem = \CCatalogProduct::GetByIDEx($bItem["PRODUCT_ID"]);

                if ($prodItem["DETAIL_PICTURE"] > 0) {
                    if (trim(\CFile::GetPath($prodItem["DETAIL_PICTURE"])) != "") {
                        $itemPic = self::getImageUrl(\CFile::GetPath($prodItem["DETAIL_PICTURE"]));
                    }
                } else if ($prodItem["PREVIEW_PICTURE"] > 0) {
                    if (trim(\CFile::GetPath($prodItem["PREVIEW_PICTURE"])) != "") {
                        $itemPic = self::getImageUrl(\CFile::GetPath($prodItem["PREVIEW_PICTURE"]));
                    }
                }

                if (strlen($itemPic) <= 0) {
                    if (count($prodItem["PROPERTIES"])) {
                        if (count($prodItem["PROPERTIES"]["CML2_LINK"])) {
                            $cartItem = \CCatalogProduct::GetByIDEx($prodItem["PROPERTIES"]["CML2_LINK"]["VALUE"]);
                            if ($cartItem["DETAIL_PICTURE"] > 0) {
                                if (trim(\CFile::GetPath($cartItem["DETAIL_PICTURE"])) != "") {
                                    $itemPic = self::getImageUrl(\CFile::GetPath($cartItem["DETAIL_PICTURE"]));
                                }
                            } else if ($cartItem["PREVIEW_PICTURE"] > 0) {
                                if (trim(\CFile::GetPath($cartItem["PREVIEW_PICTURE"])) != "") {
                                    $itemPic = self::getImageUrl(\CFile::GetPath($cartItem["PREVIEW_PICTURE"]));
                                }
                            }
                        }
                    }
                }

                $strItem = $bItem["NAME"];
                $arrItem['$name'][] = $bItem["NAME"];
                $arrItem['$amount'][] = round(floatval($bItem["PRICE"])) * $bItem["QUANTITY"];
                $arrItem['$url'][] = self::getImageUrl($bItem["DETAIL_PAGE_URL"]);
                $arrItem['$img'][] = strlen($itemPic) > 0 ? $itemPic : '<Нет изображения>';

                if (strlen($strItem) > 0) {
                    $opArrItem[] = array(
                        "op" => "append",
                        "key" => '$cart_items',
                        "value" => $strItem
                    );
                }
                $i++;

            }
            $cart = $arrItem;
            if (count($cart['$name']) == 0) {
                $cart = array();
            }

            CarrotEvents::SendEvent($userId, '$cart_viewed', $cart);
            CarrotEvents::SendOperations($userId, $opArrItem);
            self::BusketSumm($userId);

        }
    }

    /**
     * Добавление товара в корзину (обертка для сбора суммы в корзине и добавленного товара)
     *
     * @param $id
     * @param $item
     * @return EventResult
     */
    public static function onBasketAdd($id, $item)
    {
        try {
            $userId = $_COOKIE["carrotquest_uid"];
            if (defined("CARROTQUEST_API_KEY") && defined("CARROTQUEST_API_SECRET") && $item != null && $userId != "") {
                self::AddItem($item, $userId);
                self::BusketSumm($userId, $item);
            }
        } catch (Exception $e) {
            CarrotEvents::WriteLog("Error", $e->getMessage());
        }

        return new EventResult(EventResult::SUCCESS);
    }

    /**
     * Добавление товара в корзину (информация о товаре)
     *
     * @param $item
     * @param $userId
     */
    private static function AddItem($item, $userId)
    {
        if (!\CModule::IncludeModule("catalog")) return;
        $HTTP = $_SERVER['HTTPS'] ? "https://" : "http://";
        $arrItem = array(
            '$name' => $item["NAME"],
            '$amount' => round(floatval($item["PRICE"])),
            '$url' => $HTTP . $_SERVER['SERVER_NAME'] . $item["DETAIL_PAGE_URL"]
        );

        $prodItem = \CCatalogProduct::GetByIDEx($item["PRODUCT_ID"]);
        if ($prodItem["DETAIL_PICTURE"] > 0) {
            if (trim(\CFile::GetPath($prodItem["DETAIL_PICTURE"])) != "") {
                $itemPic = self::getImageUrl(\CFile::GetPath($prodItem["DETAIL_PICTURE"]));
            }
        } else if ($prodItem["PREVIEW_PICTURE"] > 0) {
            if (trim(\CFile::GetPath($prodItem["PREVIEW_PICTURE"])) != "") {
                $itemPic = self::getImageUrl(\CFile::GetPath($prodItem["PREVIEW_PICTURE"]));
            }
        }

        if (strlen($itemPic) <= 0) {
            if (count($prodItem["PROPERTIES"])) {
                if (count($prodItem["PROPERTIES"]["CML2_LINK"])) {
                    $cartItem = \CCatalogProduct::GetByIDEx($prodItem["PROPERTIES"]["CML2_LINK"]["VALUE"]);
                    if ($cartItem["DETAIL_PICTURE"] > 0) {
                        if (trim(\CFile::GetPath($cartItem["DETAIL_PICTURE"])) != "") {
                            $itemPic = self::getImageUrl(\CFile::GetPath($cartItem["DETAIL_PICTURE"]));
                        }
                    } else if ($cartItem["PREVIEW_PICTURE"] > 0) {
                        if (trim(\CFile::GetPath($cartItem["PREVIEW_PICTURE"])) != "") {
                            $itemPic = self::getImageUrl(\CFile::GetPath($cartItem["PREVIEW_PICTURE"]));
                        }
                    }
                }
            }
        }
        if (strlen($itemPic) > 0) {
            $arrItem['$img'] = $itemPic;
        }
        CarrotEvents::SendEvent($userId, '$cart_added', $arrItem);
    }

    /**
     * Сумма товаров в корзине
     *
     * @param $userId
     * @param null $item
     */
    private static function BusketSumm($userId, $item = null)
    {
        if (!\CModule::IncludeModule("sale")) return;
        $dbBasketItems = \CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "FUSER_ID" => \CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
            false,
            false,
            array()
        );

        $basketSumm = 0;

        while ($bItem = $dbBasketItems->Fetch()) {
            if ($item == null || $item["PRODUCT_ID"] != $bItem["PRODUCT_ID"]) {
                $basketSumm += (float)$bItem["PRICE"] * (float)$bItem["QUANTITY"];
            }
        }
        if ($item != null) {
            $basketSumm += (float)$item["PRICE"] * (float)$item["QUANTITY"];
        }
        if ($basketSumm) {
            $arrItem[] = array(
                "op" => "update_or_create",
                "key" => '$cart_amount',
                "value" => round($basketSumm)
            );
        } else {
            $arrItem[] = array(
                "op" => "delete",
                "key" => '$cart_amount',
                "value" => 0
            );
        }

        CarrotEvents::SendOperations($userId, $arrItem);

    }


    /**
     * Просмотр товара (событие ядра D7, при повторных просмотрах товара)
     *
     * @param $arFields
     */
    public static function onProductViewedUpdate($arFields)
    {
        if (!self::WatchLegacyEvents()) {
            try {
                $ID = intval($arFields['ID']);
                $viewed_prod = \Bitrix\Catalog\CatalogViewedProductTable::getRowById($ID);
            } catch (\Exception $e) {
                CarrotEvents::WriteLog("Error", $e->getMessage());
            }
            if (isset($viewed_prod) && array_key_exists('PRODUCT_ID', $viewed_prod)) {
                self::catalogOnBeforeViewed($viewed_prod);
            }
        }
    }


    /**
     * Просмотр товара (событие ядра D7, при первом просмотре товара)
     *
     * @param $arResult
     */
    public static function onProductViewedAdd($arResult)
    {
        if (!self::WatchLegacyEvents()) {
            self::catalogOnBeforeViewed($arResult);
        }

    }

    /**
     * Просмотр товара (устаревшее событие)
     *
     * @param $arResult
     */
    public static function onBeforeViewedAdd($arResult)
    {
        if (self::WatchLegacyEvents()) {
            self::catalogOnBeforeViewed($arResult);
        }
    }

    /**
     * Просмотр товара (общий обработчик для событий просмотра, срабатывает один раз на один просмотр)
     *
     * @param $arResult
     */
    private static function catalogOnBeforeViewed($arResult)
    {
        if (!\CModule::IncludeModule("catalog")) return;
        $HTTP = $_SERVER['HTTPS'] ? "https://" : "http://";
        $userId = $_COOKIE["carrotquest_uid"];

        $arProduct = \CCatalogProduct::GetByIDEx($arResult["PRODUCT_ID"]);
        if ($arProduct) {
            $arrItem = array(
                '$name' => $arProduct["NAME"],
                '$url' => $HTTP . $_SERVER['SERVER_NAME'] . $arProduct["DETAIL_PAGE_URL"],
            );


            if ($arProduct["DETAIL_PICTURE"] > 0) {
                $img = \CFile::GetPath($arProduct["DETAIL_PICTURE"]);
            } else if ($arProduct["PREVIEW_PICTURE"] > 0) {
                $img = \CFile::GetPath($arProduct["PREVIEW_PICTURE"]);
            }
            if (strlen($img) <= 0) {
                if (count($arProduct["PROPERTIES"])) {
                    if (count($arProduct["PROPERTIES"]["CML2_LINK"])) {
                        $prodItem = \CCatalogProduct::GetByIDEx($arProduct["PROPERTIES"]["CML2_LINK"]["VALUE"]);
                        if ($prodItem["DETAIL_PICTURE"] > 0) {
                            if (trim(\CFile::GetPath($prodItem["DETAIL_PICTURE"])) != "") {
                                $img = self::getImageUrl(\CFile::GetPath($prodItem["DETAIL_PICTURE"]));
                            }
                        } else if ($arProduct["PREVIEW_PICTURE"] > 0) {
                            if (trim(\CFile::GetPath($arProduct["PREVIEW_PICTURE"])) != "") {
                                $img = self::getImageUrl(\CFile::GetPath($arProduct["PREVIEW_PICTURE"]));
                            }
                        }
                    }
                }
            }
            if (trim($img) != "")
                $arrItem['$img'] = self::getImageUrl($img);
            CarrotEvents::SendEvent($userId, '$product_viewed', $arrItem);

            $opArrItems[] = array(
                "op" => "union"
            , "key" => '$viewed_products'
            , "value" => $arProduct["NAME"]
            );
            CarrotEvents::SendOperations($userId, $opArrItems);
        }
    }

    private static function getImageUrl($url)
    {
        if (self::startsWith($url, "https://") or self::startsWith($url, "http://")) {
            return $url;
        } else {
            $HTTP = $_SERVER['HTTPS'] ? "https://" : "http://";
            return $HTTP . $_SERVER['SERVER_NAME'] . $url;
        }
    }

    private static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}