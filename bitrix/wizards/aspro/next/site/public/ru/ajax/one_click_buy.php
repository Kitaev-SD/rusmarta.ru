<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?if((int)$_REQUEST['ELEMENT_ID'] && (int)$_REQUEST['IBLOCK_ID'] && \Bitrix\Main\Loader::includeModule('aspro.next')):?>
	<?$APPLICATION->IncludeComponent("aspro:oneclickbuy.next", "shop", array(
		"BUY_ALL_BASKET" => "N",
		"IBLOCK_ID" => (int)$_REQUEST["IBLOCK_ID"],
		"ELEMENT_ID" => (int)$_REQUEST["ELEMENT_ID"],
		"ELEMENT_QUANTITY" => (float)$_REQUEST["ELEMENT_QUANTITY"],
		"OFFER_PROPERTIES" => $_REQUEST["OFFER_PROPS"],
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"CACHE_GROUPS" => "N",
		"SHOW_LICENCE" => CNext::GetFrontParametrValue('SHOW_LICENCE'),
		"SHOW_DELIVERY_NOTE" => COption::GetOptionString('aspro.next', 'ONECLICKBUY_SHOW_DELIVERY_NOTE', 'N', SITE_ID),
		"PROPERTIES" => (strlen($tmp = COption::GetOptionString('aspro.next', 'ONECLICKBUY_PROPERTIES', 'FIO,PHONE,EMAIL,COMMENT', SITE_ID)) ? explode(',', $tmp) : array()),
		"REQUIRED" => (strlen($tmp = COption::GetOptionString('aspro.next', 'ONECLICKBUY_REQUIRED_PROPERTIES', 'FIO,PHONE', SITE_ID)) ? explode(',', $tmp) : array()),
		"DEFAULT_PERSON_TYPE" => COption::GetOptionString('aspro.next', 'ONECLICKBUY_PERSON_TYPE', '1', SITE_ID),
		"DEFAULT_DELIVERY" => COption::GetOptionString('aspro.next', 'ONECLICKBUY_DELIVERY', '2', SITE_ID),
		"DEFAULT_PAYMENT" => COption::GetOptionString('aspro.next', 'ONECLICKBUY_PAYMENT', '1', SITE_ID),
		"DEFAULT_CURRENCY" => COption::GetOptionString('aspro.next', 'ONECLICKBUY_CURRENCY', 'RUB', SITE_ID),
		),
		false
	);?>
<?endif;?>