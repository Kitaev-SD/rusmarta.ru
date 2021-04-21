<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
use Bitrix\Sale\Order;
use Bitrix\Sale\Tax;
use CSaleDelivery;
use Bitrix\Sale\Delivery\Services\Manager;

include(GetLangFileName(dirname(__FILE__) . "/", "/tinkoff.php"));
include(dirname(__FILE__) . "/sdk/tinkoff_autoload.php");

$shouldPay = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"];
$entityId = CSalePaySystemAction::GetParamValue("ORDER_PAYMENT_ID");
list($orderID, $paymentId) = \Bitrix\Sale\PaySystem\Manager::getIdsByPayment($entityId);
//get current order
$orderModel = new CSaleOrder();
$orderData = $orderModel->GetByID($orderID);

//get current user
$userModel = new CUser();
$user = $userModel->GetByID($orderData['USER_ID']);

//load Bitrix\Sale\Order
$order = Order::load($orderID);

$mail = getOwnerEmailTinkoff($orderID);
$phone = getOwnerPhoneTinkoff($orderID, $user);
$fio = getOwnerFioTinkoff($orderID);

$paymentData = array(
    'OrderId' => $orderData['ACCOUNT_NUMBER'],
    'Amount' => round($shouldPay * 100),
    'DATA' => array('Email' => $mail, 'Name' => convertEncodingTinkoff($fio), 'Connection_type' => 'Bitrix_2.0.1_atol'),
);

if ($phone) {
    $paymentData['DATA']['Phone'] = $phone;
}

$errorMessage = '';
$isTaxationEnabled = CSalePaySystemAction::GetParamValue("ENABLE_TAXATION");

if ($isTaxationEnabled) {
    //get products data including vat
    $productsList = CSaleBasket::GetList(array(), array("ORDER_ID" => $orderID), false, false, array());
    $items = array();
    $tax = '';
    CModule::IncludeModule("catalog");

    foreach ($productsList->arResult as $product) {
        $productData = CCatalogProduct::GetByID($product['PRODUCT_ID']);
        $vatData = CCatalogVat::GetByID($productData['VAT_ID']);
        $vatData = $vatData->Fetch();

        if (!$vatData) {
            $vat = 'none';
        } else {
            $tax = (int)round($vatData['RATE']);
            if ($tax === 20) {
                $vat = 'vat20';
            } elseif ($tax === 18) {
                $vat = 'vat18';
            } elseif ($tax === 10) {
                $vat = 'vat10';
            } elseif ($tax === 0) {
                $vat = 'vat0';
            } else {
                $vat = 'none';
            }
        }

        $items[] = array(
            "Name" => mb_substr(convertEncodingTinkoff($product['NAME']), 0, 64),
            "Price" => round($product['PRICE'] * 100),
            "Quantity" => round($product['QUANTITY'], 3, PHP_ROUND_HALF_UP),
            "Amount" => round($product['PRICE'] * $product['QUANTITY'] * 100),
            "PaymentMethod" => trim(CSalePaySystemAction::GetParamValue("PAYMENT_METHOD")),
            "PaymentObject" => trim(CSalePaySystemAction::GetParamValue("PAYMENT_OBJECT")),
            "Tax" => $vat,
        );
    }

    if(CSalePaySystemAction::GetParamValue("LANGUAGE_PAYMENT") == 'en'){
        $paymentData['Language'] = 'en';
    }

    if ($orderData['PRICE_DELIVERY'] != 0) {
        $deliveryVat = CSalePaySystemAction::GetParamValue("DELIVERY_TAXATION");

        if (!$deliveryVat) {
            $errorMessage .= GetMessage('SALE_TINKOFF_TAX_DELIVERY_ERROR');
        }

        if (!intval($orderData['DELIVERY_ID']) && $orderData['DELIVERY_ID']) {
            $deliveryID = CSaleDelivery::getIdByCode($orderData['DELIVERY_ID']);
            $deliveryChild = Manager::getById($deliveryID);
            $deliveryParent = Manager::getById($deliveryChild['PARENT_ID']);
            $deliveryFullName = $deliveryParent['NAME'] . ' (' . $deliveryChild['NAME'] . ')';
        } else {
            $deliveryModel = CSaleDelivery::GetByID($orderData['DELIVERY_ID']);
        }

        $deliveryName = $deliveryFullName ? $deliveryFullName : $deliveryModel['NAME'];
        $items[] = array(
            "Name" => mb_substr(convertEncodingTinkoff($deliveryName), 0, 64),
            "Price" => round($orderData['PRICE_DELIVERY'] * 100),
            "Quantity" => 1,
            "Amount" => round($orderData['PRICE_DELIVERY'] * 100),
            "PaymentMethod" => trim(CSalePaySystemAction::GetParamValue("PAYMENT_METHOD")),
            "PaymentObject" => 'service',
            "Tax" => $deliveryVat,
        );
    }

    $taxation = CSalePaySystemAction::GetParamValue("TAXATION");

    if (!$taxation) {
        $errorMessage .= GetMessage('SALE_TINKOFF_TAXATION_ERROR');
    }

    $paymentData['Receipt'] = array(
        'EmailCompany' => mb_substr(CSalePaySystemAction::GetParamValue("EMAIL_COMPANY"), 0, 64),
        'Email' => $mail,
        'Phone' => getOwnerPhoneTinkoff($orderID, $user),
        'Taxation' => $taxation,
        'Items' => $items,
    );
}

