<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Payment;
use Bitrix\Sale;
use Bitrix\Sale\Order;
use CSalePaySystemAction;
use Tinkoff\TinkoffMerchantAPI;

include_once(dirname(__FILE__) . "/sdk/TinkoffMerchantAPI.php");

Loc::loadMessages(__FILE__);

/**
 * Class tinkoffHandler
 * @package Sale\Handlers\PaySystem
 */
class tinkoffHandler extends PaySystem\ServiceHandler implements PaySystem\IRefund
{
    protected $params;

    /**
     * current payment status
     * @var string
     */
    protected $paymentStatus;

    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return PaySystem\ServiceResult
     */
    public function initiatePay(Payment $payment, Request $request = null)
    {
        $order = $this->getOrderTinkoff($payment);
        $params = $this->getParams($payment);
        $initParams = $this->initPayment($params, $order);
        $paramsExtra = array_merge($params, $initParams);
        $this->setExtraParams($paramsExtra);
        return $this->showTemplate($payment, "template");
    }

    public function getParams(Payment $payment)
    {
        if (!is_array($this->params)) {
            $params = $this->getParamsBusValue($payment);
            foreach ($params as $key => $value)
                $this->params[$key] = trim($value);
        }

        return $this->params;
    }

    public function initPayment($params, $order)
    {
        $paymentData = $this->getReceipt($params, $order);

        return $paymentData;
    }

    public function refund(Payment $payment, $refundableSum)
    {
        global $DB;
        $order = $this->getOrderTinkoff($payment);
        $params = $this->getParams($payment);
        $orderValues = $order->getFieldValues();
        $accountNumber = $orderValues['ACCOUNT_NUMBER'];
        $result = new PaySystem\ServiceResult();
        if (!empty($orderID)) {
            $result->addError(new Error('Error on try to cancel payment.'));
            return $result;
        }

        $strSql = $DB->Query("SELECT PaymentId FROM tinkoffRefund WHERE OrderId = '" . $accountNumber . "'");
        $resultSql = [];
        while ($row = $strSql->Fetch()) {
            array_push($resultSql, $row);
        }

        $paramsRefund = [
            'TerminalKey' => $params['TERMINAL_ID'],
            'PaymentId'   => $resultSql[0]['PaymentId'],
        ];
        $TinkoffGetState = new TinkoffMerchantAPI($params['TERMINAL_ID'], $params['SHOP_SECRET_WORD']);
        $requestGetState = $TinkoffGetState->buildQuery('GetState', $paramsRefund);
        $this->logsTinkoff($paramsRefund, $requestGetState);
        $requestGetState = json_decode($requestGetState, true);
        if (in_array($requestGetState['Status'], ['CONFIRMED', 'AUTHORIZED'], true)) {
            $TinkoffCancel = new TinkoffMerchantAPI($params['TERMINAL_ID'], $params['SHOP_SECRET_WORD']);
            $requestCancel = $TinkoffCancel->buildQuery('Cancel', $paramsRefund);
            $this->logsTinkoff($paramsRefund, $requestCancel);
            $requestCancel = json_decode($requestCancel, true);
            if (in_array($requestCancel['Status'], ['REFUNDED', 'REVERSED'], true)) {
                $result->setOperationType(PaySystem\ServiceResult::MONEY_LEAVING);
            } else {
                $result->addError(new Error($this->convertError($requestCancel['Message']) . ' ' . $this->convertError($requestCancel['Details'])));
            }
        } else {
            $result->addError(new Error('Error on try to confirm payment.'));
        }

        return $result;
    }

    public function convertError($error)
    {
        return mb_convert_encoding($error, LANG_CHARSET);
    }

