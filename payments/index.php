<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "������� ������ � Rusmarta");
$APPLICATION->SetPageProperty("description", "��� �������� � ������� ������. ������������ � �����������������, ������� ��������������� � ��������� �� ���� ������ � ��������-�������� Rusmarta.ru");
$APPLICATION->SetTitle("������� ������");?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"payments",
	Array(
		"ADD_SECTIONS_CHAIN" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COUNT_ELEMENTS" => "N",
		"IBLOCK_ID" => "11",
		"IBLOCK_TYPE" => "content",
		"SECTION_CODE" => "",
		"SECTION_FIELDS" => array(),
		"SECTION_ID" => "",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(),
		"SHOW_PARENT_NAME" => "",
		"TOP_DEPTH" => "2",
		"VIEW_MODE" => ""
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>