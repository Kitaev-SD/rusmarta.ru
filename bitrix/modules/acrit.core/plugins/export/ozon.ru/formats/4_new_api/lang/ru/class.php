<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'OZON.RU API v2 (�������������)';

// Settings
$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';
$MESS[$strSName.'CLIENT_ID'] = '���������� ������������� [Client ID]';
	$MESS[$strSHint.'CLIENT_ID'] = '������� ����� ���������� ������������� (�<code><a href="https://seller.ozon.ru/settings/api-keys" target="_blank">Client Id</a></code>�) ����� ������� ������ ��������.';
$MESS[$strSName.'API_KEY'] = '���� ������� [API Key]';
	$MESS[$strSHint.'API_KEY'] = '������� ����� ���� ������� (�<code><a href="https://seller.ozon.ru/settings/api-keys" target="_blank">API key</a></code>�).';
	$MESS[$strLang.'API_KEY_CHECK'] = '��������� ������';

//
$MESS[$strLang.'GUESS_BRAND'] = '�����';
$MESS[$strLang.'GUESS_GROUP'] = '���������� �� ����� ��������';

//
$MESS[$strLang.'GENERAL_SETTINGS_HEADER_STOCK'] = '������ � ��������� Ozon';
$MESS[$strLang.'GENERAL_SETTINGS_CONSIDER_RESERVED_STOCK'] = '���� ������������������ ������� � Ozon';
	$MESS[$strLang.'GENERAL_SETTINGS_HINT_CONSIDER_RESERVED_STOCK'] = '������ ����� ��������� ��������� ����������������� ������� � Ozon � ��������� � ����� �������, ����������� �� ���������� ��������.<br/><br/>
<b>��������!</b> ������ ����� ���������� ������ ��� ������� ������ �������� ��� ������� � Ozon, �������������� ��� ����������� ����������� ����� ��������.<br/><br/>
������ ������ �� Ozon ���������� � �������� �� <code>offer_id</code> - �.�. ��� ���� ������ ���� ��������� ���������, ����� ����������� ������������������ ������� � Ozon �� ����� ��������.';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = '�������� ������ � �������';
$MESS[$strName.'offer_id'] = '������������� ������ (�������)';
	$MESS[$strHint.'offer_id'] = '������������� ������ � ������� �������� � �������.<br/><br/>
	������� ������ ���� ���������� � ������ ������ ������������.';
$MESS[$strName.'name'] = '�������� ������';
	$MESS[$strHint.'name'] = '�������� ������. �� 500 ��������.';
$MESS[$strName.'images'] = '�����������';
	$MESS[$strHint.'images'] = '�����������. �� ������ 10.';
$MESS[$strName.'image_group_id'] = '������������� �������� �������� �����������';
	$MESS[$strHint.'image_group_id'] = '������������� ��� ����������� �������� �������� �����������.';
$MESS[$strName.'pdf_list'] = 'PDF-�����';
	$MESS[$strHint.'pdf_list'] = '������ pdf-������';
$MESS[$strName.'price'] = '���� (� ������ ������)';
	$MESS[$strHint.'price'] = '���� ������ � ������ ������, ������������ �� �������� ������. ���� �� ����� ��� ������ � ������� �������� old_price.';
$MESS[$strName.'old_price'] = '���� (��� ����� ������)';
	$MESS[$strHint.'old_price'] = '���� �� ������ (����� ���������� �� �������� ������). ����������� � ������. ����������� ������� ����� � �����, �� ���� ������ ����� �����.';
$MESS[$strName.'premium_price'] = '���� Premium';
	$MESS[$strHint.'premium_price'] = '���� ��� �������� � ��������� <a href="https://docs.ozon.ru/common/ozon-premium" target="_blank">Ozon Premium</a>.';
$MESS[$strName.'vat'] = '������ ��� ��� ������';
	$MESS[$strHint.'vat'] = '������ ��� ��� ������.<br/>
		<ul>
			<li>0 � �� ���������� ���</li>
			<li>0.1 � 10%</li>
			<li>0.2 � 20%</li>
		</ul>';
$MESS[$strName.'stock'] = '������� �� ������ (<b>�������� �����!</b>)';
	$MESS[$strHint.'stock'] = '����� ������� ������ �� ������.<br/><br/>
	<b>��������! ���������� �������� �������� �������� ���� � �������� ������.</b> ������� ����������� ������ ��� �������, � ������� ���� ��������� ������ ������ <b>processed</b>. �� ����� ������� �������� �������� ����� ����������� ������� �[NOT_FOUND] Product not found�. �.�. ��������� �������� � ����� ������ �� ������� �������.';
$MESS[$strName.'warehouse_id'] = 'ID ������';
	$MESS[$strHint.'warehouse_id'] = '������������� ������.';
$MESS[$strName.'barcode'] = '��������';
	$MESS[$strHint.'barcode'] = '������� �������� ������ �� �������������. ���� � ������ ��� ������ ���������, ����� �� ������ �������������� ������������� ��� � ����.<br/><br/>
	�������� ����� ��� ������� �� ������ Ozon, � ����� ��� ������� ������, ����������� ������������ ���������� (�����)';
