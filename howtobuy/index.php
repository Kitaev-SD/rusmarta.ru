<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "����� ���������� ������� � Rusmarta.ru");
$APPLICATION->SetPageProperty("description", "����� ������ ���������� � �������� ������ � ������. ���������������. ������� ������ � ������������ � ��������� �� ���� ������ � ��������-�������� Rusmarta.ru");
$APPLICATION->SetTitle("FAQ");?><?$APPLICATION->IncludeComponent(
	"bitrix:support.faq",
	"",
	Array(
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"IBLOCK_ID" => "-",
		"IBLOCK_TYPE" => "-",
		"PATH_TO_USER" => "",
		"RATING_TYPE" => "",
		"SEF_MODE" => "N",
		"SHOW_RATING" => ""
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>