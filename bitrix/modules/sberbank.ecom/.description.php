<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

IncludeModuleLangFile(__FILE__);

require(__DIR__ . "/config.php");

$psTitle = GetMessage('RBS_PARTNER_NAME');
//$psDescription = GetMessage('RBS_PAYMENT_PAY_FROM', array('#BANK#' => GetMessage('RBS_PARTNER_NAME'))); //'Оплата через ' . $mess["partner_name"];
$user_name_name = GetMessage('RBS_PAYMENT_LOGIN'); //"Логин";
$password_name = GetMessage('RBS_PAYMENT_PASSWORD'); //"Пароль";
$two_stage_name = GetMessage('RBS_PAYMENT_STAGING'); //"Стадийность платежа";
$two_stage_desc = GetMessage('RBS_PAYMENT_STAGING_DESC'); //"Если значение 'Y', будет производиться двухстадийный платеж. При пустом значении будет производиться одностадийный платеж.";
$test_mode_name = GetMessage('RBS_PAYMENT_TEST_MODE'); //"Тестовый режим";
$test_mode_desc = GetMessage('RBS_PAYMENT_TEST_MODE_DESC'); //"Если значение 'Y', плагин будет работать в тестовом режиме. При пустом значении будет стандартный режим работы.";
$logging_name = GetMessage('RBS_PAYMENT_LOGGING'); //"Логирование";
$logging_desc = GetMessage('RBS_PAYMENT_LOGGING_DESC'); //"Если значение 'Y', плагин будет логировать свою работу в файл. При пустом значении логирование происходить не будет.";
$order_number_name = GetMessage('RBS_PAYMENT_ACCOUNT_NUMBER'); //"Уникальный идентификатор заказа в магазине";
$amount_name = GetMessage('RBS_PAYMENT_ORDER_SUM'); //"Сумма заказа";
$shipment_name = GetMessage('RBS_PAYMENT_SHIPMENT_NAME'); //"Разрешить отгрузку";
$shipment_desc = GetMessage('RBS_PAYMENT_SHIPMENT_DESC'); //"Если значение 'Y', то после успешной оплаты будет автоматически разрешена отгрузка заказа.";
$shipment_set_payed = GetMessage('RBS_PAYMENT_SET_PAYED'); //"Устанавливать ли в статус оплачено";
$shipment_set_payed_desc = GetMessage('RBS_PAYMENT_SET_PAYED_DESC'); //"Устанавливать ли в статус оплачено";
//$ckeck_name = GetMessage('RBS_PAYMENT_CHECK'); //"Устанавливать ли в статус оплачено";
$check_description = GetMessage('RBS_PAYMENT_CHECK_DESC'); //"Устанавливать ли в статус оплачено";
$auto_open_form = GetMessage('RBS_PAYMENT_AUTO_OPEN_FORM'); // "Автоматическое открытие палатежной формы после оформления заказа";
$auto_open_form_desc = GetMessage('RBS_PAYMENT_AUTO_OPEN_FORM_DESC'); // "Автоматическое открытие палатежной формы после оформления заказа";
$ffd_format = GetMessage('RBS_PAYMENT_FFD_FORMAT'); 
$ffd_format_desc = GetMessage('RBS_PAYMENT_FFD_FORMAT_DESC'); 

