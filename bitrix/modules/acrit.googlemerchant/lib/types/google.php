<?php
IncludeModuleLangFile( __FILE__ );

$profileTypes["google"] = array(
    "CODE" => "google",
    "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_NAME" ),
    "DESCRIPTION" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_DESCRIPTION" ),
    "REG" => "http://google.com/merchants/",
    "HELP" => "https://support.google.com/merchants/?hl=ru#topic=3404818",
    "FIELDS" => array(
        array(
              "CODE" => "id",
              "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ID" ),
              "VALUE" => "ID",
              "TYPE" => "field",
              "REQUIRED" => "Y",
              "DELETE_ONEMPTY" => "N",
        ),
        array(
              "CODE" => "title",
              "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_TITLE" ),
              "VALUE" => "NAME",
              "TYPE" => "field",
              "REQUIRED" => "Y",
              "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE" => "description",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_DESCRIPTION" ),
            "VALUE" => "DETAIL_TEXT",
            "TYPE" => "field",
        ),
        array(
            "CODE" => "link",
            "NAME" =>  GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_LINK" ),
            "VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => "field",
            "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE" => "image_link",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_IMAGELINK" ),
            "VALUE" => "DETAIL_PICTURE",
            "TYPE" => "field",
            "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE" => "additional_image_link",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ADDITIONAL_IMAGELINK" ),
            "VALUE" => "PREVIEW_PICTURE",
            "TYPE" => "field",
            "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE" => "mobile_link",
            "NAME" =>  GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_MOBILE_LINK" ),
            "VALUE" => "DETAIL_PAGE_URL",
            "TYPE" => "field",
            "DELETE_ONEMPTY" => "N",
        ),
        array(
            "CODE" => "availability",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_AVAILABILITY" ),
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
            "CODE" => "availability_date",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_AVAILABILITY_DATE" ),
            "TYPE" => "field",
        ),
        array(
            "CODE" => "expiration_date",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_EXPIRATION_DATE" ),
        ),
        array(
            "CODE" => "price",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_PRICE" ),
            "REQUIRED" => "Y",
            "DELETE_ONEMPTY" => "N",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "price_currency",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_PRICE_CURRENCY" ),
            "REQUIRED" => "Y",
            "DELETE_ONEMPTY" => "N",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
        ),
        array(
            "CODE" => "sale_price",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SALE_PRICE" ),
            "DELETE_ONEMPTY" => "N",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "0",
        ),
        array(
            "CODE" => "sale_price_currency",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SALE_PRICE_CURRENCY" ),
            "DELETE_ONEMPTY" => "N",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "RUB",
        ),
        array(
            "CODE" => "sale_price_effective_date",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SALE_PRICE_EFFECTIVE_DATE" ),
            "TYPE" => "field",
        ),
        array(
            "CODE" => "unit_pricing_measure",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_UNIT_PRICING_MEASURE" ),
            "TYPE" => "field",
        ),
        array(
            "CODE" => "unit_pricing_base_measure",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_UNIT_PRICING_BASE_MEASURE" ),
            "TYPE" => "field",
        ),
        array(
            "CODE" => "google_product_category",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_PRODUCTCATEGORY" ),
        ),
        array(
            "CODE" => "product_type",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_TYPE" ),
        ),
        array(
            "CODE" => "brand",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_BRAND" ),
        ),
        array(
            "CODE" => "gtin",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_GTIN" ),
        ),
        array(
            "CODE" => "mpn",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_MPN" ),
        ),
        array(
            "CODE" => "identifier_exists",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_IDENTIFIER_EXISTS" ),
        ),
        array(
            "CODE" => "condition",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_CONDITION" ),
            "REQUIRED" => "Y",
            "DELETE_ONEMPTY" => "N",
            "TYPE" => "const",
            "CONTVALUE_TRUE" => "new",
        ),
        array(
            "CODE" => "adult",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ADULT" ),
        ),
        array(
            "CODE" => "multipack",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_MULTIPACK" ),
        ),
        array(
            "CODE" => "is_bundle",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_IS_BUNDLE" ),
        ),
        array(
            "CODE" => "energy_efficiency_class",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ENERGY_EFFICIENCY_CLASS" ),
        ),
        array(
            "CODE" => "age_group",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_AGE_GROUP" ),
        ),
        array(
            "CODE" => "color",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_COLOR" ),
        ),
        array(
            "CODE" => "gender",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_GENDER" ),
        ),
        array(
            "CODE" => "material",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_MATERIAL" ),
        ),
        array(
            "CODE" => "pattern",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_PATTERN" ),
        ),
        array(
            "CODE" => "size",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SIZE" ),
        ),
        array(
            "CODE" => "size_type",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SIZE_TYPE" ),
        ),
        array(
            "CODE" => "size_system",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SIZE_SYSTEM" ),
        ),
        array(
            "CODE" => "item_group_id",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ITEM_GROUP_ID" ),
            "TYPE" => "field",
            "VALUE" => "IBLOCK_SECTION_ID",
        ),
        array(
            "CODE" => "adwords_redirect",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_ADWORDS_REDIRECT" ),
        ),
        array(
            "CODE" => "excluded_destination",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_EXCLUDED_DESTINATION" ),
        ),
        array(
            "CODE" => "custom_label_0",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_CUSTOM_LABEL_0" ),
        ),
        array(
            "CODE" => "custom_label_1",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_CUSTOM_LABEL_1" ),
        ),
        array(
            "CODE" => "custom_label_2",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_CUSTOM_LABEL_2" ),
        ),
        array(
            "CODE" => "custom_label_3",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_CUSTOM_LABEL_3" ),
        ),
        array(
            "CODE" => "custom_label_4",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_CUSTOM_LABEL_4" ),
        ),
        array(
            "CODE" => "promotion_id",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_PROMOTION_ID" ),
        ),
        array(
            "CODE" => "shipping_country",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_COUNTRY" ),
            "TYPE" => "const",
            "CONTVALUE_TRUE" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_COUNTRY_DEFAULT" ),
        ),
        array(
            "CODE" => "shipping_service",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SERVICE" ),
        ),
        array(
            "CODE" => "shipping_price",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SHIPPINGPRICE" ),
        ),
        array(
            "CODE" => "shipping_label",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SHIPPINGLABEL" ),
        ),
        array(
            "CODE" => "shipping_weight",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SHIPPINGWEIGHT" ),
            "TYPE" => "field",
            "VALUE" => "CATALOG-WEIGHT",
        ),
        array(
            "CODE" => "shipping_length",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SHIPPINGLENGTH" ),
        ),
        array(
            "CODE" => "shipping_width",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SHIPPINGWIDTH" ),
        ),
        array(
            "CODE" => "shipping_height",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SHIPPINGHEIGHT" ),
        ),
        array(
            "CODE" => "min_handling_time",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_MIN_HANDLING_TIME" ),
        ),
        array(
            "CODE" => "max_handling_time",
            "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_MAX_HANDLING_TIME" ),
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

    $profileTypes["google"]["FIELDS"][10] = array(
        "CODE" => "price",
        "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_PRICE" ),
        "REQUIRED" => "Y",
        "DELETE_ONEMPTY" => "N",
        "TYPE" => "field",
        "VALUE" => $basePriceCode,
    );

    $profileTypes["google"]["FIELDS"][12] = array(
        "CODE" => "sale_price",
        "NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_SALE_PRICE" ),
        "DELETE_ONEMPTY" => "N",
        "TYPE" => "field",
        "VALUE" => $basePriceCodeWithDiscount,
    );
}

