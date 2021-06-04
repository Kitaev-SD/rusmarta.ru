<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><?

Loc::loadMessages(__FILE__);

$data = array(
    'NAME'        => GetMessage('SALE_TINKOFF_TITLE'),
    'DESCRIPTION' => GetMessage('SALE_TINKOFF_DESCRIPTION'),
    'CODES'       => array(
        "TERMINAL_ID" => array(
            "NAME"        => GetMessage("SALE_TINKOFF_TERMINAL_ID_NAME"),
            "DESCRIPTION" => GetMessage("SALE_TINKOFF_TERMINAL_ID_DESCR"),
            "SORT"        => 100,
        ),

        "SHOP_SECRET_WORD" => array(
            "NAME"        => GetMessage("SALE_TINKOFF_SHOP_SECRET_WORD_NAME"),
            "DESCRIPTION" => GetMessage("SALE_TINKOFF_SHOP_SECRET_WORD_DESCR"),
            "SORT"        => 200,
        ),

        "ENABLE_TAXATION" => array(
            "NAME"        => GetMessage("SALE_TINKOFF_ENABLE_TAXATION_NAME"),
            "DESCRIPTION" => GetMessage("SALE_TINKOFF_ENABLE_TAXATION_DESCR"),
            "INPUT"       => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    '0' => GetMessage('SALE_TINKOFF_NO'),
                    '1' => GetMessage('SALE_TINKOFF_YES'),
                )
            ),
            "SORT"        => 300,
        ),

        "EMAIL_COMPANY"     => array(
            "NAME"  => GetMessage("SALE_TINKOFF_EMAIL_COMPANY_NAME"),
            "VALUE" => "",
            "TYPE"  => "",
        ),

        "TAXATION" => array(
            "NAME"        => GetMessage("SALE_TINKOFF_TAXATION_NAME"),
            "DESCRIPTION" => GetMessage("SALE_TINKOFF_TAXATION_DESCR"),
            "INPUT"       => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    'osn'                => GetMessage('SALE_TINKOFF_TAXATION_OSN'),
                    'usn_income'         => GetMessage('SALE_TINKOFF_TAXATION_USN_IMCOME'),
                    'usn_income_outcome' => GetMessage('SALE_TINKOFF_TAXATION_USN_IMCOME_OUTCOME'),
                    'envd'               => GetMessage('SALE_TINKOFF_TAXATION_ENVD'),
                    'esn'                => GetMessage('SALE_TINKOFF_TAXATION_ESN'),
                    'patent'             => GetMessage('SALE_TINKOFF_TAXATION_PATENT'),
                )
            ),
            "SORT"        => 400,
        ),

        "PAYMENT_METHOD" => array(
            "NAME"  => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_NAME"),
            "INPUT" => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    "full_prepayment"  => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_FULL_PREPAYMENT"),
                    "prepayment"       => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_PREPAYMENT"),
                    "advance"          => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_ADVANCE"),
                    "full_payment"     => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_FULL_PAYMENT"),
                    "partial_payment " => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_PARTIAL_PAYMENT"),
                    "credit"           => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_CREDIT"),
                    "credit_payment "  => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_CREDIT_PAYMENT"),
                )
            ),
            "SORT"  => 500,
        ),

        "PAYMENT_OBJECT" => array(
            "NAME"  => GetMessage("SALE_TINKOFF_PAYMENT_OBJECT_NAME"),
            "INPUT" => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    "commodity"             => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_COMMODITY"),
                    "excise"                => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_EXCISE"),
                    "job"                   => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_JOB"),
                    "service"               => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_SERVICE"),
                    "gambling_bet "         => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_GAMBLING_BET"),
                    "gambling_prize"        => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_GAMBLING_PRIZE"),
                    "lottery"               => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_LOTTERY"),
                    "lottery_prize"         => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_LOTTERY_PRIZE"),
                    "intellectual_activity" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_INTELLECTUAL_ACTIVITY"),
                    "payment"               => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_PAYMENT"),
                    "agent_commission"      => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_AGENT_COMMISSION"),
                    "composite"             => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_PARTIAL_COMPOSITE"),
                    "another"               => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_ANOTHER"),
                )
            ),
            "SORT"  => 600,
        ),

        "DELIVERY_TAXATION" => array(
            "NAME"        => GetMessage("SALE_TINKOFF_DELIVERY_TAXATION_NAME"),
            "DESCRIPTION" => GetMessage("SALE_TINKOFF_DELIVERY_TAXATION_DESCR"),
            "INPUT"       => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    'none'  => GetMessage('SALE_TINKOFF_VAT_NONE'),
                    'vat0'  => GetMessage('SALE_TINKOFF_VAT_ZERO'),
                    'vat10' => GetMessage('SALE_TINKOFF_VAT_REDUCED'),
                    'vat20' => GetMessage('SALE_TINKOFF_VAT_STANDARD'),
                )
            ),
            "SORT"        => 700,
        ),

        "LANGUAGE_PAYMENT" => array(
            "NAME"        => GetMessage("SALE_TINKOFF_LANGUAGE_NAME"),
            "DESCRIPTION" => GetMessage("SALE_TINKOFF_LANGUAGE_DESCR"),
            "INPUT"       => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    'ru' => GetMessage('SALE_TINKOFF_LANGUAGE_RU'),
                    'en' => GetMessage('SALE_TINKOFF_LANGUAGE_EN')
                )
            ),
            "SORT"        => 800,
        ),
    )
);
