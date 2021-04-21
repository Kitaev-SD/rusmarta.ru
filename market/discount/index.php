<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "—кидки, акции, гор€чие предложени€ Ц Rusmarta");
$APPLICATION->SetPageProperty("description", "јкционные предложени€ компании. —игнализаци€ и видеооборудование, системы видеонаблюдени€ с доставкой по всей –оссии в интернет-магазине Rusmarta.ru");
$APPLICATION->SetTitle("—кидки");?><?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/discount.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>