$MESS[$strName.'depth'] = '����� ��������';
	$MESS[$strHint.'depth'] = '����� � ��� ���������� ������� �������� ������. ����� ���������� �����:
	<ul>
		<li>������, ��������, ������ ��� ��������� � ������� ����� � �������� �������.</li>
		<li>����� � ����������� �������� � �������� � �����. ����� ������ � ����� ������� ��������.</li>
	</ul>
	����� �������� ��������� � ��� ����� ���� ������ ����, ������� ������ � ��������.<br/><br/>
	����������� � �����������, �����������, ��� ������ - ������� ��������� ���������� ��������� � ���� �dimension_unit�.';
$MESS[$strName.'width'] = '������ ��������';
	$MESS[$strHint.'width'] = '������� �������� ����� � ������, ���������� ������� � ��� ������. ����� ���������� ������:
	<ul>
		<li>������, ��������, ������ ��� ��������� � ������� ����� � �������� �������.</li>
		<li>����� � ����������� �������� � �������� � �����. ������ ������ � ��� ��� �������.</li>
	</ul>	
	������ �������� ��������� � ��� ������ ���� ������ ����, ������� ������ � ��������.<br/><br/>
	����������� � �����������, �����������, ��� ������ - ������� ��������� ���������� ��������� � ���� �dimension_unit�.';
$MESS[$strName.'height'] = '������ ��������';
	$MESS[$strHint.'height'] = '������ � ��� ���������� ������� �������� ������. ����� ���������� ������:
	<ul>
		<li>������, ��������, ������ ��� ��������� � ������� ����� � �������� �������.</li>
		<li>����� � ����������� �������� � �������� � �����. ������ ������ � ��� ��� �������.</li>
	</ul>
	������ �������� ��������� � ��� ������ ���� ������ ����, ������� ������ � ��������.<br/><br/>
	����������� � �����������, �����������, ��� ������ - ������� ��������� ���������� ��������� � ���� �������� ��������� ���������.';
$MESS[$strName.'dimension_unit'] = '������� ��������� ���������';
	$MESS[$strHint.'dimension_unit'] = '������� ��������� ���������
		<ul>
			<li>mm � ����������</li>
			<li>cm � ����������</li>
			<li>in � �����</li>
		</ul>';
$MESS[$strName.'weight'] = '��� ������ � ��������';
	$MESS[$strHint.'weight'] = '��� ������ � ��������. ���������� �������� - 1000 ��������� ��� ���������������� �������� � ������ �������� ���������.<br/><br/>
	����������� � �������, �����������, ��� ������ - ������� ��������� ���������� ��������� � ���� �������� ��������� ����.';
$MESS[$strName.'weight_unit'] = '������� ��������� ����';
	$MESS[$strHint.'weight_unit'] = '������� ��������� ����:
		<ul>
			<li>g � ������</li>
			<li>kg � ����������</li>
			<li>lb � �����</li>
		</ul>';
$MESS[$strName.'category_id'] = 'ID ���������';
	$MESS[$strHint.'category_id'] = '������� ����� ID ���������.<br/><br/>
	������������ ������ ��� ���������� ������� ��������������� ����� ������ ���������';

$MESS[$strLang.'MESSAGE_CHECK_ACCESS_SUCCESS'] = '�������� �������. ������ ��������.';
$MESS[$strLang.'MESSAGE_CHECK_ACCESS_DENIED'] = '������� ������������ ������ (ClientId �/��� ApiKey).';

$MESS[$strLang.'GROUPED_ATTRIBUTES_HEADER'] = '����� �������� ���������';

$MESS[$strLang.'NOTICE_SUPPORT'] = '<b>��������!</b> �� ������ ������ �������� �� ���������������� ������� ���������� ������������. ������ � ��������� �������������� <a href="/bitrix/admin/acrit_exportproplus_new_support.php?lang=ru&AcritExpSupport_active_tab=ask" target="_blank">�� ������� ������</a>.';

$MESS[$strLang.'ERROR_WRONG_PRODUCT_SECTION'] = '��� ������ #ELEMENT_ID# ������ ��������� �� ���������.';
$MESS[$strLang.'ERROR_WRONG_PRODUCT_CATEGORY'] = '��� ������ #ELEMENT_ID# ��������� �� ����������.';
$MESS[$strLang.'ERROR_EMPTY_REQUIRED_FIELDS'] = '��� ��������� �#CATEGORY#� �� ��������� ������������ ����: #FIELDS#';
$MESS[$strLang.'ERROR_WRONG_DICTIONARY_VALUE'] = '��� ������ #ELEMENT_ID# � �������� "#ATTRIBUTE#" ������� ������������ �������� &laquo;#VALUE#&raquo;. ��������� �������� �� �����������.';
$MESS[$strLang.'ERROR_CATEGORIES_EMPTY_ANSWER'] = '������ ���������� ��������� (#URL#). ���������� ��� ���.';
$MESS[$strLang.'ERROR_EXPORT_ITEMS_BY_API'] = '������ �������� ������� � OZON: #ERROR#.';
$MESS[$strLang.'ERROR_EXPORT_ITEMS_BY_API_TASK_0'] = '������� �������� task_id';
$MESS[$strLang.'ERROR_JSON_NOT_FOUND'] = 'JSON-������ �� �������.';
$MESS[$strLang.'ERROR_PARSE_ATTRIBUTE'] = '������ ������ � ��������� �#ATTRIBUTE#�. ��������� ��������� ���������, � ����� ��������� ���������� ���������.';
