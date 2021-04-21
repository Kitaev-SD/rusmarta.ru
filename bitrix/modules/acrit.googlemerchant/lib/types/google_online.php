<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["google_online"] = array(
    "CODE" => "google_online",
    "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ONLINE_NAME" ),
    "DESCRIPTION" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ONLINE_DESCRIPTION" ),
    "REG" => "http://google.com/merchants/",
    "HELP" => "https://support.google.com/merchants/?hl=ru#topic=3404818",
    "FIELDS" => array(
        array(
              "CODE" => "id",
              "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ONLINE_ID" ),
              "VALUE" => "ID",
              "TYPE" => "field",
              "REQUIRED" => "Y",
              "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE" => "availability",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ONLINE_AVAILABILITY" ),
            "VALUE" => "",
            "TYPE" => "const",
            "CONDITION" => array(
                "CLASS_ID" => "CondGroup",
                "DATA" => array(
                    "All" => "AND",
                    "True" => "True"
                ),
                "CHILDREN" => array(
                    array(
                        "CLASS_ID" => "CondCatQuantity",
                        "DATA" => array(
                                "logic" => "EqGr",
                                "value" => "1"
                        )
                    )
                )
            ),
            "USE_CONDITION" => "Y",
            "CONTVALUE_TRUE" => "in stock",
            "CONTVALUE_FALSE" => "out of stock",
            "REQUIRED" => "Y",
            "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE" => "price",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ONLINE_PRICE" ),
            "REQUIRED" => "Y",
            "DELETE_ONEMPTY" => "N",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "sale_price",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ONLINE_SALE_PRICE" ),
            "DELETE_ONEMPTY" => "N",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "sale_price_effective_date",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ONLINE_SALE_PRICE_EFFECTIVE_DATE" ),
            "TYPE" => "field",
        ),
    ),
    "FORMAT" => '<?xml version="1.0" encoding="#ENCODING#"?>
    <rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <link>#SITE_URL#</link>
        <description>#DESCRIPTION#</description>
        #ITEMS#
    </channel>
</rss>
    ',
    "DATEFORMAT" => "Y-m-d_h:i",
    "ENCODING" => "utf8",
);

$bCatalog = false;
if( CModule::IncludeModule( "catalog" ) ){
    $arBasePrice = CCatalogGroup::GetBaseGroup();
    $basePriceCode = "CATALOG-PRICE_".$arBasePrice["ID"];
    $basePriceCodeWithDiscount = "CATALOG-PRICE_".$arBasePrice["ID"]."_WD";
    $bCatalog = true;
    
    $profileTypes["google_online"]["FIELDS"][2] = array(
        "CODE" => "price",
        "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ONLINE_PRICE" ),
        "REQUIRED" => "Y",
        "DELETE_ONEMPTY" => "N",
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );
    
    $profileTypes["google_online"]["FIELDS"][3] = array(
        "CODE" => "sale_price",
        "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ONLINE_SALE_PRICE" ),
        "DELETE_ONEMPTY" => "N",
        "TYPE" => "field",
        "VALUE" => $basePriceCodeWithDiscount,
    );
}

$profileTypes["google_online"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ONLINE_PORTAL_REQUIREMENTS" );
$profileTypes["google_online"]["EXAMPLE"] = GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ONLINE_EXAMPLE" );

$profileTypes["google_online"]["ITEMS_FORMAT"] = "<item>
    <g:id>#id#</g:id>
    <g:availability>#availability#</g:availability>
    <g:price>#price#</g:price>
    <g:sale_price>#sale_price#</g:sale_price>
    <g:sale_price_effective_date>#sale_price_effective_date#</g:sale_price_effective_date>
</item>";