<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "���� ������ � Rusmarta");
$APPLICATION->SetPageProperty("description", "����� ������� � ���������� ������ � ��������. ������������ � �����������������, ������� ��������������� � ��������� �� ���� ������ � ��������-�������� Rusmarta.ru");
$APPLICATION->SetTitle("���� ������");?>	<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
													array(
														"AREA_FILE_SHOW" => "file",
														"PATH" => SITE_DIR."include/saleleader.php",
														"AREA_FILE_RECURSIVE" => "N",
														"EDIT_MODE" => "html",
													),
													false,
													array("HIDE_ICONS" => "Y")
												);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>