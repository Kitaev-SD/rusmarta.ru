<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("������� ������");?>

<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "payments",
	Array(
		"IBLOCK_TYPE" => "content",
		"IBLOCK_ID" => "#PAYMENTS_IBLOCK_ID#",
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"COUNT_ELEMENTS" => "N",
		"TOP_DEPTH" => "2",
		"SECTION_FIELDS" => array(),
		"SECTION_USER_FIELDS" => array(),
		"VIEW_MODE" => "",
		"SHOW_PARENT_NAME" => "",
		"SECTION_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"ADD_SECTIONS_CHAIN" => "N"
	)
);?>

<h2>�������������� ��������� �������� �������</h2>
<p>�� ������ ��������� �������� ��� ������� ������ ��� �������� ����, ��������� ������� ��� ��� ���������. ����������� �� ������������ ����������� ���������&nbsp;����� 2-� �������.</p>
<p>������ ���� �������� ����-������� �������� ��������-�������� ����������� ��� 1�-�������. ��� ���������� �� ����� �� �������� �������, � ������ ���� �������� ���������� ��� ������������ � ������������� �������.</p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>