$arPSCorrespondence = array(

    "PASSWORD" => array(
        "NAME" => $password_name,
        'SORT' => 210,
    ),
    "USER_NAME" => array(
        "NAME" => $user_name_name,
        'SORT' => 200,
    ),
    "TWO_STAGE" => array(
        "NAME" => $two_stage_name,
        "DESCR" => $two_stage_desc,
        'SORT' => 240,
        'INPUT' => array(
            'TYPE' => 'Y/N'
        ),
        'DEFAULT' => array(
            "PROVIDER_VALUE" => "N",
            "PROVIDER_KEY" => "INPUT"
        )

    ),

    "AUTO_OPEN_FORM" => array(
        "NAME" => $auto_open_form,
        "DESCR" => $auto_open_form_desc,
        'SORT' => 275,
        'INPUT' => array(
            'TYPE' => 'Y/N'
        ),
        'DEFAULT' => array(
            "PROVIDER_VALUE" => "N",
            "PROVIDER_KEY" => "INPUT"
        )
    ),
    "TEST_MODE" => array(
        "NAME" => $test_mode_name,
        "DESCR" => $test_mode_desc,
//      "VALUE" => "Y",
        'SORT' => 230,
        'INPUT' => array(
            'TYPE' => 'Y/N'
        ),
        'DEFAULT' => array(
            "PROVIDER_VALUE" => "N",
            "PROVIDER_KEY" => "INPUT"
        )
    ),
    "LOGGING" => array(
        "NAME" => $logging_name,
        "DESCR" => $logging_desc,
        'SORT' => 235,
        'INPUT' => array(
            'TYPE' => 'Y/N'
        ),
        'DEFAULT' => array(
            "PROVIDER_VALUE" => "Y",
            "PROVIDER_KEY" => "INPUT"
        )
    ),
    "ORDER_NUMBER" => array(
        "NAME" => $order_number_name,
//      "VALUE" => "ID",
//      "TYPE" => "ORDER",
        'SORT' => 40,
        'DEFAULT' => array(
            'PROVIDER_KEY' => 'ORDER',
            'PROVIDER_VALUE' => 'ID'
        )
    ),
    "AMOUNT" => array(
        "NAME" => $amount_name,
//      "VALUE" => "SHOULD_PAY",
//      "TYPE" => "ORDER",
        'SORT' => 50,
        'DEFAULT' => array(
            'PROVIDER_KEY' => 'ORDER',
            'PROVIDER_VALUE' => 'SHOULD_PAY'
        )
    ),
    "SHIPMENT_ENABLE" => array(
        "NAME" => $shipment_name,
        "DESCR" => $shipment_desc,
//      "TYPE" => "VALUE",
        'SORT' => 280,
        'INPUT' => array(
            'TYPE' => 'Y/N'
        ),
        'DEFAULT' => array(
            "PROVIDER_VALUE" => "N",
            "PROVIDER_KEY" => "INPUT"
        )
    ),
    "FFD_FORMAT" => array(
        "NAME" => $ffd_format,
        "DESCR" => $ffd_format_desc,
        'SORT' => 300,
        "INPUT" => array(
            'TYPE' => 'ENUM',
            'OPTIONS' => array(
                "v1" => '1.0',
                "v2" => '1.05'
            )
        ),
        'DEFAULT' => array(
            "PROVIDER_VALUE" => "v1",
            "PROVIDER_KEY" => "INPUT"
        )
    ),
    "FFD_PAYMENT_METHOD" => array(
        "NAME" => GetMessage('RBS_PAYMENT_FFD_PAYMENT_METHOD_NAME'),
        "DESCR" => GetMessage('RBS_PAYMENT_FFD_PAYMENT_METHOD_DESCR'),
        'SORT' => 310,
        "INPUT" => array(
            'TYPE' => 'ENUM',
            'OPTIONS' => array(
                "1" => GetMessage('RBS_PAYMENT_FFD_PAYMENT_METHOD_1'),
                "2" => GetMessage('RBS_PAYMENT_FFD_PAYMENT_METHOD_2'),
                "3" => GetMessage('RBS_PAYMENT_FFD_PAYMENT_METHOD_3'),
                "4" => GetMessage('RBS_PAYMENT_FFD_PAYMENT_METHOD_4'),
                "5" => GetMessage('RBS_PAYMENT_FFD_PAYMENT_METHOD_5'),
                "6" => GetMessage('RBS_PAYMENT_FFD_PAYMENT_METHOD_6'),
                "7" => GetMessage('RBS_PAYMENT_FFD_PAYMENT_METHOD_7'),
            )
        ),
        'DEFAULT' => array(
            "PROVIDER_VALUE" => "4",
            "PROVIDER_KEY" => "INPUT"
        )
    ),
    "FFD_PAYMENT_OBJECT" => array(
        "NAME" => GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_NAME'),
        "DESCR" => GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_DESCR'),
        'SORT' => 320,
        "INPUT" => array(
            'TYPE' => 'ENUM',
            'OPTIONS' => array(
                "1"  =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_1'),
                "2"  =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_2'),
                "3"  =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_3'),
                "4"  =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_4'),
                "5"  =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_5'),
                "6"  =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_6'),
                "7"  =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_7'),
                "8"  =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_8'),
                "9"  =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_9'),
                "10" =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_10'),
                "11" =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_11'),
                "12" =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_12'),
                "13" =>  GetMessage('RBS_PAYMENT_FFD_PAYMENT_OBJECT_13'),
            )
        ),
        'DEFAULT' => array(
            "PROVIDER_VALUE" => "1",
            "PROVIDER_KEY" => "INPUT"
        )
    ),




);