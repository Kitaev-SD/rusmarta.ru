<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?$APPLICATION->IncludeComponent("altop:sale.basket.delay", ".default", 
	array(
		"PATH_TO_DELAY" => SITE_DIR."personal/cart/?delay=Y",
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>