<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "������� ��������� ����������� � Rusmarta");
$APPLICATION->SetPageProperty("description", "����� ������ � �������� ��������. ������������ � �����������������, ������� ��������������� � ��������� �� ���� ������ � ��������-�������� Rusmarta.ru");
$APPLICATION->SetTitle("�������");?><?$APPLICATION->IncludeComponent(
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