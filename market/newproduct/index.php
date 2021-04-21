<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Новинки последние поступления – Rusmarta");
$APPLICATION->SetPageProperty("description", "Новые товары в каталоге компании. Сигнализация и видеооборудование, системы видеонаблюдения с доставкой по всей России в интернет-магазине Rusmarta.ru");
$APPLICATION->SetTitle("Новинки");?><?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/newproduct.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>