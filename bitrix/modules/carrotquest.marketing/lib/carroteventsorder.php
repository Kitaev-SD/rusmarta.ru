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
use Bitrix\Sale\Compatible\EventCompatibility;
use Bitrix\Catalog;
use Bitrix\Main\EventResult;
use Bitrix\Main\Event;

class CarrotEventsOrder extends CarrotEvents
{

    public static function onOrderAdd($id, $order)
    {
        try {
            if (defined("CARROTQUEST_API_KEY") && defined("CARROTQUEST_API_SECRET") && $order != null) {
                $userId = $order["USER_ID"];
                $send_userId = $userId;
                $by_user_id = true;

                $status = \CSaleStatus::GetByID($order["STATUS_ID"]);
                if ($status["NAME"]) {
                    $arrItem[] = array(
                        "op" => "update_or_create",
                        "key" => '$last_order_status',
                        "value" => $status["NAME"]
                    );
                }

                $dbOrdUser = \CUser::GetByID($userId);
                $ord_user = $dbOrdUser->Fetch();
                if (Option::get(self::$MODULE_ID, "phone_prop")) {
                    $phone_codes_str = Option::get(self::$MODULE_ID, "phone_prop");
                    $phone_codes = explode(',', $phone_codes_str);
                    $count = count($phone_codes);
                    $i = -1;
                    do {
                        $i++;
                        if (isset($_POST[trim($phone_codes[$i])])) {
                            $arrItem[] = array(
                                "op" => "update_or_create",
                                "key" => '$phone',
                                "value" => $_POST[trim($phone_codes[$i])]
                            );
                        }
                    } while ($i < $count - 1 && !isset($_POST[trim($phone_codes[$i])])); 


                }
                if ($ord_user) {
                    // user name
                    $arrItem[] = array(
                        "op" => "update_or_create",
                        "key" => '$name',
                        "value" => trim($ord_user['LAST_NAME'] . ' ' . $ord_user['NAME'] . ' ' . $ord_user['SECOND_NAME'])
                    );


                    // user email
                    $arrItem[] = array(
                        "op" => "update_or_create",
                        "key" => '$email',
                        "value" => $ord_user['EMAIL']
                    );
                }

                // the cart is empty
                $arrItem[] = array(
                    "op" => "delete",
                    "key" => '$cart_amount',
                    "value" => 0
                );
                // the cart is empty
                $arrItem[] = array(
                    "op" => "delete",
                    "key" => '$cart_items',
                    "value" => ""
                );

                $arrItem[] = array(
                    "op" => "add",
                    "key" => '$revenue',
                    "value" => round(floatval($order["PRICE"]))
                );

                $arrItem[] = array(
                    "op" => "update_or_create",
                    "key" => '$last_payment',
                    "value" => round(floatval($order["PRICE"]))
                );

                CarrotEvents::SendOperations($send_userId, $arrItem, $by_user_id);
                CarrotEvents::SendEvent($send_userId, '$order_completed', array(
                        '$order_id' => $id,
                        '$order_amount' => round(floatval($order["PRICE"]))
                    ), $by_user_id);
            }
        } catch (Exception $e) {
            CarrotEvents::WriteLog("Error", $e->getMessage());
        }

        return new EventResult(EventResult::SUCCESS); // says that it is OK to continue CMS functions
    }

    /**
     * Order status changing
     *
     * @param $order
     * @param $value
     * @param $oldValue
     * @return EventResult
     */
    public static function onSaleStatusOrderChange($order, $value, $oldValue)
    {

        try {
            if (defined("CARROTQUEST_API_KEY") && defined("CARROTQUEST_API_SECRET") && $order != null && $order instanceof Sale\Order) {
                // send info to customer linked to order
                $userId = $order->getField("USER_ID");
                $arFilter = Array(
                    "USER_ID" => $userId,
                );
                // list of orders, sorted desc by creation date
                $rsSales = \CSaleOrder::GetList(array("DATE_INSERT" => "DESC"), $arFilter);
                $rsSalesFirst = $rsSales->Fetch();

                // if it is last order changing then send data to Carrot
                if ($rsSalesFirst && $rsSalesFirst["ID"] == $order->getId()) {
                    $status = \CSaleStatus::GetByID($value);
                    $arrItem[] = array(
                        "op" => "update_or_create",
                        "key" => '$last_order_status',
                        "value" => $status["NAME"] // new status name
                    );
                    CarrotEvents::SendOperations($userId, $arrItem, true);
                }

                if (strtoupper($value) == "F") {
                    CarrotEvents::SendEvent($userId, '$order_closed', array('$order_id' => $order->getId()), true);
                }
            }
        } catch (Exception $e) {
            CarrotEvents::WriteLog("Error", $e->getMessage());
        }

        return new EventResult(EventResult::SUCCESS); // says that it is OK to continue CMS functions
    }

    /**
     * Sends data when order or order cancelation is canceled
     *
     * @param $id
     * @param $cancel
     * @param $description
     * @return EventResult
     */
    public static function onSaleCancelOrder($id, $cancel, $description)
    {

        try {
            $order = \CSaleOrder::GetByID($id);

            if (defined("CARROTQUEST_API_KEY") && defined("CARROTQUEST_API_SECRET") && $order != null && count($order) > 0) {
                $userId = $order["USER_ID"];
                $arFilter = Array(
                    "USER_ID" => $userId,
                );
                // list of orders, sorted desc by creation date
                $rsSales = \CSaleOrder::GetList(array("DATE_INSERT" => "DESC"), $arFilter);
                $rsSalesFirst = $rsSales->Fetch();
                // if it is last order changing then send data to Carrot
                if ($rsSalesFirst && $rsSalesFirst["ID"] == $order["ID"]) {
                    $status = \CSaleStatus::GetByID($order["STATUS_ID"]);
                    $arrItem[] = array(
                        "op" => "update_or_create",
                        "key" => '$last_order_status',
                        "value" => $cancel == "Y" ? "Заказ отменён" : $status["NAME"] // set status to canceled or to actual (depending on action)
                    );
                    CarrotEvents::SendOperations($userId, $arrItem, true);
                }

                if ($cancel == "Y") {
                    $eArrItem = array(
                        '$order_id' => $id
                    );

                    if (strlen($order["ORDER_CANCEL_DESCRIPTION"]) > 0) {
                        $eArrItem['$comment'] = $order["ORDER_CANCEL_DESCRIPTION"];
                    }
                    CarrotEvents::SendEvent($userId, '$order_cancelled', $eArrItem, true);
                }
            }
        } catch (Exception $e) {
            CarrotEvents::WriteLog("Error", $e->getMessage());
        }

        return new EventResult(EventResult::SUCCESS);
    }

    /**
     *
     * Sends data when order is set to payd
     *
     * @param $id
     * @param $value
     * @return EventResult
     */
    public static function onSalePayOrder($id, $value)
    {
        try {
            $order = \CSaleOrder::GetByID($id);
            if (defined("CARROTQUEST_API_KEY") && defined("CARROTQUEST_API_SECRET") && $order != null && count($order) > 0) {
                $userId = $order["USER_ID"];
                $arFilter = Array(
                    "USER_ID" => $userId,
                );
                CarrotEvents::SendEvent($userId, $value == "Y" ? '$order_paid' : '$order_refunded', array('$order_id' => $id), true);

            }
        } catch (Exception $e) {
            CarrotEvents::WriteLog("Error", $e->getMessage());
        }

        return new EventResult(EventResult::SUCCESS);
    }
}