    public function getReceipt($params, $order)
    {
        $isShipping = false;
        $items = [];
        $errorMessage = '';
        $orderValues = $order->getFieldValues();
        $accountNumber = $orderValues['ACCOUNT_NUMBER'];
        $propertyCollection = $order->getPropertyCollection();
        $dateOrder = $this->getPhoneEmail($propertyCollection);

        if ($propPayerName = $propertyCollection->getPayerName())
            $payerName = $this->convertEncodingTinkoff($propPayerName->getValue());

        if ($propPayerPhone = $propertyCollection->getPhone())
            $payerPhone = $this->convertEncodingTinkoff($propPayerPhone->getValue());

        $amount = round($order->getPrice() * 100);
        $paymentData = [
            'OrderId' => $accountNumber,
            'Amount'  => $amount,
            'DATA'    => ['Email' => $dateOrder['EMAIL'], 'Name' => $payerName, 'Phone' => $payerPhone, 'Connection_type' => 'Bitrix_2.0.2_atol'],
        ];

        $basketItems = $order->getBasket()->getBasketItems();
        $isTaxationEnabled = $params['ENABLE_TAXATION'];

        if ($isTaxationEnabled == 1) {

            foreach ($basketItems as $basketItem) {
                $productData = $basketItem->getFieldValues();
                $vatData = $productData['VAT_RATE'];
                \Bitrix\Main\Loader::includeModule('catalog');
                $itemVatID = \CCatalogProduct::getByID($productData['PRODUCT_ID']);

                if (!$itemVatID['VAT_ID']) {
                    $vat = 'none';
                } else {
                    $vat = $this->getTinkoffTax((int)round($vatData * 100));
                }

                $nameProduct = $this->convertEncodingTinkoff($productData['NAME']);
                $items[] = [
                    "Name"          => mb_substr($nameProduct, 0, 64),
                    "Price"         => round($productData['PRICE'] * 100),
                    "Quantity"      => round($productData['QUANTITY'], 3, PHP_ROUND_HALF_UP),
                    "Amount"        => round($productData['PRICE'] * $productData['QUANTITY'] * 100),
                    "PaymentMethod" => trim($params['PAYMENT_METHOD']),
                    "PaymentObject" => trim($params['PAYMENT_OBJECT']),
                    "Tax"           => $vat,
                ];
            }

            $deliveryPrice = $order->getDeliveryPrice();
            if ($deliveryPrice > 0) {
                $deliveryVat = $params['DELIVERY_TAXATION'];
                if (!$deliveryVat) {
                    $errorMessage .= GetMessage("SALE_TINKOFF_TAX_DELIVERY_ERROR");
                }
                $deliverySystemId = reset($order->getDeliverySystemId());
                $dataDelivery = \Bitrix\Sale\Delivery\Services\Table::getRowById($deliverySystemId);
                $items[] = [
                    "Name"          => mb_substr($this->convertEncodingTinkoff($dataDelivery['NAME']), 0, 64),
                    "Price"         => round($deliveryPrice * 100),
                    "Quantity"      => 1,
                    "Amount"        => round($deliveryPrice * 100),
                    "PaymentMethod" => trim($params['PAYMENT_METHOD']),
                    "PaymentObject" => 'service',
                    "Tax"           => trim($params['DELIVERY_TAXATION']),
                ];
                $isShipping = true;
            }

            $taxation = $params['TAXATION'];

            if (!$taxation) {
                $errorMessage .= GetMessage('SALE_TINKOFF_TAXATION_ERROR');
            }

            $items = $this->balanceAmount($isShipping, $items, $amount);
            $emailCompany = mb_substr($params['EMAIL_COMPANY'], 0, 64) or 'none';
            $paymentData['Receipt'] = [
                'EmailCompany' => $emailCompany,
                'Email'        => $dateOrder['EMAIL'],
                'Phone'        => $payerPhone,
                'Taxation'     => $taxation,
                'Items'        => $items,
            ];
        }

        if ($params['LANGUAGE_PAYMENT'] == 'en') {
            $paymentData['Language'] = 'en';
        }

        $bankHandler = new TinkoffMerchantAPI($params['TERMINAL_ID'], $params['SHOP_SECRET_WORD']);
        $request = $bankHandler->buildQuery('Init', $paymentData);
        $this->logsTinkoff($paymentData, $request);
        $request = json_decode($request);

        $dataRequest = [
            'error'   => $errorMessage,
            'request' => $request
        ];

        return $dataRequest;
    }

