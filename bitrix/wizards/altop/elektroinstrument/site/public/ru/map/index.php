<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карта сайта");?>

<?$APPLICATION->IncludeComponent("bitrix:main.map", ".default", 
	array(
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400",
		"SET_TITLE" => "Y",
		"LEVEL" => "4",
		"COL_NUM" => "1",
		"SHOW_DESCRIPTION" => "N"
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>