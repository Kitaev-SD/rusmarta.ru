<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><?

include(GetLangFileName(dirname(__FILE__) . "/", "/tinkoff.php"));

$psTitle = GetMessage("SALE_TINKOFF_TITLE");
$psDescription = GetMessage("SALE_TINKOFF_DESCRIPTION");

$taxationsList = array(
	'osn' => array('NAME' => GetMessage('SALE_TINKOFF_TAXATION_OSN')),
	'usn_income' => array('NAME' => GetMessage('SALE_TINKOFF_TAXATION_USN_IMCOME')),
	'usn_income_outcome' => array('NAME' => GetMessage('SALE_TINKOFF_TAXATION_USN_IMCOME_OUTCOME')),
	'envd' => array('NAME' => GetMessage('SALE_TINKOFF_TAXATION_ENVD')),
	'esn' => array('NAME' => GetMessage('SALE_TINKOFF_TAXATION_ESN')),
	'patent' => array('NAME' => GetMessage('SALE_TINKOFF_TAXATION_PATENT')),
);

$enableTaxations = array(
	'0' => array('NAME' => GetMessage('SALE_TINKOFF_NO')),
	'1' => array('NAME' => GetMessage('SALE_TINKOFF_YES'))
);

$languageList = array(
	'en' => array('NAME' => GetMessage('SALE_TINKOFF_LANGUAGE_EN')),
	'ru' => array('NAME' => GetMessage('SALE_TINKOFF_LANGUAGE_RU')),
);

$vatList = array(
	'none' => array('NAME' => GetMessage('SALE_TINKOFF_VAT_NONE')),
	'vat0' => array('NAME' => GetMessage('SALE_TINKOFF_VAT_ZERO')),
	'vat10' => array('NAME' => GetMessage('SALE_TINKOFF_VAT_REDUCED')),
	'vat18' => array('NAME' => GetMessage('SALE_TINKOFF_VAT_STANDARD')),
    'vat20' => array('NAME' => GetMessage('SALE_TINKOFF_VAT_TWENTY')),
);

$paymentMethodList = array(
    "full_prepayment"       => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_FULL_PREPAYMENT")),
    "prepayment"            => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_PREPAYMENT")),
    "advance"               => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_ADVANCE")),
    "full_payment"          => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_FULL_PAYMENT")),
    "partial_payment "      => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_PARTIAL_PAYMENT")),
    "credit"                => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_CREDIT")),
    "credit_payment "       => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_CREDIT_PAYMENT")),
);

$paymentObjectList = array(
    "commodity"             => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_COMMODITY")),
    "excise"                => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_EXCISE")),
    "job"                   => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_JOB")),
    "service"               => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_SERVICE")),
    "gambling_bet "         => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_GAMBLING_BET")),
    "gambling_prize"        => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_GAMBLING_PRIZE")),
    "lottery"               => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_LOTTERY")),
    "lottery_prize"         => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_LOTTERY_PRIZE")),
    "intellectual_activity" => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_INTELLECTUAL_ACTIVITY")),
    "payment"               => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_PAYMENT")),
    "agent_commission"      => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_AGENT_COMMISSION")),
    "composite"             => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_PARTIAL_COMPOSITE")),
    "another"               => array("NAME" => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_ANOTHER")),
);

$arPSCorrespondence = array(
    "LANGUAGE_PAYMENT" => array(
        "NAME" => GetMessage("SALE_TINKOFF_LANGUAGE_NAME"),
        "DESCR" => GetMessage("SALE_TINKOFF_LANGUAGE_DESCR"),
        "VALUE" => $languageList,
        "TYPE" => "SELECT"
    ),
    "TAXATION" => array(
        "NAME" => GetMessage("SALE_TINKOFF_TAXATION_NAME"),
        "DESCR" => GetMessage("SALE_TINKOFF_TAXATION_DESCR"),
        "VALUE" => $taxationsList,
        "TYPE" => "SELECT"
    ),
    "TERMINAL_ID" => array(
        "NAME" => GetMessage("SALE_TINKOFF_TERMINAL_ID_NAME"),
        "DESCR" => GetMessage("SALE_TINKOFF_TERMINAL_ID_DESCR"),
        "VALUE" => "",
        "TYPE" => ""
    ),
    "ENABLE_TAXATION" => array(
        "NAME" => GetMessage("SALE_TINKOFF_ENABLE_TAXATION_NAME"),
        "DESCR" => GetMessage("SALE_TINKOFF_ENABLE_TAXATION_DESCR"),
        "TYPE" => "SELECT",
        "VALUE" => $enableTaxations,
        'DEFAULT' => '0'
    ),
    "DELIVERY_TAXATION" => array(
        "NAME" => GetMessage("SALE_TINKOFF_DELIVERY_TAXATION_NAME"),
        "DESCR" => GetMessage("SALE_TINKOFF_DELIVERY_TAXATION_DESCR"),
        "VALUE" => $vatList,
        "TYPE" => "SELECT"
    ),
    "SHOP_SECRET_WORD" => array(
        "NAME" => GetMessage("SALE_TINKOFF_SHOP_SECRET_WORD_NAME"),
        "DESCR" => GetMessage("SALE_TINKOFF_SHOP_SECRET_WORD_DESCR"),
        "VALUE" => "",
        "TYPE" => ""
    ),
	"EMAIL_COMPANY"    => array(
        "NAME"  => GetMessage("SALE_TINKOFF_EMAIL_COMPANY_NAME"),
        "VALUE" => "",
        "TYPE"  => ""
    ),
	 "PAYMENT_METHOD"  => array(
        "NAME"  => GetMessage("SALE_TINKOFF_PAYMENT_METHOD_NAME"),
        "VALUE" => $paymentMethodList,
        "TYPE"  => "SELECT",
    ),
    "PAYMENT_OBJECT"   => array(
        "NAME"  => GetMessage("SALE_TINKOFF_PAYMENT_OBJECT_NAME"),
        "VALUE" => $paymentObjectList,
        "TYPE"  => "SELECT"
    )
);
?>