    private function getTinkoffTax($tax = null)
    {
        $arrayTax = [
            0  => 'vat0',
            10 => 'vat10',
            20 => 'vat20',
        ];

        if (isset($arrayTax[$tax])) {
            return $arrayTax[$tax];
        } else {
            return 'none';
        }
    }

    public static function getPhoneEmail($propertyCollection)
    {
        $result = [];
        $propsResult = [
            'PHONE' => [],
            'EMAIL' => []
        ];

        foreach ($propertyCollection as $orderProperty) {
            $props = $orderProperty->getProperty();

            if ($props['IS_PHONE'] == 'Y')
                $propsResult['PHONE'][] = $orderProperty->getValue();

            if ($props['IS_EMAIL'] == 'Y')
                $propsResult['EMAIL'][] = $orderProperty->getValue();
        }

        $result['PHONE'] = implode(', ', $propsResult['PHONE']);
        $result['EMAIL'] = implode(', ', $propsResult['EMAIL']);

        return $result;
    }

    private function logsTinkoff($paymentData, $request)
    {
        $log = '[' . date('D M d H:i:s Y', time()) . '] ';
        $log .= json_encode($paymentData, JSON_UNESCAPED_UNICODE);
        $log .= "\n";
        file_put_contents(dirname(__FILE__) . "/tinkoff.log", $log, FILE_APPEND);

        $log = '[' . date('D M d H:i:s Y', time()) . '] ';
        $log .= $request;
        $log .= "\n";
        file_put_contents(dirname(__FILE__) . "/tinkoff.log", $log, FILE_APPEND);
    }

    private function balanceAmount($isShipping, $items, $amount)
    {
        $itemsWithoutShipping = $items;

        if ($isShipping) {
            $shipping = array_pop($itemsWithoutShipping);
        }

        $sum = 0;

        foreach ($itemsWithoutShipping as $item) {
            $sum += $item['Amount'];
        }

        if (isset($shipping)) {
            $sum += $shipping['Amount'];
        }

        if ($sum != $amount) {
            $sumAmountNew = 0;
            $difference = $amount - $sum;
            $amountNews = [];

            foreach ($itemsWithoutShipping as $key => $item) {
                $itemsAmountNew = $item['Amount'] + floor($difference * $item['Amount'] / $sum);
                $amountNews[$key] = $itemsAmountNew;
                $sumAmountNew += $itemsAmountNew;
            }

            if (isset($shipping)) {
                $sumAmountNew += $shipping['Amount'];
            }

            if ($sumAmountNew != $amount) {
                $max_key = array_keys($amountNews, max($amountNews))[0];    // ключ макс значения
                $amountNews[$max_key] = max($amountNews) + ($amount - $sumAmountNew);
            }

            foreach ($amountNews as $key => $item) {
                $items[$key]['Amount'] = $amountNews[$key];
            }
        }
        return $items;
    }

    public function convertEncodingTinkoff($productName)
    {
        return mb_convert_encoding($productName, "UTF-8", LANG_CHARSET);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPaymentIdFromRequest(Request $request)
    {
        return $request->get('orderNumber');
    }

    /**
     * @param Payment $payment
     * @param Request $request
     * @return PaySystem\ServiceResult
     */
    public function processRequest(Payment $payment, Request $request)
    {
        die('process request');
    }

    /**
     * @return array
     */
    public function getCurrencyList()
    {
        return array('RUB');
    }

    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    public function getOrderTinkoff($payment)
    {
        $collection = $payment->getCollection();
        $order = $collection->getOrder();
        return $order;
    }
}