$bankHandler = new TinkoffMerchantAPI(CSalePaySystemAction::GetParamValue("TERMINAL_ID"), CSalePaySystemAction::GetParamValue("SHOP_SECRET_WORD"));
$request = $bankHandler->buildQuery('Init', $paymentData);

logsTinkoff($paymentData, $request);
$request = json_decode($request);

if (!isset($request->PaymentURL)) {
    $errorMessage .= '<br>' . GetMessage("SALE_TINKOFF_UNAVAILABLE");
}

if (!$errorMessage): ?>
    <FORM ACTION="<?php echo $request->PaymentURL; ?>" METHOD="post">
        <INPUT TYPE="SUBMIT" VALUE="<? echo GetMessage("SALE_TINKOFF_PAYBUTTON_NAME") ?>">
    </FORM>
    <script>document.location.href ='<?=$request->PaymentURL;?>'</script>
    <?else: ?>
    <b><?php echo $errorMessage; ?></b>
<?php endif; ?>

<?php

function getOwnerEmailTinkoff($orderID)
{
    //���� � ��������� ������ e-mail
    $res = CSaleOrderPropsValue::GetOrderProps($orderID);

    while ($row = $res->fetch()) {
        if ($row['IS_EMAIL'] == 'Y' && check_email($row['VALUE'])) {
            return $row['VALUE'];
        }
    }
    if ($order = CSaleOrder::getById($orderID)) {
        if ($user = CUser::GetByID($order['USER_ID'])->fetch()) {
            return $user['EMAIL'];
        }
    }

    return "";
}

function getOwnerPhoneTinkoff($orderID, $user)
{
    $res = CSaleOrderPropsValue::GetOrderProps($orderID);

    while ($row = $res->fetch()) {
        if ($row['CODE'] == 'PHONE') {
            if (!empty($phone = $row['VALUE'])) {
                return $phone;
            }
        }
    }
    if (!empty($phone = $user->arResult[0]['PERSONAL_PHONE'])) {
        return $phone;
    }

    return "";
}

function getOwnerFioTinkoff($orderID)
{
    $res = CSaleOrderPropsValue::GetOrderProps($orderID);

    while ($row = $res->fetch()) {
        if ($row['IS_PROFILE_NAME'] == 'Y') {
            return $row['VALUE'];
        }
    }
    if ($order = CSaleOrder::getById($orderID)) {
        if ($user = CUser::GetByID($order['USER_ID'])->fetch()) {
            $fio = implode(" ", array($user['LAST_NAME'], $user['NAME'], $user['SECOND_NAME']));
            return $fio;
        }
    }

    return false;
}

function convertEncodingTinkoff($data)
{
    return mb_convert_encoding($data, "UTF-8", LANG_CHARSET);
}

function logsTinkoff($paymentData, $request)
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

?>