<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "’иты продаж Ц Rusmarta");
$APPLICATION->SetPageProperty("description", "—амые ходовые и попул€рные товары в каталоге. —игнализаци€ и видеооборудование, системы видеонаблюдени€ с доставкой по всей –оссии в интернет-магазине Rusmarta.ru");
$APPLICATION->SetTitle("’иты продаж");?>	<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
													array(
														"AREA_FILE_SHOW" => "file",
														"PATH" => SITE_DIR."include/saleleader.php",
														"AREA_FILE_RECURSIVE" => "N",
														"EDIT_MODE" => "html",
													),
													false,
													array("HIDE_ICONS" => "Y")
												);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>