$profileTypes["google"]["PORTAL_REQUIREMENTS"] = GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_PORTAL_REQUIREMENTS" );
$profileTypes["google"]["EXAMPLE"] = GetMessage( "ACRIT_GOOGLEMERCHANT_GOOGLE_MERCHANT_EXAMPLE" );

$profileTypes["google"]["ITEMS_FORMAT"] = "<item>
    <g:id>#id#</g:id>
    <title>#title#</title>
    <description>#description#</description>
    <link>#SITE_URL##link#</link>
    <g:image_link>#SITE_URL##image_link#</g:image_link>
    <g:additional_image_link>#SITE_URL##additional_image_link#</g:additional_image_link>
    <g:mobile_link>#SITE_URL##mobile_link#</g:mobile_link>
    <g:availability>#availability#</g:availability>
    <g:availability_date>#availability_date#</g:availability_date>
    <g:expiration_date>#expiration_date#</g:expiration_date>
    <g:price>#price# #price_currency#</g:price>
    <g:sale_price>#sale_price# #sale_price_currency#</g:sale_price>
    <g:sale_price_effective_date>#sale_price_effective_date#</g:sale_price_effective_date>
    <g:unit_pricing_measure>#unit_pricing_measure#</g:unit_pricing_measure>
    <g:unit_pricing_base_measure>#unit_pricing_base_measure#</g:unit_pricing_base_measure>
    <g:google_product_category>#MARKET_CATEGORY#</g:google_product_category>
    <g:product_type>#product_type#</g:product_type>
    <g:brand>#brand#</g:brand>
    <g:gtin>#gtin#</g:gtin>
    <g:mpn>#mpn#</g:mpn>
    <g:identifier_exists>#identifier_exists#</g:identifier_exists>
    <g:condition>#condition#</g:condition>
    <g:adult>#adult#</g:adult>
    <g:multipack>#multipack#</g:multipack>
    <g:is_bundle>#is_bundle#</g:is_bundle>
    <g:energy_efficiency_class>#energy_efficiency_class#</g:energy_efficiency_class>
    <g:age_group>#age_group#</g:age_group>
    <g:color>#color#</g:color>
    <g:gender>#gender#</g:gender>
    <g:material>#material#</g:material>
    <g:pattern>#pattern#</g:pattern>
    <g:size>#size#</g:size>
    <g:size_type>#size_type#</g:size_type>
    <g:size_system>#size_system#</g:size_system>
    <g:item_group_id>#item_group_id#</g:item_group_id>
    <g:adwords_redirect>#adwords_labels#</g:adwords_redirect>
    <g:excluded_destination>#excluded_destination#</g:excluded_destination>
    <g:custom_label_0>#custom_label_0#</g:custom_label_0>
    <g:custom_label_1>#custom_label_1#</g:custom_label_1>
    <g:custom_label_2>#custom_label_2#</g:custom_label_2>
    <g:custom_label_3>#custom_label_3#</g:custom_label_3>
    <g:custom_label_4>#custom_label_4#</g:custom_label_4>
    <g:promotion_id>#promotion_id#</g:promotion_id>
    <g:shipping>
        <g:country>#shipping_country#</g:country>
        <g:service>#shipping_service#</g:service>
        <g:price>#shipping_price#</g:price>
    </g:shipping>
    <g:shipping_label>#shipping_label#</g:shipping_label>
    <g:shipping_weight>#shipping_weight#</g:shipping_weight>
    <g:shipping_length>#shipping_length#</g:shipping_length>
    <g:shipping_width>#shipping_width#</g:shipping_width>
    <g:shipping_height>#shipping_height#</g:shipping_height>
    <g:min_handling_time>#min_handling_time#</g:min_handling_time>
    <g:max_handling_time>#max_handling_time#</g:max_handling_time>
</item>";