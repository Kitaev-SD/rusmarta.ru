<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Моя корзина");?>

<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket", ".default", 
	array(
		"COLUMNS_LIST" => array(
			0 => "NAME",
			1 => "DISCOUNT",
			2 => "PROPS",
			3 => "DELETE",
			4 => "DELAY",
			5 => "PRICE",
			6 => "QUANTITY",
			7 => "SUM",
		),
		"PATH_TO_ORDER" => "#SITE_DIR#personal/order/make/",
		"HIDE_COUPON" => "N",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"USE_PREPAYMENT" => "N",
		"QUANTITY_FLOAT" => "Y",
		"SET_TITLE" => "Y",
		"ACTION_VARIABLE" => "action",
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "#ITEMS_IBLOCK_ID#",
		"OFFERS_FIELD_CODE" => array(),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "COLOR",
			1 => "PROP2",
			2 => "PROP3"
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "asc",
		"OFFERS_LIMIT" => "",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"CONVERT_CURRENCY" => "N",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "COLOR",
			1 => "PROP2",
			2 => "PROP3"
		),
		"DISPLAY_IMG_WIDTH" => "178",
		"DISPLAY_IMG_HEIGHT" => "178",
		"ELEMENT_SORT_FIELD" => "RAND",
		"ELEMENT_SORT_ORDER" => "ASC",
		"DISPLAY_COMPARE" => "Y",
		"PROPERTY_CODE_MOD" => array(
			0 => "GUARANTEE"
		),
		"HIDE_NOT_AVAILABLE" => "N",
		"1CB_USE_FILE_FIELD" => "Y",
		"1CB_FILE_FIELD_MULTIPLE" => "Y",
		"1CB_FILE_FIELD_MAX_COUNT" => "5",
		"1CB_FILE_FIELD_NAME" => "Реквизиты",
		"1CB_FILE_FIELD_TYPE" => "doc, docx, txt, rtf",
		"1CB_REQUIRED_FIELDS" => array(
			0 => "NAME",
			1 => "PHONE"
		),
		"USE_BIG_DATA" => "Y",
		"BIG_DATA_RCM_TYPE" => "any",
		"COMPONENT_TEMPLATE" => ".default",
		"CORRECT_RATIO" => "Y",
		"AUTO_CALCULATION" => "Y",
		"USE_GIFTS" => "Y",
		"GIFTS_PLACE" => "",
		"GIFTS_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_TEXT_LABEL_GIFT" => "",
		"GIFTS_PRODUCT_QUANTITY_VARIABLE" => "",
		"GIFTS_PRODUCT_PROPS_VARIABLE" => "",
		"GIFTS_SHOW_OLD_PRICE" => "",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "",
		"GIFTS_SHOW_NAME" => "",
		"GIFTS_SHOW_IMAGE" => "",
		"GIFTS_MESS_BTN_BUY" => "",
		"GIFTS_MESS_BTN_DETAIL" => "",
		"GIFTS_PAGE_ELEMENT_COUNT" => "",
		"GIFTS_CONVERT_CURRENCY" => "",
		"GIFTS_HIDE_NOT_AVAILABLE" => ""
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>